// Montador de carga: catálogo de itens com peso/dimensões, cálculo de totais
// e conferência determinística de encaixe no caminhão do transportador.
//
// A conferência é conservadora de propósito: usa volume, peso e as medidas
// internas do veículo. Ela não substitui a conferência no local, mas evita
// lances em cargas que claramente não cabem.

export type CategoriaCarga = 'Móveis' | 'Eletrodomésticos' | 'Caixas e volumes' | 'Outros';

export interface ItemCatalogo {
  id: string;
  nome: string;
  categoria: CategoriaCarga;
  emoji: string;
  pesoKg: number;
  comprimentoCm: number;
  larguraCm: number;
  alturaCm: number;
  fragil: boolean;
  empilhavel: boolean;
}

export interface ItemCarga extends ItemCatalogo {
  quantidade: number;
}

export interface CargaResumo {
  itens: ItemCarga[];
  totalItens: number;
  pesoKg: number;
  volumeM3: number;
  alturaMaximaCm: number;
  comprimentoMaximoCm: number;
}

export interface MedidasVeiculo {
  comprimentoCm: number;
  larguraCm: number;
  alturaCm: number;
  capacidadeKg: number;
}

export interface VeiculoTipo extends MedidasVeiculo {
  id: string;
  nome: string;
  descricao: string;
  emoji: string;
}

export type StatusEncaixe = 'cabe' | 'limite' | 'nao_cabe';

export interface AnaliseEncaixe {
  status: StatusEncaixe;
  cabe: boolean;
  motivos: string[];
  avisos: string[];
  volumeVeiculoM3: number;
  ocupacaoVolume: number;
  ocupacaoPeso: number;
  folgaVolumeM3: number;
  folgaPesoKg: number;
  maiorItem: string | null;
}

export interface SugestaoVeiculo {
  veiculo: VeiculoTipo;
  analise: AnaliseEncaixe;
}

// Pesos e medidas são médias de mercado, em centímetros e quilogramas.
// Itens desmontáveis usam as medidas da maior peça embalada.
export const CATALOGO_CARGA: ItemCatalogo[] = [
  {
    id: 'geladeira_1p',
    nome: 'Geladeira 1 porta',
    categoria: 'Eletrodomésticos',
    emoji: '🧊',
    pesoKg: 70,
    comprimentoCm: 70,
    larguraCm: 70,
    alturaCm: 180,
    fragil: true,
    empilhavel: false,
  },
  {
    id: 'geladeira_2p',
    nome: 'Geladeira duplex',
    categoria: 'Eletrodomésticos',
    emoji: '🧊',
    pesoKg: 110,
    comprimentoCm: 80,
    larguraCm: 75,
    alturaCm: 190,
    fragil: true,
    empilhavel: false,
  },
  {
    id: 'fogao',
    nome: 'Fogão',
    categoria: 'Eletrodomésticos',
    emoji: '🔥',
    pesoKg: 45,
    comprimentoCm: 65,
    larguraCm: 65,
    alturaCm: 90,
    fragil: true,
    empilhavel: false,
  },
  {
    id: 'maquina_lavar',
    nome: 'Máquina de lavar',
    categoria: 'Eletrodomésticos',
    emoji: '🫧',
    pesoKg: 70,
    comprimentoCm: 65,
    larguraCm: 65,
    alturaCm: 100,
    fragil: true,
    empilhavel: false,
  },
  {
    id: 'microondas',
    nome: 'Micro-ondas',
    categoria: 'Eletrodomésticos',
    emoji: '📻',
    pesoKg: 15,
    comprimentoCm: 50,
    larguraCm: 40,
    alturaCm: 30,
    fragil: true,
    empilhavel: true,
  },
  {
    id: 'ar_condicionado',
    nome: 'Ar-condicionado split',
    categoria: 'Eletrodomésticos',
    emoji: '❄️',
    pesoKg: 30,
    comprimentoCm: 90,
    larguraCm: 30,
    alturaCm: 30,
    fragil: true,
    empilhavel: true,
  },
  {
    id: 'tv',
    nome: 'TV até 55"',
    categoria: 'Eletrodomésticos',
    emoji: '📺',
    pesoKg: 18,
    comprimentoCm: 125,
    larguraCm: 20,
    alturaCm: 80,
    fragil: true,
    empilhavel: false,
  },
  {
    id: 'sofa_2',
    nome: 'Sofá 2 lugares',
    categoria: 'Móveis',
    emoji: '🛋️',
    pesoKg: 70,
    comprimentoCm: 180,
    larguraCm: 90,
    alturaCm: 85,
    fragil: false,
    empilhavel: false,
  },
  {
    id: 'sofa_3',
    nome: 'Sofá 3 lugares',
    categoria: 'Móveis',
    emoji: '🛋️',
    pesoKg: 95,
    comprimentoCm: 220,
    larguraCm: 95,
    alturaCm: 90,
    fragil: false,
    empilhavel: false,
  },
  {
    id: 'cama_solteiro',
    nome: 'Cama solteiro desmontada',
    categoria: 'Móveis',
    emoji: '🛏️',
    pesoKg: 35,
    comprimentoCm: 200,
    larguraCm: 100,
    alturaCm: 30,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'cama_casal',
    nome: 'Cama casal desmontada',
    categoria: 'Móveis',
    emoji: '🛏️',
    pesoKg: 55,
    comprimentoCm: 200,
    larguraCm: 160,
    alturaCm: 35,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'guarda_roupa',
    nome: 'Guarda-roupa desmontado',
    categoria: 'Móveis',
    emoji: '🚪',
    pesoKg: 80,
    comprimentoCm: 200,
    larguraCm: 60,
    alturaCm: 40,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'mesa_jantar',
    nome: 'Mesa de jantar',
    categoria: 'Móveis',
    emoji: '🍽️',
    pesoKg: 45,
    comprimentoCm: 160,
    larguraCm: 90,
    alturaCm: 75,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'cadeira',
    nome: 'Cadeira',
    categoria: 'Móveis',
    emoji: '🪑',
    pesoKg: 8,
    comprimentoCm: 45,
    larguraCm: 50,
    alturaCm: 90,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'mesa_escritorio',
    nome: 'Mesa de escritório',
    categoria: 'Móveis',
    emoji: '🖥️',
    pesoKg: 30,
    comprimentoCm: 120,
    larguraCm: 60,
    alturaCm: 75,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'colchao_casal',
    nome: 'Colchão casal',
    categoria: 'Móveis',
    emoji: '🛌',
    pesoKg: 30,
    comprimentoCm: 190,
    larguraCm: 140,
    alturaCm: 30,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'colchao_solteiro',
    nome: 'Colchão solteiro',
    categoria: 'Móveis',
    emoji: '🛌',
    pesoKg: 20,
    comprimentoCm: 190,
    larguraCm: 90,
    alturaCm: 30,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'armario',
    nome: 'Armário 2 portas',
    categoria: 'Móveis',
    emoji: '🗄️',
    pesoKg: 60,
    comprimentoCm: 100,
    larguraCm: 50,
    alturaCm: 180,
    fragil: false,
    empilhavel: false,
  },
  {
    id: 'caixa_pequena',
    nome: 'Caixa pequena',
    categoria: 'Caixas e volumes',
    emoji: '📦',
    pesoKg: 8,
    comprimentoCm: 40,
    larguraCm: 30,
    alturaCm: 30,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'caixa_media',
    nome: 'Caixa média',
    categoria: 'Caixas e volumes',
    emoji: '📦',
    pesoKg: 15,
    comprimentoCm: 50,
    larguraCm: 40,
    alturaCm: 40,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'caixa_grande',
    nome: 'Caixa grande',
    categoria: 'Caixas e volumes',
    emoji: '📦',
    pesoKg: 25,
    comprimentoCm: 60,
    larguraCm: 50,
    alturaCm: 50,
    fragil: false,
    empilhavel: true,
  },
  {
    id: 'bicicleta',
    nome: 'Bicicleta',
    categoria: 'Outros',
    emoji: '🚲',
    pesoKg: 15,
    comprimentoCm: 180,
    larguraCm: 30,
    alturaCm: 110,
    fragil: false,
    empilhavel: false,
  },
  {
    id: 'carrinho_bebe',
    nome: 'Carrinho de bebê',
    categoria: 'Outros',
    emoji: '👶',
    pesoKg: 12,
    comprimentoCm: 90,
    larguraCm: 60,
    alturaCm: 40,
    fragil: false,
    empilhavel: true,
  },
];

export const GRUPOS_CARGA: { nome: CategoriaCarga; itens: ItemCatalogo[] }[] = (
  ['Móveis', 'Eletrodomésticos', 'Caixas e volumes', 'Outros'] as CategoriaCarga[]
).map((nome) => ({
  nome,
  itens: CATALOGO_CARGA.filter((item) => item.categoria === nome),
}));

export const ITEM_POR_ID = new Map(CATALOGO_CARGA.map((item) => [item.id, item]));

// Ordem crescente de tamanho — usada para recomendar o menor veículo que cabe.
export const VEICULOS: VeiculoTipo[] = [
  {
    id: 'fiorino',
    nome: 'Fiorino',
    descricao: 'Entregas leves e mudanças pequenas',
    emoji: '🚐',
    comprimentoCm: 145,
    larguraCm: 130,
    alturaCm: 120,
    capacidadeKg: 650,
  },
  {
    id: 'van',
    nome: 'Van',
    descricao: 'Mudanças de apartamento pequeno',
    emoji: '🚚',
    comprimentoCm: 260,
    larguraCm: 160,
    alturaCm: 150,
    capacidadeKg: 1500,
  },
  {
    id: 'tres_quartos',
    nome: 'Caminhão 3/4',
    descricao: 'Mudanças de 1 a 2 quartos',
    emoji: '🚛',
    comprimentoCm: 350,
    larguraCm: 190,
    alturaCm: 180,
    capacidadeKg: 3000,
  },
  {
    id: 'bau',
    nome: 'Caminhão baú',
    descricao: 'Mudanças maiores e cargas volumosas',
    emoji: '🚛',
    comprimentoCm: 600,
    larguraCm: 230,
    alturaCm: 240,
    capacidadeKg: 8000,
  },
  {
    id: 'carreta',
    nome: 'Carreta',
    descricao: 'Grandes volumes e mudanças comerciais',
    emoji: '🚜',
    comprimentoCm: 1400,
    larguraCm: 240,
    alturaCm: 270,
    capacidadeKg: 25000,
  },
];

const VEICULO_POR_ID = new Map(VEICULOS.map((veiculo) => [veiculo.id, veiculo]));

export function rotuloTipoVeiculo(tipo: string): string {
  return VEICULO_POR_ID.get(tipo)?.nome ?? 'Outro';
}

export function veiculoPorTipo(tipo: string): VeiculoTipo | null {
  return VEICULO_POR_ID.get(tipo) ?? null;
}

export function volumeItemM3(item: Pick<ItemCatalogo, 'comprimentoCm' | 'larguraCm' | 'alturaCm'>): number {
  return (item.comprimentoCm * item.larguraCm * item.alturaCm) / 1_000_000;
}

export function volumeVeiculoM3(veiculo: MedidasVeiculo): number {
  return (veiculo.comprimentoCm * veiculo.larguraCm * veiculo.alturaCm) / 1_000_000;
}

export function dimensoesTexto(item: Pick<ItemCatalogo, 'comprimentoCm' | 'larguraCm' | 'alturaCm'>): string {
  return `${item.comprimentoCm} × ${item.larguraCm} × ${item.alturaCm} cm`;
}

function numeroPositivo(valor: unknown): number {
  const n = Number(valor);
  return Number.isFinite(n) && n > 0 ? n : 0;
}

/** Lê as quantidades `item_<id>` do formulário e devolve os itens do catálogo. */
export function parseItensForm(valores: Record<string, string>): ItemCarga[] {
  const itens: ItemCarga[] = [];
  let unidades = 0;
  const limiteUnidades = 200;

  for (const item of CATALOGO_CARGA) {
    if (unidades >= limiteUnidades) break;
    const bruto = valores[`item_${item.id}`];
    if (bruto == null || String(bruto).trim() === '') continue;

    const numero = Number(String(bruto).replace(',', '.'));
    if (!Number.isFinite(numero)) continue;
    let quantidade = Math.floor(numero);
    if (quantidade <= 0) continue;
    quantidade = Math.min(20, quantidade, limiteUnidades - unidades);
    if (quantidade <= 0) continue;

    unidades += quantidade;
    itens.push({ ...item, quantidade });
  }

  return itens;
}

/** Lê o JSON salvo no frete. Dados antigos ou corrompidos são ignorados. */
export function parseItensDb(json: string | null | undefined): ItemCarga[] {
  if (!json) return [];
  let dados: unknown;
  try {
    dados = JSON.parse(json);
  } catch {
    return [];
  }
  if (!Array.isArray(dados)) return [];

  const itens: ItemCarga[] = [];
  let unidades = 0;

  for (const bruto of dados) {
    if (!bruto || typeof bruto !== 'object') continue;
    const registro = bruto as Record<string, unknown>;
    const quantidade = Math.floor(Number(registro.quantidade));
    if (!Number.isFinite(quantidade) || quantidade <= 0) continue;

    const restante = 200 - unidades;
    if (restante <= 0) break;
    const qtd = Math.min(20, quantidade, restante);
    const pesoKg = numeroPositivo(registro.pesoKg);
    const comprimentoCm = numeroPositivo(registro.comprimentoCm);
    const larguraCm = numeroPositivo(registro.larguraCm);
    const alturaCm = numeroPositivo(registro.alturaCm);
    if (!pesoKg || !comprimentoCm || !larguraCm || !alturaCm) continue;

    const nome = String(registro.nome ?? '').trim().slice(0, 120);
    const id = String(registro.id ?? '').trim().slice(0, 60);
    if (!nome || !id) continue;

    unidades += qtd;
    itens.push({
      id,
      nome,
      categoria: (typeof registro.categoria === 'string' ? registro.categoria : 'Outros') as CategoriaCarga,
      emoji: typeof registro.emoji === 'string' ? registro.emoji.slice(0, 8) : '📦',
      pesoKg,
      comprimentoCm,
      larguraCm,
      alturaCm,
      fragil: Boolean(registro.fragil),
      empilhavel: registro.empilhavel !== false,
      quantidade: qtd,
    });
  }

  return itens;
}

export function serializarItens(itens: ItemCarga[]): string {
  return JSON.stringify(itens);
}

export function resumoCarga(itens: ItemCarga[]): CargaResumo {
  let totalItens = 0;
  let pesoKg = 0;
  let volumeM3 = 0;
  let alturaMaximaCm = 0;
  let comprimentoMaximoCm = 0;

  for (const item of itens) {
    const quantidade = Math.max(0, Number(item.quantidade) || 0);
    totalItens += quantidade;
    pesoKg += item.pesoKg * quantidade;
    volumeM3 += volumeItemM3(item) * quantidade;
    alturaMaximaCm = Math.max(alturaMaximaCm, item.alturaCm);
    comprimentoMaximoCm = Math.max(comprimentoMaximoCm, item.comprimentoCm, item.larguraCm);
  }

  return {
    itens,
    totalItens,
    pesoKg: Math.round(pesoKg * 100) / 100,
    volumeM3: Math.round(volumeM3 * 1000) / 1000,
    alturaMaximaCm,
    comprimentoMaximoCm,
  };
}

export function medidasDoCaminhao(caminhao: {
  comprimento_cm: number;
  largura_cm: number;
  altura_cm: number;
  capacidade_kg: number;
}): MedidasVeiculo {
  return {
    comprimentoCm: numeroPositivo(caminhao.comprimento_cm),
    larguraCm: numeroPositivo(caminhao.largura_cm),
    alturaCm: numeroPositivo(caminhao.altura_cm),
    capacidadeKg: numeroPositivo(caminhao.capacidade_kg),
  };
}

function percentual(valor: number): string {
  return `${Math.round(valor * 100)}%`;
}

export function analisarEncaixe(carga: CargaResumo, veiculo: MedidasVeiculo): AnaliseEncaixe {
  const volumeVeiculo = volumeVeiculoM3(veiculo);
  if (
    veiculo.comprimentoCm <= 0 ||
    veiculo.larguraCm <= 0 ||
    veiculo.alturaCm <= 0 ||
    veiculo.capacidadeKg <= 0
  ) {
    return {
      status: 'nao_cabe',
      cabe: false,
      motivos: ['Informe as medidas internas do caminhão para conferir o encaixe.'],
      avisos: [],
      volumeVeiculoM3: 0,
      ocupacaoVolume: 0,
      ocupacaoPeso: 0,
      folgaVolumeM3: 0,
      folgaPesoKg: 0,
      maiorItem: null,
    };
  }

  const ocupacaoVolume = carga.volumeM3 / volumeVeiculo;
  const ocupacaoPeso = carga.pesoKg / veiculo.capacidadeKg;
  const falhas: string[] = [];
  const avisos: string[] = [];
  let maiorItem: string | null = null;
  let maiorVolume = 0;

  for (const item of carga.itens) {
    const volumeUnitario = volumeItemM3(item);
    if (volumeUnitario * item.quantidade > maiorVolume) {
      maiorVolume = volumeUnitario * item.quantidade;
      maiorItem = `${item.nome} (${dimensoesTexto(item)})`;
    }

    if (item.alturaCm > veiculo.alturaCm) {
      falhas.push(
        `${item.nome} tem ${item.alturaCm} cm de altura e não cabe nos ${veiculo.alturaCm} cm de altura interna.`,
      );
      continue;
    }

    const cabeNaHorizontal =
      (item.comprimentoCm <= veiculo.comprimentoCm && item.larguraCm <= veiculo.larguraCm) ||
      (item.comprimentoCm <= veiculo.larguraCm && item.larguraCm <= veiculo.comprimentoCm);

    if (!cabeNaHorizontal) {
      falhas.push(
        `${item.nome} ocupa ${item.comprimentoCm} × ${item.larguraCm} cm e não cabe no piso de ${veiculo.comprimentoCm} × ${veiculo.larguraCm} cm.`,
      );
    }
  }

  if (ocupacaoVolume > 1) {
    falhas.push(
      `O volume da carga (${carga.volumeM3.toFixed(2).replace('.', ',')} m³) passa do espaço interno do veículo (${volumeVeiculo.toFixed(2).replace('.', ',')} m³).`,
    );
  }
  if (ocupacaoPeso > 1) {
    falhas.push(
      `O peso da carga (${Math.round(carga.pesoKg)} kg) passa da capacidade do veículo (${Math.round(veiculo.capacidadeKg)} kg).`,
    );
  }

  const status: StatusEncaixe = falhas.length > 0 ? 'nao_cabe' : ocupacaoVolume > 0.85 || ocupacaoPeso > 0.9 ? 'limite' : 'cabe';

  const motivos: string[] = [];
  if (status === 'nao_cabe') {
    motivos.push(...falhas);
  } else {
    motivos.push(
      `Peso de ${Math.round(carga.pesoKg)} kg (${percentual(ocupacaoPeso)} da capacidade de ${Math.round(veiculo.capacidadeKg)} kg).`,
    );
    motivos.push(
      `Volume de ${carga.volumeM3.toFixed(2).replace('.', ',')} m³ (${percentual(ocupacaoVolume)} do espaço interno).`,
    );
    motivos.push('Os itens cabem nas medidas internas do veículo.');
    if (status === 'limite') {
      motivos.push('A carga está no limite: organize com cuidado e confirme as medidas antes de fechar.');
    }
  }

  if (status !== 'nao_cabe' && (ocupacaoVolume > 0.7 || ocupacaoPeso > 0.75)) {
    avisos.push('Sobrou pouca folga. Distribua o peso no chão e sobre o eixo, deixando os itens leves por cima.');
  }
  if (ocupacaoPeso > 0.8) {
    avisos.push('Peso elevado para o veículo: confira a tara e a lotação máxima antes de viajar.');
  }
  if (carga.alturaMaximaCm > veiculo.alturaCm * 0.85) {
    avisos.push('Há item quase encostando no teto. Deixe espaço para amarração e para a suspensão trabalhar.');
  }
  if (carga.itens.some((item) => item.fragil)) {
    avisos.push('Há itens frágeis. Use mantas, cantoneiras e amarração sem apertar painéis e portas.');
  }
  if (carga.totalItens >= 30) {
    avisos.push('São muitos volumes. Separe por ambiente e numere as caixas para agilizar a descarga.');
  }

  return {
    status,
    cabe: status !== 'nao_cabe',
    motivos,
    avisos,
    volumeVeiculoM3: Math.round(volumeVeiculo * 100) / 100,
    ocupacaoVolume: Math.round(ocupacaoVolume * 1000) / 1000,
    ocupacaoPeso: Math.round(ocupacaoPeso * 1000) / 1000,
    folgaVolumeM3: Math.round((volumeVeiculo - carga.volumeM3) * 1000) / 1000,
    folgaPesoKg: Math.round(veiculo.capacidadeKg - carga.pesoKg),
    maiorItem,
  };
}

/** Menor veículo padrão que comporta a carga; se nenhum couber, null. */
export function sugerirVeiculo(carga: CargaResumo): SugestaoVeiculo | null {
  if (carga.totalItens === 0) return null;

  let noLimite: SugestaoVeiculo | null = null;
  for (const veiculo of VEICULOS) {
    const analise = analisarEncaixe(carga, veiculo);
    if (analise.status === 'cabe') return { veiculo, analise };
    if (analise.status === 'limite' && !noLimite) noLimite = { veiculo, analise };
  }
  return noLimite;
}
