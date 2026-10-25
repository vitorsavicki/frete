// Validação local de CPF/CNPJ (dígitos verificadores), sem depender de API.
// Assim o cadastro funciona offline e não expõe documentos a terceiros.

export function somenteDigitos(valor: string): string {
  return (valor ?? '').replace(/\D/g, '');
}

export function validarCpf(entrada: string): boolean {
  const cpf = somenteDigitos(entrada);
  if (cpf.length !== 11) return false;
  if (/^(\d)\1{10}$/.test(cpf)) return false;

  const digito = (ate: number): number => {
    let soma = 0;
    for (let i = 0; i < ate; i++) soma += Number(cpf[i]) * (ate + 1 - i);
    const resto = (soma * 10) % 11;
    return resto === 10 ? 0 : resto;
  };

  return digito(9) === Number(cpf[9]) && digito(10) === Number(cpf[10]);
}

export function validarCnpj(entrada: string): boolean {
  const cnpj = somenteDigitos(entrada);
  if (cnpj.length !== 14) return false;
  if (/^(\d)\1{13}$/.test(cnpj)) return false;

  const digito = (ate: number): number => {
    // Pesos do CNPJ: 2,3,4,5,6,7,8,9,2,3,4,5,6...
    let peso = ate - 7;
    let soma = 0;
    for (let i = 0; i < ate; i++) {
      soma += Number(cnpj[i]) * peso;
      peso = peso === 2 ? 9 : peso - 1;
    }
    const resto = soma % 11;
    return resto < 2 ? 0 : 11 - resto;
  };

  return digito(12) === Number(cnpj[12]) && digito(13) === Number(cnpj[13]);
}

export type TipoDocumento = 'cpf' | 'cnpj';

export interface ResultadoDocumento {
  ok: boolean;
  tipo: TipoDocumento | null;
  digitos: string;
}

export function validarDocumento(entrada: string): ResultadoDocumento {
  const digitos = somenteDigitos(entrada);
  if (digitos.length === 11) return { ok: validarCpf(digitos), tipo: 'cpf', digitos };
  if (digitos.length === 14) return { ok: validarCnpj(digitos), tipo: 'cnpj', digitos };
  return { ok: false, tipo: null, digitos };
}

export function formatarDocumento(entrada: string): string {
  const d = somenteDigitos(entrada);
  if (d.length === 11) {
    return `${d.slice(0, 3)}.${d.slice(3, 6)}.${d.slice(6, 9)}-${d.slice(9)}`;
  }
  if (d.length === 14) {
    return `${d.slice(0, 2)}.${d.slice(2, 5)}.${d.slice(5, 8)}/${d.slice(8, 12)}-${d.slice(12)}`;
  }
  return entrada;
}
