// Schemas zod — validação de tudo que entra por formulário/API.

import { z } from 'zod';

const cep = z
  .string()
  .trim()
  .regex(/^\d{5}-?\d{3}$/, 'CEP inválido (use 00000-000)');

const textoOpcional = (max: number) => z.string().trim().max(max).optional().or(z.literal(''));

export const schemaCadastro = z.object({
  nome: z.string().trim().min(2, 'Nome muito curto').max(80),
  sobrenome: z.string().trim().min(2, 'Sobrenome muito curto').max(120),
  email: z.string().trim().email('E-mail inválido').max(255),
  senha: z.string().min(8, 'Senha precisa de pelo menos 8 caracteres').max(72),
  papel: z.enum(['cliente', 'transportador'], { message: 'Escolha: cliente ou transportador' }),
  telefone: textoOpcional(20),
  cpf_cnpj: textoOpcional(20),
});

export const schemaLogin = z.object({
  email: z.string().trim().email('E-mail inválido').max(255),
  senha: z.string().min(1, 'Informe a senha').max(72),
});

export const schemaFrete = z.object({
  category_id: z.coerce.number().int().positive('Escolha uma categoria'),
  descricao: z.string().trim().min(5, 'Descreva o que precisa transportar').max(255),
  comentario: textoOpcional(500),
  ajudantes: z.coerce.number().int().min(0).max(10),
  preco_maximo: z
    .string()
    .trim()
    .optional()
    .transform((v) => (v ? Number(v.replace(/\./g, '').replace(',', '.')) : null))
    .pipe(z.number().positive().max(1_000_000).nullable()),
  data_retirada: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Data inválida'),
  hora_retirada: z.string().regex(/^([01]\d|2[0-3]):[0-5]\d$/, 'Hora inválida').optional().or(z.literal('')),
  origem_cep: cep,
  origem_rua: z.string().trim().min(2).max(255),
  origem_bairro: z.string().trim().min(2).max(255),
  origem_cidade: z.string().trim().min(2).max(255),
  origem_uf: z.string().trim().length(2).toUpperCase(),
  destino_cep: cep,
  destino_rua: z.string().trim().min(2).max(255),
  destino_bairro: z.string().trim().min(2).max(255),
  destino_cidade: z.string().trim().min(2).max(255),
  destino_uf: z.string().trim().length(2).toUpperCase(),
});

export const schemaLance = z.object({
  valor: z
    .string()
    .trim()
    .min(1, 'Informe o valor do lance')
    .transform((v) => Number(v.replace(/\./g, '').replace(',', '.')))
    .pipe(z.number({ message: 'Valor inválido' }).positive('Lance deve ser maior que zero').max(1_000_000)),
  mensagem: textoOpcional(500),
});

// Normaliza erro do zod em mensagem amigável única
export function primeiraMensagemErro(error: z.ZodError): string {
  return error.issues[0]?.message ?? 'Dados inválidos. Confira o formulário.';
}
