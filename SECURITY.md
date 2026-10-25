# Política de Segurança — Frete v2 (Quero Mudar Já)

Este documento descreve as proteções implementadas no Frete v2, como reportar
vulnerabilidades e o que ainda depende de configuração da conta Cloudflare.

## Versões suportadas

| Versão | Suporte |
| ------ | ------- |
| `2.x` (Cloudflare Workers + Hono + D1) | ✅ Ativa |
| Código em `legacy/` (PHP de 2020) | ❌ Apenas referência histórica |

## Como reportar uma vulnerabilidade

**Não abra uma issue pública.** Use o canal privado do GitHub:

- https://github.com/vitorsavicki/frete/security/advisories/new

Inclua, se possível: descrição do problema, passos para reproduzir, impacto
estimado e versão/commit afetado. O retorno inicial é feito em até 7 dias.

## Proteções implementadas

### Autenticação e sessão

- Senhas com **PBKDF2-SHA256, 100.000 iterações** e salt aleatório de 16 bytes
  por usuário (`src/lib/crypto.ts`). Comparação em tempo constante.
- Sessões **opacas**: o cookie carrega apenas um token aleatório de 256 bits;
  nenhum dado do usuário vai no cliente (`src/lib/session.ts`).
- Cookies `HttpOnly`, `Secure` e `SameSite=Lax`; expiração de 30 dias com
  revogação imediata no logout (registro removido do D1).
- Login retorna mensagem genérica para não revelar se um e-mail existe.

### CSRF e origem

- Mutações (`POST`, `PUT`, `PATCH`, `DELETE`) passam por validação de origem
  (`src/lib/csrf.ts`): `Sec-Fetch-Site` quando disponível, com fallback para o
  header `Origin`.
- O cookie de sessão é `SameSite=Lax`, então não acompanha POST cross-site.

### Headers e conteúdo

- CSP restritiva (sem `unsafe-inline`/`unsafe-eval`) aplicada pelo middleware
  `secureHeaders` e repetida em `public/_headers` para assets estáticos.
- HSTS, `X-Content-Type-Options: nosniff`, `X-Frame-Options`, `Referrer-Policy`
  e `Permissions-Policy`.
- Renderização SSR com **hono/jsx**, que escapa HTML por padrão — as páginas não
  usam `innerHTML` nem `dangerouslySetInnerHTML`.
- Leaflet e demais assets são servidos localmente (`public/vendor/`); a página
  não conversa com APIs externas.

### Banco de dados e entrada

- Todas as queries usam **prepared statements com `bind`** (`src/routes/*`),
  eliminando injeção de SQL por concatenação.
- Validação de todos os formulários com **Zod** (`src/lib/validate.ts`).
- Regras de negócio (lance, aceite, cancelamento) isoladas e testadas em
  `src/lib/rules.ts` + `test/rules.test.ts`.
- `audit_log` registra ações sensíveis (login, cadastro, publicação, lance,
  aceite, cancelamento), sem armazenar senhas ou tokens.

### Rate limiting

- Limite em memória por IP + rota nos endpoints de autenticação
  (`src/lib/rate-limit.ts`): 10 tentativas por 10 minutos.
- Por ser **best-effort** (cada isolate do Workers tem seu próprio contador), a
  proteção principal contra abuso deve ser uma **regra de Rate Limiting na borda
  da Cloudflare** (dashboard → Security → WAF → Rate limiting rules), apontando
  para `/login` e `/cadastro`.

### Operação

- Nenhum segredo é versionado; credenciais ficam no cofre do Wrangler/Cloudflare.
- Bindings de KV e R2 foram removidos do `wrangler.json` por não serem usados
  pelo código atual. Se forem reativados, crie os recursos reais na conta antes
  do deploy e restrinja o acesso por binding mínimo.

## Boas práticas para quem for operar o projeto

1. Ative a regra de rate limiting na borda da Cloudflare para `/login` e
   `/cadastro`.
2. Mantenha `compatibility_date` do Workers atualizada e revise notas de
   segurança do runtime.
3. Rode `npm audit` e `npm test` antes de cada deploy.
4. Monitore `audit_log` e os logs de observabilidade do Worker.
5. Use domínio próprio com HTTPS e mantenha o certificado válido.

## Fora de escopo

- Engenharia social e phishing.
- Ataques de negação de serviço volumétricos (devem ser tratados na borda).
- O código legado em `legacy/`, que não está em produção.
