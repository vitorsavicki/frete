import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { dinheiro, tempoRelativo, distancia } from '../lib/format';
import type { FreightResumo, User } from '../lib/db';

interface HomeProps {
  user: User | null;
  alertas: number;
  fretesRecentes: FreightResumo[];
  totalAbertos: number;
  totalTransportadores: number;
  totalFretes: number;
}

export const HomePage: FC<HomeProps> = ({
  user,
  alertas,
  fretesRecentes,
  totalAbertos,
  totalTransportadores,
  totalFretes,
}) => (
  <Layout titulo="Início" user={user} alertas={alertas}>
    <section class="hero">
      <h1>
        Sua mudança pelo <em>menor preço</em>. <span aria-hidden="true">📦</span>
      </h1>
      <p class="subtitulo">
        Publique seu frete e deixe os transportadores disputarem: em leilão reverso, quem vence é o{' '}
        <strong>menor lance</strong>.
      </p>
      <div class="hero-acoes">
        <a href="/cadastro" class="botao botao-grande">
          Publicar meu frete grátis
        </a>
        <a href="/fretes" class="botao botao-secundario botao-grande">
          Sou transportador
        </a>
      </div>
      <dl class="stats">
        <div>
          <dt>{totalAbertos}</dt>
          <dd>fretes abertos</dd>
        </div>
        <div>
          <dt>{totalTransportadores}</dt>
          <dd>transportadores</dd>
        </div>
        <div>
          <dt>{totalFretes}</dt>
          <dd>fretes publicados</dd>
        </div>
      </dl>
    </section>

    <section>
      <h2>Como funciona</h2>
      <ol class="passos">
        <li>
          <strong>1. Você publica</strong>
          <p>Descreva a mudança: origem, destino, data e até um preço máximo.</p>
        </li>
        <li>
          <strong>2. Eles disputam</strong>
          <p>Transportadores dão lances cada vez menores pelo seu frete.</p>
        </li>
        <li>
          <strong>3. Você escolhe</strong>
          <p>Aceite o lance que preferir e combine direto com o vencedor.</p>
        </li>
      </ol>
    </section>

    {fretesRecentes.length > 0 && (
      <section>
        <h2>Fretes recém-publicados</h2>
        <div class="grade-cartoes">
          {fretesRecentes.map((f) => (
            <a class="cartao" href={`/fretes/${f.id}`}>
              <header>
                <span class="categoria">{f.categoria_nome}</span>
                <time>{tempoRelativo(f.criado_em)}</time>
              </header>
              <h3>{f.descricao}</h3>
              <p class="rota">
                {f.origem_cidade}/{f.origem_uf} → {f.destino_cidade}/{f.destino_uf}
                {f.distancia_km != null && <span> · {distancia(f.distancia_km)}</span>}
              </p>
              <footer>
                <span>{f.total_lances} lance(s)</span>
                <strong>{f.menor_lance != null ? dinheiro(f.menor_lance) : 'sem lances'}</strong>
              </footer>
            </a>
          ))}
        </div>
      </section>
    )}
  </Layout>
);
