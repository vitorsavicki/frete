// Envio de e-mail transacional com fila de segurança.
//
// Ordem de tentativa:
//   1. binding EMAIL do Cloudflare (Email Sending) — ótimo para endereços
//      verificados e para quem já tem domínio no Cloudflare;
//   2. Resend (plano grátis: 100 e-mails/dia / 3.000/mês) — melhor opção para
//      entregar a qualquer cliente sem custo;
//   3. se os dois falharem, a mensagem entra em `email_outbox` e o cron do
//      Worker tenta de novo com espera exponencial.
//
// Sem nenhum provedor configurado o site continua funcionando: a mensagem só
// aparece no log e nada é gravado na fila. Isso evita encher o D1 à toa.
//
// Ativação (detalhes em docs/email.md):
//   npx wrangler secret put RESEND_API_KEY
//   npx wrangler secret put EMAIL_FROM
//   npx wrangler deploy
//
// O remetente padrão do Resend (onboarding@resend.dev) só entrega no e-mail
// dono da conta. Para enviar a qualquer cliente, verifique um domínio grátis
// no Resend e ajuste EMAIL_FROM.

import type { AppBindings } from './bindings';
import { dinheiro } from './format';

export interface EmailParams {
  to: string;
  subject: string;
  html: string;
  text: string;
}

interface ResultadoProvedor {
  ok: boolean;
  provedor: 'cloudflare' | 'resend' | null;
  erro?: string;
}

interface EmailPendente {
  id: number;
  destinatario: string;
  assunto: string;
  html: string;
  texto: string;
  tentativas: number;
}

const REMETENTE_PADRAO = 'Quero Mudar Já <onboarding@resend.dev>';
const MAX_TENTATIVAS = 8;
const LOTE_CRON = 20;

function escapar(valor: string): string {
  return valor
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function remetente(env: AppBindings): string {
  return env.EMAIL_FROM?.trim() || REMETENTE_PADRAO;
}

/**
 * Separa "Nome <email>" em um objeto aceito pelo binding de e-mail do
 * Cloudflare. Se vier só o endereço, usa o nome da marca.
 */
function remetenteCloudflare(valor: string): { name: string; email: string } {
  const match = valor.match(/^\s*(.*?)\s*<([^<>]+)>\s*$/);
  if (match?.[2]) {
    return { name: match[1] || 'Quero Mudar Já', email: match[2].trim() };
  }
  return { name: 'Quero Mudar Já', email: valor.trim() };
}

function destinatarioValido(valor: string): boolean {
  return /^[^\s@<>]+@[^\s@<>]+\.[^\s@<>]+$/.test(valor.trim());
}

export function emailConfigurado(env: AppBindings): boolean {
  return Boolean(env.EMAIL || env.RESEND_API_KEY);
}

async function enviarPeloCloudflare(
  env: AppBindings,
  params: EmailParams,
): Promise<ResultadoProvedor> {
  if (!env.EMAIL) return { ok: false, provedor: null };
  try {
    await env.EMAIL.send({
      from: remetenteCloudflare(remetente(env)),
      to: params.to,
      subject: params.subject,
      html: params.html,
      text: params.text,
    });
    return { ok: true, provedor: 'cloudflare' };
  } catch (e) {
    const erro = e instanceof Error ? e.message : String(e);
    console.error('Cloudflare Email falhou', erro);
    return { ok: false, provedor: 'cloudflare', erro };
  }
}

async function enviarPeloResend(
  env: AppBindings,
  params: EmailParams,
): Promise<ResultadoProvedor> {
  if (!env.RESEND_API_KEY) return { ok: false, provedor: null };
  try {
    const resp = await fetch('https://api.resend.com/emails', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${env.RESEND_API_KEY}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        from: remetente(env),
        to: [params.to],
        subject: params.subject,
        html: params.html,
        text: params.text,
      }),
      signal: AbortSignal.timeout(8000),
    });
    if (!resp.ok) {
      const corpo = await resp.text().catch(() => '');
      const erro = `HTTP ${resp.status}: ${corpo.slice(0, 300)}`;
      console.error('Resend falhou', erro);
      return { ok: false, provedor: 'resend', erro };
    }
    return { ok: true, provedor: 'resend' };
  } catch (e) {
    const erro = e instanceof Error ? e.message : String(e);
    console.error('Resend indisponível', erro);
    return { ok: false, provedor: 'resend', erro };
  }
}

/** Tenta os provedores na ordem e devolve o erro consolidado. */
async function tentarEnviar(env: AppBindings, params: EmailParams): Promise<ResultadoProvedor> {
  const erros: string[] = [];
  let tentou = false;

  if (env.EMAIL) {
    tentou = true;
    const resultado = await enviarPeloCloudflare(env, params);
    if (resultado.ok) return resultado;
    if (resultado.erro) erros.push(`cloudflare: ${resultado.erro}`);
  }

  if (env.RESEND_API_KEY) {
    tentou = true;
    const resultado = await enviarPeloResend(env, params);
    if (resultado.ok) return resultado;
    if (resultado.erro) erros.push(`resend: ${resultado.erro}`);
  }

  return {
    ok: false,
    provedor: null,
    erro: tentou ? erros.join(' | ').slice(0, 500) : 'nenhum provedor configurado',
  };
}

async function enfileirar(
  env: AppBindings,
  params: EmailParams,
  motivo: string,
): Promise<void> {
  try {
    await env.DB.prepare(
      `INSERT INTO email_outbox (destinatario, assunto, html, texto, ultimo_erro)
       VALUES (?, ?, ?, ?, ?)`,
    )
      .bind(params.to, params.subject, params.html, params.text, motivo.slice(0, 500))
      .run();
  } catch (e) {
    // Tabela ainda não migrada: a falha do e-mail não pode derrubar o site.
    console.error('Não foi possível gravar o e-mail na fila', e);
  }
}

/**
 * Envia imediatamente ou deixa na fila para o cron. Nunca lança: o cadastro,
 * o lance e o aceite precisam continuar funcionando mesmo sem e-mail.
 */
export async function enviarEmail(env: AppBindings, params: EmailParams): Promise<boolean> {
  const para = params.to?.trim() ?? '';
  if (!destinatarioValido(para)) return false;

  const dados: EmailParams = { ...params, to: para };

  if (!emailConfigurado(env)) {
    console.log(`[email:simulado] para=${para} assunto="${params.subject}"`);
    return false;
  }

  const resultado = await tentarEnviar(env, dados);
  if (resultado.ok) return true;

  await enfileirar(env, dados, resultado.erro ?? 'falha desconhecida');
  return false;
}

function proximaTentativa(tentativas: number): string {
  // 5 min, 10, 20, 40... até o teto de 24 h.
  const minutos = Math.min(24 * 60, 5 * 2 ** Math.max(0, tentativas - 1));
  return new Date(Date.now() + minutos * 60_000).toISOString();
}

/**
 * Reprocessa a fila. Chamado pelo cron do Worker a cada 15 minutos.
 * Limitado a poucos itens por execução para respeitar os planos grátis.
 */
export async function processarFilaEmails(
  env: AppBindings,
  limite = LOTE_CRON,
): Promise<{ processados: number; enviados: number; falharam: number; restantes: number }> {
  if (!emailConfigurado(env)) {
    return { processados: 0, enviados: 0, falharam: 0, restantes: 0 };
  }

  let pendentes: EmailPendente[] = [];
  try {
    const agora = new Date().toISOString();
    const resultado = await env.DB.prepare(
      `SELECT id, destinatario, assunto, html, texto, tentativas
       FROM email_outbox
       WHERE status = 'pendente' AND proxima_tentativa <= ?
       ORDER BY id ASC
       LIMIT ?`,
    )
      .bind(agora, Math.max(1, Math.min(50, limite)))
      .all<EmailPendente>();
    pendentes = resultado.results ?? [];
  } catch (e) {
    console.error('Fila de e-mail indisponível', e);
    return { processados: 0, enviados: 0, falharam: 0, restantes: 0 };
  }

  let enviados = 0;
  let falharam = 0;

  for (const item of pendentes) {
    const resultado = await tentarEnviar(env, {
      to: item.destinatario,
      subject: item.assunto,
      html: item.html,
      text: item.texto,
    });

    if (resultado.ok) {
      enviados += 1;
      await env.DB.prepare(
        `UPDATE email_outbox
         SET status = 'enviado', tentativas = tentativas + 1,
             ultimo_erro = NULL, enviado_em = ?
         WHERE id = ?`,
      )
        .bind(new Date().toISOString(), item.id)
        .run()
        .catch((e) => console.error('Falha ao marcar e-mail como enviado', e));
      continue;
    }

    falharam += 1;
    const tentativas = item.tentativas + 1;
    const descartar = tentativas >= MAX_TENTATIVAS;
    await env.DB.prepare(
      `UPDATE email_outbox
       SET tentativas = ?, status = ?, ultimo_erro = ?, proxima_tentativa = ?
       WHERE id = ?`,
    )
      .bind(
        tentativas,
        descartar ? 'descartado' : 'pendente',
        (resultado.erro ?? 'falha desconhecida').slice(0, 500),
        proximaTentativa(tentativas),
        item.id,
      )
      .run()
      .catch((e) => console.error('Falha ao reagendar e-mail', e));
  }

  let restantes = 0;
  try {
    const contagem = await env.DB.prepare(
      "SELECT COUNT(*) AS n FROM email_outbox WHERE status = 'pendente'",
    ).first<{ n: number }>();
    restantes = contagem?.n ?? 0;
  } catch {
    restantes = 0;
  }

  return { processados: pendentes.length, enviados, falharam, restantes };
}

export interface StatusEmail {
  configurado: boolean;
  provedores: { cloudflare: boolean; resend: boolean };
  remetente: string;
  fila: {
    pendentes: number;
    enviados: number;
    descartados: number;
    proximaTentativa: string | null;
  } | null;
}

/** Diagnóstico público, sem destinatários e sem segredos. */
export async function statusEmail(env: AppBindings): Promise<StatusEmail> {
  let fila: StatusEmail['fila'] = null;
  try {
    const linhas = await env.DB.prepare(
      'SELECT status, COUNT(*) AS total FROM email_outbox GROUP BY status',
    ).all<{ status: string; total: number }>();
    const porStatus = new Map((linhas.results ?? []).map((l) => [l.status, l.total]));
    const proxima = await env.DB.prepare(
      "SELECT MIN(proxima_tentativa) AS proxima FROM email_outbox WHERE status = 'pendente'",
    ).first<{ proxima: string | null }>();
    fila = {
      pendentes: porStatus.get('pendente') ?? 0,
      enviados: porStatus.get('enviado') ?? 0,
      descartados: porStatus.get('descartado') ?? 0,
      proximaTentativa: proxima?.proxima ?? null,
    };
  } catch {
    fila = null;
  }

  return {
    configurado: emailConfigurado(env),
    provedores: {
      cloudflare: Boolean(env.EMAIL),
      resend: Boolean(env.RESEND_API_KEY),
    },
    remetente: remetente(env),
    fila,
  };
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
      ? 'Encontre mudanças, envie orçamentos e acompanhe seus resultados pelo painel.'
      : 'Publique sua primeira mudança e receba propostas de transportadores.';
  const html = layout(
    `Boas-vindas, ${nome}!`,
    `<p style="margin:0 0 12px;color:#334155">Sua conta no Quero Mudar Já está pronta. ${papel}</p>
     ${botao(`${dados.baseUrl}/painel`, 'Abrir meu painel')}
     <p style="margin:16px 0 0;color:#64748b;font-size:13px">Publicar e enviar orçamentos é 100% grátis. Sem taxa de intermediação.</p>`,
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
    'Novo orçamento na sua mudança',
    `<p style="margin:0 0 12px;color:#334155">Olá, ${escapar(dados.nomeDono)}! Um transportador enviou um orçamento de <strong>${dinheiro(dados.valor)}</strong> para a mudança <strong>${descricao}</strong> (#${dados.freteId}).</p>
     ${botao(`${dados.baseUrl}/fretes/${dados.freteId}`, 'Ver o orçamento')}
     <p style="margin:16px 0 0;color:#64748b;font-size:13px">Você decide quando aceitar. A mudança segue aberta até você escolher.</p>`,
    'Você recebeu este e-mail porque é o dono desta mudança.',
  );
  const text = `Olá, ${dados.nomeDono}!\n\nNovo orçamento de ${dinheiro(dados.valor)} para a mudança "${dados.descricao}" (#${dados.freteId}).\n\nVeja: ${dados.baseUrl}/fretes/${dados.freteId}`;
  return enviarEmail(env, {
    to: dados.para,
    subject: `Novo orçamento para a mudança #${dados.freteId} (${dinheiro(dados.valor)})`,
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
    '🎉 Seu orçamento foi aceito!',
    `<p style="margin:0 0 12px;color:#334155">Parabéns, ${escapar(dados.nome)}! Seu orçamento de <strong>${dinheiro(dados.valor)}</strong> foi aceito para a mudança <strong>${descricao}</strong> (#${dados.freteId}).</p>
     <p style="margin:0 0 12px;color:#334155">Combine a retirada com o cliente pelo telefone informado no painel.</p>
     ${botao(`${dados.baseUrl}/fretes/${dados.freteId}`, 'Ver a mudança')}`,
    'Você recebeu este e-mail porque seu orçamento foi aceito no Quero Mudar Já.',
  );
  const text = `Parabéns, ${dados.nome}!\n\nSeu orçamento de ${dinheiro(dados.valor)} foi aceito para a mudança "${dados.descricao}" (#${dados.freteId}).\n\nVeja: ${dados.baseUrl}/fretes/${dados.freteId}`;
  return enviarEmail(env, {
    to: dados.para,
    subject: `Seu orçamento foi aceito — mudança #${dados.freteId}`,
    html,
    text,
  });
}
