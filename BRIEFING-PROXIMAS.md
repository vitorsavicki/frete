# BRIEFING — Próximas tarefas (faça nesta ordem)

> Contexto: o site funciona, mas a página de detalhe da mudança ficou CONFUSA
> para usuário leigo. Feedback real do dono: "um usuário mal saberia usar;
> não dá para saber quem está contratando ou quem oferece o serviço".
> Regras de estilo: ler GUIA-ESTILO.md ANTES. Não regredir nada de lá.

## TAREFA 1 — Página de detalhe: deixar claro QUEM faz O QUÊ (prioridade máxima)

Hoje um visitante rola ~1500px de plano de carga antes de descobrir que pode
enviar orçamento. Corrigir em `src/pages/Freight.tsx` (DetalheFretePage):

1. **Cartão de ação logo abaixo da caixa de prazo**, variando por visitante:
   - Anônimo ou transportador (mudança aberta): título "🚛 Você faz mudanças?"
     texto "Envie seu orçamento para esta mudança. O cliente compara os valores
     e escolhe com quem fechar — direto, sem taxa do site." + botão grande
     "Enviar orçamento" (âncora para o formulário / login).
   - Dono da mudança: "Esta é a sua mudança" + resumo: X orçamentos recebidos,
     menor valor, e o que acontece depois do prazo (48h para escolher).
   - Mudança encerrada: ver TAREFA 3.
2. **Resumo da carga em 1 linha** no topo (ex.: "8 volumes · 270 kg · 3,6 m³ ·
   cabe em Caminhão baú") e mover as vistas 3D/Lado/Cima + conferência +
   boas práticas para DENTRO de um `<details>` fechado por padrão:
   "📦 Ver plano de carregamento detalhado". Nativo HTML, sem JS novo.
3. Ordem final da página: prazo → cartão de ação → rota/mapa → resumo carga
   → orçamentos → detalhes da retirada → `<details>` plano → segurança.

## TAREFA 2 — Foto hero decente

Substituir `public/img/hero-truck.webp` (é um caminhão CHILENO com URL
"granotrade.cl" visível na traseira). Buscar no Unsplash/Pexels foto de
mudança urbana (caminhão de mudança em rua residencial, ou pessoas com
caixas). Requisitos: sem marcas/placas legíveis, `.webp` < 120 KB,
creditado em `public/img/CREDITOS.md`. Idem `boxes.webp` (tem marca
"miloloo" estampada).

## TAREFA 3 — Contato após o aceite (sistema de comunicação, sem e-mail)

E-mail exige domínio comprado — NÃO tentar e-mail ainda. Solução no app:

1. Quando `status = 'encerrado'` e o visitante for o DONO ou o VENCEDOR,
   a página mostra um cartão "🤝 Mudança fechada — combinem os detalhes":
   - Dono vê: nome, e-mail e telefone do transportador vencedor
     (JOIN bids vencedor=1 → users).
   - Vencedor vê: nome, e-mail e telefone do dono (freight.user_id → users).
   - Qualquer outro visitante: não vê NADA disso (checar sessão no handler).
2. Texto do Zé Mudança no cartão: "Fechado! Agora combinem por escrito:
   data e horário exatos, valor final e o que está incluído. Guardem o
   comprovante. 🤝"
3. Depois (tarefa separada): chat simples na mesma página — tabela
   `messages` (freight_id, sender_id, texto, criado_em), visível só para
   as duas partes, notificando via `alerts`.

## TAREFA 4 — Verificar flash de erro do cadastro

POST /cadastro com CPF inválido retorna 400 re-renderizando o form.
Confirmar se a MENSAGEM de erro aparece visível no topo (ex.: "CPF inválido —
confira os números"). Se não aparecer, exibir o `erro` no topo do formulário.

## Ao terminar CADA tarefa

`npx tsc --noEmit` → `npx vitest run` → `npx wrangler deploy` → testar no
navegador → commit conventional PT → push. Ver GUIA-ESTILO.md seção 4.
