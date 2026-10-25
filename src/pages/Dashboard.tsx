import type { FC } from 'hono/jsx';
import { Layout } from './Layout';
import { dinheiro, dataCurta, tempoRelativo, dataHora } from '../lib/format';
import type { Alert, BidComNome, FreightResumo, User } from '../lib/db';

interface DashboardProps {
  user: User;
  alertas: Alert[];
  alertasNaoLidos: number;
  meusFretes: FreightResumo[];
  meusLances: (BidComNome & { frete_descricao: string; frete_status: string; menor_lance: number | null })[];
}

export const DashboardPage: FC<DashboardProps> = ({
  user,
  alertas,
  alertasNaoLidos,
  meusFretes,
  meusLances,
}) => (
  <Layout titulo="Painel" user={user} alertas={alertasNaoLidos}>
    <h1>
      Olá, {user.nome}! <small class="categoria">({user.papel})</small>
    </h1>

    <section class="cartao-padrao">
      <div class="secao-cabecalho">
        <h2>
          Notificações {alertasNaoLidos > 0 && <span class="badge">{alertasNaoLidos}</span>}
        </h2>
        {alertasNaoLidos > 0 && (
          <form method="post" action="/alertas/ler">
            <button type="submit" class="botao botao-pequeno botao-secundario">
              Marcar todas como lidas
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
              {a.freight_id ? <a href={`/fretes/${a.freight_id}`}>{a.mensagem}</a> : a.mensagem}
              <time>{dataHora(a.criado_em)}</time>
            </li>
          ))}
        </ul>
      )}
    </section>

    {user.papel === 'cliente' && (
      <section class="cartao-padrao">
        <div class="secao-cabecalho">
          <h2>Meus fretes</h2>
          <a href="/novo-frete" class="botao botao-pequeno">
            Publicar frete
          </a>
        </div>
        {meusFretes.length === 0 ? (
          <p class="vazio">
            Você ainda não publicou fretes. <a href="/novo-frete">Publique o primeiro!</a>
          </p>
        ) : (
          <div class="tabela-wrapper">
            <table class="tabela">
              <thead>
                <tr>
                  <th>Status</th>
                  <th>Frete</th>
                  <th>Rota</th>
                  <th>Retirada</th>
                  <th>Lances</th>
                  <th>Menor lance</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {meusFretes.map((f) => (
                  <tr>
                    <td>
                      <span class={`status status-${f.status}`}>{f.status}</span>
                    </td>
                    <td>
                      <strong>{f.descricao}</strong>
                    </td>
                    <td>
                      {f.origem_cidade}/{f.origem_uf} → {f.destino_cidade}/{f.destino_uf}
                    </td>
                    <td>{dataCurta(f.data_retirada)}</td>
                    <td>{f.total_lances}</td>
                    <td>{f.menor_lance != null ? dinheiro(f.menor_lance) : '—'}</td>
                    <td>
                      <a class="botao botao-pequeno" href={`/fretes/${f.id}`}>
                        Ver
                      </a>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </section>
    )}

    {user.papel === 'transportador' && (
      <section class="cartao-padrao">
        <div class="secao-cabecalho">
          <h2>Meus lances</h2>
          <a href="/fretes" class="botao botao-pequeno">
            Ver fretes abertos
          </a>
        </div>
        {meusLances.length === 0 ? (
          <p class="vazio">
            Você ainda não deu lances. <a href="/fretes">Veja os fretes abertos!</a>
          </p>
        ) : (
          <div class="tabela-wrapper">
            <table class="tabela">
              <thead>
                <tr>
                  <th>Situação</th>
                  <th>Frete</th>
                  <th>Seu lance</th>
                  <th>Menor lance</th>
                  <th>Quando</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {meusLances.map((l) => (
                  <tr>
                    <td>
                      {l.frete_status !== 'aberto' ? (
                        l.vencedor === 1 ? (
                          <span class="tag tag-ok">🏆 venceu</span>
                        ) : (
                          <span class="tag">encerrado</span>
                        )
                      ) : l.menor_lance != null && l.valor <= l.menor_lance ? (
                        <span class="tag tag-ok">ganhando</span>
                      ) : (
                        <span class="tag tag-alerta">superado</span>
                      )}
                    </td>
                    <td>
                      <strong>{l.frete_descricao}</strong>
                    </td>
                    <td class="preco">{dinheiro(l.valor)}</td>
                    <td>{l.menor_lance != null ? dinheiro(l.menor_lance) : '—'}</td>
                    <td>
                      <time>{tempoRelativo(l.criado_em)}</time>
                    </td>
                    <td>
                      <a class="botao botao-pequeno" href={`/fretes/${l.freight_id}`}>
                        Ver
                      </a>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </section>
    )}
  </Layout>
);
