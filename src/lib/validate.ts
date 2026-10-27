// Schemas zod — validação de tudo que entra por formulário/API.

import { z } from 'zod';

const cep = z
  .string()
  .trim()
  .regex(/^\d{5}-?\d{3}$/, 'CEP inválido (use 00000-000)');

const textoOpcional = (max: number) => z.string().trim().max(max).optional().or(z.literal(''));

/** Aceita "1,45" e "1.45" sem confundir separador decimal com milhar. */
function paraNumero(valor: string): number {
  const texto = valor.trim();
  if (!texto) return Number.NaN;
  if (texto.includes(',')) return Number(texto.replace(/\./g, '').replace(',', '.'));
  return Number(texto);
}

const numeroCampo = (mensagem: string, minimo: number, maximo: number) =>
  z
    .string()
    .trim()
    .min(1, mensagem)
    .transform(paraNumero)
    .pipe(z.number({ message: mensagem }).min(minimo, mensagem).max(maximo, mensagem));

export const schemaCadastro = z
  .object({
    nome: z.string().trim().min(2, 'Nome muito curto').max(80),
    sobrenome: z.string().trim().min(2, 'Sobrenome muito curto').max(120),
    email: z.string().trim().email('E-mail inválido').max(255),
    senha: z.string().min(8, 'Senha precisa de pelo menos 8 caracteres').max(72),
    senha2: z.string().min(1, 'Confirme a senha'),
    papel: z.enum(['cliente', 'transportador'], { message: 'Escolha: cliente ou transportador' }),
    telefone: textoOpcional(20),
    cpf_cnpj: textoOpcional(20),
  })
  .refine((dados) => dados.senha === dados.senha2, {
    message: 'As senhas não conferem',
    path: ['senha2'],
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
    .min(1, 'Informe o valor do orçamento')
    .transform((v) => Number(v.replace(/\./g, '').replace(',', '.')))
    .pipe(z.number({ message: 'Valor inválido' }).positive('O orçamento deve ser maior que zero').max(1_000_000)),
  mensagem: textoOpcional(500),
});

export const schemaIa = z.object({
  texto: z.string().trim().min(5, 'Escreva um pouco mais antes de usar a IA').max(500),
  tipo: z.enum(['descricao', 'mensagem']).default('descricao'),
});

export const schemaIaCarga = z.object({
  freight_id: z.coerce.number().int().positive('Frete inválido'),
});

export const schemaCaminhao = z.object({
  nome: z.string().trim().min(2, 'Dê um nome ao caminhão').max(80),
  tipo: z.enum(['fiorino', 'van', 'tres_quartos', 'bau', 'carreta', 'outro'], {
    message: 'Escolha o tipo de caminhão',
  }),
  tipo_combustivel: z.enum(['diesel', 'gasolina'], {
    message: 'Escolha diesel ou gasolina',
  }),
  comprimento: numeroCampo('Comprimento inválido (use metros)', 1, 20),
  largura: numeroCampo('Largura inválida (use metros)', 1, 4),
  altura: numeroCampo('Altura inválida (use metros)', 1, 5),
  capacidade_kg: numeroCampo('Capacidade inválida (use kg)', 100, 40_000),
  consumo_km_l: numeroCampo('Consumo inválido (km/L)', 1, 30),
  preco_combustivel: numeroCampo('Preço do combustível inválido', 1, 20),
  pedagio_por_km: numeroCampo('Pedágio por km inválido', 0, 50),
  manutencao_por_km: numeroCampo('Manutenção por km inválida', 0, 50),
  arla_por_km: numeroCampo('ARLA por km inválido', 0, 50),
  taxa_base: numeroCampo('Taxa de saída inválida', 0, 100_000),
  horas_servico: numeroCampo('Horas de serviço inválidas', 0, 48),
  custo_hora: numeroCampo('Custo da hora inválido', 0, 10_000),
  custo_ajudante: numeroCampo('Custo do ajudante inválido', 0, 10_000),
  custo_fixo: numeroCampo('Custo fixo inválido', 0, 100_000),
  margem_percentual: numeroCampo('Margem inválida (0 a 100%)', 0, 100),
});

// Normaliza erro do zod em mensagem amigável única
export function primeiraMensagemErro(error: z.ZodError): string {
  return error.issues[0]?.message ?? 'Dados inválidos. Confira o formulário.';
}
