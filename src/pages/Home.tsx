import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { FreteCard } from './FreteCard';
import { CenaMudanca, IconeCliente, IconeTransportador, MascoteMudanca, VeiculoArte } from './Ilustracoes';
import { CATALOGO_CARGA } from '../lib/carga';
import type { Category, FreightResumo, User } from '../lib/db';

interface HomeProps {
  user: User | null;
  alertas: number;
  fretesRecentes: FreightResumo[];
  categorias: Category[];
  url?: string;
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

const ITENS_DESTAQUE = [
  'geladeira_2p',
  'sofa_3',
  'cama_box_casal',
  'guarda_roupa_3p',
  'maquina_lavar',
  'mesa_jantar',
  'tv_65',
  'caixa_media',
];

const FAQ = [
  {
    pergunta: 'Quanto custa publicar uma mudança?',
    resposta:
      'Publicar é grátis e não cobramos taxa de intermediação. Você paga apenas o valor combinado com o caminhoneiro que escolher.',
  },
  {
    pergunta: 'Como funcionam os orçamentos?',
    resposta:
      'Você publica a mudança e os caminhoneiros interessados enviam seus preços. Você compara os valores, vê os detalhes e escolhe o que fizer sentido. Não é obrigado a aceitar nenhum orçamento.',
  },
  {
    pergunta: 'Quem define o preço da mudança?',
    resposta:
      'O caminhoneiro. O site mostra referências de combustível e de mercado para ele se organizar, mas o valor do orçamento é sempre decidido por quem vai fazer a mudança — nunca imposto pelo site.',
  },
  {
    pergunta: 'Qual a diferença entre cliente e transportador?',
    resposta:
      'O cliente é quem precisa mudar: publica o que vai levar e escolhe o orçamento. O transportador é o caminhoneiro dono do veículo: confere se a carga cabe e envia o preço que cabe no bolso dele.',
  },
  {
    pergunta: 'O combustível é gasolina ou diesel?',
    resposta:
      'Depende do veículo. Fiorinos e veículos leves costumam usar gasolina; vans, caminhões 3/4, baús e carretas usam diesel (com ARLA 32). O caminhoneiro escolhe o combustível no perfil e informa o preço que paga na bomba.',
  },
];

export const HomePage: FC<HomeProps> = ({
  user,
  alertas,
  fretesRecentes,
  categorias,
  url = 'https://queromudarja.com.br/',
}) => {
  const origem = (() => {
    try {
      return new URL(url).origin;
    } catch {
      return 'https://queromudarja.com.br';
    }
  })();
  const canonical = `${origem}/`;

  const jsonLd = JSON.stringify({
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'WebSite',
        '@id': `${origem}/#site`,
        url: canonical,
        name: 'Quero Mudar Já',
        inLanguage: 'pt-BR',
        description:
          'Mudanças e fretes: quem vai mudar publica e caminhoneiros enviam orçamentos. Você escolhe o melhor preço.',
        potentialAction: {
          '@type': 'SearchAction',
          target: `${origem}/fretes?q={search_term_string}`,
          'query-input': 'required name=search_term_string',
        },
      },
      {
        '@type': 'Organization',
        '@id': `${origem}/#organizacao`,
        name: 'Quero Mudar Já',
        url: canonical,
        logo: `${origem}/img/app-icon-512.png`,
        areaServed: 'BR',
      },
      {
        '@type': 'FAQPage',
        mainEntity: FAQ.map((item) => ({
          '@type': 'Question',
          name: item.pergunta,
          acceptedAnswer: { '@type': 'Answer', text: item.resposta },
        })),
      },
    ],
  });

  return (
    <Layout
      titulo="Mudanças e fretes com orçamentos de caminhoneiros"
      user={user}
      alertas={alertas}
      canonical={canonical}
      jsonLd={jsonLd}
      descricao="Publique sua mudança grátis, receba orçamentos de caminhoneiros e escolha o melhor preço. Mudanças de casa, apartamento e fretes avulsos, sem taxa de intermediação."
    >
      <section class="hero hero--novo">
        <div class="hero__conteudo">
          <span class="eyebrow">
            <span class="eyebrow__dot" aria-hidden="true"></span>
            Orçamentos grátis · sem compromisso
          </span>
          <h1>
            Quem muda publica. <em>Quem transporta</em> manda o preço.
          </h1>
          <p class="hero__texto">
            Aqui você publica o que precisa levar — de um sofá a uma casa inteira — e caminhoneiros
            e vans de mudança enviam seus preços. Você compara, escolhe e combina direto com quem
            vai fazer a mudança. Sem taxa de intermediação.
          </p>

          <div class="hero__papeis">
            <a href={user ? '/novo-frete' : '/cadastro?papel=cliente'} class="papel-card papel-card--cliente">
              <IconeCliente class="papel-card__icone" />
              <span>
                <strong>Sou cliente</strong>
                <small>Preciso mudar e quero receber orçamentos</small>
              </span>
              <span class="papel-card__seta" aria-hidden="true">→</span>
            </a>
            <a
              href={user?.papel === 'transportador' ? '/fretes' : '/cadastro?papel=transportador'}
              class="papel-card papel-card--transportador"
            >
              <IconeTransportador class="papel-card__icone" />
              <span>
                <strong>Sou caminhoneiro</strong>
                <small>Tenho veículo e quero fazer mudanças</small>
              </span>
              <span class="papel-card__seta" aria-hidden="true">→</span>
            </a>
          </div>

          <ul class="hero__garantias">
            <li><span aria-hidden="true">✓</span> Publicar é grátis</li>
            <li><span aria-hidden="true">✓</span> Sem taxa de intermediação</li>
            <li><span aria-hidden="true">✓</span> Você escolhe o preço</li>
          </ul>
        </div>

        <div class="hero__arte">
          <CenaMudanca class="cena-mudanca cena-mudanca--hero" />
          <p class="hero__legenda">Quem decide o preço é o caminhoneiro.</p>
        </div>
      </section>

      <section class="secao secao--papeis">
        <div class="secao__cabecalho secao__cabecalho--centro">
          <div>
            <span class="eyebrow">Como funciona</span>
            <h2>Você publica a mudança. O caminhoneiro faz o orçamento.</h2>
            <p class="secao__descricao">
              Quem precisa mudar publica o que vai levar, de onde sai e para onde vai. Quem tem
              veículo vê a carga, confere se cabe e manda o preço. Você compara e escolhe.
            </p>
          </div>
        </div>

        <div class="fluxo-papeis">
          <article class="fluxo-papel fluxo-papel--cliente">
            <header>
              <span class="fluxo-papel__tag">Para quem muda</span>
              <h3>Cliente / contratante</h3>
              <p>Você tem móveis, eletrodomésticos ou uma casa inteira para levar.</p>
            </header>
            <ol>
              <li><b>1.</b> Monte a lista do que vai: geladeira, cama, sofá, caixas...</li>
              <li><b>2.</b> Informe origem, destino e a data da retirada.</li>
              <li><b>3.</b> Receba orçamentos, compare e escolha o que preferir.</li>
            </ol>
            <a href={user ? '/novo-frete' : '/cadastro?papel=cliente'} class="btn btn-primario">
              Publicar minha mudança
            </a>
          </article>

          <article class="fluxo-papel fluxo-papel--transportador">
            <header>
              <span class="fluxo-papel__tag">Para quem transporta</span>
              <h3>Transportador / caminhoneiro</h3>
              <p>Você tem fiorino, van, Kombi, 3/4, baú ou carreta e quer fretes.</p>
            </header>
            <ol>
              <li><b>1.</b> Cadastre as medidas e a capacidade do seu veículo.</li>
              <li><b>2.</b> Escolha gasolina ou diesel e ajuste seus custos reais.</li>
              <li><b>3.</b> Veja se a carga cabe e envie o orçamento que você define.</li>
            </ol>
            <a
              href={user?.papel === 'transportador' ? '/caminhao' : '/cadastro?papel=transportador'}
              class="btn btn-secundario"
            >
              Cadastrar meu veículo
            </a>
          </article>
        </div>
      </section>

      {categorias.length > 0 && (
        <section class="secao">
          <div class="secao__cabecalho">
            <div>
              <h2>O que você precisa levar?</h2>
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

          <div class="catalogo-faixa">
            <div class="catalogo-faixa__texto">
              <strong>Mais de 60 itens para montar sua lista</strong>
              <span>
                Escolha item por item — com peso e medidas — para o caminhoneiro saber exatamente
                se cabe no veículo.
              </span>
            </div>
            <ul class="catalogo-chips">
              {ITENS_DESTAQUE.map((id) => {
                const item = CATALOGO_CARGA.find((candidato) => candidato.id === id);
                if (!item) return null;
                return (
                  <li>
                    <span aria-hidden="true">{item.emoji}</span> {item.nome}
                  </li>
                );
              })}
              <li class="catalogo-chips__mais">+ muitos outros</li>
            </ul>
          </div>
        </section>
      )}

      <section class="secao secao--veiculos">
        <div class="secao__cabecalho">
          <div>
            <h2>Qual veículo cabe na sua mudança?</h2>
            <p class="secao__descricao">
              O caminhoneiro informa as medidas internas e o site confere se a carga cabe. Se não
              couber, ele nem perde tempo com o orçamento.
            </p>
          </div>
        </div>

        <div class="veiculos-grade">
          <article class="veiculo-card">
            <VeiculoArte tipo="fiorino" class="veiculo-card__arte" />
            <div class="veiculo-card__conteudo">
              <span class="veiculo-card__tag veiculo-card__tag--gasolina">Gasolina</span>
              <h3>Fiorino, Kombi e vans leves</h3>
              <p>Mudanças pequenas, quitinetes, eletrodomésticos avulsos e entregas urbanas.</p>
              <small>Até ~650 kg · ideal para 1 ambiente</small>
            </div>
          </article>

          <article class="veiculo-card">
            <VeiculoArte tipo="tres_quartos" class="veiculo-card__arte" />
            <div class="veiculo-card__conteudo">
              <span class="veiculo-card__tag veiculo-card__tag--diesel">Diesel + ARLA</span>
              <h3>Van de mudança e caminhão 3/4</h3>
              <p>Apartamento de 1 a 2 quartos, com sofá, cama, guarda-roupa e caixas.</p>
              <small>Até ~3.000 kg · ideal para 2 a 3 ambientes</small>
            </div>
          </article>

          <article class="veiculo-card">
            <VeiculoArte tipo="bau" class="veiculo-card__arte" />
            <div class="veiculo-card__conteudo">
              <span class="veiculo-card__tag veiculo-card__tag--diesel">Diesel + ARLA</span>
              <h3>Baú e carreta</h3>
              <p>Casa completa, mudança comercial, escritório e volumes grandes.</p>
              <small>Até ~8.000 kg (baú) · ideal para mudança completa</small>
            </div>
          </article>
        </div>
      </section>

      <section class="secao secao--custo">
        <div class="custo-painel">
          <div class="custo-painel__texto">
            <span class="eyebrow eyebrow--claro">Para caminhoneiros</span>
            <h2>Você vê o custo. Você decide o preço.</h2>
            <p>
              O simulador considera combustível (gasolina ou diesel), ARLA 32, pedágio, manutenção,
              horas de serviço, ajudantes e a margem que você quiser. O cliente não vê seus custos
              internos.
            </p>
            <ul class="custo-lista">
              <li><span aria-hidden="true">⛽</span> Preço por litro e consumo do seu veículo</li>
              <li><span aria-hidden="true">🛣️</span> Distância rodoviária real, não linha reta</li>
              <li><span aria-hidden="true">🧾</span> Faixa de mercado apenas como referência</li>
              <li><span aria-hidden="true">🔒</span> Preços de referência atualizados pela plataforma</li>
            </ul>
            <a href={user?.papel === 'transportador' ? '/caminhao' : '/cadastro?papel=transportador'} class="btn btn-branco">
              Configurar meu veículo
            </a>
          </div>
          <div class="custo-painel__arte">
            <div class="custo-exemplo">
              <span class="custo-exemplo__titulo">Exemplo de conta</span>
              <ul>
                <li><span>Combustível</span><b>R$ 368</b></li>
                <li><span>Pedágio</span><b>R$ 40</b></li>
                <li><span>Horas de serviço</span><b>R$ 90</b></li>
                <li><span>Ajudantes</span><b>R$ 240</b></li>
                <li><span>Margem do caminhoneiro</span><b>R$ 224</b></li>
              </ul>
              <strong>O preço final é você quem define.</strong>
            </div>
          </div>
        </div>
      </section>

      {fretesRecentes.length > 0 && (
        <section class="secao">
          <div class="secao__cabecalho">
            <div>
              <h2>Mudanças esperando por um orçamento</h2>
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

      <section class="app-faixa" aria-label="Aplicativo para Android">
        <span class="app-faixa__icone" aria-hidden="true">📱</span>
        <div class="app-faixa__texto">
          <strong>O Quero Mudar Já no seu celular</strong>
          <span>
            App Android grátis com as mesmas contas e mudanças do site. Instalação direta pelo APK,
            sem loja.
          </span>
        </div>
        <div class="app-faixa__acao">
          <a href="/app" class="btn btn-branco">Baixar o app</a>
        </div>
      </section>

      <section class="secao">
        <div class="secao__cabecalho secao__cabecalho--centro">
          <div>
            <h2>Perguntas frequentes</h2>
          </div>
        </div>
        <div class="faq">
          {FAQ.map((item) => (
            <details class="faq__item">
              <summary>{item.pergunta}</summary>
              <p>{item.resposta}</p>
            </details>
          ))}
        </div>
      </section>

      <section class="cta-banda">
        <div class="cta-banda__conteudo">
          <h2>Vai mudar? Peça seu primeiro orçamento.</h2>
          <p>
            Publique o que vai levar, receba os preços dos caminhoneiros e escolha o que couber no
            seu bolso. Se você tem veículo, encontre mudanças na sua rota e envie seu orçamento.
          </p>
          <div class="cta-banda__acoes">
            <a href={user ? '/novo-frete' : '/cadastro?papel=cliente'} class="btn btn-branco btn-grande">
              Publicar minha mudança
            </a>
            <a href="/cadastro?papel=transportador" class="btn btn-contorno-claro btn-grande">
              Sou caminhoneiro
            </a>
          </div>
        </div>
        <div class="cta-banda__arte cta-banda__arte--mascote" aria-hidden="true">
          <MascoteMudanca class="mascote mascote--cta" />
        </div>
      </section>
    </Layout>
  );
};
