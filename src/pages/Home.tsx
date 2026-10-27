import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { CenaMudanca, IconeCliente, IconeTransportador, MascoteMudanca, VeiculoArte } from './Ilustracoes';
import { CATALOGO_CARGA } from '../lib/carga';
import type { Category, User } from '../lib/db';

interface HomeProps {
  user: User | null;
  alertas: number;
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

const PASSOS = [
  {
    numero: '1',
    titulo: 'Monte a lista do que vai levar',
    texto:
      'Escolha os móveis, eletrodomésticos e caixas. Informe a origem, o destino e a data da retirada.',
  },
  {
    numero: '2',
    titulo: 'Veja como tudo cabe',
    texto:
      'O site calcula peso e volume e mostra o plano de carga no veículo certo, antes de você fechar.',
  },
  {
    numero: '3',
    titulo: 'Receba propostas e combine',
    texto:
      'Compare as propostas dos transportadores, escolha com calma e acerte os detalhes direto com quem vai fazer a mudança.',
  },
];

const VEICULOS = [
  {
    tipo: 'fiorino' as const,
    nome: 'Fiorino',
    tag: 'Gasolina',
    texto: 'Mudanças pequenas, itens avulsos e entregas urbanas.',
    detalhe: 'Até 650 kg · cerca de 2,3 m³',
  },
  {
    tipo: 'kombi' as const,
    nome: 'Kombi',
    tag: 'Gasolina',
    texto: 'Quitinete, caixas e volumes leves.',
    detalhe: 'Até 1.000 kg · cerca de 3 m³',
  },
  {
    tipo: 'van' as const,
    nome: 'Van',
    tag: 'Diesel ou gasolina',
    texto: 'Apartamento pequeno, com móveis desmontados.',
    detalhe: 'Até 1.500 kg · cerca de 6,2 m³',
  },
  {
    tipo: 'tres_quartos' as const,
    nome: 'Caminhão 3/4',
    tag: 'Diesel',
    texto: 'Um a dois quartos, com sofá e eletrodomésticos.',
    detalhe: 'Até 3.000 kg · cerca de 12 m³',
  },
  {
    tipo: 'bau' as const,
    nome: 'Caminhão baú',
    tag: 'Diesel',
    texto: 'Casa completa, escritório e cargas volumosas.',
    detalhe: 'Até 8.000 kg · cerca de 33 m³',
  },
  {
    tipo: 'carreta' as const,
    nome: 'Carreta',
    tag: 'Diesel',
    texto: 'Grandes volumes e mudanças comerciais.',
    detalhe: 'Até 25.000 kg · cerca de 90 m³',
  },
];

const FAQ = [
  {
    pergunta: 'Quanto custa usar o Quero Mudar Já?',
    resposta:
      'Publicar a mudança é grátis. Não cobramos taxa de intermediação: o valor do transporte é combinado direto entre você e o transportador.',
  },
  {
    pergunta: 'Preciso aceitar todas as propostas?',
    resposta:
      'Não. Você recebe as propostas, compara os valores e os detalhes com calma e escolhe a que fizer sentido. Não há obrigação de fechar.',
  },
  {
    pergunta: 'Como sei se a minha mudança cabe no veículo?',
    resposta:
      'Ao montar a lista, o site calcula peso e volume e mostra o veículo que acomoda os volumes. O transportador confere as medidas internas do veículo dele antes de enviar a proposta.',
  },
  {
    pergunta: 'Quem são os transportadores?',
    resposta:
      'São profissionais com fiorino, Kombi, van, caminhão 3/4, baú ou carreta. Cada um informa as medidas do veículo e a capacidade de peso no perfil.',
  },
  {
    pergunta: 'O que é o plano de carga?',
    resposta:
      'É um desenho que mostra como os volumes se distribuem no veículo, com altura usada, peso e a lista do que ficou fora. Serve para planejar antes de carregar e evita surpresa na hora da retirada.',
  },
  {
    pergunta: 'O pagamento é feito pelo site?',
    resposta:
      'Não. O site aproxima quem vai mudar de quem transporta. O pagamento e os detalhes da mudança são combinados direto entre você e o transportador.',
  },
];

export const HomePage: FC<HomeProps> = ({
  user,
  alertas,
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
  const destinoCliente = user ? '/novo-frete' : '/cadastro?papel=cliente';

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
          'Mudanças e fretes com lista de itens, plano de carga e propostas de transportadores. Combine direto, sem taxa de intermediação.',
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
      titulo="Mudanças organizadas, do primeiro item ao combinado"
      user={user}
      alertas={alertas}
      canonical={canonical}
      jsonLd={jsonLd}
      tour
      descricao="Publique sua mudança grátis, monte a lista de itens, veja como tudo cabe no veículo e receba propostas de transportadores. Combine direto, sem taxa de intermediação."
    >
      <section class="home-hero">
        <div class="home-hero__texto">
          <span class="eyebrow">
            <span class="eyebrow__dot" aria-hidden="true"></span>
            Mudanças sem complicação
          </span>
          <h1>
            Sua mudança <em>organizada</em>, do primeiro item ao combinado.
          </h1>
          <p class="home-hero__resumo">
            Monte a lista do que vai levar, veja como tudo cabe no veículo e receba propostas de
            transportadores. Você compara, escolhe e combina direto — sem taxa de intermediação.
          </p>

          <div class="home-hero__acoes">
            <a href={destinoCliente} class="btn btn-primario btn-grande">
              Publicar minha mudança
            </a>
            <a href="#como-funciona" class="btn btn-fantasma btn-grande">
              Como funciona
            </a>
          </div>

          <ul class="home-hero__garantias">
            <li><span aria-hidden="true">✓</span> Grátis para publicar</li>
            <li><span aria-hidden="true">✓</span> Sem taxa de intermediação</li>
            <li><span aria-hidden="true">✓</span> Você escolhe com calma</li>
          </ul>

          <div class="home-hero__mini">
            <span class="home-hero__mini-icone" aria-hidden="true">📦</span>
            <span>
              <strong>Mais de 60 itens</strong>
              <small>com peso e medidas para montar sua lista</small>
            </span>
          </div>
        </div>

        <div class="home-hero__arte">
          <CenaMudanca class="cena-mudanca cena-mudanca--hero" />
          <p class="home-hero__legenda">
            <span aria-hidden="true">✓</span> Peso, volume e plano de carga antes de fechar
          </p>
        </div>
      </section>

      <section class="secao home-como" id="como-funciona">
        <div class="secao__cabecalho secao__cabecalho--centro">
          <div>
            <span class="eyebrow">Como funciona</span>
            <h2>Três passos para tirar a mudança do papel.</h2>
            <p class="secao__descricao">
              Sem planilha, sem ligação para cinco lugares e sem adivinhar se o sofá cabe no
              caminhão.
            </p>
          </div>
        </div>

        <ol class="home-passos">
          {PASSOS.map((passo) => (
            <li class="home-passo">
              <span class="home-passo__numero" aria-hidden="true">{passo.numero}</span>
              <h3>{passo.titulo}</h3>
              <p>{passo.texto}</p>
            </li>
          ))}
        </ol>
      </section>

      <section class="secao home-papeis">
        <div class="secao__cabecalho">
          <div>
            <span class="eyebrow">Dois lados, um combinado direto</span>
            <h2>Clareza para quem muda e para quem transporta.</h2>
          </div>
        </div>

        <div class="home-papeis__grade">
          <article class="home-papel home-papel--cliente">
            <header>
              <IconeCliente class="home-papel__icone" />
              <div>
                <span class="home-papel__tag">Quem vai mudar</span>
                <h3>Você organiza e escolhe.</h3>
              </div>
            </header>
            <ul class="lista-check">
              <li>Lista com peso e medidas de cada item.</li>
              <li>Plano de carga antes de fechar a proposta.</li>
              <li>Conversa direta com o transportador escolhido.</li>
            </ul>
            <a href={destinoCliente} class="btn btn-primario">
              Publicar minha mudança
            </a>
          </article>

          <article class="home-papel home-papel--transportador">
            <header>
              <IconeTransportador class="home-papel__icone" />
              <div>
                <span class="home-papel__tag">Quem transporta</span>
                <h3>Você encontra cargas e envia sua proposta.</h3>
              </div>
            </header>
            <ul class="lista-check">
              <li>Mudanças abertas filtradas por rota.</li>
              <li>Conferência de encaixe com as medidas do seu veículo.</li>
              <li>Sem taxa por proposta enviada.</li>
            </ul>
            <a
              href={user?.papel === 'transportador' ? '/fretes' : '/cadastro?papel=transportador'}
              class="btn btn-secundario"
            >
              {user?.papel === 'transportador' ? 'Encontrar mudanças' : 'Quero transportar'}
            </a>
          </article>
        </div>
      </section>

      {categorias.length > 0 && (
        <section class="secao">
          <div class="secao__cabecalho">
            <div>
              <span class="eyebrow">Monte sua lista</span>
              <h2>O que você precisa levar?</h2>
              <p class="secao__descricao">
                Comece pela categoria e escolha item por item. Cada volume já entra na conta com
                peso e medidas.
              </p>
            </div>
            <a href="/fretes" class="link-seta">
              Ver mudanças abertas <span aria-hidden="true">→</span>
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
                Geladeira, cama, sofá, guarda-roupa, caixas... tudo com peso e medidas para o
                transportador saber exatamente o que vai levar.
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
            <span class="eyebrow">Veículo certo</span>
            <h2>Qual veículo combina com a sua mudança?</h2>
            <p class="secao__descricao">
              Do fiorino à carreta, cada veículo tem um espaço diferente. O plano de carga mostra
              o encaixe antes de você fechar.
            </p>
          </div>
        </div>

        <div class="veiculos-grade veiculos-grade--home">
          {VEICULOS.map((veiculo) => (
            <article class="veiculo-card">
              <VeiculoArte tipo={veiculo.tipo} class="veiculo-card__arte" />
              <div class="veiculo-card__conteudo">
                <span
                  class={`veiculo-card__tag ${
                    veiculo.tag === 'Gasolina'
                      ? 'veiculo-card__tag--gasolina'
                      : 'veiculo-card__tag--diesel'
                  }`}
                >
                  {veiculo.tag}
                </span>
                <h3>{veiculo.nome}</h3>
                <p>{veiculo.texto}</p>
                <small>{veiculo.detalhe}</small>
              </div>
            </article>
          ))}
        </div>
        <p class="auxiliar veiculos-nota">
          Medidas e capacidades são referências típicas. O transportador confirma o espaço interno
          do veículo dele antes de enviar a proposta.
        </p>
      </section>

      <section class="faixa-transportador" aria-label="Área do transportador">
        <div class="faixa-transportador__texto">
          <span class="eyebrow eyebrow--claro">Para transportadores</span>
          <h2>Tem veículo e quer mais mudanças?</h2>
          <p>
            Encontre cargas na sua rota, confira se tudo cabe no seu veículo e envie sua proposta.
            Cadastrar o veículo é grátis.
          </p>
        </div>
        <div class="faixa-transportador__acoes">
          <a
            href={user?.papel === 'transportador' ? '/fretes' : '/cadastro?papel=transportador'}
            class="btn btn-branco btn-grande"
          >
            {user?.papel === 'transportador' ? 'Ver mudanças abertas' : 'Quero transportar'}
          </a>
          {user?.papel === 'transportador' && (
            <a href="/caminhao" class="btn btn-contorno-claro btn-grande">
              Configurar meu veículo
            </a>
          )}
        </div>
      </section>

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
            <span class="eyebrow">Dúvidas frequentes</span>
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

      <section class="cta-banda cta-banda--cliente">
        <div class="cta-banda__conteudo">
          <span class="eyebrow eyebrow--claro">Sua próxima mudança</span>
          <h2>Vai mudar? Comece pela lista.</h2>
          <p>
            Publique o que vai levar, veja como tudo cabe no veículo e receba propostas de
            transportadores. Você escolhe com calma e combina direto.
          </p>
          <div class="cta-banda__acoes">
            <a href={destinoCliente} class="btn btn-branco btn-grande">
              Publicar minha mudança
            </a>
            <a href="/app" class="btn btn-contorno-claro btn-grande">
              Conhecer o app
            </a>
          </div>
        </div>
        <div class="cta-banda__arte cta-banda__arte--mascote" aria-hidden="true">
          <MascoteMudanca class="mascote mascote--cta" idPrefixo="cta-mascote" />
        </div>
      </section>
    </Layout>
  );
};
