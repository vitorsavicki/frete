import { describe, it, expect } from 'vitest';
import { validarLance } from '../src/lib/rules';

describe('Regras de negócio dos orçamentos (validarLance)', () => {
  it('deve impedir que o dono envie orçamento na própria mudança', () => {
    const resultado = validarLance({
      papel: 'transportador',
      statusFrete: 'aberto',
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
      statusFrete: 'aberto',
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
      statusFrete: 'aberto',
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
      statusFrete: 'aberto',
      valor: 200,
      menorLanceDosOutros: 150,
      precoMaximo: 500,
      donoDoFrete: false
    });
    expect(resultado.ok).toBe(false);
    if (!resultado.ok) expect(resultado.erro).toBe('Seu orçamento precisa ser menor que o menor orçamento atual.');
  });
});
