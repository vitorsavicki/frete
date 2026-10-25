// APIs internas: CEP (BrasilAPI + ViaCEP + Nominatim), cidades (IBGE) e IA
// (Cloudflare Workers AI). O front nunca fala com serviços externos.
import { Hono } from 'hono';
import { consultarCep, haversineKm } from '../lib/geo';
import { analisarCargaComIa, melhorarTexto } from '../lib/ia';
import { limiteApi } from '../lib/rate-limit';
import { schemaIa, schemaIaCarga, primeiraMensagemErro } from '../lib/validate';
import { estimarCusto } from '../lib/cost';
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
  const distanciaKm = haversineKm(frete.origem_lat, frete.origem_lng, frete.destino_lat, frete.destino_lng);
  const estimativa = estimarCusto(distanciaKm, {
    consumoKmL: caminhao.consumo_km_l,
    precoDiesel: caminhao.diesel_preco,
    custoFixo: caminhao.custo_fixo + frete.ajudantes * 120,
    margemPercentual: caminhao.margem_percentual,
  });
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
    distanciaRodoviariaKm: distanciaKm,
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
