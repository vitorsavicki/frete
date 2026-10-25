// Autenticação: cadastro, login, sair. Sempre re-renderiza a página com erro
// amigável em caso de falha (nunca JSON solto — é app SSR).
import { Hono } from 'hono';
import { hashSenha, verificarSenha } from '../lib/crypto';
import {
  criarSessao,
  gravarCookieSessao,
  encerrarSessao,
  limparCookieSessao,
  type Vars,
} from '../lib/session';
import { schemaCadastro, schemaLogin, primeiraMensagemErro } from '../lib/validate';
import { audit, type User } from '../lib/db';
import { LoginPage, CadastroPage } from '../pages/Auth';

const auth = new Hono<{ Bindings: { DB: D1Database }; Variables: Vars }>();

function redir(destino: string, params: Record<string, string>): string {
  const qs = new URLSearchParams(params).toString();
  return `${destino}?${qs}`;
}

auth.get('/cadastro', (c) => {
  if (c.get('user')) return c.redirect('/painel');
  return c.html(<CadastroPage />);
});

auth.post('/cadastro', async (c) => {
  if (c.get('user')) return c.redirect('/painel');

  const body = await c.req.parseBody();
  const bruto: Record<string, string> = {};
  for (const [k, v] of Object.entries(body)) if (typeof v === 'string') bruto[k] = v;

  const resultado = schemaCadastro.safeParse(bruto);
  if (!resultado.success) {
    return c.html(<CadastroPage erro={primeiraMensagemErro(resultado.error)} valores={bruto} />, 400);
  }
  const dados = resultado.data;

  const hash = await hashSenha(dados.senha);
  let userId: number;
  try {
    const r = await c.env.DB
      .prepare(
        `INSERT INTO users (email, password_hash, nome, sobrenome, papel, telefone, cpf_cnpj)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
      )
      .bind(
        dados.email.toLowerCase(),
        hash,
        dados.nome,
        dados.sobrenome,
        dados.papel,
        dados.telefone || null,
        dados.cpf_cnpj || null,
      )
      .run();
    userId = Number(r.meta.last_row_id);
  } catch (e) {
    if (String(e).includes('UNIQUE')) {
      return c.html(
        <CadastroPage erro="Este e-mail já está cadastrado. Tente entrar." valores={bruto} />,
        409,
      );
    }
    throw e;
  }

  await audit(c.env.DB, userId, 'cadastro', 'users', userId, { papel: dados.papel });

  // Auto-login pós-cadastro
  const token = await criarSessao(
    c.env.DB,
    userId,
    c.req.header('CF-Connecting-IP') ?? null,
    c.req.header('User-Agent') ?? null,
  );
  gravarCookieSessao(c, token);
  return c.redirect(redir('/painel', { msg: 'Conta criada! Bem-vindo(a) 🎉' }));
});

auth.get('/login', (c) => {
  if (c.get('user')) return c.redirect('/painel');
  return c.html(<LoginPage />);
});

auth.post('/login', async (c) => {
  if (c.get('user')) return c.redirect('/painel');

  const body = await c.req.parseBody();
  const resultado = schemaLogin.safeParse(body);
  if (!resultado.success) {
    return c.html(<LoginPage erro={primeiraMensagemErro(resultado.error)} />, 400);
  }
  const { email, senha } = resultado.data;

  const user = await c.env.DB
    .prepare('SELECT * FROM users WHERE email = ? AND ativo = 1')
    .bind(email.toLowerCase())
    .first<User>();

  // Mensagem genérica de propósito: não revela se o e-mail existe
  if (!user || !(await verificarSenha(senha, user.password_hash))) {
    await audit(c.env.DB, user?.id ?? null, 'login_falhou', 'users', user?.id, { email });
    return c.html(<LoginPage erro="E-mail ou senha inválidos." />, 401);
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

auth.post('/sair', async (c) => {
  await encerrarSessao(c.env.DB, c.get('sessionId'));
  limparCookieSessao(c);
  return c.redirect('/');
});

export default auth;
