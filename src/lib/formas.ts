// Forma desenhada de cada volume na vista 3D.
// O packer continua guardando uma caixa (é o espaço real ocupado); a forma é
// só um detalhe dentro dessa caixa, para o desenho lembrar o móvel de verdade
// sem prometer encaixe milimétrico.

export type FormaItem = 'sofa' | 'armario' | 'geladeira' | 'cama' | 'mesa' | 'caixa' | 'tela' | 'generico';

const FORMAS: Record<string, FormaItem> = {
  geladeira_1p: 'geladeira',
  geladeira_2p: 'geladeira',
  freezer: 'geladeira',
  fogao: 'geladeira',
  maquina_lavar: 'geladeira',
  lava_loucas: 'geladeira',
  secadora: 'geladeira',
  tanquinho: 'geladeira',
  forno_eletrico: 'geladeira',
  microondas: 'geladeira',
  coifa: 'geladeira',
  purificador: 'geladeira',
  ar_condicionado: 'geladeira',
  cooktop: 'geladeira',

  guarda_roupa: 'armario',
  guarda_roupa_3p: 'armario',
  armario: 'armario',
  comoda: 'armario',
  criado_mudo: 'armario',
  rack: 'armario',
  estante: 'armario',
  sapateira: 'armario',
  aparador: 'armario',
  escrivaninha: 'armario',

  sofa_1: 'sofa',
  sofa_2: 'sofa',
  sofa_3: 'sofa',
  sofa_retratil: 'sofa',
  sofa_cama: 'sofa',
  poltrona: 'sofa',
  banco_bar: 'sofa',

  cama_solteiro: 'cama',
  cama_casal: 'cama',
  cama_box_casal: 'cama',
  cama_box_solteiro: 'cama',
  beliche: 'cama',
  berco: 'cama',
  cama_infantil: 'cama',
  colchao_casal: 'cama',
  colchao_solteiro: 'cama',
  colchao_queen: 'cama',
  colchao_king: 'cama',

  mesa_jantar: 'mesa',
  mesa_escritorio: 'mesa',
  mesa_centro: 'mesa',
  mesa_redonda: 'mesa',
  cadeira: 'mesa',

  tv: 'tela',
  tv_65: 'tela',
  espelho_grande: 'tela',
  computador: 'tela',
  home_theater: 'tela',

  caixa_pequena: 'caixa',
  caixa_media: 'caixa',
  caixa_grande: 'caixa',
  caixas: 'caixa',
  caixa_livros: 'caixa',
  caixa_cozinha: 'caixa',
  caixa_arquivo: 'caixa',
  mala_grande: 'caixa',
  air_fryer: 'caixa',
  cafeteira: 'caixa',
  liquidificador: 'caixa',
  ventilador: 'caixa',
  impressora: 'caixa',
  instrumento_musical: 'caixa',
};

/**
 * A forma vem do cadastro do item. Item novo, sem forma cadastrada, continua
 * sendo desenhado como volume simples.
 */
export function formaDoItem(itemId: string): FormaItem {
  return FORMAS[itemId] ?? 'generico';
}
