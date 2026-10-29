import { describe, it, expect } from 'vitest';
import { validarLance, podeAceitarLance, podeCancelarFrete } from '../src/lib/rules';

describe('Regras de negócio dos orçamentos (validarLance)', () => {
  it('deve impedir que o dono envie orçamento na própria mudança', () => {
    const resultado = validarLance({
      papel: 'transportador',
      situacaoFrete: 'aberto',
      valor: 100,
      menorLanceDosOutros: 200,
      precoMaximo: 500,
      donoDoFrete: true
    });
    expect(resultado.ok).toBe(false);
    if (!resultado.ok) expect(resultado.erro).toBe('Você não pode enviar orçamento para a sua própria mudança.');
  });

  it('deve permitir um orçamento válido', () => {
    const resultado = validarLance({
      papel: 'transportador',
      situacaoFrete: 'aberto',
      valor: 100,
      menorLanceDosOutros: 200,
      precoMaximo: 500,
      donoDoFrete: false
    });
    expect(resultado.ok).toBe(true);
  });

  it('deve impedir um orçamento acima do preço máximo', () => {
    const resultado = validarLance({
      papel: 'transportador',
      situacaoFrete: 'aberto',
      valor: 600,
      menorLanceDosOutros: 200,
      precoMaximo: 500,
      donoDoFrete: false
    });
    expect(resultado.ok).toBe(false);
    if (!resultado.ok) expect(resultado.erro).toBe('Acima do preço máximo definido pelo cliente.');
  });

  it('deve impedir orçamento que não seja menor que o atual', () => {
    const resultado = validarLance({
      papel: 'transportador',
      situacaoFrete: 'aberto',
      valor: 200,
      menorLanceDosOutros: 150,
      precoMaximo: 500,
      donoDoFrete: false
    });
    expect(resultado.ok).toBe(false);
    if (!resultado.ok) expect(resultado.erro).toBe('Seu orçamento precisa ser menor que o menor orçamento atual.');
  });

  it('deve bloquear proposta depois que o recebimento terminou', () => {
    const resultado = validarLance({
      papel: 'transportador',
      situacaoFrete: 'em_escolha',
      valor: 100,
      menorLanceDosOutros: null,
      precoMaximo: null,
      donoDoFrete: false
    });
    expect(resultado.ok).toBe(false);
    if (!resultado.ok) {
      expect(resultado.erro).toContain('prazo de propostas terminou');
    }
  });

  it('deve bloquear proposta quando o relógio venceu mesmo com situação aberta', () => {
    const resultado = validarLance({
      papel: 'transportador',
      situacaoFrete: 'aberto',
      valor: 100,
      menorLanceDosOutros: null,
      precoMaximo: null,
      donoDoFrete: false,
      encerraEm: '2026-10-27T10:00:00.000Z',
      agora: '2026-10-27T10:00:01.000Z'
    });
    expect(resultado.ok).toBe(false);
    if (!resultado.ok) expect(resultado.erro).toBe('O prazo para enviar orçamentos terminou.');
  });

  it('deve bloquear proposta em mudança sem propostas ou cancelada', () => {
    for (const situacaoFrete of ['sem_lances', 'expirado', 'encerrado', 'cancelado']) {
      const resultado = validarLance({
        papel: 'transportador',
        situacaoFrete,
        valor: 100,
        menorLanceDosOutros: null,
        precoMaximo: null,
        donoDoFrete: false
      });
      expect(resultado.ok).toBe(false);
    }
  });
});

describe('Escolha do cliente (podeAceitarLance)', () => {
  it('aceita desde o primeiro orçamento enquanto a mudança está aberta', () => {
    expect(podeAceitarLance(true, 'aberto')).toBe(true);
    expect(podeAceitarLance(true, 'em_escolha')).toBe(true);
    expect(podeAceitarLance(true, 'encerrado')).toBe(false);
    expect(podeAceitarLance(false, 'aberto')).toBe(false);
    expect(podeAceitarLance(false, 'em_escolha')).toBe(false);
  });

  it('bloqueia o aceite depois das 48 horas de escolha', () => {
    expect(
      podeAceitarLance(true, 'em_escolha', '2026-10-29T12:00:00.000Z', '2026-10-29T11:59:00.000Z'),
    ).toBe(true);
    expect(
      podeAceitarLance(true, 'em_escolha', '2026-10-29T12:00:00.000Z', '2026-10-29T12:00:01.000Z'),
    ).toBe(false);
  });

  it('bloqueia quem tenta escolher depois da janela mesmo em aberto', () => {
    expect(
      podeAceitarLance(true, 'aberto', '2026-10-29T12:00:00.000Z', '2026-10-29T12:00:01.000Z'),
    ).toBe(false);
  });
});

describe('Cancelamento (podeCancelarFrete)', () => {
  it('libera o dono enquanto a mudança está em andamento', () => {
    expect(podeCancelarFrete(true, 'aberto')).toBe(true);
    expect(podeCancelarFrete(true, 'em_escolha')).toBe(true);
    expect(podeCancelarFrete(true, 'encerrado')).toBe(false);
    expect(podeCancelarFrete(true, 'expirado')).toBe(false);
    expect(podeCancelarFrete(false, 'aberto')).toBe(false);
  });
});
