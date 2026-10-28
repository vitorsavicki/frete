-- ============================================================
-- Prazo do leilão reverso.
--
-- A tabela freight nasceu com status('aberto','encerrado','cancelado').
-- Em vez de reconstruir a tabela (o D1 aplica ON DELETE CASCADE e uma
-- reconstrução poderia apagar propostas), adicionamos `situacao` como
-- fonte de verdade do fluxo:
--   aberto -> em_escolha -> encerrado
--   aberto -> sem_lances
--   em_escolha -> expirado
--   aberto/em_escolha -> cancelado
--
-- `status` continua existindo para compatibilidade e para o CHECK antigo.
-- ============================================================

ALTER TABLE freight ADD COLUMN situacao TEXT NOT NULL DEFAULT 'aberto'
  CHECK (situacao IN ('aberto','em_escolha','sem_lances','expirado','encerrado','cancelado'));

ALTER TABLE freight ADD COLUMN encerra_em TEXT;
ALTER TABLE freight ADD COLUMN prazo_escolha_em TEXT;

-- Fretes que já existiam entram no novo fluxo. Os que estavam abertos
-- recebem 48 h de recebimento a partir da publicação; se esse prazo já
-- passou, a próxima visita ou o cron move para em_escolha/sem_lances.
UPDATE freight
SET situacao = CASE
  WHEN status = 'aberto' THEN 'aberto'
  WHEN status = 'encerrado' THEN 'encerrado'
  ELSE 'cancelado'
END;

UPDATE freight
SET encerra_em = strftime('%Y-%m-%dT%H:%M:%fZ', criado_em, '+48 hours')
WHERE status = 'aberto' AND encerra_em IS NULL;

UPDATE freight
SET prazo_escolha_em = strftime('%Y-%m-%dT%H:%M:%fZ', encerra_em, '+48 hours')
WHERE encerra_em IS NOT NULL AND prazo_escolha_em IS NULL;

CREATE INDEX IF NOT EXISTS idx_freight_situacao ON freight(situacao, encerra_em);
