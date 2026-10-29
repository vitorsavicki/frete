# Arquitetura

O Quero Mudar Já é um **Worker da Cloudflare** que renderiza HTML no servidor e
usa o D1 como banco. Não há SPA, bundler de cliente nem chamada direta do
navegador a serviços externos: o Worker faz a ponte e devolve HTML/JSON.

## Visão geral

```text
Navegador / app Android
        │
        │ HTTPS (HTML, CSS, JS, APK)
        ▼
┌─────────────────────────────────────────────────────────┐
│ Cloudflare Worker (src/index.tsx)                        │
│                                                         │
│  secureHeaders → sessão → CSRF → rate limit → rotas     │
│                                                         │
│  ┌──────────────┐   ┌──────────────┐   ┌─────────────┐  │
│  │ Hono + JSX   │   │ lib/regras   │   │ lib/carga   │  │
│  │ páginas SSR  │   │ custo, plano │   │ e-mail      │  │
│  └──────┬───────┘   └──────┬───────┘   └──────┬──────┘  │
│         │                  │                  │         │
│         ▼                  ▼                  ▼         │
│  ┌───────────────────────────────────────────────────┐  │
│  │ D1 (SQLite): users, freight, bids, trucks, ...    │  │
│  └───────────────────────────────────────────────────┘  │
│         │                                               │
│         ├── Workers AI (opcional)                        │
│         ├── Cloudflare Email / Resend (opcional)         │
│         └── APIs externas server-side                    │
│             (BrasilAPI, ViaCEP, Nominatim, OSRM,         │
│              Open-Meteo, IBGE, Google OAuth)             │
└─────────────────────────────────────────────────────────┘
        │
        ▼
Assets estáticos em public/ (CSS, JS, imagens, Leaflet, APK)
```

## Camadas

### 1. Worker e middlewares

Arquivo: `src/index.tsx`.

Ordem dos middlewares:

1. `secureHeaders` — CSP, HSTS, `X-Frame-Options`, `Permissions-Policy`,
   `Referrer-Policy` e demais headers.
2. `middlewareSessao` — lê o cookie `frete_sessao`, valida a sessão no D1 e
   coloca o usuário em `c.get('user')`.
3. `protecaoOrigem` — bloqueia mutações cross-site (CSRF).
4. `limiteAuth` em `/login` e `/cadastro`; `limiteApi` nas APIs de rota e IA.
5. Rotas montadas em `/` e `/api`.

O Worker também exporta `scheduled`, que roda o cron a cada 15 minutos para
reprocessar a fila de e-mails.

### 2. Rotas HTTP

Arquivos: `src/routes/*.tsx`.

| Arquivo | Responsabilidade |
| --- | --- |
| `home.tsx` | home pública, `robots.txt`, `sitemap.xml` |
| `auth.tsx` | cadastro, login local, login Google, logout |
| `freight.tsx` | listagem, detalhe, publicação, proposta, aceite, cancelamento |
| `dashboard.tsx` | painel do usuário e leitura de alertas |
| `truck.tsx` | perfil do caminhão do transportador |
| `android.tsx` | página do app e download do APK |
| `api.ts` | CEP, cidades, referências, rota, status de e-mail e IA |

As rotas usam `exigirLogin` e checam o papel antes de qualquer mutação. Os
formulários são validados com Zod (`src/lib/validate.ts`).

### 3. Páginas SSR

Arquivos: `src/pages/*.tsx`.

- `Layout.tsx` — cabeçalho, navegação, alertas, rodapé, tour e ajuda fixa.
- `Home.tsx` — home pública.
- `Freight.tsx` — listagem, detalhe, formulário de publicação e simulador.
- `Dashboard.tsx` — painel do cliente/transportador.
- `Truck.tsx` — perfil do caminhão.
- `Auth.tsx` — login e cadastro.
- `PlanoCarga.tsx` — desenho do plano (corte lateral + vista de cima).
- `Tour.tsx` — tour pela ajuda fixa e painel do mascote.
- `Ilustracoes.tsx` — SVGs próprios (mascote, cena, veículos, ícones).
- `FreteCard.tsx` — cartão de frete reutilizável.
- `AppAndroid.tsx` — página do APK.

O JSX do Hono escapa texto por padrão. Não use `innerHTML` nem
`dangerouslySetInnerHTML` para conteúdo de usuário.

### 4. Biblioteca de domínio

Arquivos: `src/lib/*.ts`.

| Módulo | Papel |
| --- | --- |
| `carga.ts` | catálogo de itens, parse do formulário/DB, resumo, encaixe, veículos |
| `montagem.ts` | packer do plano de carga e recomendação por plano |
| `cost.ts` | modelo de custo, margem, preço mínimo e faixa de mercado |
| `referencias.ts` | referências editáveis no D1 (combustível, custos, taxas, mercado) |
| `rules.ts` | regras puras de proposta, aceite e cancelamento |
| `validate.ts` | schemas Zod de todos os formulários |
| `db.ts` | tipos e helpers de auditoria/alerta |
| `session.ts` | criação, leitura e revogação de sessão |
| `crypto.ts` | hash de senha e token de sessão |
| `csrf.ts` | proteção de origem |
| `rate-limit.ts` | limite em memória por IP/rota |
| `email.ts` | envio, fila e templates |
| `geo.ts` | CEP, geocodificação e haversine |
| `rota.ts` | OSRM + fallback por linha reta |
| `clima.ts` | previsão do tempo para a retirada |
| `ia.ts` | textos e análise de carga com Workers AI |
| `google.ts` | OAuth do Google |
| `documento.ts` | validação de CPF/CNPJ |
| `format.ts` | formatação de dinheiro, peso, volume, datas e distância |
| `apk.ts` | metadados do APK gerado pelo build Android |

## Fluxo de uma requisição

Exemplo: `GET /fretes/3`.

1. O Worker aplica headers de segurança.
2. O middleware de sessão identifica o usuário (ou anônimo).
3. A rota consulta o frete, as propostas e o caminhão do transportador.
4. Se faltarem coordenadas, geocodifica e persiste o resultado.
5. Calcula rota (OSRM ou fallback), distância, clima e, para transportador,
   encaixe e estimativa de custo.
6. Renderiza o JSX e devolve HTML com cache-control apropriado.

Exemplo: `POST /fretes/3/lances`.

1. Sessão + CSRF + validação Zod.
2. Consulta o frete e o menor orçamento dos outros transportadores.
3. Aplica `validarLance` (papel, status, valor, teto e ordem).
4. Faz `INSERT ... ON CONFLICT` da proposta.
5. Cria alertas e dispara e-mail em `waitUntil` (não bloqueia a resposta).
6. Redireciona com mensagem.

## Serviços externos

Todos são chamados **server-side**. O navegador só fala com o próprio Worker,
o que mantém a CSP fechada.

| Serviço | Uso | Fallback |
| --- | --- | --- |
| BrasilAPI | CEP + coordenadas | ViaCEP e Nominatim |
| ViaCEP | CEP sem coordenadas | — |
| Nominatim/OpenStreetMap | geocodificação de endereço | sem mapa |
| OSRM | rota rodoviária real | linha reta × 1,25 |
| Open-Meteo | previsão do tempo na retirada | sem previsão |
| IBGE | lista de cidades por UF | erro amigável |
| Workers AI | melhorar texto e analisar carga | recurso desabilitado |
| Cloudflare Email / Resend | e-mails transacionais | fila no D1 |
| Google OAuth | login social | login local |

## Cache

| Onde | O que | TTL |
| --- | --- | --- |
| `public/_headers` | assets estáticos | conforme o arquivo |
| `GET /api/cep/:cep` | resposta de CEP | 1 dia |
| `GET /api/cidades/:uf` | cidades do IBGE | 7 dias |
| `GET /api/referencias` | referências de custo | 5 min + stale-while-revalidate |
| `GET /api/rota` | rota calculada | 1 dia |
| `src/lib/rota.ts` | cache em memória por coordenada | 6 h (OSRM) / 1 h (fallback) |
| `src/lib/clima.ts` | previsão por lat/lng/data | 30 min |
| `src/lib/referencias.ts` | referências no isolate | 60 s |

O D1 é a fonte de verdade; os caches em memória são best-effort e somem quando
o isolate é reciclado.

## Decisões de arquitetura

- **SSR em vez de SPA:** menos JavaScript, melhor SEO, funciona sem JS e cabe no
  plano gratuito da Cloudflare.
- **CSP fechada:** nenhum script ou estilo inline; todo JS/CSS é servido de
  `public/`.
- **APIs externas no Worker:** o navegador não precisa de CORS nem de chaves; a
  CSP continua `'self'`.
- **D1 com SQL preparado:** sem ORM e sem concatenação de SQL.
- **Regras puras:** `rules.ts`, `carga.ts`, `montagem.ts` e `cost.ts` podem ser
  testados sem Worker nem D1.
- **Fila de e-mail no D1:** uma falha do provedor não perde a mensagem nem
  bloqueia o fluxo principal.
- **Ilustrações em SVG inline:** leves, nítidas, funcionam offline no APK e não
  dependem de banco de imagens.

## Limites conhecidos

- O rate limit em memória é por isolate; a proteção forte deve ser uma regra de
  Rate Limiting na borda da Cloudflare.
- A geocodificação é best-effort: sem coordenadas, o mapa não aparece, mas o
  frete continua válido.
- A rota pode ser aproximada quando o OSRM falha; o valor é indicado na tela.
- A IA é opcional e nunca substitui a conferência determinística de encaixe.
- O cron de e-mail depende do gatilho configurado no `wrangler.json`.
