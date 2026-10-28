# Decisões e pendências

Este documento registra **por que** algumas escolhas foram feitas e **o que
ainda falta**. Ele não substitui as regras: quando uma pendência for
implementada, a regra nova deve ir para [regras-de-negocio.md](regras-de-negocio.md)
e o item deve ser atualizado ou removido daqui.

> Legenda: **✅ decidido** · **🟡 em andamento** · **⏳ pendente** · **🚫 adiado**

## 1. Redesign e primeiro acesso ✅

### O que foi decidido

- O layout precisa ser entendido por quem tem pouca familiaridade com tecnologia
  e por quem lê pouco. Isso vale para todas as telas, não só para a home.
- O mascote é uma **caixa de mudança com boné e luvas**. Ele aparece no tour de
  primeiro acesso e fica disponível na ajuda fixa.
- O tour abre sozinho **apenas na primeira visita à home**. Depois fica salvo em
  `localStorage` (`qm-tour-v1`).
- O tour pode ser revisto pelo botão **“Rever o passo a passo”** na ajuda fixa.
- A ajuda fixa fica no canto da tela e não cobre os botões principais no celular.
- Os veículos usam **imagens 3D do Microsoft Fluent Emoji** (licença MIT),
  copiadas para `public/img/veiculos/`. A troca de desenhos por imagens foi
  pedida para deixar a vitrine mais concreta. O crédito fica em
  `public/img/veiculos/CREDITOS.md`. Nada é carregado de CDN: a CSP continua
  `'self'`.
- O plano de carga mostra **todas as faixas**, com vista de cima proporcional,
  **vista 3D isométrica**, volume por categoria e uma lista explícita do que
  ficou fora.
- As três vistas ficam em **abas (3D, Lado, Cima)**. Sem JavaScript, os três
  painéis aparecem abertos; o cliente só alterna quando o JS está ativo.
- A recomendação de veículo usa o **packer** (`sugerirVeiculoParaPlano`), não só
  a conta de volume/peso, e respeita a capacidade de peso.
- Quando a carga não cabe de uma vez, `planejarViagens` calcula **quantas
  viagens** são necessárias, o que vai em cada uma e o que não entra em nenhuma.
- O bloco **“Você vê o custo. Você decide o preço.”** pertence só à área do
  transportador. A home fala de benefícios ao cliente.
- “Caminhoneiro” foi substituído por **“transportador”** na interface.
  “Caminhão” continua sendo o veículo.
- Não há prova social inventada. Nada de número de clientes, depoimento falso ou
  selo de garantia que o site não oferece.
- A home pública não usa “leilão”, “marketplace” nem “quem decide o preço”.

### Diretriz de linguagem e acessibilidade ✅

Toda tela nova segue este padrão:

1. uma ação principal por bloco;
2. botão grande, com texto curto e verbo no começo;
3. ícone ou desenho **ao lado** da palavra, nunca no lugar da instrução;
4. passos numerados e listas curtas;
5. status escrito, não só cor;
6. contraste alto, foco visível e área de toque confortável;
7. frases diretas, sem jargão de logística na área do cliente;
8. respeitar `prefers-reduced-motion` nas animações.

### Validação 🟡

O redesign foi publicado em produção e as telas principais foram conferidas em
Edge headless, em desktop (1440 px) e celular (390 px). Ainda faltam alguns
cenários antes de considerar a validação completa:

- [x] tour no primeiro acesso e botão de rever (testado em 1440 px e 390 px);
- [x] ajuda fixa abrindo, fechando e sem cobrir o CTA no celular;
- [ ] home em 360 px e 768 px (já conferida em 390 px e 1440 px);
- [x] `/fretes/3` com baú, vista de cima e lista completa dos volumes;
- [ ] cenário com volumes fora visível na tela (o cálculo é coberto por teste);
- [ ] leitura dos textos por alguém de fora do projeto.

## 2. ARLA fora da interface ✅

O ARLA é um insumo real de caminhões diesel, mas estava poluindo o formulário e a
estimativa com um termo técnico. A decisão foi:

- remover o campo do perfil do caminhão;
- remover a linha da estimativa;
- manter o custo **embutido no cálculo interno**;
- mostrar o valor somado em **“Manutenção e insumos”**;
- manter `arla_por_km` no banco e o padrão de R$ 0,06/km para perfis novos;
- perfis antigos continuam usando o valor salvo; o campo só não é mais editável;
- na gasolina, o cálculo zera o ARLA.

O cliente nunca vê custo. O transportador vê o total, sem precisar entender o
nome do insumo.

## 3. Prazo do leilão ✅

### Decisão

O leilão tem **dois relógios**:

1. **Recebimento de propostas** — o cliente escolhe 24, 48 ou 72 horas no
   formulário (padrão 48). O fim fica em `freight.encerra_em`.
2. **Escolha do cliente** — 48 horas depois do fim do recebimento. O fim fica
   em `freight.prazo_escolha_em`.

Transições:

- fim do recebimento **com propostas** → `em_escolha`;
- fim do recebimento **sem propostas** → `sem_lances`;
- aceite do dono em `em_escolha` → `encerrado` com vencedor;
- fim da escolha sem aceite → `expirado`;
- cancelamento do dono em `aberto` ou `em_escolha` → `cancelado`.

### Por que dois momentos

- O transportador precisa de um prazo claro para investir tempo na proposta.
- O cliente precisa de tempo para comparar depois que as propostas param de
  mudar. Aceitar no meio do recebimento premiava quem chegasse por último.
- Sem propostas, o cliente não fica com um frete aberto para sempre: ele sai das
  listagens e pode ser republicado.

### O que foi aplicado

- migração `0007_prazo_leilao.sql` com `situacao`, `encerra_em` e
  `prazo_escolha_em`;
- `src/lib/prazos.ts` com cálculo, rótulos e `aplicarPrazos`;
- `validarLance`, `podeAceitarLance` e `podeCancelarFrete` usando `situacao` e o
  relógio;
- cron a cada 5 minutos, além das rotas;
- telas com prazo escrito e status sem depender de cor;
- testes de regra, prazo e viagens.

### O que não existe ainda

- e-mail específico de “recebimento encerrado” e “prazo de escolha acabando”;
- contagem regressiva em tempo real (o prazo é mostrado como data e hora);
- republicar com um clique (hoje é preciso criar uma mudança nova).

## 4. Kombi na home, mas não no cadastro ⏳

A home mostra a Kombi como opção visual, porque ela é comum em mudanças
pequenas. O cadastro de veículo, porém, aceita apenas:

`fiorino`, `van`, `tres_quartos`, `bau`, `carreta` e `outro`.

Hoje a Kombi precisa ser cadastrada como `van` ou `outro`. Isso é uma
inconsistência entre a vitrine e o formulário.

Opções:

- **A.** remover a Kombi da home e ficar só com os tipos reais;
- **B.** criar o tipo `kombi`, com medidas típicas, rótulo e custo de referência;
- **C.** manter a ilustração, mas explicar que ela entra como van.

Recomendação: **B**, se a Kombi for relevante para o público; caso contrário,
**A** é mais honesto. Não deixar a vitrine prometer um tipo que o formulário não
aceita.

## 5. Endereço público ⏳

O detalhe do frete mostra rua, bairro e CEP de origem e destino para qualquer
visitante. Isso ajuda o transportador a calcular a rota, mas expõe o endereço
antes de existir um combinado.

Opções:

- manter como está, porque a rua é necessária para a proposta;
- mostrar só cidade/UF e bairro na vitrine, liberando a rua depois do aceite;
- mostrar a rua apenas para transportador logado.

Ainda não há decisão. Se mudar, atualizar a seção de privacidade em
[regras-de-negocio.md](regras-de-negocio.md) e a tela de detalhe.

## 6. E-mail e domínio 🚫 adiado

O envio real de e-mail para qualquer destinatário depende de um domínio próprio
verificado no Cloudflare/Resend. O remetente de teste `onboarding@resend.dev`
não serve para produção.

Enquanto o domínio não estiver configurado:

- os e-mails continuam indo para a fila `email_outbox` ou para o log;
- o cadastro, a proposta e o aceite funcionam sem e-mail;
- o status aparece em `/api/email/status`;
- a configuração detalhada fica em [email.md](email.md).

O usuário decidiu adiar esta parte. Não é bloqueio para o redesign nem para as
regras de prazo.

## 7. Decisões menores já tomadas ✅

| Tema | Decisão |
| --- | --- |
| Nome do mascote | “Zé Mudança” no tour e na ajuda |
| Tour automático | só na home e só na primeira visita |
| Memória do tour | `localStorage`, chave `qm-tour-v1` |
| Tour sem JavaScript | conteúdo renderizado no servidor; JS só abre, fecha e avança |
| Movimento reduzido | animações desligadas com `prefers-reduced-motion` |
| Fallback de veículo | carreta, com o número de viagens calculado pelo packer |
| Plano de carga | referência de carregamento, não promessa de encaixe milimétrico |
| Plano 3D | isométrico, gerado dos mesmos blocos do packer |
| Abas do plano | 3D, Lado e Cima; abertas sem JavaScript |
| Custo do transportador | visível só para transportador logado |
| ARLA | fora da interface, dentro do cálculo |
| Copy da home | benefícios ao cliente, sem “leilão” ou “marketplace” |
| Prova social | nenhuma inventada |
| CSP | fechada em `'self'`; sem CDN ou API externa no cliente |
| Domínio/e-mail | adiado; ver seção 6 |

## 8. Próximos passos sugeridos

1. Completar a validação do redesign em 360 px, 768 px e com leitores de fora.
2. Resolver a Kombi (tipo real ou remover da home).
3. Decidir a privacidade do endereço.
4. Configurar domínio e e-mail quando fizer sentido para o negócio.
5. Criar e-mails de “recebimento encerrado” e “prazo de escolha acabando”.
6. Avaliar um botão de republicar a partir de `sem_lances`/`expirado`.
