// Bindings do Worker (D1, assets, IA, e-mail e OAuth opcionais).
// Os opcionais degradam com elegância: sem chave, o recurso some da interface.

export interface AppBindings {
  DB: D1Database;
  ASSETS: Fetcher;
  AI?: Ai;
  RESEND_API_KEY?: string;
  EMAIL_FROM?: string;
  GOOGLE_CLIENT_ID?: string;
  GOOGLE_CLIENT_SECRET?: string;
}
