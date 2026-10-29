// Regras de negócio dos orçamentos — funções puras (fáceis de testar).

export interface ContextoLance {
  papel: string;
  /** Situação atual do frete: aberto, em_escolha, sem_lances... */
  situacaoFrete: string;
  valor: number;
  menorLanceDosOutros: number | null; // menor orçamento de OUTROS transportadores
  precoMaximo: number | null;
  donoDoFrete: boolean;
  /** Defesa extra: mesmo que o banco ainda não tenha virado, o relógio manda. */
  encerraEm?: string | null;
  agora?: Date | string | number;
}

export type ResultadoRegra = { ok: true } | { ok: false; erro: string };

function prazoVenceu(encerraEm: string | null | undefined, agora: Date | string | number): boolean {
  if (!encerraEm) return false;
  const fim = new Date(encerraEm).getTime();
  const momento = agora instanceof Date ? agora.getTime() : new Date(agora).getTime();
  return Number.isFinite(fim) && Number.isFinite(momento) && fim <= momento;
}

export function validarLance(ctx: ContextoLance): ResultadoRegra {
  if (ctx.donoDoFrete) {
    return { ok: false, erro: 'Você não pode enviar orçamento para a sua própria mudança.' };
  }
  if (ctx.papel !== 'transportador') {
    return { ok: false, erro: 'Apenas transportadores podem enviar orçamentos.' };
  }
  if (ctx.situacaoFrete === 'em_escolha') {
    return {
      ok: false,
      erro: 'O prazo de propostas terminou. O cliente está escolhendo o transportador.',
    };
  }
  if (ctx.situacaoFrete !== 'aberto') {
    return { ok: false, erro: 'Esta mudança não está mais recebendo orçamentos.' };
  }
  if (prazoVenceu(ctx.encerraEm, ctx.agora ?? Date.now())) {
    return { ok: false, erro: 'O prazo para enviar orçamentos terminou.' };
  }
  if (!(ctx.valor > 0)) {
    return { ok: false, erro: 'O orçamento precisa ser maior que zero.' };
  }
  if (ctx.precoMaximo != null && ctx.valor > ctx.precoMaximo) {
    return { ok: false, erro: 'Acima do preço máximo definido pelo cliente.' };
  }
  if (ctx.menorLanceDosOutros != null && ctx.valor >= ctx.menorLanceDosOutros) {
    return { ok: false, erro: 'Seu orçamento precisa ser menor que o menor orçamento atual.' };
  }
  return { ok: true };
}

/**
 * O cliente escolhe quando quiser: assim que chega o primeiro orçamento
 * (`aberto`) e também na janela extra de 48 h depois que as propostas
 * terminam (`em_escolha`). Vencida a janela, o frete fica `expirado` e o
 * aceite é bloqueado.
 */
export function podeAceitarLance(
  donoDoFrete: boolean,
  situacaoFrete: string,
  prazoEscolhaEm: string | null = null,
  agora: Date | string | number = Date.now(),
): boolean {
  if (!donoDoFrete) return false;
  if (situacaoFrete !== 'aberto' && situacaoFrete !== 'em_escolha') return false;
  if (!prazoEscolhaEm) return true;
  return !prazoVenceu(prazoEscolhaEm, agora);
}

/** O dono pode desistir enquanto o frete ainda está em andamento. */
export function podeCancelarFrete(donoDoFrete: boolean, situacaoFrete: string): boolean {
  return donoDoFrete && (situacaoFrete === 'aberto' || situacaoFrete === 'em_escolha');
}
