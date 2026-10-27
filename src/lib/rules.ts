// Regras de negócio dos orçamentos — funções puras (fáceis de testar).

export interface ContextoLance {
  papel: string;
  statusFrete: string;
  valor: number;
  menorLanceDosOutros: number | null; // menor orçamento de OUTROS caminhoneiros
  precoMaximo: number | null;
  donoDoFrete: boolean;
}

export type ResultadoRegra = { ok: true } | { ok: false; erro: string };

export function validarLance(ctx: ContextoLance): ResultadoRegra {
  if (ctx.donoDoFrete) {
    return { ok: false, erro: 'Você não pode enviar orçamento para a sua própria mudança.' };
  }
  if (ctx.papel !== 'transportador') {
    return { ok: false, erro: 'Apenas caminhoneiros podem enviar orçamentos.' };
  }
  if (ctx.statusFrete !== 'aberto') {
    return { ok: false, erro: 'Esta mudança não está mais recebendo orçamentos.' };
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

export function podeAceitarLance(donoDoFrete: boolean, statusFrete: string): boolean {
  return donoDoFrete && statusFrete === 'aberto';
}

export function podeCancelarFrete(donoDoFrete: boolean, statusFrete: string): boolean {
  return donoDoFrete && statusFrete === 'aberto';
}
