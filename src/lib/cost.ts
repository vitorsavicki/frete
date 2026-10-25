// Estimativa de custo operacional do transportador (combustível, pedágio,
// manutenção, ARLA, custo fixo e margem). Tudo determinístico e testável.
//
// A distância informada é a linha reta (haversine); aplicamos um fator de
// rodovia para aproximar o trajeto real. O transportador pode ajustar todos
// os parâmetros na interface — os padrões são apenas um ponto de partida.

export interface ParametrosCusto {
  /** Consumo médio do veículo em km por litro. */
  consumoKmL: number;
  /** Preço do litro do diesel. */
  precoDiesel: number;
  /** Pedágio médio por km rodado. */
  pedagioPorKm: number;
  /** Manutenção/desgaste por km rodado. */
  manutencaoPorKm: number;
  /** ARLA 32 por km rodado. */
  arlaPorKm: number;
  /** Diária, ajudante, alimentação etc. (valor fixo por frete). */
  custoFixo: number;
  /** Margem de lucro desejada, em %. */
  margemPercentual: number;
  /** Fator de correção linha reta → estrada. */
  fatorRodovia: number;
}

export const CUSTO_PADRAO: ParametrosCusto = {
  consumoKmL: 8,
  precoDiesel: 6.3,
  pedagioPorKm: 0.1,
  manutencaoPorKm: 0.35,
  arlaPorKm: 0.05,
  custoFixo: 0,
  margemPercentual: 20,
  fatorRodovia: 1.25,
};

export interface EstimativaCusto {
  distanciaRodoviariaKm: number;
  litros: number;
  combustivel: number;
  pedagio: number;
  manutencao: number;
  arla: number;
  custoFixo: number;
  custoTotal: number;
  margem: number;
  custoPorKm: number;
  /** Menor valor que cobre o custo (sem margem), arredondado para cima em R$ 10. */
  precoMinimo: number;
  /** Valor sugerido com a margem desejada, arredondado para cima em R$ 10. */
  precoSugerido: number;
}

const numeroValido = (valor: unknown, padrao: number, minimo = 0): number => {
  const n = typeof valor === 'number' ? valor : Number(valor);
  if (!Number.isFinite(n) || n < minimo) return padrao;
  return n;
};

export function normalizarParametros(parcial: Partial<ParametrosCusto> = {}): ParametrosCusto {
  return {
    consumoKmL: numeroValido(parcial.consumoKmL, CUSTO_PADRAO.consumoKmL, 1),
    precoDiesel: numeroValido(parcial.precoDiesel, CUSTO_PADRAO.precoDiesel, 0.01),
    pedagioPorKm: numeroValido(parcial.pedagioPorKm, CUSTO_PADRAO.pedagioPorKm),
    manutencaoPorKm: numeroValido(parcial.manutencaoPorKm, CUSTO_PADRAO.manutencaoPorKm),
    arlaPorKm: numeroValido(parcial.arlaPorKm, CUSTO_PADRAO.arlaPorKm),
    custoFixo: numeroValido(parcial.custoFixo, CUSTO_PADRAO.custoFixo),
    margemPercentual: Math.min(100, numeroValido(parcial.margemPercentual, CUSTO_PADRAO.margemPercentual)),
    fatorRodovia: numeroValido(parcial.fatorRodovia, CUSTO_PADRAO.fatorRodovia, 1),
  };
}

const arredondarMoeda = (valor: number): number => Math.round(valor * 100) / 100;
const arredondarDez = (valor: number): number => Math.ceil(valor / 10) * 10;

export function estimarCusto(
  distanciaLinhaRetaKm: number | null | undefined,
  parametros: Partial<ParametrosCusto> = {},
): EstimativaCusto | null {
  if (distanciaLinhaRetaKm == null || !Number.isFinite(distanciaLinhaRetaKm) || distanciaLinhaRetaKm <= 0) {
    return null;
  }

  const p = normalizarParametros(parametros);
  const distanciaRodoviariaKm = Math.round(distanciaLinhaRetaKm * p.fatorRodovia * 10) / 10;
  const litros = distanciaRodoviariaKm / p.consumoKmL;
  const combustivel = litros * p.precoDiesel;
  const pedagio = distanciaRodoviariaKm * p.pedagioPorKm;
  const manutencao = distanciaRodoviariaKm * p.manutencaoPorKm;
  const arla = distanciaRodoviariaKm * p.arlaPorKm;
  const custoTotal = combustivel + pedagio + manutencao + arla + p.custoFixo;
  const margem = custoTotal * (p.margemPercentual / 100);

  return {
    distanciaRodoviariaKm,
    litros: arredondarMoeda(litros),
    combustivel: arredondarMoeda(combustivel),
    pedagio: arredondarMoeda(pedagio),
    manutencao: arredondarMoeda(manutencao),
    arla: arredondarMoeda(arla),
    custoFixo: arredondarMoeda(p.custoFixo),
    custoTotal: arredondarMoeda(custoTotal),
    margem: arredondarMoeda(margem),
    custoPorKm: arredondarMoeda(custoTotal / distanciaRodoviariaKm),
    precoMinimo: arredondarDez(custoTotal),
    precoSugerido: arredondarDez(custoTotal + margem),
  };
}
