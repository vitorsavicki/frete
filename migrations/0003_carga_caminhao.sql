-- Montador de carga e perfil do caminhão do transportador.

-- Itens escolhidos no formulário (JSON com nome, peso e dimensões) e totais
-- desnormalizados para facilitar listagens e conferências.
ALTER TABLE freight ADD COLUMN carga_itens TEXT;
ALTER TABLE freight ADD COLUMN carga_peso_kg REAL;
ALTER TABLE freight ADD COLUMN carga_volume_m3 REAL;

-- Um caminhão por transportador; o perfil pode ser editado a qualquer momento.
CREATE TABLE IF NOT EXISTS trucks (
  id                 INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id            INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
  nome               TEXT NOT NULL,
  tipo               TEXT NOT NULL CHECK (tipo IN ('fiorino','van','tres_quartos','bau','carreta','outro')),
  comprimento_cm     REAL NOT NULL CHECK (comprimento_cm > 0),
  largura_cm         REAL NOT NULL CHECK (largura_cm > 0),
  altura_cm          REAL NOT NULL CHECK (altura_cm > 0),
  capacidade_kg      REAL NOT NULL CHECK (capacidade_kg > 0),
  consumo_km_l       REAL NOT NULL DEFAULT 8 CHECK (consumo_km_l > 0),
  diesel_preco       REAL NOT NULL DEFAULT 6.3 CHECK (diesel_preco > 0),
  custo_fixo         REAL NOT NULL DEFAULT 0 CHECK (custo_fixo >= 0),
  margem_percentual  REAL NOT NULL DEFAULT 20 CHECK (margem_percentual >= 0 AND margem_percentual <= 100),
  criado_em          TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now')),
  atualizado_em      TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now'))
);
