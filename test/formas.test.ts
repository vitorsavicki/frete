import { describe, it, expect } from 'vitest';
import { CATALOGO_CARGA } from '../src/lib/carga';
import { formaDoItem, formaOca } from '../src/lib/formas';

describe('Forma desenhada dos volumes', () => {
  it('dá forma de móvel aos itens conhecidos', () => {
    expect(formaDoItem('sofa_3')).toBe('sofa');
    expect(formaDoItem('geladeira_2p')).toBe('geladeira');
    expect(formaDoItem('guarda_roupa')).toBe('armario');
    expect(formaDoItem('cama_box_casal')).toBe('cama');
    expect(formaDoItem('mesa_jantar')).toBe('mesa');
    expect(formaDoItem('caixa_media')).toBe('caixa');
    expect(formaDoItem('caixas')).toBe('caixa');
    expect(formaDoItem('tv')).toBe('tela');
  });

  it('mantém volume simples para item sem forma cadastrada', () => {
    expect(formaDoItem('item_novo_sem_forma')).toBe('generico');
  });

  it('devolve uma forma válida para todo item do catálogo', () => {
    const formas = ['sofa', 'armario', 'geladeira', 'cama', 'mesa', 'caixa', 'tela', 'generico'];
    for (const item of CATALOGO_CARGA) {
      expect(formas, `${item.nome} sem forma`).toContain(formaDoItem(item.id));
    }
  });

  it('monta sofá, cama e mesa com peças em vez de cubo maciço', () => {
    expect(formaOca('sofa')).toBe(true);
    expect(formaOca('cama')).toBe(true);
    expect(formaOca('mesa')).toBe(true);
    expect(formaOca('geladeira')).toBe(false);
    expect(formaOca('armario')).toBe(false);
    expect(formaOca('caixa')).toBe(false);
  });
});
