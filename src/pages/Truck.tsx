import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import {
  PRECO_COMBUSTIVEL_REFERENCIA,
  TAXA_BASE_POR_TIPO,
  parametrosParaVeiculo,
} from '../lib/cost';
import { parametrosComReferencias, type Referencias } from '../lib/referencias';
import { VEICULOS, dimensoesTexto, rotuloTipoVeiculo, volumeVeiculoM3 } from '../lib/carga';
import { volume as formatarVolume } from '../lib/format';
import type { Truck, User } from '../lib/db';

interface CaminhaoProps {
  user: User;
  alertas: number;
  caminhao: Truck | null;
  valores: Record<string, string>;
  referencias?: Referencias | null;
  mensagem?: string | null;
  erro?: string | null;
}

function metros(cm: number | undefined): string {
  if (cm == null || !Number.isFinite(cm)) return '';
  return String(Math.round(cm * 100) / 10000);
}

export const CaminhaoPage: FC<CaminhaoProps> = ({
  user,
  alertas,
  caminhao,
  valores,
  referencias,
  mensagem,
  erro,
}) => {
  const v = (campo: string, padrao = '') => valores[campo] ?? padrao;
  const tipo = v('tipo', caminhao?.tipo ?? 'fiorino');
  const padroes = referencias
    ? parametrosComReferencias(tipo, {}, referencias)
    : parametrosParaVeiculo(tipo);
  const precosCombustivel = referencias?.combustivel ?? PRECO_COMBUSTIVEL_REFERENCIA;
  const taxasBase = referencias?.taxaBasePorTipo ?? TAXA_BASE_POR_TIPO;
  const combustivel = v(
    'tipo_combustivel',
    caminhao?.tipo_combustivel ?? padroes.tipoCombustivel,
  );
  const volumeInicial = caminhao
    ? volumeVeiculoM3({
        comprimentoCm: caminhao.comprimento_cm,
        larguraCm: caminhao.largura_cm,
        alturaCm: caminhao.altura_cm,
        capacidadeKg: caminhao.capacidade_kg,
      })
    : 0;

  return (
    <Layout
      titulo="Meu caminhão"
      user={user}
      alertas={alertas}
      mensagem={mensagem}
      erro={erro}
      descricao="Cadastre as medidas internas e a capacidade do seu caminhão para saber se a carga de cada mudança cabe antes de enviar o orçamento."
    >
      <header class="pagina-cabecalho painel-topo">
        <div>
          <span class="eyebrow">Perfil do transportador</span>
          <h1>{caminhao ? 'Meu caminhão' : 'Cadastre seu caminhão'}</h1>
          <p class="pagina-cabecalho__texto">
            Com as medidas internas e a capacidade, a gente confere se a carga de cada mudança cabe
            no seu veículo e mostra uma referência de custo. O valor do orçamento é sempre você quem
            define.
          </p>
        </div>
        <div class="painel-topo__acoes">
          <a href="/fretes" class="btn btn-secundario">
            Ver mudanças abertas
          </a>
          <a href="/painel" class="btn btn-fantasma btn-pequeno">
            ← Voltar ao painel
          </a>
        </div>
      </header>

      <div class="painel-grade">
        <div class="painel-coluna">
          <form
            method="post"
            action="/caminhao"
            class="cartao formulario"
            data-form-caminhao
            data-veiculos={JSON.stringify(VEICULOS)}
            data-referencias={JSON.stringify({
              combustivel: precosCombustivel,
              taxaBasePorTipo: taxasBase,
            })}
            data-taxas={JSON.stringify(taxasBase)}
          >
            <div class="cartao__cabecalho">
              <div>
                <h2>Dados do veículo</h2>
                <p class="auxiliar">Meça a parte interna do baú ou da carroceria, não a externa.</p>
              </div>
              <span class="chip">{caminhao ? 'Perfil salvo' : 'Novo perfil'}</span>
            </div>

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Nome do caminhão</span>
                <input
                  name="nome"
                  required
                  minlength={2}
                  maxlength={80}
                  placeholder="Ex.: Fiorino do João"
                  value={v('nome', caminhao?.nome ?? '')}
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Tipo</span>
                <select name="tipo" data-veiculo-tipo>
                  {VEICULOS.map((veiculo) => (
                    <option value={veiculo.id} selected={tipo === veiculo.id}>
                      {veiculo.nome}
                    </option>
                  ))}
                  <option value="outro" selected={tipo === 'outro'}>
                    Outro / personalizado
                  </option>
                </select>
              </label>
            </div>

            <div class="form-grade form-grade--3">
              <label class="campo">
                <span class="campo__rotulo">Comprimento (m)</span>
                <input
                  name="comprimento"
                  type="number"
                  required
                  min="1"
                  max="20"
                  step="0.01"
                  inputmode="decimal"
                  placeholder="1,45"
                  value={v('comprimento', metros(caminhao?.comprimento_cm))}
                  data-veiculo-medida
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Largura (m)</span>
                <input
                  name="largura"
                  type="number"
                  required
                  min="1"
                  max="4"
                  step="0.01"
                  inputmode="decimal"
                  placeholder="1,30"
                  value={v('largura', metros(caminhao?.largura_cm))}
                  data-veiculo-medida
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Altura (m)</span>
                <input
                  name="altura"
                  type="number"
                  required
                  min="1"
                  max="5"
                  step="0.01"
                  inputmode="decimal"
                  placeholder="1,20"
                  value={v('altura', metros(caminhao?.altura_cm))}
                  data-veiculo-medida
                />
              </label>
            </div>

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Capacidade de carga (kg)</span>
                <input
                  name="capacidade_kg"
                  type="number"
                  required
                  min="100"
                  max="40000"
                  step="10"
                  inputmode="numeric"
                  placeholder="650"
                  value={v('capacidade_kg', caminhao ? String(caminhao.capacidade_kg) : '')}
                  data-veiculo-capacidade
                />
              </label>
              <div class="campo">
                <span class="campo__rotulo">Volume interno calculado</span>
                <span class="medida-volume" data-veiculo-volume>
                  {volumeInicial > 0 ? formatarVolume(volumeInicial) : '—'}
                </span>
                <span class="campo__dica" data-veiculo-dica>
                  O volume é calculado automaticamente com as medidas acima.
                </span>
              </div>
            </div>

            <div class="form-grade form--acoes-veiculo">
              <button type="button" class="btn btn-fantasma btn-pequeno" data-veiculo-padrao>
                Usar medidas típicas do tipo escolhido
              </button>
            </div>

            <div class="form-secao__divisor">
              <h3>Combustível e custos por km</h3>
              <p class="auxiliar">
                Diesel ou gasolina? Escolha o combustível do seu veículo. Os valores abaixo são
                pontos de partida editáveis — use o que você realmente paga.
              </p>
            </div>

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Combustível</span>
                <select name="tipo_combustivel" data-combustivel-tipo>
                  <option value="diesel" selected={combustivel === 'diesel'}>
                    Diesel (usa ARLA 32)
                  </option>
                  <option value="gasolina" selected={combustivel === 'gasolina'}>
                    Gasolina / etanol
                  </option>
                </select>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Preço do combustível (R$/L)</span>
                <input
                  name="preco_combustivel"
                  type="number"
                  required
                  min="1"
                  max="20"
                  step="0.01"
                  inputmode="decimal"
                  value={v(
                    'preco_combustivel',
                    caminhao
                      ? String(caminhao.diesel_preco)
                      : String(
                          combustivel === 'gasolina'
                            ? precosCombustivel.gasolina
                            : precosCombustivel.diesel,
                        ),
                  )}
                  data-combustivel-preco
                />
                <span class="campo__dica">
                  Referência editável:{' '}
                  <b data-combustivel-referencia-texto>
                    {combustivel === 'gasolina'
                      ? precosCombustivel.gasolina
                      : precosCombustivel.diesel}
                  </b>{' '}
                  por litro.{' '}
                  <button type="button" class="link-mini" data-combustivel-referencia>
                    Usar referência
                  </button>
                </span>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Consumo médio (km/L)</span>
                <input
                  name="consumo_km_l"
                  type="number"
                  required
                  min="1"
                  max="30"
                  step="0.1"
                  inputmode="decimal"
                  value={v(
                    'consumo_km_l',
                    caminhao ? String(caminhao.consumo_km_l) : String(padroes.consumoKmL),
                  )}
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Pedágio médio (R$/km)</span>
                <input
                  name="pedagio_por_km"
                  type="number"
                  required
                  min="0"
                  max="50"
                  step="0.01"
                  inputmode="decimal"
                  value={v(
                    'pedagio_por_km',
                    caminhao ? String(caminhao.pedagio_por_km) : String(padroes.pedagioPorKm),
                  )}
                />
                <span class="campo__dica">Média por km; deixe 0 se a rota não tem pedágio.</span>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Manutenção e desgaste (R$/km)</span>
                <input
                  name="manutencao_por_km"
                  type="number"
                  required
                  min="0"
                  max="50"
                  step="0.01"
                  inputmode="decimal"
                  value={v(
                    'manutencao_por_km',
                    caminhao
                      ? String(caminhao.manutencao_por_km)
                      : String(padroes.manutencaoPorKm),
                  )}
                />
              </label>
              <label class="campo" data-campo-arla>
                <span class="campo__rotulo">ARLA 32 (R$/km)</span>
                <input
                  name="arla_por_km"
                  type="number"
                  min="0"
                  max="50"
                  step="0.01"
                  inputmode="decimal"
                  value={v(
                    'arla_por_km',
                    caminhao ? String(caminhao.arla_por_km) : String(padroes.arlaPorKm),
                  )}
                  data-arla
                />
                <span class="campo__dica">Só para diesel. Na gasolina o campo é ignorado.</span>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Custo fixo por frete (R$)</span>
                <input
                  name="custo_fixo"
                  type="number"
                  required
                  min="0"
                  max="100000"
                  step="10"
                  inputmode="decimal"
                  value={v(
                    'custo_fixo',
                    caminhao ? String(caminhao.custo_fixo) : String(padroes.custoFixo),
                  )}
                />
                <span class="campo__dica">Alimentação, diária, estacionamento etc.</span>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Margem desejada (%)</span>
                <input
                  name="margem_percentual"
                  type="number"
                  required
                  min="0"
                  max="100"
                  step="1"
                  inputmode="decimal"
                  value={v(
                    'margem_percentual',
                    caminhao
                      ? String(caminhao.margem_percentual)
                      : String(padroes.margemPercentual),
                  )}
                />
              </label>
            </div>

            <div class="form-secao__divisor">
              <h3>Saída, serviço e ajudantes</h3>
              <p class="auxiliar">
                A taxa de saída cobre o deslocamento até a origem e o tempo de carga. As horas de
                serviço e o custo da hora são a sua mão de obra.
              </p>
            </div>

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Taxa de saída (R$)</span>
                <input
                  name="taxa_base"
                  type="number"
                  required
                  min="0"
                  max="100000"
                  step="10"
                  inputmode="decimal"
                  value={v(
                    'taxa_base',
                    caminhao ? String(caminhao.taxa_base) : String(padroes.taxaBase),
                  )}
                />
                <span class="campo__dica" data-taxa-sugestao>
                  Sugestão para {rotuloTipoVeiculo(tipo).toLowerCase()}:{' '}
                  {String(taxasBase[(tipo as keyof typeof taxasBase)] ?? padroes.taxaBase)}.
                  <button type="button" class="link-mini" data-taxa-referencia>
                    usar sugestão
                  </button>
                </span>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Horas de serviço por frete</span>
                <input
                  name="horas_servico"
                  type="number"
                  required
                  min="0"
                  max="48"
                  step="0.5"
                  inputmode="decimal"
                  value={v(
                    'horas_servico',
                    caminhao ? String(caminhao.horas_servico) : String(padroes.horasServico),
                  )}
                />
                <span class="campo__dica">Carga, descarga, espera e deslocamento urbano.</span>
              </label>
              <label class="campo">
                <span class="campo__rotulo">Custo da sua hora (R$)</span>
                <input
                  name="custo_hora"
                  type="number"
                  required
                  min="0"
                  max="10000"
                  step="1"
                  inputmode="decimal"
                  value={v(
                    'custo_hora',
                    caminhao ? String(caminhao.custo_hora) : String(padroes.custoHora),
                  )}
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Custo por ajudante (R$)</span>
                <input
                  name="custo_ajudante"
                  type="number"
                  required
                  min="0"
                  max="10000"
                  step="10"
                  inputmode="decimal"
                  value={v(
                    'custo_ajudante',
                    caminhao ? String(caminhao.custo_ajudante) : String(padroes.custoAjudante),
                  )}
                />
                <span class="campo__dica">O frete informa quantos ajudantes precisa.</span>
              </label>
            </div>

            <div class="form-acoes">
              <button type="submit" class="btn btn-primario">
                {caminhao ? 'Salvar alterações' : 'Cadastrar caminhão'}
              </button>
              <p class="auxiliar">
                O perfil é usado apenas para calcular encaixe e preço — ele não aparece publicamente.
              </p>
            </div>
          </form>
        </div>

        <aside class="painel-lateral">
          {caminhao && (
            <section class="cartao">
              <div class="cartao__cabecalho">
                <div>
                  <h2>Resumo do veículo</h2>
                  <p class="auxiliar">{rotuloTipoVeiculo(caminhao.tipo)}</p>
                </div>
                <span class="chip chip-verde">Ativo</span>
              </div>
              <div class="caminhao-resumo">
                <span class="caminhao-resumo__emoji" aria-hidden="true">🚛</span>
                <div>
                  <strong>{caminhao.nome}</strong>
                  <p>{dimensoesTexto({
                    comprimentoCm: caminhao.comprimento_cm,
                    larguraCm: caminhao.largura_cm,
                    alturaCm: caminhao.altura_cm,
                  })}</p>
                </div>
              </div>
              <dl class="detalhes">
                <div>
                  <dt>Volume interno</dt>
                  <dd>{formatarVolume(volumeInicial)}</dd>
                </div>
                <div>
                  <dt>Capacidade</dt>
                  <dd>{Math.round(caminhao.capacidade_kg)} kg</dd>
                </div>
                <div>
                  <dt>Combustível</dt>
                  <dd>
                    {caminhao.tipo_combustivel === 'gasolina' ? 'Gasolina' : 'Diesel'} ·{' '}
                    {caminhao.diesel_preco.toLocaleString('pt-BR', {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2,
                    })}{' '}
                    /L
                  </dd>
                </div>
                <div>
                  <dt>Consumo</dt>
                  <dd>{caminhao.consumo_km_l} km/L</dd>
                </div>
                <div>
                  <dt>Taxa de saída</dt>
                  <dd>
                    {caminhao.taxa_base.toLocaleString('pt-BR', {
                      style: 'currency',
                      currency: 'BRL',
                    })}
                  </dd>
                </div>
                <div>
                  <dt>Margem desejada</dt>
                  <dd>{caminhao.margem_percentual}%</dd>
                </div>
              </dl>
            </section>
          )}

          <section class="cartao cartao--dica">
            <span class="destaque__icone" aria-hidden="true">📏</span>
            <h2>Como medir</h2>
            <ul class="lista-check">
              <li>Use a parte interna útil, sem contar a espessura das paredes.</li>
              <li>Meça comprimento, largura e altura em metros.</li>
              <li>Na capacidade, use o peso máximo que o veículo pode levar com segurança.</li>
              <li>Se tiver dúvida, arredonde para baixo — é melhor sobrar espaço.</li>
            </ul>
          </section>

          <section class="cartao">
            <h2>Por que cadastrar?</h2>
            <p class="auxiliar">
              O encaixe é conferido antes do orçamento: você vê o percentual de volume e peso
              ocupado, os itens que não cabem e uma referência de preço baseada no seu combustível,
              consumo, pedágio, manutenção, taxa de saída, horas de serviço, ajudantes e margem.
            </p>
            <p class="campo__dica">
              Tudo é calculado no próprio site, de graça. Os valores são demonstrativos e você pode
              ajustar cada um. A análise com IA é opcional e usa a cota gratuita do Cloudflare
              Workers AI.
            </p>
          </section>
        </aside>
      </div>
    </Layout>
  );
};
