import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { FreteCard } from './FreteCard';
import type { Category, FreightResumo, User } from '../lib/db';

interface HomeProps {
  user: User | null;
  alertas: number;
  fretesRecentes: FreightResumo[];
  categorias: Category[];
  totalAbertos: number;
  totalTransportadores: number;
  totalFretes: number;
}

function iconeCategoria(nome: string): string {
  const n = nome.toLowerCase();
  if (n.includes('mudan')) return '🏠';
  if (n.includes('móvel') || n.includes('movel')) return '🛋️';
  if (n.includes('eletro')) return '🔌';
  if (n.includes('frágil') || n.includes('fragil') || n.includes('especial')) return '🛡️';
  if (n.includes('escritório') || n.includes('escritorio') || n.includes('comercial')) return '🏢';
  if (n.includes('pequeno') || n.includes('encomenda')) return '📦';
  return '🚚';
}

export const HomePage: FC<HomeProps> = ({
  user,
  alertas,
  fretesRecentes,
  categorias,
  totalAbertos,
  totalTransportadores,
  totalFretes,
}) => (
  <Layout
    titulo="Mudanças e fretes pelo menor preço"
    user={user}
    alertas={alertas}
    descricao="Publique sua mudança ou frete grátis e receba lances de transportadores. No leilão reverso do Quero Mudar Já, o menor lance vence."
  >
    <section class="hero">
      <div class="hero__conteudo">
        <span class="eyebrow">
          <span class="eyebrow__dot" aria-hidden="true"></span>
          Leilão reverso · 100% grátis
        </span>
        <h1>
          Sua mudança pelo <em>menor preço</em>, sem complicação.
        </h1>
        <p class="hero__texto">
          Publique o que você precisa transportar e receba lances de transportadores.
          Você compara, escolhe e combina direto com o vencedor — sem taxa de intermediação.
        </p>
        <div class="hero__acoes">
          <a href={user ? '/novo-frete' : '/cadastro'} class="btn btn-primario btn-grande">
            Publicar meu frete grátis
            <span aria-hidden="true">→</span>
          </a>
          <a href="/fretes" class="btn btn-secundario btn-grande">
            Ver fretes abertos
          </a>
        </div>
        <ul class="hero__garantias">
          <li><span aria-hidden="true">✓</span> Sem cartão de crédito</li>
          <li><span aria-hidden="true">✓</span> Sem taxa de intermediação</li>
          <li><span aria-hidden="true">✓</span> Você decide quando aceitar</li>
        </ul>
      </div>

      <div class="hero__arte">
        <div class="hero__foto">
          <img
            src="/img/hero-truck.webp"
            alt="Caminhão de carga seguindo por uma estrada de montanha"
            width="900"
            height="600"
          />
          <div class="hero__selo hero__selo--preco">
            <small>menor lance</small>
            <strong>economia real</strong>
          </div>
          <div class="hero__selo hero__selo--online">
            <span class="pulso" aria-hidden="true"></span>
            fretes abertos agora
          </div>
        </div>
      </div>
    </section>

    <section class="stats" aria-label="Números do Quero Mudar Já">
      <div class="stats__item">
        <strong>{totalAbertos}</strong>
        <span>fretes abertos</span>
      </div>
      <div class="stats__item">
        <strong>{totalTransportadores}</strong>
        <span>transportadores</span>
      </div>
      <div class="stats__item">
        <strong>{totalFretes}</strong>
        <span>fretes publicados</span>
      </div>
      <div class="stats__item stats__item--destaque">
        <strong>R$ 0</strong>
        <span>de taxa para publicar</span>
      </div>
    </section>

    <section class="app-faixa" aria-label="Aplicativo para Android">
      <span class="app-faixa__icone" aria-hidden="true">📱</span>
      <div class="app-faixa__texto">
        <strong>Leve o Quero Mudar Já no bolso</strong>
        <span>
          App Android grátis com as mesmas contas e fretes do site. Instalação direta pelo APK,
          sem loja e sem custo.
        </span>
      </div>
      <div class="app-faixa__acao">
        <a href="/app" class="btn btn-branco">Baixar o app</a>
      </div>
    </section>

    {categorias.length > 0 && (
      <section class="secao">
        <div class="secao__cabecalho">
          <div>
            <span class="eyebrow">Do sofá à mudança completa</span>
            <h2>O que você precisa transportar?</h2>
          </div>
          <a href="/fretes" class="link-seta">
            Ver todos os fretes <span aria-hidden="true">→</span>
          </a>
        </div>
        <div class="categorias">
          {categorias.map((categoria) => (
            <a class="categoria-card" href={`/fretes?categoria=${categoria.id}`}>
              <span class="categoria-card__icone" aria-hidden="true">
                {iconeCategoria(categoria.nome)}
              </span>
              <span class="categoria-card__nome">{categoria.nome}</span>
              <span class="categoria-card__seta" aria-hidden="true">↗</span>
            </a>
          ))}
        </div>
      </section>
    )}

    {fretesRecentes.length > 0 && (
      <section class="secao">
        <div class="secao__cabecalho">
          <div>
            <span class="eyebrow">Recém-publicados</span>
            <h2>Fretes esperando por um lance</h2>
          </div>
          <a href="/fretes" class="link-seta">
            Explorar fretes <span aria-hidden="true">→</span>
          </a>
        </div>
        <div class="grade-fretes">
          {fretesRecentes.map((frete) => (
            <FreteCard frete={frete} />
          ))}
        </div>
      </section>
    )}

    <section class="secao">
      <div class="secao__cabecalho secao__cabecalho--centro">
        <div>
          <span class="eyebrow">Simples de verdade</span>
          <h2>Como funciona o leilão reverso</h2>
          <p class="secao__descricao">
            Quem precisa transportar publica. Quem transporta disputa pelo menor preço.
            Você só fecha negócio com o lance que fizer sentido.
          </p>
        </div>
      </div>
      <ol class="passos">
        <li class="passo">
          <span class="passo__numero">1</span>
          <div>
            <h3>Você publica</h3>
            <p>Informe origem, destino, data e o que será transportado. Leva poucos minutos.</p>
          </div>
        </li>
        <li class="passo">
          <span class="passo__numero">2</span>
          <div>
            <h3>Os transportadores disputam</h3>
            <p>Eles enviam lances cada vez menores para ganhar o seu frete.</p>
          </div>
        </li>
        <li class="passo">
          <span class="passo__numero">3</span>
          <div>
            <h3>Você escolhe o vencedor</h3>
            <p>Aceite o lance que preferir e combine a retirada direto com o transportador.</p>
          </div>
        </li>
      </ol>
    </section>

    <section class="secao">
      <div class="destaques">
        <article class="destaque">
          <span class="destaque__icone" aria-hidden="true">💸</span>
          <h3>Economia de verdade</h3>
          <p>Lances competitivos e nenhuma taxa de intermediação. O valor combinado é todo do transportador.</p>
        </article>
        <article class="destaque">
          <span class="destaque__icone" aria-hidden="true">🧭</span>
          <h3>Rota e mapa claros</h3>
          <p>Veja origem, destino e distância estimada em um mapa antes de decidir.</p>
        </article>
        <article class="destaque">
          <span class="destaque__icone" aria-hidden="true">⛽</span>
          <h3>Estimativa de custo</h3>
          <p>Transportadores calculam combustível, pedágio, manutenção e margem antes de dar o lance.</p>
        </article>
        <article class="destaque">
          <span class="destaque__icone" aria-hidden="true">🌦️</span>
          <h3>Previsão na retirada</h3>
          <p>Confira o clima no dia e no local da retirada para planejar a viagem sem surpresas.</p>
        </article>
      </div>
    </section>

    <section class="cta-banda">
      <div class="cta-banda__conteudo">
        <span class="eyebrow eyebrow--claro">Comece agora</span>
        <h2>Pronto para encontrar o melhor preço para a sua mudança?</h2>
        <p>Publique grátis, receba lances e escolha com tranquilidade. Se você transporta, encontre fretes na sua rota.</p>
        <div class="cta-banda__acoes">
          <a href={user ? '/novo-frete' : '/cadastro'} class="btn btn-branco btn-grande">
            Publicar frete grátis
          </a>
          <a href="/cadastro" class="btn btn-contorno-claro btn-grande">
            Criar conta de transportador
          </a>
        </div>
      </div>
      <div class="cta-banda__arte" aria-hidden="true">
        <img src="/img/boxes.webp" alt="" width="900" height="600" loading="lazy" />
      </div>
    </section>
  </Layout>
);
