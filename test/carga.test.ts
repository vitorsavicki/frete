import { describe, it, expect } from 'vitest';
import {
  CATALOGO_CARGA,
  analisarEncaixe,
  dimensoesTexto,
  medidasDoCaminhao,
  parseItensDb,
  parseItensForm,
  resumoCarga,
  rotuloTipoVeiculo,
  serializarItens,
  sugerirVeiculo,
  veiculoPorTipo,
  volumeItemM3,
  volumeVeiculoM3,
  type ItemCarga,
} from '../src/lib/carga';

function item(id: string, quantidade: number): ItemCarga {
  const base = CATALOGO_CARGA.find((candidato) => candidato.id === id);
  if (!base) throw new Error(`Item de teste inexistente: ${id}`);
  return { ...base, quantidade };
}

describe('Montador de carga', () => {
  it('lê as quantidades do formulário na ordem do catálogo', () => {
    const itens = parseItensForm({
      item_caixa_pequena: '3',
      item_geladeira_1p: '1',
      item_sofa_2: '0',
      item_bicicleta: '-2',
      item_tv: 'abc',
    });

    expect(itens.map((i) => i.id)).toEqual(['geladeira_1p', 'caixa_pequena']);
    expect(itens.map((i) => i.quantidade)).toEqual([1, 3]);
  });

  it('limita 20 unidades por item e 200 no total', () => {
    const bruto: Record<string, string> = {};
    for (const base of CATALOGO_CARGA) bruto[`item_${base.id}`] = '20';

    const itens = parseItensForm(bruto);
    const total = itens.reduce((soma, i) => soma + i.quantidade, 0);

    expect(total).toBe(200);
    expect(itens).toHaveLength(10);
    expect(itens.every((i) => i.quantidade === 20)).toBe(true);
  });

  it('ignora JSON inválido e itens sem medidas no banco', () => {
    expect(parseItensDb(null)).toEqual([]);
    expect(parseItensDb('{quebrado')).toEqual([]);
    expect(parseItensDb('{"id":1}')).toEqual([]);

    const valido = serializarItens([item('caixa_media', 2)]);
    const lidos = parseItensDb(valido);
    expect(lidos).toHaveLength(1);
    expect(lidos[0]?.id).toBe('caixa_media');
    expect(lidos[0]?.quantidade).toBe(2);

    const semMedidas = JSON.stringify([
      { id: 'x', nome: 'Sem medidas', quantidade: 1, pesoKg: 0 },
    ]);
    expect(parseItensDb(semMedidas)).toEqual([]);
  });

  it('soma peso, volume e maior item da carga', () => {
    const carga = resumoCarga([item('caixa_grande', 2), item('geladeira_1p', 1)]);

    expect(carga.totalItens).toBe(3);
    expect(carga.pesoKg).toBe(120);
    expect(carga.volumeM3).toBe(1.182);
    expect(carga.alturaMaximaCm).toBe(180);
    expect(carga.comprimentoMaximoCm).toBe(70);
    expect(volumeItemM3(item('caixa_grande', 1))).toBe(0.15);
  });

  it('calcula o volume interno do veículo e das medidas do caminhão', () => {
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (fiorino) {
      expect(volumeVeiculoM3(fiorino)).toBeCloseTo(2.262, 3);
      expect(dimensoesTexto(fiorino)).toBe('145 × 130 × 120 cm');
    }

    const medidas = medidasDoCaminhao({
      comprimento_cm: 300,
      largura_cm: 180,
      altura_cm: 150,
      capacidade_kg: 2500,
    });
    expect(medidas).toEqual({
      comprimentoCm: 300,
      larguraCm: 180,
      alturaCm: 150,
      capacidadeKg: 2500,
    });

    expect(
      medidasDoCaminhao({
        comprimento_cm: 0,
        largura_cm: -1,
        altura_cm: 0,
        capacidade_kg: Number.NaN,
      }),
    ).toEqual({ comprimentoCm: 0, larguraCm: 0, alturaCm: 0, capacidadeKg: 0 });

    expect(rotuloTipoVeiculo('bau')).toBe('Caminhão baú');
    expect(rotuloTipoVeiculo('inexistente')).toBe('Outro');
  });

  it('recomenda o menor veículo padrão que cabe', () => {
    const pequena = resumoCarga([item('caixa_pequena', 5)]);
    const sugestaoPequena = sugerirVeiculo(pequena);
    expect(sugestaoPequena?.veiculo.id).toBe('fiorino');
    expect(sugestaoPequena?.analise.status).toBe('cabe');

    const mudanca = resumoCarga([item('caixa_grande', 2), item('geladeira_1p', 1)]);
    const sugestaoMudanca = sugerirVeiculo(mudanca);
    expect(sugestaoMudanca?.veiculo.id).toBe('tres_quartos');
    expect(sugestaoMudanca?.analise.status).toBe('cabe');
  });

  it('aponta quando a carga não cabe e explica o motivo', () => {
    const carga = resumoCarga([item('geladeira_1p', 1)]);
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (!fiorino) return;

    const analise = analisarEncaixe(carga, fiorino);
    expect(analise.status).toBe('nao_cabe');
    expect(analise.cabe).toBe(false);
    expect(analise.motivos.some((motivo) => motivo.includes('altura'))).toBe(true);
    expect(analise.ocupacaoVolume).toBeGreaterThan(0);
  });

  it('marca como limite quando a ocupação passa de 85%', () => {
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (!fiorino) return;

    const carga = resumoCarga([item('caixa_media', 20), item('caixa_pequena', 9)]);
    const analise = analisarEncaixe(carga, fiorino);

    expect(analise.status).toBe('limite');
    expect(analise.cabe).toBe(true);
    expect(analise.ocupacaoVolume).toBeGreaterThan(0.85);
    expect(analise.ocupacaoVolume).toBeLessThanOrEqual(1);
    expect(analise.avisos.length).toBeGreaterThan(0);
    expect(sugerirVeiculo(carga)?.veiculo.id).toBe('van');
  });

  it('retorna não cabe quando o peso passa da capacidade', () => {
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (!fiorino) return;

    const carga = resumoCarga([item('caixa_pequena', 20), item('caixa_media', 10)]);
    const analise = analisarEncaixe(carga, fiorino);

    expect(carga.pesoKg).toBe(310);
    expect(analise.status).toBe('cabe');

    const pesada = resumoCarga([item('caixa_grande', 20), item('geladeira_1p', 6)]);
    const analisePesada = analisarEncaixe(pesada, fiorino);
    expect(analisePesada.status).toBe('nao_cabe');
    expect(analisePesada.motivos.some((motivo) => motivo.includes('peso'))).toBe(true);
  });

  it('devolve análise vazia quando o veículo não tem medidas', () => {
    const carga = resumoCarga([item('caixa_pequena', 1)]);
    const analise = analisarEncaixe(carga, {
      comprimentoCm: 0,
      larguraCm: 0,
      alturaCm: 0,
      capacidadeKg: 0,
    });

    expect(analise.status).toBe('nao_cabe');
    expect(analise.motivos[0]).toContain('medidas internas');
  });

  it('não sugere veículo para carga vazia', () => {
    expect(sugerirVeiculo(resumoCarga([]))).toBeNull();
  });
});
