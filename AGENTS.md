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
`docs/decisoes-e-pendencias.md`. Prazo automático do leilão, Kombi como tipo de
veículo e endereço público ainda estão em aberto.
