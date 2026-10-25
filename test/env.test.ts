import { env } from 'cloudflare:test';
import { describe, it, expect } from 'vitest';

// Garante que o runtime de testes é o Workers (plugin @cloudflare/vitest-plugin)
// e que o binding D1 isolado está disponível.
describe('ambiente de teste Workers', () => {
  it('expõe o binding D1 DB', () => {
    expect(env.DB).toBeDefined();
  });

  it('executa SQL no D1 isolado', async () => {
    const linha = await env.DB.prepare('SELECT 1 AS n').first<{ n: number }>();
    expect(linha?.n).toBe(1);
  });
});
