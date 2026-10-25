// Geolocalização grátis: BrasilAPI (CEP → endereço + coordenadas) e haversine.
// Tudo server-side: a página nunca chama API externa (CSP connect-src 'self').

export interface ResultadoCep {
  cep: string;
  rua: string;
  bairro: string;
  cidade: string;
  uf: string;
  lat: number | null;
  lng: number | null;
}

// BrasilAPI v2 inclui coordenadas. Retorna null se inválido/não achou/fora do ar.
export async function consultarCep(cep: string): Promise<ResultadoCep | null> {
  const limpo = cep.replace(/\D/g, '');
  if (limpo.length !== 8) return null;
  try {
    const resp = await fetch(`https://brasilapi.com.br/api/cep/v2/${limpo}`, {
      headers: { 'User-Agent': 'frete-v2 (https://github.com/vitorsavicki/frete)' },
      signal: AbortSignal.timeout(5000),
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
      cep: limpo,
      rua: d.street ?? '',
      bairro: d.neighborhood ?? '',
      cidade: d.city,
      uf: d.state,
      lat: d.location?.coordinates?.latitude ?? null,
      lng: d.location?.coordinates?.longitude ?? null,
    };
  } catch {
    return null; // API fora do ar não pode derrubar o fluxo
  }
}

// Distância em linha reta (km) entre dois pontos — suficiente para o MVP.
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
    Math.sin(dLat / 2) ** 2 +
    Math.cos(lat1 * rad) * Math.cos(lat2 * rad) * Math.sin(dLng / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
