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

- Todo frete nasce com `status = 'aberto'`.
- **Não existe prazo de encerramento automático hoje.** O frete fica aberto até
  o dono aceitar uma proposta ou cancelar. Veja a proposta de prazo na seção 13
  e em [decisoes-e-pendencias.md](decisoes-e-pendencias.md).

## 4. Propostas (leilão reverso) ✅

Rota: `POST /fretes/:id/lances` (somente `transportador` logado).

### Regras aplicadas

`src/lib/rules.ts` → `validarLance`:

1. O dono do frete não pode propor na própria mudança.
2. Só `transportador` pode propor.
3. O frete precisa estar `aberto`.
4. O valor precisa ser maior que zero.
5. Se o cliente definiu `preco_maximo`, a proposta não pode passar dele.
6. A proposta precisa ser **estritamente menor** que o menor orçamento dos
   outros transportadores. Se ainda não há proposta de outro, qualquer valor
   positivo dentro do teto vale.
7. Cada transportador tem **uma proposta por frete**. Enviar de novo atualiza a
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
- somente com `status = 'aberto'`;
- a proposta precisa pertencer ao frete.

Efeitos:

1. `bids.vencedor = 1` na proposta escolhida.
2. `freight.status = 'encerrado'`.
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
- somente com `status = 'aberto'`.

Efeitos:

- `freight.status = 'cancelado'`;
- alerta `frete_cancelado` para todos os transportadores que enviaram proposta;
- registro em `audit_log`.

Não há multa, taxa ou penalidade. Depois de encerrado ou cancelado, o frete não
aceita novas propostas nem novo aceite.

## 7. Estados do frete

```text
                 aceite do dono
  aberto ─────────────────────────► encerrado
     │
     │ cancelamento do dono
     └────────────────────────────► cancelado
```

| Estado | Recebe propostas? | Aceita proposta? | Pode cancelar? |
| --- | --- | --- | --- |
| `aberto` | ✅ | ✅ | ✅ |
| `encerrado` | ❌ | ❌ | ❌ |
| `cancelado` | ❌ | ❌ | ❌ |

**Lacuna atual:** não há estados `sem_lances`, `em_escolha` ou `expirado`, nem
prazo automático. A seção 13 propõe o fluxo com prazo.

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

## 11. Carga, plano e veículo ✅/🟡

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
7. A conferência item a item mostra `total`, `colocados` e `fora` por tipo.

O desenho tem duas vistas:

- **corte lateral** da faixa 0 (a primeira faixa de largura);
- **vista de cima** proporcional ao piso, com o footprint de todos os blocos.

O plano é uma **referência de carregamento**, não uma promessa de encaixe
milimétrico. A conferência final é no local.

### Recomendação de veículo

`sugerirVeiculoParaPlano` percorre os veículos na ordem de tamanho e devolve o
primeiro em que:

- `analisarEncaixe` não é `nao_cabe`; e
- `montarPlanoCarga` termina com `fora === 0`.

Isso evita recomendar um veículo que tem volume sobrando mas deixa volumes fora
por causa de comprimento/largura. Se nenhum veículo padrão acomodar tudo, a UI
usa a carreta como fallback e avisa que podem ser necessárias duas viagens.

### Veículos do sistema

| ID | Nome | Comprimento | Largura | Altura | Capacidade |
| --- | --- | ---: | ---: | ---: | ---: |
| `fiorino` | Fiorino | 145 cm | 130 cm | 120 cm | 650 kg |
| `van` | Van | 260 cm | 160 cm | 150 cm | 1.500 kg |
| `tres_quartos` | Caminhão 3/4 | 350 cm | 190 cm | 180 cm | 3.000 kg |
| `bau` | Caminhão baú | 600 cm | 230 cm | 240 cm | 8.000 kg |
| `carreta` | Carreta | 1.400 cm | 240 cm | 270 cm | 25.000 kg |
| `outro` | Outro/personalizado | medidas do perfil | | | |

**Kombi:** aparece como referência visual na home, mas não é um tipo selecionável
no cadastro de caminhão. Hoje ela deve ser cadastrada como `van` ou `outro`.
Isso está registrado como pendência de consistência.

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

## 13. Prazo do leilão — lacuna e proposta ⏳

### Como é hoje

Não há campo de prazo. O frete nasce `aberto` e permanece assim até:

- o dono aceitar uma proposta → `encerrado`; ou
- o dono cancelar → `cancelado`.

Consequências:

- propostas podem chegar a qualquer momento, inclusive dias depois;
- não há “fechamento sem lances”;
- não há prazo para o cliente escolher depois que o recebimento termina;
- o cliente pode deixar o frete aberto indefinidamente.

### Proposta de regra

1. No formulário de publicação, o cliente escolhe por quanto tempo quer receber
   propostas (ex.: 24 h, 48 h, 72 h ou data/hora personalizada).
2. O frete guarda `encerra_em` (timestamp UTC).
3. Enquanto `agora < encerra_em` e `status = 'aberto'`, os transportadores podem
   enviar/melhorar propostas.
4. Ao chegar `encerra_em`, o cron (ou a primeira requisição após o horário)
   fecha o recebimento:
   - com propostas → `em_escolha`;
   - sem propostas → `sem_lances`.
5. Em `em_escolha`, o cliente tem um prazo (ex.: 48 h) para aceitar uma proposta.
   - aceitou → `encerrado` + vencedor;
   - não aceitou até o prazo → `expirado` (ou reabrir, se o cliente quiser).
6. O cliente pode cancelar enquanto estiver `aberto` ou `em_escolha`; o
   cancelamento avisa todos que propuseram.
7. A UI mostra contagem regressiva e o prazo em todos os lugares relevantes.
8. E-mails/alertas: “recebimento encerrado”, “você tem até X para escolher”,
   “frete expirado”.

### Impacto técnico

- migração para `freight.encerra_em` e `freight.prazo_escolha_em`;
- novos status (`em_escolha`, `sem_lances`, `expirado`) e atualização de
  `StatusFrete`, checks do D1 e rótulos;
- regra `validarLance` precisa comparar o horário atual com `encerra_em`;
- cron precisa fechar fretes vencidos;
- testes de regra e de transição de estado;
- textos e telas de listagem/detalhe/painel.

Enquanto essa regra não for implementada, **não prometa prazo automático na
interface**. A home e o detalhe devem continuar dizendo que o cliente escolhe
com calma e que o frete fica aberto até o aceite/cancelamento.

## 14. Privacidade e o que é público

- A listagem pública mostra descrição, categoria, cidade/UF, data, distância,
  quantidade de propostas e menor valor.
- O detalhe público mostra rua, bairro e CEP de origem e destino.
- O dono vê todas as propostas e os nomes dos transportadores.
- O transportador vê apenas a própria proposta.
- O simulador de custo e os dados do caminhão são exclusivos do transportador.
- O e-mail de clientes nunca aparece em listagens públicas.
- O `audit_log` não guarda senhas nem tokens.

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
