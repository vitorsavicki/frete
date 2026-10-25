import { describe, it, expect } from 'vitest';
import { estimarCusto, normalizarParametros, CUSTO_PADRAO } from '../src/lib/cost';

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
    expect(r.combustivel).toBe(98.44);
    expect(r.pedagio).toBe(12.5);
    expect(r.manutencao).toBe(43.75);
    expect(r.arla).toBe(6.25);
    expect(r.custoTotal).toBe(160.94);
    expect(r.margem).toBe(32.19);
    expect(r.precoMinimo).toBe(170);
    expect(r.precoSugerido).toBe(200);
    expect(r.custoPorKm).toBe(1.29);
  });

  it('respeita os parâmetros informados', () => {
    const r = estimarCusto(100, {
      consumoKmL: 10,
      precoDiesel: 6,
      pedagioPorKm: 0.2,
      manutencaoPorKm: 0.5,
      arlaPorKm: 0,
      custoFixo: 100,
      margemPercentual: 10,
      fatorRodovia: 1.5,
    });
    expect(r).not.toBeNull();
    if (!r) return;

    expect(r.distanciaRodoviariaKm).toBe(150);
    expect(r.combustivel).toBe(90);
    expect(r.pedagio).toBe(30);
    expect(r.manutencao).toBe(75);
    expect(r.arla).toBe(0);
    expect(r.custoFixo).toBe(100);
    expect(r.custoTotal).toBe(295);
    expect(r.margem).toBe(29.5);
    expect(r.precoMinimo).toBe(300);
    expect(r.precoSugerido).toBe(330);
  });

  it('usa margem zero quando solicitado', () => {
    const r = estimarCusto(50, { margemPercentual: 0 });
    expect(r).not.toBeNull();
    if (!r) return;
    expect(r.margem).toBe(0);
    expect(r.precoSugerido).toBe(r.precoMinimo);
  });

  it('normaliza parâmetros inválidos para os padrões', () => {
    const p = normalizarParametros({
      consumoKmL: 0,
      precoDiesel: -1,
      pedagioPorKm: -3,
      manutencaoPorKm: Number.NaN,
      margemPercentual: 500,
      fatorRodovia: 0.5,
    });

    expect(p.consumoKmL).toBe(CUSTO_PADRAO.consumoKmL);
    expect(p.precoDiesel).toBe(CUSTO_PADRAO.precoDiesel);
    expect(p.pedagioPorKm).toBe(CUSTO_PADRAO.pedagioPorKm);
    expect(p.manutencaoPorKm).toBe(CUSTO_PADRAO.manutencaoPorKm);
    expect(p.margemPercentual).toBe(100);
    expect(p.fatorRodovia).toBe(CUSTO_PADRAO.fatorRodovia);
  });
});
