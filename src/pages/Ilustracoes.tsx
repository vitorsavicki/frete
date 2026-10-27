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

export const CenaMudanca: FC<{ class?: string }> = ({ class: classe }) => (
  <svg
    class={classe ?? 'cena-mudanca'}
    viewBox="0 0 760 520"
    role="img"
    aria-label="Caminhão de mudança carregado com sofá e caixas, em uma estrada, ao lado do mascote Zé Mudança"
  >
    <defs>
      <linearGradient id="cena-ceu" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="#dbeafe" />
        <stop offset=".68" stop-color="#eff6ff" />
        <stop offset="1" stop-color="#f8fafc" />
      </linearGradient>
      <linearGradient id="cena-cabine" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="#3b82f6" />
        <stop offset="1" stop-color="#1d4ed8" />
      </linearGradient>
      <linearGradient id="cena-caixa" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" stop-color="#fcd34d" />
        <stop offset="1" stop-color="#f59e0b" />
      </linearGradient>
    </defs>

    <rect width="760" height="520" rx="30" fill="url(#cena-ceu)" />
    <circle cx="642" cy="92" r="38" fill="#fde68a" opacity=".9" />

    <g class="cena__nuvens" fill="#fff" opacity=".9">
      <ellipse cx="128" cy="98" rx="42" ry="18" />
      <ellipse cx="164" cy="90" rx="30" ry="16" />
      <ellipse cx="92" cy="90" rx="24" ry="13" />
      <ellipse cx="330" cy="72" rx="34" ry="14" />
      <ellipse cx="358" cy="66" rx="24" ry="12" />
    </g>

    {/* casas ao fundo */}
    <g opacity=".95">
      <path d="M36 278l70-46 70 46Z" fill="#93c5fd" />
      <rect x="46" y="276" width="120" height="104" rx="8" fill="#f8fafc" />
      <rect x="66" y="300" width="28" height="32" rx="3" fill="#bfdbfe" />
      <rect x="112" y="300" width="34" height="56" rx="3" fill="#60a5fa" />
      <path d="M46 332h120" stroke="#e2e8f0" stroke-width="2" />
      <circle cx="82" cy="276" r="7" fill="#f59e0b" opacity=".75" />

      <path d="M180 300l54-34 54 34Z" fill="#93c5fd" opacity=".8" />
      <rect x="190" y="298" width="88" height="82" rx="6" fill="#f1f5f9" />
      <rect x="206" y="316" width="22" height="26" rx="3" fill="#bfdbfe" />
      <rect x="240" y="316" width="22" height="26" rx="3" fill="#bfdbfe" />
    </g>

    {/* árvores */}
    <g>
      <rect x="330" y="346" width="8" height="42" rx="3" fill="#92400e" />
      <circle cx="334" cy="334" r="28" fill="#22c55e" opacity=".85" />
      <circle cx="316" cy="344" r="18" fill="#16a34a" opacity=".8" />
      <rect x="694" y="370" width="7" height="30" rx="3" fill="#92400e" />
      <circle cx="697" cy="360" r="22" fill="#22c55e" opacity=".8" />
    </g>

    {/* rota pontilhada */}
    <g class="cena__rota" fill="none" stroke="#1d4ed8" stroke-width="3" stroke-dasharray="7 9" opacity=".45">
      <path d="M52 252C150 206 244 222 330 196s176-26 250 10" />
    </g>
    <g>
      <circle cx="52" cy="252" r="9" fill="#fff" stroke="#1d4ed8" stroke-width="3" />
      <circle cx="52" cy="252" r="3.5" fill="#1d4ed8" />
      <circle cx="586" cy="206" r="9" fill="#fff" stroke="#1d4ed8" stroke-width="3" />
      <circle cx="586" cy="206" r="3.5" fill="#1d4ed8" />
    </g>

    {/* calçada e estrada */}
    <rect x="0" y="392" width="760" height="16" fill="#cbd5e1" />
    <path d="M0 408h760v112H0Z" fill="#334155" />
    <path d="M0 424h760v96H0Z" fill="#1e293b" />
    <g class="cena__estrada" stroke="#facc15" stroke-width="6" stroke-dasharray="34 28" opacity=".8">
      <path d="M0 472h760" />
    </g>

    {/* caminhão carregado */}
    <g class="cena__caminhao">
      <ellipse cx="300" cy="411" rx="236" ry="16" fill="#0f172a" opacity=".2" />

      {/* carga na carroceria */}
      <g class="cena__carga">
        <g class="cena__sofa">
          <rect x="66" y="270" width="118" height="58" rx="10" fill="#f97316" />
          <rect x="60" y="300" width="130" height="34" rx="10" fill="#fb923c" />
          <rect x="74" y="280" width="46" height="34" rx="6" fill="#fdba74" opacity=".8" />
          <rect x="126" y="280" width="46" height="34" rx="6" fill="#fdba74" opacity=".8" />
          <rect x="52" y="330" width="146" height="12" rx="5" fill="#ea580c" />
          <rect x="60" y="342" width="12" height="20" rx="4" fill="#7c2d12" />
          <rect x="178" y="342" width="12" height="20" rx="4" fill="#7c2d12" />
        </g>

        <g class="cena__caixas">
          <rect x="200" y="278" width="62" height="56" rx="5" fill="url(#cena-caixa)" stroke="#b45309" stroke-width="2" />
          <path d="M200 292h62M231 278v56" stroke="#fef3c7" stroke-width="4" opacity=".8" />
          <rect x="270" y="292" width="56" height="42" rx="5" fill="#fbbf24" stroke="#b45309" stroke-width="2" />
          <path d="M270 302h56M298 292v42" stroke="#fef3c7" stroke-width="3.4" opacity=".8" />
          <rect x="336" y="266" width="54" height="68" rx="5" fill="#f59e0b" stroke="#b45309" stroke-width="2" />
          <path d="M336 280h54M363 266v68" stroke="#fef3c7" stroke-width="4" opacity=".8" />
        </g>

        <path d="M58 258l-6 86M396 252l8 92" stroke="#1e40af" stroke-width="4" stroke-linecap="round" opacity=".8" />
        <path d="M52 258h12M390 252h12" stroke="#fbbf24" stroke-width="5" stroke-linecap="round" />
      </g>

      {/* carroceria */}
      <rect x="48" y="318" width="366" height="72" rx="8" fill="#e2e8f0" stroke="#94a3b8" stroke-width="2" />
      <path d="M58 328h346M58 344h346" stroke="#cbd5e1" stroke-width="3" />
      <rect x="116" y="344" width="130" height="30" rx="8" fill="#1d4ed8" />
      <text
        x="181"
        y="364"
        text-anchor="middle"
        font-family="system-ui,sans-serif"
        font-size="15"
        font-weight="800"
        letter-spacing="1.6"
        fill="#fff"
      >
        MUDANÇA
      </text>
      <rect x="48" y="382" width="366" height="10" rx="5" fill="#94a3b8" />

      {/* cabine */}
      <path
        d="M404 318c0-10 8-18 18-18h52c8 0 15 4 19 11l25 38c2 4 3 8 3 12v33c0 7-6 12-13 12h-91c-7 0-13-5-13-12V318Z"
        fill="url(#cena-cabine)"
        stroke="#1e3a8a"
        stroke-width="2"
      />
      <path d="M426 310h44c6 0 11 3 14 8l16 28h-74v-36Z" fill="#dbeafe" stroke="#93c5fd" stroke-width="2" />
      <path d="M490 310h16c5 0 9 2 11 6l17 30h-44v-36Z" fill="#dbeafe" opacity=".85" />
      <path d="M508 318v58" stroke="#1e40af" stroke-width="2" opacity=".5" />
      <rect x="516" y="342" width="14" height="4" rx="2" fill="#93c5fd" />
      <rect x="404" y="378" width="122" height="12" rx="5" fill="#172554" />
      <rect x="518" y="354" width="12" height="16" rx="3" fill="#fde68a" />
      <rect x="398" y="350" width="10" height="22" rx="4" fill="#f59e0b" />
      <path d="M396 338h16" stroke="#172554" stroke-width="4" stroke-linecap="round" />
      <rect x="514" y="300" width="7" height="24" rx="3" fill="#172554" />
      <rect x="522" y="298" width="18" height="13" rx="4" fill="#fbbf24" stroke="#d97706" stroke-width="1.6" />

      {/* rodas */}
      <g>
        <circle cx="150" cy="390" r="28" fill="#0f172a" />
        <circle cx="150" cy="390" r="13" fill="#cbd5e1" />
        <circle cx="150" cy="390" r="5" fill="#64748b" />
        <circle cx="466" cy="390" r="28" fill="#0f172a" />
        <circle cx="466" cy="390" r="13" fill="#cbd5e1" />
        <circle cx="466" cy="390" r="5" fill="#64748b" />
      </g>
    </g>

    {/* selo de tudo certo */}
    <g>
      <circle cx="552" cy="246" r="23" fill="#10b981" stroke="#fff" stroke-width="4" />
      <path d="M541 246l7 8 14-17" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
    </g>

    {/* mascote inteiro, com margem para não cortar */}
    <MascoteMudanca
      class="mascote--cena"
      idPrefixo="cena-mascote"
      decorativo
      x={566}
      y={248}
      width={178}
      height={193}
    />

    {/* plantas na frente */}
    <g opacity=".9">
      <path d="M20 448c8-28 18-38 30-42-2 18-10 32-24 44Z" fill="#16a34a" />
      <path d="M34 448c-2-22 4-34 14-42 6 16 2 30-8 44Z" fill="#22c55e" />
      <path d="M706 452c-6-24-16-34-28-40 0 18 8 32 20 42Z" fill="#16a34a" />
    </g>
  </svg>
);

type TipoArte = 'fiorino' | 'kombi' | 'van' | 'tres_quartos' | 'bau' | 'carreta';

const ROTULOS_VEICULO: Record<TipoArte, string> = {
  fiorino: 'Fiorino',
  kombi: 'Kombi',
  van: 'Van',
  tres_quartos: 'Caminhão 3/4',
  bau: 'Caminhão baú',
  carreta: 'Carreta',
};

interface VeiculoArteProps {
  tipo: TipoArte;
  class?: string;
}

/** Ilustração lateral dos veículos usados em mudanças. */
export const VeiculoArte: FC<VeiculoArteProps> = ({ tipo, class: classe }) => {
  return (
    <svg
      class={classe ?? 'veiculo-arte'}
      viewBox="0 0 320 170"
      role="img"
      aria-label={`Ilustração de ${ROTULOS_VEICULO[tipo]}`}
    >
      <ellipse cx="160" cy="154" rx="132" ry="9" fill="#0f172a" opacity=".13" />

      {tipo === 'fiorino' && (
        <g>
          <path
            d="M38 106c0-12 10-22 22-22h126c10 0 19 5 24 13l28 42c2 3 3 7 3 11v5c0 7-5 12-12 12H50c-7 0-12-5-12-12v-49Z"
            fill="#2563eb"
          />
          <path d="M44 118h226v14H44z" fill="#1e40af" opacity=".32" />
          <path d="M52 94h62v30H52z" fill="#dbeafe" stroke="#93c5fd" stroke-width="1.6" />
          <path d="M124 94h38c6 0 11 3 14 8l14 22h-66V94Z" fill="#dbeafe" stroke="#93c5fd" stroke-width="1.6" />
          <path d="M156 94v30M52 124h230" stroke="#1e40af" stroke-width="1.6" opacity=".55" />
          <rect x="44" y="132" width="228" height="8" rx="4" fill="#1e40af" />
          <rect x="32" y="112" width="8" height="15" rx="3" fill="#ef4444" />
          <rect x="296" y="110" width="11" height="15" rx="3" fill="#fde68a" stroke="#d97706" stroke-width="1.2" />
          <rect x="168" y="114" width="13" height="3.5" rx="1.8" fill="#93c5fd" />
          <path d="M66 108h34M66 114h22" stroke="#bfdbfe" stroke-width="3" stroke-linecap="round" opacity=".8" />
          <path d="M212 84h14" stroke="#1e40af" stroke-width="3" stroke-linecap="round" />
          <rect x="224" y="78" width="13" height="9" rx="3" fill="#fbbf24" stroke="#d97706" stroke-width="1.4" />
          <circle cx="84" cy="140" r="19" fill="#0f172a" />
          <circle cx="84" cy="140" r="8" fill="#cbd5e1" />
          <circle cx="84" cy="140" r="3" fill="#64748b" />
          <circle cx="250" cy="140" r="19" fill="#0f172a" />
          <circle cx="250" cy="140" r="8" fill="#cbd5e1" />
          <circle cx="250" cy="140" r="3" fill="#64748b" />
        </g>
      )}

      {tipo === 'kombi' && (
        <g>
          <path
            d="M38 112c0-24 18-42 42-42h130c20 0 36 13 40 32l8 30c1 4 2 7 2 10v2c0 7-5 12-12 12H50c-7 0-12-5-12-12v-32Z"
            fill="#22c55e"
          />
          <path d="M56 82c0-8 7-15 15-15h44v39H56V82Z" fill="#dcfce7" opacity=".95" />
          <path d="M124 67h70c12 0 22 8 25 20l8 19h-103V67Z" fill="#dcfce7" opacity=".92" />
          <path d="M38 120h256v10H38z" fill="#15803d" />
          <path d="M56 106h222" stroke="#bbf7d0" stroke-width="4" opacity=".6" />
          <path d="M150 70v64M190 70v64" stroke="#15803d" stroke-width="1.8" opacity=".38" />
          <rect x="44" y="132" width="244" height="8" rx="4" fill="#15803d" />
          <rect x="32" y="112" width="8" height="15" rx="3" fill="#ef4444" />
          <rect x="294" y="110" width="11" height="15" rx="3" fill="#fde68a" stroke="#d97706" stroke-width="1.2" />
          <circle cx="82" cy="140" r="19" fill="#0f172a" />
          <circle cx="82" cy="140" r="8" fill="#cbd5e1" />
          <circle cx="82" cy="140" r="3" fill="#64748b" />
          <circle cx="244" cy="140" r="19" fill="#0f172a" />
          <circle cx="244" cy="140" r="8" fill="#cbd5e1" />
          <circle cx="244" cy="140" r="3" fill="#64748b" />
        </g>
      )}

      {tipo === 'van' && (
        <g>
          <path
            d="M24 112c0-14 11-25 25-25h186c14 0 25 8 28 21l18 42c1 3 2 6 2 9v4c0 7-6 12-13 12H37c-7 0-13-5-13-12v-51Z"
            fill="#0ea5e9"
          />
          <path d="M42 96h70v36H42z" fill="#e0f2fe" stroke="#7dd3fc" stroke-width="1.6" />
          <path d="M122 96h46v36h-46z" fill="#e0f2fe" opacity=".78" />
          <path d="M178 96h42c7 0 13 4 16 10l14 26h-72V96Z" fill="#e0f2fe" stroke="#7dd3fc" stroke-width="1.6" />
          <path d="M24 128h276" stroke="#0369a1" stroke-width="2" opacity=".45" />
          <path d="M160 96v36M42 120h260" stroke="#0369a1" stroke-width="1.8" opacity=".4" />
          <rect x="36" y="138" width="250" height="8" rx="4" fill="#0369a1" />
          <rect x="286" y="116" width="15" height="12" rx="3" fill="#fde68a" stroke="#d97706" stroke-width="1.2" />
          <rect x="100" y="82" width="40" height="6" rx="3" fill="#0369a1" opacity=".65" />
          <circle cx="78" cy="140" r="19" fill="#0f172a" />
          <circle cx="78" cy="140" r="8" fill="#cbd5e1" />
          <circle cx="78" cy="140" r="3" fill="#64748b" />
          <circle cx="250" cy="140" r="19" fill="#0f172a" />
          <circle cx="250" cy="140" r="8" fill="#cbd5e1" />
          <circle cx="250" cy="140" r="3" fill="#64748b" />
        </g>
      )}

      {tipo === 'tres_quartos' && (
        <g>
          <rect x="26" y="122" width="190" height="12" rx="4" fill="#475569" />
          <rect x="30" y="96" width="180" height="34" rx="6" fill="#e2e8f0" stroke="#94a3b8" stroke-width="1.6" />
          <path d="M36 106h168M36 116h168" stroke="#cbd5e1" stroke-width="1.6" />
          <path d="M40 96c8-34 34-48 80-48s72 14 80 48Z" fill="#fbbf24" stroke="#b45309" stroke-width="2" />
          <path d="M40 96h160" stroke="#b45309" stroke-width="2.2" />
          <path d="M62 96c4-24 18-34 38-38M118 58c20 4 34 14 38 38" fill="none" stroke="#fde68a" stroke-width="2" opacity=".85" />
          <path d="M58 92v22M202 92v22" stroke="#64748b" stroke-width="2.4" />
          <path d="M206 92c0-8 6-14 14-14h56c7 0 13 4 16 10l18 38c1 3 2 6 2 9v3c0 5-4 9-9 9h-97V92Z" fill="#1d4ed8" stroke="#1e3a8a" stroke-width="1.6" />
          <path d="M220 90h38c6 0 11 3 14 8l12 25h-64V90Z" fill="#dbeafe" stroke="#93c5fd" stroke-width="1.5" />
          <path d="M270 90h12c4 0 8 2 10 6l12 27h-34V90Z" fill="#dbeafe" opacity=".82" />
          <path d="M278 96v50" stroke="#1e40af" stroke-width="1.6" opacity=".5" />
          <rect x="286" y="116" width="12" height="4" rx="2" fill="#93c5fd" />
          <rect x="206" y="132" width="112" height="8" rx="4" fill="#1e3a8a" />
          <rect x="306" y="108" width="10" height="15" rx="3" fill="#fde68a" stroke="#d97706" stroke-width="1.2" />
          <rect x="198" y="106" width="9" height="20" rx="3" fill="#f59e0b" />
          <path d="M196 96h14" stroke="#172554" stroke-width="3.4" stroke-linecap="round" />
          <rect x="302" y="92" width="7" height="20" rx="3" fill="#172554" />
          <rect x="310" y="90" width="8" height="10" rx="3" fill="#fbbf24" stroke="#d97706" stroke-width="1.2" />
          <circle cx="76" cy="140" r="20" fill="#0f172a" />
          <circle cx="76" cy="140" r="9" fill="#cbd5e1" />
          <circle cx="76" cy="140" r="3.5" fill="#64748b" />
          <circle cx="252" cy="140" r="20" fill="#0f172a" />
          <circle cx="252" cy="140" r="9" fill="#cbd5e1" />
          <circle cx="252" cy="140" r="3.5" fill="#64748b" />
        </g>
      )}

      {tipo === 'bau' && (
        <g>
          <rect x="18" y="42" width="202" height="96" rx="8" fill="#f8fafc" stroke="#94a3b8" stroke-width="2" />
          <rect x="18" y="42" width="202" height="12" rx="6" fill="#e2e8f0" />
          <path d="M30 58v76M46 58v76M62 58v76M78 58v76M94 58v76M110 58v76M126 58v76M142 58v76M158 58v76M174 58v76M190 58v76M206 58v76" stroke="#e2e8f0" stroke-width="3" />
          <rect x="18" y="114" width="202" height="24" fill="#1d4ed8" />
          <path d="M18 120h202" stroke="#1e40af" stroke-width="2" />
          <rect x="112" y="80" width="94" height="26" rx="6" fill="#1d4ed8" />
          <text x="159" y="98" text-anchor="middle" font-family="system-ui,sans-serif" font-size="13" font-weight="800" letter-spacing="1.2" fill="#fff">
            MUDANÇA
          </text>
          <path d="M220 42v96" stroke="#64748b" stroke-width="3" />
          <path d="M226 58h9M226 88h9M226 118h9" stroke="#94a3b8" stroke-width="2.4" />
          <path d="M220 88c0-9 7-16 16-16h44c8 0 14 4 17 11l15 38c1 3 2 6 2 9v4c0 6-5 10-11 10h-83V88Z" fill="#1d4ed8" stroke="#1e3a8a" stroke-width="1.6" />
          <path d="M234 86h34c6 0 10 3 13 8l12 26h-59V86Z" fill="#dbeafe" stroke="#93c5fd" stroke-width="1.5" />
          <path d="M286 92v44" stroke="#1e40af" stroke-width="1.6" opacity=".5" />
          <rect x="294" y="112" width="11" height="4" rx="2" fill="#93c5fd" />
          <rect x="220" y="132" width="88" height="8" rx="4" fill="#1e3a8a" />
          <rect x="304" y="106" width="9" height="15" rx="3" fill="#fde68a" stroke="#d97706" stroke-width="1.2" />
          <rect x="212" y="102" width="9" height="20" rx="3" fill="#f59e0b" />
          <path d="M210 92h14" stroke="#172554" stroke-width="3.4" stroke-linecap="round" />
          <rect x="300" y="90" width="7" height="20" rx="3" fill="#172554" />
          <rect x="308" y="88" width="8" height="10" rx="3" fill="#fbbf24" stroke="#d97706" stroke-width="1.2" />
          <circle cx="64" cy="140" r="20" fill="#0f172a" />
          <circle cx="64" cy="140" r="9" fill="#cbd5e1" />
          <circle cx="64" cy="140" r="3.5" fill="#64748b" />
          <circle cx="176" cy="140" r="20" fill="#0f172a" />
          <circle cx="176" cy="140" r="9" fill="#cbd5e1" />
          <circle cx="176" cy="140" r="3.5" fill="#64748b" />
          <circle cx="270" cy="140" r="20" fill="#0f172a" />
          <circle cx="270" cy="140" r="9" fill="#cbd5e1" />
          <circle cx="270" cy="140" r="3.5" fill="#64748b" />
        </g>
      )}

      {tipo === 'carreta' && (
        <g>
          <rect x="6" y="34" width="236" height="104" rx="8" fill="#f8fafc" stroke="#94a3b8" stroke-width="2" />
          <rect x="6" y="34" width="236" height="12" rx="6" fill="#e2e8f0" />
          <path d="M20 52v70M38 52v70M56 52v70M74 52v70M92 52v70M110 52v70M128 52v70M146 52v70M164 52v70M182 52v70M200 52v70M218 52v70M236 52v70" stroke="#e2e8f0" stroke-width="3" />
          <rect x="6" y="116" width="236" height="22" fill="#1d4ed8" />
          <path d="M6 122h236" stroke="#1e40af" stroke-width="2" />
          <rect x="88" y="70" width="120" height="28" rx="6" fill="#1d4ed8" />
          <text x="148" y="89" text-anchor="middle" font-family="system-ui,sans-serif" font-size="13" font-weight="800" letter-spacing="1.4" fill="#fff">
            MUDANÇA
          </text>
          <path d="M242 34v104" stroke="#64748b" stroke-width="3" />
          <path d="M242 84c0-9 7-16 16-16h38c7 0 13 4 16 10l14 34c1 3 2 6 2 9v5c0 6-5 10-11 10h-75V84Z" fill="#1d4ed8" stroke="#1e3a8a" stroke-width="1.6" />
          <path d="M256 82h26c5 0 9 3 11 7l11 24h-48V82Z" fill="#dbeafe" stroke="#93c5fd" stroke-width="1.5" />
          <path d="M294 88v48" stroke="#1e40af" stroke-width="1.6" opacity=".5" />
          <rect x="242" y="130" width="78" height="8" rx="4" fill="#1e3a8a" />
          <rect x="310" y="104" width="8" height="15" rx="3" fill="#fde68a" stroke="#d97706" stroke-width="1.2" />
          <rect x="236" y="100" width="9" height="20" rx="3" fill="#f59e0b" />
          <path d="M234 90h14" stroke="#172554" stroke-width="3.4" stroke-linecap="round" />
          <rect x="308" y="88" width="7" height="18" rx="3" fill="#172554" />
          <rect x="314" y="86" width="6" height="9" rx="2" fill="#fbbf24" stroke="#d97706" stroke-width="1.1" />
          <circle cx="46" cy="142" r="18" fill="#0f172a" />
          <circle cx="46" cy="142" r="8" fill="#cbd5e1" />
          <circle cx="92" cy="142" r="18" fill="#0f172a" />
          <circle cx="92" cy="142" r="8" fill="#cbd5e1" />
          <circle cx="138" cy="142" r="18" fill="#0f172a" />
          <circle cx="138" cy="142" r="8" fill="#cbd5e1" />
          <circle cx="286" cy="142" r="18" fill="#0f172a" />
          <circle cx="286" cy="142" r="8" fill="#cbd5e1" />
        </g>
      )}
    </svg>
  );
};

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
