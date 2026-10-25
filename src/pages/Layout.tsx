import type { FC, PropsWithChildren } from 'hono/jsx';
import type { User } from '../lib/db';

interface LayoutProps {
  titulo: string;
  user: User | null;
  alertas?: number;
  mapa?: boolean;
  mensagem?: string | null;
  erro?: string | null;
}

export const Layout: FC<PropsWithChildren<LayoutProps>> = ({
  titulo,
  user,
  alertas = 0,
  mapa = false,
  mensagem,
  erro,
  children,
}) => (
  <html lang="pt-BR">
    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title>{`${titulo} · Quero Mudar Já`}</title>
      <meta
        name="description"
        content="Marketplace de fretes e mudanças com leilão reverso: transportadores disputam seu frete e o menor lance vence."
      />
      <link rel="stylesheet" href="/css/style.css" />
      {mapa && <link rel="stylesheet" href="/vendor/leaflet/leaflet.css" />}
      <link rel="icon" href="/img/favicon.svg" type="image/svg+xml" />
    </head>
    <body>
      <header class="topo">
        <div class="container topo-interno">
          <a href="/" class="logo" aria-label="Quero Mudar Já — início">
            <span aria-hidden="true">🚚</span> Quero<strong>Mudar</strong>Já
          </a>
          <nav aria-label="principal">
            <a href="/fretes">Fretes abertos</a>
            {user?.papel === 'cliente' && (
              <a href="/novo-frete" class="destaque">
                Publicar frete
              </a>
            )}
            {user ? (
              <>
                <a href="/painel">
                  Painel{alertas > 0 && <span class="badge">{alertas}</span>}
                </a>
                <form method="post" action="/sair" class="inline">
                  <button type="submit" class="link">
                    Sair
                  </button>
                </form>
              </>
            ) : (
              <>
                <a href="/login">Entrar</a>
                <a href="/cadastro" class="destaque">
                  Cadastre-se
                </a>
              </>
            )}
          </nav>
        </div>
      </header>

      {mensagem && (
        <div class="flash flash-ok container" role="status">
          {mensagem}
        </div>
      )}
      {erro && (
        <div class="flash flash-erro container" role="alert">
          {erro}
        </div>
      )}

      <main class="container">{children}</main>

      <footer class="rodape">
        <div class="container">
          <p>
            Quero Mudar Já v2 — leilão reverso de fretes. Reescrito em 2026 com Cloudflare Workers + Hono +
            D1. <a href="https://github.com/vitorsavicki/frete">Código aberto</a> (versão PHP de 2020 em{' '}
            <code>legacy/</code>).
          </p>
        </div>
      </footer>
      {mapa && <script src="/vendor/leaflet/leaflet.js" defer></script>}
      <script src="/js/app.js" defer></script>
    </body>
  </html>
);
