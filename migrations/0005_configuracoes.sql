-- Referências de custo editáveis sem deploy.
--
-- A ideia: preço de combustível, pedágio, manutenção, ARLA, taxa de saída e
-- faixas de mercado mudam com o tempo. Em vez de espalhar números pelo código,
-- o site lê esta tabela e expõe tudo em GET /api/referencias.
--
-- Como atualizar em produção (sem tocar no código):
--   npx wrangler d1 execute frete --remote --command \
--     "UPDATE configuracoes SET valor = '{\"diesel\":7.49,\"gasolina\":6.19}', atualizado_em = strftime('%Y-%m-%dT%H:%M:%fZ','now') WHERE chave = 'combustivel'"
--
-- Os valores abaixo são pontos de partida pesquisados no mercado brasileiro
-- (2025/2026). O transportador sempre pode sobrescrever no perfil e no
-- simulador; nada aqui é imposto ao lance final.

CREATE TABLE IF NOT EXISTS configuracoes (
  chave         TEXT PRIMARY KEY,
  valor         TEXT NOT NULL,
  descricao     TEXT,
  atualizado_em TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now'))
);

INSERT OR IGNORE INTO configuracoes (chave, valor, descricao) VALUES
  (
    'combustivel',
    '{"diesel":7.37,"gasolina":6.09}',
    'Preço de referência do litro (R$/L) por tipo de combustível.'
  ),
  (
    'custos',
    '{"pedagioPorKm":0.1,"manutencaoPorKm":0.45,"arlaPorKm":0.06,"horasServico":2,"custoHora":45,"custoAjudante":120,"fatorRodovia":1.25,"custoFixo":0,"margemPercentual":20}',
    'Custos operacionais padrão por km, por hora e por frete.'
  ),
  (
    'taxa_base',
    '{"fiorino":120,"van":180,"tres_quartos":250,"bau":400,"carreta":600,"outro":180}',
    'Taxa de saída sugerida (R$) por tipo de veículo.'
  ),
  (
    'mercado',
    '{"fiorino":{"rotulo":"Fiorino","piso":250,"localMaximo":450,"porKmMin":3.5,"porKmMax":5.5},"van":{"rotulo":"Van","piso":350,"localMaximo":650,"porKmMin":4,"porKmMax":6.5},"tres_quartos":{"rotulo":"Caminhão 3/4","piso":450,"localMaximo":850,"porKmMin":5,"porKmMax":8.5},"bau":{"rotulo":"Caminhão baú","piso":650,"localMaximo":1200,"porKmMin":7,"porKmMax":11},"carreta":{"rotulo":"Carreta","piso":900,"localMaximo":1800,"porKmMin":9,"porKmMax":14},"outro":{"rotulo":"Outro veículo","piso":300,"localMaximo":800,"porKmMin":4,"porKmMax":8}}',
    'Faixa de mercado por veículo: piso local, teto local e R$/km mínimo e máximo.'
  );
