import { describe, it, expect } from 'vitest';
import { validarCpf, validarCnpj, validarDocumento, formatarDocumento } from '../src/lib/documento';

describe('Validação de CPF/CNPJ', () => {
  it('aceita CPF válido com e sem máscara', () => {
    expect(validarCpf('529.982.247-25')).toBe(true);
    expect(validarCpf('11144477735')).toBe(true);
  });

  it('rejeita CPF inválido', () => {
    expect(validarCpf('111.111.111-11')).toBe(false);
    expect(validarCpf('529.982.247-24')).toBe(false);
    expect(validarCpf('123')).toBe(false);
  });

  it('aceita CNPJ válido com e sem máscara', () => {
    expect(validarCnpj('11.222.333/0001-81')).toBe(true);
    expect(validarCnpj('11222333000181')).toBe(true);
  });

  it('rejeita CNPJ inválido', () => {
    expect(validarCnpj('11.222.333/0001-80')).toBe(false);
    expect(validarCnpj('00000000000000')).toBe(false);
    expect(validarCnpj('123')).toBe(false);
  });

  it('identifica o tipo de documento', () => {
    expect(validarDocumento('52998224725').tipo).toBe('cpf');
    expect(validarDocumento('11222333000181').tipo).toBe('cnpj');
    expect(validarDocumento('123').tipo).toBeNull();
  });

  it('formata CPF e CNPJ', () => {
    expect(formatarDocumento('52998224725')).toBe('529.982.247-25');
    expect(formatarDocumento('11222333000181')).toBe('11.222.333/0001-81');
    expect(formatarDocumento('123')).toBe('123');
  });
});
