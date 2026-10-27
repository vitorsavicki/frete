import { describe, it, expect } from 'vitest';
import { CATALOGO_CARGA, parseCaixasForm, resumoCarga, veiculoPorTipo, type ItemCarga } from '../src/lib/carga';
import {
  dicasCarregamento,
  montarPlanoCarga,
  sugerirVeiculoParaPlano,
} from '../src/lib/montagem';

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

/**
 * Carga parecida com o frete 3 de produção: 11 volumes de móveis, 528 kg e
 * 9,492 m³. O 3/4 tem volume suficiente no papel, mas o packer deixa 6 volumes
 * fora; o baú acomoda tudo.
 */
function mudancaDeMoveis(): ItemCarga[] {
  const ids = [
    'sofa_2',
    'sofa_3',
    'cama_solteiro',
    'cama_casal',
    'guarda_roupa',
    'mesa_jantar',
    'cadeira',
    'mesa_escritorio',
    'colchao_casal',
    'colchao_solteiro',
    'armario',
  ];
  return ids.map((id) => item(id, 1));
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

  it('não recomenda veículo quando não há carga', () => {
    expect(sugerirVeiculoParaPlano(resumoCarga([]))).toBeNull();
  });

  it('recomenda o menor veículo que acomoda todos os volumes', () => {
    const sugestao = sugerirVeiculoParaPlano(resumoCarga([caixas(5)]));
    expect(sugestao?.veiculo.id).toBe('fiorino');
  });

  it('prefere o baú quando o 3/4 tem volume, mas deixa volumes fora', () => {
    const carga = resumoCarga(mudancaDeMoveis());
    expect(carga.totalItens).toBe(11);
    expect(carga.pesoKg).toBe(528);
    expect(carga.volumeM3).toBeCloseTo(9.492, 3);

    const tresQuartos = veiculoPorTipo('tres_quartos');
    const bau = veiculoPorTipo('bau');
    expect(tresQuartos).not.toBeNull();
    expect(bau).not.toBeNull();
    if (!tresQuartos || !bau) return;

    // O volume do 3/4 (11,97 m³) é maior que a carga, mas o encaixe real
    // deixa volumes fora por causa do comprimento e da largura das peças.
    const plano3q = montarPlanoCarga(carga, tresQuartos);
    expect(plano3q.fora).toBe(6);
    expect(plano3q.colocados + plano3q.fora).toBe(plano3q.totalUnidades);

    const planoBau = montarPlanoCarga(carga, bau);
    expect(planoBau.fora).toBe(0);
    expect(planoBau.itens.every((registro) => registro.fora === 0)).toBe(true);

    const sugestao = sugerirVeiculoParaPlano(carga);
    expect(sugestao?.veiculo.id).toBe('bau');
  });
});
