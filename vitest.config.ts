import { cloudflareTest } from '@cloudflare/vitest-plugin';
import { defineConfig } from 'vitest/config';

export default defineConfig({
  plugins: [
    cloudflareTest({
      wrangler: { configPath: './wrangler.json' },
      miniflare: {
        // D1 local isolado para testes (nunca toca no banco real)
        d1Databases: { DB: 'frete-teste' },
      },
    }),
  ],
});
