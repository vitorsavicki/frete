import { describe, it, expect } from 'vitest';
import { CATALOGO_CARGA, parseCaixasForm, resumoCarga, veiculoPorTipo, type ItemCarga } from '../src/lib/carga';
import { dicasCarregamento, montarPlanoCarga } from '../src/lib/montagem';

function item(id: string, quantidade: number): ItemCarga {
  const base = CATALOGO_CARGA.find((candidato) => candidato.id === id);
  if (!base) throw new Error(`Item de teste inexistente: ${id}`);
  return { ...base, quantidade };
}

function caixas(quantidade: number, tamanho = 'media'): ItemCarga {
  const caixa = parseCaixasForm({ caixas_qtd: String(quantidade), caixas_tamanho: tamanho });
  if (!caixa) throw new Error('Caixas de teste não criadas');
  return caixa;
}

function caixasPersonalizadas(
  quantidade: number,
  medidas: { comprimento: number; largura: number; altura: number; peso: number },
): ItemCarga {
  const caixa = parseCaixasForm({
    caixas_qtd: String(quantidade),
    caixas_tamanho: 'personalizada',
    caixas_comprimento: String(medidas.comprimento),
    caixas_largura: String(medidas.largura),
    caixas_altura: String(medidas.altura),
    caixas_peso: String(medidas.peso),
  });
  if (!caixa) throw new Error('Caixas personalizadas de teste não criadas');
  return caixa;
}

describe('Plano de carregamento', () => {
  it('devolve um plano vazio quando não há carga', () => {
    const van = veiculoPorTipo('van');
    expect(van).not.toBeNull();
    if (!van) return;

    const plano = montarPlanoCarga(resumoCarga([]), van);
    expect(plano.blocos).toEqual([]);
    expect(plano.faixas).toEqual([]);
    expect(plano.totalUnidades).toBe(0);
    expect(plano.colocados).toBe(0);
    expect(plano.fora).toBe(0);
    expect(plano.volumesAtras).toBe(0);
    expect(plano.larguraUsadaCm).toBe(0);
    expect(plano.ocupacaoVolume).toBe(0);
  });

  it('usa o piso antes de empilhar e respeita a largura do veículo', () => {
    const van = veiculoPorTipo('van');
    expect(van).not.toBeNull();
    if (!van) return;

    const cinco = montarPlanoCarga(resumoCarga([caixas(5)]), van);
    expect(cinco.totalUnidades).toBe(5);
    expect(cinco.colocados).toBe(5);
    expect(cinco.fora).toBe(0);
    expect(cinco.blocos.every((bloco) => !bloco.empilhado)).toBe(true);
    expect(cinco.alturaUsadaCm).toBe(40);
    expect(cinco.volumesAtras).toBe(0);

    const dez = montarPlanoCarga(resumoCarga([caixas(10)]), van);
    expect(dez.colocados).toBe(10);
    expect(dez.fora).toBe(0);
    expect(dez.blocos.every((bloco) => !bloco.empilhado)).toBe(true);
    expect(dez.alturaUsadaCm).toBe(40);
    expect(dez.faixas.length).toBe(2);
    expect(dez.volumesAtras).toBe(4);

    const vinte = montarPlanoCarga(resumoCarga([caixas(20)]), van);
    expect(vinte.colocados).toBe(20);
    expect(vinte.fora).toBe(0);
    expect(vinte.blocos.some((bloco) => bloco.empilhado)).toBe(true);
    expect(vinte.alturaUsadaCm).toBe(80);
    expect(vinte.faixas.length).toBe(3);
    expect(vinte.volumesAtras).toBe(12);
    expect(vinte.alturaUsadaCm).toBeLessThanOrEqual(van.alturaCm);
    expect(vinte.comprimentoUsadoCm).toBeLessThanOrEqual(van.comprimentoCm);
    expect(vinte.larguraUsadaCm).toBeLessThanOrEqual(van.larguraCm);
  });

  it('acomoda 20 caixas grandes em duas faixas no caminhão 3/4', () => {
    const truck = veiculoPorTipo('tres_quartos');
    expect(truck).not.toBeNull();
    if (!truck) return;

    const carga = resumoCarga([
      caixasPersonalizadas(20, { comprimento: 80, largura: 60, altura: 70, peso: 30 }),
    ]);
    const plano = montarPlanoCarga(carga, truck);

    expect(plano.totalUnidades).toBe(20);
    expect(plano.fora).toBe(0);
    expect(plano.colocados).toBe(20);
    expect(plano.faixas.length).toBe(2);
    expect(plano.volumesAtras).toBe(10);
    expect(plano.alturaUsadaCm).toBe(140);
    expect(plano.larguraUsadaCm).toBe(160);
    expect(plano.larguraUsadaCm).toBeLessThanOrEqual(truck.larguraCm);
    expect(plano.comprimentoUsadoCm).toBeLessThanOrEqual(truck.comprimentoCm);
    expect(plano.blocos.filter((bloco) => bloco.faixa === 0).length).toBe(10);
  });

  it('conta como fora o que não cabe no veículo', () => {
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (!fiorino) return;

    const cargas = resumoCarga([caixas(100, 'grande')]);
    const plano = montarPlanoCarga(cargas, fiorino);
    expect(plano.fora).toBeGreaterThan(0);
    expect(plano.colocados + plano.fora).toBe(plano.totalUnidades);
    expect(plano.blocos.length).toBe(plano.colocados);
  });

  it('não empilha frágeis e explica os cuidados', () => {
    const truck = veiculoPorTipo('tres_quartos');
    expect(truck).not.toBeNull();
    if (!truck) return;

    const carga = resumoCarga([item('geladeira_1p', 2), caixas(6)]);
    const plano = montarPlanoCarga(carga, truck);
    expect(plano.temFragil).toBe(true);
    expect(plano.temGeladeira).toBe(true);
    expect(plano.temCaixas).toBe(true);

    const geleiras = plano.blocos.filter((bloco) => /geladeira/i.test(bloco.nome));
    expect(geleiras.length).toBe(2);
    expect(geleiras.every((bloco) => !bloco.empilhado)).toBe(true);

    const dicas = dicasCarregamento(carga, plano);
    expect(dicas.some((dica) => /frágeis/i.test(dica))).toBe(true);
    expect(dicas.some((dica) => /geladeira/i.test(dica))).toBe(true);
    expect(dicas.some((dica) => /caixas/i.test(dica))).toBe(true);
  });

  it('avisa quando a carga fica fora do desenho', () => {
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (!fiorino) return;

    const carga = resumoCarga([item('geladeira_2p', 1)]);
    const plano = montarPlanoCarga(carga, fiorino);
    expect(plano.fora).toBe(1);
    expect(plano.blocos).toEqual([]);
    expect(dicasCarregamento(carga, plano).some((dica) => /fora do desenho/i.test(dica))).toBe(
      true,
    );
  });
});
