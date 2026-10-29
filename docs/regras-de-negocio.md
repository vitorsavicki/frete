# Regras de negócio

Este documento descreve as regras **como o código as aplica hoje**. Quando uma
regra mudar, atualize este arquivo e os testes correspondentes.

> Legenda de status: **✅ implementado** · **🟡 em andamento** · **⏳ planejado**

## 1. Papéis e permissões

O campo `users.papel` aceita `cliente`, `transportador` e `admin`.

| Ação | Cliente | Transportador | Admin | Visitante |
| --- | --- | --- | --- | --- |
| Ver home, listagem e detalhe de frete | ✅ | ✅ | ✅ | ✅ |
| Criar conta | ✅ | ✅ | — | ✅ |
| Publicar mudança | ✅ | ❌ | ❌ | ❌ |
| Editar/cancelar a própria mudança | ✅ | ❌ | ❌ | ❌ |
| Ver todas as propostas de uma mudança | ✅ (dono) | ❌ | ❌ | ❌ |
| Enviar/melhorar proposta | ❌ | ✅ | ❌ | ❌ |
| Aceitar proposta | ✅ (dono) | ❌ | ❌ | ❌ |
| Cadastrar caminhão | ❌ | ✅ | ❌ | ❌ |
| Ver simulador de custo | ❌ | ✅ | ❌ | ❌ |
| Ver painel | ✅ | ✅ | ✅ | ❌ |
| Usar IA de texto | ✅ | ✅ | ✅ | ❌ |
| Usar IA de análise de carga | ❌ | ✅ | ❌ | ❌ |

O papel `admin` existe no schema, mas não há tela administrativa implementada.
As verificações de rota usam `exigirLogin` + checagem explícita de `papel`.

## 2. Cadastro e conta ✅

### Cadastro local

- Nome, sobrenome, e-mail, senha e papel são obrigatórios.
- E-mail é normalizado para minúsculas e é único (`COLLATE NOCASE`).
- Senha: mínimo 8 e máximo 72 caracteres; hash PBKDF2-SHA256 com 100.000
  iterações e salt de 16 bytes.
- **Transportador** precisa informar CPF ou CNPJ válido (dígitos verificadores
  conferidos localmente). Cliente pode deixar em branco.
- Documento é salvo só com dígitos.
- E-mail duplicado retorna erro amigável (“Este e-mail já está cadastrado”).
- Após o cadastro, o usuário já sai logado e recebe um e-mail de boas-vindas
  (se houver provedor configurado).

### Login local

- E-mail + senha; mensagem genérica em caso de falha para não revelar se o
  e-mail existe.
- Usuário com `ativo = 0` não entra.
- Sessão opaca de 32 bytes, cookie `HttpOnly`, `Secure` (em HTTPS),
  `SameSite=Lax`, validade de 30 dias.
- Logout apaga a sessão no D1 e limpa o cookie.

### Login com Google (opcional)

- Só aparece se `GOOGLE_CLIENT_ID` e `GOOGLE_CLIENT_SECRET` estiverem
  configurados.
- Usa OAuth 2.0 com `state` em cookie `HttpOnly` de 10 minutos.
- Exige e-mail verificado no Google.
- Se já existir usuário com o mesmo e-mail, vincula o `google_sub` e o avatar.
- Se não existir, cria conta com o papel escolhido (`cliente` por padrão,
  `transportador` quando veio de `?papel=transportador`).

## 3. Publicação de mudança ✅

Rota: `POST /fretes` (somente `cliente`).

### Campos e limites

| Campo | Regra |
| --- | --- |
| `category_id` | inteiro positivo existente em `categories` |
| `descricao` | 5 a 255 caracteres |
| `comentario` | opcional, até 500 caracteres |
| `ajudantes` | inteiro de 0 a 10 |
| `preco_maximo` | opcional, positivo, até R$ 1.000.000 |
| `data_retirada` | `AAAA-MM-DD` |
| `hora_retirada` | `HH:MM` opcional |
| `origem_*` / `destino_*` | CEP válido, rua/bairro/cidade com ao menos 2 caracteres, UF com 2 letras |
| itens da carga | até 200 unidades no formulário |

### Itens da carga

- O catálogo tem móveis, eletrodomésticos, caixas e itens especiais, cada um
  com peso, comprimento, largura, altura, fragilidade e empilhamento.
- Caixas podem ser pequenas, médias, grandes ou com medida informada.
- Medida personalizada válida: comprimento 10–200 cm, largura 10–200 cm, altura
  10–200 cm, peso até 500 kg. Se inválida, cai para a caixa média.
- O formulário calcula peso e volume em tempo real; o servidor recalcula e grava
  `carga_itens` (JSON), `carga_peso_kg` e `carga_volume_m3`.

### Endereço e geocodificação

- CEP é consultado na BrasilAPI (fallback ViaCEP) para rua, bairro, cidade e UF.
- Coordenadas são buscadas no Nominatim (best-effort) no momento da publicação.
- Fretes antigos sem coordenadas são geocodificados sob demanda no detalhe e o
  resultado é persistido.
- Sem coordenadas, o mapa não aparece, mas o frete continua válido.

### Estado inicial

- Todo frete nasce com `situacao = 'aberto'` e `status = 'aberto'` (campo legado
  mantido em sincronia).
- O cliente escolhe o prazo de recebimento: **24, 48 ou 72 horas** (padrão 48).
- O servidor grava `encerra_em` (fim das propostas) e `prazo_escolha_em`
  (48 horas depois do fim das propostas).
- Depois de `encerra_em`, o frete sai da listagem pública mesmo que a rotina de
  prazos ainda não tenha rodado: a listagem filtra `situacao = 'aberto'` e
  `validarLance` confere o relógio.

## 4. Propostas (leilão reverso) ✅

Rota: `POST /fretes/:id/lances` (somente `transportador` logado).

### Regras aplicadas

`src/lib/rules.ts` → `validarLance`:

1. O dono do frete não pode propor na própria mudança.
2. Só `transportador` pode propor.
3. O frete precisa estar em `situacao = 'aberto'`.
4. O relógio precisa estar antes de `encerra_em`. O horário é conferido no
   servidor, não só na tela.
5. O valor precisa ser maior que zero.
6. Se o cliente definiu `preco_maximo`, a proposta não pode passar dele.
7. A proposta precisa ser **estritamente menor** que o menor orçamento dos
   outros transportadores. Se ainda não há proposta de outro, qualquer valor
   positivo dentro do teto vale.
8. Cada transportador tem **uma proposta por frete**. Enviar de novo atualiza a
   mesma linha (`ON CONFLICT ... DO UPDATE`), inclusive mensagem e data.

### Ordem e visibilidade

- A ordem é crescente por valor: menor proposta primeiro.
- O dono vê todas as propostas com nome do transportador.
- Cada transportador vê apenas a própria proposta.
- A listagem pública mostra a **quantidade de propostas** e o **menor valor**
  (`FreteCard`). O detalhe público não mostra a tabela de propostas para quem
  não é o dono.
- O cliente pode aceitar **qualquer** proposta, não obrigatoriamente a menor.

### Mensagem

- Opcional, até 500 caracteres.
- Não é exibida para outros transportadores; o dono vê na tabela.

## 5. Aceite da proposta ✅

Rota: `POST /fretes/:id/aceitar/:bidId`.

Regras (`podeAceitarLance`):

- somente o dono do frete;
- com `situacao = 'aberto'` (recebendo orçamentos) ou `'em_escolha'` (prazo de
  recebimento encerrado) — o cliente escolhe quando quiser, desde o primeiro
  orçamento;
- o relógio precisa estar antes de `prazo_escolha_em`;
- a proposta precisa pertencer ao frete.

Efeitos:

1. `bids.vencedor = 1` na proposta escolhida.
2. `freight.situacao = 'encerrado'` e `status = 'encerrado'`.
3. Alerta `voce_venceu` para o vencedor.
4. Alerta `lance_perdido` para os demais que enviaram proposta.
5. E-mail para o vencedor (se o provedor estiver configurado).
6. Registro em `audit_log` (`aceitar_lance`).
7. Redireciona com mensagem de sucesso.

O aceite é uma operação em lote (`DB.batch`) para marcar vencedor e encerrar o
frete juntos. Não há transação financeira, contrato ou pagamento no site.

## 6. Cancelamento ✅

Rota: `POST /fretes/:id/cancelar`.

Regras (`podeCancelarFrete`):

- somente o dono;
- somente com `situacao = 'aberto'` ou `'em_escolha'`.

Efeitos:

- `freight.situacao = 'cancelado'` e `status = 'cancelado'`;
- alerta `frete_cancelado` para todos os transportadores que enviaram proposta;
- registro em `audit_log`.

Não há multa, taxa ou penalidade. Depois de encerrado, sem propostas, expirado
ou cancelado, o frete não aceita novas propostas nem novo aceite.

## 7. Estados do frete

```text
                  fim do prazo, sem propostas
  aberto ─────────────────────────────────────► sem_lances
    │
    │ fim do prazo, com propostas
    ▼
  em_escolha ─── aceite do dono ──────────────► encerrado
    │
    │ prazo de escolha vence
    ▼
  expirado

  aberto ou em_escolha ─── cancelamento ──────► cancelado
```

| Situação | Recebe propostas? | Aceita proposta? | Pode cancelar? | Aparece na lista? | Detalhe público? |
| --- | --- | --- | --- | --- | --- |
| `aberto` | ✅ (até `encerra_em`) | ❌ | ✅ | ✅ | ✅ |
| `em_escolha` | ❌ | ✅ (até `prazo_escolha_em`) | ✅ | ❌ | ✅ |
| `sem_lances` | ❌ | ❌ | ❌ | ❌ | ❌ (só o dono) |
| `expirado` | ❌ | ❌ | ❌ | ❌ | ❌ (só o dono) |
| `encerrado` | ❌ | ❌ | ❌ | ❌ | ✅ |
| `cancelado` | ❌ | ❌ | ❌ | ❌ | ❌ (só o dono) |

O relógio é aplicado por `aplicarPrazos` (`src/lib/prazos.ts`), chamado na
listagem, no detalhe, no painel, no sitemap e pelo cron a cada 5 minutos. A
atualização é idempotente: rodar duas vezes não muda nada. O campo legado
`status` continua existindo e é atualizado junto para não quebrar consultas
antigas.

## 8. Alertas ✅

Tabela `alerts`, exibida no painel e no sino do cabeçalho.

| Tipo | Quando | Destinatário |
| --- | --- | --- |
| `novo_lance` | nova proposta ou melhoria | dono do frete |
| `lance_superado` | alguém enviou valor menor | transportador que estava com o menor |
| `voce_venceu` | proposta aceita | transportador vencedor |
| `lance_perdido` | frete fechado com outro | demais transportadores |
| `frete_cancelado` | frete cancelado | todos que propuseram |

O painel mostra os 30 alertas mais recentes, não lidos primeiro. “Marcar todos
como lidos” atualiza todos os alertas do usuário.

## 9. E-mails ✅ (configuração opcional)

| Evento | Destinatário | Template |
| --- | --- | --- |
| cadastro | novo usuário | boas-vindas |
| nova proposta | dono do frete | aviso de novo orçamento |
| proposta aceita | transportador vencedor | aviso de vitória |

- O envio tenta Cloudflare Email, depois Resend, depois grava em
  `email_outbox`.
- O cron a cada 15 minutos reprocessa até 20 mensagens por execução.
- Cada mensagem tem até 8 tentativas, com espera crescente até 24 h; depois vira
  `descartado` com o erro em `ultimo_erro`.
- Não há e-mail para o perdedor; ele recebe apenas alerta in-app.
- Detalhes de configuração: [email.md](email.md).

## 10. Custo do transportador ✅

O simulador é **exclusivo do transportador** e nunca aparece para o cliente.
O cliente vê apenas rota, carga e propostas.

### Fórmula

```text
distância = OSRM real ou (linha reta × fatorRodovia)
litros = distância / consumoKmL
combustível = litros × preçoCombustível
pedágio = distância × pedagioPorKm
manutenção = distância × manutencaoPorKm
ARLA = distância × arlaPorKm          (somente diesel; custo interno)
custoTotal = combustível + pedágio + manutenção + ARLA
           + taxaBase + horasServico × custoHora
           + ajudantes × custoAjudante + custoFixo
margem = custoTotal × margemPercentual / 100
preçoCusto = arredondarParaCima10(custoTotal + margem)
preçoSugerido = max(preçoCusto, pisoMercado)
```

- `preçoMínimo` = custo total arredondado para cima em R$ 10.
- `pisoMercado` = máximo entre o piso local e a distância × R$/km mínimo da
  faixa do veículo.
- `preçoSugerido` nunca fica abaixo do piso de mercado.
- O transportador pode alterar qualquer parâmetro no perfil ou no simulador; o
  valor final da proposta é sempre decisão dele.

### Padrões e referências

Os valores padrão ficam em `src/lib/cost.ts` e podem ser sobrescritos na tabela
`configuracoes` (sem deploy):

| Referência | Padrão |
| --- | --- |
| Diesel | R$ 7,37/L |
| Gasolina | R$ 6,09/L |
| Pedágio | R$ 0,10/km |
| Manutenção | R$ 0,45/km |
| ARLA (interno) | R$ 0,06/km |
| Horas de serviço | 2 h |
| Custo da hora | R$ 45/h |
| Ajudante | R$ 120/frete |
| Fator linha reta → estrada | 1,25 |
| Margem padrão | 20% |

### ARLA na interface

O ARLA continua no **cálculo interno** de custo (é um insumo real de caminhões
diesel), mas foi removido da interface:

- não há campo “ARLA” no perfil do caminhão;
- não há linha “ARLA” na estimativa;
- o valor padrão continua embutido no cálculo e aparece somado em
  **“Manutenção e insumos”**.

O perfil antigo que tinha um valor de ARLA salvo continua usando esse valor; o
campo apenas não é mais editável. Perfis novos usam o padrão do banco
(`arla_por_km = 0.06`). Na gasolina, o cálculo zera o ARLA.

## 11. Carga, plano e veículo ✅

### Catálogo e resumo

- `CATALOGO_CARGA` tem os itens com peso e medidas.
- `resumoCarga` soma quantidade, peso e volume e agrupa por item.
- `analisarEncaixe` compara peso e volume com o veículo e retorna `cabe`,
  `limite` ou `nao_cabe`, com motivos e avisos.

### Plano de carga (`src/lib/montagem.ts`)

O packer usa uma grade de **5 cm** no comprimento da carroceria:

1. Expande cada item em unidades, escolhendo a orientação que ocupa menos
   comprimento e ainda cabe na largura/altura.
2. Ordena: itens não empilháveis primeiro (mais altos e pesados); depois os
   empilháveis (maiores e mais leves).
3. Preenche o **piso de todas as faixas** antes de empilhar.
4. Só empilha itens empilháveis e não frágeis, sobre uma base plana e com apoio
   em toda a extensão.
5. Respeita a altura interna do veículo.
6. Itens que não cabem em nenhuma posição ficam “fora” e são contados.
7. O peso é conferido antes do encaixe: a soma dos volumes não pode passar da
   `capacidadeKg` do veículo. O que passa vira “fora por peso”.
8. A conferência item a item mostra `total`, `colocados`, `fora`, o volume e o
   peso de cada tipo.

O desenho tem três vistas, em abas:

- **3D**: vista isométrica gerada a partir dos mesmos blocos do packer, com
  topo, frente e lateral de cada volume;
- **corte lateral** da faixa 0 (a primeira faixa de largura);
- **vista de cima** proporcional ao piso, com o footprint de todos os blocos.

As abas funcionam sem JavaScript: os três painéis são renderizados e ficam
visíveis um abaixo do outro. O cliente adiciona a classe `plano-abas--js` e
alterna os painéis. A vista 3D também mostra o volume total por categoria
(Móveis, Eletrodomésticos, Caixas e volumes, Outros), com peso e quantidade.

O plano é uma **referência de carregamento**, não uma promessa de encaixe
milimétrico. A conferência final é no local.

### Viagens quando a carga não cabe

`planejarViagens` reaproveita o packer em cada leva, em vez de estimar por
volume:

1. Unidades maiores que a carroceria viram `impossiveis`, com o motivo escrito.
2. Cada viagem recebe o que o packer consegue colocar no veículo vazio.
3. O que sobrou vira a carga da viagem seguinte.
4. O processo para em **12 viagens**. Se ainda sobrar carga, ela aparece em
   `impossiveis` com o motivo “passa do limite de 12 viagens”.
5. O resultado informa `cabeEmUma`, `viavel`, `totalViagens`, a lista de itens
   por viagem e o motivo em texto.

Uma carga só é `viavel` quando todas as unidades entram em uma ou mais viagens.

### Recomendação de veículo

`sugerirVeiculoParaPlano` percorre os veículos na ordem de tamanho e devolve o
primeiro em que:

- `analisarEncaixe` não é `nao_cabe`; e
- `montarPlanoCarga` termina com `fora === 0`.

Isso evita recomendar um veículo que tem volume sobrando mas deixa volumes fora
por causa de comprimento/largura. Se nenhum veículo padrão acomodar tudo, a UI
usa a carreta como fallback e `planejarViagens` mostra quantas viagens serão
necessárias e o que não entra de jeito nenhum.

### Veículos do sistema

| ID | Nome | Comprimento | Largura | Altura | Capacidade |
| --- | --- | ---: | ---: | ---: | ---: |
| `fiorino` | Fiorino | 145 cm | 130 cm | 120 cm | 650 kg |
| `van` | Van | 260 cm | 160 cm | 150 cm | 1.500 kg |
| `tres_quartos` | Caminhão 3/4 | 350 cm | 190 cm | 180 cm | 3.000 kg |
| `bau` | Caminhão baú | 600 cm | 230 cm | 240 cm | 8.000 kg |
| `carreta` | Carreta | 1.400 cm | 240 cm | 270 cm | 25.000 kg |
| `outro` | Outro/personalizado | medidas do perfil | | | |

**Kombi:** não é um tipo selecionável no cadastro de caminhão e não aparece mais
na home. Quem usa Kombi deve cadastrá-la como `van` ou `outro`.

## 12. Validação, segurança e limites

- Todos os formulários passam por Zod (`src/lib/validate.ts`).
- Mutações passam por verificação de origem (CSRF).
- Login/cadastro: 10 tentativas por 10 minutos por IP/rota (em memória).
- APIs de rota e IA: 40 requisições por minuto por IP (em memória).
- Senhas nunca são logadas; `audit_log` guarda ação, entidade e metadados sem
  segredo.
- Prepared statements com `bind` em todas as queries.
- CSP fechada em `'self'`; o JS do cliente não fala com APIs externas.
- O rate limit em memória é best-effort; a proteção forte deve ser configurada
  na borda da Cloudflare.

## 13. Prazo do leilão ✅

### Como funciona

1. No formulário de publicação, o cliente escolhe **24, 48 ou 72 horas** para
   receber propostas. O padrão é 48 horas.
2. `calcularPrazos` grava `encerra_em` e `prazo_escolha_em` em UTC.
3. Enquanto `situacao = 'aberto'` e `agora < encerra_em`, os transportadores
   podem enviar ou melhorar propostas.
4. `aplicarPrazos` fecha o recebimento, na primeira requisição após o horário ou
   pelo cron:
   - com propostas → `em_escolha`;
   - sem propostas → `sem_lances`.
5. Em `em_escolha`, o cliente tem **48 horas** para aceitar uma proposta:
   - aceitou → `encerrado` + vencedor;
   - não aceitou → `expirado`.
6. O cliente pode cancelar em `aberto` ou `em_escolha`; o cancelamento avisa
   todos que propuseram.
7. Mudanças em `aberto` aparecem na listagem e no sitemap. `em_escolha`
   e `encerrado` continuam acessíveis pelo link direto; `sem_lances`,
   `expirado` e `cancelado` retornam 404 para quem não é dono.
8. As telas mostram o prazo em texto e o status escrito, sem depender de cor.
9. A rotina é idempotente e roda no cron a cada 5 minutos, além das rotas.

### Impacto técnico aplicado

- migração `0007_prazo_leilao.sql` com `situacao`, `encerra_em` e
  `prazo_escolha_em` (mais backfill e índice);
- `src/lib/prazos.ts` centraliza o relógio, os rótulos e a visibilidade;
- `validarLance` compara o horário atual com `encerra_em`;
- `podeAceitarLance` e `podeCancelarFrete` usam `situacao`;
- cron e rotas chamam `aplicarPrazos`;
- testes em `test/rules.test.ts`, `test/prazos.test.ts` e
  `test/montagem.test.ts`.

## 14. Privacidade e o que é público

- A listagem pública mostra descrição, categoria, cidade/UF, data, distância,
  prazo de propostas, quantidade de propostas e menor valor.
- O detalhe público mostra rua, bairro e CEP de origem e destino.
- O dono vê todas as propostas e os nomes dos transportadores.
- O transportador vê apenas a própria proposta.
- O simulador de custo e os dados do caminhão são exclusivos do transportador.
- O e-mail de clientes nunca aparece em listagens públicas.
- O `audit_log` não guarda senhas nem tokens.
- Mudanças `sem_lances`, `expiradas` ou `canceladas` não são indexadas nem
  abertas por visitantes; nem o sitemap as lista.

**Ponto em aberto:** o detalhe mostra o endereço completo para qualquer visitante.
Se isso for um problema, a regra futura pode esconder rua/número até o aceite e
mostrar apenas cidade/UF na vitrine. Isso ainda não está implementado.

## 15. Linguagem e posicionamento

- Usar **“transportador”** para a pessoa; **“caminhão”** para o veículo.
- Evitar “caminhoneiro” na interface.
- A home pública fala de **benefícios ao cliente** e não usa “leilão”,
  “marketplace” ou “quem decide o preço”.
- O bloco “Você vê o custo. Você decide o preço.” pertence à área do
  transportador, não à home.
- Não usar prova social inventada, número de clientes fictício ou depoimento
  falso.
- Não prometer prazo, seguro, rastreamento ou pagamento pelo site se o recurso
  não existe.
