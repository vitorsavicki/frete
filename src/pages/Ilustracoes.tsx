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
  const idCorpo = `${idPrefixo}-corpo`;
  const idCap = `${idPrefixo}-cap`;
  return (
    <svg
      class={classe ?? 'mascote'}
      viewBox="0 0 240 260"
      x={x}
      y={y}
      width={width}
      height={height}
      role={decorativo ? undefined : 'img'}
      aria-hidden={decorativo ? 'true' : undefined}
      aria-label={
        decorativo
          ? undefined
          : 'Zé Mudança, o mascote do Quero Mudar Já: uma caixa de mudança com boné, braços e pernas, segurando uma caixa pequena'
      }
    >
      <defs>
        <linearGradient id={idCorpo} x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#fcd34d" />
          <stop offset=".55" stop-color="#fbbf24" />
          <stop offset="1" stop-color="#f59e0b" />
        </linearGradient>
        <linearGradient id={idCap} x1="0" y1="0" x2="0" y2="1">
          <stop offset="0" stop-color="#3b82f6" />
          <stop offset="1" stop-color="#1e40af" />
        </linearGradient>
      </defs>

      <ellipse cx="120" cy="247" rx="72" ry="9" fill="#0f172a" opacity=".14" />

      <g class="mascote__corpo">
        {/* pernas e botas de trabalho */}
        <rect x="86" y="196" width="25" height="36" rx="10" fill="#1e3a8a" />
        <rect x="129" y="196" width="25" height="36" rx="10" fill="#1e3a8a" />
        <path
          d="M77 226h43c5 0 9 4 9 9v2c0 5-4 9-9 9H81c-5 0-9-4-9-9v-5c0-4 2-6 5-6Z"
          fill="#0f172a"
        />
        <path
          d="M123 226h43c5 0 9 4 9 9v2c0 5-4 9-9 9h-39c-5 0-9-4-9-9v-5c0-4 2-6 5-6Z"
          fill="#0f172a"
        />
        <path d="M82 235h32M128 235h32" stroke="#475569" stroke-width="2" stroke-linecap="round" />

        {/* braço esquerdo segurando a caixa */}
        <path
          d="M58 137c-13 9-19 21-19 35"
          fill="none"
          stroke="#2563eb"
          stroke-width="14"
          stroke-linecap="round"
        />
        <path
          d="M39 172c-2 5-2 9-1 13"
          fill="none"
          stroke="#1d4ed8"
          stroke-width="13"
          stroke-linecap="round"
        />
        <circle cx="36" cy="190" r="10" fill="#fbbf24" stroke="#d97706" stroke-width="2" />
        <path d="M32 188l8 4M37 184l-1 11" stroke="#d97706" stroke-width="1.5" stroke-linecap="round" />

        {/* caixa pequena na mão */}
        <g transform="rotate(-4 25 180)">
          <rect x="4" y="164" width="39" height="34" rx="4" fill="#fbbf24" stroke="#b45309" stroke-width="2" />
          <path d="M4 176h39M23 164v34" stroke="#fef3c7" stroke-width="4" opacity=".9" />
          <path d="M9 170l8 5M17 170l-8 5" stroke="#92400e" stroke-width="1.5" stroke-linecap="round" />
        </g>

        {/* corpo: caixa de mudança com fita e etiqueta */}
        <rect
          x="52"
          y="78"
          width="136"
          height="126"
          rx="18"
          fill={`url(#${idCorpo})`}
          stroke="#b45309"
          stroke-width="2.6"
        />
        <path d="M52 102h136" stroke="#d97706" stroke-width="2" opacity=".7" />
        <path d="M73 79l17 22M167 79l-17 22" stroke="#d97706" stroke-width="2" opacity=".55" />
        <rect x="110" y="78" width="20" height="126" fill="#fef3c7" opacity=".78" />
        <path d="M110 78v126M130 78v126" stroke="#fcd34d" stroke-width="2" opacity=".9" />
        <path
          d="M66 82l-8 116M174 82l8 116"
          stroke="#d97706"
          stroke-width="1.6"
          stroke-dasharray="4 6"
          opacity=".35"
        />

        {/* etiqueta de mudança */}
        <g transform="rotate(-5 78 178)">
          <rect x="59" y="166" width="35" height="27" rx="3" fill="#fff" stroke="#d97706" stroke-width="1.6" />
          <path d="M64 173h24M64 179h17M64 185h22" stroke="#94a3b8" stroke-width="1.4" stroke-linecap="round" />
          <circle cx="88" cy="187" r="2.4" fill="#ef4444" />
        </g>

        {/* rosto */}
        <g class="mascote__olhos">
          <circle cx="91" cy="128" r="14" fill="#fff" stroke="#b45309" stroke-width="2" />
          <circle cx="149" cy="128" r="14" fill="#fff" stroke="#b45309" stroke-width="2" />
          <circle cx="93" cy="130" r="7" fill="#0f172a" />
          <circle cx="147" cy="130" r="7" fill="#0f172a" />
          <circle cx="90" cy="126" r="2.6" fill="#fff" />
          <circle cx="144" cy="126" r="2.6" fill="#fff" />
        </g>
        <path
          d="M78 108c4-4 9-6 15-6M147 102c6 0 11 2 15 6"
          fill="none"
          stroke="#92400e"
          stroke-width="2.2"
          stroke-linecap="round"
          opacity=".8"
        />
        <ellipse cx="74" cy="150" rx="9" ry="6" fill="#fb923c" opacity=".45" />
        <ellipse cx="166" cy="150" rx="9" ry="6" fill="#fb923c" opacity=".45" />
        <path
          d="M100 153c6 9 14 13 20 13s14-4 20-13"
          fill="none"
          stroke="#7c2d12"
          stroke-width="3.4"
          stroke-linecap="round"
        />
        <path d="M112 160c5 4 11 4 16 0" fill="#fff" opacity=".9" />

        {/* boné */}
        <path
          d="M64 78c0-30 25-50 56-50s56 20 56 50Z"
          fill={`url(#${idCap})`}
          stroke="#172554"
          stroke-width="2.4"
        />
        <path d="M60 71h120c6 0 10 4 10 9s-4 9-10 9H60Z" fill="#172554" />
        <circle cx="120" cy="32" r="9" fill="#fbbf24" stroke="#d97706" stroke-width="2" />
        <path d="M117 29h6v6h-6z" fill="#92400e" />
        <path d="M78 62c8-12 22-20 38-22" fill="none" stroke="#93c5fd" stroke-width="2.4" stroke-linecap="round" opacity=".8" />

        {/* braço direito acenando */}
        <g class="mascote__braco mascote__braco--aceno">
          <path
            d="M184 136c14-8 22-20 24-34"
            fill="none"
            stroke="#2563eb"
            stroke-width="14"
            stroke-linecap="round"
          />
          <path
            d="M208 102c3-5 4-9 4-14"
            fill="none"
            stroke="#1d4ed8"
            stroke-width="13"
            stroke-linecap="round"
          />
          <circle cx="214" cy="82" r="10" fill="#fbbf24" stroke="#d97706" stroke-width="2" />
          <path d="M209 79l9 5M215 75l-2 11" stroke="#d97706" stroke-width="1.5" stroke-linecap="round" />
          <path
            d="M226 70c4-4 6-8 7-13M231 88h8"
            fill="none"
            stroke="#93c5fd"
            stroke-width="2.6"
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
}

/**
 * Zé Mudança em modo ajuda: uma fala curta e útil, com o texto sempre
 * visível (nada de esconder informação atrás de um balão clicável).
 */
export const MascoteAjuda: FC<MascoteAjudaProps> = ({
  fala,
  detalhe,
  tom = 'dica',
  class: classe,
  idPrefixo = 'mascote-ajuda',
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
    </div>
  </div>
);

export const CaminhaoMudanca: FC<{ class?: string }> = ({ class: classe }) => (
  <svg class={classe} viewBox="0 0 300 200" aria-hidden="true">
    <ellipse cx="150" cy="178" rx="126" ry="11" fill="#0f172a" opacity=".12" />
    <rect
      x="96"
      y="26"
      width="184"
      height="116"
      rx="16"
      fill="#f5b942"
      stroke="#0f172a"
      stroke-width="4"
    />
    <path d="M96 48h184" stroke="#fbbf24" stroke-width="7" stroke-linecap="round" />
    <path d="M118 26v116M258 26v116" stroke="#eab308" stroke-width="3" opacity=".55" />
    <path
      d="M24 142V98c0-12 8-22 20-22h52v66H24Z"
      fill="#1e4fd8"
      stroke="#0f172a"
      stroke-width="4"
      stroke-linejoin="round"
    />
    <path
      d="M40 94h38v28H40z"
      fill="#dbeafe"
      stroke="#0f172a"
      stroke-width="4"
      stroke-linejoin="round"
    />
    <path d="M24 142h256" stroke="#0f172a" stroke-width="10" stroke-linecap="round" />
    <circle cx="78" cy="158" r="24" fill="#1e293b" />
    <circle cx="78" cy="158" r="9" fill="#cbd5e1" />
    <circle cx="238" cy="158" r="24" fill="#1e293b" />
    <circle cx="238" cy="158" r="9" fill="#cbd5e1" />
    <rect x="24" y="118" width="10" height="18" rx="3" fill="#fbbf24" />
    <path d="M24 151h18" stroke="#0f172a" stroke-width="6" stroke-linecap="round" />
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
      rx="10"
      fill={cor}
      stroke="#0f172a"
      stroke-width="4"
    />
    <path d="M10 39h80" stroke="#fbbf24" stroke-width="6" />
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
