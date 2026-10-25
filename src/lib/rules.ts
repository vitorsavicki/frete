// Regras de negócio do leilão reverso — funções puras (fáceis de testar).

export interface ContextoLance {
  papel: string;
  statusFrete: string;
  valor: number;
  menorLanceDosOutros: number | null; // menor lance de OUTROS transportadores
  precoMaximo: number | null;
  donoDoFrete: boolean;
}

export type ResultadoRegra = { ok: true } | { ok: false; erro: string };

export function validarLance(ctx: ContextoLance): ResultadoRegra {
  if (ctx.donoDoFrete) {
    return { ok: false, erro: 'Você não pode dar lance no próprio frete.' };
  }
  if (ctx.papel !== 'transportador') {
    return { ok: false, erro: 'Apenas transportadores podem dar lances.' };
  }
  if (ctx.statusFrete !== 'aberto') {
    return { ok: false, erro: 'Este frete não está mais aceitando lances.' };
  }
  if (!(ctx.valor > 0)) {
    return { ok: false, erro: 'O lance precisa ser maior que zero.' };
  }
  if (ctx.precoMaximo != null && ctx.valor > ctx.precoMaximo) {
    return { ok: false, erro: 'Acima do preço máximo definido pelo cliente.' };
  }
  if (ctx.menorLanceDosOutros != null && ctx.valor >= ctx.menorLanceDosOutros) {
    return { ok: false, erro: 'Seu lance precisa ser menor que o menor lance atual.' };
  }
  return { ok: true };
}

export function podeAceitarLance(donoDoFrete: boolean, statusFrete: string): boolean {
  return donoDoFrete && statusFrete === 'aberto';
}

export function podeCancelarFrete(donoDoFrete: boolean, statusFrete: string): boolean {
  return donoDoFrete && statusFrete === 'aberto';
}
