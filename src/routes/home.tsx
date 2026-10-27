// Landing page pública: estatísticas + categorias + fretes recém-publicados.
import { Hono } from 'hono';
import { haversineKm } from '../lib/geo';
import { HomePage } from '../pages/Home';
import type { Category, FreightResumo } from '../lib/db';
import type { Vars } from '../lib/session';
import type { AppBindings } from '../lib/bindings';

const home = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

home.get('/', async (c) => {
  const db = c.env.DB;

  const [recentes, categorias, alertas] = await Promise.all([
    db
      .prepare(
        `SELECT f.*, c.nome AS categoria_nome, u.nome AS cliente_nome,
                (SELECT COUNT(*) FROM bids b WHERE b.freight_id = f.id) AS total_lances,
                (SELECT MIN(b.valor) FROM bids b WHERE b.freight_id = f.id) AS menor_lance
         FROM freight f
         JOIN categories c ON c.id = f.category_id
         JOIN users u ON u.id = f.user_id
         WHERE f.status = 'aberto'
         ORDER BY f.criado_em DESC
         LIMIT 6`,
      )
      .all<FreightResumo>(),
    db.prepare('SELECT id, nome FROM categories ORDER BY nome').all<Category>(),
    c.get('user')
      ? db
          .prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
          .bind(c.get('user')!.id)
          .first<{ n: number }>()
      : Promise.resolve({ n: 0 }),
  ]);

  const fretesRecentes = (recentes.results ?? []).map((f) => ({
    ...f,
    distancia_km: haversineKm(f.origem_lat, f.origem_lng, f.destino_lat, f.destino_lng),
  }));

  return c.html(
    <HomePage
      user={c.get('user')}
      alertas={alertas?.n ?? 0}
      fretesRecentes={fretesRecentes}
      categorias={categorias.results ?? []}
      url={c.req.url}
    />,
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
  const abertos = await c.env.DB.prepare(
    `SELECT id, criado_em FROM freight
     WHERE status = 'aberto'
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
