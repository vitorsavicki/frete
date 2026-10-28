# Operação e deploy

## Ambiente local

Pré-requisitos: Node.js 20+, npm e uma conta Cloudflare para publicar.

```bash
npm install
npm run db:migrate:local
npm run db:seed:local
npm run dev
```

O Wrangler sobe o Worker em `http://127.0.0.1:8787` (a porta pode variar).
Assets de `public/` são servidos pelo binding `ASSETS`; D1 local fica em
`.wrangler/`.

## Verificações antes de publicar

```bash
npm run typecheck
node --check public/js/app.js
npm test
```

O typecheck cobre o JSX e a biblioteca. O `node --check` pega erro de sintaxe no
JavaScript servido ao navegador. O Vitest cobre regras, custo, carga, montagem,
documento e ambiente.

## Banco D1

```bash
npm run db:migrate:local
npm run db:migrate
npm run db:seed:local
npm run db:seed
```

- `db:migrate` usa o banco remoto configurado em `wrangler.json`.
- `seed.sql` insere apenas categorias com `INSERT OR IGNORE`.
- Para conferir o schema remoto:

```bash
npx wrangler d1 execute frete --remote --command "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name"
```

- Para atualizar referências de custo sem deploy:

```bash
npx wrangler d1 execute frete --remote --command "UPDATE configuracoes SET valor = '{\"diesel\":7.49,\"gasolina\":6.19}' WHERE chave = 'combustivel'"
```

## Segredos e bindings

| Nome | Tipo | Uso |
| --- | --- | --- |
| `DB` | binding D1 | obrigatório |
| `ASSETS` | binding de assets | obrigatório |
| `AI` | binding Workers AI | opcional |
| `EMAIL` | binding Cloudflare Email | opcional |
| `RESEND_API_KEY` | secret | opcional |
| `EMAIL_FROM` | variável/secret | remetente |
| `GOOGLE_CLIENT_ID` | secret | login Google |
| `GOOGLE_CLIENT_SECRET` | secret | login Google |

Publicar segredos:

```bash
npx wrangler secret put RESEND_API_KEY
npx wrangler secret put EMAIL_FROM
npx wrangler secret put GOOGLE_CLIENT_ID
npx wrangler secret put GOOGLE_CLIENT_SECRET
```

Sem os opcionais, o recurso degrada com elegância: a IA some, o e-mail vai para
o log/fila e o botão do Google informa que o login não está configurado.

## E-mail

Ordem de envio:

1. binding `EMAIL` do Cloudflare;
2. Resend;
3. fila `email_outbox` no D1.

O cron do Worker roda a cada 5 minutos e reprocessa até 20 mensagens. Cada
mensagem tem até 8 tentativas, com espera crescente; depois vira `descartado`.

Configuração detalhada: [email.md](email.md).

**Pendência:** o envio real para qualquer destinatário depende de um domínio
próprio verificado no Cloudflare/Resend. O remetente `onboarding@resend.dev`
serve para teste, não para produção.

## Cron

`wrangler.json` declara:

```json
"triggers": { "crons": ["*/5 * * * *"] }
```

O `scheduled` do Worker chama, nesta ordem:

1. `aplicarPrazos` — fecha recebimento (`sem_lances`/`em_escolha`) e expira a
   janela de escolha (`expirado`);
2. `processarFilaEmails` — reprocessa até 20 mensagens.

Se o cron falhar, a fila continua no D1 e os prazos são aplicados na próxima
visita a `/fretes`, `/fretes/:id`, `/painel` ou `/sitemap.xml`.

## Deploy

```bash
npm run typecheck
node --check public/js/app.js
npm test
npm run db:migrate        # somente se houver migração nova
npm run deploy
```

O deploy publica o Worker, os assets e o cron. Depois de publicar:

1. abra a home e confira a cena com imagens 3D e o tour;
2. abra `/fretes`, um frete com carga e o formulário `/novo-frete`;
3. confira as abas 3D/Lado/Cima, o volume por categoria e a lista de viagens;
4. confira o prazo de propostas e os status escritos na listagem e no detalhe;
5. entre como transportador e abra `/caminhao`;
6. teste o simulador e o envio de uma proposta em ambiente de teste;
7. confira `/api/email/status`;
8. confira `/robots.txt` e `/sitemap.xml`;
9. baixe o APK em `/app`.

## APK Android

O APK é gerado pelo build Android e versionado em `public/download/`. O arquivo
`src/lib/apk.ts` é gerado por `android/build.ps1`; não edite à mão.

```powershell
powershell -File android/build.ps1
```

O build precisa de JDK, Android SDK e da keystore configurada em
`android/keystore.properties` (modelo em `keystore.properties.example`).

## Rollback

1. `npx wrangler deployments list`
2. `npx wrangler rollback <id>` para voltar ao deploy anterior.
3. Se a falha for de schema, aplique uma migração corretiva; não edite a
   migração antiga.
4. Confira a fila de e-mail e os logs em `npx wrangler tail`.

## Diagnóstico rápido

| Sintoma | Onde olhar |
| --- | --- |
| Tour não abre | `data-tour-auto` na home, `localStorage` (`qm-tour-v1`), console do navegador |
| Ajuda não abre | `data-ajuda-fixa`, `aria-expanded`, `hidden` do painel |
| Plano não atualiza | `data-montador-carga`, `data-veiculos`, `node --check public/js/app.js` |
| Abas não alternam | `data-plano-abas`, classe `plano-abas--js`, painel `--ativo` |
| Imagem de veículo quebrada | `/img/veiculos/*.png`, CSP `'self'`, créditos em `CREDITOS.md` |
| Mudança não some da lista | `situacao`, `encerra_em`, `aplicarPrazos`, cron `*/5` |
| Custo estranho | perfil do caminhão, `/api/referencias`, `configuracoes` no D1 |
| E-mail não sai | `/api/email/status`, `wrangler tail`, domínio verificado |
| CEP não preenche | `/api/cep/:cep`, rate limit, BrasilAPI/ViaCEP |
| Mapa vazio | coordenadas no frete, `/api/rota`, Nominatim/OSRM |
| Erro 500 | `wrangler tail`; o handler de erro não expõe stack ao usuário |

## Observabilidade

`wrangler.json` liga `observability.enabled`. Use:

```bash
npx wrangler tail
```

Não registre senha, token, cookie ou conteúdo de e-mail de cliente. O
`audit_log` guarda ação e contexto, não segredo.
