# E-mail grátis no Quero Mudar Já

O site envia três mensagens transacionais:

- boas-vindas depois do cadastro;
- aviso de novo orçamento para o dono da mudança;
- aviso de orçamento aceito para o transportador.

Nenhuma delas bloqueia o cadastro, o orçamento ou o aceite. Se o provedor falhar,
a mensagem entra na fila `email_outbox` (D1) e o cron do Worker tenta de novo.

## Ordem de envio

1. **Binding `EMAIL` do Cloudflare** (`send_email` no `wrangler.json`).
2. **Resend** (`RESEND_API_KEY`).
3. **Fila no D1**, reprocessada a cada 15 minutos pelo cron.

Sem nenhum provedor configurado o app continua normalmente e apenas registra
`[email:simulado]` no log. Nada é gravado na fila nesse caso.

## Opção 1 — Resend (recomendado para começar)

O plano grátis do Resend entrega para qualquer destinatário depois que um
domínio é verificado. É a opção mais simples para o site de mudanças.

1. Crie a conta em <https://resend.com>.
2. Adicione o domínio do site (ex.: `queromudarja.com.br`) e crie os registros
   DNS que o Resend mostrar (SPF/DKIM). Como o DNS costuma estar na Cloudflare,
   a propagação é rápida.
3. Crie uma API key com permissão de envio.
4. Configure os segredos e o remetente:

```bash
npx wrangler secret put RESEND_API_KEY
npx wrangler secret put EMAIL_FROM
npx wrangler deploy
```

`EMAIL_FROM` deve usar o domínio verificado, por exemplo:

```text
Quero Mudar Já <no-reply@queromudarja.com.br>
```

O remetente padrão `onboarding@resend.dev` só entrega no e-mail dono da conta —
serve para teste, não para clientes.

### Limites do plano grátis

- 100 e-mails por dia e cerca de 3.000 por mês (confira o painel do Resend,
  os limites podem mudar).
- 1 domínio verificado no plano gratuito.
- Sem SLA. Para volume maior, o plano pago do Resend ou o Workers Paid da
  Cloudflare passam a valer a pena.

## Opção 2 — Cloudflare Email Sending

O binding já está declarado no `wrangler.json`:

```json
"send_email": [{ "name": "EMAIL" }]
```

**Pré-requisito:** um domínio (zona) na mesma conta Cloudflare. O endereço
`*.workers.dev` não serve como remetente e não pode receber registros MX. Sem
uma zona, o binding existe, mas todo envio falha — inclusive para o dono da
conta.

1. Adicione o domínio à Cloudflare e aponte os nameservers no registrador.
2. Ative o Email Routing e o Email Sending:

```bash
npx wrangler email routing enable seu-dominio.com.br
npx wrangler email sending enable seu-dominio.com.br
npx wrangler email routing dns get seu-dominio.com.br
npx wrangler email sending dns get seu-dominio.com.br
```

Crie no DNS da zona os registros que os dois comandos `dns get` mostrarem.
Para envio restrito a destinos verificados, cadastre o endereço de teste:

```bash
npx wrangler email routing addresses create voce@exemplo.com
```

O endereço recebe uma confirmação por e-mail; só depois de confirmar ele passa
a receber as mensagens.

3. Troque o remetente e publique:

```bash
npx wrangler secret put EMAIL_FROM
# Quero Mudar Já <no-reply@seu-dominio.com.br>
npx wrangler deploy
```

Dependendo do plano e da configuração do binding, o envio pode ficar restrito a
**endereços de destino verificados** no Email Routing. Nesse caso ele funciona
bem para avisos internos e para o dono do projeto, mas não substitui o Resend
para mandar e-mail a qualquer cliente. Para entregar a qualquer destinatário é
necessário um domínio verificado na Cloudflare e, normalmente, o Workers Paid.

Para usar os dois, o código tenta o Cloudflare primeiro e cai no Resend se o
binding recusar. O remetente continua sendo `EMAIL_FROM`.

## Fila e reprocessamento

A tabela é criada pela migração `0006_email_outbox.sql`:

```bash
npm run db:migrate:local    # desenvolvimento
npm run db:migrate          # produção
```

O cron está em `wrangler.json`:

```json
"triggers": { "crons": ["*/15 * * * *"] }
```

Cada item tem até 8 tentativas, com espera de 5 min, 10 min, 20 min... até o
teto de 24 h. Depois disso o status vira `descartado` e o erro fica em
`ultimo_erro`. O envio é feito em lotes pequenos (20 por execução) para caber
nos planos grátis.

## Diagnóstico

```bash
curl https://SEU-DOMINIO/api/email/status
```

Resposta (sem destinatários e sem segredos):

```json
{
  "configurado": true,
  "provedores": { "cloudflare": true, "resend": true },
  "remetente": "Quero Mudar Já <no-reply@queromudarja.com.br>",
  "fila": { "pendentes": 0, "enviados": 12, "descartados": 0, "proximaTentativa": null }
}
```

Se `configurado` for `false`, nenhum provedor está ativo e os e-mails só
aparecem no log. Se `fila.pendentes` crescer, confira a API key, o domínio
verificado e o `EMAIL_FROM`.

## Custo e segurança

- O envio é sempre **server-side**. O navegador nunca fala com Resend ou com o
  binding de e-mail, então a CSP continua fechada (`script-src 'self'`).
- O endpoint de status não devolve e-mails de clientes nem chaves.
- A fila guarda o conteúdo da mensagem no D1; não coloque senha, token ou dado
  sensível no corpo do e-mail.
- Para cancelar um aviso, basta remover a chamada `waitUntil(...)` da rota
  correspondente — o cadastro e os orçamentos continuam funcionando.
