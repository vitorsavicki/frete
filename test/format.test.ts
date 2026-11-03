import { describe, it, expect } from 'vitest';
import { linkTelefone, telefone } from '../src/lib/format';

describe('Telefone do contato depois do aceite', () => {
  it('formata celular e fixo com DDD, com ou sem máscara', () => {
    expect(telefone('41996578905')).toBe('(41) 99657-8905');
    expect(telefone('(41) 99657-8905')).toBe('(41) 99657-8905');
    expect(telefone('4133334444')).toBe('(41) 3333-4444');
  });

  it('mantém o valor original quando não reconhece o formato', () => {
    expect(telefone('+55 41 99657-8905')).toBe('+55 41 99657-8905');
    expect(telefone('')).toBe('');
    expect(telefone(null)).toBe('');
  });

  it('monta um link tel: só quando há dígitos suficientes', () => {
    expect(linkTelefone('(41) 99657-8905')).toBe('tel:41996578905');
    expect(linkTelefone('+55 41 99657-8905')).toBe('tel:+5541996578905');
    expect(linkTelefone('123')).toBeNull();
    expect(linkTelefone(null)).toBeNull();
  });
});
