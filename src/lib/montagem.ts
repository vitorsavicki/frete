// Plano de carregamento: organiza os itens dentro da carroceria para o
// transportador enxergar, antes de sair, como a mudança cabe no veículo.
//
// O desenho é um corte lateral esquemático. Ele não promete encaixe
// milimétrico: serve para mostrar ordem de carregamento, uso da altura e
// onde ficam os itens frágeis. A conferência final continua sendo no local.
//
// A carroceria é dividida em faixas de largura. O corte lateral mostra a
// faixa 1; as demais completam a largura e aparecem na vista de cima.

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
  /** Faixa de largura em que o volume foi acomodado (0 é a do corte lateral). */
  faixa: number;
  /** Posição em cm a partir da parede da frente (cabine). */
  x: number;
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

export interface FaixaMontagem {
  indice: number;
  profundidadeCm: number;
  volumes: number;
}

export interface MontagemCarga {
  veiculo: MedidasVeiculo;
  blocos: BlocoMontagem[];
  faixas: FaixaMontagem[];
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
  /** Volumes acomodados nas faixas que não aparecem no corte lateral. */
  volumesAtras: number;
  alturaUsadaCm: number;
  comprimentoUsadoCm: number;
  larguraUsadaCm: number;
  ocupacaoVolume: number;
  ocupacaoPeso: number;
  temFragil: boolean;
  temCaixas: boolean;
  temGeladeira: boolean;
  temColchao: boolean;
  temTv: boolean;
}

interface Unidade {
  item: ItemCarga;
  comprimento: number;
  profundidade: number;
  altura: number;
  empilhavel: boolean;
}

interface FaixaInterna {
  indice: number;
  profundidade: number;
  alturas: Float64Array;
  suporta: Uint8Array;
  volumes: number;
}

type ModoColocacao = 'piso' | 'empilhar';

const PASSO_CM = 5;

/** Escolhe a orientação que ocupa menos comprimento e ainda cabe na largura. */
function orientar(item: ItemCarga, veiculo: MedidasVeiculo): { comprimento: number; profundidade: number } | null {
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

  if (opcoes.length === 0) return null;
  opcoes.sort((a, b) => a.comprimento - b.comprimento);
  return opcoes[0] ?? null;
}

function expandirUnidades(carga: CargaResumo, veiculo: MedidasVeiculo): { unidades: Unidade[]; fora: number } {
  const unidades: Unidade[] = [];
  let fora = 0;

  for (const item of carga.itens) {
    const quantidade = Math.max(0, Math.floor(item.quantidade));
    const orientacao = orientar(item, veiculo);
    if (!orientacao) {
      fora += quantidade;
      continue;
    }

    for (let i = 0; i < quantidade; i++) {
      unidades.push({
        item,
        comprimento: orientacao.comprimento,
        profundidade: orientacao.profundidade,
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

  if (aPiso === 0) {
    if (b.altura !== a.altura) return b.altura - a.altura;
    return b.item.pesoKg - a.item.pesoKg;
  }

  const volumeA = volumeItemM3(a.item);
  const volumeB = volumeItemM3(b.item);
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

  const celulas = Math.max(1, Math.ceil(comprimentoVeiculo / PASSO_CM));
  const faixas: FaixaInterna[] = [];
  const blocos: BlocoMontagem[] = [];
  const pesoMaximo =
    limites.pesoMaximoKg != null && limites.pesoMaximoKg > 0
      ? limites.pesoMaximoKg
      : Number.POSITIVE_INFINITY;
  let pesoColocadoKg = 0;
  let foraPorMedida = foraOrientacao;
  let foraPorPeso = 0;
  let foraPorGeometria = 0;
  let larguraOcupada = 0;

  const criarFaixa = (profundidade: number): FaixaInterna => ({
    indice: faixas.length,
    profundidade,
    alturas: new Float64Array(celulas),
    suporta: new Uint8Array(celulas),
    volumes: 0,
  });

  const cabeProfundidade = (faixa: FaixaInterna, unidade: Unidade): boolean => {
    const novaLargura = Math.max(faixa.profundidade, unidade.profundidade);
    return larguraOcupada - faixa.profundidade + novaLargura <= larguraVeiculo + 0.5;
  };

  const confirmarProfundidade = (faixa: FaixaInterna, unidade: Unidade): void => {
    const novaLargura = Math.max(faixa.profundidade, unidade.profundidade);
    if (novaLargura <= faixa.profundidade) return;
    larguraOcupada += novaLargura - faixa.profundidade;
    faixa.profundidade = novaLargura;
  };

  interface PosicaoColocacao {
    inicio: number;
    base: number;
  }

  const buscarPosicao = (
    faixa: FaixaInterna,
    unidade: Unidade,
    modo: ModoColocacao,
  ): PosicaoColocacao | null => {
    const larguraCelulas = Math.max(1, Math.ceil(unidade.comprimento / PASSO_CM));
    let melhorInicio = -1;
    let melhorBase = Number.POSITIVE_INFINITY;

    for (let inicio = 0; inicio + larguraCelulas <= celulas; inicio++) {
      const base = faixa.alturas[inicio] ?? 0;
      if (modo === 'piso' ? base > 0.5 : base <= 0.5) continue;

      let plana = true;
      for (let i = inicio + 1; i < inicio + larguraCelulas; i++) {
        if (Math.abs((faixa.alturas[i] ?? 0) - base) > 0.5) {
          plana = false;
          break;
        }
      }
      if (!plana) continue;

      if (modo === 'empilhar') {
        let apoio = true;
        for (let i = inicio; i < inicio + larguraCelulas; i++) {
          if ((faixa.suporta[i] ?? 0) !== 1) {
            apoio = false;
            break;
          }
        }
        if (!apoio) continue;
      }

      if (base + unidade.altura > alturaVeiculo + 0.5) continue;

      // Empilha sempre na base mais baixa disponível; no piso, mantém a
      // ordem da frente para o fundo.
      if (base < melhorBase - 0.01) {
        melhorBase = base;
        melhorInicio = inicio;
      }
    }

    if (melhorInicio < 0) return null;
    return { inicio: melhorInicio, base: melhorBase };
  };

  const registrarBloco = (
    faixa: FaixaInterna,
    unidade: Unidade,
    posicao: PosicaoColocacao,
  ): void => {
    const larguraCelulas = Math.max(1, Math.ceil(unidade.comprimento / PASSO_CM));
    const topo = posicao.base + unidade.altura;
    for (let i = posicao.inicio; i < posicao.inicio + larguraCelulas; i++) {
      faixa.alturas[i] = topo;
      faixa.suporta[i] = unidade.empilhavel ? 1 : 0;
    }

    faixa.volumes += 1;
    pesoColocadoKg += unidade.item.pesoKg;
    blocos.push({
      chave: `${unidade.item.id}-${faixa.indice}-${blocos.length}`,
      itemId: unidade.item.id,
      nome: unidade.item.nome,
      emoji: unidade.item.emoji,
      categoria: unidade.item.categoria,
      fragil: Boolean(unidade.item.fragil),
      faixa: faixa.indice,
      x: posicao.inicio * PASSO_CM,
      base: posicao.base,
      comprimento: Math.min(
        unidade.comprimento,
        comprimentoVeiculo - posicao.inicio * PASSO_CM,
      ),
      altura: unidade.altura,
      profundidade: unidade.profundidade,
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

    // 1. Piso das faixas que já existem.
    for (const faixa of faixas) {
      if (!cabeProfundidade(faixa, unidade)) continue;
      const posicao = buscarPosicao(faixa, unidade, 'piso');
      if (!posicao) continue;
      registrarBloco(faixa, unidade, posicao);
      confirmarProfundidade(faixa, unidade);
      colocado = true;
      break;
    }

    // 2. Faixa nova no piso, usando a largura que ainda sobra.
    if (!colocado && larguraOcupada + unidade.profundidade <= larguraVeiculo + 0.5) {
      const faixa = criarFaixa(unidade.profundidade);
      const posicao = buscarPosicao(faixa, unidade, 'piso');
      if (posicao) {
        registrarBloco(faixa, unidade, posicao);
        faixas.push(faixa);
        larguraOcupada += faixa.profundidade;
        colocado = true;
      }
    }

    // 3. Só empilha quando o piso de todas as faixas já está ocupado.
    // Escolhe a faixa com a base mais baixa para distribuir a altura.
    if (!colocado && unidade.empilhavel) {
      let escolhida: { faixa: FaixaInterna; posicao: PosicaoColocacao } | null = null;
      for (const faixa of faixas) {
        if (!cabeProfundidade(faixa, unidade)) continue;
        const posicao = buscarPosicao(faixa, unidade, 'empilhar');
        if (!posicao) continue;
        if (!escolhida || posicao.base < escolhida.posicao.base - 0.01) {
          escolhida = { faixa, posicao };
        }
      }
      if (escolhida) {
        registrarBloco(escolhida.faixa, unidade, escolhida.posicao);
        confirmarProfundidade(escolhida.faixa, unidade);
        colocado = true;
      }
    }

    if (!colocado) foraPorGeometria += 1;
  }

  let alturaUsadaCm = 0;
  let comprimentoUsadoCm = 0;
  for (const faixa of faixas) {
    for (let i = 0; i < celulas; i++) {
      const altura = faixa.alturas[i] ?? 0;
      if (altura > alturaUsadaCm) alturaUsadaCm = altura;
      if (altura > 0) {
        comprimentoUsadoCm = Math.max(
          comprimentoUsadoCm,
          Math.min(comprimentoVeiculo, (i + 1) * PASSO_CM),
        );
      }
    }
  }

  const resumoFaixas: FaixaMontagem[] = faixas.map((faixa) => ({
    indice: faixa.indice,
    profundidadeCm: Math.round(faixa.profundidade),
    volumes: faixa.volumes,
  }));
  const volumesAtras = resumoFaixas.reduce(
    (total, faixa) => (faixa.indice > 0 ? total + faixa.volumes : total),
    0,
  );

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
    faixas: resumoFaixas,
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
    volumesAtras,
    alturaUsadaCm: Math.round(alturaUsadaCm),
    comprimentoUsadoCm: Math.round(comprimentoUsadoCm),
    larguraUsadaCm: Math.round(larguraOcupada),
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
  if (montagem.volumesAtras > 0) {
    dicas.push(
      'O corte mostra a faixa 1; as faixas de trás completam a largura. Distribua o peso entre os dois lados.',
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
    if (orientar(item, veiculo)) return true;
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
