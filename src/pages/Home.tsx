import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { CenaMudanca, MascoteMudanca } from './Ilustracoes';
import type { User } from '../lib/db';

interface HomeProps {
  user: User | null;
  alertas: number;
  url?: string;
}

const PASSOS = [
  {
    numero: '1',
    titulo: 'Publique sua mudança',
    texto:
      'Diga o que precisa levar, de onde para onde e quando. Leva poucos minutos e é grátis.',
  },
  {
    numero: '2',
    titulo: 'Receba orçamentos',
    texto:
      'Transportadores que fazem a sua rota enviam o preço. Você acompanha tudo pelo site.',
  },
  {
    numero: '3',
    titulo: 'Escolha e combine',
    texto:
      'Compare com calma, escolha um transportador e acerte os detalhes direto com ele.',
  },
];

const FAQ = [
  {
    pergunta: 'Quanto custa usar o Quero Mudar Já?',
    resposta:
      'Publicar a mudança é grátis. Não cobramos taxa de intermediação: você paga só o valor combinado com o transportador.',
  },
  {
    pergunta: 'Como recebo os orçamentos?',
    resposta:
      'Depois de publicar, os transportadores interessados enviam o preço pela página da mudança. Você recebe um alerta e compara tudo no painel.',
  },
  {
    pergunta: 'Preciso aceitar o primeiro orçamento?',
    resposta:
      'Não. Você recebe, compara e escolhe com calma. Não há obrigação de fechar com ninguém.',
  },
  {
    pergunta: 'Quem são os transportadores?',
    resposta:
      'São profissionais cadastrados, com veículo próprio. Cada um informa o tipo e o espaço do veículo no perfil.',
  },
  {
    pergunta: 'Como combino os detalhes?',
    resposta:
      'Depois de escolher, você fala direto com o transportador para combinar data, horário e valor final. O site não processa pagamento.',
  },
];

export const HomePage: FC<HomeProps> = ({
  user,
  alertas,
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
          'Publique sua mudança e receba orçamentos de transportadores. Veja as mudanças abertas se você transporta. Combine direto, sem taxa de intermediação.',
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
      titulo="Publique sua mudança e receba orçamentos"
      user={user}
      alertas={alertas}
      canonical={canonical}
      jsonLd={jsonLd}
      descricao="Publique sua mudança e receba orçamentos de transportadores. Se você transporta, veja as mudanças abertas. Combine direto, sem taxa de intermediação."
    >
      <section class="home-hero">
        <div class="home-hero__texto">
          <span class="eyebrow">
            <span class="eyebrow__dot" aria-hidden="true"></span>
            Mudanças sem complicação
          </span>
          <h1>
            Publique sua mudança.
            <br />
            Receba <em>orçamentos</em>.
          </h1>
          <p class="home-hero__resumo">
            Conte o que precisa levar e para onde. Transportadores enviam o preço. Você compara,
            escolhe e combina direto com quem vai fazer a mudança.
          </p>

          <div class="home-hero__caminhos">
            <a href={destinoCliente} class="home-caminho home-caminho--cliente">
              <span class="home-caminho__tag">Vou me mudar</span>
              <strong>Publique o que precisa levar</strong>
              <span class="home-caminho__texto">Receba orçamentos e escolha com calma.</span>
              <span class="home-caminho__acao">Publicar minha mudança →</span>
            </a>
            <a href="/fretes" class="home-caminho home-caminho--transportador">
              <span class="home-caminho__tag">Faço mudanças</span>
              <strong>Veja as mudanças abertas</strong>
              <span class="home-caminho__texto">
                Encontre mudanças na sua rota e envie seu orçamento.
              </span>
              <span class="home-caminho__acao">Ver mudanças abertas →</span>
            </a>
          </div>

          <ul class="home-hero__garantias">
            <li><span aria-hidden="true">✓</span> Grátis para publicar</li>
            <li><span aria-hidden="true">✓</span> Sem taxa de intermediação</li>
            <li><span aria-hidden="true">✓</span> Você escolhe com calma</li>
          </ul>
        </div>

        <div class="home-hero__arte">
          <CenaMudanca class="cena-mudanca--hero" />
          <p class="home-hero__legenda">
            <span aria-hidden="true">✓</span> Você paga só o combinado, direto ao transportador
          </p>
        </div>
      </section>

      <section class="secao home-como" id="como-funciona">
        <div class="secao__cabecalho secao__cabecalho--centro">
          <div>
            <span class="eyebrow">Como funciona</span>
            <h2>Publique uma vez. Receba orçamentos. Escolha.</h2>
            <p class="secao__descricao">
              Sem ligar para cinco lugares e sem adivinhar quanto vai custar.
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

        <p class="home-como__acao">
          <a href={destinoCliente} class="btn btn-primario btn-grande">
            Publicar minha mudança
          </a>
        </p>
      </section>

      <section class="faixa-transportador" aria-label="Área do transportador">
        <div class="faixa-transportador__texto">
          <span class="eyebrow eyebrow--claro">Para quem transporta</span>
          <h2>Tem veículo? Veja as mudanças abertas.</h2>
          <p>
            Encontre mudanças na sua rota, confira se a carga cabe no seu veículo e envie seu
            orçamento. Cadastrar o veículo é grátis.
          </p>
        </div>
        <div class="faixa-transportador__acoes">
          <a href="/fretes" class="btn btn-branco btn-grande">
            Ver mudanças abertas
          </a>
          {user?.papel === 'transportador' ? (
            <a href="/caminhao" class="btn btn-contorno-claro btn-grande">
              Configurar meu veículo
            </a>
          ) : (
            !user && (
              <a href="/cadastro?papel=transportador" class="btn btn-contorno-claro btn-grande">
                Quero transportar
              </a>
            )
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
          <h2>Publique sua mudança agora.</h2>
          <p>
            Conte o que precisa levar e receba orçamentos de transportadores. Você escolhe e
            combina direto.
          </p>
          <div class="cta-banda__acoes">
            <a href={destinoCliente} class="btn btn-branco btn-grande">
              Publicar minha mudança
            </a>
            <a href="/fretes" class="btn btn-contorno-claro btn-grande">
              Ver mudanças abertas
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
