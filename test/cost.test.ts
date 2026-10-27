import { describe, it, expect } from 'vitest';
import {
  estimarCusto,
  normalizarParametros,
  parametrosParaVeiculo,
  referenciaMercadoPara,
  CUSTO_PADRAO,
  PRECO_COMBUSTIVEL_REFERENCIA,
  TAXA_BASE_POR_TIPO,
} from '../src/lib/cost';

describe('Estimador de custo do transporte', () => {
  it('retorna null quando não há distância', () => {
    expect(estimarCusto(null)).toBeNull();
    expect(estimarCusto(undefined)).toBeNull();
    expect(estimarCusto(0)).toBeNull();
    expect(estimarCusto(-10)).toBeNull();
  });

  it('calcula a rota padrão de 100 km em linha reta', () => {
    const r = estimarCusto(100);
    expect(r).not.toBeNull();
    if (!r) return;

    expect(r.distanciaRodoviariaKm).toBe(125);
    expect(r.combustivel).toBe(115.16);
    expect(r.pedagio).toBe(12.5);
    expect(r.manutencao).toBe(56.25);
    expect(r.arla).toBe(7.5);
    expect(r.taxaBase).toBe(180);
    expect(r.custoHoras).toBe(90);
    expect(r.custoAjudantes).toBe(0);
    expect(r.custoTotal).toBe(461.41);
    expect(r.margem).toBe(92.28);
    expect(r.precoCusto).toBe(560);
    expect(r.precoMinimo).toBe(470);
    expect(r.pisoMercado).toBe(500);
    expect(r.referenciaMercado.minimo).toBe(500);
    expect(r.referenciaMercado.maximo).toBe(820);
    expect(r.precoSugerido).toBe(560);
    expect(r.ajusteMercado).toBe(0);
    expect(r.abaixoDoMercado).toBe(false);
    expect(r.custoPorKm).toBe(3.69);
  });

  it('respeita todos os parâmetros informados', () => {
    const r = estimarCusto(100, {
      consumoKmL: 10,
      precoCombustivel: 6,
      pedagioPorKm: 0.2,
      manutencaoPorKm: 0.5,
      arlaPorKm: 0,
      custoFixo: 100,
      margemPercentual: 10,
      fatorRodovia: 1.5,
      taxaBase: 200,
      horasServico: 3,
      custoHora: 50,
      custoAjudante: 100,
      ajudantes: 1,
      tipoVeiculo: 'tres_quartos',
      tipoCombustivel: 'diesel',
    });
    expect(r).not.toBeNull();
    if (!r) return;

    expect(r.distanciaRodoviariaKm).toBe(150);
    expect(r.combustivel).toBe(90);
    expect(r.pedagio).toBe(30);
    expect(r.manutencao).toBe(75);
    expect(r.arla).toBe(0);
    expect(r.taxaBase).toBe(200);
    expect(r.custoHoras).toBe(150);
    expect(r.custoAjudantes).toBe(100);
    expect(r.custoFixo).toBe(100);
    expect(r.custoTotal).toBe(745);
    expect(r.margem).toBe(74.5);
    expect(r.precoCusto).toBe(820);
    expect(r.precoMinimo).toBe(750);
    expect(r.pisoMercado).toBe(750);
    expect(r.precoSugerido).toBe(820);
  });

  it('não devolve valores irreais para mudanças curtas', () => {
    const r = estimarCusto(16, { tipoVeiculo: 'van' });
    expect(r).not.toBeNull();
    if (!r) return;

    expect(r.distanciaRodoviariaKm).toBe(20);
    expect(r.custoTotal).toBeGreaterThan(250);
    expect(r.precoMinimo).toBe(310);
    expect(r.pisoMercado).toBe(350);
    expect(r.precoSugerido).toBe(370);
    expect(r.precoSugerido).toBeGreaterThan(300);
  });

  it('aplica o piso de mercado do veículo mesmo quando o custo é menor', () => {
    const r = estimarCusto(16, { tipoVeiculo: 'bau' });
    expect(r).not.toBeNull();
    if (!r) return;

    expect(r.taxaBase).toBe(TAXA_BASE_POR_TIPO.bau);
    expect(r.precoCusto).toBe(630);
    expect(r.pisoMercado).toBe(650);
    expect(r.precoSugerido).toBe(650);
    expect(r.ajusteMercado).toBe(20);
    expect(r.abaixoDoMercado).toBe(true);
  });

  it('soma o custo de cada ajudante', () => {
    const semAjudante = estimarCusto(100, { ajudantes: 0 });
    const comDois = estimarCusto(100, { ajudantes: 2 });
    expect(semAjudante).not.toBeNull();
    expect(comDois).not.toBeNull();
    if (!semAjudante || !comDois) return;

    expect(comDois.custoAjudantes).toBe(240);
    expect(comDois.custoTotal - semAjudante.custoTotal).toBeCloseTo(240, 2);
  });

  it('mantém a margem zero, mas respeita o piso de mercado', () => {
    const r = estimarCusto(100, { margemPercentual: 0 });
    expect(r).not.toBeNull();
    if (!r) return;
    expect(r.margem).toBe(0);
    expect(r.precoMinimo).toBe(470);
    expect(r.precoSugerido).toBe(500);
    expect(r.ajusteMercado).toBe(30);
  });

  it('normaliza parâmetros inválidos para os padrões', () => {
    const p = normalizarParametros({
      consumoKmL: 0,
      precoCombustivel: -1,
      pedagioPorKm: -3,
      manutencaoPorKm: Number.NaN,
      arlaPorKm: -1,
      margemPercentual: 500,
      fatorRodovia: 0.5,
      taxaBase: -10,
      horasServico: -1,
      custoHora: Number.NaN,
      custoAjudante: -5,
      ajudantes: -2,
      tipoVeiculo: 'inexistente' as never,
    });

    expect(p.consumoKmL).toBe(CUSTO_PADRAO.consumoKmL);
    expect(p.precoCombustivel).toBe(CUSTO_PADRAO.precoCombustivel);
    expect(p.pedagioPorKm).toBe(CUSTO_PADRAO.pedagioPorKm);
    expect(p.manutencaoPorKm).toBe(CUSTO_PADRAO.manutencaoPorKm);
    expect(p.arlaPorKm).toBe(CUSTO_PADRAO.arlaPorKm);
    expect(p.margemPercentual).toBe(100);
    expect(p.fatorRodovia).toBe(CUSTO_PADRAO.fatorRodovia);
    expect(p.taxaBase).toBe(TAXA_BASE_POR_TIPO.van);
    expect(p.horasServico).toBe(CUSTO_PADRAO.horasServico);
    expect(p.custoHora).toBe(CUSTO_PADRAO.custoHora);
    expect(p.custoAjudante).toBe(CUSTO_PADRAO.custoAjudante);
    expect(p.ajudantes).toBe(0);
    expect(p.tipoVeiculo).toBe('van');
  });

  it('usa gasolina no fiorino e ignora o ARLA 32', () => {
    const p = parametrosParaVeiculo('fiorino');
    expect(p.tipoCombustivel).toBe('gasolina');
    expect(p.precoCombustivel).toBe(PRECO_COMBUSTIVEL_REFERENCIA.gasolina);
    expect(p.arlaPorKm).toBe(0);

    const r = estimarCusto(16, { tipoVeiculo: 'fiorino' });
    expect(r).not.toBeNull();
    if (!r) return;
    expect(r.tipoCombustivel).toBe('gasolina');
    expect(r.arla).toBe(0);
    expect(r.combustivel).toBe(15.23);
    expect(r.precoSugerido).toBeGreaterThan(250);
  });

  it('zera o ARLA mesmo quando o parâmetro é enviado na gasolina', () => {
    const p = normalizarParametros({
      tipoCombustivel: 'gasolina',
      precoCombustivel: 6.09,
      arlaPorKm: 0.5,
    });
    expect(p.arlaPorKm).toBe(0);
  });

  it('monta parâmetros iniciais com a taxa de saída do veículo', () => {
    const p = parametrosParaVeiculo('bau', { ajudantes: 2 });
    expect(p.tipoVeiculo).toBe('bau');
    expect(p.taxaBase).toBe(TAXA_BASE_POR_TIPO.bau);
    expect(p.ajudantes).toBe(2);
  });

  it('calcula a faixa de mercado para veículos e distâncias diferentes', () => {
    const local = referenciaMercadoPara('van', 20);
    expect(local.minimo).toBe(350);
    expect(local.maximo).toBe(650);

    const longa = referenciaMercadoPara('van', 300);
    expect(longa.minimo).toBe(1200);
    expect(longa.maximo).toBe(1950);

    const bau = referenciaMercadoPara('bau', 20);
    expect(bau.minimo).toBe(650);
    expect(bau.maximo).toBe(1200);

    const desconhecido = referenciaMercadoPara('xpto', 20);
    expect(desconhecido.tipo).toBe('van');
  });
});
