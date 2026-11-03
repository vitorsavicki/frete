import type { FC } from 'hono/jsx';

// Ilustrações próprias do Quero Mudar Já.
//
// Por que SVG inline: o site tem CSP fechada (script-src 'self') e não quer
// depender de banco de imagens. As ilustrações são leves, nítidas em qualquer
// tela, funcionam offline no APK e podem ser animadas por CSS — com fallback
// para quem prefere menos movimento (prefers-reduced-motion).

interface MascoteProps {
  class?: string;
  idPrefixo?: string;
  decorativo?: boolean;
  /** Posição e tamanho só são usados quando o mascote é encaixado dentro de outra cena SVG. */
  x?: number;
  y?: number;
  width?: number;
  height?: number;
}

export const MascoteMudanca: FC<MascoteProps> = ({
  class: classe,
  idPrefixo = 'mascote',
  decorativo = false,
  x,
  y,
  width,
  height,
}) => {
  return (
    <svg
      class={classe ?? 'mascote'}
      viewBox="0 0 240 260"
      x={x}
      y={y}
      width={width}
      height={height}
      data-mascote={idPrefixo}
      role={decorativo ? undefined : 'img'}
      aria-hidden={decorativo ? 'true' : undefined}
      aria-label={
        decorativo
          ? undefined
          : 'Zé Mudança, o boneco de pano do Quero Mudar Já: cabelo de lã, olhos de botão, macacão azul com um caminhão costurado no peito e uma caixa pequena na mão'
      }
    >
      <ellipse cx="120" cy="248" rx="66" ry="8" fill="#0f172a" opacity=".13" />

      <g class="mascote__corpo">
        {/* pernas e botas de tecido */}
        <rect
          x="96"
          y="192"
          width="20"
          height="40"
          rx="10"
          fill="#1e4fd8"
          stroke="#1e3a8a"
          stroke-width="2.2"
        />
        <rect
          x="124"
          y="192"
          width="20"
          height="40"
          rx="10"
          fill="#1e4fd8"
          stroke="#1e3a8a"
          stroke-width="2.2"
        />
        <path d="M92 220h26c4 0 7 3 7 7v3c0 3-3 6-6 6H98c-3 0-6-2-6-5v-11Z" fill="#1e293b" />
        <path d="M122 220h26c4 0 7 3 7 7v3c0 3-3 6-6 6h-21c-3 0-6-2-6-5v-11Z" fill="#1e293b" />
        <path
          d="M99 213h14M127 213h14"
          stroke="#93c5fd"
          stroke-width="1.7"
          stroke-dasharray="3 4"
          stroke-linecap="round"
        />

        {/* corpo de tecido: camisa âmbar e macacão indigo */}
        <rect
          x="64"
          y="92"
          width="112"
          height="116"
          rx="30"
          fill="#f5b942"
          stroke="#1e293b"
          stroke-width="2.6"
        />
        <path
          d="M64 148h112v28c0 17-13 30-30 30H94c-17 0-30-13-30-30v-28Z"
          fill="#1e4fd8"
          stroke="#1e293b"
          stroke-width="2.6"
        />
        <rect
          x="92"
          y="104"
          width="56"
          height="48"
          rx="13"
          fill="#1e4fd8"
          stroke="#1e293b"
          stroke-width="2.4"
        />
        <path
          d="M94 110 80 96M146 110l14-14"
          stroke="#1e4fd8"
          stroke-width="9"
          stroke-linecap="round"
        />
        <circle cx="79" cy="95" r="4" fill="#f5b942" stroke="#92400e" stroke-width="1.6" />
        <circle cx="161" cy="95" r="4" fill="#f5b942" stroke="#92400e" stroke-width="1.6" />
        <path
          d="M72 188h96"
          stroke="#93c5fd"
          stroke-width="1.8"
          stroke-dasharray="4 5"
          stroke-linecap="round"
          opacity=".9"
        />
        <path
          d="M74 158h22v18H74zM144 158h22v18h-22z"
          fill="none"
          stroke="#93c5fd"
          stroke-width="1.6"
          stroke-dasharray="4 4"
          stroke-linejoin="round"
        />
        <path d="M126 176h18v14h-18z" fill="#f5b942" stroke="#92400e" stroke-width="1.4" />
        <path d="M128 178l14 10M142 178l-14 10" stroke="#92400e" stroke-width="1" opacity=".55" />

        {/* caminhão costurado no peito */}
        <rect
          x="102"
          y="122"
          width="36"
          height="26"
          rx="5"
          fill="#fff"
          stroke="#1e293b"
          stroke-width="1.6"
        />
        <path
          d="M108 130h11v10h-11zM119 133h4l4 4v3h-8"
          fill="none"
          stroke="#1e4fd8"
          stroke-width="1.8"
          stroke-linejoin="round"
        />
        <circle cx="111" cy="142" r="2" fill="#1e4fd8" />
        <circle cx="125" cy="142" r="2" fill="#1e4fd8" />
        <path d="M122 134h3l2 3h-5z" fill="#f5b942" />

        {/* cabeça de pano */}
        <rect
          x="73"
          y="12"
          width="94"
          height="90"
          rx="36"
          fill="#f3d3b3"
          stroke="#b45309"
          stroke-width="2.6"
        />
        <circle cx="72" cy="60" r="9" fill="#f3d3b3" stroke="#b45309" stroke-width="2.2" />
        <circle cx="168" cy="60" r="9" fill="#f3d3b3" stroke="#b45309" stroke-width="2.2" />

        {/* cabelo de lã */}
        <path
          d="M77 44c1-17 13-30 29-34 4-6 9-9 16-9 7 0 12 3 16 9 16 4 27 17 28 34-7-1-12-4-16-10-4 4-9 6-15 6-4-3-8-5-13-5s-9 2-13 5c-6 0-11-2-15-6-4 6-9 9-17 10Z"
          fill="#1e293b"
        />
        <path
          d="M104 16c5-3 10-4 15-3M132 14c5 0 9 2 12 5"
          fill="none"
          stroke="#475569"
          stroke-width="2"
          stroke-linecap="round"
          opacity=".65"
        />

        {/* olhos de botão */}
        <g class="mascote__olhos">
          <circle cx="101" cy="57" r="11" fill="#f8fafc" stroke="#1e293b" stroke-width="2" />
          <circle cx="139" cy="57" r="11" fill="#f8fafc" stroke="#1e293b" stroke-width="2" />
          <circle cx="98" cy="54" r="1.5" fill="#1e293b" />
          <circle cx="104" cy="54" r="1.5" fill="#1e293b" />
          <circle cx="98" cy="60" r="1.5" fill="#1e293b" />
          <circle cx="104" cy="60" r="1.5" fill="#1e293b" />
          <circle cx="136" cy="54" r="1.5" fill="#1e293b" />
          <circle cx="142" cy="54" r="1.5" fill="#1e293b" />
          <circle cx="136" cy="60" r="1.5" fill="#1e293b" />
          <circle cx="142" cy="60" r="1.5" fill="#1e293b" />
          <path
            d="M98 57h6M136 57h6"
            stroke="#1e293b"
            stroke-width="1.4"
            stroke-linecap="round"
          />
        </g>

        <path
          d="M93 40c5-3 11-3 16 0M131 40c5-3 11-3 16 0"
          fill="none"
          stroke="#92400e"
          stroke-width="1.8"
          stroke-linecap="round"
        />
        <ellipse cx="84" cy="72" rx="8" ry="4.6" fill="#fca5a5" opacity=".5" />
        <ellipse cx="156" cy="72" rx="8" ry="4.6" fill="#fca5a5" opacity=".5" />
        <path d="M120 62v7" stroke="#d97706" stroke-width="2" stroke-linecap="round" />
        <path
          d="M106 78c4 6 9 9 14 9s10-3 14-9"
          fill="none"
          stroke="#92400e"
          stroke-width="2.4"
          stroke-dasharray="4 4"
          stroke-linecap="round"
        />

        {/* braço esquerdo segurando a caixa */}
        <path
          d="M70 118c-12 10-19 26-21 42"
          fill="none"
          stroke="#1e4fd8"
          stroke-width="13"
          stroke-linecap="round"
        />
        <path d="M49 160l-3 12" stroke="#1e3a8a" stroke-width="13" stroke-linecap="round" />
        <g transform="rotate(-4 26 192)">
          <rect
            x="8"
            y="176"
            width="38"
            height="32"
            rx="4"
            fill="#f5b942"
            stroke="#b45309"
            stroke-width="2"
          />
          <path d="M8 188h38M27 176v32" stroke="#fef3c7" stroke-width="3.4" opacity=".9" />
          <rect
            x="13"
            y="181"
            width="12"
            height="5"
            rx="1"
            fill="#fff"
            stroke="#b45309"
            stroke-width=".9"
          />
        </g>
        <circle cx="44" cy="182" r="9" fill="#f3d3b3" stroke="#b45309" stroke-width="2" />

        {/* braço direito acenando */}
        <g class="mascote__braco mascote__braco--aceno">
          <path
            d="M170 118c13-5 23-17 27-31"
            fill="none"
            stroke="#1e4fd8"
            stroke-width="13"
            stroke-linecap="round"
          />
          <path d="M196 90l4-8" stroke="#1e3a8a" stroke-width="13" stroke-linecap="round" />
          <circle cx="204" cy="78" r="9.5" fill="#f3d3b3" stroke="#b45309" stroke-width="2" />
          <path
            d="M216 64c3-3 5-7 6-11M224 82h8"
            fill="none"
            stroke="#93c5fd"
            stroke-width="2.4"
            stroke-linecap="round"
          />
        </g>
      </g>
    </svg>
  );
};

interface MascoteAjudaProps {
  fala: string;
  detalhe?: string;
  tom?: 'dica' | 'atencao' | 'ok';
  class?: string;
  idPrefixo?: string;
  /** Ação útil: o Zé nunca aparece sem um próximo passo. */
  acaoTexto?: string;
  acaoHref?: string;
}

/**
 * Zé Mudança em modo ajuda: uma fala curta e útil, com o texto sempre
 * visível (nada de esconder informação atrás de um balão clicável) e um
 * link para a pessoa continuar sozinha.
 */
export const MascoteAjuda: FC<MascoteAjudaProps> = ({
  fala,
  detalhe,
  tom = 'dica',
  class: classe,
  idPrefixo = 'mascote-ajuda',
  acaoTexto,
  acaoHref,
}) => (
  <div
    class={`mascote-ajuda mascote-ajuda--${tom}${classe ? ` ${classe}` : ''}`}
    data-mascote-ajuda
  >
    <MascoteMudanca class="mascote mascote--ajuda" idPrefixo={idPrefixo} decorativo />
    <div class="mascote-ajuda__balao" aria-live="polite">
      <span class="mascote-ajuda__nome">Zé Mudança</span>
      <strong data-mascote-fala>{fala}</strong>
      {detalhe && <p data-mascote-detalhe>{detalhe}</p>}
      {acaoTexto && acaoHref && (
        <a class="mascote-ajuda__acao" href={acaoHref}>
          {acaoTexto}
        </a>
      )}
    </div>
  </div>
);

export const MarcaQueroMudar: FC<{ class?: string }> = ({ class: classe }) => (
  <svg class={classe} viewBox="0 0 48 48" fill="none" aria-hidden="true">
    <rect width="48" height="48" rx="12" fill="currentColor" />
    <path
      d="M9 31.5V19c0-1.7 1.3-3 3-3h15v15.5"
      stroke="#fff"
      stroke-width="2.2"
      stroke-linejoin="round"
    />
    <path
      d="M27 22h6.4c.7 0 1.3.4 1.7 1l4 6.1c.2.4.4.9.4 1.4v1h-2.3"
      stroke="#fff"
      stroke-width="2.2"
      stroke-linejoin="round"
    />
    <path d="M9 31.5h3.6M19.2 31.5h8M34.6 31.5H37" stroke="#fff" stroke-width="2.2" />
    <circle cx="16.2" cy="33.8" r="3" stroke="#fff" stroke-width="2.2" />
    <circle cx="31.2" cy="33.8" r="3" stroke="#fff" stroke-width="2.2" />
    <path d="M29.2 25.4h3.8l2.1 4.2h-5.9v-4.2Z" fill="#f5b942" />
  </svg>
);

const rodasVeiculo = (xs: number[]) =>
  xs.map((x) => (
    <>
      <circle cx={x} cy="32.6" r="4.4" fill="#1e293b" />
      <circle cx={x} cy="32.6" r="1.7" fill="#cbd5e1" />
    </>
  ));

/** Ícone de veículo em traço fino, no mesmo estilo do restante do site. */
export const IconeVeiculo: FC<{ tipo?: string; class?: string }> = ({
  tipo = 'outro',
  class: classe,
}) => {
  const traco = {
    stroke: '#1e293b',
    'stroke-width': '2.2',
    'stroke-linejoin': 'round',
  } as const;

  return (
    <svg class={classe ?? 'icone-veiculo'} viewBox="0 0 64 40" aria-hidden="true">
      <path d="M4 37h56" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round" />

      {tipo === 'fiorino' ? (
        <>
          <path d="M4 31V23c0-5 4-9 9-9h8v17" fill="#1e4fd8" {...traco} />
          <path d="M21 10h35c1.7 0 3 1.3 3 3v18H21z" fill="#f5b942" {...traco} />
          <path d="M8 20h9v6H8z" fill="#dbeafe" stroke="#1e293b" stroke-width="1.4" />
          <path d="M26 14h28" stroke="#d97706" stroke-width="1.6" opacity=".55" />
          {rodasVeiculo([13, 46])}
        </>
      ) : tipo === 'van' ? (
        <>
          <path d="M4 31V21c0-4 3-8 8-8h9v18" fill="#1e4fd8" {...traco} />
          <path d="M21 7h35c1.7 0 3 1.3 3 3v21H21z" fill="#f5b942" {...traco} />
          <path d="M8 17h10v7H8z" fill="#dbeafe" stroke="#1e293b" stroke-width="1.4" />
          <path d="M26 11h28" stroke="#d97706" stroke-width="1.6" opacity=".55" />
          {rodasVeiculo([13, 46])}
        </>
      ) : tipo === 'tres_quartos' ? (
        <>
          <path d="M4 31V21c0-4 3-8 8-8h8v18" fill="#1e4fd8" {...traco} />
          <path d="M20 11h36c1.7 0 3 1.3 3 3v17H20z" fill="#f5b942" {...traco} />
          <path d="M8 17h9v7H8z" fill="#dbeafe" stroke="#1e293b" stroke-width="1.4" />
          <path d="M25 15h28" stroke="#d97706" stroke-width="1.6" opacity=".55" />
          {rodasVeiculo([13, 46])}
        </>
      ) : tipo === 'carreta' ? (
        <>
          <path d="M3 31V20c0-5 4-8 8-8h7v19" fill="#1e4fd8" {...traco} />
          <path d="M18 8h43c1.6 0 3 1.4 3 3v20H18z" fill="#f5b942" {...traco} />
          <path d="M7 17h9v8H7z" fill="#dbeafe" stroke="#1e293b" stroke-width="1.4" />
          <path d="M23 12v19M55 10v19M51 10v19" stroke="#1e293b" stroke-width="1.2" opacity=".45" />
          {rodasVeiculo([12, 39, 53])}
        </>
      ) : (
        <>
          <path d="M4 31V19c0-4 3-7 7-7h8v19" fill="#1e4fd8" {...traco} />
          <path
            d="M19 6h40c1.7 0 3 1.3 3 3v22H19z"
            fill={tipo === 'outro' ? '#f1f5f9' : '#f5b942'}
            {...traco}
            stroke-dasharray={tipo === 'outro' ? '4 3' : undefined}
          />
          <path d="M8 16h9v8H8z" fill="#dbeafe" stroke="#1e293b" stroke-width="1.4" />
          <path d="M56 10v18M52 10v18" stroke="#1e293b" stroke-width="1.2" opacity=".45" />
          {rodasVeiculo([12, 47])}
        </>
      )}
    </svg>
  );
};

export const CaminhaoMudanca: FC<{ class?: string }> = ({ class: classe }) => (
  <svg class={classe} viewBox="0 0 300 200" aria-hidden="true">
    <ellipse cx="150" cy="174" rx="126" ry="9" fill="#0f172a" opacity=".1" />
    <path d="M12 176h276" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" />

    {/* baú */}
    <rect
      x="104"
      y="40"
      width="172"
      height="104"
      rx="6"
      fill="#f5b942"
      stroke="#1e293b"
      stroke-width="2.4"
    />
    <path d="M110 45h160" stroke="#fcd34d" stroke-width="3" stroke-linecap="round" />
    <path
      d="M136 48v90M170 48v90M204 48v90M238 48v90"
      stroke="#d97706"
      stroke-width="1.8"
      opacity=".4"
    />
    <path d="M266 46v92M271 46v92" stroke="#b45309" stroke-width="1.6" opacity=".55" />
    <rect x="104" y="128" width="172" height="9" fill="#1e4fd8" />

    {/* cabine */}
    <path
      d="M26 144V105c0-10 8-18 18-18h42l22 30v27H26Z"
      fill="#1e4fd8"
      stroke="#1e293b"
      stroke-width="2.4"
      stroke-linejoin="round"
    />
    <path
      d="M48 100h28l18 25H48z"
      fill="#dbeafe"
      stroke="#1e293b"
      stroke-width="1.8"
      stroke-linejoin="round"
    />
    <path d="M78 144v-32" stroke="#172554" stroke-width="1.8" />
    <path d="M68 118h8" stroke="#172554" stroke-width="2.4" stroke-linecap="round" />
    <path d="M27 106H19v14" fill="none" stroke="#1e293b" stroke-width="2.4" stroke-linecap="round" />

    {/* chassi, para-choques e farol */}
    <path d="M24 144h256" stroke="#1e293b" stroke-width="7" stroke-linecap="round" />
    <rect x="16" y="138" width="16" height="10" rx="2" fill="#cbd5e1" stroke="#1e293b" stroke-width="2" />
    <rect x="22" y="122" width="9" height="10" rx="2" fill="#fde68a" stroke="#1e293b" stroke-width="1.6" />
    <rect x="274" y="138" width="14" height="9" rx="2" fill="#cbd5e1" stroke="#1e293b" stroke-width="2" />

    {/* rodas */}
    {[74, 238].map((x) => (
      <>
        <circle cx={x} cy="152" r="20" fill="#1e293b" />
        <circle cx={x} cy="152" r="12" fill="#334155" />
        <circle cx={x} cy="152" r="6" fill="#cbd5e1" />
      </>
    ))}
    <path d="M54 144a20 20 0 0 1 40 0M218 144a20 20 0 0 1 40 0" fill="none" stroke="#1e293b" stroke-width="2.4" />
  </svg>
);

const CaixaMudanca: FC<{ class?: string; cor?: string }> = ({
  class: classe,
  cor = '#f5b942',
}) => (
  <svg class={classe} viewBox="0 0 100 100" aria-hidden="true">
    <rect
      x="10"
      y="22"
      width="80"
      height="70"
      rx="8"
      fill={cor}
      stroke="#1e293b"
      stroke-width="2.4"
    />
    <path d="M10 39h80" stroke="#fbbf24" stroke-width="5" />
    <path d="M50 22v70" stroke="#fbbf24" stroke-width="12" opacity=".7" />
    <path d="M44 22v12M56 22v12" stroke="#eab308" stroke-width="2" />
  </svg>
);

export const CenaMudanca: FC<{ class?: string }> = ({ class: classe }) => (
  <div
    class={`cena-mudanca cena-mudanca--foto${classe ? ` ${classe}` : ''}`}
    role="img"
    aria-label="Caminhão de mudança com caixas e o mascote Zé Mudança"
  >
    <span class="cena-mudanca__ceu" aria-hidden="true"></span>
    <span class="cena-mudanca__estrada" aria-hidden="true"></span>
    <CaminhaoMudanca class="cena-mudanca__caminhao" />
    <CaixaMudanca class="cena-mudanca__caixa cena-mudanca__caixa--grande" />
    <CaixaMudanca class="cena-mudanca__caixa cena-mudanca__caixa--pequena" cor="#fde68a" />
    <MascoteMudanca class="cena-mudanca__mascote" idPrefixo="cena-mascote" decorativo />
  </div>
);

export const IconeCliente: FC<{ class?: string }> = ({ class: classe }) => (
  <svg class={classe} viewBox="0 0 64 64" aria-hidden="true">
    <circle cx="32" cy="32" r="30" fill="#dbeafe" />
    <path d="M12 44V28c0-2 1-3 3-3h20v19" fill="none" stroke="#1d4ed8" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" />
    <path d="M35 30h8l7 8v6h-4" fill="none" stroke="#1d4ed8" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" />
    <path d="M12 44h4M21 44h10M40 44h4" stroke="#1d4ed8" stroke-width="3.4" stroke-linecap="round" />
    <circle cx="19" cy="47" r="4" fill="#1d4ed8" />
    <circle cx="39" cy="47" r="4" fill="#1d4ed8" />
    <path d="M46 17l4-4M52 26h5M44 26h-2" stroke="#f59e0b" stroke-width="3" stroke-linecap="round" />
  </svg>
);

export const IconeTransportador: FC<{ class?: string }> = ({ class: classe }) => (
  <svg class={classe} viewBox="0 0 64 64" aria-hidden="true">
    <circle cx="32" cy="32" r="30" fill="#fef3c7" />
    <path d="M14 34c0-7 6-13 13-13h9v24H14V34Z" fill="#f59e0b" />
    <path d="M36 25h8c4 0 7 2 9 5l5 9v6H36V25Z" fill="#1d4ed8" />
    <path d="M40 29h5c2 0 4 1 5 3l4 7h-14V29Z" fill="#dbeafe" />
    <circle cx="24" cy="47" r="5" fill="#0f172a" />
    <circle cx="48" cy="47" r="5" fill="#0f172a" />
    <path d="M14 21h9M10 27h6" stroke="#1d4ed8" stroke-width="3" stroke-linecap="round" />
  </svg>
);
