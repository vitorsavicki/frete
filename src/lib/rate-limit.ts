// Rate limit em memória (por isolado do Workers) para endpoints sensíveis.
// É defesa em profundidade best-effort: cada isolado tem seu contador, então
// em escala o teto real é maior. A camada principal de proteção em produção é
// a regra de Rate Limiting do dashboard Cloudflare (grátis, na borda) —
// documentada no SECURITY.md.
import { createMiddleware } from 'hono/factory';

interface Entrada {
  contador: number;
  reiniciaEm: number;
}

const tentativas = new Map<string, Entrada>();

function criarLimite(limite: number, janelaMs: number, somentePost = true) {
  return createMiddleware(async (c, next) => {
    if (somentePost && c.req.method !== 'POST') return next();

    const ip = c.req.header('CF-Connecting-IP') ?? 'desconhecido';
    const chave = `${ip}:${c.req.path}`;
    const agora = Date.now();
    const entrada = tentativas.get(chave);

    if (entrada && agora < entrada.reiniciaEm) {
      if (entrada.contador >= limite) {
        return c.text('Muitas tentativas. Espere alguns minutos e tente de novo.', 429);
      }
      entrada.contador++;
    } else {
      tentativas.set(chave, { contador: 1, reiniciaEm: agora + janelaMs });
    }

    // Faxina para o Map não crescer para sempre
    if (tentativas.size > 5000) {
      for (const [k, v] of tentativas) {
        if (v.reiniciaEm < agora && tentativas.size > 5000) tentativas.delete(k);
      }
    }

    return next();
  });
}

/** Login/cadastro: 10 tentativas por 10 minutos por IP + rota. */
export const limiteAuth = criarLimite(10, 10 * 60 * 1000);

/** APIs de uso auxiliar (IA, consultas): 40 chamadas por minuto. */
export const limiteApi = criarLimite(40, 60 * 1000);
