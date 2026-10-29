// Fretes: listagem com filtros, detalhe, publicar, dar lance, aceitar, cancelar.
// Toda query usa prepared statement (bind) — injeção de SQL impossível.
import { Hono } from 'hono';
import { exigirLogin, type Vars } from '../lib/session';
import { schemaFrete, schemaLance, primeiraMensagemErro } from '../lib/validate';
import { validarLance, podeAceitarLance, podeCancelarFrete } from '../lib/rules';
import { audit, criarAlerta, type Category, type FreightResumo, type BidComNome, type Truck, type User } from '../lib/db';
import { consultarCep, geocodificarEndereco, haversineKm, distanciaRodoviariaKm } from '../lib/geo';
import { calcularRota } from '../lib/rota';
import { previsaoRetirada } from '../lib/clima';
import { emailLanceVencedor, emailNovoLance } from '../lib/email';
import { estimarCustoRodoviario, type ParametrosCusto } from '../lib/cost';
import { lerReferencias, parametrosComReferencias } from '../lib/referencias';
import {
  analisarEncaixe,
  dimensoesTexto,
  medidasDoCaminhao,
  parseItensDb,
  parseItensForm,
  resumoCarga,
  serializarItens,
} from '../lib/carga';
import { sugerirVeiculoParaPlano } from '../lib/montagem';
import { aplicarPrazos, calcularPrazos, detalhePublico } from '../lib/prazos';
import { ListaFretesPage, DetalheFretePage, NovoFretePage } from '../pages/Freight';
import type { AppBindings } from '../lib/bindings';

const freight = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

function baseUrl(c: { req: { url: string } }): string {
  return new URL(c.req.url).origin;
}

const POR_PAGINA = 10;

function redir(destino: string, params: Record<string, string>): string {
  const qs = new URLSearchParams(params).toString();
  return `${destino}?${qs}`;
}

const SQL_RESUMO = `
  SELECT f.*, c.nome AS categoria_nome, u.nome AS cliente_nome,
         (SELECT COUNT(*) FROM bids b WHERE b.freight_id = f.id) AS total_lances,
         (SELECT MIN(b.valor) FROM bids b WHERE b.freight_id = f.id) AS menor_lance
  FROM freight f
  JOIN categories c ON c.id = f.category_id
  JOIN users u ON u.id = f.user_id`;

// ---------------- GET /fretes — lista pública com filtros ----------------
freight.get('/fretes', async (c) => {
  const db = c.env.DB;
  const q = c.req.query();

  // O relógio do leilão é aplicado antes da consulta: mudança com prazo
  // vencido não aparece na lista pública.
  await aplicarPrazos(db);

  const filtros = {
    categoria: (q.categoria ?? '').trim(),
    uf: (q.uf ?? '').trim().toUpperCase(),
    cidade: (q.cidade ?? '').trim(),
    q: (q.q ?? '').trim(),
    data: (q.data ?? '').trim(),
    pagina: Math.max(1, parseInt(q.pagina ?? '1', 10) || 1),
  };

  const where: string[] = ["f.situacao = 'aberto'"];
  const binds: (string | number)[] = [];

  if (filtros.categoria && /^\d+$/.test(filtros.categoria)) {
    where.push('f.category_id = ?');
    binds.push(Number(filtros.categoria));
  }
  if (filtros.uf) {
    where.push('(f.origem_uf = ? OR f.destino_uf = ?)');
    binds.push(filtros.uf, filtros.uf);
  }
  if (filtros.cidade) {
    where.push('(f.origem_cidade LIKE ? COLLATE NOCASE OR f.destino_cidade LIKE ? COLLATE NOCASE)');
    const termo = `%${filtros.cidade}%`;
    binds.push(termo, termo);
  }
  if (filtros.q) {
    where.push('(f.descricao LIKE ? COLLATE NOCASE OR f.comentario LIKE ? COLLATE NOCASE)');
    const termo = `%${filtros.q}%`;
    binds.push(termo, termo);
  }
  if (/^\d{4}-\d{2}-\d{2}$/.test(filtros.data)) {
    where.push('f.data_retirada >= ?');
    binds.push(filtros.data);
  }

  const whereSql = `WHERE ${where.join(' AND ')}`;
  const clausulas = [...binds];

  const [lista, total, categorias, alertas] = await Promise.all([
    db
      .prepare(`${SQL_RESUMO} ${whereSql} ORDER BY f.criado_em DESC LIMIT ? OFFSET ?`)
      .bind(...clausulas, POR_PAGINA, (filtros.pagina - 1) * POR_PAGINA)
      .all<FreightResumo>(),
    db.prepare(`SELECT COUNT(*) AS n FROM freight f ${whereSql}`).bind(...clausulas).first<{ n: number }>(),
    db.prepare('SELECT id, nome FROM categories ORDER BY nome').all<Category>(),
    c.get('user')
      ? db
          .prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
          .bind(c.get('user')!.id)
          .first<{ n: number }>()
      : Promise.resolve({ n: 0 }),
  ]);

  const totalPaginas = Math.max(1, Math.ceil((total?.n ?? 0) / POR_PAGINA));
  const fretes = (lista.results ?? []).map((f) => ({
    ...f,
    distancia_km: haversineKm(f.origem_lat, f.origem_lng, f.destino_lat, f.destino_lng),
  }));

  // Query string dos filtros (sem paginação) para links de navegação
  const qsFiltros = new URLSearchParams();
  for (const chave of ['categoria', 'uf', 'cidade', 'q', 'data'] as const) {
    if (filtros[chave]) qsFiltros.set(chave, filtros[chave]);
  }

  return c.html(
    <ListaFretesPage
      user={c.get('user')}
      alertas={alertas?.n ?? 0}
      fretes={fretes}
      categorias={categorias.results ?? []}
      filtros={filtros}
      totalPaginas={totalPaginas}
      totalResultados={total?.n ?? 0}
      queryString={qsFiltros.toString()}
      url={c.req.url}
      mensagem={q.msg || null}
      erro={q.erro || null}
    />,
  );
});

// ---------------- GET /novo-frete — formulário (só cliente) ----------------
freight.get('/novo-frete', async (c) => {
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;
  if (user!.papel !== 'cliente') {
    return c.redirect(redir('/fretes', { erro: 'Só clientes publicam fretes.' }));
  }

  const [categorias, alertas] = await Promise.all([
    c.env.DB.prepare('SELECT id, nome FROM categories ORDER BY nome').all<Category>(),
    c.env.DB.prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
      .bind(user!.id)
      .first<{ n: number }>(),
  ]);

  return c.html(
    <NovoFretePage
      user={user!}
      alertas={alertas?.n ?? 0}
      categorias={categorias.results ?? []}
      valores={{}}
      iaAtiva={Boolean(c.env.AI)}
    />,
  );
});

// ---------------- POST /fretes — publicar ----------------
freight.post('/fretes', async (c) => {
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;
  if (user!.papel !== 'cliente') {
    return c.redirect(redir('/fretes', { erro: 'Só clientes publicam fretes.' }));
  }

  const body = await c.req.parseBody();
  const bruto: Record<string, string> = {};
  for (const [k, v] of Object.entries(body)) if (typeof v === 'string') bruto[k] = v;

  const resultado = schemaFrete.safeParse(bruto);
  if (!resultado.success) {
    const [categorias, alertas] = await Promise.all([
      c.env.DB.prepare('SELECT id, nome FROM categories ORDER BY nome').all<Category>(),
      c.env.DB.prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
        .bind(user!.id)
        .first<{ n: number }>(),
    ]);
    return c.html(
      <NovoFretePage
        user={user!}
        alertas={alertas?.n ?? 0}
        categorias={categorias.results ?? []}
        valores={bruto}
        iaAtiva={Boolean(c.env.AI)}
        erro={primeiraMensagemErro(resultado.error)}
      />,
      400,
    );
  }
  const f = resultado.data;
  const itensCarga = parseItensForm(bruto);
  const resumo = resumoCarga(itensCarga);
  const prazos = calcularPrazos(new Date(), f.prazo_recebimento);

  // Coordenadas best-effort via BrasilAPI (sem coords, o mapa simplesmente não aparece)
  const [cepOrigem, cepDestino] = await Promise.all([
    consultarCep(f.origem_cep),
    consultarCep(f.destino_cep),
  ]);

  let freteId: number;
  try {
    const r = await c.env.DB
      .prepare(
        `INSERT INTO freight (
           user_id, category_id, descricao, comentario, ajudantes, preco_maximo,
           data_retirada, hora_retirada,
           origem_cep, origem_rua, origem_bairro, origem_cidade, origem_uf, origem_lat, origem_lng,
           destino_cep, destino_rua, destino_bairro, destino_cidade, destino_uf, destino_lat, destino_lng,
           carga_itens, carga_peso_kg, carga_volume_m3,
           situacao, encerra_em, prazo_escolha_em
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      )
      .bind(
        user!.id,
        f.category_id,
        f.descricao,
        f.comentario || null,
        f.ajudantes,
        f.preco_maximo,
        f.data_retirada,
        f.hora_retirada || null,
        f.origem_cep,
        f.origem_rua,
        f.origem_bairro,
        f.origem_cidade,
        f.origem_uf,
        cepOrigem?.lat ?? null,
        cepOrigem?.lng ?? null,
        f.destino_cep,
        f.destino_rua,
        f.destino_bairro,
        f.destino_cidade,
        f.destino_uf,
        cepDestino?.lat ?? null,
        cepDestino?.lng ?? null,
        itensCarga.length > 0 ? serializarItens(itensCarga) : null,
        itensCarga.length > 0 ? resumo.pesoKg : null,
        itensCarga.length > 0 ? resumo.volumeM3 : null,
        'aberto',
        prazos.encerraEm,
        prazos.prazoEscolhaEm,
      )
      .run();
    freteId = Number(r.meta.last_row_id);
  } catch {
    return c.redirect(redir('/novo-frete', { erro: 'Não foi possível publicar. Tente de novo.' }));
  }

  await audit(c.env.DB, user!.id, 'publicar_frete', 'freight', freteId);
  return c.redirect(redir(`/fretes/${freteId}`, { msg: 'Mudança publicada! 🚚 Agora é só esperar os orçamentos.' }));
});

// ---------------- GET /fretes/:id — detalhe ----------------
freight.get('/fretes/:id', async (c) => {
  const id = parseInt(c.req.param('id'), 10);
  if (!Number.isInteger(id) || id <= 0) return c.notFound();

  const user = c.get('user');
  const db = c.env.DB;
  const q = c.req.query();

  await aplicarPrazos(db);

  let frete = await db.prepare(`${SQL_RESUMO} WHERE f.id = ?`).bind(id).first<FreightResumo>();
  if (!frete) return c.notFound();

  const donoDoFrete = user?.id === frete.user_id;

  // Mudança sem propostas, expirada ou cancelada não fica pública. O dono
  // continua enxergando o próprio histórico.
  if (!donoDoFrete && !detalhePublico(frete.situacao)) return c.notFound();

  // Fretes antigos podem não ter coordenadas. Geocodifica sob demanda e
  // persiste o resultado para que o mapa funcione nas próximas visitas.
  if (
    frete.origem_lat == null ||
    frete.origem_lng == null ||
    frete.destino_lat == null ||
    frete.destino_lng == null
  ) {
    const [origemGeo, destinoGeo] = await Promise.all([
      frete.origem_lat == null || frete.origem_lng == null
        ? geocodificarEndereco({
            rua: frete.origem_rua,
            bairro: frete.origem_bairro,
            cidade: frete.origem_cidade,
            uf: frete.origem_uf,
          })
        : Promise.resolve(null),
      frete.destino_lat == null || frete.destino_lng == null
        ? geocodificarEndereco({
            rua: frete.destino_rua,
            bairro: frete.destino_bairro,
            cidade: frete.destino_cidade,
            uf: frete.destino_uf,
          })
        : Promise.resolve(null),
    ]);

    const atualizacoes: Promise<unknown>[] = [];
    if (origemGeo) {
      atualizacoes.push(
        db
          .prepare('UPDATE freight SET origem_lat = ?, origem_lng = ? WHERE id = ?')
          .bind(origemGeo.lat, origemGeo.lng, id)
          .run(),
      );
    }
    if (destinoGeo) {
      atualizacoes.push(
        db
          .prepare('UPDATE freight SET destino_lat = ?, destino_lng = ? WHERE id = ?')
          .bind(destinoGeo.lat, destinoGeo.lng, id)
          .run(),
      );
    }
    if (atualizacoes.length > 0) await Promise.all(atualizacoes);

    frete = {
      ...frete,
      origem_lat: origemGeo?.lat ?? frete.origem_lat,
      origem_lng: origemGeo?.lng ?? frete.origem_lng,
      destino_lat: destinoGeo?.lat ?? frete.destino_lat,
      destino_lng: destinoGeo?.lng ?? frete.destino_lng,
    };
  }

  const [lances, meuLance, alertas, caminhao] = await Promise.all([
    donoDoFrete
      ? db
          .prepare(
            `SELECT b.*, u.nome AS transportador_nome
             FROM bids b JOIN users u ON u.id = b.transportador_id
             WHERE b.freight_id = ? ORDER BY b.valor ASC`,
          )
          .bind(id)
          .all<BidComNome>()
      : Promise.resolve({ results: [] as BidComNome[] }),
    user?.papel === 'transportador'
      ? db
          .prepare(
            `SELECT b.*, u.nome AS transportador_nome
             FROM bids b JOIN users u ON u.id = b.transportador_id
             WHERE b.freight_id = ? AND b.transportador_id = ?`,
          )
          .bind(id, user.id)
          .first<BidComNome | null>()
      : Promise.resolve(null),
    user
      ? db
          .prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
          .bind(user.id)
          .first<{ n: number }>()
      : Promise.resolve({ n: 0 }),
    user?.papel === 'transportador'
      ? db.prepare('SELECT * FROM trucks WHERE user_id = ?').bind(user.id).first<Truck>()
      : Promise.resolve(null as Truck | null),
  ]);

  const rota = await calcularRota(
    frete.origem_lat != null && frete.origem_lng != null
      ? { lat: frete.origem_lat, lng: frete.origem_lng }
      : null,
    frete.destino_lat != null && frete.destino_lng != null
      ? { lat: frete.destino_lat, lng: frete.destino_lng }
      : null,
  );
  const linhaRetaKm = haversineKm(
    frete.origem_lat,
    frete.origem_lng,
    frete.destino_lat,
    frete.destino_lng,
  );
  const distanciaEstradaKm = rota?.distanciaKm ?? distanciaRodoviariaKm(linhaRetaKm);
  const itensCarga = parseItensDb(frete.carga_itens);
  const carga = itensCarga.length > 0 ? resumoCarga(itensCarga) : null;
  const veiculoRecomendado = carga ? sugerirVeiculoParaPlano(carga) : null;
  const medidasCaminhao = caminhao ? medidasDoCaminhao(caminhao) : null;
  const analiseCaminhao = carga && medidasCaminhao ? analisarEncaixe(carga, medidasCaminhao) : null;

  // O simulador de custo é exclusivo do transportador. O cliente vê apenas a
  // rota e os lances — nunca os custos internos de quem transporta.
  const transportador = user?.papel === 'transportador';
  const referencias = await lerReferencias(c.env.DB);
  const custoInicial: Partial<ParametrosCusto> | undefined = transportador
    ? caminhao
      ? {
          consumoKmL: caminhao.consumo_km_l,
          precoCombustivel: caminhao.diesel_preco,
          tipoCombustivel:
            caminhao.tipo_combustivel === 'gasolina' ? 'gasolina' : 'diesel',
          pedagioPorKm: caminhao.pedagio_por_km,
          manutencaoPorKm: caminhao.manutencao_por_km,
          arlaPorKm: caminhao.arla_por_km,
          custoFixo: caminhao.custo_fixo,
          margemPercentual: caminhao.margem_percentual,
          taxaBase: caminhao.taxa_base,
          horasServico: caminhao.horas_servico,
          custoHora: caminhao.custo_hora,
          custoAjudante: caminhao.custo_ajudante,
          ajudantes: frete.ajudantes,
          tipoVeiculo: caminhao.tipo as ParametrosCusto['tipoVeiculo'],
        }
      : parametrosComReferencias('van', { ajudantes: frete.ajudantes }, referencias)
    : undefined;
  const estimativaCaminhao =
    transportador && distanciaEstradaKm
      ? estimarCustoRodoviario(distanciaEstradaKm, custoInicial, {
          mercado: referencias.mercado,
        })
      : null;

  const previsao = await previsaoRetirada(frete.origem_lat, frete.origem_lng, frete.data_retirada);

  return c.html(
    <DetalheFretePage
      user={user}
      alertas={alertas?.n ?? 0}
      frete={{
        ...frete,
        distancia_km: linhaRetaKm,
      }}
      distanciaRodoviariaKm={distanciaEstradaKm}
      rotaGeometria={rota?.geometria ?? null}
      rotaFonte={rota?.fonte ?? null}
      lances={lances.results ?? []}
      meuLance={meuLance}
      donoDoFrete={donoDoFrete}
      previsao={previsao}
      iaAtiva={Boolean(c.env.AI)}
      caminhao={caminhao}
      carga={carga}
      analiseCaminhao={analiseCaminhao}
      veiculoRecomendado={veiculoRecomendado}
      estimativaCaminhao={estimativaCaminhao}
      custoInicial={custoInicial}
      referencias={transportador ? referencias : null}
      url={c.req.url}
      mensagem={q.msg || null}
      erro={q.erro || null}
    />,
  );
});


// ---------------- POST /fretes/:id/lances — dar/melhorar lance ----------------
freight.post('/fretes/:id/lances', async (c) => {
  const id = parseInt(c.req.param('id'), 10);
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;

  const voltarErro = (erro: string) => c.redirect(redir(`/fretes/${id}`, { erro }));

  const body = await c.req.parseBody();
  const bruto: Record<string, string> = {};
  for (const [k, v] of Object.entries(body)) if (typeof v === 'string') bruto[k] = v;

  const resultado = schemaLance.safeParse(bruto);
  if (!resultado.success) return voltarErro(primeiraMensagemErro(resultado.error));
  const { valor, mensagem } = resultado.data;

  const frete = await c.env.DB
    .prepare('SELECT user_id, situacao, encerra_em, preco_maximo, descricao FROM freight WHERE id = ?')
    .bind(id)
    .first<{
      user_id: number;
      situacao: string;
      encerra_em: string | null;
      preco_maximo: number | null;
      descricao: string;
    }>();
  if (!frete) return c.notFound();

  // Menor lance de OUTROS transportadores (para a regra do leilão reverso)
  const menorDosOutros = await c.env.DB
    .prepare(
      'SELECT transportador_id, valor FROM bids WHERE freight_id = ? AND transportador_id != ? ORDER BY valor ASC LIMIT 1',
    )
    .bind(id, user!.id)
    .first<{ transportador_id: number; valor: number } | null>();

  const regra = validarLance({
    papel: user!.papel,
    situacaoFrete: frete.situacao,
    valor,
    menorLanceDosOutros: menorDosOutros?.valor ?? null,
    precoMaximo: frete.preco_maximo,
    donoDoFrete: frete.user_id === user!.id,
    encerraEm: frete.encerra_em,
    agora: new Date(),
  });
  if (!regra.ok) return voltarErro(regra.erro);

  await c.env.DB
    .prepare(
      `INSERT INTO bids (freight_id, transportador_id, valor, mensagem)
       VALUES (?, ?, ?, ?)
       ON CONFLICT (freight_id, transportador_id)
       DO UPDATE SET valor = excluded.valor, mensagem = excluded.mensagem, criado_em = excluded.criado_em`,
    )
    .bind(id, user!.id, valor, mensagem || null)
    .run();

  // Alertas: dono do frete sempre; quem estava com o menor lance, se superado
  const avisos: Promise<void>[] = [
    criarAlerta(
      c.env.DB,
      frete.user_id,
      id,
      'novo_lance',
      `Novo orçamento de R$ ${valor.toFixed(2).replace('.', ',')} na sua mudança #${id}.`,
    ),
  ];
  if (menorDosOutros && valor < menorDosOutros.valor) {
    avisos.push(
      criarAlerta(
        c.env.DB,
        menorDosOutros.transportador_id,
        id,
        'lance_superado',
        `Seu orçamento na mudança #${id} foi superado. Envie um novo valor!`,
      ),
    );
  }
  await Promise.all(avisos);
  await audit(c.env.DB, user!.id, 'lance', 'freight', id, { valor });

  // Aviso por e-mail para o dono (Resend; sem chave vira log)
  const dono = await c.env.DB
    .prepare('SELECT email, nome FROM users WHERE id = ?')
    .bind(frete.user_id)
    .first<{ email: string; nome: string }>();
  if (dono?.email) {
    c.executionCtx.waitUntil(
      emailNovoLance(c.env, {
        para: dono.email,
        nomeDono: dono.nome,
        descricao: frete.descricao,
        valor,
        freteId: id,
        baseUrl: baseUrl(c),
      }),
    );
  }

  return c.redirect(redir(`/fretes/${id}`, { msg: 'Orçamento enviado! Boa sorte 🍀' }));
});

// ---------------- POST /fretes/:id/aceitar/:bidId — encerrar leilão ----------------
freight.post('/fretes/:id/aceitar/:bidId', async (c) => {
  const id = parseInt(c.req.param('id'), 10);
  const bidId = parseInt(c.req.param('bidId'), 10);
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;

  const voltarErro = (erro: string) => c.redirect(redir(`/fretes/${id}`, { erro }));

  const frete = await c.env.DB
    .prepare('SELECT user_id, situacao, prazo_escolha_em, descricao FROM freight WHERE id = ?')
    .bind(id)
    .first<{
      user_id: number;
      situacao: string;
      prazo_escolha_em: string | null;
      descricao: string;
    }>();
  if (!frete) return c.notFound();

  const lance = await c.env.DB
    .prepare('SELECT id, transportador_id, valor FROM bids WHERE id = ? AND freight_id = ?')
    .bind(bidId, id)
    .first<{ id: number; transportador_id: number; valor: number } | null>();
  if (!lance) return voltarErro('Orçamento não encontrado nesta mudança.');

  const agora = new Date();
  if (
    !podeAceitarLance(
      frete.user_id === user!.id,
      frete.situacao,
      frete.prazo_escolha_em,
      agora,
    )
  ) {
    return voltarErro(
      frete.situacao !== 'aberto' && frete.situacao !== 'em_escolha'
        ? 'Esta mudança não está mais aberta para escolha.'
        : frete.user_id !== user!.id
          ? 'Só o dono da mudança pode escolher o transportador.'
          : 'O prazo para escolher o transportador terminou.',
    );
  }

  // Atômico: marca vencedor e encerra na mesma transação. As duas condições
  // impedem aceite duplicado (duas abas, duplo clique) e aceite depois do
  // prazo de escolha, mesmo que a tela esteja desatualizada.
  const agoraIso = agora.toISOString();
  const [resultadoLance, resultadoFrete] = await c.env.DB.batch([
    c.env.DB
      .prepare(
        `UPDATE bids SET vencedor = 1
          WHERE id = ? AND freight_id = ?
            AND EXISTS (
              SELECT 1 FROM freight f
               WHERE f.id = bids.freight_id
                 AND f.situacao IN ('aberto', 'em_escolha')
                 AND (f.prazo_escolha_em IS NULL OR f.prazo_escolha_em > ?)
            )`,
      )
      .bind(bidId, id, agoraIso),
    c.env.DB
      .prepare(
        `UPDATE freight SET situacao = 'encerrado', status = 'encerrado'
          WHERE id = ? AND situacao IN ('aberto', 'em_escolha')
            AND (prazo_escolha_em IS NULL OR prazo_escolha_em > ?)`,
      )
      .bind(id, agoraIso),
  ]);

  if ((resultadoFrete?.meta.changes ?? 0) === 0 || (resultadoLance?.meta.changes ?? 0) === 0) {
    return voltarErro('Esta mudança já foi fechada ou o prazo de escolha terminou.');
  }

  // Alertas: vencedor comemora, os outros ficam sabendo
  const perdedores = await c.env.DB
    .prepare('SELECT DISTINCT transportador_id FROM bids WHERE freight_id = ? AND id != ?')
    .bind(id, bidId)
    .all<{ transportador_id: number }>();

  await Promise.all([
    criarAlerta(
      c.env.DB,
      lance.transportador_id,
      id,
      'voce_venceu',
      `🎉 Seu orçamento de R$ ${lance.valor.toFixed(2).replace('.', ',')} foi aceito para a mudança #${id} (${frete.descricao})!`,
    ),
    ...(perdedores.results ?? []).map((p) =>
      criarAlerta(c.env.DB, p.transportador_id, id, 'lance_perdido', `A mudança #${id} foi fechada.`),
    ),
  ]);
  await audit(c.env.DB, user!.id, 'aceitar_lance', 'freight', id, { bidId });

  // Aviso por e-mail para o vencedor
  const vencedor = await c.env.DB
    .prepare('SELECT email, nome FROM users WHERE id = ?')
    .bind(lance.transportador_id)
    .first<{ email: string; nome: string }>();
  if (vencedor?.email) {
    c.executionCtx.waitUntil(
      emailLanceVencedor(c.env, {
        para: vencedor.email,
        nome: vencedor.nome,
        descricao: frete.descricao,
        valor: lance.valor,
        freteId: id,
        baseUrl: baseUrl(c),
      }),
    );
  }

  return c.redirect(redir(`/fretes/${id}`, { msg: 'Orçamento aceito! Mudança fechada. 🤝' }));
});

// ---------------- POST /fretes/:id/cancelar — dono cancela ----------------
freight.post('/fretes/:id/cancelar', async (c) => {
  const id = parseInt(c.req.param('id'), 10);
  const user = c.get('user');
  const bloqueio = exigirLogin(user);
  if (bloqueio) return bloqueio;

  const voltarErro = (erro: string) => c.redirect(redir(`/fretes/${id}`, { erro }));

  const frete = await c.env.DB
    .prepare('SELECT user_id, situacao FROM freight WHERE id = ?')
    .bind(id)
    .first<{ user_id: number; situacao: string }>();
  if (!frete) return c.notFound();

  if (!podeCancelarFrete(frete.user_id === user!.id, frete.situacao)) {
    return voltarErro('Só o dono pode cancelar, e apenas enquanto a mudança está em andamento.');
  }

  await c.env.DB
    .prepare("UPDATE freight SET situacao = 'cancelado', status = 'cancelado' WHERE id = ?")
    .bind(id)
    .run();

  const licitantes = await c.env.DB
    .prepare('SELECT DISTINCT transportador_id FROM bids WHERE freight_id = ?')
    .bind(id)
    .all<{ transportador_id: number }>();

  await Promise.all(
    (licitantes.results ?? []).map((l) =>
      criarAlerta(c.env.DB, l.transportador_id, id, 'frete_cancelado', `O frete #${id} foi cancelado pelo cliente.`),
    ),
  );
  await audit(c.env.DB, user!.id, 'cancelar_frete', 'freight', id);

  return c.redirect(redir('/painel', { msg: 'Frete cancelado. Os licitantes foram avisados.' }));
});

export default freight;
