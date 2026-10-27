// Rota rodoviária real com OSRM (serviço público de demonstração, sem chave)
// e queda graciosa para a estimativa por linha reta.
//
// Por que OSRM: a distância em linha reta engana em regiões com rios, serras
// ou desvios de rodovia. O OSRM devolve a distância, a duração e o traçado
// da rota, que usamos no mapa e no cálculo de custo do transportador.
//
// Limites do servidor público: uso moderado, sem SLA e sem volume alto. O
// resultado fica em cache por 24 h por par de coordenadas; em caso de erro,
// a rota aproximada é devolvida na hora e o OSRM é tentado de novo depois.
// Toda chamada acontece no Worker — o navegador nunca fala com o OSRM.

import { distanciaRodoviariaKm, haversineKm } from './geo';

export interface Coordenada {
  lat: number;
  lng: number;
}

export interface Rota {
  /** Distância rodoviária em km, com uma casa decimal. */
  distanciaKm: number;
  /** Duração estimada em minutos (null quando veio da estimativa por linha reta). */
  duracaoMin: number | null;
  /** Traçado da rota no formato [lat, lng]. */
  geometria: [number, number][];
  /** 'osrm' = rota real; 'aproximada' = linha reta × 1,25. */
  fonte: 'osrm' | 'aproximada';
}

interface CacheRota {
  rota: Rota;
  expiraEm: number;
}

const TTL_OSRM_MS = 24 * 60 * 60 * 1000;
const TTL_FALLBACK_MS = 10 * 60 * 1000;
const MAX_CACHE = 300;
const TIMEOUT_MS = 6000;
const UA = 'frete-v2/1.0 (https://github.com/vitorsavicki/frete)';

const cache = new Map<string, CacheRota>();

function coordenadaValida(lat: number, lng: number): boolean {
  return (
    Number.isFinite(lat) &&
    Number.isFinite(lng) &&
    Math.abs(lat) <= 90 &&
    Math.abs(lng) <= 180
  );
}

function chaveCache(origem: Coordenada, destino: Coordenada): string {
  // 4 casas ≈ 11 m: suficiente para reaproveitar rotas do mesmo trecho.
  return `${origem.lat.toFixed(4)},${origem.lng.toFixed(4)};${destino.lat.toFixed(4)},${destino.lng.toFixed(4)}`;
}

function faxinaCache(agora: number): void {
  for (const [chave, item] of cache) {
    if (item.expiraEm <= agora) cache.delete(chave);
  }
  if (cache.size > MAX_CACHE) {
    const excedente = cache.size - MAX_CACHE;
    let removidos = 0;
    for (const chave of cache.keys()) {
      cache.delete(chave);
      if (++removidos >= excedente) break;
    }
  }
}

function rotaAproximada(origem: Coordenada, destino: Coordenada): Rota {
  const linhaReta = haversineKm(origem.lat, origem.lng, destino.lat, destino.lng);
  const km = distanciaRodoviariaKm(linhaReta) ?? 0;
  return {
    distanciaKm: km,
    duracaoMin: null,
    geometria: [
      [origem.lat, origem.lng],
      [destino.lat, destino.lng],
    ],
    fonte: 'aproximada',
  };
}

interface OsrmResposta {
  code?: string;
  routes?: {
    distance?: number;
    duration?: number;
    geometry?: { coordinates?: [number, number][] };
  }[];
}

async function buscarOsrm(origem: Coordenada, destino: Coordenada): Promise<Rota | null> {
  const coords = `${origem.lng},${origem.lat};${destino.lng},${destino.lat}`;
  const qs = new URLSearchParams({
    overview: 'simplified',
    geometries: 'geojson',
    alternatives: 'false',
    steps: 'false',
  });
  try {
    const resp = await fetch(`https://router.project-osrm.org/route/v1/driving/${coords}?${qs}`, {
      headers: { 'User-Agent': UA, Accept: 'application/json' },
      signal: AbortSignal.timeout(TIMEOUT_MS),
    });
    if (!resp.ok) return null;
    const dados = (await resp.json()) as OsrmResposta;
    const rota = dados.routes?.[0];
    if (dados.code !== 'Ok' || !rota?.distance || !Number.isFinite(rota.distance)) return null;

    const distanciaKm = Math.round((rota.distance / 1000) * 10) / 10;
    if (!(distanciaKm > 0)) return null;

    const geometria = (rota.geometry?.coordinates ?? [])
      .filter((p): p is [number, number] => Array.isArray(p) && p.length >= 2)
      .map(([lng, lat]) => [lat, lng] as [number, number])
      .filter(([lat, lng]) => coordenadaValida(lat, lng));

    return {
      distanciaKm,
      duracaoMin:
        rota.duration && Number.isFinite(rota.duration) ? Math.round(rota.duration / 60) : null,
      // O traçado pode vir vazio em respostas simplificadas; nesse caso o mapa
      // liga origem e destino em linha reta.
      geometria:
        geometria.length >= 2
          ? geometria
          : [
              [origem.lat, origem.lng],
              [destino.lat, destino.lng],
            ],
      fonte: 'osrm',
    };
  } catch {
    return null;
  }
}

/**
 * Calcula a rota rodoviária entre dois pontos. Nunca lança: se o OSRM falhar,
 * devolve a estimativa linha reta × 1,25 com `fonte: 'aproximada'`.
 */
export async function calcularRota(
  origem: Coordenada | null | undefined,
  destino: Coordenada | null | undefined,
): Promise<Rota | null> {
  if (!origem || !destino) return null;
  if (!coordenadaValida(origem.lat, origem.lng) || !coordenadaValida(destino.lat, destino.lng)) {
    return null;
  }

  const agora = Date.now();
  const chave = chaveCache(origem, destino);
  const salvo = cache.get(chave);
  if (salvo && salvo.expiraEm > agora) return salvo.rota;

  faxinaCache(agora);

  const osrm = await buscarOsrm(origem, destino);
  const rota = osrm ?? rotaAproximada(origem, destino);
  cache.set(chave, {
    rota,
    expiraEm: agora + (osrm ? TTL_OSRM_MS : TTL_FALLBACK_MS),
  });
  return rota;
}

/** Limpa o cache em memória (usado em testes e em diagnósticos). */
export function limparCacheRota(): void {
  cache.clear();
}
