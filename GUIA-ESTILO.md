# GUIA DE ESTILO — Quero Mudar Já (LEIA ANTES DE QUALQUER MUDANÇA)

Regras obrigatórias para qualquer IA ou pessoa que mexer neste repo.
Este guia existe porque o visual e o texto já ficaram bons — e regrediram antes.
Quando em dúvida, MUDE MENOS. Não reescreva o que funciona.

## 1. Identidade visual — UMA linguagem só

- **Estilo oficial: flat/line.** Referência: `public/img/app-icon-512.png` e `public/img/favicon.svg`.
  Todo ícone/ilustração nova deve seguir esse estilo (traço grosso, cantos arredondados, 2 cores).
- **Paleta:** indigo `#1e4fd8` (primária), âmbar `#f5b942` (acento), tinta slate `#1e293b`/`#475569`, fundo `#f4f6fb`. Já definidas em `public/css/style.css` — use as variáveis, não crie cores novas.
- **PROIBIDO:**
  - Emojis 3D / assets glossy estilo "brinquedo" (o seletor de veículos antigo foi refeito por isso)
  - Fotos de estoque com placas/marcas estrangeiras visíveis (a foto antiga mostrava um caminhão chileno com URL ".cl")
  - Imagens com marca d'água ou logo de terceiros
  - Misturar estilos (foto + cartoon + 3D na mesma página)
- Fotos: só de Unsplash/Pexels (licença livre), tema "mudança urbana no Brasil", otimizadas em `.webp` < 120 KB, crédito em `public/img/CREDITOS.md`.
- Assets externos: NUNCA por CDN — baixar para `public/` (CSP é fechada de propósito, não abra exceções).

## 2. Copy — português para quem nunca usou o app

| SEMPRE diga | NUNCA diga (na UI) |
|---|---|
| mudança | frete (só em código/URLs) |
| orçamento | lance, bid |
| "Mudar de casa" / "Fazer mudanças" | cliente / transportador |
| você, frases curtas | jargão técnico, inglês |

- Tom: **vizinho prestativo**. Direto, gentil, com emoji leve (1 por frase, no máximo).
- Toda ação importante tem mensagem de sucesso com próxima etapa. Exemplos certos já no ar:
  - "Conta criada! Bem-vindo(a) 🎉"
  - "Mudança publicada! 🚚 Agora é só esperar os orçamentos."
  - "Orçamento aceito! Mudança fechada. 🤝"
- Erros: sempre dizer O QUE fazer. "CPF inválido — confira os números." Nunca só "Erro 400".
- Pendências de copy conhecidas (corrija se encontrar): a página de detalhe diz "Rota do frete" → trocar para "Rota da mudança"; o 404 diz "o frete já partiu" (aceitável, é humor).

## 3. Mascote: Zé Mudança 🚛

- Persona: o amigo que já fez 200 mudanças. Fala de "você", humor leve, SEMPRE deixa o usuário sabendo o próximo passo.
- Regra de ouro: **Zé nunca aparece sem uma ação útil** (botão ou link). Se não tem ação, não usa o Zé.
- Onde o Zé deve aparecer: tour de primeiros passos, estados vazios ("Nenhuma mudança por aqui. Quando aparecer uma na sua rota, eu te aviso!"), 404, sucesso de aceite (com o contato das partes), e — no futuro — assinatura dos e-mails.
- Rosto do Zé: usar o caminhão flat do ícone (`favicon.svg` / `app-icon-512.png`). Não inventar outro desenho.

## 4. Antes de commitar (checklist obrigatório)

1. `npx tsc --noEmit` → 0 erros
2. `npx vitest run` → todos verdes (e escreva teste se criou regra em `src/lib/rules.ts`)
3. `npx wrangler deploy` → abrir o site e testar o fluxo inteiro de verdade:
   cadastro → publicar → orçamento → prazo → aceitar → notificações
4. Commits: conventional commits em PT, minúsculas (`fix: ...`, `feat: ...`, `docs: ...`)
5. `git push` no final — trabalho só existe quando está no GitHub

## 5. Não regredir (já está certo, não reescrever)

- Segurança: CSP, `protecaoOrigem` (CSRF), `limiteAuth` (rate limit), senhas PBKDF2, cookies HttpOnly
- Regras de negócio em `src/lib/rules.ts` (com testes em `test/rules.test.ts`)
- Transações atômicas com `db.batch()` em aceitar/cancelar
- Alertas in-app em todas as mudanças de estado
- Fluxo de prazos: `encerra_em` (fim dos orçamentos) → `prazo_escolha_em` (fim da escolha) — máquina de estados via `situacao`
- Mensagens via query string `?msg=` / `?erro=` + redirect 302 (padrão POST-redirect)

## 6. Próximas tarefas sugeridas (nesta ordem)

Ver **BRIEFING-PROXIMAS.md** — lista detalhada e ordenada (clareza da página de detalhe, hero, contato após aceite, flash de erro).
