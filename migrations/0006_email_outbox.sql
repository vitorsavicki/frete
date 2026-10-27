-- Fila de e-mail (outbox): garante que uma falha do provedor não perca a
-- mensagem. O envio imediato tenta Cloudflare Email (binding EMAIL) e depois
-- Resend; o que falhar entra aqui e é reprocessado pelo cron do Worker.
--
-- Por que uma fila no D1: o plano grátis do Resend limita 100 e-mails/dia e o
-- binding de e-mail do Cloudflare só entrega para endereços verificados em
-- planos gratuitos. Guardar a mensagem permite tentar de novo mais tarde sem
-- bloquear o cadastro, o lance ou o aceite.

CREATE TABLE IF NOT EXISTS email_outbox (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  destinatario  TEXT NOT NULL,
  assunto       TEXT NOT NULL,
  html          TEXT NOT NULL,
  texto         TEXT NOT NULL,
  status        TEXT NOT NULL DEFAULT 'pendente'
                CHECK (status IN ('pendente','enviado','descartado')),
  tentativas    INTEGER NOT NULL DEFAULT 0,
  ultimo_erro   TEXT,
  proxima_tentativa TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now')),
  criado_em     TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now')),
  enviado_em    TEXT
);

CREATE INDEX IF NOT EXISTS idx_email_outbox_pendente
  ON email_outbox(status, proxima_tentativa);
