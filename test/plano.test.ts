import { describe, it, expect } from 'vitest';
import { jsx } from 'hono/jsx';
import { renderToString } from 'hono/jsx/dom/server';
import { PlanoCarga } from '../src/pages/PlanoCarga';
import { parseCaixasForm, resumoCarga, veiculoPorTipo } from '../src/lib/carga';

function caixas(quantidade: number, tamanho: string) {
  const caixa = parseCaixasForm({ caixas_qtd: String(quantidade), caixas_tamanho: tamanho });
  if (!caixa) throw new Error('Caixas de teste não criadas');
  return caixa;
}

describe('Plano de carga renderizado', () => {
  it('mostra as três abas, o 3D e o volume por categoria', () => {
    const van = veiculoPorTipo('van');
    expect(van).not.toBeNull();
    if (!van) return;

    const html = renderToString(
      jsx(PlanoCarga, { carga: resumoCarga([caixas(6, 'media')]), veiculo: van }),
    );

    expect(html).toContain('data-plano-abas');
    expect(html).toContain('data-plano-aba="3d"');
    expect(html).toContain('data-plano-aba="lado"');
    expect(html).toContain('data-plano-aba="cima"');
    expect(html).toContain('class="plano-iso"');
    expect(html).toContain('plano-iso__face--topo');
    expect(html).toContain('data-plano-categorias');
    expect(html).toContain('Caixas e volumes');
    expect(html).toContain('data-plano-viagens');
    expect(html).toContain('Tudo cabe em 1 viagem');
    expect(html).not.toContain('NaN');
    expect(html).not.toContain('undefined');
  });

  it('lista as viagens quando a carga não cabe de uma vez', () => {
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (!fiorino) return;

    const html = renderToString(
      jsx(PlanoCarga, { carga: resumoCarga([caixas(40, 'grande')]), veiculo: fiorino }),
    );

    expect(html).toMatch(/São necessárias \d+ viagens/);
    expect(html).toContain('Viagem 1');
    expect(html).toContain('plano-viagem__titulo');
    expect(html).toContain('Vão nas próximas viagens');
  });

  it('explica o que não entra e mantém a lista completa', () => {
    const fiorino = veiculoPorTipo('fiorino');
    expect(fiorino).not.toBeNull();
    if (!fiorino) return;

    const html = renderToString(
      jsx(PlanoCarga, {
        carga: resumoCarga([
          {
            id: 'sofa_3',
            nome: 'Sofá 3 lugares',
            categoria: 'Móveis',
            emoji: '🛋️',
            fragil: false,
            empilhavel: false,
            quantidade: 1,
            pesoKg: 95,
            comprimentoCm: 220,
            larguraCm: 95,
            alturaCm: 90,
          },
        ]),
        veiculo: fiorino,
      }),
    );

    expect(html).toContain('Este veículo não leva tudo');
    expect(html).toContain('O que não entra neste veículo');
    expect(html).toContain('não passa nas medidas do veículo');
    expect(html).toContain('Sofá 3 lugares');
  });

  it('mantém os três painéis visíveis sem JavaScript', () => {
    const van = veiculoPorTipo('van');
    expect(van).not.toBeNull();
    if (!van) return;

    const html = renderToString(jsx(PlanoCarga, { carga: resumoCarga([]), veiculo: van }));
    expect(html).toContain('data-plano-painel="3d"');
    expect(html).toContain('data-plano-painel="lado"');
    expect(html).toContain('data-plano-painel="cima"');
    expect(html).not.toContain('plano-abas--js');
    expect(html).not.toContain('plano-abas__painel--ativo');
  });
});
