import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { dinheiro, dataCurta, dataHora, tempoRelativo, distancia } from '../lib/format';
import type { BidComNome, Category, FreightResumo, User } from '../lib/db';

// ---------------- Lista de fretes abertos ----------------

export interface FiltrosLista {
  categoria: string;
  uf: string;
  cidade: string;
  q: string;
  data: string;
  pagina: number;
}

interface ListaProps {
  user: User | null;
  alertas: number;
  fretes: FreightResumo[];
  categorias: Category[];
  filtros: FiltrosLista;
  totalPaginas: number;
  queryString: string;
  mensagem?: string | null;
  erro?: string | null;
}

export const ListaFretesPage: FC<ListaProps> = ({
  user,
  alertas,
  fretes,
  categorias,
  filtros,
  totalPaginas,
  queryString,
  mensagem,
  erro,
}) => (
  <Layout titulo="Fretes abertos" user={user} alertas={alertas} mensagem={mensagem} erro={erro}>
    <h1>Fretes abertos para lance</h1>

    <form method="get" action="/fretes" class="filtros">
      <label>
        Categoria
        <select name="categoria">
          <option value="">Todas</option>
          {categorias.map((c) => (
            <option value={String(c.id)} selected={filtros.categoria === String(c.id)}>
              {c.nome}
            </option>
          ))}
        </select>
      </label>
      <label>
        UF
        <input name="uf" maxlength={2} value={filtros.uf} placeholder="PR" />
      </label>
      <label>
        Cidade
        <input name="cidade" maxlength={100} value={filtros.cidade} placeholder="Curitiba" />
      </label>
      <label>
        Retirada a partir de
        <input type="date" name="data" value={filtros.data} />
      </label>
      <label>
        Palavra-chave
        <input name="q" maxlength={80} value={filtros.q} placeholder="geladeira, sofá..." />
      </label>
      <button type="submit" class="botao">
        Pesquisar
      </button>
    </form>

    {fretes.length === 0 ? (
      <p class="vazio">
        Nenhum frete aberto com esses filtros.{' '}
        {user?.papel === 'cliente' && <a href="/novo-frete">Publique o primeiro!</a>}
      </p>
    ) : (
      <div class="tabela-wrapper">
        <table class="tabela">
          <thead>
            <tr>
              <th>Publicado</th>
              <th>Frete</th>
              <th>Origem → Destino</th>
              <th>Distância</th>
              <th>Retirada</th>
              <th>Lances</th>
              <th>Menor lance</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {fretes.map((f) => (
              <tr>
                <td>
                  <time>{tempoRelativo(f.criado_em)}</time>
                </td>
                <td>
                  <strong>{f.descricao}</strong>
                  <br />
                  <small class="categoria">{f.categoria_nome}</small>
                </td>
                <td>
                  {f.origem_cidade}/{f.origem_uf} → {f.destino_cidade}/{f.destino_uf}
                </td>
                <td>{distancia(f.distancia_km)}</td>
                <td>{dataCurta(f.data_retirada)}</td>
                <td>{f.total_lances}</td>
                <td>
                  <strong class={f.menor_lance != null ? 'preco' : ''}>
                    {f.menor_lance != null ? dinheiro(f.menor_lance) : '—'}
                  </strong>
                </td>
                <td>
                  <a class="botao botao-pequeno" href={`/fretes/${f.id}`}>
                    {user?.papel === 'transportador' ? 'Dar lance' : 'Detalhes'}
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    )}

    {totalPaginas > 1 && (
      <nav class="paginacao" aria-label="paginação">
        {filtros.pagina > 1 ? (
          <a href={`/fretes?${queryString}&pagina=${filtros.pagina - 1}`}>← Anterior</a>
        ) : (
          <span class="desabilitado">← Anterior</span>
        )}
        <span>
          Página {filtros.pagina} de {totalPaginas}
        </span>
        {filtros.pagina < totalPaginas ? (
          <a href={`/fretes?${queryString}&pagina=${filtros.pagina + 1}`}>Próxima →</a>
        ) : (
          <span class="desabilitado">Próxima →</span>
        )}
      </nav>
    )}
  </Layout>
);

// ---------------- Detalhe do frete ----------------

interface DetalheProps {
  user: User | null;
  alertas: number;
  frete: FreightResumo;
  lances: BidComNome[];
  meuLance: BidComNome | null;
  donoDoFrete: boolean;
  mensagem?: string | null;
  erro?: string | null;
}

export const DetalheFretePage: FC<DetalheProps> = ({
  user,
  alertas,
  frete,
  lances,
  meuLance,
  donoDoFrete,
  mensagem,
  erro,
}) => {
  const aberto = frete.status === 'aberto';
  const vencedor = lances.find((l) => l.vencedor === 1);
  return (
    <Layout titulo={`Frete #${frete.id}`} user={user} alertas={alertas} mapa mensagem={mensagem} erro={erro}>
      <div class="detalhe-cabecalho">
        <div>
          <span class={`status status-${frete.status}`}>{frete.status.toUpperCase()}</span>{' '}
          <span class="categoria">{frete.categoria_nome}</span>
          <h1>{frete.descricao}</h1>
          <p class="auxiliar">
            Publicado {tempoRelativo(frete.criado_em)} por {donoDoFrete ? 'você' : frete.cliente_nome}
          </p>
        </div>
        {aberto && donoDoFrete && (
          <form
            method="post"
            action={`/fretes/${frete.id}/cancelar`}
            onsubmit="return confirm('Cancelar este frete? Os transportadores com lance serão avisados.')"
          >
            <button type="submit" class="botao botao-perigo">
              Cancelar frete
            </button>
          </form>
        )}
      </div>

      <div class="grade-dois">
        <section class="cartao-padrao">
          <h2>Rota</h2>
          <div class="rota-detalhe">
            <div>
              <strong>Origem</strong>
              <p>
                {frete.origem_rua}, {frete.origem_bairro}
                <br />
                {frete.origem_cidade}/{frete.origem_uf} — CEP {frete.origem_cep}
              </p>
            </div>
            <span class="seta" aria-hidden="true">
              →
            </span>
            <div>
              <strong>Destino</strong>
              <p>
                {frete.destino_rua}, {frete.destino_bairro}
                <br />
                {frete.destino_cidade}/{frete.destino_uf} — CEP {frete.destino_cep}
              </p>
            </div>
          </div>
          {frete.distancia_km != null && (
            <p class="auxiliar">Distância em linha reta: {distancia(frete.distancia_km)}</p>
          )}
          <div
            id="mapa"
            data-origem-lat={frete.origem_lat ?? ''}
            data-origem-lng={frete.origem_lng ?? ''}
            data-destino-lat={frete.destino_lat ?? ''}
            data-destino-lng={frete.destino_lng ?? ''}
            aria-label="Mapa da rota"
          />
        </section>

        <section class="cartao-padrao">
          <h2>Detalhes</h2>
          <dl class="detalhes">
            <dt>Data de retirada</dt>
            <dd>
              {dataCurta(frete.data_retirada)}
              {frete.hora_retirada && ` às ${frete.hora_retirada}`}
            </dd>
            <dt>Ajudantes necessários</dt>
            <dd>{frete.ajudantes}</dd>
            {donoDoFrete && frete.preco_maximo != null && (
              <>
                <dt>Seu preço máximo</dt>
                <dd>{dinheiro(frete.preco_maximo)} (só você vê)</dd>
              </>
            )}
            {frete.comentario && (
              <>
                <dt>Observações</dt>
                <dd>{frete.comentario}</dd>
              </>
            )}
          </dl>
        </section>
      </div>

      <section class="cartao-padrao">
        <h2>
          Lances {frete.total_lances > 0 && <small>({frete.total_lances})</small>}
        </h2>

        {vencedor && (
          <p class="flash flash-ok">
            🎉 Lance vencedor: <strong>{dinheiro(vencedor.valor)}</strong>
            {donoDoFrete && ` — ${vencedor.transportador_nome}`}
          </p>
        )}

        {donoDoFrete ? (
          lances.length === 0 ? (
            <p class="vazio">Ainda sem lances. Os transportadores foram avisados do seu frete!</p>
          ) : (
            <div class="tabela-wrapper">
              <table class="tabela">
                <thead>
                  <tr>
                    <th>Transportador</th>
                    <th>Lance</th>
                    <th>Mensagem</th>
                    <th>Quando</th>
                    {aberto && <th></th>}
                  </tr>
                </thead>
                <tbody>
                  {lances.map((l) => (
                    <tr class={l.vencedor === 1 ? 'linha-vencedor' : ''}>
                      <td>{l.transportador_nome}</td>
                      <td>
                        <strong class="preco">{dinheiro(l.valor)}</strong>
                      </td>
                      <td>{l.mensagem || '—'}</td>
                      <td>
                        <time>{dataHora(l.criado_em)}</time>
                      </td>
                      {aberto && (
                        <td>
                          {l.vencedor === 1 ? (
                            '🏆 Vencedor'
                          ) : (
                            <form
                              method="post"
                              action={`/fretes/${frete.id}/aceitar/${l.id}`}
                              onsubmit="return confirm('Aceitar este lance e encerrar o leilão?')"
                            >
                              <button type="submit" class="botao botao-pequeno">
                                Aceitar
                              </button>
                            </form>
                          )}
                        </td>
                      )}
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )
        ) : (
          <p>
            Menor lance atual:{' '}
            <strong class="preco">
              {frete.menor_lance != null ? dinheiro(frete.menor_lance) : 'nenhum ainda'}
            </strong>
            {meuLance && (
              <>
                {' '}
                · Seu lance: <strong>{dinheiro(meuLance.valor)}</strong>{' '}
                {meuLance.vencedor === 1 ? (
                  <span class="tag tag-ok">🏆 Você venceu!</span>
                ) : frete.menor_lance != null && meuLance.valor <= frete.menor_lance ? (
                  <span class="tag tag-ok">ganhando</span>
                ) : (
                  <span class="tag tag-alerta">superado</span>
                )}
              </>
            )}
          </p>
        )}

        {aberto && user?.papel === 'transportador' && !donoDoFrete && (
          <form method="post" action={`/fretes/${frete.id}/lances`} class="form-lance">
            <h3>{meuLance ? 'Melhorar meu lance' : 'Dar um lance'}</h3>
            <div class="dupla">
              <label>
                Valor (R$)
                <input
                  name="valor"
                  required
                  inputmode="decimal"
                  placeholder="950,00"
                  maxlength={12}
                  autocomplete="off"
                />
              </label>
              <label>
                Mensagem ao cliente (opcional)
                <input name="mensagem" maxlength={500} placeholder="Inclui ajudante e cobertura de carga" />
              </label>
            </div>
            <button type="submit" class="botao">
              Enviar lance
            </button>
            {frete.menor_lance != null && (
              <p class="auxiliar">Seu lance precisa ser menor que {dinheiro(frete.menor_lance)}.</p>
            )}
          </form>
        )}

        {aberto && !user && (
          <p class="auxiliar">
            <a href="/cadastro">Cadastre-se como transportador</a> ou <a href="/login">entre</a> para dar um
            lance.
          </p>
        )}
        {aberto && user?.papel === 'cliente' && !donoDoFrete && (
          <p class="auxiliar">Apenas transportadores podem dar lances.</p>
        )}
      </section>
    </Layout>
  );
};

// ---------------- Publicar frete ----------------

interface NovoFreteProps {
  user: User;
  alertas: number;
  categorias: Category[];
  valores: Record<string, string>;
  erro?: string | null;
}

export const NovoFretePage: FC<NovoFreteProps> = ({ user, alertas, categorias, valores, erro }) => {
  const v = (campo: string) => valores[campo] ?? '';
  const hoje = new Date().toISOString().slice(0, 10);
  return (
    <Layout titulo="Publicar frete" user={user} alertas={alertas} erro={erro}>
      <div class="cartao-form cartao-form-largo">
        <h1>Publicar frete</h1>
        <p class="auxiliar">Descreva sua mudação e os transportadores disputam pelo menor preço.</p>
        <form method="post" action="/fretes">
          <label>
            Categoria
            <select name="category_id" required>
              <option value="">Escolha...</option>
              {categorias.map((c) => (
                <option value={String(c.id)} selected={v('category_id') === String(c.id)}>
                  {c.nome}
                </option>
              ))}
            </select>
          </label>

          <label>
            Breve descrição do que precisa mudar
            <input
              name="descricao"
              required
              minlength={5}
              maxlength={255}
              placeholder="Ex.: Apartamento 2 quartos completo"
              value={v('descricao')}
            />
          </label>

          <div class="grade-dois">
            <fieldset>
              <legend>Origem</legend>
              <label>
                CEP
                <input
                  name="origem_cep"
                  required
                  data-cep="origem"
                  inputmode="numeric"
                  placeholder="80000-000"
                  maxlength={9}
                  value={v('origem_cep')}
                />
              </label>
              <label>
                Rua
                <input name="origem_rua" required maxlength={255} value={v('origem_rua')} />
              </label>
              <label>
                Bairro
                <input name="origem_bairro" required maxlength={255} value={v('origem_bairro')} />
              </label>
              <div class="dupla">
                <label>
                  Cidade
                  <input name="origem_cidade" required maxlength={255} value={v('origem_cidade')} />
                </label>
                <label>
                  UF
                  <input name="origem_uf" required maxlength={2} value={v('origem_uf')} />
                </label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Destino</legend>
              <label>
                CEP
                <input
                  name="destino_cep"
                  required
                  data-cep="destino"
                  inputmode="numeric"
                  placeholder="80000-000"
                  maxlength={9}
                  value={v('destino_cep')}
                />
              </label>
              <label>
                Rua
                <input name="destino_rua" required maxlength={255} value={v('destino_rua')} />
              </label>
              <label>
                Bairro
                <input name="destino_bairro" required maxlength={255} value={v('destino_bairro')} />
              </label>
              <div class="dupla">
                <label>
                  Cidade
                  <input name="destino_cidade" required maxlength={255} value={v('destino_cidade')} />
                </label>
                <label>
                  UF
                  <input name="destino_uf" required maxlength={2} value={v('destino_uf')} />
                </label>
              </div>
            </fieldset>
          </div>

          <div class="dupla">
            <label>
              Data da retirada
              <input type="date" name="data_retirada" required min={hoje} value={v('data_retirada')} />
            </label>
            <label>
              Hora (opcional)
              <input type="time" name="hora_retirada" value={v('hora_retirada')} />
            </label>
          </div>

          <div class="dupla">
            <label>
              Ajudantes necessários
              <input type="number" name="ajudantes" min={0} max={10} value={v('ajudantes') || '0'} />
            </label>
            <label>
              Preço máximo (R$, opcional — só você vê)
              <input
                name="preco_maximo"
                inputmode="decimal"
                maxlength={12}
                placeholder="1.500,00"
                value={v('preco_maximo')}
              />
            </label>
          </div>

          <label>
            Observações (opcional)
            <textarea name="comentario" maxlength={500} rows={3}>
              {v('comentario')}
            </textarea>
          </label>

          <button type="submit" class="botao">
            Publicar e abrir leilão
          </button>
        </form>
      </div>
    </Layout>
  );
};
