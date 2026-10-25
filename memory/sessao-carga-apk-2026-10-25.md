# Sessão 2026-10-25 — redesign, carga com IA e APK Android

## Produção

- URL: https://frete.vitorsavicki.workers.dev
- Deploy: `bcc69ecc-b63e-46d3-bb5f-a7fd23fd5804` (wrangler 4.152.0)
- Migration `0003_carga_caminhao.sql` aplicada no D1 remoto (`frete`,
  `77a441f6-2e58-467b-b47f-76a131fc702a`). Nenhuma migration pendente.

## O que foi entregue

- Redesign responsivo de Home, Auth, Freight, Dashboard, Layout, FreteCard,
  caminhão e página do app.
- Montador de carga opcional no cadastro: 23 itens predefinidos, quantidades,
  totais de peso/volume e snapshot JSON no frete.
- Perfil de caminhão do transportador (`/caminhao`): medidas internas,
  capacidade, consumo, diesel, custo fixo e margem. Um caminhão por
  transportador (`trucks.user_id UNIQUE`).
- Conferência de encaixe determinística (`src/lib/carga.ts`) com status
  `cabe`/`limite`/`nao_cabe`, ocupação e recomendações de veículo.
- Estimador de custo e preço sugerido (`src/lib/cost.ts`), com distância por
  haversine × 1,25.
- IA opcional de carga em `POST /api/ia/carga`: análise textual da carga e do
  preço; o encaixe determinístico continua sendo a fonte da verdade. Em caso
  de indisponibilidade, a API responde 503 com o resultado determinístico e a
  interface mantém o painel local.
- Modelo Workers AI trocado para `@cf/meta/llama-3.1-8b-instruct-fp8`: o
  `@cf/meta/llama-3.1-8b-instruct` foi descontinuado em 2026-05-30 e passou a
  falhar com `InferenceUpstreamError (5028)`.

## APK Android

- Pacote: `br.com.queromudarja.app`
- Versão: 1.0.0 (versionCode 1), minSdk 24 (Android 7.0), targetSdk 34
- Tamanho: 50.135 bytes
- SHA-256: `6d7f3c9488c0bdbec33b869f239754d0dea3842d4b3a2c2d0649b332be1cc66d`
- Download: `/download/quero-mudar-ja-1.0.0.apk` (versionado) e
  `/download/quero-mudar-ja.apk` (estável)
- Página pública: `/app`, com instruções, segurança, versão, tamanho e hash.
- O APK é uma casca WebView que carrega a produção; links externos abrem no
  navegador, upload de arquivo, voltar, erros de rede e `confirm` da IA estão
  tratados. Só pede permissão de internet.
- Assinatura v2/v3 verificada; certificado SHA-256
  `370b02995541356b46d49d777060f48694a2b61d795c9c9027e19bba610b63e9`.
- Build: `powershell -ExecutionPolicy Bypass -File android/build.ps1`
  (JDK 17 + Android SDK 35 em `E:\kube\dev\android-tools`). O script publica o
  APK, gera o `.sha256` e atualiza `src/lib/apk.ts`.
- Keystore: `.secrets/android/queromudarja.jks`, senha em
  `android/keystore.properties` (ambos gitignored). **Sem backup do keystore
  não é possível atualizar o app já instalado.**
- Distribuição fora da Play Store: o aviso de “fonte desconhecida” é normal.

## Validação

- `npm run typecheck`: 0 erros.
- `npm test`: 5 arquivos / 28 testes passando (aviso de AI remoto e timeout de
  fechamento do Vitest continuam, sem falha de teste).
- Smoke local: contas `carga.cliente.1792945372@example.com` e
  `carga.transp.1792945372@example.com`; frete #4 com 5 itens, 245 kg e
  3,68 m³; caminhão 3/4; encaixe `cabe`; IA retornando texto e fallback 503
  determinístico.
- “Usar medidas típicas” revalidado: Fiorino preenche 1,45 × 1,30 × 1,20 m,
  650 kg e mantém a mensagem “Medidas típicas de Fiorino aplicadas…”.
- QA mobile 390×844 e 360×800 em `/app` local e produção: sem overflow,
  sem erros de console e sem respostas ruins.
- Produção: `/app` 200; APK versionado e estável baixam 50.135 bytes com o
  SHA-256 acima; `Content-Type: application/vnd.android.package-archive` e
  `Content-Disposition: attachment`; `/caminhao` redireciona para login;
  `/api/ia/carga` sem sessão responde 401; `/api/cep/01310100` responde 200.

## Pendências e limites conhecidos

- Credenciais Google OAuth e Resend ainda ausentes: login por e-mail/senha é o
  caminho atual e os recursos de e-mail ficam simulados.
- OAuth do Google não funciona dentro da WebView (abre navegador externo).
- OSRM público inacessível nesta rede; distância segue como haversine × 1,25.
- APK verificado estaticamente (assinatura, badging e manifesto), mas ainda não
  instalado em aparelho/emulador Android real.
- Sem backup externo do keystore.
