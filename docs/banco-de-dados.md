# Banco de dados

O site usa **Cloudflare D1** (SQLite). O schema fica em `migrations/` e é
aplicado com Wrangler. Todas as consultas usam SQL preparado (`bind`); não há
ORM.

## Convenções

- Nomes em `snake_case`.
- Datas e horas em **ISO-8601 UTC** (`AAAA-MM-DDTHH:MM:SS.sssZ`).
- Datas de calendário (retirada) em `AAAA-MM-DD`; horário em `HH:MM`.
- Booleanos em `0`/`1`.
- Dinheiro em reais (`REAL`), sem centavos separados.
- Peso em kg; medidas de veículo em cm; volume em m³.

## Migrações

| Arquivo | O que cria/altera |
| --- | --- |
| `0001_init.sql` | `users`, `sessions`, `categories`, `freight`, `bids`, `alerts`, `audit_log` |
| `0002_google.sql` | `users.google_sub`, `users.avatar_url` e índice único |
| `0003_carga_caminhao.sql` | carga no frete (`carga_itens`, `carga_peso_kg`, `carga_volume_m3`) e tabela `trucks` |
| `0004_custos_servico.sql` | combustível e custos operacionais em `trucks` (inclui `arla_por_km`) |
| `0005_configuracoes.sql` | tabela `configuracoes` e referências iniciais |
| `0006_email_outbox.sql` | fila `email_outbox` e índice de pendentes |
| `0007_prazo_leilao.sql` | `freight.situacao`, `freight.encerra_em`, `freight.prazo_escolha_em`, backfill e índice |

Aplicar:

```bash
npm run db:migrate:local
npm run db:migrate
```

Nunca edite uma migração já aplicada em produção. Crie a próxima.

## Tabelas

### `users`

Uma linha por pessoa. O e-mail é único sem diferenciar maiúsculas.

| Coluna | Tipo | Regra |
| --- | --- | --- |
| `id` | inteiro | chave primária |
| `email` | texto | único, `COLLATE NOCASE` |
| `password_hash` | texto | `pbkdf2:iteracoes:salt:hash` |
| `nome`, `sobrenome` | texto | obrigatórios |
| `telefone` | texto | opcional |
| `cpf_cnpj` | texto | só dígitos; obrigatório para transportador na aplicação |
| `papel` | texto | `cliente`, `transportador` ou `admin` |
| `ativo` | inteiro | `1` permite login; `0` bloqueia |
| `google_sub` | texto | identificador do Google, único quando existe |
| `avatar_url` | texto | foto do Google, opcional |
| `criado_em` | texto | data de criação |

### `sessions`

Sessões opacas. O cookie leva apenas o `id` aleatório; o banco guarda o dono e a
validade.

| Coluna | Regra |
| --- | --- |
| `id` | 32 bytes hexadecimais |
| `user_id` | referência a `users`, apaga em cascata |
| `expira_em` | validade de 30 dias |
| `ip`, `user_agent` | contexto de auditoria |

Índice: `idx_sessions_expira`.

### `categories`

Categorias exibidas no formulário e nos filtros. `nome` é único. As iniciais
estão em `seed.sql`.

### `freight`

A mudança publicada pelo cliente. O endereço é desnormalizado de propósito: um
frete tem exatamente uma origem e um destino.

| Grupo | Colunas |
| --- | --- |
| Dono e categoria | `user_id`, `category_id` |
| Texto | `descricao`, `comentario` |
| Serviço | `ajudantes`, `preco_maximo` |
| Estado | `situacao` (`aberto`, `em_escolha`, `sem_lances`, `expirado`, `encerrado`, `cancelado`), `status` (legado), `motivo_cancelamento` |
| Prazos | `encerra_em`, `prazo_escolha_em` |
| Retirada | `data_retirada`, `hora_retirada` |
| Origem | `origem_cep`, `origem_rua`, `origem_bairro`, `origem_cidade`, `origem_uf`, `origem_lat`, `origem_lng` |
| Destino | `destino_cep`, `destino_rua`, `destino_bairro`, `destino_cidade`, `destino_uf`, `destino_lat`, `destino_lng` |
| Carga | `carga_itens` (JSON), `carga_peso_kg`, `carga_volume_m3` |
| Criação | `criado_em` |

Índices: `idx_freight_status`, `idx_freight_user`, `idx_freight_situacao`.

`situacao` é o campo que manda nas regras novas. `status` continua existindo
para consultas antigas e é atualizado junto (`encerrado` para `sem_lances`,
`expirado` e `encerrado`).

`encerra_em` é o fim do recebimento de propostas. `prazo_escolha_em` é o fim da
janela de escolha (48 h depois). O formulário envia `prazo_recebimento` (24, 48
ou 72 horas), mas ele não é gravado como coluna: o servidor converte em
`encerra_em` e `prazo_escolha_em`. A migração `0007` preencheu os fretes antigos
com 48 h a partir da criação e criou o índice de `situacao`.

`carga_itens` guarda uma lista JSON de itens com `id`, `nome`, `emoji`,
`categoria`, `quantidade`, `pesoKg`, `comprimentoCm`, `larguraCm`, `alturaCm`,
`fragil` e `empilhavel`. O servidor recalcula os totais ao publicar.

### `bids`

Propostas dos transportadores. Leilão reverso: menor valor primeiro, mas o
cliente pode aceitar qualquer proposta.

| Coluna | Regra |
| --- | --- |
| `freight_id` | referência ao frete, apaga em cascata |
| `transportador_id` | referência ao usuário |
| `valor` | maior que zero |
| `mensagem` | opcional, até 500 caracteres na aplicação |
| `vencedor` | `1` na proposta aceita |
| `criado_em` | data da proposta/melhoria |
| — | `UNIQUE (freight_id, transportador_id)` |

Índices: `idx_bids_freight`, `idx_bids_transportador`.

### `alerts`

Avisos in-app do sino e do painel.

| Coluna | Regra |
| --- | --- |
| `user_id` | destinatário |
| `freight_id` | frete relacionado, opcional |
| `tipo` | `novo_lance`, `lance_superado`, `voce_venceu`, `lance_perdido`, `frete_cancelado` |
| `mensagem` | texto curto |
| `lido` | `0`/`1` |

Índice: `idx_alerts_user`.

### `audit_log`

Registro de ações sensíveis (`login`, `cadastro`, `publicar_frete`, `lance`,
`aceitar_lance`, `cancelar_frete`, `sair`). Guarda `acao`, `entidade`,
`entidade_id` e `meta` em JSON, sem senha nem token.

Índice: `idx_audit_user`.

### `trucks`

Um veículo por transportador (`user_id` único).

| Coluna | Regra |
| --- | --- |
| `nome` | apelido do veículo |
| `tipo` | `fiorino`, `van`, `tres_quartos`, `bau`, `carreta`, `outro` |
| `comprimento_cm`, `largura_cm`, `altura_cm` | medidas internas, maiores que zero |
| `capacidade_kg` | maior que zero |
| `consumo_km_l` | maior que zero |
| `diesel_preco` | preço do combustível usado no cálculo |
| `tipo_combustivel` | `diesel` ou `gasolina` |
| `pedagio_por_km`, `manutencao_por_km`, `arla_por_km` | custos por km |
| `taxa_base`, `horas_servico`, `custo_hora`, `custo_ajudante`, `custo_fixo` | custos do serviço |
| `margem_percentual` | 0 a 100 |
| `criado_em`, `atualizado_em` | auditoria |

O `trucks.tipo` **não aceita `kombi`**. Quem usa Kombi deve cadastrá-la como
`van` ou `outro`. A home não exibe mais vitrine de veículos, então não há
promessa de um tipo que o formulário não aceita.

`arla_por_km` continua no banco e no cálculo interno, mas não aparece mais na
interface. Perfis antigos mantêm o valor salvo; perfis novos recebem `0.06`.

### `configuracoes`

Chave-valor para ajustar referências sem deploy.

| Chave | Conteúdo |
| --- | --- |
| `combustivel` | `{"diesel":7.37,"gasolina":6.09}` |
| `custos` | pedágio, manutenção, ARLA, horas, hora, ajudante, fator de rodovia, custo fixo e margem |
| `taxa_base` | taxa de saída sugerida por tipo de veículo |
| `mercado` | piso local, teto local e R$/km por tipo de veículo |

O app lê os valores com fallback para `src/lib/referencias.ts` se a tabela não
responder.

### `email_outbox`

Fila de e-mails transacionais.

| Coluna | Regra |
| --- | --- |
| `destinatario`, `assunto`, `html`, `texto` | conteúdo da mensagem |
| `status` | `pendente`, `enviado`, `descartado` |
| `tentativas` | até 8 |
| `ultimo_erro` | último erro do provedor |
| `proxima_tentativa` | controle do cron |
| `criado_em`, `enviado_em` | auditoria |

Índice: `idx_email_outbox_pendente`.

## Relações

```text
users 1 ── N freight
users 1 ── N bids
users 1 ── 1 trucks
users 1 ── N sessions
users 1 ── N alerts
users 1 ── N audit_log
categories 1 ── N freight
freight 1 ── N bids
freight 1 ── N alerts
```

## O que não existe no banco

- tabela de pagamentos, contratos ou comissões;
- tabela separada de prazos do leilão (os prazos são colunas de `freight`);
- tabela de avaliações ou depoimentos;
- tabela de administração além do papel `admin` em `users`.
