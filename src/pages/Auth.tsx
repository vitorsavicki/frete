import type { FC } from 'hono/jsx';
import { Layout } from './Layout';

interface AuthProps {
  erro?: string | null;
  valores?: Record<string, string>;
  googleAtivo?: boolean;
  papelInicial?: 'cliente' | 'transportador';
}

function GoogleIcon() {
  return (
    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
      <path
        fill="#4285F4"
        d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5a5.6 5.6 0 0 1-2.4 3.7v3h3.9c2.3-2.1 3.5-5.2 3.5-8.9Z"
      />
      <path
        fill="#34A853"
        d="M12 24c3.2 0 6-1.1 8-2.9l-3.9-3c-1.1.7-2.5 1.2-4.1 1.2-3.1 0-5.8-2.1-6.7-5H1.3v3.1A12 12 0 0 0 12 24Z"
      />
      <path
        fill="#FBBC05"
        d="M5.3 14.3a7.2 7.2 0 0 1 0-4.6V6.6H1.3a12 12 0 0 0 0 10.8l4-3.1Z"
      />
      <path
        fill="#EA4335"
        d="M12 4.8c1.8 0 3.3.6 4.6 1.8l3.4-3.4A12 12 0 0 0 1.3 6.6l4 3.1c.9-2.9 3.6-4.9 6.7-4.9Z"
      />
    </svg>
  );
}

const AuthAside: FC = () => (
  <aside class="auth__lado" aria-hidden="true">
    <img src="/img/casa.webp" alt="" width="900" height="600" />
    <div class="auth__lado-conteudo">
      <span class="eyebrow eyebrow--claro">Mudança sem estresse</span>
      <h2>De um sofá a uma casa inteira, com quem faz mudança de verdade.</h2>
      <ul class="auth__lista">
        <li><span>✓</span> Publique sua mudança em poucos minutos</li>
        <li><span>✓</span> Receba orçamentos de caminhoneiros</li>
        <li><span>✓</span> Pague só o valor combinado, sem taxa do site</li>
      </ul>
      <p class="auth__nota">
        Publicar é grátis e não cobramos intermediação. Você decide se aceita ou não cada
        orçamento.
      </p>
    </div>
  </aside>
);

export const LoginPage: FC<AuthProps> = ({ erro, googleAtivo }) => (
  <Layout titulo="Entrar" user={null} erro={erro} descricao="Entre na sua conta do Quero Mudar Já para acompanhar suas mudanças, orçamentos e avisos.">
    <div class="auth">
      <div class="auth__cartao">
        <div class="auth__cabecalho">
          <span class="eyebrow">Bem-vindo de volta</span>
          <h1>Entrar na sua conta</h1>
          <p class="auxiliar">Acompanhe suas mudanças e orçamentos em um só lugar.</p>
        </div>

        {googleAtivo && (
          <>
            <a class="btn btn-google btn-bloco" href="/auth/google?papel=cliente">
              <GoogleIcon />
              Continuar com Google
            </a>
            <div class="divisor"><span>ou entre com e-mail</span></div>
          </>
        )}

        <form method="post" action="/login" class="formulario">
          <label class="campo">
            <span class="campo__rotulo">E-mail</span>
            <input
              type="email"
              name="email"
              required
              autocomplete="email"
              maxlength={255}
              placeholder="voce@exemplo.com"
            />
          </label>

          <label class="campo">
            <span class="campo__rotulo">Senha</span>
            <span class="campo-senha">
              <input
                type="password"
                name="senha"
                required
                autocomplete="current-password"
                maxlength={72}
                placeholder="Sua senha"
                data-senha
              />
              <button type="button" class="ver-senha" data-ver-senha aria-label="Mostrar senha">
                👁️
              </button>
            </span>
          </label>

          <button type="submit" class="btn btn-primario btn-bloco btn-grande">
            Entrar
          </button>
        </form>

        <p class="auth__rodape">
          Ainda não tem conta? <a href="/cadastro">Criar conta grátis</a>
        </p>
      </div>

      <AuthAside />
    </div>
  </Layout>
);

export const CadastroPage: FC<AuthProps> = ({
  erro,
  valores = {},
  googleAtivo,
  papelInicial = 'cliente',
}) => {
  const papel =
    valores.papel === 'transportador' ||
    (valores.papel == null && papelInicial === 'transportador')
      ? 'transportador'
      : 'cliente';
  return (
    <Layout
      titulo="Criar conta grátis"
      user={null}
      erro={erro}
      descricao="Crie sua conta grátis no Quero Mudar Já. Publique sua mudança ou encontre mudanças para fazer, sem taxa de intermediação."
    >
      <div class="auth auth--cadastro">
        <div class="auth__cartao">
          <div class="auth__cabecalho">
            <span class="eyebrow">Grátis para publicar</span>
            <h1>Criar sua conta</h1>
            <p class="auxiliar">Leva menos de dois minutos. Sem cartão de crédito.</p>
          </div>

          <form method="post" action="/cadastro" class="formulario">
            <fieldset class="papel-selector" data-papel-selector>
              <legend class="campo__rotulo">Eu quero...</legend>
              <label class={papel === 'cliente' ? 'opcao ativa' : 'opcao'}>
                <input
                  type="radio"
                  name="papel"
                  value="cliente"
                  checked={papel === 'cliente'}
                  data-papel
                />
                <span class="opcao__icone" aria-hidden="true">📦</span>
                <span>
                  <strong>Mudar de casa</strong>
                  <small>Publicar e receber orçamentos</small>
                </span>
              </label>
              <label class={papel === 'transportador' ? 'opcao ativa' : 'opcao'}>
                <input
                  type="radio"
                  name="papel"
                  value="transportador"
                  checked={papel === 'transportador'}
                  data-papel
                />
                <span class="opcao__icone" aria-hidden="true">🚛</span>
                <span>
                  <strong>Fazer mudanças</strong>
                  <small>Ver cargas e enviar meus preços</small>
                </span>
              </label>
            </fieldset>

            {googleAtivo && (
              <>
                <a
                  class="btn btn-google btn-bloco"
                  href={`/auth/google?papel=${papel}`}
                  data-google
                >
                  <GoogleIcon />
                  Criar conta com Google
                </a>
                <p class="campo__dica campo__dica--centro" data-google-nota>
                  A conta será criada como {papel === 'transportador' ? 'transportador' : 'cliente'}.
                </p>
                <div class="divisor"><span>ou preencha seus dados</span></div>
              </>
            )}

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Nome</span>
                <input
                  name="nome"
                  required
                  minlength={2}
                  maxlength={80}
                  autocomplete="given-name"
                  placeholder="Ana"
                  value={valores.nome ?? ''}
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">Sobrenome</span>
                <input
                  name="sobrenome"
                  required
                  minlength={2}
                  maxlength={120}
                  autocomplete="family-name"
                  placeholder="Silva"
                  value={valores.sobrenome ?? ''}
                />
              </label>
            </div>

            <label class="campo">
              <span class="campo__rotulo">E-mail</span>
              <input
                type="email"
                name="email"
                required
                autocomplete="email"
                maxlength={255}
                placeholder="voce@exemplo.com"
                value={valores.email ?? ''}
              />
            </label>

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Senha</span>
                <span class="campo-senha">
                  <input
                    type="password"
                    name="senha"
                    required
                    minlength={8}
                    maxlength={72}
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    data-senha
                  />
                  <button type="button" class="ver-senha" data-ver-senha aria-label="Mostrar senha">
                    👁️
                  </button>
                </span>
                <span class="forca" data-forca aria-hidden="true">
                  <i></i>
                  <i></i>
                  <i></i>
                  <i></i>
                </span>
                <span class="campo__dica" data-forca-texto>
                  Use 8+ caracteres, com letras e números.
                </span>
              </label>

              <label class="campo">
                <span class="campo__rotulo">Confirmar senha</span>
                <span class="campo-senha">
                  <input
                    type="password"
                    name="senha2"
                    required
                    autocomplete="new-password"
                    placeholder="Repita a senha"
                    data-senha2
                  />
                  <button type="button" class="ver-senha" data-ver-senha aria-label="Mostrar senha">
                    👁️
                  </button>
                </span>
                <span class="campo__dica" data-senha-confere>
                  As duas senhas precisam ser iguais.
                </span>
              </label>
            </div>

            <div class="form-grade form-grade--2">
              <label class="campo">
                <span class="campo__rotulo">Telefone <small>(opcional)</small></span>
                <input
                  name="telefone"
                  inputmode="tel"
                  autocomplete="tel"
                  maxlength={20}
                  placeholder="(41) 99999-0000"
                  data-telefone
                  value={valores.telefone ?? ''}
                />
              </label>
              <label class="campo">
                <span class="campo__rotulo">
                  CPF ou CNPJ <small data-doc-dica>{papel === 'transportador' ? '(obrigatório)' : '(opcional)'}</small>
                </span>
                <input
                  name="cpf_cnpj"
                  inputmode="numeric"
                  maxlength={20}
                  placeholder="000.000.000-00"
                  data-documento
                  value={valores.cpf_cnpj ?? ''}
                />
              </label>
            </div>

            <p class="campo__dica">
              Ao criar a conta você concorda em receber avisos sobre suas mudanças e orçamentos por
              e-mail. Nada de spam.
            </p>

            <button type="submit" class="btn btn-primario btn-bloco btn-grande">
              Criar conta grátis
            </button>
          </form>

          <p class="auth__rodape">
            Já tem conta? <a href="/login">Entrar</a>
          </p>
        </div>

        <AuthAside />
      </div>
    </Layout>
  );
};
