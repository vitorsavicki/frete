// Plano de carregamento: organiza os itens dentro da carroceria para o
// transportador enxergar, antes de sair, como a mudança cabe no veículo.
//
// O desenho é um corte lateral esquemático. Ele não promete encaixe
// milimétrico: serve para mostrar ordem de carregamento, uso da altura e
// onde ficam os itens frágeis. A conferência final continua sendo no local.
//
// O piso é preenchido como um tabuleiro: cada volume ocupa uma área de
// comprimento × largura, e os menores aproveitam os espaços que sobram.
// Quando o piso acaba, os volumes empilháveis sobem para a base mais baixa
// que suporta o peso. Assim a carga ocupa a carroceria de forma parecida
// com o carregamento real, em vez de ficar toda encostada em uma lateral.

import {
  VEICULOS,
  analisarEncaixe,
  resumoCarga,
  resumoCategorias,
  volumeItemM3,
  type CargaResumo,
  type CategoriaCarga,
  type ItemCarga,
  type MedidasVeiculo,
  type ResumoCategoria,
  type SugestaoVeiculo,
} from './carga';

export interface BlocoMontagem {
  chave: string;
  itemId: string;
  nome: string;
  emoji: string;
  categoria: CategoriaCarga;
  fragil: boolean;
  /** Posição em cm a partir da parede da frente (cabine). */
  x: number;
  /** Posição em cm a partir da lateral esquerda da carroceria. */
  z: number;
  /** Altura da base do bloco em cm a partir do piso. */
  base: number;
  comprimento: number;
  altura: number;
  profundidade: number;
  /** Peso de uma unidade deste volume. */
  pesoKg: number;
  /** Volume de uma unidade deste volume, em m³. */
  volumeM3: number;
  quantidade: number;
  empilhado: boolean;
}

export interface ItemMontagem {
  chave: string;
  nome: string;
  emoji: string;
  categoria: CategoriaCarga;
  fragil: boolean;
  total: number;
  colocados: number;
  fora: number;
  /** Volume de todos os volumes deste tipo de item. */
  volumeM3: number;
  /** Peso de todos os volumes deste tipo de item. */
  pesoKg: number;
  volumeColocadoM3: number;
  pesoColocadoKg: number;
}

export interface MontagemCarga {
  veiculo: MedidasVeiculo;
  blocos: BlocoMontagem[];
  /** Um registro por tipo de item, para conferir todos os volumes. */
  itens: ItemMontagem[];
  totalUnidades: number;
  colocados: number;
  fora: number;
  /** Motivos da conferência: ajuda a orientar o cliente sem jargão. */
  foraPorMedida: number;
  foraPorPeso: number;
  foraPorGeometria: number;
  /** Volumes que realmente entraram no desenho. */
  pesoColocadoKg: number;
  volumeColocadoM3: number;
  /** Volume e peso por categoria, para mostrar o volume das caixas. */
  categorias: ResumoCategoria[];
  alturaUsadaCm: number;
  comprimentoUsadoCm: number;
  larguraUsadaCm: number;
  /** Espaço que sobra em cada direção, para explicar os vãos do desenho. */
  sobraAlturaCm: number;
  sobraComprimentoCm: number;
  sobraLarguraCm: number;
  ocupacaoVolume: number;
  ocupacaoPeso: number;
  temFragil: boolean;
  temCaixas: boolean;
  temGeladeira: boolean;
  temColchao: boolean;
  temTv: boolean;
}

interface Orientacao {
  comprimento: number;
  profundidade: number;
}

interface Unidade {
  item: ItemCarga;
  comprimento: number;
  profundidade: number;
  /** Orientações possíveis, da mais estável para a alternativa. */
  orientacoes: Orientacao[];
  altura: number;
  empilhavel: boolean;
}

type ModoColocacao = 'piso' | 'empilhar';

const PASSO_CM = 5;

/**
 * Lista as orientações que cabem no veículo, da mais estável para a
 * alternativa. Evita atravessar a carroceria inteira com uma peça só quando é
 * possível girá-la; a segunda opção serve para aproveitar vãos estreitos.
 */
function orientar(
  item: ItemCarga,
  veiculo: MedidasVeiculo,
): Orientacao[] {
  const opcoes = [
    { comprimento: item.comprimentoCm, profundidade: item.larguraCm },
    { comprimento: item.larguraCm, profundidade: item.comprimentoCm },
  ].filter(
    (opcao) =>
      opcao.comprimento > 0 &&
      opcao.profundidade > 0 &&
      opcao.comprimento <= veiculo.comprimentoCm &&
      opcao.profundidade <= veiculo.larguraCm &&
      item.alturaCm <= veiculo.alturaCm,
  );

  if (opcoes.length === 0) return [];

  const confortavel = Math.max(0, veiculo.larguraCm * 0.7);
  opcoes.sort((a, b) => {
    const aCabe = a.profundidade <= confortavel ? 0 : 1;
    const bCabe = b.profundidade <= confortavel ? 0 : 1;
    if (aCabe !== bCabe) return aCabe - bCabe;
    if (a.profundidade !== b.profundidade) return a.profundidade - b.profundidade;
    return a.comprimento - b.comprimento;
  });
  return opcoes;
}

function expandirUnidades(carga: CargaResumo, veiculo: MedidasVeiculo): { unidades: Unidade[]; fora: number } {
  const unidades: Unidade[] = [];
  let fora = 0;

  for (const item of carga.itens) {
    const quantidade = Math.max(0, Math.floor(item.quantidade));
    const orientacoes = orientar(item, veiculo);
    if (orientacoes.length === 0) {
      fora += quantidade;
      continue;
    }

    for (let i = 0; i < quantidade; i++) {
      unidades.push({
        item,
        comprimento: orientacoes[0].comprimento,
        profundidade: orientacoes[0].profundidade,
        orientacoes,
        altura: item.alturaCm,
        empilhavel: item.empilhavel !== false && !item.fragil,
      });
    }
  }

  return { unidades, fora };
}

function ordenarUnidades(a: Unidade, b: Unidade): number {
  const aPiso = a.empilhavel ? 1 : 0;
  const bPiso = b.empilhavel ? 1 : 0;
  if (aPiso !== bPiso) return aPiso - bPiso;

  // Peças grandes e pesadas primeiro: elas ocupam a frente e recebem os
  // volumes menores nos espaços que sobram.
  if (aPiso === 0) {
    if (b.comprimento !== a.comprimento) return b.comprimento - a.comprimento;
    if (b.altura !== a.altura) return b.altura - a.altura;
    return b.item.pesoKg - a.item.pesoKg;
  }

  const volumeA = volumeItemM3(a.item);
  const volumeB = volumeItemM3(b.item);
  const areaA = a.comprimento * a.profundidade;
  const areaB = b.comprimento * b.profundidade;
  // Peças de base grande primeiro: elas formam a camada de apoio para as
  // caixas e colchões menores, sem deixar vãos no piso.
  if (areaB !== areaA) return areaB - areaA;
  if (volumeB !== volumeA) return volumeB - volumeA;
  return a.item.pesoKg - b.item.pesoKg;
}

export interface LimitesMontagem {
  /** Quando informado, o plano para de colocar volumes ao atingir o peso. */
  pesoMaximoKg?: number | null;
}

export function montarPlanoCarga(
  carga: CargaResumo,
  veiculo: MedidasVeiculo,
  limites: LimitesMontagem = {},
): MontagemCarga {
  const alturaVeiculo = Math.max(0, veiculo.alturaCm);
  const comprimentoVeiculo = Math.max(0, veiculo.comprimentoCm);
  const larguraVeiculo = Math.max(0, veiculo.larguraCm);
  const volumeVeiculo = (comprimentoVeiculo * larguraVeiculo * alturaVeiculo) / 1_000_000;

  const { unidades, fora: foraOrientacao } = expandirUnidades(carga, {
    comprimentoCm: comprimentoVeiculo,
    larguraCm: larguraVeiculo,
    alturaCm: alturaVeiculo,
    capacidadeKg: veiculo.capacidadeKg,
  });
  unidades.sort(ordenarUnidades);

  const celulasX = Math.max(1, Math.ceil(comprimentoVeiculo / PASSO_CM));
  const colunas = Math.max(1, Math.ceil(larguraVeiculo / PASSO_CM));
  const alturas = new Float64Array(celulasX * colunas);
  const suporta = new Uint8Array(celulasX * colunas);
  const blocos: BlocoMontagem[] = [];
  const pesoMaximo =
    limites.pesoMaximoKg != null && limites.pesoMaximoKg > 0
      ? limites.pesoMaximoKg
      : Number.POSITIVE_INFINITY;
  let pesoColocadoKg = 0;
  let foraPorMedida = foraOrientacao;
  let foraPorPeso = 0;
  let foraPorGeometria = 0;

  const indiceCelula = (x: number, z: number): number => x * colunas + z;

  interface PosicaoColocacao {
    x: number;
    z: number;
    base: number;
  }

  const buscarPosicao = (
    unidade: Unidade,
    orientacao: Orientacao,
    modo: ModoColocacao,
  ): PosicaoColocacao | null => {
    const comprimentoCelulas = Math.max(1, Math.ceil(orientacao.comprimento / PASSO_CM));
    const profundidadeCelulas = Math.max(1, Math.ceil(orientacao.profundidade / PASSO_CM));
    if (comprimentoCelulas > celulasX || profundidadeCelulas > colunas) return null;

    let melhor: PosicaoColocacao | null = null;

    // Varre a carroceria da frente para a porta e da esquerda para a
    // direita. No piso, a primeira posição livre é a escolhida. Ao
    // empilhar, procura a base mais baixa para não criar torres.
    for (let x = 0; x + comprimentoCelulas <= celulasX; x++) {
      for (let z = 0; z + profundidadeCelulas <= colunas; z++) {
        const base = modo === 'piso' ? 0 : (alturas[indiceCelula(x, z)] ?? 0);
        if (modo === 'empilhar') {
          if (base <= 0.5) continue;
          if (base + unidade.altura > alturaVeiculo + 0.5) continue;
        }

        let livre = true;
        for (let xi = x; xi < x + comprimentoCelulas && livre; xi++) {
          for (let zi = z; zi < z + profundidadeCelulas; zi++) {
            const indice = indiceCelula(xi, zi);
            const altura = alturas[indice] ?? 0;
            if (modo === 'piso') {
              if (altura > 0.5) {
                livre = false;
                break;
              }
            } else if (
              Math.abs(altura - base) > 0.5 ||
              (suporta[indice] ?? 0) !== 1
            ) {
              livre = false;
              break;
            }
          }
        }
        if (!livre) continue;

        if (modo === 'piso') return { x, z, base: 0 };
        if (!melhor || base < melhor.base - 0.01) melhor = { x, z, base };
      }
    }

    return melhor;
  };

  const registrarBloco = (
    unidade: Unidade,
    orientacao: Orientacao,
    posicao: PosicaoColocacao,
  ): void => {
    const comprimentoCelulas = Math.max(1, Math.ceil(orientacao.comprimento / PASSO_CM));
    const profundidadeCelulas = Math.max(1, Math.ceil(orientacao.profundidade / PASSO_CM));
    const topo = posicao.base + unidade.altura;
    // A tampa de um volume baixo e firme serve de apoio, mesmo que ele
    // próprio não possa ser levantado. É assim que colchões e caixas leves
    // vão por cima de camas e sofás.
    const apoia = !unidade.item.fragil && unidade.altura <= 120;
    for (let xi = posicao.x; xi < posicao.x + comprimentoCelulas; xi++) {
      for (let zi = posicao.z; zi < posicao.z + profundidadeCelulas; zi++) {
        const indice = indiceCelula(xi, zi);
        alturas[indice] = topo;
        suporta[indice] = apoia ? 1 : 0;
      }
    }

    pesoColocadoKg += unidade.item.pesoKg;
    blocos.push({
      chave: `${unidade.item.id}-${blocos.length}`,
      itemId: unidade.item.id,
      nome: unidade.item.nome,
      emoji: unidade.item.emoji,
      categoria: unidade.item.categoria,
      fragil: Boolean(unidade.item.fragil),
      x: posicao.x * PASSO_CM,
      z: posicao.z * PASSO_CM,
      base: posicao.base,
      comprimento: Math.min(
        orientacao.comprimento,
        comprimentoVeiculo - posicao.x * PASSO_CM,
      ),
      altura: unidade.altura,
      profundidade: Math.min(
        orientacao.profundidade,
        larguraVeiculo - posicao.z * PASSO_CM,
      ),
      pesoKg: unidade.item.pesoKg,
      volumeM3: volumeItemM3(unidade.item),
      quantidade: 1,
      empilhado: posicao.base > 0.5,
    });
  };

  for (const unidade of unidades) {
    // O peso é conferido antes de tentar o encaixe. Assim a mesma função
    // serve para o plano completo e para cada viagem de um caminhão menor.
    if (pesoColocadoKg + unidade.item.pesoKg > pesoMaximo + 0.001) {
      foraPorPeso += 1;
      continue;
    }

    let colocado = false;

    // 1. Piso: primeiro lugar livre, da frente para a porta. Tenta a
    // orientação preferida e, se ela não couber no vão, gira o volume.
    for (const orientacao of unidade.orientacoes) {
      const posicaoPiso = buscarPosicao(unidade, orientacao, 'piso');
      if (!posicaoPiso) continue;
      registrarBloco(unidade, orientacao, posicaoPiso);
      colocado = true;
      break;
    }

    // 2. Sem piso, empilha na base mais baixa que suporta o volume.
    if (!colocado && unidade.empilhavel) {
      let melhor: { orientacao: Orientacao; posicao: PosicaoColocacao } | null = null;
      for (const orientacao of unidade.orientacoes) {
        const posicaoEmpilhada = buscarPosicao(unidade, orientacao, 'empilhar');
        if (!posicaoEmpilhada) continue;
        if (!melhor || posicaoEmpilhada.base < melhor.posicao.base - 0.01) {
          melhor = { orientacao, posicao: posicaoEmpilhada };
        }
      }
      if (melhor) {
        registrarBloco(unidade, melhor.orientacao, melhor.posicao);
        colocado = true;
      }
    }

    if (!colocado) foraPorGeometria += 1;
  }

  let alturaUsadaCm = 0;
  let comprimentoUsadoCm = 0;
  let larguraUsadaCm = 0;
  for (const bloco of blocos) {
    alturaUsadaCm = Math.max(alturaUsadaCm, bloco.base + bloco.altura);
    comprimentoUsadoCm = Math.max(comprimentoUsadoCm, bloco.x + bloco.comprimento);
    larguraUsadaCm = Math.max(larguraUsadaCm, bloco.z + bloco.profundidade);
  }

  // Conferência item a item: cada unidade aparece como "colocada" ou "fora".
  // Isso evita a leitura de que o desenho simplesmente esqueceu volumes.
  const mapaItens = new Map<string, ItemMontagem>();
  for (const item of carga.itens) {
    const chave = `${item.id}|${item.nome}`;
    const registro = mapaItens.get(chave);
    if (registro) {
      registro.total += item.quantidade;
    } else {
      mapaItens.set(chave, {
        chave,
        nome: item.nome,
        emoji: item.emoji,
        categoria: item.categoria,
        fragil: Boolean(item.fragil),
        total: item.quantidade,
        colocados: 0,
        fora: 0,
        volumeM3: volumeItemM3(item) * item.quantidade,
        pesoKg: item.pesoKg * item.quantidade,
        volumeColocadoM3: 0,
        pesoColocadoKg: 0,
      });
    }
  }
  for (const bloco of blocos) {
    const registro = mapaItens.get(`${bloco.itemId}|${bloco.nome}`);
    if (registro) {
      registro.colocados += 1;
      registro.volumeColocadoM3 += bloco.volumeM3;
      registro.pesoColocadoKg += bloco.pesoKg;
    }
  }
  for (const registro of mapaItens.values()) {
    registro.fora = Math.max(0, registro.total - registro.colocados);
  }
  const itens = [...mapaItens.values()];
  const foraTotal = itens.reduce((total, item) => total + item.fora, 0);

  const nomes = carga.itens.map((item) => item.nome.toLowerCase()).join(' ');
  const ocupacaoPeso = veiculo.capacidadeKg > 0 ? carga.pesoKg / veiculo.capacidadeKg : 0;
  const volumeColocadoM3 = blocos.reduce((total, bloco) => total + bloco.volumeM3, 0);

  return {
    veiculo: {
      comprimentoCm: comprimentoVeiculo,
      larguraCm: larguraVeiculo,
      alturaCm: alturaVeiculo,
      capacidadeKg: veiculo.capacidadeKg,
    },
    blocos,
    itens,
    totalUnidades: carga.totalItens,
    colocados: blocos.length,
    fora: foraTotal,
    foraPorMedida,
    foraPorPeso,
    foraPorGeometria,
    pesoColocadoKg: Math.round(pesoColocadoKg * 100) / 100,
    volumeColocadoM3: Math.round(volumeColocadoM3 * 1000) / 1000,
    categorias: resumoCategorias(carga),
    alturaUsadaCm: Math.round(alturaUsadaCm),
    comprimentoUsadoCm: Math.round(comprimentoUsadoCm),
    larguraUsadaCm: Math.round(larguraUsadaCm),
    sobraAlturaCm: Math.max(0, Math.round(alturaVeiculo - alturaUsadaCm)),
    sobraComprimentoCm: Math.max(0, Math.round(comprimentoVeiculo - comprimentoUsadoCm)),
    sobraLarguraCm: Math.max(0, Math.round(larguraVeiculo - larguraUsadaCm)),
    ocupacaoVolume: volumeVeiculo > 0 ? carga.volumeM3 / volumeVeiculo : 0,
    ocupacaoPeso,
    temFragil: carga.itens.some((item) => item.fragil),
    temCaixas: carga.itens.some(
      (item) => item.categoria === 'Caixas e volumes' || /caixa/i.test(item.nome),
    ),
    temGeladeira: /geladeira|freezer/i.test(nomes),
    temColchao: /colch[aã]o/i.test(nomes),
    temTv: /\btv\b|televis|espelho|vidro/i.test(nomes),
  };
}

/** Recomendações de carregamento adaptadas ao que está na carga. */
export function dicasCarregamento(carga: CargaResumo, montagem: MontagemCarga): string[] {
  const dicas: string[] = [
    'Comece pelos itens pesados e grandes, rente à frente da carroceria e sempre no chão.',
  ];

  if (montagem.fora > 0) {
    dicas.push(
      `${montagem.fora} ${montagem.fora === 1 ? 'volume ficou' : 'volumes ficaram'} fora do desenho. Confirme as medidas ou veja a divisão em viagens no plano.`,
    );
  }
  if (montagem.temFragil) {
    dicas.push(
      'Frágeis por último e por cima, com manta, cantoneira e amarração sem apertar painéis ou portas.',
    );
  }
  if (montagem.temGeladeira) {
    dicas.push(
      'Geladeira em pé e amarrada, longe da porta. Depois da mudança, espere algumas horas antes de ligar.',
    );
  }
  if (montagem.temColchao) {
    dicas.push(
      'Colchão em pé ou apoiado em superfície plana. Nunca dobre nem apoie peso em cima.',
    );
  }
  if (montagem.temTv) {
    dicas.push(
      'TV, espelhos e vidros na vertical, embalados e presos para não torcerem durante a viagem.',
    );
  }
  if (montagem.temCaixas) {
    dicas.push(
      'Numere as caixas por ambiente e deixe as mais leves no topo. Caixas de livros vão no chão.',
    );
  }
  if (montagem.sobraLarguraCm > 20) {
    dicas.push(
      `A carga deixa ${montagem.sobraLarguraCm} cm livres na largura. Centralize e amarre bem para ela não deslizar na curva.`,
    );
  }
  if (montagem.alturaUsadaCm > montagem.veiculo.alturaCm * 0.85) {
    dicas.push('A carga chega perto do teto. Deixe folga para amarração e para a suspensão trabalhar.');
  }
  if (montagem.ocupacaoPeso > 0.8) {
    dicas.push('Peso elevado: distribua a carga sobre o eixo e confira a tara e a lotação antes de viajar.');
  }
  if (carga.itens.length > 0 && dicas.length === 1) {
    dicas.push('Passe uma cinta ou corda de ponta a ponta e confira a amarração antes de fechar a porta.');
  }

  return dicas.slice(0, 6);
}

/**
 * Menor veículo padrão em que o plano de carga realmente acomoda todos os
 * volumes. A conta de volume sozinha não basta: um 3/4 pode ter espaço de
 * sobra e, mesmo assim, deixar itens fora por causa do comprimento e da
 * largura de cada peça. Aqui a recomendação nasce do mesmo desenho que o
 * transportador vê.
 */
export function sugerirVeiculoParaPlano(carga: CargaResumo): SugestaoVeiculo | null {
  if (carga.totalItens === 0) return null;

  for (const veiculo of VEICULOS) {
    const analise = analisarEncaixe(carga, veiculo);
    if (analise.status === 'nao_cabe') continue;
    const plano = montarPlanoCarga(carga, veiculo, { pesoMaximoKg: veiculo.capacidadeKg });
    if (plano.fora === 0) return { veiculo, analise };
  }

  return null;
}

export interface ItemViagem {
  nome: string;
  emoji: string;
  quantidade: number;
}

export interface ViagemCarga {
  numero: number;
  montagem: MontagemCarga;
  itens: ItemViagem[];
  pesoKg: number;
  volumeM3: number;
}

export interface ItemImpossivel {
  nome: string;
  emoji: string;
  quantidade: number;
  motivo: string;
}

export interface PlanoViagens {
  veiculo: MedidasVeiculo;
  viagens: ViagemCarga[];
  totalViagens: number;
  /** A carga inteira cabe em uma única viagem. */
  cabeEmUma: boolean;
  /** Todas as unidades podem ser levadas, em uma ou mais viagens. */
  viavel: boolean;
  /** Unidades que não entram neste veículo de jeito nenhum. */
  impossiveis: ItemImpossivel[];
  /** Unidades que sobraram por causa do limite de viagens. */
  restantes: number;
  volumes: number;
  pesoKg: number;
  volumeM3: number;
  limiteViagens: number;
  motivo: string;
}

export const LIMITE_VIAGENS = 12;

function clonarItens(itens: ItemCarga[]): ItemCarga[] {
  return itens
    .map((item) => ({ ...item, quantidade: Math.max(0, Math.floor(item.quantidade)) }))
    .filter((item) => item.quantidade > 0);
}

/**
 * Distribui a carga em viagens quando ela não cabe de uma vez no veículo
 * escolhido. Cada viagem usa o mesmo packer do desenho: nada de estimar por
 * volume e prometer um encaixe que o plano não mostra.
 *
 * Itens maiores que a carroceria ou mais pesados que a lotação viram
 * "impossíveis" com o motivo escrito, em vez de sumirem da conta.
 */
export function planejarViagens(
  carga: CargaResumo,
  veiculo: MedidasVeiculo,
  limite = LIMITE_VIAGENS,
): PlanoViagens {
  const limiteViagens = Math.max(1, Math.floor(limite));
  const base: PlanoViagens = {
    veiculo,
    viagens: [],
    totalViagens: 0,
    cabeEmUma: carga.totalItens === 0,
    viavel: carga.totalItens === 0,
    impossiveis: [],
    restantes: 0,
    volumes: carga.totalItens,
    pesoKg: carga.pesoKg,
    volumeM3: carga.volumeM3,
    limiteViagens,
    motivo: carga.totalItens === 0 ? 'Sem volumes para planejar.' : '',
  };

  if (carga.totalItens === 0) return base;

  let restantesItens = clonarItens(carga.itens);
  const impossiveis: ItemImpossivel[] = [];

  // 1. O que nem passa pela porta: medida maior que o veículo.
  restantesItens = restantesItens.filter((item) => {
    if (orientar(item, veiculo).length > 0) return true;
    impossiveis.push({
      nome: item.nome,
      emoji: item.emoji,
      quantidade: item.quantidade,
      motivo: 'não passa nas medidas do veículo',
    });
    return false;
  });

  // 2. Cada viagem consome o que couber no desenho. Como o packer é
  //    determinístico, o que não entra numa carroceria vazia não entra em
  //    nenhuma outra.
  const viagens: ViagemCarga[] = [];
  while (restantesItens.length > 0 && viagens.length < limiteViagens) {
    const resumo = resumoCarga(restantesItens);
    const montagem = montarPlanoCarga(resumo, veiculo, {
      pesoMaximoKg: veiculo.capacidadeKg,
    });

    if (montagem.colocados === 0) break;

    const colocadosPorChave = new Map(
      montagem.itens.map((item) => [item.chave, item.colocados] as const),
    );
    const proximos: ItemCarga[] = [];
    const itensViagem: ItemViagem[] = [];

    for (const item of restantesItens) {
      const colocados = colocadosPorChave.get(`${item.id}|${item.nome}`) ?? 0;
      if (colocados > 0) {
        itensViagem.push({
          nome: item.nome,
          emoji: item.emoji,
          quantidade: colocados,
        });
      }
      const sobra = item.quantidade - colocados;
      if (sobra > 0) proximos.push({ ...item, quantidade: sobra });
    }

    if (itensViagem.length === 0) break;

    viagens.push({
      numero: viagens.length + 1,
      montagem,
      itens: itensViagem,
      pesoKg: Math.round(montagem.pesoColocadoKg * 100) / 100,
      volumeM3: Math.round(montagem.volumeColocadoM3 * 1000) / 1000,
    });
    restantesItens = proximos;
  }

  const restantes = restantesItens.reduce((total, item) => total + item.quantidade, 0);
  if (restantes > 0 && viagens.length >= limiteViagens) {
    const porItem = new Map<string, ItemImpossivel>();
    for (const item of restantesItens) {
      const chave = `${item.id}|${item.nome}`;
      const registro = porItem.get(chave);
      if (registro) registro.quantidade += item.quantidade;
      else {
        porItem.set(chave, {
          nome: item.nome,
          emoji: item.emoji,
          quantidade: item.quantidade,
          motivo: `passa do limite de ${limiteViagens} viagens`,
        });
      }
    }
    impossiveis.push(...porItem.values());
  } else if (restantes > 0) {
    const porItem = new Map<string, ItemImpossivel>();
    for (const item of restantesItens) {
      const chave = `${item.id}|${item.nome}`;
      const registro = porItem.get(chave);
      if (registro) registro.quantidade += item.quantidade;
      else {
        porItem.set(chave, {
          nome: item.nome,
          emoji: item.emoji,
          quantidade: item.quantidade,
          motivo: 'não coube no desenho do veículo',
        });
      }
    }
    impossiveis.push(...porItem.values());
  }

  const totalViagens = viagens.length;
  const cabeEmUma = totalViagens === 1 && restantes === 0 && impossiveis.length === 0;
  const viavel = restantes === 0 && impossiveis.length === 0;

  let motivo = '';
  if (cabeEmUma) {
    motivo = 'Tudo cabe em 1 viagem.';
  } else if (viavel) {
    motivo = `A carga não cabe de uma vez. São necessárias ${totalViagens} viagens com este veículo.`;
  } else if (restantes > 0 && totalViagens >= limiteViagens) {
    motivo = `Com ${totalViagens} viagens ainda sobram ${restantes} ${
      restantes === 1 ? 'volume' : 'volumes'
    }. Confirme o veículo ou divida a mudança.`;
  } else if (totalViagens === 0) {
    motivo = 'Nenhum volume deste veículo entra no desenho. Confira as medidas ou escolha um veículo maior.';
  } else {
    motivo = `São necessárias ${totalViagens} viagens, mas ${impossiveis.reduce(
      (total, item) => total + item.quantidade,
      0,
    )} ${
      impossiveis.reduce((total, item) => total + item.quantidade, 0) === 1
        ? 'volume não entra'
        : 'volumes não entram'
    } neste veículo.`;
  }

  return {
    ...base,
    viagens,
    totalViagens,
    cabeEmUma,
    viavel,
    impossiveis,
    restantes,
    motivo,
  };
}
