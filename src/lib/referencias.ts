// Referências configuráveis de custo e de mercado.
//
// A tabela `configuracoes` guarda JSON editável por SQL (sem deploy). Esta
// camada lê, valida e mescla com os padrões do código. A API pública
// `GET /api/referencias` expõe o resultado para o simulador do navegador — que
// continua sem falar com serviços externos por causa da CSP fechada.
//
// Regra de ouro: nada aqui é obrigatório. O transportador sempre pode editar
// os valores no perfil e no simulador; o lance final é decisão dele.

import {
  CUSTO_PADRAO,
  PRECO_COMBUSTIVEL_REFERENCIA,
  REFERENCIA_MERCADO,
  TAXA_BASE_POR_TIPO,
  parametrosParaVeiculo,
  type ParametrosCusto,
  type ReferenciaMercadoBase,
  type TipoCombustivel,
  type TipoVeiculoCusto,
} from './cost';

export interface CustosReferencia {
  pedagioPorKm: number;
  manutencaoPorKm: number;
  arlaPorKm: number;
  horasServico: number;
  custoHora: number;
  custoAjudante: number;
  fatorRodovia: number;
  custoFixo: number;
  margemPercentual: number;
}

export interface Referencias {
  combustivel: Record<TipoCombustivel, number>;
  custos: CustosReferencia;
  taxaBasePorTipo: Record<TipoVeiculoCusto, number>;
  mercado: Record<TipoVeiculoCusto, ReferenciaMercadoBase>;
  /** Data da última alteração lida do banco (null quando veio só dos padrões). */
  atualizadoEm: string | null;
  /** 'd1' quando a tabela respondeu; 'padrao' quando não havia banco/linhas. */
  origem: 'd1' | 'padrao';
}

const TIPOS: TipoVeiculoCusto[] = ['fiorino', 'van', 'tres_quartos', 'bau', 'carreta', 'outro'];

const CUSTOS_PADRAO: CustosReferencia = {
  pedagioPorKm: CUSTO_PADRAO.pedagioPorKm,
  manutencaoPorKm: CUSTO_PADRAO.manutencaoPorKm,
  arlaPorKm: CUSTO_PADRAO.arlaPorKm,
  horasServico: CUSTO_PADRAO.horasServico,
  custoHora: CUSTO_PADRAO.custoHora,
  custoAjudante: CUSTO_PADRAO.custoAjudante,
  fatorRodovia: CUSTO_PADRAO.fatorRodovia,
  custoFixo: CUSTO_PADRAO.custoFixo,
  margemPercentual: CUSTO_PADRAO.margemPercentual,
};

function padroes(): Referencias {
  return {
    combustivel: { ...PRECO_COMBUSTIVEL_REFERENCIA },
    custos: { ...CUSTOS_PADRAO },
    taxaBasePorTipo: { ...TAXA_BASE_POR_TIPO },
    mercado: Object.fromEntries(
      TIPOS.map((tipo) => [tipo, { ...REFERENCIA_MERCADO[tipo] }]),
    ) as Record<TipoVeiculoCusto, ReferenciaMercadoBase>,
    atualizadoEm: null,
    origem: 'padrao',
  };
}

function numero(valor: unknown, padrao: number, minimo: number, maximo: number): number {
  const n = typeof valor === 'number' ? valor : Number(valor);
  if (!Number.isFinite(n) || n < minimo || n > maximo) return padrao;
  return n;
}

function lerJson(valor: string | null | undefined): Record<string, unknown> | null {
  if (!valor) return null;
  try {
    const dados: unknown = JSON.parse(valor);
    if (!dados || typeof dados !== 'object' || Array.isArray(dados)) return null;
    return dados as Record<string, unknown>;
  } catch {
    return null;
  }
}

const MAX_CACHE_MS = 60_000;
let cache: { valor: Referencias; expiraEm: number } | null = null;

/** Limpa o cache em memória (testes e diagnóstico). */
export function limparCacheReferencias(): void {
  cache = null;
}

/**
 * Lê as referências do D1 e mescla com os padrões. Nunca lança: em caso de
 * falha, o site continua com os valores de segurança do código.
 */
export async function lerReferencias(db: D1Database | undefined): Promise<Referencias> {
  const agora = Date.now();
  if (cache && cache.expiraEm > agora) return cache.valor;

  const base = padroes();
  if (!db) return base;

  try {
    const resultado = await db
      .prepare('SELECT chave, valor, atualizado_em FROM configuracoes')
      .all<{ chave: string; valor: string; atualizado_em: string | null }>();
    const linhas = resultado.results ?? [];
    if (linhas.length === 0) {
      cache = { valor: base, expiraEm: agora + MAX_CACHE_MS };
      return base;
    }

    const porChave = new Map(linhas.map((linha) => [linha.chave, linha]));
    let atualizadoEm: string | null = null;
    for (const linha of linhas) {
      if (linha.atualizado_em && (!atualizadoEm || linha.atualizado_em > atualizadoEm)) {
        atualizadoEm = linha.atualizado_em;
      }
    }

    const combustivel = lerJson(porChave.get('combustivel')?.valor);
    if (combustivel) {
      base.combustivel.diesel = numero(
        combustivel.diesel,
        base.combustivel.diesel,
        1,
        30,
      );
      base.combustivel.gasolina = numero(
        combustivel.gasolina,
        base.combustivel.gasolina,
        1,
        30,
      );
    }

    const custos = lerJson(porChave.get('custos')?.valor);
    if (custos) {
      base.custos = {
        pedagioPorKm: numero(custos.pedagioPorKm, base.custos.pedagioPorKm, 0, 50),
        manutencaoPorKm: numero(custos.manutencaoPorKm, base.custos.manutencaoPorKm, 0, 50),
        arlaPorKm: numero(custos.arlaPorKm, base.custos.arlaPorKm, 0, 50),
        horasServico: numero(custos.horasServico, base.custos.horasServico, 0, 48),
        custoHora: numero(custos.custoHora, base.custos.custoHora, 0, 10_000),
        custoAjudante: numero(custos.custoAjudante, base.custos.custoAjudante, 0, 10_000),
        fatorRodovia: numero(custos.fatorRodovia, base.custos.fatorRodovia, 1, 3),
        custoFixo: numero(custos.custoFixo, base.custos.custoFixo, 0, 1_000_000),
        margemPercentual: numero(custos.margemPercentual, base.custos.margemPercentual, 0, 100),
      };
    }

    const taxas = lerJson(porChave.get('taxa_base')?.valor);
    if (taxas) {
      for (const tipo of TIPOS) {
        base.taxaBasePorTipo[tipo] = numero(
          taxas[tipo],
          base.taxaBasePorTipo[tipo],
          0,
          100_000,
        );
      }
    }

    const mercado = lerJson(porChave.get('mercado')?.valor);
    if (mercado) {
      for (const tipo of TIPOS) {
        const entrada = mercado[tipo];
        if (!entrada || typeof entrada !== 'object' || Array.isArray(entrada)) continue;
        const dados = entrada as Record<string, unknown>;
        const padrao = base.mercado[tipo];
        base.mercado[tipo] = {
          rotulo:
            typeof dados.rotulo === 'string' && dados.rotulo.trim()
              ? dados.rotulo.trim().slice(0, 40)
              : padrao.rotulo,
          piso: numero(dados.piso, padrao.piso, 0, 100_000),
          localMaximo: numero(dados.localMaximo, padrao.localMaximo, 0, 100_000),
          porKmMin: numero(dados.porKmMin, padrao.porKmMin, 0, 100),
          porKmMax: numero(dados.porKmMax, padrao.porKmMax, 0, 100),
        };
      }
    }

    base.atualizadoEm = atualizadoEm;
    base.origem = 'd1';
    cache = { valor: base, expiraEm: agora + MAX_CACHE_MS };
    return base;
  } catch (e) {
    // Tabela ainda não migrada ou D1 indisponível: segue com os padrões.
    console.error('referências: usando padrões', e);
    return base;
  }
}

/**
 * Parâmetros iniciais já com as referências configuradas. Use no servidor
 * quando não houver um caminhão cadastrado.
 */
export function parametrosComReferencias(
  tipoVeiculo: string | null | undefined,
  sobrescritas: Partial<ParametrosCusto>,
  referencias: Referencias,
): ParametrosCusto {
  const tipo = (TIPOS as string[]).includes(String(tipoVeiculo))
    ? (tipoVeiculo as TipoVeiculoCusto)
    : 'van';
  const tipoCombustivel =
    sobrescritas.tipoCombustivel ?? (tipo === 'fiorino' ? 'gasolina' : 'diesel');

  return parametrosParaVeiculo(tipo, {
    ...referencias.custos,
    ...sobrescritas,
    tipoCombustivel,
    precoCombustivel:
      sobrescritas.precoCombustivel ?? referencias.combustivel[tipoCombustivel],
    taxaBase: sobrescritas.taxaBase ?? referencias.taxaBasePorTipo[tipo],
  });
}
