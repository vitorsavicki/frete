// Estimativa de custo operacional do transportador.
//
// O modelo antigo só multiplicava a distância pelo consumo e aplicava uma
// margem. Isso gerava valores irreais para mudanças curtas (uma van de 16 km
// saía por menos de R$ 40). Agora o cálculo inclui:
// - taxa de saída (deslocamento até o local, tempo de carga/descarga);
// - horas de serviço e custo da hora;
// - ajudantes (quantidade × custo por ajudante);
// - combustível, pedágio, manutenção, ARLA e custo fixo;
// - margem desejada pelo transportador;
// - faixa de referência de mercado por tipo de veículo.
//
// O transportador continua livre para definir o valor final do lance: a
// sugestão é apenas uma referência. Nenhum parâmetro é imposto pela
// plataforma — todos podem ser ajustados no perfil e no simulador.

export type TipoVeiculoCusto = 'fiorino' | 'van' | 'tres_quartos' | 'bau' | 'carreta' | 'outro';
export type TipoCombustivel = 'diesel' | 'gasolina';

export interface ParametrosCusto {
  /** Consumo médio do veículo em km por litro. */
  consumoKmL: number;
  /** Preço do litro do combustível escolhido. */
  precoCombustivel: number;
  /** Diesel ou gasolina — muda o preço de referência e o ARLA. */
  tipoCombustivel: TipoCombustivel;
  /** Pedágio médio por km rodado. */
  pedagioPorKm: number;
  /** Manutenção/desgaste por km rodado. */
  manutencaoPorKm: number;
  /** ARLA 32 por km rodado (só se aplica a diesel). */
  arlaPorKm: number;
  /** Diária, alimentação, estacionamento etc. (valor fixo por frete). */
  custoFixo: number;
  /** Margem de lucro desejada, em %. */
  margemPercentual: number;
  /** Fator de correção linha reta → estrada (usado quando não há rota real). */
  fatorRodovia: number;
  /** Taxa de saída: cobre o deslocamento até a origem e o tempo de carga. */
  taxaBase: number;
  /** Horas de serviço previstas para carga, descarga e deslocamento urbano. */
  horasServico: number;
  /** Custo da hora de serviço (mão de obra do próprio transportador). */
  custoHora: number;
  /** Custo por ajudante, por frete. */
  custoAjudante: number;
  /** Quantidade de ajudantes necessários neste frete. */
  ajudantes: number;
  /** Tipo do veículo, usado para a faixa de mercado. */
  tipoVeiculo: TipoVeiculoCusto;
}

export interface ReferenciaMercadoBase {
  rotulo: string;
  /** Piso para uma mudança local, mesmo com poucos quilômetros. */
  piso: number;
  /** Teto da faixa local, para não encolher a referência em trechos curtos. */
  localMaximo: number;
  /** Referência por km para trechos maiores. */
  porKmMin: number;
  porKmMax: number;
}

export interface ReferenciaMercado extends ReferenciaMercadoBase {
  tipo: TipoVeiculoCusto;
  minimo: number;
  maximo: number;
}

// Valores de referência pesquisados em empresas de mudança e no mercado
// brasileiro (2025/2026). São faixas amplas de propósito: região, acesso,
// andar, embalagem, temporada e pedágio mudam bastante o preço final.
export const REFERENCIA_MERCADO: Record<TipoVeiculoCusto, ReferenciaMercadoBase> = {
  fiorino: {
    rotulo: 'Fiorino',
    piso: 250,
    localMaximo: 450,
    porKmMin: 3.5,
    porKmMax: 5.5,
  },
  van: {
    rotulo: 'Van',
    piso: 350,
    localMaximo: 650,
    porKmMin: 4,
    porKmMax: 6.5,
  },
  tres_quartos: {
    rotulo: 'Caminhão 3/4',
    piso: 450,
    localMaximo: 850,
    porKmMin: 5,
    porKmMax: 8.5,
  },
  bau: {
    rotulo: 'Caminhão baú',
    piso: 650,
    localMaximo: 1200,
    porKmMin: 7,
    porKmMax: 11,
  },
  carreta: {
    rotulo: 'Carreta',
    piso: 900,
    localMaximo: 1800,
    porKmMin: 9,
    porKmMax: 14,
  },
  outro: {
    rotulo: 'Outro veículo',
    piso: 300,
    localMaximo: 800,
    porKmMin: 4,
    porKmMax: 8,
  },
};

// Taxa de saída padrão por tipo de veículo. O transportador pode alterar no
// perfil ou no simulador; estes valores só evitam sugestões irreais.
export const TAXA_BASE_POR_TIPO: Record<TipoVeiculoCusto, number> = {
  fiorino: 120,
  van: 180,
  tres_quartos: 250,
  bau: 400,
  carreta: 600,
  outro: 180,
};

// Referências de combustível (média nacional, outubro/2026). São só pontos de
// partida: o transportador informa o preço que paga na bomba e escolhe entre
// diesel e gasolina no perfil do veículo.
export const PRECO_COMBUSTIVEL_REFERENCIA: Record<TipoCombustivel, number> = {
  diesel: 7.37,
  gasolina: 6.09,
};

export const DIESEL_REFERENCIA = PRECO_COMBUSTIVEL_REFERENCIA.diesel;
export const GASOLINA_REFERENCIA = PRECO_COMBUSTIVEL_REFERENCIA.gasolina;

// Veículos leves costumam ser flex/gasolina; os pesados, diesel. O transportador
// pode trocar no perfil — isto é apenas a sugestão inicial.
export const COMBUSTIVEL_POR_TIPO: Record<TipoVeiculoCusto, TipoCombustivel> = {
  fiorino: 'gasolina',
  van: 'diesel',
  tres_quartos: 'diesel',
  bau: 'diesel',
  carreta: 'diesel',
  outro: 'diesel',
};

export const CUSTO_PADRAO: ParametrosCusto = {
  consumoKmL: 8,
  precoCombustivel: DIESEL_REFERENCIA,
  tipoCombustivel: 'diesel',
  pedagioPorKm: 0.1,
  manutencaoPorKm: 0.45,
  arlaPorKm: 0.06,
  custoFixo: 0,
  margemPercentual: 20,
  fatorRodovia: 1.25,
  taxaBase: 180,
  horasServico: 2,
  custoHora: 45,
  custoAjudante: 120,
  ajudantes: 0,
  tipoVeiculo: 'van',
};

export interface EstimativaCusto {
  distanciaRodoviariaKm: number;
  tipoCombustivel: TipoCombustivel;
  precoCombustivel: number;
  litros: number;
  combustivel: number;
  pedagio: number;
  manutencao: number;
  arla: number;
  taxaBase: number;
  horasServico: number;
  custoHoras: number;
  ajudantes: number;
  custoAjudantes: number;
  custoFixo: number;
  custoTotal: number;
  margem: number;
  custoPorKm: number;
  /** Custo + margem, antes do piso de mercado. */
  precoCusto: number;
  /** Menor valor que cobre o custo (sem margem), arredondado para cima em R$ 10. */
  precoMinimo: number;
  /** Piso da faixa de mercado para o tipo de veículo e a distância. */
  pisoMercado: number;
  /** Faixa de referência de mercado (mínimo e máximo). */
  referenciaMercado: ReferenciaMercado;
  /** Valor sugerido: custo + margem, mas nunca abaixo do piso de mercado. */
  precoSugerido: number;
  /** Quanto o piso de mercado acrescentou à sugestão (0 quando não houve ajuste). */
  ajusteMercado: number;
  /** true quando o custo + margem fica abaixo do piso de mercado. */
  abaixoDoMercado: boolean;
}

const TIPOS_VEICULO: TipoVeiculoCusto[] = [
  'fiorino',
  'van',
  'tres_quartos',
  'bau',
  'carreta',
  'outro',
];

const numeroValido = (valor: unknown, padrao: number, minimo = 0, maximo = Number.POSITIVE_INFINITY): number => {
  const n = typeof valor === 'number' ? valor : Number(valor);
  if (!Number.isFinite(n) || n < minimo) return padrao;
  return Math.min(maximo, n);
};

function normalizarTipo(valor: unknown): TipoVeiculoCusto {
  const texto = typeof valor === 'string' ? valor : '';
  return (TIPOS_VEICULO as string[]).includes(texto) ? (texto as TipoVeiculoCusto) : CUSTO_PADRAO.tipoVeiculo;
}

function normalizarCombustivel(valor: unknown): TipoCombustivel {
  return valor === 'gasolina' || valor === 'diesel' ? valor : CUSTO_PADRAO.tipoCombustivel;
}

export function normalizarParametros(parcial: Partial<ParametrosCusto> = {}): ParametrosCusto {
  const tipoVeiculo = normalizarTipo(parcial.tipoVeiculo);
  const tipoCombustivel =
    parcial.tipoCombustivel == null
      ? COMBUSTIVEL_POR_TIPO[tipoVeiculo]
      : normalizarCombustivel(parcial.tipoCombustivel);
  const taxaBasePadrao = TAXA_BASE_POR_TIPO[tipoVeiculo] ?? CUSTO_PADRAO.taxaBase;
  const precoCombustivelPadrao = PRECO_COMBUSTIVEL_REFERENCIA[tipoCombustivel];
  const arlaPadrao = tipoCombustivel === 'diesel' ? CUSTO_PADRAO.arlaPorKm : 0;

  return {
    consumoKmL: numeroValido(parcial.consumoKmL, CUSTO_PADRAO.consumoKmL, 1, 60),
    precoCombustivel: numeroValido(parcial.precoCombustivel, precoCombustivelPadrao, 0.01, 50),
    tipoCombustivel,
    pedagioPorKm: numeroValido(parcial.pedagioPorKm, CUSTO_PADRAO.pedagioPorKm, 0, 50),
    manutencaoPorKm: numeroValido(parcial.manutencaoPorKm, CUSTO_PADRAO.manutencaoPorKm, 0, 50),
    // Gasolina não usa ARLA 32; zerar evita cobrar um insumo que não existe.
    arlaPorKm: tipoCombustivel === 'diesel' ? numeroValido(parcial.arlaPorKm, arlaPadrao, 0, 50) : 0,
    custoFixo: numeroValido(parcial.custoFixo, CUSTO_PADRAO.custoFixo, 0, 1_000_000),
    margemPercentual: numeroValido(
      parcial.margemPercentual,
      CUSTO_PADRAO.margemPercentual,
      0,
      100,
    ),
    fatorRodovia: numeroValido(parcial.fatorRodovia, CUSTO_PADRAO.fatorRodovia, 1, 3),
    taxaBase: numeroValido(parcial.taxaBase, taxaBasePadrao, 0, 1_000_000),
    horasServico: numeroValido(parcial.horasServico, CUSTO_PADRAO.horasServico, 0, 48),
    custoHora: numeroValido(parcial.custoHora, CUSTO_PADRAO.custoHora, 0, 10_000),
    custoAjudante: numeroValido(parcial.custoAjudante, CUSTO_PADRAO.custoAjudante, 0, 10_000),
    ajudantes: Math.round(numeroValido(parcial.ajudantes, CUSTO_PADRAO.ajudantes, 0, 20)),
    tipoVeiculo,
  };
}

const arredondarMoeda = (valor: number): number => Math.round(valor * 100) / 100;
const arredondarDez = (valor: number): number => Math.ceil(valor / 10) * 10;

/** Faixa de mercado para um tipo de veículo e uma distância rodoviária. */
export function referenciaMercadoPara(
  tipoVeiculo: string | null | undefined,
  distanciaRodoviariaKm: number,
  tabela: Record<TipoVeiculoCusto, ReferenciaMercadoBase> = REFERENCIA_MERCADO,
): ReferenciaMercado {
  const tipo = normalizarTipo(tipoVeiculo);
  const base = tabela[tipo] ?? REFERENCIA_MERCADO[tipo];
  const distancia = Number.isFinite(distanciaRodoviariaKm) && distanciaRodoviariaKm > 0
    ? distanciaRodoviariaKm
    : 0;
  const minimo = arredondarDez(Math.max(base.piso, distancia * base.porKmMin));
  const maximo = arredondarDez(
    Math.max(base.localMaximo, distancia * base.porKmMax, minimo + 100),
  );

  return { ...base, tipo, minimo, maximo };
}

/**
 * Parâmetros iniciais para um veículo. Use no lugar de espalhar CUSTO_PADRAO
 * quando quiser que a taxa de saída acompanhe o tipo de veículo.
 */
export function parametrosParaVeiculo(
  tipoVeiculo: string | null | undefined,
  sobrescritas: Partial<ParametrosCusto> = {},
): ParametrosCusto {
  const tipo = normalizarTipo(tipoVeiculo);
  const tipoCombustivel = normalizarCombustivel(
    sobrescritas.tipoCombustivel ?? COMBUSTIVEL_POR_TIPO[tipo],
  );
  return normalizarParametros({
    ...CUSTO_PADRAO,
    ...sobrescritas,
    tipoVeiculo: tipo,
    tipoCombustivel,
    precoCombustivel:
      sobrescritas.precoCombustivel ?? PRECO_COMBUSTIVEL_REFERENCIA[tipoCombustivel],
    taxaBase: sobrescritas.taxaBase ?? TAXA_BASE_POR_TIPO[tipo],
  });
}

export interface OpcoesEstimativa {
  /** true quando a distância já é rodoviária (OSRM). */
  rodoviaria?: boolean;
  /** Faixa de mercado configurada (D1). Sem isso, usa a tabela do código. */
  mercado?: Record<TipoVeiculoCusto, ReferenciaMercadoBase>;
}

export function estimarCusto(
  distanciaKm: number | null | undefined,
  parametros: Partial<ParametrosCusto> = {},
  opcoes: OpcoesEstimativa = {},
): EstimativaCusto | null {
  if (distanciaKm == null || !Number.isFinite(distanciaKm) || distanciaKm <= 0) {
    return null;
  }

  const p = normalizarParametros(parametros);
  // Quando a distância já veio do OSRM (rota real), não aplicamos o fator de
  // correção de novo. O fator continua valendo para a estimativa por linha reta.
  const distanciaRodoviariaKm = opcoes.rodoviaria
    ? Math.round(distanciaKm * 10) / 10
    : Math.round(distanciaKm * p.fatorRodovia * 10) / 10;
  const litros = distanciaRodoviariaKm / p.consumoKmL;
  const combustivel = litros * p.precoCombustivel;
  const pedagio = distanciaRodoviariaKm * p.pedagioPorKm;
  const manutencao = distanciaRodoviariaKm * p.manutencaoPorKm;
  const arla = p.tipoCombustivel === 'diesel' ? distanciaRodoviariaKm * p.arlaPorKm : 0;
  const custoHoras = p.horasServico * p.custoHora;
  const custoAjudantes = p.ajudantes * p.custoAjudante;
  const custoTotal =
    combustivel +
    pedagio +
    manutencao +
    arla +
    p.taxaBase +
    custoHoras +
    custoAjudantes +
    p.custoFixo;
  const margem = custoTotal * (p.margemPercentual / 100);
  const precoCusto = arredondarDez(custoTotal + margem);
  const referenciaMercado = referenciaMercadoPara(
    p.tipoVeiculo,
    distanciaRodoviariaKm,
    opcoes.mercado,
  );
  const precoSugerido = Math.max(precoCusto, referenciaMercado.minimo);

  return {
    distanciaRodoviariaKm,
    tipoCombustivel: p.tipoCombustivel,
    precoCombustivel: arredondarMoeda(p.precoCombustivel),
    litros: arredondarMoeda(litros),
    combustivel: arredondarMoeda(combustivel),
    pedagio: arredondarMoeda(pedagio),
    manutencao: arredondarMoeda(manutencao),
    arla: arredondarMoeda(arla),
    taxaBase: arredondarMoeda(p.taxaBase),
    horasServico: p.horasServico,
    custoHoras: arredondarMoeda(custoHoras),
    ajudantes: p.ajudantes,
    custoAjudantes: arredondarMoeda(custoAjudantes),
    custoFixo: arredondarMoeda(p.custoFixo),
    custoTotal: arredondarMoeda(custoTotal),
    margem: arredondarMoeda(margem),
    custoPorKm: arredondarMoeda(custoTotal / distanciaRodoviariaKm),
    precoCusto,
    precoMinimo: arredondarDez(custoTotal),
    pisoMercado: referenciaMercado.minimo,
    referenciaMercado,
    precoSugerido,
    ajusteMercado: arredondarMoeda(precoSugerido - precoCusto),
    abaixoDoMercado: precoCusto < referenciaMercado.minimo,
  };
}

/**
 * Atalho para quando a distância já é rodoviária (OSRM). Evita aplicar o
 * fator linha reta → estrada duas vezes.
 */
export function estimarCustoRodoviario(
  distanciaRodoviariaKm: number | null | undefined,
  parametros: Partial<ParametrosCusto> = {},
  opcoes: Omit<OpcoesEstimativa, 'rodoviaria'> = {},
): EstimativaCusto | null {
  return estimarCusto(distanciaRodoviariaKm, parametros, { ...opcoes, rodoviaria: true });
}
