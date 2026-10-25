// Proteção CSRF para app SSR same-origin, sem cerimônia de token:
// 1) Cookie de sessão já é SameSite=Lax (não viaja em POST cross-site)
// 2) Aqui validamos a origem da requisição em métodos de mutação.
//    - Sec-Fetch-Site (todo navegador moderno manda): aceita same-origin/same-site/none
//    - Fallback: compara host do header Origin com o host da URL
import { createMiddleware } from 'hono/factory';

const METODOS_SEGUROS = new Set(['GET', 'HEAD', 'OPTIONS']);

export const protecaoOrigem = createMiddleware(async (c, next) => {
  if (METODOS_SEGUROS.has(c.req.method)) return next();

  const fetchSite = c.req.header('Sec-Fetch-Site');
  if (fetchSite) {
    if (fetchSite === 'same-origin' || fetchSite === 'same-site' || fetchSite === 'none') {
      return next();
    }
    return c.text('Origem não permitida.', 403);
  }

  const origin = c.req.header('Origin');
  if (origin) {
    try {
      if (new URL(origin).host !== new URL(c.req.url).host) {
        return c.text('Origem não permitida.', 403);
      }
    } catch {
      return c.text('Origem não permitida.', 403);
    }
  }

  return next();
});
