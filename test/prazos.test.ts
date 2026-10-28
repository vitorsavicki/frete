import { describe, it, expect } from 'vitest';
import {
  PRAZO_ESCOLHA_HORAS,
  aplicarPrazos,
  calcularPrazos,
  detalhePublico,
  prazoRecebimentoValido,
  rotuloSituacao,
  situacaoPublica,
} from '../src/lib/prazos';

const BASE = new Date('2026-10-27T12:00:00.000Z');

/** D1 falso: guarda o SQL executado e devolve a contagem combinada. */
function bancoFalso(contagens: number[]) {
  const sqls: string[] = [];
  let indice = 0;
  const db = {
    prepare(sql: string) {
      sqls.push(sql);
      return {
        bind() {
          return {
            run: async () => {
              const changes = contagens[indice] ?? 0;
              indice += 1;
              return { meta: { changes } };
            },
          };
        },
      };
    },
  };
  return { db: db as unknown as D1Database, sqls };
}

describe('Prazos do leilão', () => {
  it('aceita apenas 24, 48 ou 72 horas de recebimento', () => {
    expect(prazoRecebimentoValido(24)).toBe(true);
    expect(prazoRecebimentoValido(48)).toBe(true);
    expect(prazoRecebimentoValido(72)).toBe(true);
    expect(prazoRecebimentoValido(12)).toBe(false);
    expect(prazoRecebimentoValido(0)).toBe(false);
  });

  it('calcula o fim das propostas e a janela de escolha', () => {
    const prazos = calcularPrazos(BASE, 24);
    expect(prazos.encerraEm).toBe('2026-10-28T12:00:00.000Z');
    expect(prazos.prazoEscolhaEm).toBe('2026-10-30T12:00:00.000Z');
    expect(PRAZO_ESCOLHA_HORAS).toBe(48);
  });

  it('usa 48 horas quando o prazo recebido não é válido', () => {
    const prazos = calcularPrazos(BASE, 7);
    expect(prazos.encerraEm).toBe('2026-10-29T12:00:00.000Z');
    expect(prazos.prazoEscolhaEm).toBe('2026-10-31T12:00:00.000Z');
  });

  it('escreve o status sem depender de cor', () => {
    expect(rotuloSituacao('aberto')).toBe('Recebendo orçamentos');
    expect(rotuloSituacao('em_escolha')).toBe('Escolhendo proposta');
    expect(rotuloSituacao('sem_lances')).toBe('Sem propostas');
    expect(rotuloSituacao('expirado')).toBe('Prazo de escolha venceu');
    expect(rotuloSituacao('encerrado')).toBe('Encerrada');
    expect(rotuloSituacao('cancelado')).toBe('Cancelada');
  });

  it('só lista mudanças abertas e mantém o detalhe de quem está em andamento', () => {
    expect(situacaoPublica('aberto')).toBe(true);
    expect(situacaoPublica('em_escolha')).toBe(false);
    expect(situacaoPublica('encerrado')).toBe(false);

    expect(detalhePublico('aberto')).toBe(true);
    expect(detalhePublico('em_escolha')).toBe(true);
    expect(detalhePublico('encerrado')).toBe(true);
    expect(detalhePublico('sem_lances')).toBe(false);
    expect(detalhePublico('expirado')).toBe(false);
    expect(detalhePublico('cancelado')).toBe(false);
  });

  it('fecha os três relógios em ordem e devolve a contagem', async () => {
    const { db, sqls } = bancoFalso([2, 3, 1]);
    const resultado = await aplicarPrazos(db, BASE);

    expect(resultado).toEqual({ semLances: 2, emEscolha: 3, expirados: 1 });
    expect(sqls).toHaveLength(3);
    expect(sqls[0]).toContain("situacao = 'sem_lances'");
    expect(sqls[1]).toContain("situacao = 'em_escolha'");
    expect(sqls[2]).toContain("situacao = 'expirado'");
  });
});
