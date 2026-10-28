// Prazos do leilão reverso.
//
// O relógio tem dois momentos:
//   1. recebimento de propostas (`encerra_em`);
//   2. escolha do cliente (`prazo_escolha_em`), 48 h depois do fim das
//      propostas.
//
// Sem nenhuma proposta, a mudança termina como `sem_lances`. Se o cliente
// não escolher dentro da janela, termina como `expirado`. Os nomes são
// guardados em `freight.situacao`; `status` continua sendo o campo legado.

export const PRAZOS_RECEBIMENTO = [24, 48, 72] as const;
export type PrazoRecebimento = (typeof PRAZOS_RECEBIMENTO)[number];
export const PRAZO_ESCOLHA_HORAS = 48;

const HORA_MS = 60 * 60 * 1000;

export function prazoRecebimentoValido(valor: number): valor is PrazoRecebimento {
  return (PRAZOS_RECEBIMENTO as readonly number[]).includes(valor);
}

/** Calcula o fim das propostas e o fim da escolha a partir de agora. */
export function calcularPrazos(
  base: Date,
  horas: number,
): { encerraEm: string; prazoEscolhaEm: string } {
  const duracao = prazoRecebimentoValido(horas) ? horas : 48;
  const encerra = new Date(base.getTime() + duracao * HORA_MS);
  const escolha = new Date(encerra.getTime() + PRAZO_ESCOLHA_HORAS * HORA_MS);
  return {
    encerraEm: encerra.toISOString(),
    prazoEscolhaEm: escolha.toISOString(),
  };
}

/** Rótulo escrito, sem depender só de cor, para todas as telas. */
export function rotuloSituacao(situacao: string): string {
  if (situacao === 'aberto') return 'Recebendo orçamentos';
  if (situacao === 'em_escolha') return 'Escolhendo proposta';
  if (situacao === 'sem_lances') return 'Sem propostas';
  if (situacao === 'expirado') return 'Prazo de escolha venceu';
  if (situacao === 'encerrado') return 'Encerrada';
  if (situacao === 'cancelado') return 'Cancelada';
  return situacao;
}

export function situacaoPublica(situacao: string): boolean {
  return situacao === 'aberto';
}

/**
 * O detalhe pode ser lido por quem não é dono enquanto a mudança está em
 * andamento ou já foi fechada com um transportador. Mudanças sem propostas,
 * expiradas ou canceladas somem também do detalhe público.
 */
export function detalhePublico(situacao: string): boolean {
  return situacao === 'aberto' || situacao === 'em_escolha' || situacao === 'encerrado';
}

/**
 * Avança os fretes cujo relógio venceu. É chamado nas listagens, no detalhe,
 * no painel, no sitemap e pelo cron. A atualização é idempotente.
 */
export interface ResultadoPrazos {
  semLances: number;
  emEscolha: number;
  expirados: number;
}

export async function aplicarPrazos(
  db: D1Database,
  agora: Date = new Date(),
): Promise<ResultadoPrazos> {
  const agoraIso = agora.toISOString();

  // Primeiro os que nunca receberam proposta. Quem tem proposta continua
  // em aberto e cai na atualização seguinte.
  const semLances = await db
    .prepare(
      `UPDATE freight
       SET situacao = 'sem_lances', status = 'encerrado'
       WHERE situacao = 'aberto'
         AND encerra_em IS NOT NULL
         AND encerra_em <= ?
         AND NOT EXISTS (SELECT 1 FROM bids b WHERE b.freight_id = freight.id)`,
    )
    .bind(agoraIso)
    .run();

  const emEscolha = await db
    .prepare(
      `UPDATE freight
       SET situacao = 'em_escolha',
           prazo_escolha_em = COALESCE(
             prazo_escolha_em,
             strftime('%Y-%m-%dT%H:%M:%fZ', encerra_em, '+48 hours')
           )
       WHERE situacao = 'aberto'
         AND encerra_em IS NOT NULL
         AND encerra_em <= ?
         AND EXISTS (SELECT 1 FROM bids b WHERE b.freight_id = freight.id)`,
    )
    .bind(agoraIso)
    .run();

  const expirados = await db
    .prepare(
      `UPDATE freight
       SET situacao = 'expirado', status = 'encerrado'
       WHERE situacao = 'em_escolha'
         AND prazo_escolha_em IS NOT NULL
         AND prazo_escolha_em <= ?`,
    )
    .bind(agoraIso)
    .run();

  return {
    semLances: semLances.meta.changes ?? 0,
    emEscolha: emEscolha.meta.changes ?? 0,
    expirados: expirados.meta.changes ?? 0,
  };
}
