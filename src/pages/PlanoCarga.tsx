import type { FC } from 'hono/jsx';
import {
  dimensoesTexto,
  type CargaResumo,
  type CategoriaCarga,
  type MedidasVeiculo,
} from '../lib/carga';
import { dicasCarregamento, montarPlanoCarga } from '../lib/montagem';

interface PlanoCargaProps {
  carga: CargaResumo;
  veiculo: MedidasVeiculo & { nome?: string; emoji?: string };
  ajudantes?: number;
  compacto?: boolean;
  idPrefixo?: string;
}

const ALTURA_DESENHO = 180;
const LARGURA_MAXIMA = 900;
const TOPO = 74;
const BASE = 48;
const CARGO_X = 88;
const ALTURA_PESSOA_CM = 170;
const ALTURA_PESSOA_DESENHO = 110;

type PapelEquipe = 'cliente' | 'motorista' | 'ajudante';

interface PessoaCarregandoProps {
  x: number;
  pisoY: number;
  papel: PapelEquipe;
  escala: number;
}

const PessoaCarregando: FC<PessoaCarregandoProps> = ({ x, pisoY, papel, escala }) => {
  const camisa = papel === 'cliente' ? '#0f766e' : papel === 'motorista' ? '#1d4ed8' : '#f59e0b';
  const detalhe = papel === 'cliente' ? '#134e4a' : papel === 'motorista' ? '#172554' : '#b45309';
  const rotulo =
    papel === 'cliente' ? 'dono da mudança' : papel === 'motorista' ? 'motorista' : 'ajudante';

  return (
    <g class={`plano-pessoa plano-pessoa--${papel}`} transform={`translate(${x} ${pisoY})`}>
      <g transform={`scale(${escala})`}>
        <ellipse cx="0" cy="2" rx="24" ry="5" class="plano-pessoa__sombra" />
        <path d="M-11 -68l-9 16M11 -68l9 16" class="plano-pessoa__braco" />
        <rect x="-11" y="-78" width="22" height="48" rx="8" fill={camisa} />
        {papel === 'ajudante' && (
          <>
            <rect x="-8" y="-73" width="16" height="21" rx="4" fill="#fef3c7" opacity=".9" />
            <path d="M-11 -64h22" stroke="#fff" stroke-width="2.2" opacity=".85" />
          </>
        )}
        <rect
          x="-19"
          y="-64"
          width="38"
          height="34"
          rx="3"
          fill="#fbbf24"
          stroke="#d97706"
          stroke-width="1.4"
          class="plano-pessoa__caixa"
        />
        <path d="M-19 -52h38M0 -64v34" class="plano-pessoa__fita" />
        <circle cx="-21" cy="-50" r="4.5" fill="#fcd9b6" />
        <circle cx="21" cy="-50" r="4.5" fill="#fcd9b6" />
        <circle cx="0" cy="-94" r="13" fill="#fcd9b6" />
        {papel === 'cliente' ? (
          <path
            d="M-12 -99c2-7 9-11 16-9 4 1 7 4 8 9-4-3-9-4-14-3-4 0-7 2-10 3Z"
            fill="#3f2a1d"
          />
        ) : (
          <>
            <path d="M-13 -99a13 13 0 0 1 26 0Z" fill={detalhe} />
            <path d="M-15 -99h30" stroke={detalhe} stroke-width="3" stroke-linecap="round" />
          </>
        )}
        <rect x="-9" y="-32" width="7" height="32" rx="3.5" fill="#1e293b" />
        <rect x="2" y="-32" width="7" height="32" rx="3.5" fill="#1e293b" />
      </g>
      <text x="0" y="24" text-anchor="middle" class="plano-pessoa__legenda">
        {rotulo}
      </text>
    </g>
  );
};

function classeCategoria(categoria: CategoriaCarga): string {
  if (categoria === 'Móveis') return 'moveis';
  if (categoria === 'Eletrodomésticos') return 'eletrodomesticos';
  if (categoria === 'Caixas e volumes') return 'caixas';
  return 'outros';
}

function cortar(texto: string, limite: number): string {
  if (limite < 4) return '';
  if (texto.length <= limite) return texto;
  return `${texto.slice(0, limite - 1).trimEnd()}…`;
}

/** Nome curto para caber no desenho; o título completo fica no tooltip. */
function rotuloCurto(nome: string): string {
  if (!/^caixa/i.test(nome)) return nome;
  const n = nome.toLowerCase();
  if (n.includes('pequen')) return 'Cx. P';
  if (n.includes('medida')) return 'Cx. medida';
  if (n.includes('médi') || n.includes('medi')) return 'Cx. M';
  if (n.includes('grande')) return 'Cx. G';
  if (n.includes('livro')) return 'Cx. livros';
  if (n.includes('cozinha')) return 'Cx. cozinha';
  if (n.includes('arquivo') || n.includes('escrit')) return 'Cx. arquivo';
  return 'Cx. medida';
}

function numero(valor: number): string {
  return String(Math.round(valor));
}

export const PlanoCarga: FC<PlanoCargaProps> = ({
  carga,
  veiculo,
  ajudantes = 0,
  compacto = false,
  idPrefixo = 'plano',
}) => {
  const montagem = montarPlanoCarga(carga, veiculo);
  const dicas = dicasCarregamento(carga, montagem);
  const vazio = carga.totalItens === 0;
  const totalAjudantes = Math.max(0, Math.floor(ajudantes));
  const papeis: PapelEquipe[] =
    totalAjudantes === 0
      ? ['cliente']
      : [
          'motorista',
          ...Array.from({ length: Math.min(2, totalAjudantes) }, () => 'ajudante' as const),
        ];
  const alturaCm = Math.max(1, veiculo.alturaCm);
  const comprimentoCm = Math.max(1, veiculo.comprimentoCm);
  const larguraCm = Math.max(1, veiculo.larguraCm);
  const escala = Math.min(ALTURA_DESENHO / alturaCm, LARGURA_MAXIMA / comprimentoCm);
  const internaL = Math.max(40, comprimentoCm * escala);
  const internaA = Math.max(24, alturaCm * escala);
  const escalaPessoa = (ALTURA_PESSOA_CM * escala) / ALTURA_PESSOA_DESENHO;
  const meiaPessoa = 24 * escalaPessoa;
  const espacamentoPessoa = 48 * escalaPessoa + 12;
  const pisoY = TOPO + internaA;
  const pessoaBaseX = CARGO_X + internaL + 14 + meiaPessoa;
  const DIR = 14 + meiaPessoa * 2 + espacamentoPessoa * (papeis.length - 1) + 22;
  const largura = CARGO_X + internaL + Math.max(56, DIR);
  const altura = pisoY + BASE;
  const cabH = Math.min(110, Math.max(48, internaA * 0.62));
  const cabY = pisoY - cabH;

  const blocosVisiveis = montagem.blocos.filter((bloco) => bloco.faixa === 0);
  const nomesAbaixo = new Set(
    blocosVisiveis
      .filter((bloco) =>
        blocosVisiveis.some(
          (outro) =>
            outro.chave !== bloco.chave &&
            outro.x === bloco.x &&
            Math.abs(outro.base + outro.altura - bloco.base) < 0.6 &&
            outro.nome === bloco.nome,
        ),
      )
      .map((bloco) => bloco.chave),
  );
  const escalaFaixa = 300 / Math.max(1, larguraCm);
  let acumuladoFaixa = 0;
  const faixasDesenho = montagem.faixas.map((faixa) => {
    const x = acumuladoFaixa * escalaFaixa;
    const larguraFaixa = Math.max(1.5, faixa.profundidadeCm * escalaFaixa - 2);
    acumuladoFaixa += faixa.profundidadeCm;
    return { ...faixa, x, largura: larguraFaixa };
  });
  const sobraLargura = Math.max(0, larguraCm - acumuladoFaixa);
  const sobraDesenho = sobraLargura * escalaFaixa;

  return (
    <section
      class={`plano-carga${compacto ? ' plano-carga--compacto' : ''}`}
      data-plano-carga
      data-plano-prefixo={idPrefixo}
    >
      <header class="plano-carga__cabecalho">
        <div>
          <span class="eyebrow">Carregamento</span>
          <h3 data-plano-titulo>
            {vazio ? 'Seu caminhão, pronto para a mudança' : 'Como a carga se encaixa'}
          </h3>
          <p class="auxiliar">
            Corte lateral do veículo. Pesados na frente, frágeis por cima e caixas numeradas no
            fundo.
          </p>
        </div>
        <span class="chip plano-carga__veiculo" data-plano-veiculo>
          {veiculo.emoji ? `${veiculo.emoji} ` : ''}
          {veiculo.nome ?? 'Veículo'} · {dimensoesTexto(veiculo)}
        </span>
      </header>

      <div class="plano-carga__quadro" data-plano-quadro>
        <svg
          class="plano-carga__svg"
          viewBox={`0 0 ${largura} ${altura}`}
          role="img"
          aria-label={
            vazio
              ? 'Caminhão vazio, pronto para receber a mudança'
              : `Desenho esquemático da carga dentro de ${veiculo.nome ?? 'um caminhão'}, com ${montagem.colocados} volumes acomodados em ${montagem.faixas.length} ${montagem.faixas.length === 1 ? 'faixa' : 'faixas'} de largura`
          }
        >
          <title>Plano de carregamento</title>
          <desc>
            {vazio
              ? 'Escolha os itens da mudança para ver como eles se organizam dentro do veículo.'
              : `${montagem.colocados} volumes posicionados. O corte lateral mostra a faixa 1; as demais faixas aparecem na vista de cima. Itens frágeis aparecem com contorno tracejado e ficam por cima.`}
          </desc>

          <rect
            x={CARGO_X - 8}
            y={TOPO - 8}
            width={internaL + 16}
            height={internaA + 16}
            rx="12"
            class="plano-caminhao__parede"
          />
          <rect
            x={CARGO_X}
            y={TOPO}
            width={internaL}
            height={internaA}
            rx="6"
            class="plano-caminhao__interior"
          />

          <g class="plano-caminhao__cabine">
            <path
              d={`M12 ${pisoY}V${cabY + 18}q0-18 18-18h26q14 0 18 13l8 22v${pisoY - cabY - 35}H12Z`}
              class="plano-caminhao__cabine-corpo"
            />
            <path
              d={`M24 ${cabY + 18}h36q8 0 11 8l7 19H24V${cabY + 18}Z`}
              class="plano-caminhao__vidro"
            />
            <rect x="24" y={pisoY - 12} width={CARGO_X - 34} height="12" rx="3" class="plano-caminhao__para-choque" />
            <circle cx={CARGO_X - 28} cy={pisoY + 10} r="16" class="plano-caminhao__roda" />
            <circle cx={CARGO_X - 28} cy={pisoY + 10} r="6" class="plano-caminhao__aro" />
            <circle cx={CARGO_X + internaL - 28} cy={pisoY + 10} r="16" class="plano-caminhao__roda" />
            <circle cx={CARGO_X + internaL - 28} cy={pisoY + 10} r="6" class="plano-caminhao__aro" />
          </g>

          <path d={`M${CARGO_X} ${pisoY}h${internaL}`} class="plano-caminhao__piso" />
          <path
            d={`M${CARGO_X + internaL} ${TOPO}v${internaA}`}
            class="plano-caminhao__porta"
          />
          <path
            d={`M${CARGO_X + internaL + 7} ${TOPO + 8}l12 18-12 18`}
            class="plano-caminhao__porta-aberta"
          />

          <g class="plano-regua">
            <path d={`M${CARGO_X - 18} ${TOPO}v${internaA}`} />
            <path d={`M${CARGO_X - 23} ${TOPO}h9`} />
            <path d={`M${CARGO_X - 23} ${TOPO + internaA / 2}h9`} />
            <path d={`M${CARGO_X - 23} ${pisoY}h9`} />
            <text x={CARGO_X - 27} y={TOPO + 4} text-anchor="end">
              {numero(alturaCm)}
            </text>
            <text x={CARGO_X - 27} y={TOPO + internaA / 2 + 4} text-anchor="end">
              {numero(alturaCm / 2)}
            </text>
            <text x={CARGO_X - 27} y={pisoY + 4} text-anchor="end">
              0
            </text>
            <text x={CARGO_X - 27} y={TOPO - 12} text-anchor="end" class="plano-regua__unidade">
              cm
            </text>
          </g>

          {vazio && (
            <g class="plano-vazio">
              <rect
                x={CARGO_X + 22}
                y={pisoY - 58}
                width="86"
                height="58"
                rx="6"
                class="plano-vazio__caixa"
              />
              <rect
                x={CARGO_X + 118}
                y={pisoY - 42}
                width="58"
                height="42"
                rx="6"
                class="plano-vazio__caixa"
              />
              <text x={CARGO_X + 26} y={pisoY - 70} class="plano-vazio__texto">
                escolha os itens para montar o carregamento
              </text>
            </g>
          )}

          {blocosVisiveis.map((bloco) => {
            const larguraBloco = Math.max(2, bloco.comprimento * escala - 1);
            const alturaBloco = Math.max(2, bloco.altura * escala - 1);
            const x = CARGO_X + bloco.x * escala;
            const y = TOPO + (internaA - (bloco.base + bloco.altura) * escala);
            const maxChars = Math.floor((larguraBloco - 6) / 5);
            const rotulo = nomesAbaixo.has(bloco.chave) ? '' : cortar(rotuloCurto(bloco.nome), maxChars);
            const mostrarRotulo = rotulo.length > 0 && alturaBloco >= 16 && larguraBloco >= 30;

            return (
              <g
                class={`plano-bloco plano-bloco--${classeCategoria(bloco.categoria)}${
                  bloco.fragil ? ' plano-bloco--fragil' : ''
                }`}
              >
                <title>
                  {bloco.nome}
                  {bloco.fragil ? ' (frágil)' : ''} · base a {numero(bloco.base)} cm · {numero(bloco.altura)} cm de altura
                </title>
                <rect
                  x={x}
                  y={y}
                  width={larguraBloco}
                  height={alturaBloco}
                  rx="3"
                  class="plano-bloco__caixa"
                />
                {mostrarRotulo && (
                  <text
                    x={x + 5}
                    y={y + Math.min(alturaBloco - 6, 14)}
                    class="plano-bloco__nome"
                  >
                    {rotulo}
                  </text>
                )}
                {bloco.fragil && larguraBloco >= 42 && alturaBloco >= 26 && (
                  <text
                    x={x + larguraBloco - 5}
                    y={y + alturaBloco - 6}
                    text-anchor="end"
                    class="plano-bloco__fragil"
                  >
                    frágil
                  </text>
                )}
              </g>
            );
          })}

          {papeis.map((papel, indice) => (
            <PessoaCarregando
              key={`${papel}-${indice}`}
              x={pessoaBaseX + indice * espacamentoPessoa}
              pisoY={pisoY}
              papel={papel}
              escala={escalaPessoa}
            />
          ))}
        </svg>
        <p class="plano-carga__equipe" data-plano-equipe>
          {totalAjudantes === 0
            ? 'Sem ajudante: quem organiza a carga é o dono da mudança.'
            : `Com ${totalAjudantes} ${
                totalAjudantes === 1 ? 'ajudante' : 'ajudantes'
              }: o motorista e a equipe organizam a carga.`}
        </p>

        {!vazio && montagem.faixas.length > 0 && (
          <div class="plano-faixas" data-plano-faixas>
            <div class="plano-faixas__topo">
              <strong>Vista de cima</strong>
              <span>
                {montagem.faixas.length} {montagem.faixas.length === 1 ? 'faixa' : 'faixas'} ·{' '}
                {numero(montagem.larguraUsadaCm)} de {numero(larguraCm)} cm de largura
              </span>
            </div>
            <svg
              viewBox="0 0 300 34"
              class="plano-faixas__svg"
              role="img"
              aria-label={`Vista de cima da carroceria dividida em ${montagem.faixas.length} ${
                montagem.faixas.length === 1 ? 'faixa de largura' : 'faixas de largura'
              }`}
            >
              <title>Divisão da carroceria em faixas de largura</title>
              <rect
                x="0"
                y="4"
                width="300"
                height="22"
                rx="5"
                class="plano-faixas__contorno"
              />
              {faixasDesenho.map((faixa, indice) => (
                <g
                  class={`plano-faixas__faixa plano-faixas__faixa--${indice % 3}${
                    indice === 0 ? ' plano-faixas__faixa--vista' : ''
                  }`}
                >
                  <title>
                    {`Faixa ${indice + 1}: ${faixa.volumes} ${
                      faixa.volumes === 1 ? 'volume' : 'volumes'
                    } em ${numero(faixa.profundidadeCm)} cm de largura`}
                  </title>
                  <rect
                    x={faixa.x + 1}
                    y="6"
                    width={faixa.largura}
                    height="18"
                    rx="3"
                    class="plano-faixas__barra"
                  />
                  {faixa.largura >= 16 && (
                    <text
                      x={faixa.x + 1 + faixa.largura / 2}
                      y="19"
                      text-anchor="middle"
                      class="plano-faixas__numero"
                    >
                      {faixa.volumes}
                    </text>
                  )}
                </g>
              ))}
              {sobraDesenho > 1 && (
                <rect
                  x={acumuladoFaixa * escalaFaixa + 1}
                  y="6"
                  width={Math.max(1, sobraDesenho - 2)}
                  height="18"
                  rx="3"
                  class="plano-faixas__sobra"
                />
              )}
            </svg>
            <p class="plano-faixas__nota">
              {montagem.faixas.length === 1
                ? 'Toda a carga está na faixa 1, que aparece no corte lateral.'
                : 'O corte lateral mostra a faixa 1. As demais completam a largura da carroceria.'}
            </p>
          </div>
        )}
      </div>

      <div class="plano-carga__rodape">
        <div class="plano-carga__resumo">
          <ul class="plano-carga__legenda" aria-label="Legenda das cores">
            <li><i class="plano-cor plano-cor--moveis"></i> Móveis</li>
            <li><i class="plano-cor plano-cor--eletrodomesticos"></i> Eletrodomésticos</li>
            <li><i class="plano-cor plano-cor--caixas"></i> Caixas</li>
            <li><i class="plano-cor plano-cor--outros"></i> Outros</li>
            <li><i class="plano-cor plano-cor--fragil"></i> Frágil</li>
          </ul>
          <p class="plano-carga__numeros" data-plano-numeros hidden={vazio}>
            {!vazio && (
              <>
                {montagem.colocados} de {montagem.totalUnidades}{' '}
                {montagem.totalUnidades === 1 ? 'volume acomodado' : 'volumes acomodados'}
                {montagem.volumesAtras > 0
                  ? ` · ${montagem.volumesAtras} ${
                      montagem.volumesAtras === 1 ? 'na faixa de trás' : 'nas faixas de trás'
                    }`
                  : ''}
                {montagem.fora > 0 ? ` · ${montagem.fora} fora` : ''}
                {` · ${numero(montagem.alturaUsadaCm)} cm de altura usada`}
              </>
            )}
          </p>
        </div>

        <div class="plano-carga__dicas" data-plano-dicas>
          <strong>{vazio ? 'Antes de carregar' : 'Boas práticas para esta carga'}</strong>
          <ul>
            {(vazio
              ? [
                  'Monte a lista de itens para o site calcular peso, volume e veículo.',
                  'Itens pesados vão na frente e no chão. Frágeis vão por cima.',
                  'O desenho é uma prévia: a conferência final é feita no local.',
                ]
              : dicas
            ).map((dica) => (
              <li>{dica}</li>
            ))}
          </ul>
        </div>
      </div>
    </section>
  );
};
