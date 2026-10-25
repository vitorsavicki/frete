import { defineWorkersConfig } from '@cloudflare/vitest-pool-workers/config';

export default defineWorkersConfig({
  test: {
    poolOptions: {
      workers: {
        wrangler: { configPath: './wrangler.json' },
        miniflare: {
          // D1 local isolado para testes (nunca toca no banco real)
          d1Databases: { DB: 'frete-teste' },
        },
      },
    },
  },
});
