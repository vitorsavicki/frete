import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import {
  dinheiro,
  dataCurta,
  tempoRelativo,
  dataHora,
  peso,
  volume as formatarVolume,
} from '../lib/format';
import { dimensoesTexto, rotuloTipoVeiculo, volumeVeiculoM3 } from '../lib/carga';
import { rotuloSituacao } from '../lib/prazos';
import type { Alert, BidComNome, FreightResumo, Truck, User } from '../lib/db';

interface DashboardProps {
  user: User;
  alertas: Alert[];
  alertasNaoLidos: number;
  meusFretes: FreightResumo[];
  meusLances: (BidComNome & { frete_descricao: string; frete_status: string; menor_lance: number | null })[];
  caminhao: Truck | null;
}

function iniciais(user: User): string {
  const nome = (user.nome ?? '').trim();
  const sobrenome = (user.sobrenome ?? '').trim();
  return `${nome.charAt(0)}${sobrenome.charAt(0)}`.toUpperCase() || '👤';
}

function rotuloStatus(status: string): string {
  return rotuloSituacao(status);
}

export const DashboardPage: FC<DashboardProps> = ({
  user,
  alertas,
  alertasNaoLidos,
  meusFretes,
  meusLances,
  caminhao,
}) => {
  const clientes = user.papel === 'cliente';
  const fretesAbertos = meusFretes.filter((f) => f.situacao === 'aberto').length;
  const lancesRecebidos = meusFretes.reduce((total, f) => total + Number(f.total_lances ?? 0), 0);
  const lancesEmDisputa = meusLances.filter(
    (l) => l.frete_status === 'aberto' || l.frete_status === 'em_escolha',
  ).length;
  const lancesVencidos = meusLances.filter((l) => l.vencedor === 1).length;
  const valorVencido = meusLances
    .filter((l) => l.vencedor === 1)
    .reduce((total, l) => total + Number(l.valor), 0);

  return (
    <Layout
      titulo="Meu painel"
      user={user}
      alertas={alertasNaoLidos}
      descricao="Acompanhe suas mudanças, orçamentos e notificações no Quero Mudar Já."
    >
      <header class="painel-topo">
        <div class="painel-topo__perfil">
          {user.avatar_url ? (
            <img class="avatar avatar--grande" src={user.avatar_url} alt="" width="60" height="60" loading="lazy" />
          ) : (
            <span class="avatar avatar--grande" aria-hidden="true">
              {iniciais(user)}
            </span>
          )}
          <div>
            <span class="eyebrow">
              Painel do {clientes ? 'cliente' : 'transportador'}
            </span>
            <h1>Olá, {user.nome}!</h1>
            <p class="auxiliar">
              {clientes
                ? 'Acompanhe suas mudanças, os orçamentos recebidos e os avisos em um só lugar.'
                : 'Veja seus orçamentos, acompanhe as respostas e encontre novas mudanças.'}
            </p>
          </div>
        </div>
        <div class="painel-topo__acoes">
          {clientes ? (
            <a href="/novo-frete" class="btn btn-primario">
              + Publicar mudança
            </a>
          ) : (
            <a href="/fretes" class="btn btn-primario">
              Encontrar mudanças <span aria-hidden="true">→</span>
            </a>
          )}
        </div>
      </header>

      <section class="painel-stats" aria-label="Resumo da conta">
        {clientes ? (
          <>
            <div class="painel-stat">
              <strong>{fretesAbertos}</strong>
              <span>mudanças abertas</span>
            </div>
            <div class="painel-stat">
              <strong>{meusFretes.length}</strong>
              <span>mudanças publicadas</span>
            </div>
            <div class="painel-stat">
              <strong>{lancesRecebidos}</strong>
              <span>orçamentos recebidos</span>
            </div>
            <div class="painel-stat painel-stat--destaque">
              <strong>{alertasNaoLidos}</strong>
              <span>avisos não lidos</span>
            </div>
          </>
        ) : (
          <>
            <div class="painel-stat">
              <strong>{meusLances.length}</strong>
              <span>orçamentos enviados</span>
            </div>
            <div class="painel-stat">
              <strong>{lancesEmDisputa}</strong>
              <span>aguardando resposta</span>
            </div>
            <div class="painel-stat">
              <strong>{lancesVencidos}</strong>
              <span>orçamentos aceitos</span>
            </div>
            <div class="painel-stat painel-stat--destaque">
              <strong>{dinheiro(valorVencido)}</strong>
              <span>em mudanças fechadas</span>
            </div>
          </>
        )}
      </section>

      <div class="painel-grade">
        <div class="painel-coluna">
          {clientes ? (
            <section class="cartao">
              <div class="cartao__cabecalho">
                <div>
                  <h2>Minhas mudanças</h2>
                  <p class="auxiliar">{meusFretes.length === 0 ? 'Publique a primeira mudança para começar.' : 'Acompanhe os orçamentos e escolha o melhor.'}</p>
                </div>
                <a href="/novo-frete" class="btn btn-fantasma btn-pequeno">
                  Publicar
                </a>
              </div>

              {meusFretes.length === 0 ? (
                <div class="estado-vazio estado-vazio--compacto">
                  <span class="estado-vazio__emoji" aria-hidden="true">📦</span>
                  <h3>Nenhuma mudança publicada ainda</h3>
                  <p class="auxiliar">Descreva sua mudança e receba propostas de transportadores.</p>
                  <a href="/novo-frete" class="btn btn-primario btn-pequeno">
                    Publicar minha primeira mudança
                  </a>
                </div>
              ) : (
                <div class="lista-painel">
                  {meusFretes.map((f) => (
                    <a class="item-painel" href={`/fretes/${f.id}`}>
                      <div class="item-painel__topo">
                        <span class={`status status-${f.situacao}`}>{rotuloSituacao(f.situacao)}</span>
                        <span class="chip">{f.categoria_nome}</span>
                        <time>{tempoRelativo(f.criado_em)}</time>
                      </div>
                      <h3>{f.descricao}</h3>
                      <p class="item-painel__rota">
                        {f.origem_cidade}/{f.origem_uf}
                        <span aria-hidden="true"> → </span>
                        {f.destino_cidade}/{f.destino_uf}
                      </p>
                      <div class="item-painel__meta">
                        <span><span aria-hidden="true">📅</span> {dataCurta(f.data_retirada)}</span>
                        {f.situacao === 'aberto' && f.encerra_em && (
                          <span><span aria-hidden="true">⏳</span> Propostas até {dataHora(f.encerra_em)}</span>
                        )}
                        {f.situacao === 'em_escolha' && f.prazo_escolha_em && (
                          <span><span aria-hidden="true">🗳️</span> Escolha até {dataHora(f.prazo_escolha_em)}</span>
                        )}
                        <span><span aria-hidden="true">🤝</span> {f.total_lances} {f.total_lances === 1 ? 'orçamento' : 'orçamentos'}</span>
                        <span class="item-painel__preco">
                          menor orçamento:{' '}
                          <strong>{f.menor_lance != null ? dinheiro(f.menor_lance) : '—'}</strong>
                        </span>
                      </div>
                      <span class="item-painel__seta" aria-hidden="true">→</span>
                    </a>
                  ))}
                </div>
              )}
            </section>
          ) : (
            <section class="cartao">
              <div class="cartao__cabecalho">
                <div>
                  <h2>Meus orçamentos</h2>
                  <p class="auxiliar">
                    {meusLances.length === 0
                      ? 'Encontre uma mudança na sua rota e envie seu primeiro orçamento.'
                      : 'Acompanhe a posição de cada orçamento em tempo real.'}
                  </p>
                </div>
                <a href="/fretes" class="btn btn-fantasma btn-pequeno">
                  Ver mudanças
                </a>
              </div>

              {meusLances.length === 0 ? (
                <div class="estado-vazio estado-vazio--compacto">
                  <span class="estado-vazio__emoji" aria-hidden="true">🚛</span>
                  <h3>Nenhum orçamento enviado ainda</h3>
                  <p class="auxiliar">Veja as mudanças abertas e comece a enviar orçamentos.</p>
                  <a href="/fretes" class="btn btn-primario btn-pequeno">
                    Encontrar mudanças
                  </a>
                </div>
              ) : (
                <div class="lista-painel">
                  {meusLances.map((l) => (
                    <a class="item-painel" href={`/fretes/${l.freight_id}`}>
                      <div class="item-painel__topo">
                        {l.vencedor === 1 ? (
                          <span class="tag tag-ok">🏆 aceito</span>
                        ) : l.frete_status === 'aberto' ? (
                          l.menor_lance != null && l.valor <= l.menor_lance ? (
                            <span class="tag tag-ok">melhor valor</span>
                          ) : (
                            <span class="tag tag-alerta">superado</span>
                          )
                        ) : l.frete_status === 'em_escolha' ? (
                          <span class="tag tag-alerta">cliente escolhendo</span>
                        ) : (
                          <span class="tag">{rotuloSituacao(l.frete_status)}</span>
                        )}
                        <time>{tempoRelativo(l.criado_em)}</time>
                      </div>
                      <h3>{l.frete_descricao}</h3>
                      <div class="item-painel__meta item-painel__meta--valores">
                        <span>
                          <small>Seu orçamento</small>
                          <strong class="preco">{dinheiro(l.valor)}</strong>
                        </span>
                        <span>
                          <small>Menor orçamento</small>
                          <strong>{l.menor_lance != null ? dinheiro(l.menor_lance) : '—'}</strong>
                        </span>
                        <span>
                          <small>Situação da mudança</small>
                          <strong>{rotuloStatus(l.frete_status)}</strong>
                        </span>
                      </div>
                      <span class="item-painel__seta" aria-hidden="true">→</span>
                    </a>
                  ))}
                </div>
              )}
            </section>
          )}

          <section class="cartao cartao--dica">
            <span class="destaque__icone" aria-hidden="true">{clientes ? '💡' : '⛽'}</span>
            <h2>{clientes ? 'Dica para receber melhores orçamentos' : 'Dica para montar seu orçamento'}</h2>
            <p class="auxiliar">
              {clientes
                ? 'Informe o volume, os itens principais e se precisa de ajudante. Mudanças bem descritas recebem mais orçamentos.'
                : 'Use a calculadora de custo no detalhe da mudança para incluir combustível, pedágio, manutenção e margem antes de enviar o orçamento.'}
            </p>
            <a href={clientes ? '/novo-frete' : '/fretes'} class="btn btn-secundario btn-pequeno">
              {clientes ? 'Publicar mudança' : 'Ver mudanças abertas'}
            </a>
          </section>
        </div>

        <aside class="painel-lateral">
          {!clientes && (
            <section class={`cartao ${caminhao ? '' : 'cartao--dica'}`}>
              <div class="cartao__cabecalho">
                <div>
                  <h2>Meu veículo</h2>
                  <p class="auxiliar">
                    {caminhao ? rotuloTipoVeiculo(caminhao.tipo) : 'Ainda não cadastrado'}
                  </p>
                </div>
                <span class={`chip ${caminhao ? 'chip-verde' : ''}`}>
                  {caminhao ? 'Ativo' : 'Opcional'}
                </span>
              </div>

              {caminhao ? (
                <>
                  <div class="caminhao-resumo">
                    <span class="caminhao-resumo__emoji" aria-hidden="true">
                      🚛
                    </span>
                    <div>
                      <strong>{caminhao.nome}</strong>
                      <p>
                        {dimensoesTexto({
                          comprimentoCm: caminhao.comprimento_cm,
                          larguraCm: caminhao.largura_cm,
                          alturaCm: caminhao.altura_cm,
                        })}
                      </p>
                    </div>
                  </div>
                  <dl class="detalhes">
                    <div>
                      <dt>Volume interno</dt>
                      <dd>
                        {formatarVolume(
                          volumeVeiculoM3({
                            comprimentoCm: caminhao.comprimento_cm,
                            larguraCm: caminhao.largura_cm,
                            alturaCm: caminhao.altura_cm,
                            capacidadeKg: caminhao.capacidade_kg,
                          }),
                        )}
                      </dd>
                    </div>
                    <div>
                      <dt>Capacidade</dt>
                      <dd>{peso(caminhao.capacidade_kg)}</dd>
                    </div>
                    <div>
                      <dt>Margem desejada</dt>
                      <dd>{caminhao.margem_percentual}%</dd>
                    </div>
                  </dl>
                  <a href="/caminhao" class="btn btn-secundario btn-bloco btn-pequeno">
                    Editar veículo
                  </a>
                </>
              ) : (
                <>
                  <p class="auxiliar">
                    Cadastre as medidas internas e a capacidade para saber se a carga de cada
                    mudança cabe no seu veículo — e receba um orçamento sugerido com os seus custos.
                  </p>
                  <a href="/caminhao" class="btn btn-primario btn-bloco btn-pequeno">
                    Cadastrar meu veículo
                  </a>
                </>
              )}
            </section>
          )}

          <section class="cartao">
            <div class="cartao__cabecalho">
              <div>
                <h2>
                  Notificações {alertasNaoLidos > 0 && <span class="badge">{alertasNaoLidos}</span>}
                </h2>
                <p class="auxiliar">Avisos de orçamentos e mudanças.</p>
              </div>
              {alertasNaoLidos > 0 && (
                <form method="post" action="/alertas/ler">
                  <button type="submit" class="btn btn-fantasma btn-mini">
                    Marcar lidas
                  </button>
                </form>
              )}
            </div>

            {alertas.length === 0 ? (
              <p class="vazio">Nenhuma notificação por aqui.</p>
            ) : (
              <ul class="lista-alertas">
                {alertas.map((a) => (
                  <li class={a.lido === 0 ? 'nao-lido' : ''}>
                    <span class="lista-alertas__ponto" aria-hidden="true"></span>
                    <div>
                      {a.freight_id ? <a href={`/fretes/${a.freight_id}`}>{a.mensagem}</a> : <span>{a.mensagem}</span>}
                      <time>{dataHora(a.criado_em)}</time>
                    </div>
                  </li>
                ))}
              </ul>
            )}
          </section>
        </aside>
      </div>
    </Layout>
  );
};
