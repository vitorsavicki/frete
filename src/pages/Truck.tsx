import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { CUSTO_PADRAO } from '../lib/cost';
import { VEICULOS, dimensoesTexto, rotuloTipoVeiculo, volumeVeiculoM3 } from '../lib/carga';
import { volume as formatarVolume } from '../lib/format';
import type { Truck, User } from '../lib/db';

interface CaminhaoProps {
  user: User;
  alertas: number;
  caminhao: Truck | null;
  valores: Record<string, string>;
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
  mensagem,
  erro,
}) => {
  const v = (campo: string, padrao = '') => valores[campo] ?? padrao;
  const tipo = v('tipo', caminhao?.tipo ?? 'fiorino');
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
      descricao="Cadastre as medidas internas e a capacidade do seu caminhão para saber se a carga de cada frete cabe antes de dar o lance."
    >
      <header class="pagina-cabecalho painel-topo">
        <div>
          <span class="eyebrow">Perfil do transportador</span>
          <h1>{caminhao ? 'Meu caminhão' : 'Cadastre seu caminhão'}</h1>
          <p class="pagina-cabecalho__texto">
            Com as medidas internas e a capacidade, a gente confere se a carga de cada frete cabe
            no seu veículo e calcula um lance sugerido com os seus custos.
          </p>
        </div>
        <div class="painel-topo__acoes">
          <a href="/fretes" class="btn btn-secundario">
            Ver fretes abertos
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
              <h3>Custos para calcular o lance</h3>
              <p class="auxiliar">Esses valores ficam salvos no seu perfil e podem ser ajustados a qualquer momento.</p>
            </div>

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Consumo (km/L)</span>
                <input
                  name="consumo_km_l"
                  type="number"
                  required
                  min="1"
                  max="30"
                  step="0.1"
                  inputmode="decimal"
                  value={v('consumo_km_l', caminhao ? String(caminhao.consumo_km_l) : String(CUSTO_PADRAO.consumoKmL))}
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Diesel (R$/L)</span>
                <input
                  name="diesel_preco"
                  type="number"
                  required
                  min="1"
                  max="20"
                  step="0.01"
                  inputmode="decimal"
                  value={v('diesel_preco', caminhao ? String(caminhao.diesel_preco) : String(CUSTO_PADRAO.precoDiesel))}
                />
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
                  value={v('custo_fixo', caminhao ? String(caminhao.custo_fixo) : '0')}
                />
                <span class="campo__dica">Alimentação, diária, ajudante extra etc.</span>
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
                    caminhao ? String(caminhao.margem_percentual) : String(CUSTO_PADRAO.margemPercentual),
                  )}
                />
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
                  <dt>Consumo</dt>
                  <dd>{caminhao.consumo_km_l} km/L</dd>
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
              O encaixe é conferido antes do lance: você vê o percentual de volume e peso ocupado,
              os itens que não cabem e uma sugestão de preço baseada no seu consumo, diesel,
              custo fixo e margem.
            </p>
            <p class="campo__dica">
              Tudo é calculado no próprio site, de graça. A análise com IA é opcional e usa a cota
              gratuita do Cloudflare Workers AI.
            </p>
          </section>
        </aside>
      </div>
    </Layout>
  );
};
