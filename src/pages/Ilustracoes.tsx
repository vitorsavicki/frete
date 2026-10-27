import type { FC } from 'hono/jsx';

// Ilustrações próprias do Quero Mudar Já.
//
// Por que SVG inline: o site tem CSP fechada (script-src 'self') e não quer
// depender de banco de imagens. As ilustrações são leves, nítidas em qualquer
// tela, funcionam offline no APK e podem ser animadas por CSS — com fallback
// para quem prefere menos movimento (prefers-reduced-motion).

export const MascoteMudanca: FC<{ class?: string; idPrefixo?: string; decorativo?: boolean }> = ({
  class: classe,
  idPrefixo = 'mascote',
  decorativo = false,
}) => {
  const idCorpo = `${idPrefixo}-corpo`;
  const idCap = `${idPrefixo}-cap`;
  return (
    <svg
      class={classe ?? 'mascote'}
      viewBox="0 0 220 240"
      role={decorativo ? undefined : 'img'}
      aria-hidden={decorativo ? 'true' : undefined}
      aria-label={
        decorativo
          ? undefined
          : 'Zé Mudança, o mascote do Quero Mudar Já: uma van de pano com braços e pernas, segurando uma caixa'
      }
    >
      <defs>
        <linearGradient id={idCorpo} x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#60a5fa" />
          <stop offset="1" stop-color="#2563eb" />
        </linearGradient>
        <linearGradient id={idCap} x1="0" y1="0" x2="0" y2="1">
          <stop offset="0" stop-color="#1e40af" />
          <stop offset="1" stop-color="#172554" />
        </linearGradient>
      </defs>

      <ellipse cx="110" cy="230" rx="66" ry="9" fill="#0f172a" opacity=".13" />

      <g class="mascote__corpo">
        {/* pernas e pés de pano */}
        <rect x="78" y="188" width="20" height="30" rx="9" fill="#1e3a8a" />
        <rect x="122" y="188" width="20" height="30" rx="9" fill="#1e3a8a" />
        <circle cx="88" cy="220" r="15" fill="#172554" />
        <circle cx="132" cy="220" r="15" fill="#172554" />
        <circle cx="88" cy="220" r="9" fill="#1d4ed8" />
        <circle cx="132" cy="220" r="9" fill="#1d4ed8" />
        <path d="M80 220h16M124 220h16" stroke="#93c5fd" stroke-width="2.4" stroke-dasharray="3 3" />

        {/* braço esquerdo segurando a caixa */}
        <path d="M48 136c-10 10-14 22-12 32" fill="none" stroke="#2563eb" stroke-width="13" stroke-linecap="round" />
        <path d="M39 160c-2 3-3 5-3 8" fill="none" stroke="#fbbf24" stroke-width="13" stroke-linecap="round" />
        <circle cx="35" cy="170" r="9" fill="#fde68a" stroke="#d97706" stroke-width="2" />
        <path d="M31 168l8 4M36 165l-1 10" stroke="#d97706" stroke-width="1.5" stroke-linecap="round" />

        {/* caixa na mão */}
        <g>
          <rect x="8" y="156" width="42" height="36" rx="3" fill="#fbbf24" stroke="#d97706" stroke-width="2" />
          <path d="M8 168h42M27 156v36" stroke="#fef3c7" stroke-width="4" opacity=".9" />
          <path d="M14 162l8 6M22 162l-8 6" stroke="#b45309" stroke-width="1.6" stroke-linecap="round" />
        </g>

        {/* corpo: van de pano, frente para quem olha */}
        <path
          d="M42 94c0-22 18-40 40-40h56c22 0 40 18 40 40v70c0 22-16 40-38 40H80c-22 0-38-18-38-40V94Z"
          fill={`url(#${idCorpo})`}
          stroke="#1e40af"
          stroke-width="3"
        />
        <path
          d="M48 100c0-18 14-32 32-32h60c18 0 32 14 32 32v58c0 18-12 32-30 32H78c-18 0-30-14-30-32v-58Z"
          fill="none"
          stroke="#bfdbfe"
          stroke-width="2"
          stroke-dasharray="6 5"
          opacity=".85"
        />

        {/* para-brisa / rosto */}
        <rect x="54" y="72" width="112" height="80" rx="24" fill="#e0f2fe" stroke="#1e40af" stroke-width="3" />
        <path d="M66 84c8-6 18-9 30-9h32" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" opacity=".85" />

        {/* olhos de botão */}
        <g class="mascote__olhos">
          <circle cx="86" cy="110" r="13" fill="#0f172a" />
          <circle cx="134" cy="110" r="13" fill="#0f172a" />
          <circle cx="83" cy="106" r="4" fill="#fff" opacity=".85" />
          <circle cx="131" cy="106" r="4" fill="#fff" opacity=".85" />
          <circle cx="86" cy="110" r="2" fill="#64748b" />
          <circle cx="134" cy="110" r="2" fill="#64748b" />
        </g>
        <path d="M74 92c5-5 12-7 20-7M126 85c8 0 15 2 20 7" fill="none" stroke="#1e40af" stroke-width="2.2" stroke-linecap="round" stroke-dasharray="3 3" />
        <ellipse cx="67" cy="132" rx="8" ry="6" fill="#fca5a5" opacity=".65" />
        <ellipse cx="153" cy="132" rx="8" ry="6" fill="#fca5a5" opacity=".65" />
        <path d="M96 136c5 7 13 10 22 10s17-3 22-10" fill="none" stroke="#0f172a" stroke-width="3.5" stroke-linecap="round" />
        <path d="M108 131h16" stroke="#1e40af" stroke-width="3" stroke-linecap="round" />

        {/* faróis e para-choque */}
        <circle cx="66" cy="166" r="9" fill="#fde68a" stroke="#d97706" stroke-width="2" />
        <circle cx="154" cy="166" r="9" fill="#fde68a" stroke="#d97706" stroke-width="2" />
        <path d="M60 166h12M148 166h12" stroke="#d97706" stroke-width="1.6" stroke-linecap="round" />
        <rect x="48" y="176" width="124" height="22" rx="10" fill="#fbbf24" stroke="#d97706" stroke-width="2.4" />
        <rect x="94" y="181" width="32" height="12" rx="3" fill="#fff" stroke="#d97706" stroke-width="1.4" />
        <text x="110" y="190" text-anchor="middle" font-family="system-ui,sans-serif" font-size="7.5" font-weight="800" fill="#92400e">
          ZM 24
        </text>

        {/* retrovisores / orelhas */}
        <path d="M42 108H30" stroke="#1e40af" stroke-width="4" stroke-linecap="round" />
        <rect x="18" y="100" width="15" height="13" rx="6" fill="#fbbf24" stroke="#d97706" stroke-width="2" />
        <path d="M178 108h12" stroke="#1e40af" stroke-width="4" stroke-linecap="round" />
        <rect x="187" y="100" width="15" height="13" rx="6" fill="#fbbf24" stroke="#d97706" stroke-width="2" />

        {/* braço direito acenando */}
        <g class="mascote__braco mascote__braco--aceno">
          <path d="M176 136c12-10 19-21 21-32" fill="none" stroke="#2563eb" stroke-width="13" stroke-linecap="round" />
          <path d="M195 108c2-4 3-7 3-10" fill="none" stroke="#fbbf24" stroke-width="13" stroke-linecap="round" />
          <circle cx="199" cy="93" r="10" fill="#fde68a" stroke="#d97706" stroke-width="2" />
          <path d="M195 90l8 5M200 86l-2 10" stroke="#d97706" stroke-width="1.5" stroke-linecap="round" />
        </g>

        {/* costuras e remendo de pano */}
        <path d="M58 62c14-9 30-13 52-13s38 4 52 13" fill="none" stroke="#93c5fd" stroke-width="2.2" stroke-dasharray="5 5" opacity=".85" />
        <path d="M52 154v10M168 150v12" stroke="#93c5fd" stroke-width="2.2" stroke-dasharray="4 4" opacity=".8" />
        <g transform="rotate(8 160 58)">
          <rect x="148" y="49" width="24" height="19" rx="3" fill="#fbbf24" stroke="#d97706" stroke-width="1.8" />
          <path d="M153 54l14 9M167 54l-14 9" stroke="#b45309" stroke-width="1.4" stroke-linecap="round" />
        </g>

        {/* boné de pano */}
        <path d="M62 58c0-20 21-34 48-34s48 14 48 34Z" fill={`url(#${idCap})`} stroke="#0f172a" stroke-width="2.4" />
        <rect x="59" y="48" width="102" height="13" rx="6.5" fill="#172554" stroke="#0f172a" stroke-width="2" />
        <circle cx="110" cy="22" r="8" fill="#fbbf24" stroke="#d97706" stroke-width="2" />
        <path d="M72 43c8-8 20-13 34-14" fill="none" stroke="#3b82f6" stroke-width="2.4" stroke-linecap="round" opacity=".75" />
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
    viewBox="0 0 720 460"
    role="img"
    aria-label="Van de mudança com sofá e caixas, ao lado do mascote Zé Mudança"
  >
    <defs>
      <linearGradient id="cena-ceu" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="#dbeafe" />
        <stop offset=".7" stop-color="#eff6ff" />
        <stop offset="1" stop-color="#f8fafc" />
      </linearGradient>
      <linearGradient id="cena-van" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="#3b82f6" />
        <stop offset="1" stop-color="#1d4ed8" />
      </linearGradient>
      <linearGradient id="cena-box" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" stop-color="#fcd34d" />
        <stop offset="1" stop-color="#f59e0b" />
      </linearGradient>
    </defs>

    <rect width="720" height="460" rx="28" fill="url(#cena-ceu)" />
    <circle cx="610" cy="92" r="38" fill="#fde68a" opacity=".9" />
    <path d="M0 318c90-42 180-42 270 0s180 42 270 0 180-42 270 0v142H0Z" fill="#bfdbfe" opacity=".55" />
    <path d="M0 356c100-34 200-34 300 0s200 34 300 0 120-20 180-10v114H0Z" fill="#93c5fd" opacity=".35" />
    <path d="M0 404h720v56H0Z" fill="#334155" />
    <path d="M0 418h720v42H0Z" fill="#1e293b" />
    <g class="cena__estrada" stroke="#facc15" stroke-width="6" stroke-dasharray="34 28" opacity=".8">
      <path d="M0 438h720" />
    </g>

    <g class="cena__nuvens" fill="#fff" opacity=".9">
      <ellipse cx="128" cy="96" rx="42" ry="18" />
      <ellipse cx="164" cy="88" rx="30" ry="16" />
      <ellipse cx="92" cy="88" rx="24" ry="13" />
    </g>

    <g class="cena__van">
      <ellipse cx="392" cy="402" rx="190" ry="16" fill="#0f172a" opacity=".18" />
      <path
        d="M238 268c0-18 14-32 32-32h214c14 0 26 8 31 21l24 56c2 5 3 10 3 16v28c0 10-8 18-18 18H256c-10 0-18-8-18-18V268Z"
        fill="url(#cena-van)"
      />
      <path d="M236 302h310v44H236z" fill="#1e40af" opacity=".38" />
      <path d="M400 250h78c6 0 11 3 14 9l24 47H400v-56Z" fill="#dbeafe" />
      <path d="M410 260h58c4 0 7 2 9 6l18 32H410v-38Z" fill="#93c5fd" />
      <path d="M300 250h82v56h-82z" fill="#dbeafe" opacity=".95" />
      <path d="M308 258h66v40h-66z" fill="#93c5fd" opacity=".9" />
      <path d="M238 268c0-18 14-32 32-32h26v88h-58v-56Z" fill="#2563eb" />
      <path d="M282 344h22v22h-22z" fill="#e2e8f0" opacity=".35" />
      <path d="M470 344h30v18h-30z" fill="#fbbf24" />
      <path d="M500 344h10v18h-10z" fill="#ef4444" />

      <g class="cena__rodas" fill="#0f172a">
        <circle cx="300" cy="374" r="30" />
        <circle cx="488" cy="374" r="30" />
      </g>
      <g class="cena__rodas" fill="#cbd5e1">
        <circle cx="300" cy="374" r="14" />
        <circle cx="488" cy="374" r="14" />
      </g>
      <g class="cena__rodas" fill="#64748b">
        <path d="M300 362v24M288 374h24M488 362v24M476 374h24" stroke="#64748b" stroke-width="5" />
      </g>
    </g>

    <g class="cena__carga">
      <path d="M88 348h126v50H88z" fill="url(#cena-box)" />
      <path d="M88 348h126v10H88z" fill="#fef3c7" opacity=".8" />
      <path d="M126 348h14v50h-14z" fill="#fef3c7" opacity=".65" />
      <path d="M112 300h76v48h-76z" fill="#fbbf24" />
      <path d="M112 300h76v9h-76z" fill="#fef3c7" opacity=".8" />
      <path d="M140 300h12v48h-12z" fill="#fef3c7" opacity=".6" />
      <path d="M196 330h48v40h-48z" fill="#f59e0b" />
      <path d="M196 330h48v8h-48z" fill="#fef3c7" opacity=".75" />

      <g class="cena__sofa">
        <path d="M34 262h90v58H34z" fill="#f97316" />
        <path d="M26 300c0-10 8-18 18-18h70c10 0 18 8 18 18v34H26v-34Z" fill="#fb923c" />
        <path d="M40 274h34v48H40zM88 274h28v48H88z" fill="#fdba74" opacity=".8" />
        <path d="M20 328h118v18H20z" fill="#ea580c" />
        <path d="M32 346h10v18H32zM116 346h10v18h-10z" fill="#7c2d12" />
      </g>
    </g>

    <g transform="translate(560 294) scale(.46)">
      <MascoteMudanca class="mascote mascote--cena" idPrefixo="cena-mascote" />
    </g>

    <g class="cena__balao">
      <path
        d="M486 146h150c14 0 24 10 24 24v46c0 14-10 24-24 24h-76l-26 26 2-26h-50c-14 0-24-10-24-24v-46c0-14 10-24 24-24Z"
        fill="#fff"
        stroke="#bfdbfe"
        stroke-width="3"
      />
      <text x="570" y="176" text-anchor="middle" font-family="system-ui,sans-serif" font-size="17" font-weight="700" fill="#0f172a">
        Bora mudar?
      </text>
      <text x="570" y="202" text-anchor="middle" font-family="system-ui,sans-serif" font-size="13" fill="#475569">
        você escolhe o preço
      </text>
    </g>
  </svg>
);

type TipoArte = 'fiorino' | 'kombi' | 'van' | 'tres_quartos' | 'bau' | 'carreta';

interface VeiculoArteProps {
  tipo: TipoArte;
  class?: string;
}

/** Ilustração lateral dos veículos usados em mudanças. */
export const VeiculoArte: FC<VeiculoArteProps> = ({ tipo, class: classe }) => {
  const carroceria = tipo === 'bau' || tipo === 'carreta' ? '#f59e0b' : '#1d4ed8';
  const detalhe = tipo === 'bau' || tipo === 'carreta' ? '#b45309' : '#1e40af';

  return (
    <svg
      class={classe ?? 'veiculo-arte'}
      viewBox="0 0 260 130"
      role="img"
      aria-label={`Ilustração de ${tipo.replace('_', ' ')}`}
    >
      <ellipse cx="130" cy="112" rx="92" ry="9" fill="#0f172a" opacity=".14" />
      {tipo === 'fiorino' && (
        <g>
          <path d="M42 52c0-10 8-18 18-18h96c9 0 17 6 20 15l15 40v20c0 5-4 9-9 9H51c-5 0-9-4-9-9V52Z" fill={carroceria} />
          <path d="M60 48h44v30H60zM112 48h30c5 0 9 3 11 8l10 22h-51V48Z" fill="#dbeafe" />
          <path d="M48 82h164v18H48z" fill={detalhe} opacity=".35" />
          <rect x="198" y="72" width="16" height="12" rx="4" fill="#fde68a" />
          <circle cx="80" cy="100" r="16" fill="#0f172a" />
          <circle cx="80" cy="100" r="7" fill="#cbd5e1" />
          <circle cx="186" cy="100" r="16" fill="#0f172a" />
          <circle cx="186" cy="100" r="7" fill="#cbd5e1" />
        </g>
      )}
      {tipo === 'kombi' && (
        <g>
          <path d="M40 74c0-22 18-40 40-40h112c19 0 35 15 35 34v26c0 6-5 10-10 10H50c-6 0-10-4-10-10V74Z" fill="#22c55e" />
          <path d="M58 60c0-12 10-22 22-22h34v50H58V60Z" fill="#dbeafe" />
          <path d="M122 38h52c14 0 26 10 29 24l4 26h-85V38Z" fill="#bbf7d0" opacity=".85" />
          <path d="M40 90h187v14H40z" fill="#15803d" />
          <circle cx="78" cy="104" r="15" fill="#0f172a" />
          <circle cx="78" cy="104" r="7" fill="#cbd5e1" />
          <circle cx="190" cy="104" r="15" fill="#0f172a" />
          <circle cx="190" cy="104" r="7" fill="#cbd5e1" />
          <path d="M60 88h120" stroke="#fff" stroke-width="4" opacity=".55" />
        </g>
      )}
      {(tipo === 'van' || tipo === 'tres_quartos') && (
        <g>
          <path d="M28 66c0-12 9-22 21-22h120c12 0 22 7 26 18l17 44v12c0 6-5 10-11 10H39c-6 0-11-4-11-10V66Z" fill={tipo === 'van' ? '#0ea5e9' : carroceria} />
          <path d="M44 56h58v34H44zM110 56h56c7 0 13 4 15 10l14 24h-85V56Z" fill="#e0f2fe" />
          <path d="M32 96h196v14H32z" fill={tipo === 'van' ? '#0369a1' : detalhe} opacity=".4" />
          <rect x="224" y="82" width="14" height="12" rx="3" fill="#fde68a" />
          <circle cx="74" cy="108" r="17" fill="#0f172a" />
          <circle cx="74" cy="108" r="7" fill="#cbd5e1" />
          <circle cx="202" cy="108" r="17" fill="#0f172a" />
          <circle cx="202" cy="108" r="7" fill="#cbd5e1" />
        </g>
      )}
      {(tipo === 'bau' || tipo === 'carreta') && (
        <g>
          <path d="M18 40h160v66H18z" fill={carroceria} />
          <path d="M18 40h160v10H18zM18 58h160v3H18zM18 78h160v3H18zM18 98h160v3H18z" fill="#fef3c7" opacity=".55" />
          <path d="M178 58h30c8 0 15 5 18 13l13 35h-61V58Z" fill="#1e40af" />
          <path d="M186 64h20c5 0 9 3 11 8l9 24h-40V64Z" fill="#dbeafe" />
          <path d="M178 88h60v18h-60z" fill="#1e3a8a" />
          <rect x="234" y="90" width="13" height="12" rx="3" fill="#fde68a" />
          <circle cx="62" cy="110" r="16" fill="#0f172a" />
          <circle cx="62" cy="110" r="7" fill="#cbd5e1" />
          <circle cx="152" cy="110" r="16" fill="#0f172a" />
          <circle cx="152" cy="110" r="7" fill="#cbd5e1" />
          <circle cx="208" cy="110" r="16" fill="#0f172a" />
          <circle cx="208" cy="110" r="7" fill="#cbd5e1" />
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
