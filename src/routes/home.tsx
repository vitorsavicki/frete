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

  const [recentes, abertos, transportadores, fretes, categorias, alertas] = await Promise.all([
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
    db.prepare("SELECT COUNT(*) AS n FROM freight WHERE status = 'aberto'").first<{ n: number }>(),
    db.prepare("SELECT COUNT(*) AS n FROM users WHERE papel = 'transportador'").first<{ n: number }>(),
    db.prepare('SELECT COUNT(*) AS n FROM freight').first<{ n: number }>(),
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
      totalAbertos={abertos?.n ?? 0}
      totalTransportadores={transportadores?.n ?? 0}
      totalFretes={fretes?.n ?? 0}
    />,
  );
});

export default home;
