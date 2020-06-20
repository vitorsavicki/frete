<?php
$v_params = $this->getParams();
$o_boleto = $v_params['o_boleto'];
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa				  |
// | 														              |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto HSBC: Bruno Leonardo M. F. Gon�alves          |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = $o_boleto->getDiasPrazoBol();
$taxa_boleto = $o_boleto->getTaxaBol();
$data_venc = date('d/m/Y', strtotime($o_boleto->getDataVencBol())); // Prazo de X dias OU informe data: "13/04/2006"; 
$valor_cobrado = $o_boleto->getValorBol(); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["numero_documento"] = $o_boleto->getNumeroBol();	// N�mero do documento - REGRA: M�ximo de 13 digitos
$dadosboleto["data_vencimento"] = date('d/m/Y', strtotime($o_boleto->getDataVencBol())); // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date('d/m/Y', strtotime($o_boleto->getDataEmissaoBol())); // Data de emiss�o do Boleto
$dadosboleto["data_processamento"] = date('d/m/Y', strtotime($o_boleto->getDataInclusaoBol())); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $o_boleto->getNomeClienteBol();
$dadosboleto["endereco1"] = $o_boleto->getEndClienteBol();
$dadosboleto["endereco2"] = $o_boleto->getEnd2ClienteBol();

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = $o_boleto->getDemonstrativo1Bol();
$dadosboleto["demonstrativo2"] = $o_boleto->getDemonstrativo2Bol();
$dadosboleto["demonstrativo3"] = $o_boleto->getDemonstrativo3Bol();
$dadosboleto["instrucoes1"] = $o_boleto->getInstrucao1Bol();
$dadosboleto["instrucoes2"] = $o_boleto->getInstrucao2Bol();
$dadosboleto["instrucoes3"] = $o_boleto->getInstrucao3Bol();
$dadosboleto["instrucoes4"] = $o_boleto->getInstrucao4Bol();

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = $o_boleto->getQuantidadeBol();
$dadosboleto["valor_unitario"] = $o_boleto->getValorUnitBol();
$dadosboleto["aceite"] = $o_boleto->getAceiteBol();	
$dadosboleto["especie"] = $o_boleto->getEspecieBol();
$dadosboleto["especie_doc"] = $o_boleto->getEspecieBol();


// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //


// DADOS PERSONALIZADOS - HSBC
$dadosboleto["codigo_cedente"] = 35825329; //C�digo do Cedente (Somente 7 digitos) ag�ncia e conta
$dadosboleto["carteira"] = $o_boleto->getCarteiraBol();  // C�digo da Carteira

// SEUS DADOS
$dadosboleto["identificacao"] = $o_boleto->getIdentificacaoBol(); 
$dadosboleto["cpf_cnpj"] = $o_boleto->getCnpjCedenteBol(); 
$dadosboleto["endereco"] = $o_boleto->getEndCedenteBol(); 
$dadosboleto["cidade_uf"] = $o_boleto->getCidadeCedenteBol() . '/' . $o_boleto->getUfCedenteBol();
$dadosboleto["cedente"] = $o_boleto->getCedenteBol();

// N�O ALTERAR!
include("include/funcoes_hsbc.php"); 
include("include/layout_hsbc.php");
?>
