// Login com Google (OAuth 2.0 Authorization Code) — gratuito, sem SDK.
// Só é habilitado quando GOOGLE_CLIENT_ID e GOOGLE_CLIENT_SECRET existem.
//
// Configuração no Google Cloud Console:
//   1. Crie um projeto e uma credencial "OAuth client ID" do tipo Web.
//   2. Authorized redirect URIs:
//        https://frete.vitorsavicki.workers.dev/auth/google/callback
//        http://127.0.0.1:8787/auth/google/callback   (dev)
//   3. npx wrangler secret put GOOGLE_CLIENT_ID
//      npx wrangler secret put GOOGLE_CLIENT_SECRET

import type { AppBindings } from './bindings';

const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
const TOKEN_URL = 'https://oauth2.googleapis.com/token';
const USERINFO_URL = 'https://openidconnect.googleapis.com/v1/userinfo';

export interface PerfilGoogle {
  sub: string;
  email: string;
  emailVerificado: boolean;
  nome: string;
  sobrenome: string;
  avatar: string | null;
}

export function googleConfigurado(env: AppBindings): boolean {
  return Boolean(env.GOOGLE_CLIENT_ID && env.GOOGLE_CLIENT_SECRET);
}

export function urlAutorizacaoGoogle(
  env: AppBindings,
  redirectUri: string,
  state: string,
): string | null {
  if (!env.GOOGLE_CLIENT_ID) return null;
  const params = new URLSearchParams({
    client_id: env.GOOGLE_CLIENT_ID,
    redirect_uri: redirectUri,
    response_type: 'code',
    scope: 'openid email profile',
    state,
    prompt: 'select_account',
    access_type: 'online',
  });
  return `${AUTH_URL}?${params.toString()}`;
}

export async function trocarCodigoGoogle(
  env: AppBindings,
  code: string,
  redirectUri: string,
): Promise<string | null> {
  if (!env.GOOGLE_CLIENT_ID || !env.GOOGLE_CLIENT_SECRET) return null;
  try {
    const resp = await fetch(TOKEN_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        client_id: env.GOOGLE_CLIENT_ID,
        client_secret: env.GOOGLE_CLIENT_SECRET,
        code,
        grant_type: 'authorization_code',
        redirect_uri: redirectUri,
      }),
      signal: AbortSignal.timeout(8000),
    });
    if (!resp.ok) {
      console.error('Google token falhou', resp.status, await resp.text().catch(() => ''));
      return null;
    }
    const dados = (await resp.json()) as { access_token?: string };
    return dados.access_token ?? null;
  } catch (e) {
    console.error('Google token indisponível', e);
    return null;
  }
}

export async function obterPerfilGoogle(accessToken: string): Promise<PerfilGoogle | null> {
  try {
    const resp = await fetch(USERINFO_URL, {
      headers: { Authorization: `Bearer ${accessToken}` },
      signal: AbortSignal.timeout(8000),
    });
    if (!resp.ok) return null;
    const d = (await resp.json()) as {
      sub?: string;
      email?: string;
      email_verified?: boolean;
      given_name?: string;
      family_name?: string;
      name?: string;
      picture?: string;
    };
    if (!d.sub || !d.email) return null;
    const nome = (d.given_name || d.name || d.email.split('@')[0] || 'Cliente').trim();
    return {
      sub: d.sub,
      email: d.email.toLowerCase(),
      emailVerificado: d.email_verified === true,
      nome: nome.slice(0, 80),
      sobrenome: (d.family_name || 'Google').slice(0, 120),
      avatar: d.picture ?? null,
    };
  } catch {
    return null;
  }
}
