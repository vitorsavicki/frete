import type { FC } from 'hono/jsx';
import { Layout } from './Layout';

interface AuthProps {
  erro?: string | null;
  valores?: Record<string, string>;
}

export const LoginPage: FC<AuthProps> = ({ erro }) => (
  <Layout titulo="Entrar" user={null} erro={erro}>
    <div class="cartao-form">
      <h1>Entrar</h1>
      <form method="post" action="/login">
        <label>
          E-mail
          <input type="email" name="email" required autocomplete="email" maxlength={255} />
        </label>
        <label>
          Senha
          <input type="password" name="senha" required autocomplete="current-password" maxlength={72} />
        </label>
        <button type="submit" class="botao">
          Entrar
        </button>
      </form>
      <p class="auxiliar">
        Ainda não tem conta? <a href="/cadastro">Cadastre-se grátis</a>
      </p>
    </div>
  </Layout>
);

export const CadastroPage: FC<AuthProps> = ({ erro, valores = {} }) => (
  <Layout titulo="Cadastre-se" user={null} erro={erro}>
    <div class="cartao-form">
      <h1>Cadastre-se</h1>
      <form method="post" action="/cadastro">
        <fieldset class="papel-selector">
          <legend>Eu quero...</legend>
          <label class={valores.papel !== 'transportador' ? 'opcao ativa' : 'opcao'}>
            <input type="radio" name="papel" value="cliente" checked={valores.papel !== 'transportador'} />
            <span aria-hidden="true">📦</span>
            Contratar fretes
          </label>
          <label class={valores.papel === 'transportador' ? 'opcao ativa' : 'opcao'}>
            <input
              type="radio"
              name="papel"
              value="transportador"
              checked={valores.papel === 'transportador'}
            />
            <span aria-hidden="true">🚛</span>
            Dar lances em fretes
          </label>
        </fieldset>

        <div class="dupla">
          <label>
            Nome
            <input name="nome" required minlength={2} maxlength={80} value={valores.nome ?? ''} />
          </label>
          <label>
            Sobrenome
            <input
              name="sobrenome"
              required
              minlength={2}
              maxlength={120}
              value={valores.sobrenome ?? ''}
            />
          </label>
        </div>

        <label>
          E-mail
          <input
            type="email"
            name="email"
            required
            autocomplete="email"
            maxlength={255}
            value={valores.email ?? ''}
          />
        </label>

        <label>
          Senha <small>(mínimo 8 caracteres)</small>
          <input
            type="password"
            name="senha"
            required
            minlength={8}
            maxlength={72}
            autocomplete="new-password"
          />
        </label>

        <div class="dupla">
          <label>
            Telefone <small>(opcional)</small>
            <input
              name="telefone"
              inputmode="tel"
              maxlength={20}
              placeholder="(41) 99999-0000"
              value={valores.telefone ?? ''}
            />
          </label>
          <label>
            CPF/CNPJ <small>(opcional)</small>
            <input name="cpf_cnpj" maxlength={20} value={valores.cpf_cnpj ?? ''} />
          </label>
        </div>

        <button type="submit" class="botao">
          Criar conta grátis
        </button>
      </form>
      <p class="auxiliar">
        Já tem conta? <a href="/login">Entrar</a>
      </p>
    </div>
  </Layout>
);
