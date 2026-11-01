-- ============================================================
-- A escolha não pode passar da retirada.
--
-- A janela de 48 h nasceu contando só do fim das propostas. Se a
-- mudança é marcada para antes disso, o cliente ficaria escolhendo
-- depois do horário da retirada. Aqui os prazos já gravados são
-- limitados ao horário da retirada (fim do dia quando não há hora).
--
-- Novos fretes já nascem limitados pela rota e pela validação; esta
-- migração corrige o que foi publicado antes da regra.
-- ============================================================

UPDATE freight
SET prazo_escolha_em = MIN(
  prazo_escolha_em,
  strftime(
    '%Y-%m-%dT%H:%M:%fZ',
    data_retirada || 'T' || COALESCE(NULLIF(hora_retirada, ''), '23:59') || ':00-03:00'
  )
)
WHERE prazo_escolha_em IS NOT NULL
  AND situacao IN ('aberto', 'em_escolha');
