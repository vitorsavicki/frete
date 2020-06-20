<?php
//incluindo classes da camada Model
require_once 'models/PessoaModel.php';
require_once 'models/EstadoModel.php';
require_once 'models/CidadeModel.php';
require_once 'models/EnderecoModel.php';
require_once 'models/AvaliacaoModel.php';
require_once 'models/PerguntaPesquisaModel.php';
require_once 'models/VoucherModel.php';
require_once 'models/PessoaVoucherModel.php';
require_once 'models/RespostaPesquisaModel.php';
require_once 'lib/View.php';
include './lib/function.php';

/**
 *
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 *
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - ClienteController.php

 *
 */
class PessoaController {
	/**
	 * Efetua a manipulação dos modelos necessários
	 * para a aprensentação da lista de pessoas
	 */
	public function listarPessoaAction() {
		$o_Pessoa = new PessoaModel();

		//Listando as pessoas  cadastrados
		$v_pessoas = $o_Pessoa -> _list();

		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de pessoas
		$o_view = new View('views/listarPessoa.phtml');

		//Passando os dados do contato para a View
		$o_view -> setParams(array('v_pessoas' => $v_pessoas));

		//Imprimindo código HTML
		$o_view -> showContents();
	}

	/**
	 * Efetua a manipulação dos modelos necessários
	 * para a aprensentação da lista de clientes
	 */
	public function listarClienteAction() {
		$o_Pessoa = new PessoaModel();

		//Listando os clientes cadastrados
		$v_pessoas = $o_Pessoa -> _list(null, 'U');

		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de clientes
		$o_view = new View('views/listarCliente.phtml');

		//Passando os dados do cliente para a View
		$o_view -> setParams(array('v_pessoas' => $v_pessoas));

		//Imprimindo código HTML
		$o_view -> showContents();
	}

	/**
	 * Efetua a manipulação dos modelos necessários
	 * para a aprensentação da lista de clientes
	 */
	public function listarTransportadorAction() {
		$o_Pessoa = new PessoaModel();

		//Listando os transportadores cadastrados
		$v_pessoas = $o_Pessoa -> _list(null, 'T');

		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de transportadores
		$o_view = new View('views/listarTransportador.phtml');

		//Passando os dados do transportador para a View
		$o_view -> setParams(array('v_pessoas' => $v_pessoas));

		//Imprimindo código HTML
		$o_view -> showContents();
	}

	/**
	 * Gerencia a  de criação
	 * e edição dos transportadores
	 */
	public function cadastroTransportadorAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do transportador foi passado
		if (isset($_REQUEST['idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idPes']))
				//buscando dados do transportador
				$o_pessoa -> loadById($_REQUEST['idPes']);

		if (count($_POST) > 0) {
			//Incluir o endereço antes
			$o_endereco = new EnderecoModel();

			if (isset($_REQUEST['idEnd']))
				if (DataValidator::isNumeric($_REQUEST['idEnd']))
					$o_endereco -> loadById($_REQUEST['idEnd']);
			$o_endereco -> setCepEnd(DataFilter::cleanString($_POST['cepEnd']));
			$o_endereco -> setRuaEnd(DataFilter::cleanString(protecao($_POST['ruaEnd'])));
			$o_endereco -> setBairroEnd(DataFilter::cleanString(protecao($_POST['bairroEnd'])));
			$o_endereco -> setComplementoEnd(DataFilter::cleanString(protecao($_POST['complementoEnd'])));
			$o_endereco -> setTb_Cidade_idCid(DataFilter::cleanString($_POST['tb_Cidade_idCid']));
			$o_endereco -> setTb_Estado_idEst(DataFilter::cleanString($_POST['tb_Estado_idEst']));
			$idEndereco = $o_endereco -> save();

			if (isset($_REQUEST['idEnd']))
				if (DataValidator::isNumeric($_REQUEST['idEnd']))
					$o_endereco -> loadById($_REQUEST['idEnd']);

			$o_pessoa -> setPrimeiroNomePes(DataFilter::cleanString(protecao($_POST['primeiroNomePes'])));
			$o_pessoa -> setSobreNomePes(DataFilter::cleanString(protecao($_POST['sobreNomePes'])));
			$o_pessoa -> setEmailPes(DataFilter::cleanString($_POST['emailPes']));
			$o_pessoa -> setSenhaPes(DataFilter::cleanString(md5($_POST['senhaPes'])));
			$o_pessoa -> setCpfCnpjPes(DataFilter::cleanString($_POST['cpfCnpjPes']));
			// verifica se foi enviado um arquivo
			if (isset($_FILES['fotoPes']['name']) && $_FILES["fotoPes"]["error"] == 0) {

				$arquivo_tmp = $_FILES['fotoPes']['tmp_name'];
				$nome = $_FILES['fotoPes']['name'];

				// Pega a extensao
				$extensao = strrchr($nome, '.');

				// Converte a extensao para mimusculo
				$extensao = strtolower($extensao);

				// Somente imagens, .jpg;.jpeg;.gif;.png
				// Aqui eu enfilero as extesões permitidas e separo por ';'
				// Isso server apenas para eu poder pesquisar dentro desta String
				if (strstr('.jpg;.jpeg;.gif;.png', $extensao)) {
					// Cria um nome único para esta imagem
					// Evita que duplique as imagens no servidor.
					$novoNome = md5(microtime()) . $extensao;

					// Concatena a pasta com o nome
					$destino = 'upload/pessoa/foto' . $novoNome;
					$o_pessoa -> setFotoPes(DataFilter::cleanString($destino));
					$_SESSION['fotoPes'] = $destino;
					// tenta mover o arquivo para o destino

				}

			} else {
				$o_pessoa -> setFotoPes(DataFilter::cleanString('http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png'));
			}
			$o_pessoa -> setTelefoneFixoPes(DataFilter::cleanString($_POST['telefoneFixoPes']));
			$o_pessoa -> setTelefoneCelularPes(DataFilter::cleanString($_POST['telefoneCelularPes']));
			$o_pessoa -> setTb_endereco_idEnd($idEndereco);
			//salvando dados e redirecionando para a lista de transportadores
			$idPessoa = $o_pessoa -> saveTran();

			if (isset($_REQUEST['codigoVou'])) {
				if (DataFilter::cleanString($_REQUEST['codigoVou'])) {

					$o_voucher = new VoucherModel();
					$o_voucher -> loadByCodigo(protecao($_POST['codigoVou']));
					$o_pessoa_voucher = New PessoaVoucherModel();
					$o_pessoa_voucher -> setTb_voucher_idVou($o_voucher -> getIdVou());
					$o_pessoa_voucher -> setTb_pessoa_idPes($idPessoa);
					$o_pessoa_voucher -> save();
				}
			}

			$o_resposta_pesquisa = new RespostaPesquisaModel();
			$o_resposta_pesquisa -> setTb_pergunta_pesquisa_idPqa(DataFilter::cleanString($_POST['tb_pergunta_pesquisa_idPqa']));
			$o_resposta_pesquisa -> setTb_pessoa_idPes($idPessoa);
			$o_resposta_pesquisa -> save();
			$arquivo = '<!DOCTYPE html "-//w3c//dtd xhtml 1.0 transitional //en" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
    <!--[if gte mso 9]><xml>
     <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
     </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <title>Template Base</title>
    
</head>
<body style="width: 100% !important;min-width: 100%;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100% !important;margin: 0;padding: 0;background-color: #FFFFFF">
    <style id="media-query">
        /* Client-specific Styles & Reset */
        #outlook a {
            padding: 0;
        }

        /* .ExternalClass applies to Outlook.com (the artist formerly known as Hotmail) */
        .ExternalClass {
            width: 100%;
        }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

        #backgroundTable {
            margin: 0;
            padding: 0;
            width: 100% !important;
            line-height: 100% !important;
        }

        /* Buttons */
        .button a {
            display: inline-block;
            text-decoration: none;
            -webkit-text-size-adjust: none;
            text-align: center;
        }

            .button a div {
                text-align: center !important;
            }

        /* Outlook First */
        body.outlook p {
            display: inline !important;
        }

        /*  Media Queries */
@media only screen and (max-width: 500px) {
  table[class="body"] img {
    height: auto !important;
    max-width: 100% !important; }
  table[class="body"] img.fullwidth {
    width: 100% !important; }
  table[class="body"] center {
    min-width: 0 !important; }
  table[class="body"] .container {
    width: 95% !important; }
  table[class="body"] .row {
    width: 100% !important;
    display: block !important; }
  table[class="body"] .wrapper {
    display: block !important;
    padding-right: 0 !important; }
  table[class="body"] .columns, table[class="body"] .column {
    table-layout: fixed !important;
    float: none !important;
    width: 100% !important;
    padding-right: 0px !important;
    padding-left: 0px !important;
    display: block !important; }
  table[class="body"] .wrapper.first .columns, table[class="body"] .wrapper.first .column {
    display: table !important; }
  table[class="body"] table.columns td, table[class="body"] table.column td, .col {
    width: 100% !important; }
  table[class="body"] table.columns td.expander {
    width: 1px !important; }
  table[class="body"] .right-text-pad, table[class="body"] .text-pad-right {
    padding-left: 10px !important; }
  table[class="body"] .left-text-pad, table[class="body"] .text-pad-left {
    padding-right: 10px !important; }
  table[class="body"] .hide-for-small, table[class="body"] .show-for-desktop {
    display: none !important; }
  table[class="body"] .show-for-small, table[class="body"] .hide-for-desktop {
    display: inherit !important; }
  .mixed-two-up .col {
    width: 100% !important; } }
 @media screen and (max-width: 500px) {
            div[class="col"] {
                width: 100% !important;
            }
        }

        @media screen and (min-width: 501px) {
            table[class="block-grid"] {
                width: 500px !important;
            }
        }
    </style>
    <table class="body" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 100%;width: 100%;table-layout: fixed" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tbody><tr style="vertical-align: top">
            <td class="center" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;background-color: #FFFFFF" align="center" valign="top">
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #2C3E50" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: #2C3E50" cellpadding="0" cellspacing="0" width="100%" bgcolor="#2C3E50">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;width: 100%;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px" align="center">
            <div align="center">

                <img class="center" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 150px;max-width: 150px" align="center" border="0" data-custom-width="150" src="http://www.savicki.com.br/template/imagesEmail/b4cc86fef13b4bbe9dd1335704e31160.png" alt="Image" title="Image">
            </div>
        </td>
    </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #18BC9C" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px" align="center">
            <div style="height: 10px;">
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 10px solid transparent;width: 100%" align="center" border="0" cellspacing="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center"></td>
                    </tr>
                </tbody></table>
            </div>
        </td>
    </tr>
</tbody></table><table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 30px;padding-right: 0px;padding-bottom: 30px;padding-left: 0px">
        <div style="color:#ffffff;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:28px; line-height:34px;">Ol&#225; <strong>' . $_POST['primeiroNomePes'] . '</strong></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:28px; line-height:34px;"><strong><br data-mce-bogus="1"></strong></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;"><span style="line-height: 21px; font-size: 18px;" data-mce-style="line-height: 21px; font-size: 18px;">Seu sadastro foi realizado com sucesso.</span></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;"><span style="line-height: 21px; font-size: 18px;" data-mce-style="line-height: 21px; font-size: 18px;"><br data-mce-bogus="1"></span></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;"><span style="line-height: 21px; font-size: 18px;" data-mce-style="line-height: 21px; font-size: 18px;"><a style="font-size: 18px; line-height: 21px;;color:#0000FF" href="http://www.savicki.com.br" data-mce-href="http://www.savicki.com.br">Clique aqui</a> e continue acessando o site normalmente.</span></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br></div><div style="font-size:14px;line-height:17px;text-align:center;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">&nbsp;<br></div><div style="font-size:14px;line-height:17px;text-align:center;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">&nbsp;<br></div>
        </div>
    </td>
  </tr>
</tbody></table>
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px" align="center">
            <div style="height: 10px;">
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 10px solid transparent;width: 100%" align="center" border="0" cellspacing="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center"></td>
                    </tr>
                </tbody></table>
            </div>
        </td>
    </tr>
</tbody></table>                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #2C3E50" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 30px;padding-right: 0px;padding-bottom: 30px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">

<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" valign="top">
      <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" border="0" cellspacing="0" cellpadding="0">
        <tbody><tr style="vertical-align: top">
          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px;max-width: 193px" align="center" valign="top">
            <!--[if (gte mso 9)|(IE)]>
            <table width="193" align="left" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="193" align="left">
            <![endif]-->
            <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
              <tbody><tr style="vertical-align: top">
                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="left" valign="middle">


                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="https://www.facebook.com/" title="Facebook" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/facebook.png" alt="Facebook" title="Facebook" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://twitter.com/" title="Twitter" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/twitter.png" alt="Twitter" title="Twitter" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://www.linkedin.com/" title="LinkedIn" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/linkedin.png" alt="LinkedIn" title="LinkedIn" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://instagram.com/" title="Instagram" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/instagram.png" alt="Instagram" title="Instagram" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>

                </td>
              </tr>
            </tbody></table>
            <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
            </table>
            <![endif]-->
          </td>
        </tr>
      </tbody></table>
    </td>
  </tr>
</tbody></table><table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 15px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#959595;line-height:150%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">Savicki.</p></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">Travessa Percy Withers, 121, cj 91 Curitiba-PR&nbsp;CEP 80240-190</p></div><div style="font-size:12px;line-height:18px;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 18px"></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">&nbsp;www.savicki.com.br</p></div>
        </div>
    </td>
  </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
            </td>
        </tr>
    </tbody></table>

</body></html>';
			// Envia o E-Mail
			$contato = new SendEmail();
			$contato -> nomeEmail = $_POST['primeiroNomePes'];
			// Nome do destinatário que vai receber o E-Mail
			$contato -> paraEmail = $_POST['emailPes'];
			// Email destinatário que vai receber o E-Mail

			$contato -> remetenteNome = "Savicki";
			// Nome do remetente que vai enviar o E-Mail
			$contato -> remetenteEmail = "contato@savicki.com.br";
			// E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail

			$contato -> assuntoEmail = $_POST['primeiroNomePes'] . ", seu cadastrado  foi concluído com êxito";
			// Assunto da mensagem

			$contato -> conteudoEmail = $arquivo;
			// Conteudo da mensagem

			$contato -> confirmacao = 1;
			// Se for 1 exibi a mensagem de confirmação
			$contato -> mensagem = "ok";
			// Mensagem de Confirmação
			$contato -> erroMsg = "erro";
			// pode colocar uma mensagem de erro aqui!!
			$contato -> confirmacaoErro = 1;
			// Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
			$contato -> enviar();
			// envia a mensagem

			Application::redirect('?controle=Pessoa&acao=paginaLogin&variavel=login');
			exit ;
		}

		$o_view = new View('views/cadastroTransportador.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();
	}

	/**
	 * Gerencia a  de criação
	 * e edição dos cientes
	 */
	public function cadastroClienteAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do cliente foi passado
		if (isset($_REQUEST['idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idPes']))
				//buscando dados do cliente
				$o_pessoa -> loadById($_REQUEST['idPes']);

		if (count($_POST) > 0) {
			$o_pessoa -> setPrimeiroNomePes(DataFilter::cleanString(protecao($_POST['primeiroNomePes'])));
			$o_pessoa -> setSobreNomePes(DataFilter::cleanString(protecao($_POST['sobreNomePes'])));
			if ($o_pessoa -> loadById(null, $_POST['emailPes'])) {

			}

			$o_pessoa -> setEmailPes(DataFilter::cleanString($_POST['emailPes']));
			$o_pessoa -> setSenhaPes(DataFilter::cleanString(md5($_POST['senhaPes'])));
			$o_pessoa -> setCpfCnpjPes(DataFilter::cleanString($_POST['cpfCnpjPes']));

			// verifica se foi enviado um arquivo
			if (isset($_FILES['fotoPes']['name']) && $_FILES["fotoPes"]["error"] == 0) {

				$arquivo_tmp = $_FILES['fotoPes']['tmp_name'];
				$nome = $_FILES['fotoPes']['name'];

				// Pega a extensao
				$extensao = strrchr($nome, '.');

				// Converte a extensao para mimusculo
				$extensao = strtolower($extensao);

				// Somente imagens, .jpg;.jpeg;.gif;.png
				// Aqui eu enfilero as extesões permitidas e separo por ';'
				// Isso server apenas para eu poder pesquisar dentro desta String
				if (strstr('.jpg;.jpeg;.gif;.png', $extensao)) {
					// Cria um nome único para esta imagem
					// Evita que duplique as imagens no servidor.
					$novoNome = md5(microtime()) . $extensao;

					// Concatena a pasta com o nome
					$destino = 'upload/pessoa/foto' . $novoNome;
					$o_pessoa -> setFotoPes(DataFilter::cleanString($destino));
					$_SESSION['fotoPes'] = $destino;

					// tenta mover o arquivo para o destino
					if (@move_uploaded_file($arquivo_tmp, $destino)) {

					}

				}

			} else {
				$o_pessoa -> setFotoPes(DataFilter::cleanString('http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png'));
			}

			$o_pessoa -> setTelefoneFixoPes(DataFilter::cleanString($_POST['telefoneFixoPes']));
			$o_pessoa -> setTelefoneCelularPes(DataFilter::cleanString($_POST['telefoneCelularPes']));
			//salvando dados e redirecionando para a lista de clientes
			$idPessoa = $o_pessoa -> saveCli();

			$o_resposta_pesquisa = new RespostaPesquisaModel();
			$o_resposta_pesquisa -> setTb_pergunta_pesquisa_idPqa(DataFilter::cleanString($_POST['tb_pergunta_pesquisa_idPqa']));
			$o_resposta_pesquisa -> setTb_pessoa_idPes($idPessoa);
			$o_resposta_pesquisa -> save();
			$arquivo = '<!DOCTYPE html "-//w3c//dtd xhtml 1.0 transitional //en" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
    <!--[if gte mso 9]><xml>
     <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
     </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <title>Template Base</title>
    
</head>
<body style="width: 100% !important;min-width: 100%;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100% !important;margin: 0;padding: 0;background-color: #FFFFFF">
    <style id="media-query">
        /* Client-specific Styles & Reset */
        #outlook a {
            padding: 0;
        }

        /* .ExternalClass applies to Outlook.com (the artist formerly known as Hotmail) */
        .ExternalClass {
            width: 100%;
        }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

        #backgroundTable {
            margin: 0;
            padding: 0;
            width: 100% !important;
            line-height: 100% !important;
        }

        /* Buttons */
        .button a {
            display: inline-block;
            text-decoration: none;
            -webkit-text-size-adjust: none;
            text-align: center;
        }

            .button a div {
                text-align: center !important;
            }

        /* Outlook First */
        body.outlook p {
            display: inline !important;
        }

        /*  Media Queries */
@media only screen and (max-width: 500px) {
  table[class="body"] img {
    height: auto !important;
    max-width: 100% !important; }
  table[class="body"] img.fullwidth {
    width: 100% !important; }
  table[class="body"] center {
    min-width: 0 !important; }
  table[class="body"] .container {
    width: 95% !important; }
  table[class="body"] .row {
    width: 100% !important;
    display: block !important; }
  table[class="body"] .wrapper {
    display: block !important;
    padding-right: 0 !important; }
  table[class="body"] .columns, table[class="body"] .column {
    table-layout: fixed !important;
    float: none !important;
    width: 100% !important;
    padding-right: 0px !important;
    padding-left: 0px !important;
    display: block !important; }
  table[class="body"] .wrapper.first .columns, table[class="body"] .wrapper.first .column {
    display: table !important; }
  table[class="body"] table.columns td, table[class="body"] table.column td, .col {
    width: 100% !important; }
  table[class="body"] table.columns td.expander {
    width: 1px !important; }
  table[class="body"] .right-text-pad, table[class="body"] .text-pad-right {
    padding-left: 10px !important; }
  table[class="body"] .left-text-pad, table[class="body"] .text-pad-left {
    padding-right: 10px !important; }
  table[class="body"] .hide-for-small, table[class="body"] .show-for-desktop {
    display: none !important; }
  table[class="body"] .show-for-small, table[class="body"] .hide-for-desktop {
    display: inherit !important; }
  .mixed-two-up .col {
    width: 100% !important; } }
 @media screen and (max-width: 500px) {
            div[class="col"] {
                width: 100% !important;
            }
        }

        @media screen and (min-width: 501px) {
            table[class="block-grid"] {
                width: 500px !important;
            }
        }
    </style>
    <table class="body" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 100%;width: 100%;table-layout: fixed" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tbody><tr style="vertical-align: top">
            <td class="center" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;background-color: #FFFFFF" align="center" valign="top">
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #2C3E50" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: #2C3E50" cellpadding="0" cellspacing="0" width="100%" bgcolor="#2C3E50">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;width: 100%;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px" align="center">
            <div align="center">

                <img class="center" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 150px;max-width: 150px" align="center" border="0" data-custom-width="150" src="http://www.savicki.com.br/template/imagesEmail/b4cc86fef13b4bbe9dd1335704e31160.png" alt="Image" title="Image">
            </div>
        </td>
    </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #18BC9C" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px" align="center">
            <div style="height: 10px;">
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 10px solid transparent;width: 100%" align="center" border="0" cellspacing="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center"></td>
                    </tr>
                </tbody></table>
            </div>
        </td>
    </tr>
</tbody></table><table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 30px;padding-right: 0px;padding-bottom: 30px;padding-left: 0px">
        <div style="color:#ffffff;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:28px; line-height:34px;">Ol&#225; <strong>' . $_POST['primeiroNomePes'] . '</strong></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:28px; line-height:34px;"><strong><br data-mce-bogus="1"></strong></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;"><span style="line-height: 21px; font-size: 18px;" data-mce-style="line-height: 21px; font-size: 18px;">Seu sadastro foi realizado com sucesso.</span></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;"><span style="line-height: 21px; font-size: 18px;" data-mce-style="line-height: 21px; font-size: 18px;"><br data-mce-bogus="1"></span></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;"><span style="line-height: 21px; font-size: 18px;" data-mce-style="line-height: 21px; font-size: 18px;"><a style="font-size: 18px; line-height: 21px;;color:#0000FF" href="http://www.savicki.com.br/" data-mce-href="http://www.savicki.com.br">Clique aqui</a> e continue acessando o site normalmente.</span></span></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br></div><div style="font-size:14px;line-height:17px;text-align:center;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">&nbsp;<br></div><div style="font-size:14px;line-height:17px;text-align:center;color:#ffffff;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">&nbsp;<br></div>
        </div>
    </td>
  </tr>
</tbody></table>
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px" align="center">
            <div style="height: 10px;">
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 10px solid transparent;width: 100%" align="center" border="0" cellspacing="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center"></td>
                    </tr>
                </tbody></table>
            </div>
        </td>
    </tr>
</tbody></table>                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #2C3E50" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 30px;padding-right: 0px;padding-bottom: 30px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">

<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" valign="top">
      <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" border="0" cellspacing="0" cellpadding="0">
        <tbody><tr style="vertical-align: top">
          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px;max-width: 193px" align="center" valign="top">
            <!--[if (gte mso 9)|(IE)]>
            <table width="193" align="left" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="193" align="left">
            <![endif]-->
            <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
              <tbody><tr style="vertical-align: top">
                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="left" valign="middle">


                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="https://www.facebook.com/" title="Facebook" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/facebook.png" alt="Facebook" title="Facebook" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://twitter.com/" title="Twitter" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/twitter.png" alt="Twitter" title="Twitter" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://www.linkedin.com/" title="LinkedIn" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/linkedin.png" alt="LinkedIn" title="LinkedIn" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://instagram.com/" title="Instagram" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/instagram.png" alt="Instagram" title="Instagram" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>

                </td>
              </tr>
            </tbody></table>
            <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
            </table>
            <![endif]-->
          </td>
        </tr>
      </tbody></table>
    </td>
  </tr>
</tbody></table><table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 15px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#959595;line-height:150%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">Savicki.</p></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">Travessa Percy Withers, 121, cj 91 Curitiba-PR&nbsp;CEP 80240-190</p></div><div style="font-size:12px;line-height:18px;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 18px"></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">&nbsp;www.savicki.com.br</p></div>
        </div>
    </td>
  </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
            </td>
        </tr>
    </tbody></table>

</body></html>';
			// Envia o E-Mail
			$contato = new SendEmail();
			$contato -> nomeEmail = $_POST['primeiroNomePes'];
			// Nome do destinatário que vai receber o E-Mail
			$contato -> paraEmail = $_POST['emailPes'];
			// Email destinatário que vai receber o E-Mail

			$contato -> remetenteNome = "Savicki";
			// Nome do remetente que vai enviar o E-Mail
			$contato -> remetenteEmail = "contato@savicki.com.br";
			// E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail

			$contato -> assuntoEmail = $_POST['primeiroNomePes'] . ", seu cadastrado  foi concluído com êxito";
			// Assunto da mensagem

			$contato -> conteudoEmail = $arquivo;
			// Conteudo da mensagem

			$contato -> confirmacao = 1;
			// Se for 1 exibi a mensagem de confirmação
			$contato -> mensagem = "ok";
			// Mensagem de Confirmação
			$contato -> erroMsg = "erro";
			// pode colocar uma mensagem de erro aqui!!
			$contato -> confirmacaoErro = 1;
			// Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
			$contato -> enviar();
			// envia a mensagem
			Application::redirect('?controle=Pessoa&acao=paginaLogin&variavel=login');
			exit ;

		}

		$o_view = new View('views/cadastroCliente.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();
	}

	public function manterClienteCAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do cliente foi passado
		if (isset($_REQUEST['idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idPes']))
				//buscando dados do cliente
				$o_pessoa -> loadById($_REQUEST['idPes']);

		if (count($_POST) > 0) {
			$o_pessoa -> setPrimeiroNomePes(DataFilter::cleanString(protecao($_POST['primeiroNomePes'])));
			$o_pessoa -> setSobreNomePes(DataFilter::cleanString(protecao($_POST['sobreNomePes'])));
			$o_pessoa -> setEmailPes(DataFilter::cleanString($_POST['emailPes']));
			if ($_POST['senhaPes'] <> '') {
				$o_pessoa -> setSenhaPes(DataFilter::cleanString(md5($_POST['senhaPes'])));
			}

			$o_pessoa -> setCpfCnpjPes(DataFilter::cleanString($_POST['cpfCnpjPes']));

			// verifica se foi enviado um arquivo
			if (isset($_FILES['fotoPes']['name']) && $_FILES["fotoPes"]["error"] == 0) {

				$arquivo_tmp = $_FILES['fotoPes']['tmp_name'];
				$nome = $_FILES['fotoPes']['name'];

				// Pega a extensao
				$extensao = strrchr($nome, '.');

				// Converte a extensao para mimusculo
				$extensao = strtolower($extensao);

				// Somente imagens, .jpg;.jpeg;.gif;.png
				// Aqui eu enfilero as extesões permitidas e separo por ';'
				// Isso server apenas para eu poder pesquisar dentro desta String
				if (strstr('.jpg;.jpeg;.gif;.png', $extensao)) {
					// Cria um nome único para esta imagem
					// Evita que duplique as imagens no servidor.
					$novoNome = md5(microtime()) . $extensao;

					// Concatena a pasta com o nome
					$destino = 'upload/pessoa/foto' . $novoNome;
					$o_pessoa -> setFotoPes(DataFilter::cleanString($destino));
					$_SESSION['fotoPes'] = $destino;

					// tenta mover o arquivo para o destino
					if (@move_uploaded_file($arquivo_tmp, $destino)) {

					}

				}

			} else {
				$o_pessoa -> setFotoPes(DataFilter::cleanString(isset($_POST['fotoPes'])));
			}

			$o_pessoa -> setTelefoneFixoPes(DataFilter::cleanString($_POST['telefoneFixoPes']));
			$o_pessoa -> setTelefoneCelularPes(DataFilter::cleanString($_POST['telefoneCelularPes']));
			//salvando dados e redirecionando para a lista de clientes
			if ($o_pessoa -> saveCli() > 0)
				$o_pessoa -> saveCli();
			Application::redirect('?controle=Pessoa&acao=areaCliente&variavel=editado');
			exit ;

		}

		$o_view = new View('views/manterCliente.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();
	}

	public function manterClienteAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do cliente foi passado
		if (isset($_REQUEST['idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idPes']))
				//buscando dados do cliente
				$o_pessoa -> loadById($_REQUEST['idPes']);

		if (count($_POST) > 0) {
			$o_pessoa -> setPrimeiroNomePes(DataFilter::cleanString(protecao($_POST['primeiroNomePes'])));
			$o_pessoa -> setSobreNomePes(DataFilter::cleanString(protecao($_POST['sobreNomePes'])));
			$o_pessoa -> setEmailPes(DataFilter::cleanString($_POST['emailPes']));
			if ($_POST['senhaPes'] <> '') {
				$o_pessoa -> setSenhaPes(DataFilter::cleanString(md5($_POST['senhaPes'])));
			}
			$o_pessoa -> setCpfCnpjPes(DataFilter::cleanString($_POST['cpfCnpjPes']));
			$o_pessoa -> setTb_Status_idSta(DataFilter::cleanString($_POST['tb_Status_idSta']));

			// verifica se foi enviado um arquivo
			if (isset($_FILES['fotoPes']['name']) && $_FILES["fotoPes"]["error"] == 0) {

				$arquivo_tmp = $_FILES['fotoPes']['tmp_name'];
				$nome = $_FILES['fotoPes']['name'];

				// Pega a extensao
				$extensao = strrchr($nome, '.');

				// Converte a extensao para mimusculo
				$extensao = strtolower($extensao);

				// Somente imagens, .jpg;.jpeg;.gif;.png
				// Aqui eu enfilero as extesões permitidas e separo por ';'
				// Isso server apenas para eu poder pesquisar dentro desta String
				if (strstr('.jpg;.jpeg;.gif;.png', $extensao)) {
					// Cria um nome único para esta imagem
					// Evita que duplique as imagens no servidor.
					$novoNome = md5(microtime()) . $extensao;

					// Concatena a pasta com o nome
					$destino = 'upload/pessoa/foto' . $novoNome;
					$o_pessoa -> setFotoPes(DataFilter::cleanString($destino));
					$_SESSION['fotoPes'] = $destino;

					// tenta mover o arquivo para o destino
					if (@move_uploaded_file($arquivo_tmp, $destino)) {

					}

				}

			} else {
				$o_pessoa -> setFotoPes(DataFilter::cleanString(isset($_POST['fotoPes'])));
			}

			$o_pessoa -> setTelefoneFixoPes(DataFilter::cleanString($_POST['telefoneFixoPes']));
			$o_pessoa -> setTelefoneCelularPes(DataFilter::cleanString($_POST['telefoneCelularPes']));
			//salvando dados e redirecionando para a lista de clientes
			if ($o_pessoa -> saveCli() > 0)
				$o_pessoa -> saveCli();
			Application::redirect('?controle=Pessoa&acao=listarCliente&variavel=editado');
			exit ;

		}

		$o_view = new View('views/manterCliente.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();
	}

	public function manterTransportadorTAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do transportador foi passado
		if (isset($_REQUEST['idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idPes']))
				//buscando dados do transportador
				$o_pessoa -> loadById($_REQUEST['idPes']);

		if (count($_POST) > 0) {
			//Incluir o endereço antes
			$o_endereco = new EnderecoModel();
			if (isset($_REQUEST['idEnd']))
				if (DataValidator::isNumeric($_REQUEST['idEnd']))
					$o_endereco -> loadById($_REQUEST['idEnd']);
			$o_endereco -> setCepEnd(DataFilter::cleanString($_POST['cepEnd']));
			$o_endereco -> setRuaEnd(DataFilter::cleanString(protecao($_POST['ruaEnd'])));
			$o_endereco -> setBairroEnd(DataFilter::cleanString(protecao($_POST['bairroEnd'])));
			$o_endereco -> setComplementoEnd(DataFilter::cleanString(protecao($_POST['complementoEnd'])));
			$o_endereco -> setTb_Cidade_idCid(DataFilter::cleanString($_POST['tb_Cidade_idCid']));
			$o_endereco -> setTb_Estado_idEst(DataFilter::cleanString($_POST['tb_Estado_idEst']));
			$idEndereco = $o_endereco -> save();

			if (isset($_REQUEST['idEnd']))
				if (DataValidator::isNumeric($_REQUEST['idEnd']))
					$o_endereco -> loadById($_REQUEST['idEnd']);

			$o_pessoa -> setPrimeiroNomePes(DataFilter::cleanString(protecao($_POST['primeiroNomePes'])));
			$o_pessoa -> setSobreNomePes(DataFilter::cleanString(protecao($_POST['sobreNomePes'])));
			$o_pessoa -> setEmailPes(DataFilter::cleanString($_POST['emailPes']));
			if ($_POST['senhaPes'] <> '') {
				$o_pessoa -> setSenhaPes(DataFilter::cleanString(md5($_POST['senhaPes'])));
			}
			$o_pessoa -> setCpfCnpjPes(DataFilter::cleanString($_POST['cpfCnpjPes']));

			// verifica se foi enviado um arquivo
			if (isset($_FILES['fotoPes']['name']) && $_FILES["fotoPes"]["error"] == 0) {

				$arquivo_tmp = $_FILES['fotoPes']['tmp_name'];
				$nome = $_FILES['fotoPes']['name'];

				// Pega a extensao
				$extensao = strrchr($nome, '.');

				// Converte a extensao para mimusculo
				$extensao = strtolower($extensao);

				// Somente imagens, .jpg;.jpeg;.gif;.png
				// Aqui eu enfilero as extesões permitidas e separo por ';'
				// Isso server apenas para eu poder pesquisar dentro desta String
				if (strstr('.jpg;.jpeg;.gif;.png', $extensao)) {
					// Cria um nome único para esta imagem
					// Evita que duplique as imagens no servidor.
					$novoNome = md5(microtime()) . $extensao;

					// Concatena a pasta com o nome
					$destino = 'upload/pessoa/foto' . $novoNome;
					$o_pessoa -> setFotoPes(DataFilter::cleanString($destino));
					$_SESSION['fotoPes'] = $destino;

					// tenta mover o arquivo para o destino
					if (@move_uploaded_file($arquivo_tmp, $destino)) {

					}

				}

			} else {
				$o_pessoa -> setFotoPes(DataFilter::cleanString(isset($_POST['fotoPes'])));
			}

			$o_pessoa -> setTelefoneFixoPes(DataFilter::cleanString($_POST['telefoneFixoPes']));
			$o_pessoa -> setTelefoneCelularPes(DataFilter::cleanString($_POST['telefoneCelularPes']));
			$o_pessoa -> setTb_endereco_idEnd($idEndereco);

			//salvando dados e redirecionando para a lista de transportadores
			$o_pessoa -> saveTran();
			Application::redirect('?controle=Pessoa&acao=areaTransportador&variavel=editado');
			exit ;

		}
		$o_view = new View('views/manterTransportador.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();
	}

	public function manterTransportadorAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do transportador foi passado
		if (isset($_REQUEST['idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idPes']))
				//buscando dados do transportador
				$o_pessoa -> loadById($_REQUEST['idPes']);

		if (count($_POST) > 0) {
			//Incluir o endereço antes
			$o_endereco = new EnderecoModel();
			if (isset($_REQUEST['idEnd']))
				if (DataValidator::isNumeric($_REQUEST['idEnd']))
					$o_endereco -> loadById($_REQUEST['idEnd']);
			$o_endereco -> setCepEnd(DataFilter::cleanString($_POST['cepEnd']));
			$o_endereco -> setRuaEnd(DataFilter::cleanString(protecao($_POST['ruaEnd'])));
			$o_endereco -> setBairroEnd(DataFilter::cleanString(protecao($_POST['bairroEnd'])));
			$o_endereco -> setComplementoEnd(DataFilter::cleanString(protecao($_POST['complementoEnd'])));
			$o_endereco -> setTb_Cidade_idCid(DataFilter::cleanString($_POST['tb_Cidade_idCid']));
			$o_endereco -> setTb_Estado_idEst(DataFilter::cleanString($_POST['tb_Estado_idEst']));
			$idEndereco = $o_endereco -> save();

			if (isset($_REQUEST['idEnd']))
				if (DataValidator::isNumeric($_REQUEST['idEnd']))
					$o_endereco -> loadById($_REQUEST['idEnd']);

			$o_pessoa -> setPrimeiroNomePes(DataFilter::cleanString(protecao($_POST['primeiroNomePes'])));
			$o_pessoa -> setSobreNomePes(DataFilter::cleanString(protecao($_POST['sobreNomePes'])));
			$o_pessoa -> setEmailPes(DataFilter::cleanString($_POST['emailPes']));
			if ($_POST['senhaPes'] <> '') {
				$o_pessoa -> setSenhaPes(DataFilter::cleanString(md5($_POST['senhaPes'])));
			}
			$o_pessoa -> setCpfCnpjPes(DataFilter::cleanString($_POST['cpfCnpjPes']));
			$o_pessoa -> setTb_Status_idSta(DataFilter::cleanString($_POST['tb_Status_idSta']));

			// verifica se foi enviado um arquivo
			if (isset($_FILES['fotoPes']['name']) && $_FILES["fotoPes"]["error"] == 0) {

				$arquivo_tmp = $_FILES['fotoPes']['tmp_name'];
				$nome = $_FILES['fotoPes']['name'];

				// Pega a extensao
				$extensao = strrchr($nome, '.');

				// Converte a extensao para mimusculo
				$extensao = strtolower($extensao);

				// Somente imagens, .jpg;.jpeg;.gif;.png
				// Aqui eu enfilero as extesões permitidas e separo por ';'
				// Isso server apenas para eu poder pesquisar dentro desta String
				if (strstr('.jpg;.jpeg;.gif;.png', $extensao)) {
					// Cria um nome único para esta imagem
					// Evita que duplique as imagens no servidor.
					$novoNome = md5(microtime()) . $extensao;

					// Concatena a pasta com o nome
					$destino = 'upload/pessoa/foto' . $novoNome;
					$o_pessoa -> setFotoPes(DataFilter::cleanString($destino));
					$_SESSION['fotoPes'] = $destino;

					// tenta mover o arquivo para o destino
					if (@move_uploaded_file($arquivo_tmp, $destino)) {

					}

				}

			} else {
				$o_pessoa -> setFotoPes(DataFilter::cleanString(isset($_POST['fotoPes'])));
			}

			$o_pessoa -> setTelefoneFixoPes(DataFilter::cleanString($_POST['telefoneFixoPes']));
			$o_pessoa -> setTelefoneCelularPes(DataFilter::cleanString($_POST['telefoneCelularPes']));
			$o_pessoa -> setTb_endereco_idEnd($idEndereco);

			//salvando dados e redirecionando para a lista de transportadores
			$o_pessoa -> saveTran();
			Application::redirect('?controle=Pessoa&acao=listarTransportador&variavel=editado');
			exit ;

		}
		$o_view = new View('views/manterTransportador.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();
	}

	/**
	 * Gerencia a requisições de exclusão das pessoas
	 */
	public function apagarPessoaAction() {
		if (DataValidator::isNumeric($_GET['idPes'])) {
			//apagando a pessoa
			$o_pessoa = new PessoaModel();
			$o_pessoa -> loadById($_GET['idPes']);
			$o_pessoa -> delete();

			Application::redirect('?controle=Pessoa&acao=listarPessoa');
			exit ;
		}
	}

	/**
	 * Gerencia a requisições de exclusão dos transportadores
	 */
	public function apagarTransportadorAction() {
		if (DataValidator::isNumeric($_GET['idPes'])) {

			//apagando o transportador
			$o_pessoa = new PessoaModel();
			$o_pessoa -> loadById($_GET['idPes']);
			$o_pessoa -> delete();

			//apagando o endereco
			$o_endereco = new EnderecoModel();
			$o_endereco -> loadById(null, $_GET['idPes']);
			$o_endereco -> delete();

			Application::redirect('?controle=Pessoa&acao=listarTransportador');
			exit ;
		}
	}

	/**
	 * Gerencia a requisições de exclusão das pessoas
	 */
	public function apagarClienteAction() {
		if (DataValidator::isNumeric($_GET['idPes'])) {
			//apagando o cliente
			$o_pessoa = new PessoaModel();
			$o_pessoa -> loadById($_GET['idPes']);
			$o_pessoa -> delete();

			Application::redirect('?controle=Pessoa&acao=listarCliente');
			exit ;
		}
	}

	/**
	 * Efetua a manipulação dos modelos necessários
	 * para a aprensentação da lista de transportadores
	 */
	public function pesquisarTransportadorAction() {
		$o_Pessoa = new PessoaModel();

		//Listando os transportadores cadastrados
		$v_pessoas = $o_Pessoa -> _list(null, 'T');

		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/pesquisarPessoa.phtml');

		//Passando os dados do transportador para a View
		$o_view -> setParams(array('v_pessoas' => $v_pessoas));

		//Imprimindo código HTML
		$o_view -> showContents();
	}

	/**
	 * Efetua a manipulação dos modelos necessários
	 * para a aprensentação da lista de clientes
	 */
	public function pesquisarClienteAction() {
		$o_Pessoa = new PessoaModel();

		//Listando os clientes cadastrados
		$v_pessoas = $o_Pessoa -> _list(null, 'U');

		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de clientes
		$o_view = new View('views/pesquisarPessoa.phtml');

		//Passando os dados do cliente para a View
		$o_view -> setParams(array('v_pessoas' => $v_pessoas));

		//Imprimindo código HTML
		$o_view -> showContents();
	}

	/**
	 * Efetua a manipulação dos modelos necessários
	 * para a aprensentação da lista de pessoas
	 */
	public function pesquisarPessoaAction() {
		$o_Pessoa = new PessoaModel();

		//Listando as pessoas cadastradas
		$v_pessoas = $o_Pessoa -> _list(null, 'U');

		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de pessoas
		$o_view = new View('views/pesquisarPessoa.phtml');

		//Passando os dados da pessoa para a View
		$o_view -> setParams(array('v_pessoas' => $v_pessoas));

		//Imprimindo código HTML
		$o_view -> showContents();
	}

	public function paginaLoginAction() {
		$o_view = new View('views/login.php');
		$o_view -> showContents();
	}

	public function recuperarSenhaAction() {
		$o_Pessoa = new PessoaModel();

		//verificando se o id do lance foi passado
		if (isset($_REQUEST['fEmail']))
			//verificando se o id passado é valido
			$o_Pessoa -> loadById(null, $_REQUEST['fEmail']);

		if (count($_POST) > 0) {
			/*
			 ENVIO DE E-MAIL
			 NÃO ESQUECER DE COLOCAR O INCLUDE: include("lib/email.php");
			 */
			$arquivo = '<!DOCTYPE html "-//w3c//dtd xhtml 1.0 transitional //en" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
    <!--[if gte mso 9]><xml>
     <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
     </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <title>Template Base</title>
    
</head>
<body style="width: 100% !important;min-width: 100%;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100% !important;margin: 0;padding: 0;background-color: #FFFFFF">
    <style id="media-query">
        /* Client-specific Styles & Reset */
        #outlook a {
            padding: 0;
        }

        /* .ExternalClass applies to Outlook.com (the artist formerly known as Hotmail) */
        .ExternalClass {
            width: 100%;
        }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

        #backgroundTable {
            margin: 0;
            padding: 0;
            width: 100% !important;
            line-height: 100% !important;
        }

        /* Buttons */
        .button a {
            display: inline-block;
            text-decoration: none;
            -webkit-text-size-adjust: none;
            text-align: center;
        }

            .button a div {
                text-align: center !important;
            }

        /* Outlook First */
        body.outlook p {
            display: inline !important;
        }

        /*  Media Queries */
@media only screen and (max-width: 500px) {
  table[class="body"] img {
    height: auto !important;
    max-width: 100% !important; }
  table[class="body"] img.fullwidth {
    width: 100% !important; }
  table[class="body"] center {
    min-width: 0 !important; }
  table[class="body"] .container {
    width: 95% !important; }
  table[class="body"] .row {
    width: 100% !important;
    display: block !important; }
  table[class="body"] .wrapper {
    display: block !important;
    padding-right: 0 !important; }
  table[class="body"] .columns, table[class="body"] .column {
    table-layout: fixed !important;
    float: none !important;
    width: 100% !important;
    padding-right: 0px !important;
    padding-left: 0px !important;
    display: block !important; }
  table[class="body"] .wrapper.first .columns, table[class="body"] .wrapper.first .column {
    display: table !important; }
  table[class="body"] table.columns td, table[class="body"] table.column td, .col {
    width: 100% !important; }
  table[class="body"] table.columns td.expander {
    width: 1px !important; }
  table[class="body"] .right-text-pad, table[class="body"] .text-pad-right {
    padding-left: 10px !important; }
  table[class="body"] .left-text-pad, table[class="body"] .text-pad-left {
    padding-right: 10px !important; }
  table[class="body"] .hide-for-small, table[class="body"] .show-for-desktop {
    display: none !important; }
  table[class="body"] .show-for-small, table[class="body"] .hide-for-desktop {
    display: inherit !important; }
  .mixed-two-up .col {
    width: 100% !important; } }
 @media screen and (max-width: 500px) {
            div[class="col"] {
                width: 100% !important;
            }
        }

        @media screen and (min-width: 501px) {
            table[class="block-grid"] {
                width: 500px !important;
            }
        }
    </style>
    <table class="body" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 100%;width: 100%;table-layout: fixed" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tbody><tr style="vertical-align: top">
            <td class="center" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;background-color: #FFFFFF" align="center" valign="top">
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #2C3E50" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: #2C3E50" cellpadding="0" cellspacing="0" width="100%" bgcolor="#2C3E50">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;width: 100%;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px" align="center">
            <div align="center">

                <img class="center" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 150px;max-width: 150px" align="center" border="0" data-custom-width="150" src="http://www.savicki.com.br/template/imagesEmail/b4cc86fef13b4bbe9dd1335704e31160.png" alt="Image" title="Image">
            </div>
        </td>
    </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #18BC9C" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#FFFFFF;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br> <span style="font-size: 18px; line-height: 21px;" data-mce-style="font-size: 18px;">Recebemos um pedido para redefinir senha em nosso sistema.</span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;"><br data-mce-bogus="1"></span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">Nome: ' . $o_Pessoa -> getPrimeiroNomePes() . '</span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">E-mail: <a style="font-size: 18px; line-height: 21px;;color:#0000FF" href="mailto:vitorsavicki@hotmail.com">' . $o_Pessoa -> getEmailPes() . '</a></span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;"><br data-mce-bogus="1"></span></div><div style="font-size:12px;line-height:14px;text-align:center;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;">Para redefinir a senha clique no bot&#227;o abaixo.</span></div>
        </div>
    </td>
  </tr>
</tbody></table>
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr style="vertical-align: top">
    <td class="button-container" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px" align="center">
      <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">

        <tbody><tr style="vertical-align: top">
          <td class="button" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;height: 22px;text-align: center" width="100%" align="center" valign="middle">
             <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr style="vertical-align: top">
                  <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" width="166">
              <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/?controle=Pessoa&amp;acao=redefinirSenha&amp;nome=&#39; . $o_Pessoa-&gt;getPrimeiroNomePes() . &#39;&amp;idPes=&#39; . $o_Pessoa-&gt;getidPes() . &#39;"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:166px;"
                  arcsize="10%" 
                  strokecolor="#3C8DBC"
                  fillcolor="#3C8DBC" >
                <w:anchorlock/>
                  <center 
                    style="color:#ffffff;
                      font-family:Arial, &#39;Helvetica Neue&#39;, Helvetica, sans-serif;
                      font-size:16px">
              <![endif]--> 
              <!--[if !mso]><!- - --><div style="display: inline-block;
              border-radius: 4px; 
              -webkit-border-radius: 4px; 
              -moz-border-radius: 4px; 
              max-width: 100%;
              width: auto;                      
              border-top: 0px solid transparent;
              border-right: 0px solid transparent;
              border-bottom: 0px solid transparent;
              border-left: 0px solid transparent;" align="center"><table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tbody><tr style="vertical-align: top"> 
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #3C8DBC;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 20px;                    padding-bottom: 5px;                    padding-left: 20px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #3C8DBC;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/?controle=Pessoa&acao=redefinirSenha&nome=' . $o_Pessoa -> getPrimeiroNomePes() . '&idPes=' . $o_Pessoa -> getidPes() . '" target="_blank">
                            <!--[if !mso]><!- - -->
                            <p style="margin: 0;font-family: inherit;font-size: 16px;line-height: 32px" data-mce-style="font-family: inherit; font-size: 16px; line-height: 32px;">Redefinir Senha&nbsp;<br></p>
                            <!--<![endif]-->
                            <!--[if mso]>
                            Redefinir Senha 
                            <![endif]--> 
                        </a><!--[if !mso]><!- - --></td></tr></tbody></table></div><!--<![endif]-->
              <!--[if mso]>
                    </center>
                </v:roundrect>
              <![endif]--> 
                    </td>
                </tr>
              </tbody></table>
              <div>
          </div></td>
        </tr>
      </tbody></table>
    </td>
  </tr>
</tbody></table>
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#FFFFFF;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">Caso voc&#234; nao fez este pedido em nosso site, por favor, ignore este e-mail!</span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">Atenciosamente Equipe Savicki.</span></div>
        </div>
    </td>
  </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
                <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #2C3E50" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody><tr style="vertical-align: top">
                        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="500" class="ieCell" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="container" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 500px;margin: 0 auto;text-align: inherit" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                <tbody><tr style="vertical-align: top">
                                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="100%">
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="500"><![endif]--><div class="col num12" style="display: inline-block;vertical-align: top;width: 100%">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 30px;padding-right: 0px;padding-bottom: 30px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">

<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" valign="top">
      <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" border="0" cellspacing="0" cellpadding="0">
        <tbody><tr style="vertical-align: top">
          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px;max-width: 193px" align="center" valign="top">
            <!--[if (gte mso 9)|(IE)]>
            <table width="193" align="left" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="193" align="left">
            <![endif]-->
            <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
              <tbody><tr style="vertical-align: top">
                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="left" valign="middle">


                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="https://www.facebook.com/" title="Facebook" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/facebook.png" alt="Facebook" title="Facebook" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://twitter.com/" title="Twitter" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/twitter.png" alt="Twitter" title="Twitter" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://www.linkedin.com/" title="LinkedIn" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/linkedin.png" alt="LinkedIn" title="LinkedIn" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>
                  <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" align="left" border="0" cellspacing="0" cellpadding="0" height="37">
                      <tbody><tr style="vertical-align: top">
                          <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" width="37" align="left" valign="middle">
                            <a href="http://instagram.com/" title="Instagram" target="_blank">
                                <img style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important" src="http://www.savicki.com.br/template/imagesEmail/instagram.png" alt="Instagram" title="Instagram" width="32">
                            </a>
                          </td>
                      </tr>
                  </tbody></table>

                </td>
              </tr>
            </tbody></table>
            <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
            </table>
            <![endif]-->
          </td>
        </tr>
      </tbody></table>
    </td>
  </tr>
</tbody></table><table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 15px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#959595;line-height:150%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">Savicki.</p></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">Travessa Percy Withers, 121, cj 91 Curitiba-PR&nbsp;CEP 80240-190</p></div><div style="font-size:12px;line-height:18px;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 18px"></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">&nbsp;www.savicki.com.br</p></div>
        </div>
    </td>
  </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]-->                                                    <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                    </table><![endif]-->
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                            <!--[if (gte mso 9)|(IE)]>
                                       </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody></table>
            </td>
        </tr>
    </tbody></table>

</body></html>';

			// Envia o E-Mail
			$contato = new SendEmail();
			$contato -> nomeEmail = $o_Pessoa -> getPrimeiroNomePes();
			// Nome do destinatário que vai receber o E-Mail
			$contato -> paraEmail = $o_Pessoa -> getEmailPes();
			// Email destinatário que vai receber o E-Mail

			$contato -> remetenteNome = "Savicki";
			// Nome do remetente que vai enviar o E-Mail
			$contato -> remetenteEmail = "fconato@savicki.com.br";
			// E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail

			$contato -> assuntoEmail = "Recebemos uma solicitação para redefinir a senha para sua conta";
			// Assunto da mensagem

			$contato -> conteudoEmail = $arquivo;
			// Conteudo da mensagem

			$contato -> confirmacao = 1;
			// Se for 1 exibi a mensagem de confirmação
			$contato -> mensagem = "ok";
			// Mensagem de Confirmação
			$contato -> erroMsg = "erro";
			// pode colocar uma mensagem de erro aqui!!
			$contato -> confirmacaoErro = 1;
			// Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.

			$contato -> enviar();
			// envia a mensagem
		}
	}

	public function areaClienteAction() {
		$o_view = new View('views/areaCliente.php');
		$o_view -> showContents();
	}

	public function areaTransportadorAction() {
		$o_view = new View('views/areaTransportador.php');
		$o_view -> showContents();
	}

	public function areaAdministradorAction() {
		$o_view = new View('views/areaAdministrador.php');
		$o_view -> showContents();
	}

	public function bloquearAcessoAction() {
		if (DataValidator::isNumeric($_GET['idPes'])) {
			//bloqueando Acesso
			$o_pessoa = new PessoaModel();
			$o_pessoa -> loadByIdAcesso($_GET['idPes']);
			$o_pessoa -> bloquearAcesso();

			Application::redirect('?controle=Mensalidade&acao=controlarMensalidade&variavel=bloqueado');
			exit ;
		}
	}

	public function liberarAcessoAction() {
		if (DataValidator::isNumeric($_GET['idPes'])) {
			//bloqueando Acesso
			$o_pessoa = new PessoaModel();
			$o_pessoa -> loadByIdAcesso($_GET['idPes'], null);
			$o_pessoa -> liberarAcesso();

			Application::redirect('?controle=Mensalidade&acao=controlarMensalidade&variavel=liberado');
			exit ;
		}
	}

	public function perfilTransportadorAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do cliente foi passado
		if (isset($_REQUEST['tb_pessoa_idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['tb_pessoa_idPes']))
				//buscando dados do cliente
				$o_pessoa -> loadByIdPerfil($_REQUEST['tb_pessoa_idPes']);

		$o_view = new View('views/perfilTransportador.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();

	}

	public function manterSenhaAction() {
		$o_pessoa = new PessoaModel();

		//verificando se o id do cliente foi passado
		if (isset($_REQUEST['idPes']))
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idPes']))
				//buscando dados do cliente
				$o_pessoa -> loadById($_REQUEST['idPes']);

		if (count($_POST) > 0) {

			$o_pessoa -> setSenhaPes(DataFilter::cleanString(md5($_POST['senhaPes'])));

			//salvando dados e redirecionando para a lista de clientes
			if ($o_pessoa -> saveSenha() > 0)
				$o_pessoa -> saveSenha();
			Application::redirect('?controle=Pessoa&acao=paginaLogin&variavel=senhaRedefinida&variavel=senhaAlterada');
			exit ;

		}

		$o_view = new View('views/redefinirSenha.phtml');
		$o_view -> setParams(array('o_pessoa' => $o_pessoa));
		$o_view -> showContents();
	}

	public function redefinirSenhaAction() {
		$o_view = new View('views/redefinirSenha.phtml');
		$o_view -> showContents();
	}

}
?>
