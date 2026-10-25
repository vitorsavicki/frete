# Plano de Refatoração e Melhoria - Frete v2

## Visão Geral
Projeto "Quero Mudar Já v2" - marketplace de fretes com leilão reverso, construído com Cloudflare Workers + Hono + D1. O objetivo é refatorar com melhores práticas de segurança, aproveitar features gratuitas do Cloudflare e melhorar a qualidade do código.

## Observações Atuais
- **Arquitetura**: Hono framework, D1 (SQLite), TypeScript, Zod validation
- **Segurança existente**: Headers de segurança básicos, PBKDF2 password hashing, sessões opacas de 256 bits, expiração 30 dias
- **Features Cloudflare usadas**: D1 database, Workers, Hono, Vitest
- **Features Cloudflare gratuitas disponíveis**: KV, R2, Queues, Durable Objects, Workers AI, Vectorize, Crypto Workers

## Áreas de Melhoria

### 1. Segurança (Security Hardening)
- [ ] Headers de segurança completos (HSTS, CSP, Referrer-Policy, X-Content-Type-Options, Frame-Options)
- [ ] Rate limiting por IP (usando KV ou Durable Object)
- [ ] Proteção CSRF para formulários
- [ ] Validação de entrada mais rigorosa
- [ ] Timing-safe comparation já implementada em crypto.ts ✓

### 2. Features Cloudflare Gratuitas
- [ ] **KV Store**: Cache de CEPs, dados frequentes, blacklist de IPs
- [ ] **R2 Storage**: Upload de imagens de fretes/transportadores
- [ ] **Queues**: Processamento assíncrono de alertas, emails
- [ ] **Durable Objects**: Estado consistente across requests, rate limiting distribuído
- [ ] **Workers AI**: Para processamento de linguagem natural ou classificação de categorias
- [ ] **Vectorize**: Busca semântica para descrições de frete

### 3. Arquitetura e Código
- [ ] Rotas centralizadas - currently multiple `app.route('/', ...)` which may conflict
- [ ] Middleware patterns mais consistentes
- [ ] Tipagem mais forte em rotas e parâmetros
- [ ] Extrair lógica de negócio de rotas para services/modules
- [ ] Melhor cobertura de testes

### 4. Observability
- [ ] Métricas de performance
- [ ] Logging estruturado
- [ ] Rastreamento de erros

### 5. Testing
- [ ] Testes unitários para regras de negócio (já têm em rules.ts ✓)
- [ ] Testes de integração para rotas críticas
- [ ] Testes de segurança (XSS, injection attempts)

## Prioridade Inicial
1. Headers de segurança completos
2. Rate limiting básico
3. KV store para cache de CEPs
4. Refatorar rotas para evitar conflitos
5. Melhorar types e typesafety

## Próximos Passos
- Implementar headers de segurança avançados
- Adicionir rate limiting usando KV ou Durable Object
- Migrar validação de CEP para cache KV
- Refatorar estrutura de rotas
- Adicionir testes de segurança básicos