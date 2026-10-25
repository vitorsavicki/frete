// Rate limit em memória (por isolado do Workers) para os endpoints sensíveis
// de autenticação. É defesa em profundidade best-effort: cada isolado tem seu
// contador, então em escala o teto real é maior. A camada principal de
// proteção em produção é a regra de Rate Limiting do dashboard Cloudflare
// (grátis, na borda) — documentada no SECURITY.md.
import { createMiddleware } from 'hono/factory';

interface Entrada {
  contador: number;
  reiniciaEm: number;
}

const tentativas = new Map<string, Entrada>();
const LIMITE = 10; // tentativas
const JANELA_MS = 10 * 60 * 1000; // por 10 minutos

export const limiteAuth = createMiddleware(async (c, next) => {
  if (c.req.method !== 'POST') return next();

  const ip = c.req.header('CF-Connecting-IP') ?? 'desconhecido';
  const chave = `${ip}:${c.req.path}`;
  const agora = Date.now();
  const entrada = tentativas.get(chave);

  if (entrada && agora < entrada.reiniciaEm) {
    if (entrada.contador >= LIMITE) {
      return c.text('Muitas tentativas. Espere alguns minutos e tente de novo.', 429);
    }
    entrada.contador++;
  } else {
    tentativas.set(chave, { contador: 1, reiniciaEm: agora + JANELA_MS });
  }

  // Faxina para o Map não crescer para sempre
  if (tentativas.size > 5000) {
    for (const [k, v] of tentativas) {
      if (v.reiniciaEm < agora) tentativas.delete(k);
    }
  }

  return next();
});
