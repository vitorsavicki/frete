# Documentação do Quero Mudar Já

Este diretório é a fonte de verdade sobre **o que o site faz**, **quais são as
regras** e **como operar**. O código e os testes continuam sendo a verdade
final sobre o comportamento, mas nenhuma mudança deve ficar sem documentação.

## Regra de manutenção

Toda alteração que mude uma destas coisas precisa atualizar a documentação no
**mesmo commit**:

- regra de negócio (quem pode fazer o quê, quando, com qual limite);
- fluxo do usuário (nova tela, novo passo, nova ação);
- schema, migração ou índice do D1;
- rota HTTP, API interna ou parâmetro;
- variável de ambiente, binding, segredo ou cron;
- cálculo de preço, plano de carga ou recomendação de veículo;
- texto público que explique o produto;
- processo de deploy, build do APK ou configuração de e-mail.

Se a mudança for só de estilo e não alterar comportamento, não é obrigatório
atualizar os documentos funcionais — mas o README e este índice devem continuar
corretos.

## Documentos

| Documento | Use quando precisar de... |
| --- | --- |
| [arquitetura.md](arquitetura.md) | entender como o Worker, o D1 e os serviços externos se conversam |
| [regras-de-negocio.md](regras-de-negocio.md) | saber quem pode fazer o quê e quais são os limites |
| [banco-de-dados.md](banco-de-dados.md) | conferir tabelas, colunas, relações e migrações |
| [rotas-e-api.md](rotas-e-api.md) | encontrar uma rota, método, autenticação ou cache |
| [funcionalidades.md](funcionalidades.md) | ver o catálogo de telas e recursos, com status |
| [operacao-e-deploy.md](operacao-e-deploy.md) | rodar, publicar, configurar segredos e resolver problemas |
| [decisoes-e-pendencias.md](decisoes-e-pendencias.md) | entender escolhas de produto, lacunas e próximos passos |
| [email.md](email.md) | configurar Cloudflare Email Sending ou Resend |
| [../SECURITY.md](../SECURITY.md) | política de segurança e proteções |

## Checklist de “pronto”

Antes de considerar uma entrega concluída:

- [ ] o comportamento novo está descrito em `funcionalidades.md`;
- [ ] se há regra nova, ela está em `regras-de-negocio.md`;
- [ ] se há tabela/coluna nova, ela está em `banco-de-dados.md` e em uma
      migração;
- [ ] se há rota nova, ela está em `rotas-e-api.md`;
- [ ] se há segredo/binding novo, ele está no README e em
      `operacao-e-deploy.md`;
- [ ] se a decisão for polêmica ou tiver prazo, ela está em
      `decisoes-e-pendencias.md`;
- [ ] `npm run typecheck`, `node --check public/js/app.js` e `npm test` passam;
- [ ] o README continua apontando para o estado real do projeto.

## Convenções

- **Idioma:** português do Brasil, com linguagem simples. Termos técnicos só
  quando necessários.
- **Datas:** ISO-8601 (`AAAA-MM-DD`) para dados; textos podem usar o formato
  brasileiro.
- **Valores:** R$ com duas casas quando for dinheiro; peso em kg; volume em m³;
  medidas em cm no código e metros na interface.
- **Papéis:** `cliente`, `transportador` e `admin`. “Caminhoneiro” não é usado
  na interface; o veículo continua sendo “caminhão”.
- **Segredos:** nunca documente chaves, tokens, senhas ou conteúdo de e-mail de
  clientes. Use nomes de variáveis e exemplos genéricos.
