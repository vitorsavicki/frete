// Perfil do caminhão do transportador: medidas internas, capacidade e custos
// usados na conferência de encaixe e no preço sugerido dos fretes.
import { Hono } from 'hono';
import { exigirLogin, type Vars } from '../lib/session';
import { schemaCaminhao, primeiraMensagemErro } from '../lib/validate';
import { audit, type Truck } from '../lib/db';
import { CaminhaoPage } from '../pages/Truck';
import type { AppBindings } from '../lib/bindings';

const truck = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

function redir(destino: string, params: Record<string, string>): string {
  return `${destino}?${new URLSearchParams(params).toString()}`;
}

async function contexto(c: {
  env: AppBindings;
  get: (k: 'user') => { id: number } | null;
}) {
  const user = c.get('user')!;
  const [alertas, caminhao] = await Promise.all([
    c.env.DB.prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
      .bind(user.id)
      .first<{ n: number }>(),
    c.env.DB.prepare('SELECT * FROM trucks WHERE user_id = ?').bind(user.id).first<Truck>(),
  ]);
  return { alertas: alertas?.n ?? 0, caminhao };
}

truck.get('/caminhao', async (c) => {
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;
  if (user!.papel !== 'transportador') {
    return c.redirect(redir('/painel', { erro: 'O perfil de caminhão é exclusivo para transportadores.' }));
  }

  const { alertas, caminhao } = await contexto(c);
  const q = c.req.query();
  return c.html(
    <CaminhaoPage
      user={user!}
      alertas={alertas}
      caminhao={caminhao}
      valores={{}}
      mensagem={q.msg || null}
      erro={q.erro || null}
    />,
  );
});

truck.post('/caminhao', async (c) => {
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;
  if (user!.papel !== 'transportador') {
    return c.redirect(redir('/painel', { erro: 'O perfil de caminhão é exclusivo para transportadores.' }));
  }

  const body = await c.req.parseBody();
  const bruto: Record<string, string> = {};
  for (const [k, v] of Object.entries(body)) if (typeof v === 'string') bruto[k] = v;

  const resultado = schemaCaminhao.safeParse(bruto);
  if (!resultado.success) {
    const { alertas, caminhao } = await contexto(c);
    return c.html(
      <CaminhaoPage
        user={user!}
        alertas={alertas}
        caminhao={caminhao}
        valores={bruto}
        erro={primeiraMensagemErro(resultado.error)}
      />,
      400,
    );
  }
  const d = resultado.data;

  await c.env.DB.prepare(
    `INSERT INTO trucks (
       user_id, nome, tipo, comprimento_cm, largura_cm, altura_cm, capacidade_kg,
       consumo_km_l, diesel_preco, custo_fixo, margem_percentual
     ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
     ON CONFLICT (user_id) DO UPDATE SET
       nome = excluded.nome,
       tipo = excluded.tipo,
       comprimento_cm = excluded.comprimento_cm,
       largura_cm = excluded.largura_cm,
       altura_cm = excluded.altura_cm,
       capacidade_kg = excluded.capacidade_kg,
       consumo_km_l = excluded.consumo_km_l,
       diesel_preco = excluded.diesel_preco,
       custo_fixo = excluded.custo_fixo,
       margem_percentual = excluded.margem_percentual,
       atualizado_em = strftime('%Y-%m-%dT%H:%M:%fZ','now')`,
  )
    .bind(
      user!.id,
      d.nome,
      d.tipo,
      Math.round(d.comprimento * 100),
      Math.round(d.largura * 100),
      Math.round(d.altura * 100),
      Math.round(d.capacidade_kg),
      d.consumo_km_l,
      d.diesel_preco,
      d.custo_fixo,
      d.margem_percentual,
    )
    .run();

  await audit(c.env.DB, user!.id, 'salvar_caminhao', 'trucks', user!.id, {
    tipo: d.tipo,
    capacidade_kg: Math.round(d.capacidade_kg),
  });

  return c.redirect(redir('/caminhao', { msg: 'Caminhão salvo! Agora você pode conferir o encaixe nos fretes. 🚛' }));
});

export default truck;
