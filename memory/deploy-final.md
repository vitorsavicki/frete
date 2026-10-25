# Deploy final — 2026-10-25

> Atualização: o estado mais recente (redesign, carga com IA e APK Android)
> está em `memory/sessao-carga-apk-2026-10-25.md`. Este arquivo registra o
> deploy anterior da v2 e pode estar defasado.

## Onde o trabalho anterior parou
A sessão anterior (Letta Code) terminou com o código pronto e `tsc` sem erros, mas sem o teste local completo e sem deploy atualizado. O Worker em produção ainda era o MVP antigo ("MVP Quero Mudar Já v2 operando.").

## O que foi feito nesta sessão

### Validação local
- `npm run typecheck` — 0 erros.
- `npm test` — 4 testes passando (`test/rules.test.ts`).
- Migrações D1 locais já aplicadas (tabelas `users`, `sessions`, `categories`, `freight`, `bids`, `alerts`, `audit_log`).
- Smoke test E2E via `wrangler dev` + curl:
  - cadastro de cliente e transportador;
  - login/sessão por cookie;
  - publicação de frete;
  - lance de transportador;
  - aceite do lance pelo cliente (frete vira `encerrado`, vencedor marcado, alertas gerados);
  - `/api/cep/01310100` retornando endereço + coordenadas da BrasilAPI.

### Correção de configuração
`wrangler.json` ainda tinha bindings não usados pelo código e inválidos para a conta:
- `RATE_LIMIT_KV` com id placeholder `@cloudflare/kv` (nenhum namespace KV existe na conta);
- `FRETE_IMAGES` (R2 não está habilitado na conta).

Ambos foram removidos. O rate limit atual é em memória (`src/lib/rate-limit.ts`) e não usa KV. Os bindings podem voltar quando KV/R2 forem realmente criados e usados.

### Deploy
- Comando: `npx wrangler deploy`
- URL: https://frete.vitorsavicki.workers.dev
- Version ID: `bc995d8d-4a24-4397-a0a5-864701ee7472`
- Verificação em produção: `/`, `/login`, `/cadastro`, `/fretes` e `/api/cep/01310100` retornam 200; CSP/HSTS aplicados corretamente.

## Banco remoto (D1)
- Migração `0001_init.sql` aplicada.
- Categorias semeadas (6).
- Sem usuários/fretes de produção (base limpa).

## Pendências / observações
- `src/lib/rate-limit.ts` cita um `SECURITY.md` que ainda não existe no repositório.
- R2 (upload de imagens), filas e outros itens do `plano-refatoracao.md` continuam como melhorias futuras.
- Os arquivos novos do projeto ainda estão fora do git (untracked); falta um commit inicial da v2.
