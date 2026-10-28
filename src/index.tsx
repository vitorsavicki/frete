// Frete v2 (Quero Mudar Já) — Workers + Hono + D1.
// SSR com hono/jsx (escapa HTML por padrão = XSS bloqueado por construção).

import { Hono } from 'hono';
import { secureHeaders } from 'hono/secure-headers';
import type { AppBindings } from './lib/bindings';
import { middlewareSessao, type Vars } from './lib/session';
import { protecaoOrigem } from './lib/csrf';
import { limiteAuth } from './lib/rate-limit';
import { Layout } from './pages/Layout';
import home from './routes/home';
import auth from './routes/auth';
import freight from './routes/freight';
import dashboard from './routes/dashboard';
import truck from './routes/truck';
import android from './routes/android';
import api from './routes/api';
import { processarFilaEmails } from './lib/email';
import { aplicarPrazos } from './lib/prazos';

const app = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

// 1) Headers de segurança em TODAS as respostas dinâmicas.
//    (assets estáticos recebem os mesmos headers via public/_headers)
app.use(
  '*',
  secureHeaders({
    // A política de tiles do OpenStreetMap exige que o navegador envie o
    // Referer. O padrão do Hono (no-referrer) faz o OSM bloquear o mapa.
    referrerPolicy: 'strict-origin-when-cross-origin',
    xFrameOptions: 'DENY',
    permissionsPolicy: {
      camera: [],
      microphone: [],
      geolocation: ['self'],
    },
    contentSecurityPolicy: {
      defaultSrc: ["'self'"],
      scriptSrc: ["'self'"],
      styleSrc: ["'self'"],
      imgSrc: [
        "'self'",
        'data:',
        'https://tile.openstreetmap.org',
        'https://*.tile.openstreetmap.org',
        'https://*.googleusercontent.com',
      ],
      connectSrc: ["'self'"], // APIs externas são chamadas server-side (CSP fechada)
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

// 4) Rate limit nos endpoints abusáveis (login/cadastro)
app.use('/login', limiteAuth);
app.use('/cadastro', limiteAuth);

// Rotas
app.route('/', home);
app.route('/', auth);
app.route('/', freight);
app.route('/', dashboard);
app.route('/', truck);
app.route('/', android);
app.route('/api', api);

app.notFound((c) =>
  c.html(
    <Layout titulo="Não encontrado" user={c.get('user')}>
      <div class="estado-vazio">
        <span class="estado-vazio__emoji" aria-hidden="true">
          🧭
        </span>
        <h1>404 — Página não encontrada</h1>
        <p class="auxiliar">Esse caminho não existe (ou o frete já partiu 🚚).</p>
        <a href="/" class="btn btn-primario">
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
      <div class="estado-vazio">
        <span class="estado-vazio__emoji" aria-hidden="true">
          ⚠️
        </span>
        <h1>Algo deu errado</h1>
        <p class="auxiliar">Nosso time já foi notificado. Tente de novo em instantes.</p>
        <a href="/" class="btn btn-primario">
          Voltar ao início
        </a>
      </div>
    </Layout>,
    500,
  );
});

// O cron é opcional: sem gatilho configurado, o Worker continua atendendo
// normalmente e os e-mails ficam na fila para a próxima execução.
export default {
  fetch: app.fetch,
  scheduled: async (
    _event: ScheduledController,
    env: AppBindings,
    _ctx: ExecutionContext,
  ): Promise<void> => {
    // Fecha leilões vencidos antes de qualquer outra tarefa: é o que tira a
    // mudança das listagens públicas mesmo sem ninguém visitar o site.
    const prazos = await aplicarPrazos(env.DB);
    if (prazos.semLances + prazos.emEscolha + prazos.expirados > 0) {
      console.log('[prazos]', JSON.stringify(prazos));
    }

    const resultado = await processarFilaEmails(env);
    if (resultado.processados > 0) {
      console.log('[email:fila]', JSON.stringify(resultado));
    }
  },
};
