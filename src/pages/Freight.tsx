import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { FreteCard } from './FreteCard';
import {
  dinheiro,
  dataCurta,
  dataHora,
  tempoRelativo,
  distancia,
  distanciaAproximada,
  peso,
  volume,
} from '../lib/format';
import { CUSTO_PADRAO, estimarCusto, type ParametrosCusto, type EstimativaCusto } from '../lib/cost';
import {
  GRUPOS_CARGA,
  VEICULOS,
  dimensoesTexto,
  parseItensForm,
  resumoCarga,
  sugerirVeiculo,
  type AnaliseEncaixe,
  type CargaResumo,
  type SugestaoVeiculo,
} from '../lib/carga';
import type { Previsao } from '../lib/clima';
import type { BidComNome, Category, FreightResumo, Truck, User } from '../lib/db';

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
  totalResultados: number;
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
  totalResultados,
  queryString,
  mensagem,
  erro,
}) => {
  const temFiltros = Boolean(filtros.categoria || filtros.uf || filtros.cidade || filtros.q || filtros.data);
  const linkPagina = (pagina: number) =>
    queryString ? `/fretes?${queryString}&pagina=${pagina}` : `/fretes?pagina=${pagina}`;

  return (
    <Layout
      titulo="Fretes abertos"
      user={user}
      alertas={alertas}
      mensagem={mensagem}
      erro={erro}
      descricao="Veja fretes e mudanças abertos para lance. Filtre por categoria, cidade, UF e data de retirada."
    >
      <header class="pagina-cabecalho">
        <div>
          <span class="eyebrow">Oportunidades na sua rota</span>
          <h1>Fretes abertos para lance</h1>
          <p class="pagina-cabecalho__texto">
            Encontre cargas, compare distâncias e envie seu lance. Quem oferece o melhor preço
            fecha negócio direto com o cliente.
          </p>
        </div>
        {user?.papel === 'transportador' && (
          <a href="/painel" class="btn btn-secundario">
            Meus lances <span aria-hidden="true">→</span>
          </a>
        )}
      </header>

      <form method="get" action="/fretes" class="filtros">
        <label class="campo filtros__campo filtros__campo--busca">
          <span class="campo__rotulo">Buscar</span>
          <input name="q" maxlength={80} value={filtros.q} placeholder="geladeira, sofá, mudança..." />
        </label>
        <label class="campo filtros__campo">
          <span class="campo__rotulo">Categoria</span>
          <select name="categoria">
            <option value="">Todas</option>
            {categorias.map((c) => (
              <option value={String(c.id)} selected={filtros.categoria === String(c.id)}>
                {c.nome}
              </option>
            ))}
          </select>
        </label>
        <label class="campo filtros__campo">
          <span class="campo__rotulo">Cidade</span>
          <input name="cidade" maxlength={100} value={filtros.cidade} placeholder="Curitiba" />
        </label>
        <label class="campo filtros__campo filtros__campo--uf">
          <span class="campo__rotulo">UF</span>
          <input name="uf" maxlength={2} value={filtros.uf} placeholder="PR" data-uf />
        </label>
        <label class="campo filtros__campo">
          <span class="campo__rotulo">Retirada a partir de</span>
          <input type="date" name="data" value={filtros.data} />
        </label>
        <div class="filtros__acoes">
          <button type="submit" class="btn btn-primario">
            Filtrar fretes
          </button>
          {temFiltros && (
            <a href="/fretes" class="btn btn-fantasma">
              Limpar
            </a>
          )}
        </div>
      </form>

      <div class="resultado-topo">
        <p>
          <strong>{totalResultados}</strong>{' '}
          {totalResultados === 1 ? 'frete encontrado' : 'fretes encontrados'}
        </p>
        {totalPaginas > 1 && (
          <span class="auxiliar">
            Página {filtros.pagina} de {totalPaginas}
          </span>
        )}
      </div>

      {fretes.length === 0 ? (
        <div class="estado-vazio">
          <span class="estado-vazio__emoji" aria-hidden="true">🔎</span>
          <h2>Nenhum frete com esses filtros</h2>
          <p class="auxiliar">
            Tente remover algum filtro ou volte mais tarde — novos fretes aparecem todos os dias.
          </p>
          <div class="estado-vazio__acoes">
            <a href="/fretes" class="btn btn-secundario">Limpar filtros</a>
            {user?.papel === 'cliente' && (
              <a href="/novo-frete" class="btn btn-primario">Publicar frete</a>
            )}
          </div>
        </div>
      ) : (
        <div class="grade-fretes">
          {fretes.map((f) => (
            <FreteCard
              frete={f}
              cta={user?.papel === 'transportador' ? 'Ver e dar lance' : 'Ver detalhes'}
            />
          ))}
        </div>
      )}

      {totalPaginas > 1 && (
        <nav class="paginacao" aria-label="Paginação">
          {filtros.pagina > 1 ? (
            <a class="btn btn-fantasma btn-pequeno" href={linkPagina(filtros.pagina - 1)}>
              ← Anterior
            </a>
          ) : (
            <span class="btn btn-fantasma btn-pequeno desabilitado" aria-disabled="true">
              ← Anterior
            </span>
          )}
          <span class="paginacao__atual">
            {filtros.pagina} / {totalPaginas}
          </span>
          {filtros.pagina < totalPaginas ? (
            <a class="btn btn-fantasma btn-pequeno" href={linkPagina(filtros.pagina + 1)}>
              Próxima →
            </a>
          ) : (
            <span class="btn btn-fantasma btn-pequeno desabilitado" aria-disabled="true">
              Próxima →
            </span>
          )}
        </nav>
      )}
    </Layout>
  );
};

// ---------------- Detalhe do frete ----------------

interface DetalheProps {
  user: User | null;
  alertas: number;
  frete: FreightResumo;
  lances: BidComNome[];
  meuLance: BidComNome | null;
  donoDoFrete: boolean;
  previsao?: Previsao | null;
  iaAtiva?: boolean;
  caminhao: Truck | null;
  carga: CargaResumo | null;
  analiseCaminhao: AnaliseEncaixe | null;
  veiculoRecomendado: SugestaoVeiculo | null;
  estimativaCaminhao: EstimativaCusto | null;
  custoInicial?: Partial<ParametrosCusto>;
  mensagem?: string | null;
  erro?: string | null;
}

function rotuloStatus(status: string): string {
  if (status === 'aberto') return 'Aberto para lances';
  if (status === 'encerrado') return 'Encerrado';
  if (status === 'cancelado') return 'Cancelado';
  return status;
}

function rotuloEncaixe(status: string): string {
  if (status === 'cabe') return 'Cabe no seu caminhão';
  if (status === 'limite') return 'Cabe, mas está no limite';
  return 'Não cabe no seu caminhão';
}

function percentualOcupacao(valor: number): number {
  return Math.max(0, Math.min(100, Math.round(valor * 100)));
}

function valorParametro(valor: number): string {
  // <input type="number"> só aceita ponto como separador decimal; usar vírgula
  // aqui faria o navegador descartar o valor e o estimador cair para zero.
  return String(valor);
}

export const DetalheFretePage: FC<DetalheProps> = ({
  user,
  alertas,
  frete,
  lances,
  meuLance,
  donoDoFrete,
  previsao,
  iaAtiva,
  caminhao,
  carga,
  analiseCaminhao,
  veiculoRecomendado,
  estimativaCaminhao,
  custoInicial,
  mensagem,
  erro,
}) => {
  const aberto = frete.status === 'aberto';
  const vencedor = lances.find((l) => l.vencedor === 1);
  const parametrosBase: ParametrosCusto = { ...CUSTO_PADRAO, ...(custoInicial ?? {}) };
  const estimativa = estimarCusto(frete.distancia_km, custoInicial);
  const temMapa = frete.origem_lat != null && frete.origem_lng != null;
  const transportador = user?.papel === 'transportador';

  return (
    <Layout
      titulo={`Frete #${frete.id}`}
      user={user}
      alertas={alertas}
      mapa
      mensagem={mensagem}
      erro={erro}
      descricao={`Frete de ${frete.origem_cidade}/${frete.origem_uf} para ${frete.destino_cidade}/${frete.destino_uf}: ${frete.descricao}`}
    >
      <header class="detalhe-topo">
        <div class="detalhe-topo__conteudo">
          <div class="chips">
            <span class={`status status-${frete.status}`}>{rotuloStatus(frete.status)}</span>
            <span class="chip chip-categoria">{frete.categoria_nome}</span>
            <span class="chip">Frete #{frete.id}</span>
          </div>
          <h1>{frete.descricao}</h1>
          <p class="auxiliar">
            Publicado {tempoRelativo(frete.criado_em)} por{' '}
            <strong>{donoDoFrete ? 'você' : frete.cliente_nome}</strong>
          </p>
        </div>
        {aberto && donoDoFrete && (
          <form method="post" action={`/fretes/${frete.id}/cancelar`} data-confirm="Cancelar este frete? Os transportadores com lance serão avisados.">
            <button type="submit" class="btn btn-perigo btn-pequeno">
              Cancelar frete
            </button>
          </form>
        )}
      </header>

      <div class="detalhe-grade">
        <div class="detalhe-principal">
          <section class="cartao">
            <div class="cartao__cabecalho">
              <h2>Rota do frete</h2>
              {estimativa && (
                <span class="chip chip-verde">
                  ~{estimativa.distanciaRodoviariaKm.toFixed(1).replace('.', ',')} km por estrada
                </span>
              )}
            </div>

            <div class="rota-detalhe">
              <div class="rota-ponto">
                <span class="rota-ponto__marca rota-ponto__marca--origem" aria-hidden="true"></span>
                <div>
                  <small>Origem</small>
                  <strong>{frete.origem_cidade}/{frete.origem_uf}</strong>
                  <p>
                    {frete.origem_rua}, {frete.origem_bairro}
                    <br />
                    CEP {frete.origem_cep}
                  </p>
                </div>
              </div>
              <span class="rota-linha" aria-hidden="true"></span>
              <div class="rota-ponto">
                <span class="rota-ponto__marca rota-ponto__marca--destino" aria-hidden="true"></span>
                <div>
                  <small>Destino</small>
                  <strong>{frete.destino_cidade}/{frete.destino_uf}</strong>
                  <p>
                    {frete.destino_rua}, {frete.destino_bairro}
                    <br />
                    CEP {frete.destino_cep}
                  </p>
                </div>
              </div>
            </div>

            <div
              id="mapa"
              class="mapa"
              data-origem-lat={frete.origem_lat ?? ''}
              data-origem-lng={frete.origem_lng ?? ''}
              data-destino-lat={frete.destino_lat ?? ''}
              data-destino-lng={frete.destino_lng ?? ''}
              data-mapa
              aria-label="Mapa da rota do frete"
            >
              <div class="mapa__vazio">
                {temMapa
                  ? 'Carregando mapa…'
                  : 'Mapa indisponível para este endereço. Use os endereços acima para se orientar.'}
              </div>
            </div>
            {frete.distancia_km != null && (
              <p class="auxiliar mapa__legenda">
                Distância em linha reta: {distancia(frete.distancia_km)} · estimativa rodoviária:{' '}
                {distanciaAproximada(frete.distancia_km)}
              </p>
            )}
          </section>

          {carga && (
            <section class="cartao carga-detalhe">
              <div class="cartao__cabecalho">
                <div>
                  <span class="eyebrow">Carga detalhada</span>
                  <h2>Itens para transportar</h2>
                  <p class="auxiliar">
                    {carga.totalItens} {carga.totalItens === 1 ? 'volume' : 'volumes'} ·{' '}
                    {peso(carga.pesoKg)} · {volume(carga.volumeM3)}
                  </p>
                </div>
                {veiculoRecomendado && (
                  <span
                    class={`chip ${
                      veiculoRecomendado.analise.status === 'limite' ? 'chip-alerta' : 'chip-verde'
                    }`}
                  >
                    {veiculoRecomendado.veiculo.emoji} {veiculoRecomendado.veiculo.nome}
                  </span>
                )}
              </div>

              <ul class="carga-lista">
                {carga.itens.map((item) => (
                  <li class="carga-lista__item">
                    <span class="carga-lista__emoji" aria-hidden="true">
                      {item.emoji}
                    </span>
                    <div class="carga-lista__texto">
                      <strong>
                        {item.quantidade}× {item.nome}
                      </strong>
                      <small>
                        {dimensoesTexto(item)} · {peso(item.pesoKg)} cada
                      </small>
                    </div>
                    {item.fragil && <span class="tag tag-alerta">frágil</span>}
                  </li>
                ))}
              </ul>

              <div class="carga-totais">
                <div>
                  <small>Peso total</small>
                  <strong>{peso(carga.pesoKg)}</strong>
                </div>
                <div>
                  <small>Volume total</small>
                  <strong>{volume(carga.volumeM3)}</strong>
                </div>
                <div>
                  <small>Maior altura</small>
                  <strong>{carga.alturaMaximaCm} cm</strong>
                </div>
              </div>

              {veiculoRecomendado && (
                <div class={`carga-veiculo carga-veiculo--${veiculoRecomendado.analise.status}`}>
                  <span class="carga-veiculo__emoji" aria-hidden="true">
                    {veiculoRecomendado.veiculo.emoji}
                  </span>
                  <div>
                    <small>Menor veículo padrão que comporta a carga</small>
                    <strong>{veiculoRecomendado.veiculo.nome}</strong>
                    <p>
                      {veiculoRecomendado.veiculo.descricao} ·{' '}
                      {dimensoesTexto(veiculoRecomendado.veiculo)} · até{' '}
                      {peso(veiculoRecomendado.veiculo.capacidadeKg)}
                    </p>
                    {veiculoRecomendado.analise.status === 'limite' && (
                      <p class="campo__dica campo__dica--erro">
                        A carga fica no limite deste veículo. Confirme as medidas antes de contratar.
                      </p>
                    )}
                  </div>
                </div>
              )}

              {!veiculoRecomendado && (
                <p class="observacao">
                  A carga passa de uma carreta padrão. Provavelmente serão necessárias duas viagens.
                </p>
              )}

              {transportador &&
                aberto &&
                (caminhao && analiseCaminhao && estimativaCaminhao ? (
                  <div class={`encaixe encaixe--${analiseCaminhao.status}`}>
                    <div class="encaixe__topo">
                      <div>
                        <span class="eyebrow">Conferência com o seu caminhão</span>
                        <h3>{rotuloEncaixe(analiseCaminhao.status)}</h3>
                        <p class="auxiliar">
                          {caminhao.nome} ·{' '}
                          {dimensoesTexto({
                            comprimentoCm: caminhao.comprimento_cm,
                            larguraCm: caminhao.largura_cm,
                            alturaCm: caminhao.altura_cm,
                          })}{' '}
                          · capacidade {peso(caminhao.capacidade_kg)}
                        </p>
                      </div>
                      <span
                        class={`tag ${
                          analiseCaminhao.status === 'cabe'
                            ? 'tag-ok'
                            : analiseCaminhao.status === 'limite'
                              ? 'tag-alerta'
                              : 'tag-perigo'
                        }`}
                      >
                        {analiseCaminhao.status === 'cabe'
                          ? '✓ cabe'
                          : analiseCaminhao.status === 'limite'
                            ? '⚠ no limite'
                            : '✕ não cabe'}
                      </span>
                    </div>

                    <div class="medidores">
                      <div class="medidor">
                        <div class="medidor__topo">
                          <span>Volume ocupado</span>
                          <strong>{percentualOcupacao(analiseCaminhao.ocupacaoVolume)}%</strong>
                        </div>
                        <progress
                          max={100}
                          value={percentualOcupacao(analiseCaminhao.ocupacaoVolume)}
                          aria-label="Ocupação do volume do caminhão"
                        />
                        <small>
                          {volume(carga.volumeM3)} de {volume(analiseCaminhao.volumeVeiculoM3)} ·{' '}
                          {analiseCaminhao.folgaVolumeM3 >= 0
                            ? `sobram ${volume(analiseCaminhao.folgaVolumeM3)}`
                            : 'sem folga'}
                        </small>
                      </div>
                      <div class="medidor">
                        <div class="medidor__topo">
                          <span>Peso ocupado</span>
                          <strong>{percentualOcupacao(analiseCaminhao.ocupacaoPeso)}%</strong>
                        </div>
                        <progress
                          max={100}
                          value={percentualOcupacao(analiseCaminhao.ocupacaoPeso)}
                          aria-label="Ocupação da capacidade de peso do caminhão"
                        />
                        <small>
                          {peso(carga.pesoKg)} de {peso(caminhao.capacidade_kg)} ·{' '}
                          {analiseCaminhao.folgaPesoKg >= 0
                            ? `sobram ${peso(analiseCaminhao.folgaPesoKg)}`
                            : 'acima do limite'}
                        </small>
                      </div>
                    </div>

                    <ul class="encaixe__lista">
                      {analiseCaminhao.motivos.map((motivo) => (
                        <li>{motivo}</li>
                      ))}
                    </ul>

                    {analiseCaminhao.avisos.length > 0 && (
                      <div class="encaixe__avisos">
                        <strong>Atenção no carregamento</strong>
                        <ul>
                          {analiseCaminhao.avisos.map((aviso) => (
                            <li>{aviso}</li>
                          ))}
                        </ul>
                      </div>
                    )}

                    <div class="encaixe__preco">
                      <div>
                        <small>Lance sugerido para o seu caminhão</small>
                        <strong>{dinheiro(estimativaCaminhao.precoSugerido)}</strong>
                        <p>
                          Custo mínimo {dinheiro(estimativaCaminhao.precoMinimo)} · combustível,
                          pedágio, manutenção, ARLA, custo fixo e margem de{' '}
                          {Math.round(caminhao.margem_percentual)}%.
                        </p>
                      </div>
                      <a href="/caminhao" class="btn btn-fantasma btn-pequeno">
                        Ajustar custos
                      </a>
                    </div>

                    {iaAtiva && (
                      <div class="ia-carga">
                        <button
                          type="button"
                          class="btn btn-secundario btn-pequeno"
                          data-ia-carga
                          data-frete-id={String(frete.id)}
                        >
                          <span aria-hidden="true">✨</span> Analisar carga com IA
                        </button>
                        <p class="campo__dica" data-ia-carga-status aria-live="polite"></p>
                        <div class="ia-carga__saida" data-ia-carga-saida hidden></div>
                      </div>
                    )}
                  </div>
                ) : (
                  <div class="convite">
                    <p>
                      <strong>Quer saber se esta carga cabe no seu caminhão?</strong>
                      <span>
                        Cadastre as medidas internas e a capacidade do veículo — é grátis e leva 2
                        minutos.
                      </span>
                    </p>
                    <div class="convite__acoes">
                      <a href="/caminhao" class="btn btn-primario btn-pequeno">
                        Cadastrar meu caminhão
                      </a>
                    </div>
                  </div>
                ))}
            </section>
          )}

          <section class="cartao">
            <div class="cartao__cabecalho">
              <h2>Lances {frete.total_lances > 0 && <small>({frete.total_lances})</small>}</h2>
              {aberto && (
                <span class={`tag ${frete.total_lances > 0 ? 'tag-ok' : ''}`}>
                  {frete.total_lances > 0 ? 'recebendo lances' : 'aguardando lances'}
                </span>
              )}
            </div>

            {vencedor && (
              <div class="vencedor">
                <span class="vencedor__emoji" aria-hidden="true">🏆</span>
                <div>
                  <strong>Lance vencedor: {dinheiro(vencedor.valor)}</strong>
                  {donoDoFrete && <p>{vencedor.transportador_nome}</p>}
                </div>
              </div>
            )}

            {donoDoFrete ? (
              lances.length === 0 ? (
                <div class="estado-vazio estado-vazio--compacto">
                  <span class="estado-vazio__emoji" aria-hidden="true">📨</span>
                  <p>Nenhum lance ainda. Os transportadores foram avisados do seu frete!</p>
                </div>
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
                          <td data-label="Transportador">{l.transportador_nome}</td>
                          <td data-label="Lance">
                            <strong class="preco">{dinheiro(l.valor)}</strong>
                          </td>
                          <td data-label="Mensagem">{l.mensagem || '—'}</td>
                          <td data-label="Quando">
                            <time>{dataHora(l.criado_em)}</time>
                          </td>
                          {aberto && (
                            <td data-label="Ação">
                              {l.vencedor === 1 ? (
                                '🏆 Vencedor'
                              ) : (
                                <form
                                  method="post"
                                  action={`/fretes/${frete.id}/aceitar/${l.id}`}
                                  data-confirm="Aceitar este lance e encerrar o leilão?"
                                >
                                  <button type="submit" class="btn btn-primario btn-pequeno">
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
              <>
                <div class="lance-resumo">
                  <div>
                    <small>Menor lance atual</small>
                    <strong>{frete.menor_lance != null ? dinheiro(frete.menor_lance) : 'Nenhum ainda'}</strong>
                  </div>
                  {meuLance && (
                    <div>
                      <small>Seu lance</small>
                      <strong>{dinheiro(meuLance.valor)}</strong>
                      {meuLance.vencedor === 1 ? (
                        <span class="tag tag-ok">🏆 Você venceu!</span>
                      ) : frete.menor_lance != null && meuLance.valor <= frete.menor_lance ? (
                        <span class="tag tag-ok">ganhando</span>
                      ) : (
                        <span class="tag tag-alerta">superado</span>
                      )}
                    </div>
                  )}
                </div>

                {aberto && user?.papel === 'transportador' && (
                  <form method="post" action={`/fretes/${frete.id}/lances`} class="form-lance">
                    <h3>{meuLance ? 'Melhorar meu lance' : 'Dar um lance'}</h3>
                    <div class="form-grade form-grade--2">
                      <label class="campo">
                        <span class="campo__rotulo">Valor do lance (R$)</span>
                        <input
                          name="valor"
                          required
                          inputmode="decimal"
                          placeholder="950,00"
                          maxlength={12}
                          autocomplete="off"
                          data-moeda
                        />
                      </label>
                      <label class="campo">
                        <span class="campo__rotulo campo__rotulo--acao">
                          Mensagem ao cliente (opcional)
                          {iaAtiva && (
                            <button
                              type="button"
                              class="btn btn-fantasma btn-mini"
                              data-ia="mensagem"
                              data-alvo="mensagem-lance"
                            >
                              <span aria-hidden="true">✨</span> Melhorar com IA
                            </button>
                          )}
                        </span>
                        <input
                          id="mensagem-lance"
                          name="mensagem"
                          maxlength={500}
                          placeholder="Inclui ajudante e cobertura de carga"
                        />
                      </label>
                    </div>
                    <div class="form-acoes">
                      <button type="submit" class="btn btn-primario">
                        Enviar lance
                      </button>
                      {frete.menor_lance != null && (
                        <p class="auxiliar">
                          Seu lance precisa ser menor que {dinheiro(frete.menor_lance)}.
                        </p>
                      )}
                    </div>
                    <p class="campo__dica" data-ia-status aria-live="polite"></p>
                  </form>
                )}

                {aberto && !user && (
                  <div class="convite">
                    <p>
                      <strong>Quer dar um lance neste frete?</strong>
                      <span>Crie sua conta de transportador grátis ou entre na sua conta.</span>
                    </p>
                    <div class="convite__acoes">
                      <a href="/cadastro" class="btn btn-primario btn-pequeno">Criar conta grátis</a>
                      <a href="/login" class="btn btn-fantasma btn-pequeno">Entrar</a>
                    </div>
                  </div>
                )}
                {aberto && user?.papel === 'cliente' && !donoDoFrete && (
                  <p class="auxiliar">Apenas transportadores podem dar lances.</p>
                )}
              </>
            )}
          </section>

          {estimativa && (
            <section
              class="cartao estimador"
              data-estimador
              data-distancia={String(frete.distancia_km)}
              data-inicial={custoInicial ? JSON.stringify(parametrosBase) : undefined}
            >
              <div class="estimador__cabecalho">
                <div>
                  <span class="eyebrow">Ferramenta gratuita</span>
                  <h2>Estimativa de custo do transporte</h2>
                  <p class="auxiliar">
                    Ajuste os valores do seu veículo para descobrir o lance mínimo que cobre os custos
                    e o valor sugerido com margem.
                  </p>
                </div>
                <span class="chip chip-verde">
                  ~{estimativa.distanciaRodoviariaKm.toFixed(1).replace('.', ',')} km
                </span>
              </div>

              <div class="estimador__grade">
                <div class="estimador__parametros">
                  <div class="form-grade form-grade--2">
                    <label class="campo">
                      <span class="campo__rotulo">Consumo (km/L)</span>
                      <input
                        type="number"
                        min="1"
                        step="0.1"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.consumoKmL)}
                        data-custo="consumoKmL"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Diesel (R$/L)</span>
                      <input
                        type="number"
                        min="0.01"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.precoDiesel)}
                        data-custo="precoDiesel"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Pedágio (R$/km)</span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.pedagioPorKm)}
                        data-custo="pedagioPorKm"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Manutenção (R$/km)</span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.manutencaoPorKm)}
                        data-custo="manutencaoPorKm"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">ARLA 32 (R$/km)</span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.arlaPorKm)}
                        data-custo="arlaPorKm"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Custo fixo (R$)</span>
                      <input
                        type="number"
                        min="0"
                        step="10"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.custoFixo)}
                        data-custo="custoFixo"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Margem desejada (%)</span>
                      <input
                        type="number"
                        min="0"
                        max="100"
                        step="1"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.margemPercentual)}
                        data-custo="margemPercentual"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Fator estrada</span>
                      <input
                        type="number"
                        min="1"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.fatorRodovia)}
                        data-custo="fatorRodovia"
                      />
                    </label>
                  </div>
                  <p class="campo__dica">
                    {custoInicial
                      ? 'Os valores do seu caminhão vêm preenchidos. Ajustes aqui valem só para esta simulação.'
                      : 'Os valores ficam salvos neste navegador. Nenhum dado é enviado para fora.'}
                  </p>
                </div>

                <div class="estimador__resultado" aria-live="polite">
                  <span class="estimador__rotulo">Lance sugerido</span>
                  <strong class="estimador__preco" data-saida="precoSugerido">
                    {dinheiro(estimativa.precoSugerido)}
                  </strong>
                  <span class="estimador__minimo">
                    custo mínimo: <b data-saida="precoMinimo">{dinheiro(estimativa.precoMinimo)}</b>
                  </span>
                  <dl class="estimador__linhas">
                    <div>
                      <dt>Distância rodoviária</dt>
                      <dd data-saida="distanciaRodoviariaKm">
                        {estimativa.distanciaRodoviariaKm.toFixed(1).replace('.', ',')} km
                      </dd>
                    </div>
                    <div>
                      <dt>Combustível</dt>
                      <dd data-saida="combustivel">{dinheiro(estimativa.combustivel)}</dd>
                    </div>
                    <div>
                      <dt>Pedágio</dt>
                      <dd data-saida="pedagio">{dinheiro(estimativa.pedagio)}</dd>
                    </div>
                    <div>
                      <dt>Manutenção</dt>
                      <dd data-saida="manutencao">{dinheiro(estimativa.manutencao)}</dd>
                    </div>
                    <div>
                      <dt>ARLA 32</dt>
                      <dd data-saida="arla">{dinheiro(estimativa.arla)}</dd>
                    </div>
                    <div>
                      <dt>Custo fixo</dt>
                      <dd data-saida="custoFixo">{dinheiro(estimativa.custoFixo)}</dd>
                    </div>
                    <div class="estimador__linhas-total">
                      <dt>Custo total</dt>
                      <dd data-saida="custoTotal">{dinheiro(estimativa.custoTotal)}</dd>
                    </div>
                    <div>
                      <dt>Margem</dt>
                      <dd data-saida="margem">{dinheiro(estimativa.margem)}</dd>
                    </div>
                  </dl>
                  <p class="estimador__aviso">
                    Estimativa aproximada. Confira pedágios, combustível e condições da estrada antes
                    de fechar o negócio.
                  </p>
                </div>
              </div>
            </section>
          )}
        </div>

        <aside class="detalhe-lateral">
          <section class="cartao">
            <h2>Detalhes da retirada</h2>
            <dl class="detalhes">
              <div>
                <dt><span aria-hidden="true">📅</span> Data</dt>
                <dd>
                  {dataCurta(frete.data_retirada)}
                  {frete.hora_retirada && ` às ${frete.hora_retirada}`}
                </dd>
              </div>
              <div>
                <dt><span aria-hidden="true">🧑‍🔧</span> Ajudantes</dt>
                <dd>{frete.ajudantes === 0 ? 'Não precisa' : `${frete.ajudantes}`}</dd>
              </div>
              <div>
                <dt><span aria-hidden="true">🏷️</span> Categoria</dt>
                <dd>{frete.categoria_nome}</dd>
              </div>
              {donoDoFrete && frete.preco_maximo != null && (
                <div>
                  <dt><span aria-hidden="true">🔒</span> Seu preço máximo</dt>
                  <dd>{dinheiro(frete.preco_maximo)} <small>(só você vê)</small></dd>
                </div>
              )}
            </dl>

            {frete.comentario && (
              <div class="observacao">
                <strong>Observações do cliente</strong>
                <p>{frete.comentario}</p>
              </div>
            )}

            {previsao && (
              <div class="clima">
                <span class="clima__emoji" aria-hidden="true">{previsao.emoji}</span>
                <div>
                  <strong>Previsão para a retirada</strong>
                  <p>
                    {previsao.descricao} · {previsao.minima}° a {previsao.maxima}°
                  </p>
                  <small>{previsao.chuvaPercentual}% de chance de chuva</small>
                </div>
              </div>
            )}
          </section>

          <section class="cartao cartao--dica">
            <span class="destaque__icone" aria-hidden="true">🛡️</span>
            <h2>Combine com segurança</h2>
            <p class="auxiliar">
              Prefira combinar por escrito os detalhes da retirada, o valor e o que está incluído
              no transporte. O pagamento acontece direto entre as partes.
            </p>
            <ul class="lista-check">
              <li>Confirme o veículo e a equipe</li>
              <li>Combine horário e local exatos</li>
              <li>Guarde a conversa e o comprovante</li>
            </ul>
          </section>
        </aside>
      </div>
    </Layout>
  );
};

// ---------------- Publicar frete ----------------

interface NovoFreteProps {
  user: User;
  alertas: number;
  categorias: Category[];
  valores: Record<string, string>;
  iaAtiva?: boolean;
  erro?: string | null;
}

export const NovoFretePage: FC<NovoFreteProps> = ({
  user,
  alertas,
  categorias,
  valores,
  iaAtiva,
  erro,
}) => {
  const v = (campo: string) => valores[campo] ?? '';
  const hoje = new Date().toISOString().slice(0, 10);
  const itensSelecionados = parseItensForm(valores);
  const resumo = resumoCarga(itensSelecionados);
  const sugestao = sugerirVeiculo(resumo);

  return (
    <Layout
      titulo="Publicar frete"
      user={user}
      alertas={alertas}
      mapa
      erro={erro}
      descricao="Publique seu frete ou mudança grátis e receba lances de transportadores."
    >
      <header class="pagina-cabecalho pagina-cabecalho--form">
        <div>
          <span class="eyebrow">Leva poucos minutos</span>
          <h1>Publicar um frete</h1>
          <p class="pagina-cabecalho__texto">
            Quanto mais detalhes, melhores são os lances. Você pode revisar tudo antes de publicar.
          </p>
        </div>
        <a href="/painel" class="btn btn-fantasma btn-pequeno">
          ← Voltar ao painel
        </a>
      </header>

      <form method="post" action="/fretes" class="form-publicar">
        <section class="cartao form-secao">
          <div class="form-secao__cabecalho">
            <span class="form-secao__numero">1</span>
            <div>
              <h2>O que vamos transportar?</h2>
              <p class="auxiliar">Escolha a categoria e descreva a carga.</p>
            </div>
          </div>

          <label class="campo">
            <span class="campo__rotulo">Categoria</span>
            <select name="category_id" required>
              <option value="">Escolha uma categoria…</option>
              {categorias.map((c) => (
                <option value={String(c.id)} selected={v('category_id') === String(c.id)}>
                  {c.nome}
                </option>
              ))}
            </select>
          </label>

          <label class="campo">
            <span class="campo__rotulo campo__rotulo--acao">
              Breve descrição
              {iaAtiva && (
                <button
                  type="button"
                  class="btn btn-fantasma btn-mini"
                  data-ia="descricao"
                  data-alvo="descricao-frete"
                >
                  <span aria-hidden="true">✨</span> Melhorar com IA
                </button>
              )}
            </span>
            <input
              id="descricao-frete"
              name="descricao"
              required
              minlength={5}
              maxlength={255}
              placeholder="Ex.: Apartamento de 2 quartos, com geladeira e sofá"
              value={v('descricao')}
            />
            <span class="campo__dica">Resuma o volume e os itens principais.</span>
          </label>

          <label class="campo">
            <span class="campo__rotulo">Observações <small>(opcional)</small></span>
            <textarea name="comentario" maxlength={500} rows={3} placeholder="Ex.: preciso de ajudante para descer 3 andares sem elevador.">
              {v('comentario')}
            </textarea>
          </label>
          <p class="campo__dica" data-ia-status aria-live="polite"></p>
        </section>

        <section class="cartao form-secao">
          <div class="form-secao__cabecalho">
            <span class="form-secao__numero">2</span>
            <div>
              <h2>De onde para onde?</h2>
              <p class="auxiliar">Digite o CEP e preenchemos o endereço automaticamente.</p>
            </div>
          </div>

          <div class="form-grade form-grade--2 form-enderecos">
            <fieldset class="endereco">
              <legend>
                <span class="endereco__marca endereco__marca--origem" aria-hidden="true"></span>
                Origem
              </legend>
              <label class="campo">
                <span class="campo__rotulo">CEP</span>
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
              <label class="campo">
                <span class="campo__rotulo">Rua e número</span>
                <input name="origem_rua" required maxlength={255} placeholder="Rua das Flores, 123" value={v('origem_rua')} />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Bairro</span>
                <input name="origem_bairro" required maxlength={255} placeholder="Centro" value={v('origem_bairro')} />
              </label>
              <div class="form-grade form-grade--cidade">
                <label class="campo">
                  <span class="campo__rotulo">Cidade</span>
                  <input name="origem_cidade" required maxlength={255} placeholder="Curitiba" value={v('origem_cidade')} />
                </label>
                <label class="campo">
                  <span class="campo__rotulo">UF</span>
                  <input name="origem_uf" required maxlength={2} placeholder="PR" value={v('origem_uf')} data-uf />
                </label>
              </div>
            </fieldset>

            <fieldset class="endereco">
              <legend>
                <span class="endereco__marca endereco__marca--destino" aria-hidden="true"></span>
                Destino
              </legend>
              <label class="campo">
                <span class="campo__rotulo">CEP</span>
                <input
                  name="destino_cep"
                  required
                  data-cep="destino"
                  inputmode="numeric"
                  placeholder="01310-100"
                  maxlength={9}
                  value={v('destino_cep')}
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Rua e número</span>
                <input name="destino_rua" required maxlength={255} placeholder="Av. Paulista, 1000" value={v('destino_rua')} />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Bairro</span>
                <input name="destino_bairro" required maxlength={255} placeholder="Bela Vista" value={v('destino_bairro')} />
              </label>
              <div class="form-grade form-grade--cidade">
                <label class="campo">
                  <span class="campo__rotulo">Cidade</span>
                  <input name="destino_cidade" required maxlength={255} placeholder="São Paulo" value={v('destino_cidade')} />
                </label>
                <label class="campo">
                  <span class="campo__rotulo">UF</span>
                  <input name="destino_uf" required maxlength={2} placeholder="SP" value={v('destino_uf')} data-uf />
                </label>
              </div>
            </fieldset>
          </div>

          <div class="mapa-preview">
            <div id="mapa-form" class="mapa mapa--preview" data-mapa-form>
              <div class="mapa__vazio">
                Preencha os CEPs de origem e destino para visualizar a rota.
              </div>
            </div>
            <p class="campo__dica" data-mapa-form-status>
              O mapa é uma prévia aproximada e aparece depois que os dois CEPs forem encontrados.
            </p>
          </div>
        </section>

        <section
          class="cartao form-secao"
          data-montador-carga
          data-veiculos={JSON.stringify(VEICULOS)}
        >
          <div class="form-secao__cabecalho">
            <span class="form-secao__numero">3</span>
            <div>
              <h2>
                Monte sua carga <span class="tag">opcional</span>
              </h2>
              <p class="auxiliar">
                Escolha os itens e as quantidades. A gente estima o peso, o volume e o menor
                veículo que cabe — e o transportador confere com o caminhão dele antes do lance.
              </p>
            </div>
          </div>

          <div class="catalogo-carga">
            {GRUPOS_CARGA.map((grupo, indice) => (
              <details class="catalogo-grupo" open={indice === 0}>
                <summary>
                  <span>{grupo.nome}</span>
                  <small>{grupo.itens.length} itens</small>
                </summary>
                <div class="catalogo-lista">
                  {grupo.itens.map((item) => {
                    const quantidade = Math.min(
                      20,
                      Math.max(0, Math.floor(Number(valores[`item_${item.id}`]) || 0)),
                    );
                    return (
                      <div
                        class="item-catalogo"
                        data-item-carga
                        data-item-id={item.id}
                        data-peso={String(item.pesoKg)}
                        data-comprimento={String(item.comprimentoCm)}
                        data-largura={String(item.larguraCm)}
                        data-altura={String(item.alturaCm)}
                      >
                        <span class="item-catalogo__emoji" aria-hidden="true">
                          {item.emoji}
                        </span>
                        <div class="item-catalogo__info">
                          <strong>{item.nome}</strong>
                          <small>
                            {dimensoesTexto(item)} · {peso(item.pesoKg)}
                          </small>
                        </div>
                        <div class="stepper">
                          <button
                            type="button"
                            class="stepper__botao"
                            data-stepper="menos"
                            aria-label={`Diminuir ${item.nome}`}
                          >
                            −
                          </button>
                          <input
                            class="stepper__campo"
                            type="number"
                            name={`item_${item.id}`}
                            min="0"
                            max="20"
                            step="1"
                            inputmode="numeric"
                            placeholder="0"
                            aria-label={`Quantidade de ${item.nome}`}
                            value={quantidade > 0 ? String(quantidade) : ''}
                            data-qtd
                          />
                          <button
                            type="button"
                            class="stepper__botao"
                            data-stepper="mais"
                            aria-label={`Aumentar ${item.nome}`}
                          >
                            +
                          </button>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </details>
            ))}
          </div>

          <div class="carga-resumo" data-carga-resumo aria-live="polite">
            <div class="carga-resumo__numeros">
              <div>
                <small>Itens</small>
                <strong data-carga-total-itens>{resumo.totalItens}</strong>
              </div>
              <div>
                <small>Peso estimado</small>
                <strong data-carga-peso>{peso(resumo.pesoKg)}</strong>
              </div>
              <div>
                <small>Volume estimado</small>
                <strong data-carga-volume>{volume(resumo.volumeM3)}</strong>
              </div>
            </div>
            <div
              class={`carga-resumo__veiculo ${
                sugestao
                  ? `carga-resumo__veiculo--${sugestao.analise.status}`
                  : 'carga-resumo__veiculo--vazio'
              }`}
              data-carga-veiculo-caixa
            >
              <span class="carga-resumo__emoji" aria-hidden="true" data-carga-veiculo-emoji>
                {sugestao?.veiculo.emoji ?? '🚚'}
              </span>
              <div>
                <small>Veículo recomendado</small>
                <strong data-carga-veiculo>{sugestao?.veiculo.nome ?? 'Selecione os itens'}</strong>
                <p data-carga-veiculo-obs>
                  {sugestao
                    ? `${sugestao.veiculo.descricao} · ${dimensoesTexto(
                        sugestao.veiculo,
                      )} · até ${peso(sugestao.veiculo.capacidadeKg)}`
                    : resumo.totalItens > 0
                      ? 'A carga passa de uma carreta padrão — provavelmente serão duas viagens.'
                      : 'Opcional: monte a carga para estimar peso, volume e veículo.'}
                </p>
              </div>
            </div>
            <p class="campo__dica">
              É uma estimativa com medidas médias de mercado. Você pode editar as quantidades ou
              deixar em branco se preferir descrever a carga só no texto.
            </p>
          </div>
        </section>

        <section class="cartao form-secao">
          <div class="form-secao__cabecalho">
            <span class="form-secao__numero">4</span>
            <div>
              <h2>Quando e como?</h2>
              <p class="auxiliar">Informe a data de retirada e os detalhes que ajudam no lance.</p>
            </div>
          </div>

          <div class="form-grade form-grade--3">
            <label class="campo">
              <span class="campo__rotulo">Data da retirada</span>
              <input type="date" name="data_retirada" required min={hoje} value={v('data_retirada')} />
            </label>
            <label class="campo">
              <span class="campo__rotulo">Hora <small>(opcional)</small></span>
              <input type="time" name="hora_retirada" value={v('hora_retirada')} />
            </label>
            <label class="campo">
              <span class="campo__rotulo">Ajudantes necessários</span>
              <input type="number" name="ajudantes" min={0} max={10} value={v('ajudantes') || '0'} />
            </label>
          </div>

          <label class="campo">
            <span class="campo__rotulo">Preço máximo <small>(opcional — só você vê)</small></span>
            <input
              name="preco_maximo"
              inputmode="decimal"
              maxlength={12}
              placeholder="1.500,00"
              value={v('preco_maximo')}
              data-moeda
            />
            <span class="campo__dica">
              Use como referência interna. Os transportadores não veem esse valor.
            </span>
          </label>
        </section>

        <div class="form-publicar__acoes">
          <p class="auxiliar">
            Ao publicar, o frete fica aberto para lances. Você decide quando aceitar e pode cancelar
            enquanto estiver aberto.
          </p>
          <button type="submit" class="btn btn-primario btn-grande">
            Publicar e abrir leilão
          </button>
        </div>
      </form>
    </Layout>
  );
};
