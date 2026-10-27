// APIs internas: CEP (BrasilAPI + ViaCEP + Nominatim), cidades (IBGE) e IA
// (Cloudflare Workers AI). O front nunca fala com serviços externos.
import { Hono } from 'hono';
import { consultarCep } from '../lib/geo';
import { calcularRota } from '../lib/rota';
import { analisarCargaComIa, melhorarTexto } from '../lib/ia';
import { limiteApi } from '../lib/rate-limit';
import { schemaIa, schemaIaCarga, primeiraMensagemErro } from '../lib/validate';
import { estimarCustoRodoviario } from '../lib/cost';
import { lerReferencias } from '../lib/referencias';
import { statusEmail } from '../lib/email';
import {
  analisarEncaixe,
  medidasDoCaminhao,
  parseItensDb,
  resumoCarga,
  rotuloTipoVeiculo,
  sugerirVeiculo,
  dimensoesTexto,
  volumeVeiculoM3,
} from '../lib/carga';
import type { Truck } from '../lib/db';
import type { Vars } from '../lib/session';
import type { AppBindings } from '../lib/bindings';

const api = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

const UA = 'frete-v2/1.0 (https://github.com/vitorsavicki/frete)';

api.get('/cep/:cep', async (c) => {
  const cep = (c.req.param('cep') ?? '').replace(/\D/g, '');
  if (cep.length !== 8) {
    return c.json({ erro: 'CEP inválido' }, 400);
  }

  const resultado = await consultarCep(cep);
  if (!resultado) {
    return c.json({ erro: 'CEP não encontrado' }, 404);
  }

  // CDN pode cachear por 1 dia: menos chamadas às APIs públicas
  c.header('Cache-Control', 'public, max-age=86400');
  return c.json(resultado);
});

api.get('/cidades/:uf', async (c) => {
  const uf = (c.req.param('uf') ?? '').toUpperCase();
  if (!/^[A-Z]{2}$/.test(uf)) return c.json({ erro: 'UF inválida' }, 400);

  try {
    const resp = await fetch(
      `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios?orderBy=nome`,
      { headers: { 'User-Agent': UA, Accept: 'application/json' }, signal: AbortSignal.timeout(6000) },
    );
    if (!resp.ok) return c.json({ erro: 'Não foi possível carregar as cidades' }, 502);
    const dados = (await resp.json()) as { nome?: string }[];
    const cidades = dados.map((m) => m.nome).filter((n): n is string => Boolean(n));
    c.header('Cache-Control', 'public, max-age=604800');
    return c.json({ uf, cidades });
  } catch {
    return c.json({ erro: 'IBGE indisponível' }, 502);
  }
});

// Referências configuráveis de custo. O simulador do navegador consome este
// endpoint em vez de carregar números fixos no JavaScript. Público e cacheável:
// não expõe dado pessoal nem o custo real de nenhum transportador.
api.get('/referencias', async (c) => {
  const referencias = await lerReferencias(c.env.DB);
  c.header('Cache-Control', 'public, max-age=300, stale-while-revalidate=3600');
  return c.json(referencias);
});

// Diagnóstico do e-mail: mostra provedores ativos e o tamanho da fila, sem
// destinatários nem segredos. Útil para conferir o setup gratuito em produção.
api.get('/email/status', async (c) => {
  const status = await statusEmail(c.env);
  c.header('Cache-Control', 'no-store');
  return c.json(status);
});

// Rota rodoviária real (OSRM) para o mapa e para a estimativa de custo.
// O navegador chama só este endpoint interno; o CSP continua fechado.
api.use('/rota', limiteApi);
api.get('/rota', async (c) => {
  const q = c.req.query();
  const origem = {
    lat: Number(q.origem_lat),
    lng: Number(q.origem_lng),
  };
  const destino = {
    lat: Number(q.destino_lat),
    lng: Number(q.destino_lng),
  };
  const valida = (p: { lat: number; lng: number }) =>
    Number.isFinite(p.lat) &&
    Number.isFinite(p.lng) &&
    Math.abs(p.lat) <= 90 &&
    Math.abs(p.lng) <= 180;

  if (!valida(origem) || !valida(destino)) {
    return c.json({ erro: 'Coordenadas inválidas' }, 400);
  }

  const rota = await calcularRota(origem, destino);
  if (!rota) return c.json({ erro: 'Não foi possível calcular a rota' }, 502);

  c.header('Cache-Control', 'public, max-age=86400');
  return c.json(rota);
});


api.use('/ia/*', limiteApi);

api.post('/ia/texto', async (c) => {
  if (!c.get('user')) {
    return c.json({ erro: 'Entre na sua conta para usar a IA.' }, 401);
  }

  const corpo = await c.req.json().catch(() => null);
  const resultado = schemaIa.safeParse(corpo);
  if (!resultado.success) {
    return c.json({ erro: primeiraMensagemErro(resultado.error) }, 400);
  }

  if (!c.env.AI) {
    return c.json({ erro: 'A IA não está disponível neste ambiente.' }, 503);
  }

  const texto = await melhorarTexto(c.env.AI, resultado.data.tipo, resultado.data.texto);
  if (!texto) {
    return c.json({ erro: 'Não consegui melhorar o texto agora. Tente de novo.' }, 503);
  }

  return c.json({ texto });
});

api.post('/ia/carga', async (c) => {
  const user = c.get('user');
  if (!user) {
    return c.json({ erro: 'Entre na sua conta para usar a IA.' }, 401);
  }
  if (user.papel !== 'transportador') {
    return c.json({ erro: 'A análise de carga é exclusiva para transportadores.' }, 403);
  }

  const corpo = await c.req.json().catch(() => null);
  const resultado = schemaIaCarga.safeParse(corpo);
  if (!resultado.success) {
    return c.json({ erro: primeiraMensagemErro(resultado.error) }, 400);
  }

  const db = c.env.DB;
  const frete = await db
    .prepare(
      `SELECT id, descricao, ajudantes, carga_itens, origem_lat, origem_lng, destino_lat, destino_lng
       FROM freight WHERE id = ?`,
    )
    .bind(resultado.data.freight_id)
    .first<{
      id: number;
      descricao: string;
      ajudantes: number;
      carga_itens: string | null;
      origem_lat: number | null;
      origem_lng: number | null;
      destino_lat: number | null;
      destino_lng: number | null;
    }>();
  if (!frete) return c.json({ erro: 'Frete não encontrado.' }, 404);

  const caminhao = await db
    .prepare('SELECT * FROM trucks WHERE user_id = ?')
    .bind(user.id)
    .first<Truck>();
  if (!caminhao) {
    return c.json(
      {
        erro: 'Cadastre seu caminhão para analisar se a carga cabe.',
        acao: '/caminhao',
      },
      409,
    );
  }

  const itens = parseItensDb(frete.carga_itens);
  if (itens.length === 0) {
    return c.json({ erro: 'Este frete não tem a lista de itens da carga.' }, 422);
  }

  const carga = resumoCarga(itens);
  const medidas = medidasDoCaminhao(caminhao);
  const encaixe = analisarEncaixe(carga, medidas);
  const rota = await calcularRota(
    { lat: frete.origem_lat ?? 0, lng: frete.origem_lng ?? 0 },
    { lat: frete.destino_lat ?? 0, lng: frete.destino_lng ?? 0 },
  );
  const distanciaRodoviariaKm = rota?.distanciaKm ?? null;
  const referencias = await lerReferencias(c.env.DB);
  const estimativa = estimarCustoRodoviario(
    distanciaRodoviariaKm,
    {
      consumoKmL: caminhao.consumo_km_l,
      precoCombustivel: caminhao.diesel_preco,
      tipoCombustivel: caminhao.tipo_combustivel === 'gasolina' ? 'gasolina' : 'diesel',
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
      tipoVeiculo: caminhao.tipo as never,
    },
    { mercado: referencias.mercado },
  );
  const sugestao = sugerirVeiculo(carga);

  if (!c.env.AI) {
    return c.json(
      {
        erro: 'A IA não está disponível neste ambiente. A conferência determinística continua valendo.',
        encaixe,
        precoSugerido: estimativa?.precoSugerido ?? null,
      },
      503,
    );
  }

  const texto = await analisarCargaComIa(c.env.AI, {
    descricao: frete.descricao,
    itens: carga.itens.map((item) => ({
      nome: item.nome,
      quantidade: item.quantidade,
      pesoKg: item.pesoKg,
      dimensoes: dimensoesTexto(item),
    })),
    pesoTotalKg: carga.pesoKg,
    volumeTotalM3: carga.volumeM3,
    capacidadeKg: medidas.capacidadeKg,
    volumeVeiculoM3: Math.round(volumeVeiculoM3(medidas) * 100) / 100,
    caminhao: caminhao.nome,
    tipoCaminhao: rotuloTipoVeiculo(caminhao.tipo),
    dimensoesCaminhao: dimensoesTexto(medidas),
    encaixe: {
      status: encaixe.status,
      ocupacaoVolume: encaixe.ocupacaoVolume,
      ocupacaoPeso: encaixe.ocupacaoPeso,
      motivos: encaixe.motivos,
    },
    distanciaRodoviariaKm,
    precoSugerido: estimativa?.precoSugerido ?? null,
    precoMinimo: estimativa?.precoMinimo ?? null,
    ajudantes: frete.ajudantes,
  });

  if (!texto) {
    return c.json(
      {
        erro: 'A IA não conseguiu analisar agora. A conferência determinística continua valendo.',
        encaixe,
        precoSugerido: estimativa?.precoSugerido ?? null,
      },
      503,
    );
  }

  return c.json({
    texto,
    encaixe,
    precoSugerido: estimativa?.precoSugerido ?? null,
    precoMinimo: estimativa?.precoMinimo ?? null,
    veiculoRecomendado: sugestao?.veiculo.nome ?? null,
  });
});

export default api;
