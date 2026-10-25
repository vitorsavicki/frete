// Painel do usuário logado: notificações + meus fretes (cliente) ou meus lances
// (transportador).
import { Hono } from 'hono';
import { exigirLogin, type Vars } from '../lib/session';
import { DashboardPage } from '../pages/Dashboard';
import type { Alert, BidComNome, FreightResumo } from '../lib/db';

const dashboard = new Hono<{ Bindings: { DB: D1Database }; Variables: Vars }>();

dashboard.get('/painel', async (c) => {
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;
  const u = user!;
  const db = c.env.DB;

  const [alertas, naoLidos, meusFretes, meusLances] = await Promise.all([
    db
      .prepare('SELECT * FROM alerts WHERE user_id = ? ORDER BY lido ASC, criado_em DESC LIMIT 30')
      .bind(u.id)
      .all<Alert>(),
    db
      .prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
      .bind(u.id)
      .first<{ n: number }>(),
    u.papel === 'cliente'
      ? db
          .prepare(
            `SELECT f.*, c.nome AS categoria_nome, u2.nome AS cliente_nome,
                    (SELECT COUNT(*) FROM bids b WHERE b.freight_id = f.id) AS total_lances,
                    (SELECT MIN(b.valor) FROM bids b WHERE b.freight_id = f.id) AS menor_lance
             FROM freight f
             JOIN categories c ON c.id = f.category_id
             JOIN users u2 ON u2.id = f.user_id
             WHERE f.user_id = ?
             ORDER BY f.criado_em DESC`,
          )
          .bind(u.id)
          .all<FreightResumo>()
      : Promise.resolve({ results: [] as FreightResumo[] }),
    u.papel === 'transportador'
      ? db
          .prepare(
            `SELECT b.*, u3.nome AS transportador_nome,
                    f.descricao AS frete_descricao, f.status AS frete_status,
                    (SELECT MIN(b2.valor) FROM bids b2 WHERE b2.freight_id = b.freight_id) AS menor_lance
             FROM bids b
             JOIN freight f ON f.id = b.freight_id
             JOIN users u3 ON u3.id = b.transportador_id
             WHERE b.transportador_id = ?
             ORDER BY b.criado_em DESC
             LIMIT 50`,
          )
          .bind(u.id)
          .all<
            BidComNome & { frete_descricao: string; frete_status: string; menor_lance: number | null }
          >()
      : Promise.resolve({ results: [] as (BidComNome & { frete_descricao: string; frete_status: string; menor_lance: number | null })[] }),
  ]);

  const q = c.req.query();

  return c.html(
    <DashboardPage
      user={u}
      alertas={alertas.results ?? []}
      alertasNaoLidos={naoLidos?.n ?? 0}
      meusFretes={meusFretes.results ?? []}
      meusLances={meusLances.results ?? []}
    />,
  );
});

dashboard.post('/alertas/ler', async (c) => {
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;
  await c.env.DB.prepare('UPDATE alerts SET lido = 1 WHERE user_id = ?').bind(user!.id).run();
  return c.redirect('/painel');
});

export default dashboard;
