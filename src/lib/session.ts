// Sessões opacas em D1: o cookie guarda só um id aleatório de 256 bits.
// Revogável (logout derruba na hora), expira em 30 dias, renovável por uso.

import { createMiddleware } from 'hono/factory';
import type { Context } from 'hono';
import { getCookie, setCookie, deleteCookie } from 'hono/cookie';
import { gerarTokenSessao } from './crypto';
import type { User } from './db';

export const COOKIE_SESSAO = 'frete_sessao';
const TTL_DIAS = 30;

export type Vars = {
  user: User | null;
  sessionId: string | null;
};

export async function criarSessao(
  db: D1Database,
  userId: number,
  ip: string | null,
  userAgent: string | null,
): Promise<string> {
  const token = gerarTokenSessao();
  const expira = new Date(Date.now() + TTL_DIAS * 24 * 3600 * 1000).toISOString();
  await db
    .prepare('INSERT INTO sessions (id, user_id, expira_em, ip, user_agent) VALUES (?, ?, ?, ?, ?)')
    .bind(token, userId, expira, ip, userAgent)
    .run();
  return token;
}

export function gravarCookieSessao(c: Context, token: string): void {
  setCookie(c, COOKIE_SESSAO, token, {
    httpOnly: true,
    secure: true,
    sameSite: 'Lax',
    path: '/',
    maxAge: TTL_DIAS * 24 * 3600,
  });
}

export async function encerrarSessao(db: D1Database, sessionId: string | null): Promise<void> {
  if (sessionId) {
    await db.prepare('DELETE FROM sessions WHERE id = ?').bind(sessionId).run();
  }
}

export function limparCookieSessao(c: Context): void {
  deleteCookie(c, COOKIE_SESSAO, { path: '/' });
}

// Middleware global: resolve cookie → sessão → usuário em cada request.
// Também faz faxina preguiçosa de sessões expiradas (1% das requisições).
export const middlewareSessao = createMiddleware<{
  Bindings: { DB: D1Database };
  Variables: Vars;
}>(async (c, next) => {
  c.set('user', null);
  c.set('sessionId', null);

  const token = getCookie(c, COOKIE_SESSAO);
  if (token && /^[0-9a-f]{64}$/.test(token)) {
    const row = await c.env.DB
      .prepare(
        `SELECT s.id AS session_id, s.expira_em, u.*
         FROM sessions s JOIN users u ON u.id = s.user_id
         WHERE s.id = ? AND u.ativo = 1`,
      )
      .bind(token)
      .first<(User & { session_id: string; expira_em: string }) | null>();

    if (row) {
      if (new Date(row.expira_em).getTime() > Date.now()) {
        const { session_id, expira_em, ...user } = row;
        c.set('user', user as User);
        c.set('sessionId', session_id);
      } else {
        await c.env.DB.prepare('DELETE FROM sessions WHERE id = ?').bind(token).run();
        deleteCookie(c, COOKIE_SESSAO, { path: '/' });
      }
    }
  }

  if (Math.random() < 0.01) {
    c.executionCtx.waitUntil(
      c.env.DB.prepare("DELETE FROM sessions WHERE expira_em < strftime('%Y-%m-%dT%H:%M:%fZ','now')").run(),
    );
  }

  await next();
});

// Guardas de rota
export function exigirLogin(user: User | null): Response | null {
  if (!user) return new Response(null, { status: 302, headers: { Location: '/login' } });
  return null;
}
