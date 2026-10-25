// Envio de e-mail transacional via Resend (plano grátis: 100 e-mails/dia).
// Sem RESEND_API_KEY o app continua funcionando: as mensagens ficam no log.
//
// Para ativar em produção:
//   npx wrangler secret put RESEND_API_KEY
//   (opcional) npx wrangler secret put EMAIL_FROM
//
// O remetente padrão do Resend (onboarding@resend.dev) só entrega no e-mail
// dono da conta. Para enviar a qualquer cliente, verifique um domínio grátis
// no Resend e ajuste EMAIL_FROM.

import type { AppBindings } from './bindings';
import { dinheiro } from './format';

interface EmailParams {
  to: string;
  subject: string;
  html: string;
  text: string;
}

const REMETENTE_PADRAO = 'Quero Mudar Já <onboarding@resend.dev>';

function escapar(valor: string): string {
  return valor
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

export function emailConfigurado(env: AppBindings): boolean {
  return Boolean(env.RESEND_API_KEY);
}

export async function enviarEmail(env: AppBindings, params: EmailParams): Promise<boolean> {
  if (!params.to || !params.to.includes('@')) return false;

  if (!env.RESEND_API_KEY) {
    console.log(`[email:simulado] para=${params.to} assunto="${params.subject}"`);
    return false;
  }

  try {
    const resp = await fetch('https://api.resend.com/emails', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${env.RESEND_API_KEY}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        from: env.EMAIL_FROM || REMETENTE_PADRAO,
        to: [params.to],
        subject: params.subject,
        html: params.html,
        text: params.text,
      }),
      signal: AbortSignal.timeout(8000),
    });
    if (!resp.ok) {
      console.error('Resend falhou', resp.status, await resp.text().catch(() => ''));
      return false;
    }
    return true;
  } catch (e) {
    console.error('Resend indisponível', e);
    return false;
  }
}

function layout(titulo: string, corpoHtml: string, rodape: string): string {
  return `<!doctype html>
<html lang="pt-BR">
  <body style="margin:0;background:#f1f5f9;font-family:system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;color:#0f172a">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 12px">
      <tr><td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0">
          <tr><td style="background:#1d4ed8;padding:20px 24px;color:#fff;font-size:18px;font-weight:700">Quero Mudar Já</td></tr>
          <tr><td style="padding:24px">
            <h1 style="margin:0 0 12px;font-size:20px">${titulo}</h1>
            ${corpoHtml}
          </td></tr>
          <tr><td style="padding:16px 24px;background:#f8fafc;color:#64748b;font-size:12px">${rodape}</td></tr>
        </table>
      </td></tr>
    </table>
  </body>
</html>`;
}

function botao(url: string, texto: string): string {
  return `<p style="margin:20px 0 8px"><a href="${url}" style="display:inline-block;background:#1d4ed8;color:#fff;text-decoration:none;padding:11px 18px;border-radius:10px;font-weight:600">${texto}</a></p>`;
}

export async function emailBoasVindas(
  env: AppBindings,
  dados: { para: string; nome: string; papel: string; baseUrl: string },
): Promise<boolean> {
  const nome = escapar(dados.nome);
  const papel =
    dados.papel === 'transportador'
      ? 'Encontre fretes, dê lances e acompanhe seus resultados pelo painel.'
      : 'Publique seu primeiro frete e receba lances de transportadores.';
  const html = layout(
    `Boas-vindas, ${nome}!`,
    `<p style="margin:0 0 12px;color:#334155">Sua conta no Quero Mudar Já está pronta. ${papel}</p>
     ${botao(`${dados.baseUrl}/painel`, 'Abrir meu painel')}
     <p style="margin:16px 0 0;color:#64748b;font-size:13px">Publicar e dar lances é 100% grátis. Sem taxa de intermediação.</p>`,
    'Você recebeu este e-mail porque criou uma conta no Quero Mudar Já.',
  );
  const text = `Boas-vindas, ${dados.nome}!\n\nSua conta está pronta. ${papel}\n\nAcesse: ${dados.baseUrl}/painel`;
  return enviarEmail(env, { to: dados.para, subject: 'Bem-vindo(a) ao Quero Mudar Já 🚚', html, text });
}

export async function emailNovoLance(
  env: AppBindings,
  dados: {
    para: string;
    nomeDono: string;
    descricao: string;
    valor: number;
    freteId: number;
    baseUrl: string;
  },
): Promise<boolean> {
  const descricao = escapar(dados.descricao);
  const html = layout(
    'Novo lance no seu frete',
    `<p style="margin:0 0 12px;color:#334155">Olá, ${escapar(dados.nomeDono)}! Um transportador deu um lance de <strong>${dinheiro(dados.valor)}</strong> no frete <strong>${descricao}</strong> (#${dados.freteId}).</p>
     ${botao(`${dados.baseUrl}/fretes/${dados.freteId}`, 'Ver o lance')}
     <p style="margin:16px 0 0;color:#64748b;font-size:13px">Você decide quando aceitar. O leilão segue aberto até você escolher.</p>`,
    'Você recebeu este e-mail porque é o dono deste frete.',
  );
  const text = `Olá, ${dados.nomeDono}!\n\nNovo lance de ${dinheiro(dados.valor)} no frete "${dados.descricao}" (#${dados.freteId}).\n\nVeja: ${dados.baseUrl}/fretes/${dados.freteId}`;
  return enviarEmail(env, {
    to: dados.para,
    subject: `Novo lance no frete #${dados.freteId} (${dinheiro(dados.valor)})`,
    html,
    text,
  });
}

export async function emailLanceVencedor(
  env: AppBindings,
  dados: {
    para: string;
    nome: string;
    descricao: string;
    valor: number;
    freteId: number;
    baseUrl: string;
  },
): Promise<boolean> {
  const descricao = escapar(dados.descricao);
  const html = layout(
    '🎉 Seu lance venceu!',
    `<p style="margin:0 0 12px;color:#334155">Parabéns, ${escapar(dados.nome)}! Seu lance de <strong>${dinheiro(dados.valor)}</strong> venceu o frete <strong>${descricao}</strong> (#${dados.freteId}).</p>
     <p style="margin:0 0 12px;color:#334155">Combine a retirada com o cliente pelo telefone informado no painel.</p>
     ${botao(`${dados.baseUrl}/fretes/${dados.freteId}`, 'Ver o frete')}`,
    'Você recebeu este e-mail porque venceu um leilão no Quero Mudar Já.',
  );
  const text = `Parabéns, ${dados.nome}!\n\nSeu lance de ${dinheiro(dados.valor)} venceu o frete "${dados.descricao}" (#${dados.freteId}).\n\nVeja: ${dados.baseUrl}/fretes/${dados.freteId}`;
  return enviarEmail(env, {
    to: dados.para,
    subject: `Seu lance venceu o frete #${dados.freteId}`,
    html,
    text,
  });
}
