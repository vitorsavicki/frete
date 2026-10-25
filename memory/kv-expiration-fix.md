# Correção de Erro KV PUT - Expiração Absolute Timestamp

## Problema
O Cloudflare KV rejeitava a expiração com erro: `400 Invalid expiration of 86400. Please specify integer greater than the current number of seconds since the UNIX epoch.`

## Causa
O parâmetro `expiration` do KV precisa ser um **timestamp absoluto** (segundos desde UNIX epoch), não um **duração em segundos** (86400 = 24 horas). O KV calcula `now + expiration` para determinar quando o dado expira, então enviar apenas `86400` significava expirar imediatamente (já que 86400 segundos desde o epoch já passou há muito tempo).

## Solução Aplicada

### Arquivo: `src/lib/rate-limit.ts`

**Antes (incorreto):**
```typescript
expiration: 86400, // 1 dia em segundos - ERRO
```

**Depois (correto):**
```typescript
const expiryTimestamp = Math.floor(Date.now() / 1000) + 86400; // agora + 24h em segundos
expiration: expiryTimestamp, // timestamp absoluto - CORRETO
```

### Funções Corrigidas

1. **`checkRateLimit()`** (linha 53-59): Atualização do contador de rate limit
2. **`setCachedCep()`** (linha 100-103): Cache de consultas CEP

Ambas as funções agora calculam `Math.floor(Date.now() / 1000) + 86400` para obter o timestamp absoluto (agora + 24 horas) e passam esse valor para o parâmetro `expiration` do KV.

## Por que isso funcionou?

O Cloudflare KV armazena dados com uma chave de expiração baseada em timestamp. Quando você especifica `expiration: X`, o KV armazena o dado até que o tempo atual ultrapasse o valor de `X` (em segundos desde UNIX epoch). 

- `expiration: 86400` → dados expiram imediatamente (já que 86400 segundos = 1970-01-01 + 1day já passou)
- `expiration: Math.floor(Date.now()/1000) + 86400` → dados expiram daqui a 24 horas

## Status

✅ Ambas as funções no `src/lib/rate-limit.ts` têm a correção aplicada  
⏳ Será necessário fazer `npm run deploy` ou `npx wrangler dev` para verificar se o erro desaparece