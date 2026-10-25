// Tipos das tabelas (espelham migrations/0001_init.sql) + helpers de banco.

export type Papel = 'cliente' | 'transportador' | 'admin';
export type StatusFrete = 'aberto' | 'encerrado' | 'cancelado';

export interface User {
  id: number;
  email: string;
  password_hash: string;
  nome: string;
  sobrenome: string;
  telefone: string | null;
  cpf_cnpj: string | null;
  papel: Papel;
  ativo: number;
  criado_em: string;
}

export interface Category {
  id: number;
  nome: string;
}

export interface Freight {
  id: number;
  user_id: number;
  category_id: number;
  descricao: string;
  comentario: string | null;
  ajudantes: number;
  preco_maximo: number | null;
  status: StatusFrete;
  motivo_cancelamento: string | null;
  data_retirada: string;
  hora_retirada: string | null;
  origem_cep: string;
  origem_rua: string;
  origem_bairro: string;
  origem_cidade: string;
  origem_uf: string;
  destino_cep: string;
  destino_rua: string;
  destino_bairro: string;
  destino_cidade: string;
  destino_uf: string;
  origem_lat: number | null;
  origem_lng: number | null;
  destino_lat: number | null;
  destino_lng: number | null;
  criado_em: string;
}

// Frete enriquecido para listagens (JOINs + agregações)
export interface FreightResumo extends Freight {
  categoria_nome: string;
  cliente_nome: string;
  total_lances: number;
  menor_lance: number | null;
  distancia_km?: number | null;
}

export interface Bid {
  id: number;
  freight_id: number;
  transportador_id: number;
  valor: number;
  mensagem: string | null;
  vencedor: number;
  criado_em: string;
}

export interface BidComNome extends Bid {
  transportador_nome: string;
}

export interface Alert {
  id: number;
  user_id: number;
  freight_id: number | null;
  tipo: string;
  mensagem: string;
  lido: number;
  criado_em: string;
}

// Log de auditoria — nunca guarda senhas nem dados sensíveis
export async function audit(
  db: D1Database,
  userId: number | null,
  acao: string,
  entidade?: string,
  entidadeId?: number,
  meta?: Record<string, unknown>,
): Promise<void> {
  try {
    await db
      .prepare('INSERT INTO audit_log (user_id, acao, entidade, entidade_id, meta) VALUES (?, ?, ?, ?, ?)')
      .bind(userId, acao, entidade ?? null, entidadeId ?? null, meta ? JSON.stringify(meta) : null)
      .run();
  } catch (e) {
    // Auditoria nunca pode quebrar o fluxo principal
    console.error('audit falhou', e);
  }
}

export async function criarAlerta(
  db: D1Database,
  userId: number,
  freightId: number | null,
  tipo: string,
  mensagem: string,
): Promise<void> {
  await db
    .prepare('INSERT INTO alerts (user_id, freight_id, tipo, mensagem) VALUES (?, ?, ?, ?)')
    .bind(userId, freightId, tipo, mensagem)
    .run();
}
