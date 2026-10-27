# Quero Mudar Já — Frete v2

Site de mudanças e fretes em que o cliente publica o que precisa levar e recebe
propostas de transportadores. O cliente compara, escolhe e combina os detalhes
direto com quem vai transportar. O site não cobra taxa de intermediação e não
processa pagamento.

> **Produção:** <https://frete.vitorsavicki.workers.dev>
>
> O código deste repositório está em desenvolvimento. O deploy de produção pode
> estar atrás do `HEAD`; confira a seção [Estado atual](#estado-atual) antes de
> publicar.

## Índice

- [O que o site faz](#o-que-o-site-faz)
- [Stack](#stack)
- [Começando](#começando)
- [Scripts](#scripts)
- [Estrutura do projeto](#estrutura-do-projeto)
- [Fluxos principais](#fluxos-principais)
- [Documentação](#documentação)
- [Testes e verificações](#testes-e-verificações)
- [Deploy](#deploy)
- [Estado atual](#estado-atual)
- [Segurança](#segurança)
- [Licença](#licença)

## O que o site faz

**Para quem vai mudar (cliente):**

- monta a lista de móveis, eletrodomésticos e caixas com peso e medidas;
- vê o resumo da carga (peso, volume e veículo recomendado);
- confere o plano de carga antes de fechar;
- publica a mudança e recebe propostas de transportadores;
- compara as propostas e escolhe com calma;
- combina retirada, entrega e pagamento direto com o transportador.

**Para quem transporta (transportador):**

- cadastra as medidas internas e a capacidade do veículo;
- vê mudanças abertas filtradas por categoria, cidade, UF e data;
- confere se a carga cabe no veículo antes de enviar a proposta;
- usa uma referência de custo (combustível, pedágio, manutenção, horas,
  ajudantes, margem) para decidir o valor;
- acompanha as propostas no painel e recebe alertas quando é superado ou
  vencedor.

**O que o site não faz:**

- não cobra taxa de intermediação;
- não define o preço final no lugar do transportador;
- não processa pagamento;
- não garante a execução do transporte — o combinado é entre as partes.

## Stack

| Camada | Tecnologia |
| --- | --- |
| Runtime | Cloudflare Workers |
| Framework | Hono + `hono/jsx` (SSR) |
| Banco | Cloudflare D1 (SQLite) |
| IA | Cloudflare Workers AI (opcional) |
| E-mail | Cloudflare Email Sending e/ou Resend (opcional) |
| Mapa | Leaflet + tiles do OpenStreetMap (servidos no cliente) |
| Validação | Zod |
| Testes | Vitest |
| Deploy | Wrangler |
| App | Android nativo empacotado como WebView (APK) |

Não há framework de front-end, bundler de cliente nem dependência de CDN. O
HTML é renderizado no Worker e o JavaScript de `public/js/app.js` é progressivo:
as páginas continuam funcionando sem ele.

## Começando

### Pré-requisitos

- Node.js 20 ou superior
- npm
- Conta Cloudflare (para deploy; o desenvolvimento local usa o runtime do
  Wrangler)

### Instalação

```bash
npm install
npm run db:migrate:local
npm run db:seed:local   # opcional: categorias iniciais
npm run dev
```

O Wrangler sobe o Worker em `http://127.0.0.1:8787` (a porta pode variar; confira
a saída do comando).

### Variáveis e bindings

O `wrangler.json` declara:

| Binding | Uso | Obrigatório |
| --- | --- | --- |
| `DB` | D1 com o schema do site | Sim |
| `ASSETS` | arquivos de `public/` | Sim |
| `AI` | Workers AI para textos e análise de carga | Não |
| `EMAIL` | Cloudflare Email Sending | Não |
| `RESEND_API_KEY` | envio por Resend | Não |
| `EMAIL_FROM` | remetente dos e-mails | Não |
| `GOOGLE_CLIENT_ID` | login com Google | Não |
| `GOOGLE_CLIENT_SECRET` | login com Google | Não |

Sem os opcionais, o recurso correspondente degrada com elegância: a IA some da
interface, o e-mail vai para o log/fila e o botão do Google informa que o login
não está configurado.

Para segredos em produção:

```bash
npx wrangler secret put RESEND_API_KEY
npx wrangler secret put EMAIL_FROM
npx wrangler secret put GOOGLE_CLIENT_ID
npx wrangler secret put GOOGLE_CLIENT_SECRET
```

## Scripts

| Comando | O que faz |
| --- | --- |
| `npm run dev` | sobe o Worker local com Wrangler |
| `npm run deploy` | publica o Worker na Cloudflare |
| `npm run typecheck` | roda o TypeScript sem emitir arquivos |
| `npm test` | roda a suíte Vitest uma vez |
| `npm run test:watch` | Vitest em modo watch |
| `npm run db:migrate:local` | aplica migrações no D1 local |
| `npm run db:migrate` | aplica migrações no D1 remoto |
| `npm run db:seed:local` | insere as categorias no D1 local |
| `npm run db:seed` | insere as categorias no D1 remoto |
| `npm run audit` | `npm audit --audit-level=high` |

Verificação rápida do JavaScript servido:

```bash
node --check public/js/app.js
```

## Estrutura do projeto

```text
.
├── android/              # projeto, build e assinatura do APK
├── docs/                 # documentação funcional, técnica e de operação
├── migrations/           # migrações D1 (0001 a 0006)
├── memory/               # registros de sessões e decisões
├── public/               # CSS, JS, imagens, APK, Leaflet e headers
├── src/
│   ├── index.tsx         # Worker: middlewares, rotas, cron, erros
│   ├── lib/              # regras, carga, custo, banco, e-mail, segurança
│   ├── pages/            # componentes SSR (hono/jsx)
│   └── routes/           # rotas HTTP (home, auth, fretes, painel, caminhão, API)
├── test/                 # Vitest
├── seed.sql              # categorias iniciais
├── wrangler.json         # bindings, cron e assets
└── SECURITY.md           # política de segurança
```

### Onde está cada coisa

| Assunto | Arquivo principal |
| --- | --- |
| Regras de orçamento | `src/lib/rules.ts` |
| Carga, catálogo e veículos | `src/lib/carga.ts` |
| Plano de carga (packer) | `src/lib/montagem.ts` |
| Custo do transportador | `src/lib/cost.ts` |
| Referências configuráveis | `src/lib/referencias.ts` |
| Validação de formulários | `src/lib/validate.ts` |
| Sessão, cookie e login | `src/lib/session.ts`, `src/lib/crypto.ts` |
| CSRF e rate limit | `src/lib/csrf.ts`, `src/lib/rate-limit.ts` |
| E-mail e fila | `src/lib/email.ts` |
| Rotas de frete | `src/routes/freight.tsx` |
| Home, tour e ajuda | `src/pages/Home.tsx`, `src/pages/Tour.tsx` |
| Plano de carga (UI) | `src/pages/PlanoCarga.tsx` |
| Custo (UI) | `src/pages/Truck.tsx`, `src/pages/Freight.tsx` |

## Fluxos principais

### Publicar uma mudança

1. O cliente entra ou cria uma conta.
2. Em `/novo-frete`, escolhe categoria, descrição, endereços, data, ajudantes e
   os itens da carga.
3. O servidor valida tudo com Zod, calcula peso/volume e grava o frete com
   status `aberto`.
4. A mudança aparece em `/fretes` e os transportadores podem enviar propostas.

### Enviar uma proposta

1. O transportador precisa estar logado e ter papel `transportador`.
2. Ele vê a carga, o encaixe com o veículo dele e a estimativa de custo.
3. Envia um valor maior que zero, menor ou igual ao preço máximo do cliente (se
   houver) e menor que o menor orçamento dos outros transportadores.
4. Cada transportador tem uma proposta por frete; pode melhorá-la enquanto o
   frete estiver aberto.

### Escolher e encerrar

1. O dono da mudança vê todas as propostas.
2. Ao aceitar uma proposta, o frete vira `encerrado`, a proposta é marcada como
   vencedora e os demais transportadores recebem um alerta.
3. O pagamento e os detalhes são combinados direto entre as partes.

> **Importante:** hoje não existe prazo automático de encerramento. O frete fica
> `aberto` até o dono aceitar uma proposta ou cancelar. Veja
> [docs/decisoes-e-pendencias.md](docs/decisoes-e-pendencias.md) para a proposta
> de leilão com prazo.

## Documentação

A documentação é parte da entrega. Toda mudança de comportamento, regra, schema,
rota ou operação deve atualizar o documento correspondente no mesmo commit.

| Documento | Conteúdo |
| --- | --- |
| [docs/README.md](docs/README.md) | índice, regra de documentação e checklist |
| [docs/arquitetura.md](docs/arquitetura.md) | visão geral, camadas, bindings e fluxo de requisição |
| [docs/regras-de-negocio.md](docs/regras-de-negocio.md) | papéis, fretes, propostas, aceite, cancelamento, custos e plano |
| [docs/banco-de-dados.md](docs/banco-de-dados.md) | tabelas, relações, índices e migrações |
| [docs/rotas-e-api.md](docs/rotas-e-api.md) | rotas HTTP, APIs internas, autenticação e cache |
| [docs/funcionalidades.md](docs/funcionalidades.md) | catálogo de funcionalidades e status |
| [docs/operacao-e-deploy.md](docs/operacao-e-deploy.md) | ambiente local, segredos, deploy, cron, APK e troubleshooting |
| [docs/decisoes-e-pendencias.md](docs/decisoes-e-pendencias.md) | decisões de produto, lacunas e próximos passos |
| [docs/email.md](docs/email.md) | configuração de Cloudflare Email e Resend |
| [SECURITY.md](SECURITY.md) | política de segurança e proteções |

## Testes e verificações

Antes de abrir commit ou publicar:

```bash
npm run typecheck
node --check public/js/app.js
npm test
```

A suíte cobre:

- regras de orçamento (`test/rules.test.ts`);
- cálculo de custo e normalização (`test/cost.test.ts`);
- catálogo, resumo e encaixe de carga (`test/carga.test.ts`);
- montagem/plano de carga (`test/montagem.test.ts`);
- validação de CPF/CNPJ (`test/documento.test.ts`);
- bindings e ambiente (`test/env.test.ts`).

## Deploy

```bash
npm run typecheck
npm test
npm run db:migrate        # se houver migração nova
npm run deploy
```

O deploy publica o Worker, os assets de `public/` e o cron de e-mail. Confira a
seção [Operação e deploy](docs/operacao-e-deploy.md) para segredos, D1 remoto,
smoke test e rollback.

## Estado atual

- **Produção:** `https://frete.vitorsavicki.workers.dev`.
- **Último deploy registrado:** `79d5e460-2c55-4a24-9dfa-feebadb69ec5`
  (home nova, tour de primeiro acesso, ajuda fixa, vista de cima do plano e
  remoção do ARLA da interface).
- **Publicado neste deploy:** home focada no cliente, tour do Zé Mudança,
  ajuda fixa, plano de carga com vista de cima e conferência por volume,
  recomendação pelo packer e custo do transportador sem o campo de ARLA.
- **Validação:** telas conferidas em Edge headless em 1440 px e 390 px; o
  `/fretes/3` de produção mostra o baú, os 11 volumes e a conferência completa.
- **Pendências conhecidas:** domínio próprio, envio real de e-mail (depende de
  domínio na Cloudflare), prazo de encerramento do leilão, tipo de veículo
  Kombi e leitura dos textos por alguém de fora do projeto.

O estado detalhado fica em
[docs/decisoes-e-pendencias.md](docs/decisoes-e-pendencias.md).

## Segurança

As proteções implementadas, o canal de reporte e as recomendações de operação
estão em [SECURITY.md](SECURITY.md). Em resumo:

- CSP fechada em `'self'`, sem `unsafe-inline`/`unsafe-eval`;
- SSR com escape automático do JSX;
- prepared statements em todas as queries;
- senhas com PBKDF2-SHA256 e sessões opacas;
- CSRF por origem e `SameSite=Lax`;
- validação Zod em todos os formulários;
- rate limit nos endpoints de autenticação e nas APIs mais abusáveis;
- auditoria de ações sensíveis em `audit_log`.

## Licença

Projeto privado (`"private": true` no `package.json`). Não há licença de uso
aberto definida neste repositório.
