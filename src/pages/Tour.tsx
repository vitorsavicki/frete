import type { FC } from 'hono/jsx';
import { IconeCliente, IconeTransportador, MascoteMudanca, VeiculoArte } from './Ilustracoes';
import type { User } from '../lib/db';

// Tour de primeiro acesso e ajuda fixa do Zé Mudança.
//
// O conteúdo é renderizado no servidor (funciona sem JS e mantém a CSP
// fechada). O app.js só controla abrir/fechar, avançar as etapas e guardar
// no localStorage que a pessoa já viu o passo a passo.

interface Passo {
  titulo: string;
  texto: string;
  fala: string;
  arte: 'lista' | 'veiculo' | 'propostas' | 'encontro' | 'pronto';
}

const PASSOS: Passo[] = [
  {
    titulo: 'Oi! Eu sou o Zé Mudança.',
    texto:
      'Vou te mostrar em poucos passos como planejar a mudança, entender o espaço e falar direto com quem vai transportar.',
    fala: 'Vem comigo! É rapidinho.',
    arte: 'lista',
  },
  {
    titulo: 'Comece pela lista do que vai levar.',
    texto:
      'Escolha móveis, eletrodomésticos e caixas. O site calcula o peso, o volume e mostra o veículo que acomoda tudo.',
    fala: 'Quanto mais completa a lista, melhor o plano.',
    arte: 'veiculo',
  },
  {
    titulo: 'Receba propostas de transportadores.',
    texto:
      'Quem tem o veículo certo para a sua carga envia uma proposta. Você compara os valores e os detalhes com calma.',
    fala: 'Nada de escolher no escuro.',
    arte: 'propostas',
  },
  {
    titulo: 'Combine direto, sem intermediário.',
    texto:
      'Escolha a proposta que faz sentido e acerte os detalhes direto com o transportador. Publicar a mudança é grátis.',
    fala: 'Você fala com quem vai fazer a mudança.',
    arte: 'encontro',
  },
  {
    titulo: 'Tudo pronto para começar.',
    texto:
      'Publique sua mudança ou, se você transporta, veja as cargas abertas na sua rota. Eu fico aqui do lado se precisar.',
    fala: 'Boa mudança! Estou por aqui.',
    arte: 'pronto',
  },
];

const ArtePasso: FC<{ tipo: Passo['arte'] }> = ({ tipo }) => {
  if (tipo === 'veiculo') {
    return (
      <div class="tour__arte-veiculo">
        <VeiculoArte tipo="bau" class="veiculo-arte veiculo-arte--tour" />
        <span class="tour__selo-arte">Plano de carga</span>
      </div>
    );
  }
  if (tipo === 'propostas') {
    return (
      <div class="tour__propostas">
        <span class="tour__proposta">
          <b>Transportador A</b>
          <strong>R$ 780</strong>
          <small>Van · 2 ajudantes</small>
        </span>
        <span class="tour__proposta tour__proposta--melhor">
          <b>Transportador B</b>
          <strong>R$ 850</strong>
          <small>3/4 · carga coberta</small>
        </span>
        <span class="tour__proposta">
          <b>Transportador C</b>
          <strong>R$ 920</strong>
          <small>Baú · embalagem</small>
        </span>
      </div>
    );
  }
  if (tipo === 'encontro') {
    return (
      <div class="tour__encontro">
        <IconeCliente class="tour__pessoa-icone" />
        <span class="tour__aperto" aria-hidden="true">
          🤝
        </span>
        <IconeTransportador class="tour__pessoa-icone" />
      </div>
    );
  }
  if (tipo === 'pronto') {
    return (
      <div class="tour__pronto">
        <span class="tour__check" aria-hidden="true">
          ✓
        </span>
        <span>Lista, espaço e proposta no mesmo lugar.</span>
      </div>
    );
  }
  return (
    <div class="tour__checklist">
      <span><i aria-hidden="true">1</i> O que vai levar</span>
      <span><i aria-hidden="true">2</i> Para onde vai</span>
      <span><i aria-hidden="true">3</i> Quando pode retirar</span>
    </div>
  );
};

export const TourMascote: FC<{ auto?: boolean; user?: User | null }> = ({
  auto = false,
  user = null,
}) => (
  <div
    class="tour"
    data-tour
    data-tour-auto={auto ? '1' : undefined}
    hidden
  >
    <div class="tour__fundo" data-tour-fechar></div>
    <section
      class="tour__caixa"
      role="dialog"
      aria-modal="true"
      aria-label="Passo a passo do Quero Mudar Já"
      tabindex={-1}
      data-tour-caixa
    >
      <button type="button" class="tour__fechar" data-tour-fechar aria-label="Fechar o passo a passo">
        ×
      </button>

      <div class="tour__lado">
        <span class="tour__etiqueta">Zé Mudança</span>
        <MascoteMudanca class="mascote mascote--tour" idPrefixo="tour-mascote" />
        <p class="tour__fala" data-tour-fala aria-live="polite">
          {PASSOS[0]?.fala}
        </p>
      </div>

      <div class="tour__conteudo">
        <div class="tour__topo">
          <span class="eyebrow">Primeiros passos</span>
          <span class="tour__contador" data-tour-contador>
            1 de {PASSOS.length}
          </span>
        </div>

        <div class="tour__passos">
          {PASSOS.map((passo, indice) => (
            <article
              class="tour__passo"
              data-tour-passo={String(indice)}
              data-tour-texto={passo.fala}
              hidden={indice !== 0}
            >
              <ArtePasso tipo={passo.arte} />
              <div>
                <h2>{passo.titulo}</h2>
                <p>{passo.texto}</p>
                {indice === PASSOS.length - 1 && (
                  <div class="tour__cta">
                    {user?.papel !== 'transportador' && (
                      <a
                        href={user ? '/novo-frete' : '/cadastro?papel=cliente'}
                        class="btn btn-primario btn-pequeno"
                        data-tour-fechar
                      >
                        Publicar minha mudança
                      </a>
                    )}
                    <a
                      href="/fretes"
                      class={`btn ${user?.papel === 'transportador' ? 'btn-primario' : 'btn-fantasma'} btn-pequeno`}
                      data-tour-fechar
                    >
                      {user?.papel === 'transportador' ? 'Encontrar mudanças' : 'Ver mudanças abertas'}
                    </a>
                  </div>
                )}
              </div>
            </article>
          ))}
        </div>

        <div class="tour__progresso" data-tour-progresso aria-label="Etapas do passo a passo">
          {PASSOS.map((passo, indice) => (
            <button
              type="button"
              class={`tour__ponto${indice === 0 ? ' tour__ponto--ativo' : ''}`}
              data-tour-ponto={String(indice)}
              aria-label={`Ir para o passo ${indice + 1}: ${passo.titulo}`}
              aria-current={indice === 0 ? 'step' : undefined}
            >
              <span aria-hidden="true"></span>
            </button>
          ))}
        </div>

        <div class="tour__acoes">
          <button type="button" class="tour__pular" data-tour-pular>
            Pular por agora
          </button>
          <div class="tour__navegacao">
            <button type="button" class="btn btn-fantasma btn-pequeno" data-tour-voltar disabled>
              Voltar
            </button>
            <button type="button" class="btn btn-primario btn-pequeno" data-tour-continuar>
              Continuar
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
);

export const AjudaFixa: FC<{ user: User | null }> = ({ user }) => (
  <aside class="ajuda-fixa" data-ajuda-fixa aria-label="Ajuda do Zé Mudança">
    <div class="ajuda-fixa__painel" id="ajuda-painel" hidden>
      <div class="ajuda-fixa__topo">
        <strong>Posso ajudar?</strong>
        <button
          type="button"
          class="ajuda-fixa__fechar"
          data-ajuda-toggle
          aria-label="Fechar ajuda"
        >
          ×
        </button>
      </div>
      <p>
        {user?.papel === 'transportador'
          ? 'Veja as cargas abertas na sua rota, confira se cabem no seu veículo e envie sua proposta.'
          : 'Monte a lista, entenda o espaço do veículo e escolha com calma a proposta que combina com a sua mudança.'}
      </p>
      <nav class="ajuda-fixa__links" aria-label="Atalhos de ajuda">
        <a href={user ? (user.papel === 'cliente' ? '/novo-frete' : '/fretes') : '/cadastro?papel=cliente'}>
          {user?.papel === 'transportador' ? 'Encontrar mudanças' : 'Publicar uma mudança'}
        </a>
        <a href="/fretes">Ver mudanças abertas</a>
        <a href="/app">Baixar o app Android</a>
        <button type="button" data-tour-abrir>
          Rever o passo a passo
        </button>
      </nav>
    </div>
    <button
      type="button"
      class="ajuda-fixa__botao"
      data-ajuda-toggle
      aria-expanded="false"
      aria-controls="ajuda-painel"
      aria-label="Precisa de ajuda?"
    >
      <MascoteMudanca class="mascote mascote--ajuda-fixa" idPrefixo="ajuda-fixa" decorativo />
      <span class="ajuda-fixa__texto ajuda-fixa__texto--longo">Precisa de ajuda?</span>
      <span class="ajuda-fixa__texto ajuda-fixa__texto--curto">Ajuda</span>
    </button>
  </aside>
);
