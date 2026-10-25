-- ============================================================
-- Frete v2 (Quero Mudar Já) — schema inicial (SQLite / Cloudflare D1)
-- Portado e simplificado do MySQL 2020 (ver legacy/scripts-database).
-- Convenções: snake_case, timestamps em ISO-8601 UTC, booleans 0/1.
-- ============================================================

-- Usuários: clientes, transportadores e admin
-- (legado: tb_pessoa + tb_perfil + tb_status)
CREATE TABLE IF NOT EXISTS users (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  email         TEXT NOT NULL UNIQUE COLLATE NOCASE,
  password_hash TEXT NOT NULL,              -- formato: pbkdf2:iteracoes:salt(b64):hash(b64)
  nome          TEXT NOT NULL,
  sobrenome     TEXT NOT NULL,
  telefone      TEXT,
  cpf_cnpj      TEXT,
  papel         TEXT NOT NULL CHECK (papel IN ('cliente','transportador','admin')),
  ativo         INTEGER NOT NULL DEFAULT 1,
  criado_em     TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now'))
);

-- Sessões opacas e revogáveis — o cookie guarda apenas o id aleatório
CREATE TABLE IF NOT EXISTS sessions (
  id         TEXT PRIMARY KEY,              -- 32 bytes hex aleatórios
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  expira_em  TEXT NOT NULL,
  criado_em  TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now')),
  ip         TEXT,
  user_agent TEXT
);
CREATE INDEX IF NOT EXISTS idx_sessions_expira ON sessions(expira_em);

-- Categorias de mudança/frete (legado: tb_categoria)
CREATE TABLE IF NOT EXISTS categories (
  id   INTEGER PRIMARY KEY AUTOINCREMENT,
  nome TEXT NOT NULL UNIQUE
);

-- Fretes/mudanças publicados pelos clientes.
-- (legado: tb_transporte + tb_endereco_transporte; endereço desnormalizado
-- de propósito: um frete tem exatamente uma origem e um destino, e a
-- normalização em 4 tabelas só adicionava JOINs sem benefício.)
CREATE TABLE IF NOT EXISTS freight (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id       INTEGER NOT NULL REFERENCES users(id),
  category_id   INTEGER NOT NULL REFERENCES categories(id),
  descricao     TEXT NOT NULL,
  comentario    TEXT,
  ajudantes     INTEGER NOT NULL DEFAULT 0,
  preco_maximo  REAL,                       -- teto que o cliente aceita pagar (opcional)
  status        TEXT NOT NULL DEFAULT 'aberto' CHECK (status IN ('aberto','encerrado','cancelado')),
  motivo_cancelamento TEXT,
  data_retirada TEXT NOT NULL,              -- YYYY-MM-DD
  hora_retirada TEXT,                       -- HH:MM
  origem_cep    TEXT NOT NULL,
  origem_rua    TEXT NOT NULL,
  origem_bairro TEXT NOT NULL,
  origem_cidade TEXT NOT NULL,
  origem_uf     TEXT NOT NULL,
  destino_cep   TEXT NOT NULL,
  destino_rua   TEXT NOT NULL,
  destino_bairro TEXT NOT NULL,
  destino_cidade TEXT NOT NULL,
  destino_uf    TEXT NOT NULL,
  origem_lat    REAL,
  origem_lng    REAL,
  destino_lat   REAL,
  destino_lng   REAL,
  criado_em     TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now'))
);
CREATE INDEX IF NOT EXISTS idx_freight_status ON freight(status, criado_em);
CREATE INDEX IF NOT EXISTS idx_freight_user ON freight(user_id);

-- Lances dos transportadores (legado: tb_lance).
-- Leilão reverso: o MENOR lance vence. Um lance por transportador por frete;
-- o transportador pode melhorar seu lance (sempre abaixo do mínimo atual).
CREATE TABLE IF NOT EXISTS bids (
  id               INTEGER PRIMARY KEY AUTOINCREMENT,
  freight_id       INTEGER NOT NULL REFERENCES freight(id) ON DELETE CASCADE,
  transportador_id INTEGER NOT NULL REFERENCES users(id),
  valor            REAL NOT NULL CHECK (valor > 0),
  mensagem         TEXT,
  vencedor         INTEGER NOT NULL DEFAULT 0,
  criado_em        TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now')),
  UNIQUE (freight_id, transportador_id)
);
CREATE INDEX IF NOT EXISTS idx_bids_freight ON bids(freight_id, valor);
CREATE INDEX IF NOT EXISTS idx_bids_transportador ON bids(transportador_id);

-- Alertas in-app (legado: tb_alertaLance / tb_alertaMensagem)
CREATE TABLE IF NOT EXISTS alerts (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  freight_id INTEGER REFERENCES freight(id) ON DELETE CASCADE,
  tipo       TEXT NOT NULL,                 -- novo_lance | lance_superado | voce_venceu | lance_perdido | frete_cancelado
  mensagem   TEXT NOT NULL,
  lido       INTEGER NOT NULL DEFAULT 0,
  criado_em  TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now'))
);
CREATE INDEX IF NOT EXISTS idx_alerts_user ON alerts(user_id, lido);

-- Log de auditoria de ações sensíveis (legado: tb_log)
CREATE TABLE IF NOT EXISTS audit_log (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id     INTEGER REFERENCES users(id) ON DELETE SET NULL,
  acao        TEXT NOT NULL,                -- login | login_falhou | cadastro | publicar_frete | lance | aceitar_lance | cancelar_frete | sair
  entidade    TEXT,
  entidade_id INTEGER,
  meta        TEXT,                         -- JSON com contexto (sem dados sensíveis)
  criado_em   TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now'))
);
CREATE INDEX IF NOT EXISTS idx_audit_user ON audit_log(user_id, criado_em);
