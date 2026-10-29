// Landing page pública: dois caminhos diretos (publicar mudança ou ver mudanças).
import { Hono } from 'hono';
import { HomePage } from '../pages/Home';
import type { Vars } from '../lib/session';
import type { AppBindings } from '../lib/bindings';
import { aplicarPrazos } from '../lib/prazos';

const home = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

home.get('/', async (c) => {
  const db = c.env.DB;
  const alertas = c.get('user')
    ? await db
        .prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
        .bind(c.get('user')!.id)
        .first<{ n: number }>()
    : null;

  return c.html(
    <HomePage user={c.get('user')} alertas={alertas?.n ?? 0} url={c.req.url} />,
  );
});

// robots.txt e sitemap.xml gerados no Worker para usar o domínio real
// (funciona em produção, preview e localhost sem configurar domínio no código).
home.get('/robots.txt', (c) => {
  const origem = new URL(c.req.url).origin;
  c.header('Cache-Control', 'public, max-age=86400');
  return c.text(
    [
      'User-agent: *',
      'Allow: /',
      'Disallow: /painel',
      'Disallow: /caminhao',
      'Disallow: /api/',
      'Disallow: /login',
      'Disallow: /cadastro',
      '',
      `Sitemap: ${origem}/sitemap.xml`,
    ].join('\n'),
  );
});

function xmlEscape(valor: string): string {
  return valor
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

home.get('/sitemap.xml', async (c) => {
  const origem = new URL(c.req.url).origin;
  await aplicarPrazos(c.env.DB);
  const abertos = await c.env.DB.prepare(
    `SELECT id, criado_em FROM freight
     WHERE situacao = 'aberto'
     ORDER BY criado_em DESC
     LIMIT 500`,
  ).all<{ id: number; criado_em: string }>();

  const urls: { loc: string; lastmod?: string; prioridade: string }[] = [
    { loc: `${origem}/`, prioridade: '1.0' },
    { loc: `${origem}/fretes`, prioridade: '0.9' },
    { loc: `${origem}/app`, prioridade: '0.6' },
    ...(abertos.results ?? []).map((frete) => ({
      loc: `${origem}/fretes/${frete.id}`,
      lastmod: frete.criado_em?.slice(0, 10),
      prioridade: '0.7',
    })),
  ];

  const corpo = urls
    .map(
      (item) =>
        `  <url><loc>${xmlEscape(item.loc)}</loc>${
          item.lastmod ? `<lastmod>${item.lastmod}</lastmod>` : ''
        }<priority>${item.prioridade}</priority></url>`,
    )
    .join('\n');

  c.header('Cache-Control', 'public, max-age=3600');
  return c.body(
    `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${corpo}\n</urlset>`,
    200,
    { 'Content-Type': 'application/xml; charset=UTF-8' },
  );
});

export default home;
