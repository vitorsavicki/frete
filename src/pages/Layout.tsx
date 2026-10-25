import type { FC, PropsWithChildren } from 'hono/jsx';
import type { User } from '../lib/db';

interface LayoutProps {
  titulo: string;
  user: User | null;
  alertas?: number;
  mapa?: boolean;
  mensagem?: string | null;
  erro?: string | null;
  descricao?: string;
}

function iniciais(user: User): string {
  const nome = (user.nome ?? '').trim();
  const sobrenome = (user.sobrenome ?? '').trim();
  return `${nome.charAt(0)}${sobrenome.charAt(0)}`.toUpperCase() || '👤';
}

export const Layout: FC<PropsWithChildren<LayoutProps>> = ({
  titulo,
  user,
  alertas = 0,
  mapa = false,
  mensagem,
  erro,
  descricao = 'Marketplace de fretes e mudanças com leilão reverso: transportadores disputam seu frete e o menor lance vence. Publicar e dar lances é 100% grátis.',
  children,
}) => (
  <html lang="pt-BR">
    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title>{`${titulo} · Quero Mudar Já`}</title>
      <meta name="description" content={descricao} />
      <meta name="theme-color" content="#0f172a" />
      <meta property="og:type" content="website" />
      <meta property="og:site_name" content="Quero Mudar Já" />
      <meta property="og:title" content={`${titulo} · Quero Mudar Já`} />
      <meta property="og:description" content={descricao} />
      <meta property="og:image" content="/img/hero-truck.webp" />
      <meta name="twitter:card" content="summary_large_image" />
      <link rel="stylesheet" href="/css/style.css" />
      {mapa && <link rel="stylesheet" href="/vendor/leaflet/leaflet.css" />}
      <link rel="icon" href="/img/favicon.svg" type="image/svg+xml" />
    </head>
    <body>
      <a href="#conteudo" class="pular">
        Pular para o conteúdo
      </a>

      <header class="site-header" data-header>
        <div class="container header-inner">
          <a href="/" class="brand" aria-label="Quero Mudar Já — início">
            <span class="brand-mark" aria-hidden="true">
              <svg viewBox="0 0 48 48" fill="none">
                <rect width="48" height="48" rx="14" fill="currentColor" />
                <path
                  d="M10 30V17.5A1.5 1.5 0 0 1 11.5 16H25v14"
                  stroke="#fff"
                  stroke-width="2.6"
                  stroke-linejoin="round"
                />
                <path
                  d="M25 20h7.2c.6 0 1.1.3 1.4.9l3.2 6.1c.1.3.2.6.2.9V30h-2.4"
                  stroke="#fff"
                  stroke-width="2.6"
                  stroke-linejoin="round"
                />
                <path d="M10 30h3.5M19.5 30h9M33.5 30H36" stroke="#fff" stroke-width="2.6" />
                <circle cx="17" cy="32.5" r="3" stroke="#fff" stroke-width="2.4" />
                <circle cx="31" cy="32.5" r="3" stroke="#fff" stroke-width="2.4" />
                <path d="M27 24h3.4l2 4H27v-4Z" fill="#fff" opacity=".9" />
              </svg>
            </span>
            <span class="brand-text">
              Quero <strong>Mudar</strong> Já
            </span>
          </a>

          <button
            class="nav-toggle"
            type="button"
            aria-expanded="false"
            aria-controls="menu-principal"
            data-nav-toggle
          >
            <span class="sr-only">Abrir menu</span>
            <span class="nav-toggle-bars" aria-hidden="true">
              <i></i>
              <i></i>
              <i></i>
            </span>
          </button>

          <nav class="site-nav" id="menu-principal" aria-label="Principal" data-nav>
            <a href="/fretes">Fretes abertos</a>
            {user?.papel === 'cliente' && (
              <a href="/novo-frete" class="nav-destaque">
                Publicar frete
              </a>
            )}
            {user?.papel === 'transportador' && <a href="/caminhao">Meu caminhão</a>}
            {user ? (
              <>
                <a href="/painel" class="nav-painel">
                  {user.avatar_url ? (
                    <img class="avatar" src={user.avatar_url} alt="" width="26" height="26" loading="lazy" />
                  ) : (
                    <span class="avatar" aria-hidden="true">
                      {iniciais(user)}
                    </span>
                  )}
                  <span>Painel</span>
                  {alertas > 0 && <span class="badge">{alertas > 9 ? '9+' : alertas}</span>}
                </a>
                <form method="post" action="/sair" class="inline-form">
                  <button type="submit" class="link-sair">
                    Sair
                  </button>
                </form>
              </>
            ) : (
              <>
                <a href="/login">Entrar</a>
                <a href="/cadastro" class="nav-destaque">
                  Criar conta grátis
                </a>
              </>
            )}
          </nav>
        </div>
      </header>

      {mensagem && (
        <div class="container">
          <div class="flash flash-ok" role="status">
            <span aria-hidden="true">✅</span> {mensagem}
          </div>
        </div>
      )}
      {erro && (
        <div class="container">
          <div class="flash flash-erro" role="alert">
            <span aria-hidden="true">⚠️</span> {erro}
          </div>
        </div>
      )}

      <main id="conteudo" class="container pagina">
        {children}
      </main>

      <footer class="site-footer">
        <div class="container footer-grid">
          <div>
            <a href="/" class="brand brand-footer">
              <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none">
                  <rect width="48" height="48" rx="14" fill="currentColor" />
                  <path d="M10 30V17.5A1.5 1.5 0 0 1 11.5 16H25v14" stroke="#fff" stroke-width="2.6" />
                  <path d="M25 20h7.2c.6 0 1.1.3 1.4.9l3.2 6.1c.1.3.2.6.2.9V30h-2.4" stroke="#fff" stroke-width="2.6" />
                  <path d="M10 30h3.5M19.5 30h9M33.5 30H36" stroke="#fff" stroke-width="2.6" />
                  <circle cx="17" cy="32.5" r="3" stroke="#fff" stroke-width="2.4" />
                  <circle cx="31" cy="32.5" r="3" stroke="#fff" stroke-width="2.4" />
                </svg>
              </span>
              <span class="brand-text">Quero <strong>Mudar</strong> Já</span>
            </a>
            <p class="footer-tagline">
              Leilão reverso de fretes: você publica, os transportadores disputam e o menor lance vence.
            </p>
          </div>

          <nav class="footer-links" aria-label="Rodapé">
            <div>
              <h2>Navegar</h2>
              <a href="/fretes">Fretes abertos</a>
              {user?.papel === 'cliente' ? (
                <a href="/novo-frete">Publicar frete</a>
              ) : (
                <a href={user ? '/fretes' : '/cadastro'}>
                  {user ? 'Encontrar fretes' : 'Publicar frete'}
                </a>
              )}
              {user && <a href="/painel">Meu painel</a>}
              {user?.papel === 'transportador' && <a href="/caminhao">Meu caminhão</a>}
              <a href="/app">App Android (APK)</a>
            </div>
            <div>
              <h2>Conta</h2>
              {user ? (
                <>
                  <a href="/painel">Olá, {user.nome}</a>
                  {user.papel === 'transportador' && <a href="/caminhao">Meu caminhão</a>}
                  <form method="post" action="/sair">
                    <button type="submit" class="footer-link">
                      Sair
                    </button>
                  </form>
                </>
              ) : (
                <>
                  <a href="/cadastro">Criar conta grátis</a>
                  <a href="/login">Entrar</a>
                </>
              )}
            </div>
          </nav>
        </div>
        <div class="container footer-base">
          <p>© {new Date().getFullYear()} Quero Mudar Já · Feito com Cloudflare Workers + D1</p>
          <p class="footer-mini">Publicar e dar lances é grátis. Sem taxa de intermediação.</p>
        </div>
      </footer>

      {mapa && <script src="/vendor/leaflet/leaflet.js" defer></script>}
      <script src="/js/app.js" defer></script>
    </body>
  </html>
);
