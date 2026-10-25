// Geolocalização grátis e sem chave:
// 1) BrasilAPI CEP v2 (endereço + coordenadas quando disponíveis)
// 2) ViaCEP como redundância (endereço, sem coordenadas)
// 3) Nominatim/OpenStreetMap como geocodificador de reserva
// Tudo server-side: a página nunca chama API externa (CSP connect-src 'self').

export interface ResultadoCep {
  cep: string;
  rua: string;
  bairro: string;
  cidade: string;
  uf: string;
  lat: number | null;
  lng: number | null;
  fonte: 'brasilapi' | 'viacep';
  /** true quando as coordenadas vieram do Nominatim (aproximadas). */
  geocodificado: boolean;
}

interface EnderecoParcial {
  cep: string;
  rua: string;
  bairro: string;
  cidade: string;
  uf: string;
  lat: number | null;
  lng: number | null;
  fonte: 'brasilapi' | 'viacep';
}

interface NominatimResultado {
  lat?: string;
  lon?: string;
}

const UA = 'frete-v2/1.0 (https://github.com/vitorsavicki/frete)';
const TIMEOUT_MS = 4500;
const PAUSA_NOMINATIM_MS = 1100; // política de uso: no máximo 1 req/s

let ultimaChamadaNominatim = 0;

function esperar(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function buscarBrasilApi(cep: string): Promise<EnderecoParcial | null> {
  try {
    const resp = await fetch(`https://brasilapi.com.br/api/cep/v2/${cep}`, {
      headers: { 'User-Agent': UA },
      signal: AbortSignal.timeout(TIMEOUT_MS),
    });
    if (!resp.ok) return null;
    const d = (await resp.json()) as {
      cep?: string;
      street?: string;
      neighborhood?: string;
      city?: string;
      state?: string;
      location?: { coordinates?: { latitude?: number; longitude?: number } };
    };
    if (!d.city || !d.state) return null;
    return {
      cep,
      rua: d.street ?? '',
      bairro: d.neighborhood ?? '',
      cidade: d.city,
      uf: d.state,
      lat: d.location?.coordinates?.latitude ?? null,
      lng: d.location?.coordinates?.longitude ?? null,
      fonte: 'brasilapi',
    };
  } catch {
    return null;
  }
}

async function buscarViaCep(cep: string): Promise<EnderecoParcial | null> {
  try {
    const resp = await fetch(`https://viacep.com.br/ws/${cep}/json/`, {
      headers: { 'User-Agent': UA, Accept: 'application/json' },
      signal: AbortSignal.timeout(TIMEOUT_MS),
    });
    if (!resp.ok) return null;
    const d = (await resp.json()) as {
      erro?: boolean | string;
      cep?: string;
      logradouro?: string;
      bairro?: string;
      localidade?: string;
      uf?: string;
    };
    if (d.erro || !d.localidade || !d.uf) return null;
    return {
      cep,
      rua: d.logradouro ?? '',
      bairro: d.bairro ?? '',
      cidade: d.localidade,
      uf: d.uf,
      lat: null,
      lng: null,
      fonte: 'viacep',
    };
  } catch {
    return null;
  }
}

interface GeocodificarParams {
  rua?: string;
  bairro?: string;
  cidade: string;
  uf: string;
}

/** Geocodifica um endereço no Nominatim (gratuito, sem chave). */
export async function geocodificarEndereco(
  endereco: GeocodificarParams,
): Promise<{ lat: number; lng: number } | null> {
  const partes = [endereco.rua, endereco.bairro, endereco.cidade, endereco.uf, 'Brasil'].filter(
    (p) => p && String(p).trim().length > 0,
  );
  if (!endereco.cidade || !endereco.uf) return null;

  // Respeita a política de 1 requisição por segundo do Nominatim.
  const desdeUltima = Date.now() - ultimaChamadaNominatim;
  if (desdeUltima < PAUSA_NOMINATIM_MS) await esperar(PAUSA_NOMINATIM_MS - desdeUltima);
  ultimaChamadaNominatim = Date.now();

  try {
    const qs = new URLSearchParams({
      format: 'jsonv2',
      limit: '1',
      countrycodes: 'br',
      q: partes.join(', '),
    });
    const resp = await fetch(`https://nominatim.openstreetmap.org/search?${qs.toString()}`, {
      headers: { 'User-Agent': UA, Accept: 'application/json' },
      signal: AbortSignal.timeout(TIMEOUT_MS),
    });
    if (!resp.ok) return null;
    const dados = (await resp.json()) as NominatimResultado[];
    const primeiro = dados[0];
    if (!primeiro?.lat || !primeiro?.lon) return null;
    const lat = Number(primeiro.lat);
    const lng = Number(primeiro.lon);
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
    return { lat, lng };
  } catch {
    return null;
  }
}

/**
 * Busca um CEP com redundância de provedores e geocodificação aproximada.
 * Nunca lança: retorna null se o CEP não existir ou todas as APIs falharem.
 */
export async function consultarCep(
  cep: string,
  opcoes: { geocodificar?: boolean } = {},
): Promise<ResultadoCep | null> {
  const limpo = (cep ?? '').replace(/\D/g, '');
  if (limpo.length !== 8) return null;

  let endereco = await buscarBrasilApi(limpo);
  if (!endereco) endereco = await buscarViaCep(limpo);
  if (!endereco) return null;

  let geocodificado = false;
  if (endereco.lat == null || endereco.lng == null) {
    if (opcoes.geocodificar !== false) {
      const coords = await geocodificarEndereco({
        rua: endereco.rua,
        bairro: endereco.bairro,
        cidade: endereco.cidade,
        uf: endereco.uf,
      });
      if (coords) {
        endereco = { ...endereco, lat: coords.lat, lng: coords.lng };
        geocodificado = true;
      }
    }
  }

  return { ...endereco, geocodificado };
}

/** Distância rodoviária aproximada a partir da linha reta (fator 1,25). */
export function distanciaRodoviariaKm(distanciaLinhaRetaKm: number | null | undefined): number | null {
  if (distanciaLinhaRetaKm == null || !Number.isFinite(distanciaLinhaRetaKm)) return null;
  return Math.round(distanciaLinhaRetaKm * 1.25 * 10) / 10;
}

// Distância em linha reta (km) entre dois pontos — base para a estimativa.
export function haversineKm(
  lat1: number | null,
  lng1: number | null,
  lat2: number | null,
  lng2: number | null,
): number | null {
  if (lat1 == null || lng1 == null || lat2 == null || lng2 == null) return null;
  const R = 6371;
  const rad = Math.PI / 180;
  const dLat = (lat2 - lat1) * rad;
  const dLng = (lng2 - lng1) * rad;
  const a =
    Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * rad) * Math.cos(lat2 * rad) * Math.sin(dLng / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
