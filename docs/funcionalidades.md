# Funcionalidades

Catálogo do que existe hoje, em linguagem simples. Use este documento para
saber **o que a pessoa consegue fazer** — as regras detalhadas ficam em
[regras-de-negocio.md](regras-de-negocio.md).

> Legenda: **✅ pronto** · **🟡 em andamento** · **⏳ planejado**

## Primeiro acesso e ajuda

| Recurso | Status | O que faz |
| --- | --- | --- |
| Tour do Zé Mudança | ✅ | Na primeira visita à home, abre um passo a passo animado com lista, veículo, propostas e encontro direto; o convite final muda conforme o papel |
| Rever o passo a passo | ✅ | Botão na ajuda fixa reabre o tour a qualquer momento |
| Ajuda fixa | ✅ | Mascote no canto da tela abre um painel com atalhos, texto e a opção de rever o tour conforme o papel |
| Memória do tour | ✅ | O tour não abre sozinho de novo depois que a pessoa fecha; fica salvo em `localStorage` (`qm-tour-v1`) |
| Funciona sem JavaScript | ✅ | O conteúdo do tour e da ajuda é renderizado no servidor; o JS só abre, fecha e avança |
| Respeita movimento reduzido | ✅ | As animações param com `prefers-reduced-motion` |

## Home pública

| Recurso | Status | O que faz |
| --- | --- | --- |
| Hero do cliente | ✅ | Explica em uma frase o que o site faz e leva para “Publicar minha mudança” |
| Como funciona em 3 passos | ✅ | Lista → plano de carga → propostas e combinado direto |
| Dois papéis | ✅ | Separa “quem vai mudar” de “quem transporta”, com ações diferentes |
| Categorias | ✅ | Atalhos para as categorias de mudança |
| Itens em destaque | ✅ | Mostra exemplos do catálogo com peso e medidas |
| Seis veículos ilustrados | ✅ | Fiorino, Kombi, van, 3/4, baú e carreta com desenhos próprios |
| Faixa do transportador | ✅ | Convite discreto para quem tem veículo; não mistura com a copy do cliente |
| App Android | ✅ | Link para a página do APK |
| Perguntas frequentes | ✅ | Dúvidas sobre preço, propostas, plano, pagamento e transportadores |
| CTA final | ✅ | Repete a ação principal com o mascote |

## Conta e acesso

| Recurso | Status | O que faz |
| --- | --- | --- |
| Cadastro de cliente | ✅ | Nome, e-mail, senha e papel |
| Cadastro de transportador | ✅ | Pede CPF/CNPJ válido |
| Login local | ✅ | E-mail e senha |
| Login com Google | ✅ (se configurado) | OAuth com e-mail verificado |
| Sair | ✅ | Apaga a sessão no banco e o cookie |
| Recuperar senha | ⏳ | Ainda não existe |
| Editar perfil | ⏳ | Ainda não existe tela para trocar nome, telefone ou senha |

## Publicar uma mudança

| Recurso | Status | O que faz |
| --- | --- | --- |
| Formulário em etapas | ✅ | Categoria, descrição, endereços, data, ajudantes e carga |
| Busca de CEP | ✅ | Preenche rua, bairro, cidade e UF |
| Mapa da rota | ✅ | Mostra origem, destino e distância aproximada |
| Lista de itens | ✅ | Móveis, eletrodomésticos, caixas e itens especiais com peso e medidas |
| Caixas personalizadas | ✅ | Pequena, média, grande ou medida informada |
| Cálculo ao vivo | ✅ | Peso, volume e veículo recomendado enquanto a lista muda |
| Plano de carga | ✅ | Corte lateral + vista de cima proporcional + lista de volumes |
| Itens fora do plano | ✅ | Lista explícita do que não coube, com contagem por item |
| Preço máximo | ✅ | Teto opcional que limita as propostas |
| IA para descrição | ✅ (se configurada) | Melhora o texto antes de publicar |

## Receber propostas

| Recurso | Status | O que faz |
| --- | --- | --- |
| Listagem pública | ✅ | Filtros por categoria, UF, cidade, texto e data |
| Detalhe do frete | ✅ | Rota, carga, plano, distância e prazo de retirada |
| Proposta do transportador | ✅ | Valor, mensagem e melhoria enquanto o frete está aberto |
| Menor proposta em destaque | ✅ | O card mostra quantidade e menor valor |
| Propostas visíveis ao dono | ✅ | Tabela com nome do transportador e mensagem |
| Aceite | ✅ | Dono escolhe qualquer proposta e encerra o frete |
| Cancelamento | ✅ | Dono cancela frete aberto e avisa quem propôs |
| Alertas no sino | ✅ | Novo orçamento, superado, vencedor, perdedor e cancelamento |
| Prazo automático | ⏳ | Proposta em [decisoes-e-pendencias.md](decisoes-e-pendencias.md) |

## Perfil do transportador e custos

| Recurso | Status | O que faz |
| --- | --- | --- |
| Medidas internas | ✅ | Comprimento, largura, altura e capacidade |
| Medidas típicas | ✅ | Preenche o formulário a partir do tipo escolhido |
| Volume calculado | ✅ | Mostra o espaço interno em m³ |
| Combustível | ✅ | Diesel ou gasolina e preço por litro |
| Custos por km | ✅ | Pedágio e manutenção |
| Custos do serviço | ✅ | Taxa de saída, horas, hora e ajudante |
| Margem | ✅ | Percentual desejado |
| Referências do banco | ✅ | Preços e taxas podem mudar sem deploy |
| Simulador no frete | ✅ | Mostra custo, preço mínimo e faixa de mercado |
| “Você vê o custo. Você decide o preço.” | ✅ | Bloco exclusivo da área do transportador |
| Análise de carga com IA | ✅ (se configurada) | Texto de apoio; não substitui o encaixe |

## Carga, plano e veículo

| Recurso | Status | O que faz |
| --- | --- | --- |
| Catálogo com mais de 60 itens | ✅ | Peso, medidas, fragilidade e empilhamento |
| Resumo da carga | ✅ | Quantidade, peso, volume e altura máxima |
| Encaixe no veículo | ✅ | `cabe`, `limite` ou `nao_cabe`, com motivos |
| Plano por faixas de largura | ✅ | Preenche o piso antes de empilhar |
| Corte lateral | ✅ | Mostra a faixa da frente, alturas e frágeis |
| Vista de cima real | ✅ | Retângulos proporcionais ao piso, com todas as faixas |
| Lista de conferência | ✅ | Todos os itens, com “No plano” ou “N fora” |
| Recomendação pelo packer | ✅ | Escolhe o menor veículo que não deixa volumes fora |
| Fallback para carreta | ✅ | Quando nenhum veículo padrão acomoda tudo, usa a carreta e avisa |
| Duas viagens | ✅ (texto) | Aviso quando nem a carreta padrão resolve |

## App Android

| Recurso | Status | O que faz |
| --- | --- | --- |
| Página `/app` | ✅ | Explica o app e oferece o APK |
| Download direto | ✅ | Arquivo versionado em `public/download/` |
| WebView | ✅ | Usa as mesmas páginas e a mesma conta do site |
| Notificações push | ⏳ | Ainda não existem |
| Publicação em loja | ⏳ | Distribuição só por APK |

## Linguagem e acessibilidade

O layout deve ser entendido por quem tem pouca familiaridade com tecnologia —
e também por quem lê pouco:

- uma ação principal por bloco, com botão grande e texto curto;
- ícones e desenhos ao lado das palavras, nunca no lugar de uma instrução
  importante;
- passos numerados e listas curtas;
- contraste alto, foco visível e áreas de toque confortáveis;
- frases diretas, sem “marketplace”, “leilão” ou jargão de logística na área do
  cliente;
- o plano de carga usa cores, formas e status escrito, para não depender só da
  cor;
- o tour mostra o caminho com desenho, fala curta e botões claros.

Essa é uma diretriz de produto, não um recurso opcional. Toda tela nova deve
seguir o mesmo padrão.
