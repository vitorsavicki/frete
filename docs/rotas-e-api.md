# Rotas e API

O Worker responde HTML renderizado no servidor e algumas APIs internas em JSON.
Não há API pública versionada nem CORS aberto: o JavaScript do cliente fala
apenas com o mesmo domínio.

## Middlewares

Ordem em `src/index.tsx`:

1. `secureHeaders` — CSP, HSTS, `X-Frame-Options`, `Referrer-Policy` etc.
2. `middlewareSessao` — lê o cookie, valida no D1 e define `c.get('user')`.
3. `protecaoOrigem` — bloqueia `POST` de outra origem (CSRF).
4. `limiteAuth` em `/login` e `/cadastro`.
5. `limiteApi` em `/api/rota` e `/api/ia/*`.

## Páginas

| Método | Rota | Quem vê | O que faz |
| --- | --- | --- | --- |
| `GET` | `/` | todos | Home pública, categorias, tour de primeiro acesso e ajuda fixa |
| `GET` | `/fretes` | todos | Lista mudanças abertas com filtros; aplica o relógio antes de consultar |
| `GET` | `/fretes/:id` | todos | Detalhe do frete, rota, carga, plano, status e prazos; propostas só para o dono. 404 para visitante em `sem_lances`, `expirado` ou `cancelado` |
| `GET` | `/novo-frete` | cliente logado | Formulário de publicação, com prazo de 24/48/72 h |
| `POST` | `/fretes` | cliente logado | Valida, calcula os prazos e publica a mudança |
| `POST` | `/fretes/:id/lances` | transportador logado | Envia ou melhora a proposta enquanto o relógio permite |
| `POST` | `/fretes/:id/aceitar/:bidId` | dono do frete | Aceita uma proposta em `em_escolha`, antes do fim da janela |
| `POST` | `/fretes/:id/cancelar` | dono do frete | Cancela um frete `aberto` ou `em_escolha` |
| `GET` | `/painel` | logado | Painel com alertas e fretes/propostas |
| `POST` | `/alertas/ler` | logado | Marca todos os alertas como lidos |
| `GET` | `/caminhao` | transportador logado | Perfil do veículo e custos |
| `POST` | `/caminhao` | transportador logado | Salva o perfil do veículo |
| `GET` | `/cadastro` | visitante | Formulário de conta |
| `POST` | `/cadastro` | visitante | Cria conta local |
| `GET` | `/login` | visitante | Formulário de login |
| `POST` | `/login` | visitante | Autentica |
| `GET` | `/auth/google` | visitante | Inicia OAuth do Google, se configurado |
| `GET` | `/auth/google/callback` | visitante | Conclui OAuth |
| `POST` | `/sair` | logado | Encerra a sessão |
| `GET` | `/app` | todos | Página de download do APK |
| `GET` | `/robots.txt` | buscadores | Regras de indexação |
| `GET` | `/sitemap.xml` | buscadores | Sitemap com fretes abertos |

### Redirecionamentos e mensagens

As rotas de formulário redirecionam com `?msg=` ou `?erro=` para exibir um
aviso. Exemplo: `POST /fretes/3/lances` volta para `/fretes/3?msg=...`.

## APIs internas

Todas ficam sob `/api` e devolvem JSON.

| Método | Rota | Autenticação | Cache | Observação |
| --- | --- | --- | --- | --- |
| `GET` | `/api/cep/:cep` | pública | 1 dia | BrasilAPI + fallback ViaCEP |
| `GET` | `/api/cidades/:uf` | pública | 7 dias | IBGE |
| `GET` | `/api/referencias` | pública | 5 min + SWR | Custos de referência configuráveis |
| `GET` | `/api/email/status` | pública | `no-store` | Diagnóstico sem destinatários/segredos |
| `GET` | `/api/rota` | pública com rate limit | 1 dia | OSRM, com fallback de linha reta |
| `POST` | `/api/ia/texto` | logado | sem cache | Workers AI; 503 sem binding |
| `POST` | `/api/ia/carga` | transportador | sem cache | Analisa carga, encaixe e custo |

### Parâmetros

`GET /api/rota`:

- `origem_lat`, `origem_lng`, `destino_lat`, `destino_lng` — números válidos.

`POST /api/ia/texto`:

```json
{ "texto": "texto para melhorar", "tipo": "descricao" }
```

`POST /api/ia/carga`:

```json
{ "freight_id": 3 }
```

### Erros

As APIs usam JSON `{ "erro": "mensagem simples" }` com status HTTP adequado:

- `400` dados inválidos;
- `401` não autenticado;
- `403` papel sem permissão;
- `404` não encontrado;
- `409` falta cadastro de veículo;
- `422` frete sem lista de carga;
- `502` serviço externo indisponível;
- `503` IA desligada ou provedor fora do ar.

## Segurança por rota

- `POST` sempre passa por verificação de origem, além do cookie `SameSite=Lax`.
- Rotas de dono comparam `freight.user_id` com o usuário da sessão.
- Rotas de transportador checam `user.papel`.
- Toda query usa `bind`; nenhuma entrada vai direto para o SQL.
- O detalhe público não mostra a tabela de propostas para quem não é o dono.
- O e-mail de usuários não aparece em listagens públicas.
- O simulador de custo e os dados do caminhão são exclusivos do transportador.

## Cache e indexação

- Assets estáticos usam `public/_headers`.
- `robots.txt` e `sitemap.xml` são gerados pelo Worker.
- Só fretes com `situacao = 'aberto'` entram na home, na listagem e no sitemap.
- Mudanças `sem_lances`, `expirado` ou `cancelado` retornam 404 para visitantes;
  `em_escolha` e `encerrado` continuam acessíveis pelo link direto e recebem
  `noindex`.
- Páginas dinâmicas não usam cache de CDN além do que a rota declara.

## Relógio do leilão

`aplicarPrazos` roda antes das consultas em:

- `GET /fretes`
- `GET /fretes/:id`
- `GET /painel`
- `GET /sitemap.xml`
- cron `scheduled` (a cada 5 minutos)

A rotina é idempotente e devolve a contagem de `sem_lances`, `em_escolha` e
`expirado`. Ela é a mesma para todos os pontos, então não há divergência de
estado entre telas.
