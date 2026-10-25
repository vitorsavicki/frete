// API interna mínima. O front chama /api/cep/:cep; o CEP de verdade é buscado
// server-side na BrasilAPI — a página nunca fala com API externa (CSP fechada).
import { Hono } from 'hono';
import { consultarCep } from '../lib/geo';

const api = new Hono<{ Bindings: { DB: D1Database } }>();

api.get('/cep/:cep', async (c) => {
  const cep = (c.req.param('cep') ?? '').replace(/\D/g, '');
  if (cep.length !== 8) {
    return c.json({ erro: 'CEP inválido' }, 400);
  }

  const resultado = await consultarCep(cep);
  if (!resultado) {
    return c.json({ erro: 'CEP não encontrado' }, 404);
  }

  // CDN pode cachear por 1 dia: menos chamadas à BrasilAPI (rate limit deles)
  c.header('Cache-Control', 'public, max-age=86400');
  return c.json(resultado);
});

export default api;
