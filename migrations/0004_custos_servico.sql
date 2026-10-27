-- Custos operacionais e combustível do perfil do transportador.
-- Todos os valores são sugestões iniciais: o transportador edita no perfil e
-- o lance final continua sendo decisão dele.

ALTER TABLE trucks ADD COLUMN tipo_combustivel TEXT NOT NULL DEFAULT 'diesel'
  CHECK (tipo_combustivel IN ('diesel','gasolina'));
ALTER TABLE trucks ADD COLUMN pedagio_por_km REAL NOT NULL DEFAULT 0.1
  CHECK (pedagio_por_km >= 0);
ALTER TABLE trucks ADD COLUMN manutencao_por_km REAL NOT NULL DEFAULT 0.45
  CHECK (manutencao_por_km >= 0);
ALTER TABLE trucks ADD COLUMN arla_por_km REAL NOT NULL DEFAULT 0.06
  CHECK (arla_por_km >= 0);
ALTER TABLE trucks ADD COLUMN taxa_base REAL NOT NULL DEFAULT 180
  CHECK (taxa_base >= 0);
ALTER TABLE trucks ADD COLUMN horas_servico REAL NOT NULL DEFAULT 2
  CHECK (horas_servico >= 0);
ALTER TABLE trucks ADD COLUMN custo_hora REAL NOT NULL DEFAULT 45
  CHECK (custo_hora >= 0);
ALTER TABLE trucks ADD COLUMN custo_ajudante REAL NOT NULL DEFAULT 120
  CHECK (custo_ajudante >= 0);

-- Fiorinos e veículos leves normalmente são gasolina; os demais seguem diesel.
UPDATE trucks SET tipo_combustivel = 'gasolina' WHERE tipo = 'fiorino';
