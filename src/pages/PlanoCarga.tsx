import type { FC } from 'hono/jsx';
import {
  dimensoesTexto,
  type CargaResumo,
  type CategoriaCarga,
  type MedidasVeiculo,
} from '../lib/carga';
import { peso, volume } from '../lib/format';
import {
  dicasCarregamento,
  montarPlanoCarga,
  planejarViagens,
  type MontagemCarga,
} from '../lib/montagem';

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

/** Cores das três faces de cada volume na vista 3D. */
function coresCategoria(categoria: CategoriaCarga): { topo: string; frente: string; lado: string; traco: string } {
  if (categoria === 'Móveis') {
    return { topo: '#dbeafe', frente: '#93c5fd', lado: '#60a5fa', traco: '#2563eb' };
  }
  if (categoria === 'Eletrodomésticos') {
    return { topo: '#fef3c7', frente: '#fcd34d', lado: '#f59e0b', traco: '#b45309' };
  }
  if (categoria === 'Caixas e volumes') {
    return { topo: '#dcfce7', frente: '#86efac', lado: '#4ade80', traco: '#047857' };
  }
  return { topo: '#f1f5f9', frente: '#cbd5e1', lado: '#94a3b8', traco: '#64748b' };
}

const ISO_COSSENO = Math.cos(Math.PI / 6);
const ISO_SENO = 0.5;

interface PontoIso {
  x: number;
  y: number;
}

function pontoIso(
  x: number,
  y: number,
  z: number,
  origem: PontoIso,
  escala: number,
): PontoIso {
  return {
    x: origem.x + (x - z) * ISO_COSSENO * escala,
    y: origem.y + (x + z) * ISO_SENO * escala - y * escala,
  };
}

function pontosPoly(pontos: PontoIso[]): string {
  return pontos.map((ponto) => `${ponto.x.toFixed(1)},${ponto.y.toFixed(1)}`).join(' ');
}

interface VistaIsoProps {
  montagem: MontagemCarga;
  veiculo: MedidasVeiculo & { nome?: string };
  vazio: boolean;
}

/**
 * Vista isométrica: cada volume vira um cubo com topo, frente e lateral.
 * O desenho mantém as proporções do veículo e usa a mesma montagem do packer,
 * para o cliente enxergar a carga como ela realmente ficaria.
 */
const VistaIso: FC<VistaIsoProps> = ({ montagem, veiculo, vazio }) => {
  const comprimento = Math.max(1, veiculo.comprimentoCm);
  const largura = Math.max(1, veiculo.larguraCm);
  const altura = Math.max(1, veiculo.alturaCm);
  const larguraView = 880;
  const margem = 46;
  const escala = Math.max(
    0.05,
    Math.min(
      (larguraView - margem * 2) / ((comprimento + largura) * ISO_COSSENO),
      430 / ((comprimento + largura) * ISO_SENO + altura),
    ),
  );
  const larguraDesenho = (comprimento + largura) * ISO_COSSENO * escala;
  const alturaDesenho = (comprimento + largura) * ISO_SENO * escala + altura * escala;
  const alturaView = margem * 2 + alturaDesenho;
  const origem: PontoIso = {
    x: (larguraView - larguraDesenho) / 2 + largura * ISO_COSSENO * escala,
    y: (alturaView - alturaDesenho) / 2 + altura * escala,
  };

  const p = (x: number, y: number, z: number) => pontoIso(x, y, z, origem, escala);

  const faixaOffset = new Map<number, number>();
  let acumulado = 0;
  for (const faixa of montagem.faixas) {
    faixaOffset.set(faixa.indice, acumulado);
    acumulado += faixa.profundidadeCm;
  }

  const blocosOrdenados = [...montagem.blocos].sort((a, b) => {
    const za = faixaOffset.get(a.faixa) ?? 0;
    const zb = faixaOffset.get(b.faixa) ?? 0;
    return a.x + za - (b.x + zb);
  });

  const piso = [p(0, 0, 0), p(comprimento, 0, 0), p(comprimento, 0, largura), p(0, 0, largura)];
  const paredeFundo = [
    p(comprimento, 0, 0),
    p(comprimento, altura, 0),
    p(comprimento, altura, largura),
    p(comprimento, 0, largura),
  ];
  const paredeLado = [
    p(0, 0, 0),
    p(comprimento, 0, 0),
    p(comprimento, altura, 0),
    p(0, altura, 0),
  ];
  const frente = p(0, 0, 0);
  const porta = p(comprimento, 0, largura);

  return (
    <svg
      class="plano-iso"
      viewBox={`0 0 ${larguraView.toFixed(0)} ${alturaView.toFixed(0)}`}
      role="img"
      aria-label={
        vazio
          ? 'Vista 3D de um veículo vazio, pronto para receber a mudança'
          : `Vista 3D da carga dentro de ${veiculo.nome ?? 'um veículo'}, com ${montagem.colocados} volumes`
      }
    >
      <title>Vista 3D do carregamento</title>
      <desc>
        Cada caixa representa um volume. A frente fica na parte de cima do desenho e a porta na
        parte de baixo. As faces mais claras são o topo dos volumes.
      </desc>

      <polygon points={pontosPoly(paredeFundo)} class="plano-iso__parede" />
      <polygon points={pontosPoly(paredeLado)} class="plano-iso__parede plano-iso__parede--lado" />
      <polygon points={pontosPoly(piso)} class="plano-iso__piso" />

      {montagem.faixas.map((faixa) => {
        const z = faixaOffset.get(faixa.indice) ?? 0;
        if (z <= 0) return null;
        return (
          <path
            d={`M${p(0, 0, z).x.toFixed(1)} ${p(0, 0, z).y.toFixed(1)}L${p(comprimento, 0, z).x.toFixed(1)} ${p(comprimento, 0, z).y.toFixed(1)}`}
            class="plano-iso__faixa"
          />
        );
      })}

      {blocosOrdenados.map((bloco) => {
        const z0 = faixaOffset.get(bloco.faixa) ?? 0;
        const z1 = z0 + bloco.profundidade;
        const x0 = bloco.x;
        const x1 = bloco.x + bloco.comprimento;
        const y0 = bloco.base;
        const y1 = bloco.base + bloco.altura;
        const cores = coresCategoria(bloco.categoria);
        const topo = [p(x0, y1, z0), p(x1, y1, z0), p(x1, y1, z1), p(x0, y1, z1)];
        const faceFrente = [p(x1, y0, z0), p(x1, y0, z1), p(x1, y1, z1), p(x1, y1, z0)];
        const faceLado = [p(x0, y0, z1), p(x1, y0, z1), p(x1, y1, z1), p(x0, y1, z1)];

        return (
          <g
            class={`plano-iso__bloco plano-bloco--${classeCategoria(bloco.categoria)}${
              bloco.fragil ? ' plano-bloco--fragil' : ''
            }`}
          >
            <title>
              {bloco.nome}
              {bloco.fragil ? ' (frágil)' : ''} · {numero(bloco.comprimento)} ×{' '}
              {numero(bloco.profundidade)} × {numero(bloco.altura)} cm · base a{' '}
              {numero(bloco.base)} cm
            </title>
            <polygon
              points={pontosPoly(faceLado)}
              fill={cores.lado}
              stroke={cores.traco}
              class="plano-iso__face plano-iso__face--lado"
            />
            <polygon
              points={pontosPoly(faceFrente)}
              fill={cores.frente}
              stroke={cores.traco}
              class="plano-iso__face plano-iso__face--frente"
            />
            <polygon
              points={pontosPoly(topo)}
              fill={cores.topo}
              stroke={cores.traco}
              class="plano-iso__face plano-iso__face--topo"
            />
          </g>
        );
      })}

      <path
        d={`M${p(0, altura, 0).x.toFixed(1)} ${p(0, altura, 0).y.toFixed(1)}L${p(comprimento, altura, 0).x.toFixed(1)} ${p(comprimento, altura, 0).y.toFixed(1)}L${p(comprimento, altura, largura).x.toFixed(1)} ${p(comprimento, altura, largura).y.toFixed(1)}`}
        class="plano-iso__contorno"
      />

      <text x={frente.x} y={frente.y - 12} text-anchor="middle" class="plano-iso__rotulo">
        frente (cabine)
      </text>
      <text x={porta.x} y={porta.y + 20} text-anchor="middle" class="plano-iso__rotulo">
        porta
      </text>

      {vazio && (
        <g class="plano-iso__vazio">
          <rect
            x={origem.x - 95}
            y={origem.y - 28}
            width="190"
            height="52"
            rx="8"
            class="plano-iso__vazio-caixa"
          />
          <text x={origem.x} y={origem.y + 4} text-anchor="middle" class="plano-iso__vazio-texto">
            escolha os itens para montar a carga em 3D
          </text>
        </g>
      )}
    </svg>
  );
};

export const PlanoCarga: FC<PlanoCargaProps> = ({
  carga,
  veiculo,
  ajudantes = 0,
  compacto = false,
  idPrefixo = 'plano',
}) => {
  const montagem = montarPlanoCarga(carga, veiculo, { pesoMaximoKg: veiculo.capacidadeKg });
  const viagens = planejarViagens(carga, veiculo);
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

  // Vista de cima: proporcional ao piso real do veículo. Cada retângulo é a
  // base de um volume; a lista abaixo confere o que ficou fora.
  const vistaX = 26;
  const vistaY = 26;
  const vistaEscala = Math.min(620 / comprimentoCm, 190 / larguraCm);
  const vistaLargura = comprimentoCm * vistaEscala;
  const vistaAltura = larguraCm * vistaEscala;
  const vistaViewLargura = vistaX + vistaLargura + 40;
  const vistaViewAltura = vistaY + vistaAltura + 34;
  const faixaOffset = new Map<number, number>();
  let acumuladoVista = 0;
  for (const faixa of montagem.faixas) {
    faixaOffset.set(faixa.indice, acumuladoVista);
    acumuladoVista += faixa.profundidadeCm;
  }
  const itensFora = montagem.itens.filter((item) => item.fora > 0);
  const sobraLarguraCm = Math.max(0, larguraCm - montagem.larguraUsadaCm);

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
            Veja a carga em 3D, no corte lateral e na vista de cima. Pesados na frente, frágeis por
            cima e todos os volumes conferidos na lista.
          </p>
        </div>
        <span class="chip plano-carga__veiculo" data-plano-veiculo>
          {veiculo.emoji ? `${veiculo.emoji} ` : ''}
          {veiculo.nome ?? 'Veículo'} · {dimensoesTexto(veiculo)}
        </span>
      </header>

      <div class="plano-carga__quadro" data-plano-quadro>
        <div class="plano-abas" data-plano-abas>
          <div class="plano-abas__botoes" role="tablist" aria-label="Formas de ver o carregamento">
            <button
              type="button"
              class="plano-aba plano-aba--ativo"
              data-plano-aba="3d"
              role="tab"
              aria-selected="true"
            >
              <span aria-hidden="true">🧊</span> 3D
            </button>
            <button
              type="button"
              class="plano-aba"
              data-plano-aba="lado"
              role="tab"
              aria-selected="false"
            >
              <span aria-hidden="true">📐</span> Lado
            </button>
            <button
              type="button"
              class="plano-aba"
              data-plano-aba="cima"
              role="tab"
              aria-selected="false"
            >
              <span aria-hidden="true">🗺️</span> Cima
            </button>
          </div>

          <div class="plano-abas__painel" data-plano-painel="3d">
            <VistaIso montagem={montagem} veiculo={veiculo} vazio={vazio} />
            <p class="plano-abas__nota">
              Gire a cabeça, não a tela: a frente do veículo fica em cima e a porta embaixo. Cada
              cubo é um volume da lista.
            </p>
          </div>

          <div class="plano-abas__painel" data-plano-painel="lado">
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
          </div>

          <div class="plano-abas__painel" data-plano-painel="cima">
        {!vazio && montagem.faixas.length > 0 && (
          <div class="plano-vista" data-plano-vista>
            <div class="plano-vista__topo">
              <strong>Vista de cima real</strong>
              <span>
                {numero(comprimentoCm)} × {numero(larguraCm)} cm · {montagem.faixas.length}{' '}
                {montagem.faixas.length === 1 ? 'faixa de largura' : 'faixas de largura'}
              </span>
            </div>
            <svg
              viewBox={`0 0 ${vistaViewLargura.toFixed(1)} ${vistaViewAltura.toFixed(1)}`}
              class="plano-vista__svg"
              role="img"
              aria-label={`Vista de cima proporcional da carroceria, com ${montagem.colocados} volumes posicionados e ${montagem.fora} fora`}
            >
              <title>Vista de cima da carroceria</title>
              <rect
                x={vistaX}
                y={vistaY}
                width={vistaLargura}
                height={vistaAltura}
                rx="6"
                class="plano-vista__piso"
              />
              <text x={vistaX} y={vistaY - 8} class="plano-vista__rotulo">
                frente (cabine)
              </text>
              <text
                x={vistaX + vistaLargura}
                y={vistaY - 8}
                text-anchor="end"
                class="plano-vista__rotulo"
              >
                porta
              </text>
              {sobraLarguraCm > 0.5 && (
                <rect
                  x={vistaX}
                  y={vistaY + montagem.larguraUsadaCm * vistaEscala}
                  width={vistaLargura}
                  height={Math.max(1, sobraLarguraCm * vistaEscala)}
                  rx="3"
                  class="plano-vista__sobra"
                />
              )}
              {montagem.blocos.map((bloco) => {
                const offset = faixaOffset.get(bloco.faixa) ?? 0;
                const x = vistaX + bloco.x * vistaEscala;
                const y = vistaY + offset * vistaEscala;
                return (
                  <g
                    class={`plano-vista__bloco plano-bloco--${classeCategoria(bloco.categoria)}${
                      bloco.fragil ? ' plano-bloco--fragil' : ''
                    }`}
                  >
                    <title>
                      {bloco.nome}
                      {bloco.fragil ? ' (frágil)' : ''} · faixa {bloco.faixa + 1} · base{' '}
                      {numero(bloco.comprimento)} × {numero(bloco.profundidade)} cm
                    </title>
                    <rect
                      x={x}
                      y={y}
                      width={Math.max(1.4, bloco.comprimento * vistaEscala - 0.8)}
                      height={Math.max(1.4, bloco.profundidade * vistaEscala - 0.8)}
                      rx="2"
                      class="plano-vista__caixa"
                    />
                  </g>
                );
              })}
            </svg>
            <p class="plano-vista__nota">
              Cada retângulo é a base de um volume. Volumes empilhados aparecem na mesma posição; a
              altura usada está no corte lateral.
            </p>
          </div>
        )}
          </div>
        </div>

        {!vazio && (
          <div class="plano-categorias" data-plano-categorias>
            <div class="plano-categorias__topo">
              <strong>Quanto espaço cada grupo ocupa</strong>
              <span>Volume total: {volume(carga.volumeM3)}</span>
            </div>
            <ul class="plano-categorias__lista">
              {montagem.categorias.map((categoria) => (
                <li class={`plano-categoria plano-categoria--${classeCategoria(categoria.categoria)}`}>
                  <span class="plano-categoria__nome">{categoria.categoria}</span>
                  <strong>{volume(categoria.volumeM3)}</strong>
                  <small>
                    {categoria.volumes} {categoria.volumes === 1 ? 'volume' : 'volumes'} ·{' '}
                    {peso(categoria.pesoKg)}
                  </small>
                </li>
              ))}
            </ul>
          </div>
        )}

        {!vazio && (
          <div class={`plano-viagens${viagens.cabeEmUma ? ' plano-viagens--ok' : ''}`} data-plano-viagens>
            <div class="plano-viagens__topo">
              <span class="plano-viagens__icone" aria-hidden="true">
                {viagens.cabeEmUma ? '✅' : viagens.viavel ? '🔁' : '⚠️'}
              </span>
              <div>
                <strong>
                  {viagens.cabeEmUma
                    ? 'Tudo cabe em 1 viagem'
                    : viagens.viavel
                      ? `São necessárias ${viagens.totalViagens} viagens`
                      : 'Este veículo não leva tudo'}
                </strong>
                <p>{viagens.motivo}</p>
              </div>
            </div>

            {viagens.totalViagens > 1 && (
              <ol class="plano-viagens__lista">
                {viagens.viagens.map((viagem) => (
                  <li class="plano-viagem">
                    <div class="plano-viagem__titulo">
                      <strong>Viagem {viagem.numero}</strong>
                      <span>
                        {volume(viagem.volumeM3)} · {peso(viagem.pesoKg)}
                      </span>
                    </div>
                    <p>
                      {viagem.itens
                        .map((item) => `${item.quantidade}× ${item.emoji} ${item.nome}`)
                        .join(' · ')}
                    </p>
                  </li>
                ))}
              </ol>
            )}

            {viagens.impossiveis.length > 0 && (
              <div class="plano-viagens__impossiveis">
                <strong>O que não entra neste veículo</strong>
                <ul>
                  {viagens.impossiveis.map((item) => (
                    <li>
                      <span aria-hidden="true">{item.emoji}</span> {item.quantidade}× {item.nome} —{' '}
                      {item.motivo}
                    </li>
                  ))}
                </ul>
              </div>
            )}
          </div>
        )}

        {!vazio && montagem.itens.length > 0 && (
          <div class="plano-conferencia" data-plano-conferencia>
            {itensFora.length > 0 && (
              <div class="plano-fora" data-plano-fora>
                <strong>
                  {viagens.totalViagens > 1 && viagens.viavel
                    ? 'Vão nas próximas viagens'
                    : 'Ficaram fora do plano'}
                </strong>
                <ul>
                  {itensFora.map((item) => (
                    <li>
                      <span aria-hidden="true">{item.emoji}</span>
                      <span>
                        <b>{item.nome}</b> — {item.fora} de {item.total}{' '}
                        {item.total === 1 ? 'volume' : 'volumes'}
                      </span>
                    </li>
                  ))}
                </ul>
                <p>
                  {viagens.cabeEmUma
                    ? 'O veículo escolhido não acomoda tudo. Confira as medidas ou escolha um veículo maior.'
                    : viagens.viavel
                      ? 'Estes volumes não couberam na primeira viagem e entram nas próximas. A lista de viagens mostra a divisão.'
                      : 'Estes volumes não entram neste veículo. Confira as medidas ou escolha um veículo maior.'}
                </p>
              </div>
            )}

            <div class="plano-lista" data-plano-itens>
              <div class="plano-lista__topo">
                <strong>Conferência dos volumes</strong>
                <span>Todos os itens da lista, inclusive os que ficaram fora</span>
              </div>
              <ul>
                {montagem.itens.map((item) => (
                  <li
                    class={`plano-lista__item${
                      item.fora > 0 ? ' plano-lista__item--fora' : ''
                    }`}
                  >
                    <span class="plano-lista__emoji" aria-hidden="true">
                      {item.emoji}
                    </span>
                    <span class="plano-lista__nome">
                      {item.nome}
                      <small>
                        {item.total} {item.total === 1 ? 'volume' : 'volumes'} ·{' '}
                        {volume(item.volumeM3)}
                      </small>
                    </span>
                    <span class="plano-lista__status">
                      {item.fora === 0
                        ? 'No plano'
                        : viagens.viavel && viagens.totalViagens > 1
                          ? `${item.fora} na próxima`
                          : `${item.fora} fora`}
                    </span>
                  </li>
                ))}
              </ul>
            </div>
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
                {montagem.totalUnidades === 1 ? 'volume' : 'volumes'}
                {viagens.totalViagens > 1 ? ' na 1ª viagem' : ' acomodados'}
                {montagem.volumesAtras > 0
                  ? ` · ${montagem.volumesAtras} ${
                      montagem.volumesAtras === 1 ? 'na faixa de trás' : 'nas faixas de trás'
                    }`
                  : ''}
                {viagens.totalViagens > 1
                  ? ` · ${viagens.totalViagens} viagens no total`
                  : montagem.fora > 0
                    ? ` · ${montagem.fora} fora`
                    : ''}
                {` · ${volume(montagem.volumeColocadoM3)} nesta viagem`}
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
