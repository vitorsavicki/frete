// Frete v2 (Quero Mudar Já) — Workers + Hono + D1.
// SSR com hono/jsx (escapa HTML por padrão = XSS bloqueado por construção).

import { Hono } from 'hono';
import { secureHeaders } from 'hono/secure-headers';
import { middlewareSessao, type Vars } from './lib/session';
import { protecaoOrigem } from './lib/csrf';
import { limiteAuth } from './lib/rate-limit';
import { Layout } from './pages/Layout';
import home from './routes/home';
import auth from './routes/auth';
import freight from './routes/freight';
import dashboard from './routes/dashboard';
import api from './routes/api';

export type Bindings = {
  DB: D1Database;
  ASSETS: Fetcher;
};

const app = new Hono<{ Bindings: Bindings; Variables: Vars }>();

// 1) Headers de segurança em TODAS as respostas dinâmicas.
//    (assets estáticos recebem os mesmos headers via public/_headers)
app.use(
  '*',
  secureHeaders({
    contentSecurityPolicy: {
      defaultSrc: ["'self'"],
      scriptSrc: ["'self'"],
      styleSrc: ["'self'"],
      imgSrc: ["'self'", 'data:', 'https://*.tile.openstreetmap.org'],
      connectSrc: ["'self'"], // CEP é buscado server-side (proxy /api/cep)
      fontSrc: ["'self'"],
      objectSrc: ["'none'"],
      baseUri: ["'self'"],
      formAction: ["'self'"],
      frameAncestors: ["'none'"],
      upgradeInsecureRequests: [],
    },
  }),
);

// 2) Sessão (cookie → usuário) disponível em todas as rotas
app.use('*', middlewareSessao);

// 3) CSRF: bloqueia mutações vindas de outros sites
app.use('*', protecaoOrigem);

// 4) Rate limit só nos endpoints abusáveis (login/cadastro)
app.use('/login', limiteAuth);
app.use('/cadastro', limiteAuth);

// Rotas
app.route('/', home);
app.route('/', auth);
app.route('/', freight);
app.route('/', dashboard);
app.route('/api', api);

app.notFound((c) =>
  c.html(
    <Layout titulo="Não encontrado" user={c.get('user')}>
      <div class="cartao-form">
        <h1>404 — Página não encontrada</h1>
        <p class="auxiliar">Esse caminho não existe (ou o frete já partiu 🚚).</p>
        <a href="/" class="botao">
          Voltar ao início
        </a>
      </div>
    </Layout>,
    404,
  ),
);

app.onError((err, c) => {
  console.error('Erro não tratado:', err);
  return c.html(
    <Layout titulo="Erro" user={c.get('user')}>
      <div class="cartao-form">
        <h1>Algo deu errado</h1>
        <p class="auxiliar">Nosso time já foi notificado. Tente de novo em instantes.</p>
        <a href="/" class="botao">
          Voltar ao início
        </a>
      </div>
    </Layout>,
    500,
  );
});

export default app;
