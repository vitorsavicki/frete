import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { FreteCard } from './FreteCard';
import {
  dinheiro,
  dataCurta,
  dataHora,
  tempoRelativo,
  distancia,
  peso,
  volume,
} from '../lib/format';
import { CUSTO_PADRAO, type ParametrosCusto, type EstimativaCusto } from '../lib/cost';
import type { Referencias } from '../lib/referencias';
import {
  GRUPOS_CARGA,
  TAMANHOS_CAIXA,
  VEICULOS,
  dimensoesTexto,
  parseItensForm,
  resumoCarga,
  veiculoPorTipo,
  type AnaliseEncaixe,
  type CargaResumo,
  type SugestaoVeiculo,
} from '../lib/carga';
import { sugerirVeiculoParaPlano } from '../lib/montagem';
import { PRAZOS_RECEBIMENTO, PRAZO_ESCOLHA_HORAS, rotuloSituacao } from '../lib/prazos';
import { PlanoCarga } from './PlanoCarga';
import { MascoteAjuda } from './Ilustracoes';
import type { Previsao } from '../lib/clima';
import type { BidComNome, Category, FreightResumo, Truck, User } from '../lib/db';

// ---------------- Lista de fretes abertos ----------------

// Pequena, média e grande têm um bloco próprio no formulário. As caixas
// especiais (livros, cozinha, arquivo) continuam no catálogo.
const CAIXAS_GENERICAS = new Set(['caixa_pequena', 'caixa_media', 'caixa_grande']);

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
  url?: string;
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
  url,
  mensagem,
  erro,
}) => {
  const temFiltros = Boolean(filtros.categoria || filtros.uf || filtros.cidade || filtros.q || filtros.data);
  const linkPagina = (pagina: number) =>
    queryString ? `/fretes?${queryString}&pagina=${pagina}` : `/fretes?pagina=${pagina}`;
  // Filtros e paginação geram páginas quase idênticas: o canônico aponta para
  // a listagem principal e o robô não indexa as variações.
  const origem = url ? new URL(url).origin : '';
  const canonical = origem ? `${origem}/fretes` : undefined;
  const indexavel = !temFiltros && filtros.pagina <= 1;

  return (
    <Layout
      titulo="Mudanças abertas para orçamento"
      user={user}
      alertas={alertas}
      mensagem={mensagem}
      erro={erro}
      canonical={canonical}
      robots={indexavel ? undefined : 'noindex, follow'}
      descricao={`${totalResultados} mudanças e fretes abertos para orçamento. Filtre por categoria, cidade, UF e data de retirada e envie seu orçamento grátis.`}
    >
      <header class="pagina-cabecalho">
        <div>
          <span class="eyebrow">Mudanças na sua rota</span>
          <h1>Mudanças abertas para orçamento</h1>
          <p class="pagina-cabecalho__texto">
            Encontre cargas, compare distâncias e envie seu orçamento. O cliente escolhe com quem
            fechar, direto, sem taxa de intermediação.
          </p>
        </div>
        {user?.papel === 'transportador' && (
          <a href="/painel" class="btn btn-secundario">
            Meus orçamentos <span aria-hidden="true">→</span>
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
            Filtrar mudanças
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
          {totalResultados === 1 ? 'mudança encontrada' : 'mudanças encontradas'}
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
              cta={user?.papel === 'transportador' ? 'Ver e enviar orçamento' : 'Ver detalhes'}
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
  distanciaRodoviariaKm: number | null;
  rotaGeometria: [number, number][] | null;
  rotaFonte: 'osrm' | 'aproximada' | null;
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
  referencias?: Referencias | null;
  url?: string;
  mensagem?: string | null;
  erro?: string | null;
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
  distanciaRodoviariaKm,
  rotaGeometria,
  rotaFonte,
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
  referencias,
  url,
  mensagem,
  erro,
}) => {
  const situacao = frete.situacao;
  const aberto = situacao === 'aberto';
  const emEscolha = situacao === 'em_escolha';
  const podeCancelar = donoDoFrete && (aberto || emEscolha);
  const podeAceitar = donoDoFrete && emEscolha;
  const vencedor = lances.find((l) => l.vencedor === 1);
  const parametrosBase: ParametrosCusto = { ...CUSTO_PADRAO, ...(custoInicial ?? {}) };
  const estimativa = estimativaCaminhao;
  const temMapa = frete.origem_lat != null && frete.origem_lng != null;
  const transportador = user?.papel === 'transportador';
  const sugestaoLance = estimativa?.precoSugerido ?? null;
  const veiculoPlano = carga
    ? caminhao
      ? {
          comprimentoCm: caminhao.comprimento_cm,
          larguraCm: caminhao.largura_cm,
          alturaCm: caminhao.altura_cm,
          capacidadeKg: caminhao.capacidade_kg,
          nome: caminhao.nome,
          emoji: '🚛',
        }
      : (veiculoRecomendado?.veiculo ?? veiculoPorTipo('carreta'))
    : null;
  // Canônico próprio para cada frete; encerrados/cancelados saem do índice
  // para não competir com os fretes realmente abertos.
  const origem = url ? new URL(url).origin : '';
  const canonical = origem ? `${origem}/fretes/${frete.id}` : undefined;
  const descricao = (
    `Frete de ${frete.origem_cidade}/${frete.origem_uf} para ` +
    `${frete.destino_cidade}/${frete.destino_uf}: ${frete.descricao}`
  ).slice(0, 155);

  return (
    <Layout
      titulo={`Frete #${frete.id} — ${frete.origem_cidade} para ${frete.destino_cidade}`}
      user={user}
      alertas={alertas}
      mapa
      mensagem={mensagem}
      erro={erro}
      canonical={canonical}
      robots={aberto ? undefined : 'noindex, follow'}
      tipo="article"
      descricao={descricao}
    >
      <header class="detalhe-topo">
        <div class="detalhe-topo__conteudo">
          <div class="chips">
            <span class={`status status-${situacao}`}>{rotuloSituacao(situacao)}</span>
            <span class="chip chip-categoria">{frete.categoria_nome}</span>
            <span class="chip">Frete #{frete.id}</span>
          </div>
          <h1>{frete.descricao}</h1>
          <p class="auxiliar">
            Publicado {tempoRelativo(frete.criado_em)} por{' '}
            <strong>{donoDoFrete ? 'você' : frete.cliente_nome}</strong>
          </p>
        </div>
        {podeCancelar && (
          <form method="post" action={`/fretes/${frete.id}/cancelar`} data-confirm="Cancelar esta mudança? Os transportadores que enviaram orçamento serão avisados.">
            <button type="submit" class="btn btn-perigo btn-pequeno">
              Cancelar frete
            </button>
          </form>
        )}
      </header>

      <div class={`prazo-frete prazo-frete--${situacao}`} data-prazo-frete>
        <span class="prazo-frete__icone" aria-hidden="true">
          {aberto ? '⏳' : emEscolha ? '🗳️' : situacao === 'encerrado' ? '✅' : situacao === 'cancelado' ? '❌' : situacao === 'sem_lances' ? '📭' : '⏰'}
        </span>
        <div>
          {aberto && (
            <>
              <strong>Propostas até {dataHora(frete.encerra_em)}</strong>
              <p>
                Depois desse prazo, você terá {PRAZO_ESCOLHA_HORAS} horas para comparar e escolher
                um transportador.
              </p>
            </>
          )}
          {emEscolha && (
            <>
              <strong>Escolha até {dataHora(frete.prazo_escolha_em)}</strong>
              <p>
                As propostas terminaram. Abra a lista abaixo e escolha o transportador que vai fazer
                a mudança.
              </p>
            </>
          )}
          {situacao === 'sem_lances' && (
            <>
              <strong>O prazo terminou sem propostas</strong>
              <p>
                A mudança saiu das listagens públicas. Ajuste a descrição ou publique de novo para
                receber novas propostas.
              </p>
            </>
          )}
          {situacao === 'expirado' && (
            <>
              <strong>O prazo de escolha venceu</strong>
              <p>
                A mudança não foi fechada e saiu das listagens públicas. Publique de novo para
                recomeçar.
              </p>
            </>
          )}
          {situacao === 'encerrado' && (
            <>
              <strong>Mudança fechada</strong>
              <p>
                {vencedor
                  ? `Orçamento aceito${donoDoFrete ? ` de ${vencedor.transportador_nome}` : ''}. Combine os detalhes finais direto com o transportador.`
                  : 'Esta mudança já foi fechada.'}
              </p>
            </>
          )}
          {situacao === 'cancelado' && (
            <>
              <strong>Mudança cancelada</strong>
              <p>Esta mudança não está mais disponível para propostas.</p>
            </>
          )}
        </div>
      </div>

      <div class="detalhe-grade">
        <div class="detalhe-principal">
          <section class="cartao">
            <div class="cartao__cabecalho">
              <h2>Rota do frete</h2>
              {distanciaRodoviariaKm != null && (
                <span class="chip chip-verde">
                  ~{distanciaRodoviariaKm.toFixed(1).replace('.', ',')} km por estrada
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
              data-rota={rotaGeometria ? JSON.stringify(rotaGeometria) : undefined}
              data-rota-fonte={rotaFonte ?? undefined}
              data-mapa
              aria-label="Mapa da rota do frete"
            >
              <div class="mapa__vazio">
                {temMapa
                  ? 'Carregando mapa…'
                  : 'Mapa indisponível para este endereço. Use os endereços acima para se orientar.'}
              </div>
            </div>
            {distanciaRodoviariaKm != null && (
              <p class="auxiliar mapa__legenda">
                Distância rodoviária estimada: {distancia(distanciaRodoviariaKm)}
                {rotaFonte === 'osrm' ? ' (rota real)' : ' (aproximada)'}
                {frete.distancia_km != null && (
                  <> · linha reta: {distancia(frete.distancia_km)}</>
                )}
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

              {veiculoPlano && (
                <PlanoCarga
                  carga={carga}
                  veiculo={veiculoPlano}
                  ajudantes={frete.ajudantes}
                  idPrefixo={`frete-${frete.id}`}
                />
              )}

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
                  A carga não cabe em uma carreta padrão. O plano abaixo mostra quantas viagens são
                  necessárias e o que vai em cada uma.
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
                        <small>Orçamento sugerido para o seu caminhão</small>
                        <strong>{dinheiro(estimativaCaminhao.precoSugerido)}</strong>
                        <p>
                          Custo mínimo {dinheiro(estimativaCaminhao.precoMinimo)} · combustível,
                          pedágio, manutenção e insumos, custo fixo e margem de{' '}
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
              <h2>Orçamentos {frete.total_lances > 0 && <small>({frete.total_lances})</small>}</h2>
              {(aberto || emEscolha) && (
                <span class={`tag ${frete.total_lances > 0 ? 'tag-ok' : ''}`}>
                  {emEscolha
                    ? 'propostas encerradas · escolha do cliente'
                    : frete.total_lances > 0
                      ? 'recebendo orçamentos'
                      : 'aguardando orçamento'}
                </span>
              )}
            </div>

            {vencedor && (
              <div class="vencedor">
                <span class="vencedor__emoji" aria-hidden="true">🏆</span>
                <div>
                  <strong>Orçamento aceito: {dinheiro(vencedor.valor)}</strong>
                  {donoDoFrete && <p>{vencedor.transportador_nome}</p>}
                </div>
              </div>
            )}

            {donoDoFrete ? (
              lances.length === 0 ? (
                <div class="estado-vazio estado-vazio--compacto">
                  <span class="estado-vazio__emoji" aria-hidden="true">
                    {situacao === 'sem_lances' ? '📭' : '📨'}
                  </span>
                  <p>
                    {situacao === 'sem_lances'
                      ? 'O prazo terminou sem propostas. Publique de novo para tentar outra vez.'
                      : situacao === 'expirado'
                        ? 'O prazo de escolha venceu sem um transportador escolhido.'
                        : 'Nenhuma proposta ainda. Sua mudança está publicada e visível para os transportadores.'}
                  </p>
                </div>
              ) : (
                <div class="tabela-wrapper">
                  <table class="tabela">
                    <thead>
                      <tr>
                        <th>Transportador</th>
                        <th>Orçamento</th>
                        <th>Mensagem</th>
                        <th>Quando</th>
                        {podeAceitar && <th></th>}
                      </tr>
                    </thead>
                    <tbody>
                      {lances.map((l) => (
                        <tr class={l.vencedor === 1 ? 'linha-vencedor' : ''}>
                          <td data-label="Transportador">{l.transportador_nome}</td>
                          <td data-label="Orçamento">
                            <strong class="preco">{dinheiro(l.valor)}</strong>
                          </td>
                          <td data-label="Mensagem">{l.mensagem || '—'}</td>
                          <td data-label="Quando">
                            <time>{dataHora(l.criado_em)}</time>
                          </td>
                          {podeAceitar && (
                            <td data-label="Ação">
                              {l.vencedor === 1 ? (
                                '🏆 Vencedor'
                              ) : (
                                <form
                                  method="post"
                                  action={`/fretes/${frete.id}/aceitar/${l.id}`}
                                  data-confirm="Aceitar este orçamento e fechar a mudança?"
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
                    <small>Menor orçamento atual</small>
                    <strong>{frete.menor_lance != null ? dinheiro(frete.menor_lance) : 'Nenhum ainda'}</strong>
                  </div>
                  {meuLance && (
                    <div>
                      <small>Seu orçamento</small>
                      <strong>{dinheiro(meuLance.valor)}</strong>
                      {meuLance.vencedor === 1 ? (
                        <span class="tag tag-ok">🏆 Você venceu!</span>
                      ) : frete.menor_lance != null && meuLance.valor <= frete.menor_lance ? (
                        <span class="tag tag-ok">melhor valor</span>
                      ) : (
                        <span class="tag tag-alerta">superado</span>
                      )}
                    </div>
                  )}
                </div>

                {aberto && user?.papel === 'transportador' && (
                  <form method="post" action={`/fretes/${frete.id}/lances`} class="form-lance">
                    <h3>{meuLance ? 'Enviar novo orçamento' : 'Enviar orçamento'}</h3>
                    <div class="form-grade form-grade--2">
                      <label class="campo">
                        <span class="campo__rotulo">Valor do orçamento (R$)</span>
                        <input
                          name="valor"
                          required
                          inputmode="decimal"
                          placeholder="950,00"
                          maxlength={12}
                          autocomplete="off"
                          data-moeda
                          data-lance-valor
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
                        Enviar orçamento
                      </button>
                      {frete.menor_lance != null && (
                        <p class="auxiliar">
                          Para ficar à frente, seu orçamento precisa ser menor que{' '}
                          {dinheiro(frete.menor_lance)}.
                        </p>
                      )}
                    </div>
                    <p class="campo__dica" data-ia-status aria-live="polite"></p>
                  </form>
                )}

                {aberto && !user && (
                  <div class="convite">
                    <p>
                      <strong>Quer enviar um orçamento para esta mudança?</strong>
                      <span>Crie sua conta de transportador grátis ou entre na sua conta.</span>
                    </p>
                    <div class="convite__acoes">
                      <a href="/cadastro" class="btn btn-primario btn-pequeno">Criar conta grátis</a>
                      <a href="/login" class="btn btn-fantasma btn-pequeno">Entrar</a>
                    </div>
                  </div>
                )}
                {aberto && user?.papel === 'cliente' && !donoDoFrete && (
                  <p class="auxiliar">Só transportadores podem enviar orçamentos.</p>
                )}
              </>
            )}
          </section>

          {estimativa && (
            <section
              class="cartao estimador"
              data-estimador
              data-distancia={valorParametro(estimativa.distanciaRodoviariaKm)}
              data-rodoviaria="1"
              data-inicial={JSON.stringify(parametrosBase)}
              data-referencias={referencias ? JSON.stringify(referencias) : undefined}
              data-ajudantes-frete={String(frete.ajudantes)}
            >
              <div class="estimador__cabecalho">
                <div>
                  <span class="eyebrow">Ferramenta do transportador</span>
                  <h2>Simulador de custo e orçamento</h2>
                  <p class="auxiliar">
                    Ajuste os valores do seu veículo para ver o custo real da viagem, a faixa de
                    mercado e um orçamento de partida.{' '}
                    <strong>O valor final é sempre você quem define.</strong>
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
                      <span class="campo__rotulo">Tipo de veículo</span>
                      <select data-custo="tipoVeiculo">
                        {VEICULOS.map((veiculo) => (
                          <option
                            value={veiculo.id}
                            selected={parametrosBase.tipoVeiculo === veiculo.id}
                          >
                            {veiculo.nome} — {veiculo.descricao}
                          </option>
                        ))}
                        <option value="outro" selected={parametrosBase.tipoVeiculo === 'outro'}>
                          Outro veículo
                        </option>
                      </select>
                      <span class="campo__dica" data-taxa-sugestao>
                        A taxa de saída sugerida acompanha o veículo.
                      </span>
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Combustível</span>
                      <select data-custo="tipoCombustivel" data-combustivel-tipo>
                        <option value="diesel" selected={parametrosBase.tipoCombustivel === 'diesel'}>
                          Diesel
                        </option>
                        <option
                          value="gasolina"
                          selected={parametrosBase.tipoCombustivel === 'gasolina'}
                        >
                          Gasolina / etanol
                        </option>
                      </select>
                      <span class="campo__dica">
                        Fiorino e veículos leves costumam ser gasolina; os pesados, diesel.
                      </span>
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Preço do combustível (R$/L)</span>
                      <input
                        type="number"
                        min="0.01"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.precoCombustivel)}
                        data-custo="precoCombustivel"
                        data-combustivel-preco
                      />
                      <span class="campo__dica">
                        Referência:{' '}
                        <b data-combustivel-referencia-texto>
                          {dinheiro(
                            referencias?.combustivel[parametrosBase.tipoCombustivel] ??
                              parametrosBase.precoCombustivel,
                          )}
                        </b>{' '}
                        <button type="button" class="link-mini" data-combustivel-referencia>
                          usar
                        </button>
                      </span>
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Consumo médio (km/L)</span>
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
                      <span class="campo__rotulo">Pedágio (R$/km)</span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.pedagioPorKm)}
                        data-custo="pedagioPorKm"
                      />
                      <span class="campo__dica">Média por km; use 0 se a rota não tem praça.</span>
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Manutenção e desgaste (R$/km)</span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.manutencaoPorKm)}
                        data-custo="manutencaoPorKm"
                      />
                    </label>
                    <input
                      type="hidden"
                      data-custo="arlaPorKm"
                      value={valorParametro(parametrosBase.arlaPorKm)}
                    />
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
                      <span class="campo__dica">Alimentação, diária, estacionamento etc.</span>
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Taxa de saída (R$)</span>
                      <input
                        type="number"
                        min="0"
                        step="10"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.taxaBase)}
                        data-custo="taxaBase"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Horas de serviço</span>
                      <input
                        type="number"
                        min="0"
                        step="0.5"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.horasServico)}
                        data-custo="horasServico"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Custo da sua hora (R$)</span>
                      <input
                        type="number"
                        min="0"
                        step="1"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.custoHora)}
                        data-custo="custoHora"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Ajudantes neste frete</span>
                      <input
                        type="number"
                        min="0"
                        max="20"
                        step="1"
                        inputmode="numeric"
                        value={valorParametro(parametrosBase.ajudantes)}
                        data-custo="ajudantes"
                      />
                    </label>
                    <label class="campo">
                      <span class="campo__rotulo">Custo por ajudante (R$)</span>
                      <input
                        type="number"
                        min="0"
                        step="10"
                        inputmode="decimal"
                        value={valorParametro(parametrosBase.custoAjudante)}
                        data-custo="custoAjudante"
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
                  </div>
                  <p class="campo__dica">
                    Os valores do seu caminhão vêm preenchidos. Ajustes aqui valem só para esta
                    simulação e não alteram o perfil.
                  </p>
                </div>

                <div class="estimador__resultado" aria-live="polite">
                  <span class="estimador__rotulo">Orçamento de partida</span>
                  <strong class="estimador__preco" data-saida="precoSugerido">
                    {dinheiro(estimativa.precoSugerido)}
                  </strong>
                  <span class="estimador__minimo">
                    custo mínimo: <b data-saida="precoMinimo">{dinheiro(estimativa.precoMinimo)}</b>
                  </span>
                  <p class="estimador__mercado" data-saida="faixaMercado">
                    Mercado ({estimativa.referenciaMercado.rotulo}):{' '}
                    {dinheiro(estimativa.referenciaMercado.minimo)} a{' '}
                    {dinheiro(estimativa.referenciaMercado.maximo)}
                  </p>
                  <p class="estimador__aviso" data-aviso-mercado hidden={!estimativa.abaixoDoMercado}>
                    Pelo custo, este frete ficaria abaixo do piso de mercado. O valor sugerido foi
                    elevado para a faixa mínima — confira a concorrência antes de enviar.
                  </p>
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
                      <dt>Manutenção e insumos</dt>
                      <dd data-saida="manutencao">
                        {dinheiro(estimativa.manutencao + estimativa.arla)}
                      </dd>
                    </div>
                    <div>
                      <dt>Taxa de saída</dt>
                      <dd data-saida="taxaBase">{dinheiro(estimativa.taxaBase)}</dd>
                    </div>
                    <div>
                      <dt>Horas de serviço</dt>
                      <dd data-saida="custoHoras">{dinheiro(estimativa.custoHoras)}</dd>
                    </div>
                    <div>
                      <dt>Ajudantes</dt>
                      <dd data-saida="custoAjudantes">{dinheiro(estimativa.custoAjudantes)}</dd>
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
                    <div>
                      <dt>Custo por km</dt>
                      <dd data-saida="custoPorKm">{dinheiro(estimativa.custoPorKm)}</dd>
                    </div>
                  </dl>
                  {aberto && (
                    <button type="button" class="btn btn-secundario btn-bloco" data-usar-sugestao>
                      Usar este valor no orçamento
                    </button>
                  )}
                  <p class="estimador__aviso">
                    Valores demonstrativos para você decidir. O Quero Mudar Já não impõe preço nem
                    cobra margem: o orçamento é seu.
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
  const sugestao = sugerirVeiculoParaPlano(resumo);
  const veiculoPlano =
    sugestao?.veiculo ??
    (resumo.totalItens === 0 ? veiculoPorTipo('van') : veiculoPorTipo('carreta')) ??
    VEICULOS[1]!;
  const caixasTamanho = v('caixas_tamanho') || 'media';
  const caixasPersonalizada = caixasTamanho === 'personalizada';
  const caixasFragil = v('caixas_fragil') === '1';
  const caixaPadrao = TAMANHOS_CAIXA.find((tamanho) => tamanho.id === caixasTamanho) ?? TAMANHOS_CAIXA[1]!;
  const valorCaixa = (campo: string, padrao: number) => v(campo) || String(padrao);
  const falaMascote =
    resumo.totalItens === 0
      ? 'Comece pelo que é maior.'
      : sugestao
        ? `Sua mudança tem ${volume(resumo.volumeM3)} e ${peso(resumo.pesoKg)}.`
        : 'Essa carga passa de uma carreta.';
  const detalheMascote =
    resumo.totalItens === 0
      ? 'Geladeira, cama e sofá definem o veículo. As caixas entram depois, nos espaços que sobram.'
      : sugestao
        ? `O menor veículo que acomoda todos os volumes é ${sugestao.veiculo.nome}. O desenho abaixo mostra como a carga se organiza.`
        : 'O plano abaixo mostra quantas viagens são necessárias e o que vai em cada uma.';

  return (
    <Layout
      titulo="Publicar minha mudança"
      user={user}
      alertas={alertas}
      mapa
      erro={erro}
      descricao="Publique sua mudança grátis, monte a lista de móveis e receba propostas de transportadores."
    >
      <header class="pagina-cabecalho pagina-cabecalho--form">
        <div>
          <span class="eyebrow">Leva poucos minutos</span>
          <h1>Publicar minha mudança</h1>
          <p class="pagina-cabecalho__texto">
            Conte o que vai levar. Quanto mais detalhes, mais fácil para o transportador entender a
            mudança e enviar uma proposta justa.
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
              <h2>O que vai na mudança?</h2>
              <p class="auxiliar">Escolha a categoria e descreva o que precisa levar.</p>
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
                Monte a mudança <span class="tag">opcional</span>
              </h2>
              <p class="auxiliar">
                Coloque os móveis, eletrodomésticos e caixas. O site calcula o peso, o volume e o
                veículo que cabe — e mostra como a carga se organiza lá dentro.
              </p>
            </div>
          </div>

          <MascoteAjuda
            fala={falaMascote}
            detalhe={detalheMascote}
            tom={resumo.totalItens > 0 && !sugestao ? 'atencao' : 'dica'}
            idPrefixo="mascote-form"
          />

          <div class="caixas-carga" data-caixas>
            <div class="caixas-carga__cabecalho">
              <span class="caixas-carga__icone" aria-hidden="true">📦</span>
              <div>
                <h3>Caixas</h3>
                <p>
                  Informe só a quantidade ou detalhe o tamanho. Se não souber, usamos a medida
                  média de mercado.
                </p>
              </div>
            </div>

            <div class="form-grade form-grade--3">
              <label class="campo">
                <span class="campo__rotulo">Quantas caixas?</span>
                <input
                  type="number"
                  name="caixas_qtd"
                  min="0"
                  max="200"
                  step="1"
                  inputmode="numeric"
                  placeholder="Ex.: 20"
                  value={v('caixas_qtd')}
                  data-caixas-qtd
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Tamanho médio</span>
                <select name="caixas_tamanho" data-caixas-tamanho>
                  {TAMANHOS_CAIXA.map((tamanho) => (
                    <option value={tamanho.id} selected={caixasTamanho === tamanho.id}>
                      {tamanho.rotulo} · {dimensoesTexto(tamanho)} · {peso(tamanho.pesoKg)}
                    </option>
                  ))}
                  <option value="personalizada" selected={caixasPersonalizada}>
                    Outra medida (informar abaixo)
                  </option>
                </select>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Tem item frágil?</span>
                <select name="caixas_fragil" data-caixas-fragil>
                  <option value="0" selected={!caixasFragil}>
                    Não
                  </option>
                  <option value="1" selected={caixasFragil}>
                    Sim (louça, vidro, espelho)
                  </option>
                </select>
              </label>
            </div>

            <div class="caixas-medidas" data-caixas-medidas hidden={!caixasPersonalizada}>
              <p class="campo__dica">
                Informe as medidas de uma caixa padrão. Se algum campo ficar em branco, usamos a
                caixa média.
              </p>
              <div class="form-grade form-grade--4">
                <label class="campo">
                  <span class="campo__rotulo">Comprimento (cm)</span>
                  <input
                    type="number"
                    name="caixas_comprimento"
                    min="10"
                    max="250"
                    step="1"
                    placeholder="50"
                    value={valorCaixa('caixas_comprimento', caixaPadrao.comprimentoCm)}
                  />
                </label>
                <label class="campo">
                  <span class="campo__rotulo">Largura (cm)</span>
                  <input
                    type="number"
                    name="caixas_largura"
                    min="10"
                    max="200"
                    step="1"
                    placeholder="40"
                    value={valorCaixa('caixas_largura', caixaPadrao.larguraCm)}
                  />
                </label>
                <label class="campo">
                  <span class="campo__rotulo">Altura (cm)</span>
                  <input
                    type="number"
                    name="caixas_altura"
                    min="10"
                    max="200"
                    step="1"
                    placeholder="40"
                    value={valorCaixa('caixas_altura', caixaPadrao.alturaCm)}
                  />
                </label>
                <label class="campo">
                  <span class="campo__rotulo">Peso médio (kg)</span>
                  <input
                    type="number"
                    name="caixas_peso"
                    min="1"
                    max="500"
                    step="1"
                    placeholder="15"
                    value={valorCaixa('caixas_peso', caixaPadrao.pesoKg)}
                  />
                </label>
              </div>
            </div>

            <p class="campo__dica">
              Sem medida? A caixa média tem {dimensoesTexto(TAMANHOS_CAIXA[1]!)} e pesa cerca de{' '}
              {peso(TAMANHOS_CAIXA[1]!.pesoKg)}.
            </p>
          </div>

          <div class="catalogo-carga">
            {GRUPOS_CARGA.map((grupo, indice) => {
              const itens = grupo.itens.filter((item) => !CAIXAS_GENERICAS.has(item.id));
              return (
                <details class="catalogo-grupo" open={indice === 0}>
                  <summary>
                    <span>{grupo.nome}</span>
                    <small>{itens.length} itens</small>
                  </summary>
                  <div class="catalogo-lista">
                    {itens.map((item) => {
                    const quantidade = Math.min(
                      20,
                      Math.max(0, Math.floor(Number(valores[`item_${item.id}`]) || 0)),
                    );
                    return (
                      <div
                        class="item-catalogo"
                        data-item-carga
                        data-item-id={item.id}
                        data-nome={item.nome}
                        data-emoji={item.emoji}
                        data-categoria={item.categoria}
                        data-fragil={item.fragil ? '1' : '0'}
                        data-empilhavel={item.empilhavel ? '1' : '0'}
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
              );
            })}
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
                      ? 'A carga não cabe em uma carreta padrão. O plano abaixo mostra quantas viagens são necessárias.'
                      : 'Opcional: monte a carga para estimar peso, volume e veículo.'}
                </p>
              </div>
            </div>
            <p class="campo__dica">
              É uma estimativa com medidas médias de mercado. Você pode editar as quantidades ou
              deixar em branco se preferir descrever a mudança só no texto.
            </p>
          </div>

          <PlanoCarga
            carga={resumo}
            veiculo={veiculoPlano}
            ajudantes={Number(v('ajudantes')) || 0}
            compacto
            idPrefixo="novo-frete"
          />
        </section>

        <section class="cartao form-secao">
          <div class="form-secao__cabecalho">
            <span class="form-secao__numero">4</span>
            <div>
              <h2>Quando e como?</h2>
              <p class="auxiliar">Informe a data de retirada e os detalhes que ajudam no orçamento.</p>
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
            <span class="campo__rotulo">Tempo para receber propostas</span>
            <select name="prazo_recebimento">
              {PRAZOS_RECEBIMENTO.map((horas) => (
                <option value={String(horas)} selected={(v('prazo_recebimento') || '48') === String(horas)}>
                  {horas} horas
                </option>
              ))}
            </select>
            <span class="campo__dica">
              Depois desse prazo, você tem {PRAZO_ESCOLHA_HORAS} horas para comparar e escolher um
              transportador.
            </span>
          </label>

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
            Ao publicar, sua mudança fica visível para transportadores durante o prazo escolhido.
            Depois, você compara as propostas e escolhe com calma. Pode cancelar enquanto estiver
            aberta.
          </p>
          <button type="submit" class="btn btn-primario btn-grande">
            Publicar minha mudança
          </button>
        </div>
      </form>
    </Layout>
  );
};
