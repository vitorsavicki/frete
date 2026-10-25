// Previsão do tempo para o dia da retirada — Open-Meteo (grátis, sem chave).
// Só consulta quando a data está dentro da janela de previsão (16 dias).

export interface Previsao {
  data: string;
  minima: number;
  maxima: number;
  chuvaPercentual: number;
  codigo: number;
  descricao: string;
  emoji: string;
}

interface CacheEntrada {
  expiraEm: number;
  valor: Previsao | null;
}

const cache = new Map<string, CacheEntrada>();
const TTL_MS = 60 * 60 * 1000;

const DESCRICOES: Record<number, { texto: string; emoji: string }> = {
  0: { texto: 'céu limpo', emoji: '☀️' },
  1: { texto: 'quase limpo', emoji: '🌤️' },
  2: { texto: 'parcialmente nublado', emoji: '⛅' },
  3: { texto: 'nublado', emoji: '☁️' },
  45: { texto: 'nevoeiro', emoji: '🌫️' },
  48: { texto: 'nevoeiro com geada', emoji: '🌫️' },
  51: { texto: 'garoa leve', emoji: '🌦️' },
  53: { texto: 'garoa', emoji: '🌦️' },
  55: { texto: 'garoa forte', emoji: '🌧️' },
  61: { texto: 'chuva leve', emoji: '🌦️' },
  63: { texto: 'chuva', emoji: '🌧️' },
  65: { texto: 'chuva forte', emoji: '🌧️' },
  71: { texto: 'neve leve', emoji: '🌨️' },
  73: { texto: 'neve', emoji: '🌨️' },
  75: { texto: 'neve forte', emoji: '🌨️' },
  80: { texto: 'pancadas de chuva', emoji: '🌦️' },
  81: { texto: 'pancadas moderadas', emoji: '🌧️' },
  82: { texto: 'pancadas fortes', emoji: '⛈️' },
  95: { texto: 'trovoadas', emoji: '⛈️' },
  96: { texto: 'trovoadas com granizo', emoji: '⛈️' },
  99: { texto: 'trovoadas fortes com granizo', emoji: '⛈️' },
};

function descrever(codigo: number): { texto: string; emoji: string } {
  return DESCRICOES[codigo] ?? { texto: 'tempo variável', emoji: '🌡️' };
}

function chaveCache(lat: number, lng: number, data: string): string {
  return `${lat.toFixed(3)},${lng.toFixed(3)},${data}`;
}

export function dataDentroDaPrevisao(dataIso: string): boolean {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(dataIso)) return false;
  const hoje = new Date();
  const alvo = new Date(`${dataIso}T12:00:00Z`);
  if (Number.isNaN(alvo.getTime())) return false;
  const dias = Math.round((alvo.getTime() - hoje.getTime()) / (24 * 3600 * 1000));
  return dias >= 0 && dias <= 15;
}

export async function previsaoRetirada(
  lat: number | null | undefined,
  lng: number | null | undefined,
  dataIso: string,
): Promise<Previsao | null> {
  if (lat == null || lng == null || !dataDentroDaPrevisao(dataIso)) return null;

  const chave = chaveCache(lat, lng, dataIso);
  const agora = Date.now();
  const emCache = cache.get(chave);
  if (emCache && emCache.expiraEm > agora) return emCache.valor;

  try {
    const params = new URLSearchParams({
      latitude: String(lat),
      longitude: String(lng),
      daily: 'temperature_2m_max,temperature_2m_min,precipitation_probability_max,weather_code',
      timezone: 'America/Sao_Paulo',
      start_date: dataIso,
      end_date: dataIso,
    });
    const resp = await fetch(`https://api.open-meteo.com/v1/forecast?${params.toString()}`, {
      headers: { 'User-Agent': 'frete-v2/1.0 (https://github.com/vitorsavicki/frete)' },
      signal: AbortSignal.timeout(5000),
    });
    if (!resp.ok) {
      cache.set(chave, { expiraEm: agora + TTL_MS, valor: null });
      return null;
    }
    const d = (await resp.json()) as {
      daily?: {
        time?: string[];
        temperature_2m_max?: number[];
        temperature_2m_min?: number[];
        precipitation_probability_max?: number[];
        weather_code?: number[];
      };
    };
    const maxima = d.daily?.temperature_2m_max?.[0];
    const minima = d.daily?.temperature_2m_min?.[0];
    const chuva = d.daily?.precipitation_probability_max?.[0] ?? 0;
    const codigo = d.daily?.weather_code?.[0] ?? 0;
    if (typeof maxima !== 'number' || typeof minima !== 'number') return null;

    const descricao = descrever(codigo);
    const previsao: Previsao = {
      data: dataIso,
      minima: Math.round(minima),
      maxima: Math.round(maxima),
      chuvaPercentual: Math.round(chuva),
      codigo,
      descricao: descricao.texto,
      emoji: descricao.emoji,
    };
    cache.set(chave, { expiraEm: agora + TTL_MS, valor: previsao });
    return previsao;
  } catch {
    cache.set(chave, { expiraEm: agora + TTL_MS, valor: null });
    return null;
  }
}
