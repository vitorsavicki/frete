// Autenticação: cadastro, login (inclusive Google), sair.
// Sempre re-renderiza a página com erro amigável em caso de falha — é app SSR.
import { Hono } from 'hono';
import { deleteCookie, getCookie, setCookie } from 'hono/cookie';
import { hashSenha, verificarSenha, gerarTokenSessao } from '../lib/crypto';
import {
  criarSessao,
  gravarCookieSessao,
  encerrarSessao,
  limparCookieSessao,
  type Vars,
} from '../lib/session';
import {
  schemaCadastro,
  schemaLogin,
  primeiraMensagemErro,
} from '../lib/validate';
import { validarDocumento } from '../lib/documento';
import { audit, type User } from '../lib/db';
import { emailBoasVindas } from '../lib/email';
import {
  googleConfigurado,
  obterPerfilGoogle,
  trocarCodigoGoogle,
  urlAutorizacaoGoogle,
} from '../lib/google';
import { LoginPage, CadastroPage } from '../pages/Auth';
import type { AppBindings } from '../lib/bindings';

const auth = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

const COOKIE_OAUTH = 'oauth_state';

function redir(destino: string, params: Record<string, string>): string {
  const qs = new URLSearchParams(params).toString();
  return `${destino}?${qs}`;
}

function origem(c: { req: { url: string } }): string {
  return new URL(c.req.url).origin;
}

function seguro(c: { req: { url: string } }): boolean {
  return new URL(c.req.url).protocol === 'https:';
}

auth.get('/cadastro', (c) => {
  if (c.get('user')) return c.redirect('/painel');
  const papelInicial = c.req.query('papel') === 'transportador' ? 'transportador' : 'cliente';
  return c.html(
    <CadastroPage googleAtivo={googleConfigurado(c.env)} papelInicial={papelInicial} />,
  );
});

auth.post('/cadastro', async (c) => {
  if (c.get('user')) return c.redirect('/painel');

  const body = await c.req.parseBody();
  const bruto: Record<string, string> = {};
  for (const [k, v] of Object.entries(body)) if (typeof v === 'string') bruto[k] = v;

  const resultado = schemaCadastro.safeParse(bruto);
  if (!resultado.success) {
    return c.html(
      <CadastroPage
        erro={primeiraMensagemErro(resultado.error)}
        valores={bruto}
        googleAtivo={googleConfigurado(c.env)}
      />,
      400,
    );
  }
  const dados = resultado.data;

  // Documento: valida dígitos verificadores localmente e exige CPF/CNPJ de transportadores.
  const documento = dados.cpf_cnpj ? validarDocumento(dados.cpf_cnpj) : null;
  if (dados.cpf_cnpj && !documento?.ok) {
    return c.html(
      <CadastroPage
        erro="CPF ou CNPJ inválido. Confira os números."
        valores={bruto}
        googleAtivo={googleConfigurado(c.env)}
      />,
      400,
    );
  }
  if (dados.papel === 'transportador' && !documento?.ok) {
    return c.html(
      <CadastroPage
        erro="Informe um CPF ou CNPJ válido para se cadastrar como transportador."
        valores={bruto}
        googleAtivo={googleConfigurado(c.env)}
      />,
      400,
    );
  }

  const hash = await hashSenha(dados.senha);
  const email = dados.email.toLowerCase();
  let userId: number;
  try {
    const r = await c.env.DB.prepare(
      `INSERT INTO users (email, password_hash, nome, sobrenome, papel, telefone, cpf_cnpj)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
    )
      .bind(
        email,
        hash,
        dados.nome,
        dados.sobrenome,
        dados.papel,
        dados.telefone || null,
        documento?.digitos || null,
      )
      .run();
    userId = Number(r.meta.last_row_id);
  } catch (e) {
    if (String(e).includes('UNIQUE')) {
      return c.html(
        <CadastroPage
          erro="Este e-mail já está cadastrado. Tente entrar."
          valores={bruto}
          googleAtivo={googleConfigurado(c.env)}
        />,
        409,
      );
    }
    throw e;
  }

  await audit(c.env.DB, userId, 'cadastro', 'users', userId, { papel: dados.papel });

  const token = await criarSessao(
    c.env.DB,
    userId,
    c.req.header('CF-Connecting-IP') ?? null,
    c.req.header('User-Agent') ?? null,
  );
  gravarCookieSessao(c, token);

  // E-mail de boas-vindas (não bloqueia a resposta; sem chave só registra no log)
  c.executionCtx.waitUntil(
    emailBoasVindas(c.env, {
      para: email,
      nome: dados.nome,
      papel: dados.papel,
      baseUrl: origem(c),
    }),
  );

  return c.redirect(redir('/painel', { msg: 'Conta criada! Bem-vindo(a) 🎉' }));
});

auth.get('/login', (c) => {
  if (c.get('user')) return c.redirect('/painel');
  return c.html(
    <LoginPage googleAtivo={googleConfigurado(c.env)} erro={c.req.query('erro')} />,
  );
});

auth.post('/login', async (c) => {
  if (c.get('user')) return c.redirect('/painel');

  const body = await c.req.parseBody();
  const resultado = schemaLogin.safeParse(body);
  if (!resultado.success) {
    return c.html(
      <LoginPage erro={primeiraMensagemErro(resultado.error)} googleAtivo={googleConfigurado(c.env)} />,
      400,
    );
  }
  const { email, senha } = resultado.data;

  const user = await c.env.DB.prepare('SELECT * FROM users WHERE email = ? AND ativo = 1')
    .bind(email.toLowerCase())
    .first<User>();

  // Mensagem genérica de propósito: não revela se o e-mail existe
  if (!user || !(await verificarSenha(senha, user.password_hash))) {
    await audit(c.env.DB, user?.id ?? null, 'login_falhou', 'users', user?.id, { email });
    return c.html(
      <LoginPage erro="E-mail ou senha inválidos." googleAtivo={googleConfigurado(c.env)} />,
      401,
    );
  }

  const token = await criarSessao(
    c.env.DB,
    user.id,
    c.req.header('CF-Connecting-IP') ?? null,
    c.req.header('User-Agent') ?? null,
  );
  gravarCookieSessao(c, token);
  await audit(c.env.DB, user.id, 'login', 'users', user.id);
  return c.redirect(redir('/painel', { msg: `Olá de novo, ${user.nome}!` }));
});

// ---------------- Login com Google (OAuth 2.0) ----------------

auth.get('/auth/google', (c) => {
  if (c.get('user')) return c.redirect('/painel');
  if (!googleConfigurado(c.env)) {
    return c.redirect(redir('/login', { erro: 'Login com Google não está configurado neste ambiente.' }));
  }

  const papel = c.req.query('papel') === 'transportador' ? 'transportador' : 'cliente';
  const state = `${gerarTokenSessao().slice(0, 32)}.${papel}`;
  setCookie(c, COOKIE_OAUTH, state, {
    httpOnly: true,
    secure: seguro(c),
    sameSite: 'Lax',
    path: '/',
    maxAge: 600,
  });

  const url = urlAutorizacaoGoogle(c.env, `${origem(c)}/auth/google/callback`, state);
  if (!url) return c.redirect(redir('/login', { erro: 'Login com Google indisponível.' }));
  return c.redirect(url);
});

auth.get('/auth/google/callback', async (c) => {
  const stateEsperado = getCookie(c, COOKIE_OAUTH);
  deleteCookie(c, COOKIE_OAUTH, { path: '/' });

  const erroGoogle = c.req.query('error');
  if (erroGoogle) return c.redirect(redir('/login', { erro: 'Login com Google cancelado.' }));

  const state = c.req.query('state') ?? '';
  const code = c.req.query('code') ?? '';
  if (!stateEsperado || state !== stateEsperado || !code) {
    return c.redirect(redir('/login', { erro: 'Sessão de login expirada. Tente de novo.' }));
  }

  const papel = state.endsWith('.transportador') ? 'transportador' : 'cliente';
  const accessToken = await trocarCodigoGoogle(c.env, code, `${origem(c)}/auth/google/callback`);
  if (!accessToken) {
    return c.redirect(redir('/login', { erro: 'Não foi possível concluir o login com Google.' }));
  }

  const perfil = await obterPerfilGoogle(accessToken);
  if (!perfil || !perfil.emailVerificado) {
    return c.redirect(redir('/login', { erro: 'Sua conta Google precisa ter e-mail verificado.' }));
  }

  let user = await c.env.DB.prepare('SELECT * FROM users WHERE google_sub = ?')
    .bind(perfil.sub)
    .first<User>();

  if (!user) {
    user = await c.env.DB.prepare('SELECT * FROM users WHERE email = ?')
      .bind(perfil.email)
      .first<User>();
  }

  if (user && user.ativo !== 1) {
    return c.redirect(redir('/login', { erro: 'Esta conta está desativada.' }));
  }

  if (!user) {
    // Conta nova: senha aleatória (o acesso será sempre via Google).
    const hash = await hashSenha(gerarTokenSessao());
    try {
      const r = await c.env.DB.prepare(
        `INSERT INTO users (email, password_hash, nome, sobrenome, papel, google_sub, avatar_url)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
      )
        .bind(perfil.email, hash, perfil.nome, perfil.sobrenome, papel, perfil.sub, perfil.avatar)
        .run();
      const novoId = Number(r.meta.last_row_id);
      user = await c.env.DB.prepare('SELECT * FROM users WHERE id = ?').bind(novoId).first<User>();
      await audit(c.env.DB, novoId, 'cadastro_google', 'users', novoId, { papel });
      c.executionCtx.waitUntil(
        emailBoasVindas(c.env, {
          para: perfil.email,
          nome: perfil.nome,
          papel,
          baseUrl: origem(c),
        }),
      );
    } catch (e) {
      console.error('Falha ao criar usuário Google', e);
      return c.redirect(redir('/login', { erro: 'Não foi possível criar sua conta. Tente de novo.' }));
    }
  } else if (!user.google_sub || !user.avatar_url) {
    await c.env.DB.prepare('UPDATE users SET google_sub = COALESCE(google_sub, ?), avatar_url = COALESCE(avatar_url, ?) WHERE id = ?')
      .bind(perfil.sub, perfil.avatar, user.id)
      .run();
  }

  if (!user) {
    return c.redirect(redir('/login', { erro: 'Não foi possível concluir o login.' }));
  }

  const token = await criarSessao(
    c.env.DB,
    user.id,
    c.req.header('CF-Connecting-IP') ?? null,
    c.req.header('User-Agent') ?? null,
  );
  gravarCookieSessao(c, token);
  await audit(c.env.DB, user.id, 'login_google', 'users', user.id);
  return c.redirect(redir('/painel', { msg: `Olá de novo, ${user.nome}!` }));
});

auth.post('/sair', async (c) => {
  await encerrarSessao(c.env.DB, c.get('sessionId'));
  limparCookieSessao(c);
  return c.redirect('/');
});

export default auth;
