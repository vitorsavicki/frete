import type { FC } from 'hono/jsx';
import { dataCurta, dinheiro, distanciaAproximada, tempoRelativo } from '../lib/format';
import type { FreightResumo } from '../lib/db';

interface FreteCardProps {
  frete: FreightResumo;
  cta?: string;
}

/** Cartão de frete usado na home, na listagem e nos destaques. */
export const FreteCard: FC<FreteCardProps> = ({ frete, cta = 'Ver detalhes' }) => (
  <a class="freight-card" href={`/fretes/${frete.id}`}>
    <div class="freight-card__top">
      <span class="chip chip-categoria">{FreteCardCategoria(frete)}</span>
      <time>{tempoRelativo(frete.criado_em)}</time>
    </div>

    <h3>{frete.descricao}</h3>

    <p class="freight-route">
      <span class="route-dot" aria-hidden="true"></span>
      <span>
        {frete.origem_cidade}/{frete.origem_uf}
      </span>
      <span class="route-arrow" aria-hidden="true">
        →
      </span>
      <span>
        {frete.destino_cidade}/{frete.destino_uf}
      </span>
    </p>

    <div class="freight-card__meta">
      <span>
        <span aria-hidden="true">📅</span> {dataCurta(frete.data_retirada)}
      </span>
      {frete.distancia_km != null && (
        <span>
          <span aria-hidden="true">🛣️</span> {distanciaAproximada(frete.distancia_km)}
        </span>
      )}
    </div>

    <div class="freight-card__footer">
      <span class="freight-card__bids">
        <span aria-hidden="true">🤝</span> {frete.total_lances}{' '}
        {frete.total_lances === 1 ? 'orçamento' : 'orçamentos'}
      </span>
      <span class="freight-card__price">
        {frete.menor_lance != null ? (
          <>
            <small>menor orçamento</small>
            <strong>{dinheiro(frete.menor_lance)}</strong>
          </>
        ) : (
          <small>aguardando orçamento</small>
        )}
      </span>
    </div>

    <span class="freight-card__cta">{cta}</span>
  </a>
);

function FreteCardCategoria(frete: FreightResumo): string {
  return frete.categoria_nome;
}
