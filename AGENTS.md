# Instruções para quem trabalha neste repositório

## Documentação é parte da entrega

Toda mudança que altere comportamento, regra, schema, rota, cálculo, texto
público ou operação deve atualizar a documentação **no mesmo commit**. Não deixe
para depois.

- Regra de negócio → `docs/regras-de-negocio.md`
- Tela ou recurso → `docs/funcionalidades.md`
- Tabela, coluna ou migração → `docs/banco-de-dados.md`
- Rota, API ou parâmetro → `docs/rotas-e-api.md`
- Binding, segredo, deploy ou cron → `docs/operacao-e-deploy.md`
- Decisão de produto, lacuna ou pendência → `docs/decisoes-e-pendencias.md`
- Visão geral ou estado do projeto → `README.md` e `docs/README.md`

Se a mudança for só de estilo e não alterar comportamento, não é obrigatório
atualizar os documentos funcionais, mas o índice deve continuar correto.

## Layout simples para pessoas leigas

O site precisa ser entendido por quem tem pouca familiaridade com tecnologia e
por quem lê pouco. Isso vale para **todas** as telas:

- uma ação principal por bloco, com botão grande e texto curto;
- ícone ou desenho ao lado da palavra, nunca no lugar da instrução;
- passos numerados e listas curtas;
- status escrito, não só cor;
- contraste alto, foco visível e área de toque confortável;
- frases diretas, sem jargão de logística na área do cliente;
- respeitar `prefers-reduced-motion`;
- testar o texto pensando em quem nunca usou um site de mudança.

## Vocabulário

- **transportador** é a pessoa; **caminhão** é o veículo.
- Não use “caminhoneiro” na interface.
- Na área do cliente, evite “leilão”, “marketplace”, “lance” e “packer”.
- “Você vê o custo. Você decide o preço.” é exclusivo da área do transportador.
- Não invente prova social, prazo, seguro, rastreamento ou pagamento.

## Antes de commitar

```bash
npm run typecheck
node --check public/js/app.js
npm test
```

Confira também se o README continua apontando para o estado real do projeto.

## Privacidade e segurança

- Nunca registre senha, token, cookie ou conteúdo de e-mail de cliente.
- CSP fechada em `'self'`; não adicione CDN ou API externa no cliente sem
  decisão explícita.
- Toda consulta usa SQL preparado com `bind`.
- Rotas de mutação passam por verificação de origem.

## Pendências

Antes de prometer algo na interface, confira
`docs/decisoes-e-pendencias.md`. Kombi como tipo próprio de veículo (hoje
cadastrada como van ou outro), endereço público e os e-mails de fim de prazo
ainda estão em aberto.

## REGRA ZERO
Leia GUIA-ESTILO.md antes de qualquer mudanca visual ou de copy. Nao regredir o que la esta marcado como pronto.

## Diretrizes específicas para o DeepSeek V4.1 Flash (OpenCode)

Esta seção complementa as regras acima. Em caso de conflito, **as regras deste
repositório (documentação, vocabulário, UX para leigos, privacidade e REGRA ZERO)
prevalecem**. Aqui só se define *como* o Flash deve se comportar para não
alucinar e não desperdiçar tokens.

### 1. Economia extrema de tokens
- Responda diretamente com código, comando ou diff. Sem preâmbulo, sem resumo
  do que foi pedido, sem repetir a pergunta.
- Se precisar explicar, no máximo 2 frases. Se a explicação passar disso, é
  sinal de que a tarefa deveria ter sido decomposta.
- Proibido gerar "código de exemplo", "versão alternativa" ou `TODO` que
  ninguém pediu.

### 2. Proibido adivinhar
- Antes de editar qualquer arquivo, use `grep`/`rg` para localizar e `read`
  para ler o trecho real. Não presumir estrutura pelo nome do arquivo.
- Em arquivos com mais de ~500 linhas, localize com `grep` e leia só o trecho
  relevante. Proibido ler arquivo grande inteiro.
- Se não tiver 100% de certeza sobre sintaxe, API ou regra de negócio, **pare**
  e escreva: `[Requer Intervenção Humana] Razão: ...`. Não tente adivinhar.

### 3. Ordem obrigatória de ferramentas (para mudança de código)
1. **Localizar** — `rg`/`grep` para achar função/classe/rota.
2. **Ler** — `read` no arquivo e trecho exatos.
3. **Editar** — `edit` preciso, seguindo o estilo do arquivo.
4. **Verificar** — rodar o check correspondente (ver "Antes de commitar").

### 4. Documentação no mesmo commit (reforço da regra do repositório)
- Toda mudança de comportamento, regra, schema, rota, cálculo, texto público ou
  operação **deve** atualizar o doc correspondente no mesmo commit, conforme a
  tabela do topo deste arquivo.
- Se a mudança for só de estilo e não alterar comportamento, não é obrigatório
  atualizar docs funcionais, mas o índice (`README.md` / `docs/README.md`) deve
  continuar correto.
- Antes de prometer algo na interface, confira `docs/decisoes-e-pendencias.md`.

### 5. UX e copy (não regredir)
- Respeitar integralmente a seção "Layout simples para pessoas leigas" e a
  `REGRA ZERO` (`GUIA-ESTILO.md`). O Flash **não** deve "melhorar" copy por
  conta própria.
- Vocabulário: **transportador** é a pessoa, **caminhão** é o veículo. Nunca
  usar "caminhoneiro" na interface. Na área do cliente, evitar "leilão",
  "marketplace", "lance" e "packer". "Você vê o custo. Você decide o preço." é
  exclusivo da área do transportador.
- Não inventar prova social, prazo, seguro, rastreamento ou pagamento.

### 6. Privacidade e segurança (reforço)
- Nunca registrar senha, token, cookie ou conteúdo de e-mail de cliente.
- CSP fechada em `'self'`; não adicionar CDN ou API externa no cliente sem
  decisão explícita.
- Toda consulta usa SQL preparado com `bind`.
- Rotas de mutação passam por verificação de origem.
- Se a tarefa pedir algo que viole qualquer item acima, **recusar e apontar a
  regra** em vez de executar.

### 7. Verificação antes de encerrar a tarefa
Antes de dizer que terminou, rodar (quando aplicável ao que mudou):
```bash
npm run typecheck
node --check public/js/app.js
npm test