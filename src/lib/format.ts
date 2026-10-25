// Formatação pt-BR (datas e dinheiro) — roda no Workers (Intl disponível).

const fmtDinheiro = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

const fmtDataHora = new Intl.DateTimeFormat('pt-BR', {
  timeZone: 'America/Sao_Paulo',
  day: '2-digit',
  month: '2-digit',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit',
});

const fmtData = new Intl.DateTimeFormat('pt-BR', {
  timeZone: 'UTC',
  day: '2-digit',
  month: '2-digit',
  year: 'numeric',
});

const fmtPeso = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 });
const fmtVolume = new Intl.NumberFormat('pt-BR', {
  minimumFractionDigits: 1,
  maximumFractionDigits: 1,
});

export function peso(valor: number | null | undefined): string {
  if (valor == null || !Number.isFinite(valor)) return '—';
  return `${fmtPeso.format(valor)} kg`;
}

export function volume(valor: number | null | undefined): string {
  if (valor == null || !Number.isFinite(valor)) return '—';
  return `${fmtVolume.format(valor)} m³`;
}

export function dinheiro(valor: number | null | undefined): string {
  if (valor == null) return '—';
  return fmtDinheiro.format(valor);
}

// ISO 'YYYY-MM-DD' → 'DD/MM/YYYY' (sem conversão de fuso: data pura)
export function dataCurta(iso: string | null | undefined): string {
  if (!iso) return '—';
  const [ano, mes, dia] = iso.slice(0, 10).split('-');
  if (!ano || !mes || !dia) return iso;
  return `${dia}/${mes}/${ano}`;
}

// ISO datetime UTC → 'DD/MM/YYYY HH:MM' em Brasília
export function dataHora(iso: string | null | undefined): string {
  if (!iso) return '—';
  const d = new Date(iso);
  if (isNaN(d.getTime())) return iso;
  return fmtDataHora.format(d).replace(',', '');
}

// "há 3 dias" (como o app original: "3 dias atrás")
export function tempoRelativo(iso: string | null | undefined): string {
  if (!iso) return '—';
  const d = new Date(iso);
  if (isNaN(d.getTime())) return '—';
  const seg = Math.floor((Date.now() - d.getTime()) / 1000);
  if (seg < 60) return 'agora mesmo';
  const min = Math.floor(seg / 60);
  if (min < 60) return `há ${min} min`;
  const h = Math.floor(min / 60);
  if (h < 24) return `há ${h} h`;
  const dias = Math.floor(h / 24);
  if (dias === 1) return 'há 1 dia';
  if (dias < 30) return `há ${dias} dias`;
  return fmtData.format(d);
}

export function distancia(km: number | null | undefined): string {
  if (km == null) return '—';
  return `${km.toFixed(1).replace('.', ',')} km`;
}

/** Linha reta → estimativa rodoviária (fator 1,25), formatada. */
export function distanciaAproximada(kmLinhaReta: number | null | undefined): string {
  if (kmLinhaReta == null || !Number.isFinite(kmLinhaReta)) return '—';
  const rodovia = Math.round(kmLinhaReta * 1.25 * 10) / 10;
  return `~${rodovia.toFixed(1).replace('.', ',')} km`;
}
