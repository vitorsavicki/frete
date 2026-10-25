<?php
//incluindo classes da camada Model
require_once 'models/MensagemModel.php';
require_once 'models/LanceModel.php';
require_once 'models/TransporteModel.php';
require_once 'models/PessoaModel.php';
require_once 'models/AlertaMensagemModel.php';
include './lib/function.php';
/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - MensagemController.php
 * 

 *
 */
class MensagemController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de mensagens
	*/
	public function listarMensagemCliAction()
	{
		$o_Mensagem = new MensagemModel();
		
		//Listando as mensagens cadastrados
		$v_mensagens = $o_Mensagem->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de mensagens
		$o_view = new View('views/listarMensagemCli.phtml');
		
		//Passando os dados da mensagem para a View
		$o_view->setParams(array('v_mensagens' => $v_mensagens));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	public function listarMensagemTranspAction()
	{
		$o_Mensagem = new MensagemModel();
		
		//Listando as mensagens cadastrados
		$v_mensagens = $o_Mensagem->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de mensagens
		$o_view = new View('views/listarMensagemTransp.phtml');
		
		//Passando os dados da mensagem para a View
		$o_view->setParams(array('v_mensagens' => $v_mensagens));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição das mensagens 
	*/
	public function manterMensagemAction()
	{
		$o_mensagem = new MensagemModel();
		
		//verificando se o id da mensagem foi passado
		if( isset($_REQUEST['idMen']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idMen']) )
				//buscando dados da mensagem
				$o_mensagem->loadById($_REQUEST['idMen']);
			
		if(count($_POST) > 0)
		{
			$o_mensagem->setConteudoMen(DataFilter::cleanString(protecao($_POST['fconteudoMen'])));
			$o_mensagem->setTb_pessoa_idPes(DataFilter::cleanString($_POST['fidPes']));
			$o_mensagem->setTb_lance_idLan(DataFilter::cleanString($_POST['fidLan']));
			$idMensagem = $o_mensagem->save();
			
			$o_alertaMen = new AlertaMensagemModel();
			$o_alertaMen->setTb_Mensagem_idMen($idMensagem);
			$o_alertaMen->setStatusAleMen('N');
			$o_alertaMen->save();
			
			$o_Lance = new LanceModel();
			$o_Lance->loadById($_POST['fidLan']);
			
			$o_Pessoa = new PessoaModel();
			$o_Pessoa->loadById($_POST['fidPes']);
			
			$o_Transportador = new PessoaModel();
			$o_Transportador->loadById($_POST['fidPes']);
			
			$o_Transporte = new TransporteModel();
			$o_Transporte->loadById($o_Lance->getTb_transporte_idTransp());
			
			$o_PessoaPerfil = new PessoaModel();
			$o_PessoaPerfil->loadByIdPerfil($o_Lance->getTb_pessoa_idPes());
			
			$o_PerfilPessoa = new PessoaModel();
			$o_PerfilPessoa->loadById($_POST['fidPes']);
			
			if($o_PerfilPessoa->getCodigoPer() == 'T'){
				
			$o_Pessoa = new PessoaModel();
			$o_Pessoa->loadById($o_Transporte->getTb_pessoa_idPes());
			
			$o_Transportador = new PessoaModel();
			$o_Transportador->loadById($_POST['fidPes']);
			
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">'. $o_Transporte->getDescricaoTransp() .'</span></div>
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
                                        <table class="block-grid two-up" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="250"><![endif]--><div class="col num6" style="display: inline-block;vertical-align: top;width: 250px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;width: 100%" align="center">
            <div align="center">

                <img class="center fullwidth" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 100% !important;max-width: 250px" align="center" border="0" data-custom-width="250" src="http://www.savicki.com.br/'. $o_Transportador->getFotoPes() .  '" alt="Image" title="Image">
            </div>
        </td>
    </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]--> <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="250"><![endif]--><div class="col num6" style="display: inline-block;vertical-align: top;width: 250px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#FFFFFF;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">'. $o_Transportador->getPrimeiroNomePes() .'<br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left">Trabalhos &nbsp;Efetuados: '. $o_PessoaPerfil->getQtdeTransportes() .'</p></div><div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left">Lances Ofertados: '. $o_PessoaPerfil->getQtdeLances() .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">&nbsp;</p></div>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">Valor do lance: R$ 100.00</span></div>
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
            <div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left">Mensagem:</p></div>
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
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: #FFFFFF" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
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
        <div style="color:#0000FF;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;color:#0000FF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">'. $o_mensagem->getConteudoMen() .'<br></div>
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
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr style="vertical-align: top">
    <td class="button-container" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px" align="center">
      <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">

        <tbody><tr style="vertical-align: top">
          <td class="button" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;height: 22px;text-align: center" width="100%" align="center" valign="middle">
             <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr style="vertical-align: top">
                  <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" width="124">
              <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/index.php?controle=Mensagem&amp;acao=listarMensagemCli&amp;opcao=Mensagens"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:124px;"
                  arcsize="10%" 
                  strokecolor="#3AAEE0"
                  fillcolor="#3AAEE0" >
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
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #3AAEE0;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 20px;                    padding-bottom: 5px;                    padding-left: 20px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #3AAEE0;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/index.php?controle=Mensagem&acao=listarMensagemCli&opcao=Mensagens" target="_blank">
                            <!--[if !mso]><!- - -->
                            <p style="margin: 0;font-family: inherit;font-size: 16px;line-height: 32px" data-mce-style="font-family: inherit; font-size: 16px; line-height: 32px;">Responder<br></p>
                            <!--<![endif]-->
                            <!--[if mso]>
                            Responder
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
						
						
			
			
				$contato = new SendEmail();
				$contato->nomeEmail = $o_Pessoa->getPrimeiroNomePes(); // Nome do destinatário que vai receber o E-Mail
				$contato->paraEmail = $o_Pessoa->getEmailPes(); // Email destinatário que vai receber o E-Mail
				
				//$contato->copiaEmail = $o_Transportador->getEmailPes();
				//$contato->copiaNome = $o_Transportador->getPrimeiroNomePes();
				
				$contato->remetenteNome = "Savicki";	// Nome do remetente que vai enviar o E-Mail
				$contato->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
				
				$contato->assuntoEmail = "Você recebeu uma nova mensagem!"; // Assunto da mensagem
				
				$contato->conteudoEmail = $arquivo;// Conteudo da mensagem
				
				$contato->confirmacao = 0; // Se for 1 exibi a mensagem de confirmação
				$contato->mensagem = "erro"; // Mensagem de Confirmação		
				$contato->erroMsg = "ok";// pode colocar uma mensagem de erro aqui!!
				$contato->confirmacaoErro = 1; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
				
				$contato->enviar(); // envia a mensagem
				}
				
				if($o_PerfilPessoa->getCodigoPer() == 'U'){
					
					$o_Pessoa = new PessoaModel();
					$o_Pessoa->loadById($_POST['fidPes']);
			
					$o_Transportador = new PessoaModel();
					$o_Transportador->loadById($o_Lance->getTb_pessoa_idPes());
					
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
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;width: 100%;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px" align="center">
            <div align="center">

                <img class="center" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 150px;max-width: 150px" align="center" border="0" data-custom-width="150" src="images/b4cc86fef13b4bbe9dd1335704e31160.png" alt="Image" title="Image">
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;" mce-data-marked="1">'.$o_Transporte->getDescricaoTransp().'</span></div>
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
                                        <table class="block-grid two-up" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #333;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="250"><![endif]--><div class="col num6" style="display: inline-block;vertical-align: top;width: 250px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;width: 100%" align="center">
            <div align="center">

                <img class="center fullwidth" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 100% !important;max-width: 250px" align="center" border="0" data-custom-width="250" src=http://www.savicki.com.br/"'.$o_Pessoa->getFotoPes().'" alt="Image" title="Image">
            </div>
        </td>
    </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]--> <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="250"><![endif]--><div class="col num6" style="display: inline-block;vertical-align: top;width: 250px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#FFFFFF;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">'.$o_Pessoa->getPrimeiroNomePes().'<br></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">&nbsp;</p></div>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">Valor do meu lance: R$ '. number_format($o_Lance->getValorlan(), 2, ',', '.') .'</span></div>
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
            <div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left">Mensagem:</p></div>
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
                                        <table class="block-grid" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: #FFFFFF" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
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
        <div style="color:#0000FF;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;color:#0000FF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">' . $o_mensagem->getConteudoMen() .'<br></div>
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
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr style="vertical-align: top">
    <td class="button-container" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px" align="center">
      <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">

        <tbody><tr style="vertical-align: top">
          <td class="button" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;height: 22px;text-align: center" width="100%" align="center" valign="middle">
             <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr style="vertical-align: top">
                  <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" width="124">
              <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/index.php?controle=Mensagem&amp;acao=listarMensagemTransp&amp;opcao=Mensagens"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:124px;"
                  arcsize="10%" 
                  strokecolor="#3AAEE0"
                  fillcolor="#3AAEE0" >
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
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #3AAEE0;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 20px;                    padding-bottom: 5px;                    padding-left: 20px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #3AAEE0;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/index.php?controle=Mensagem&acao=listarMensagemTransp&opcao=Mensagens" target="_blank">
                            <!--[if !mso]><!- - -->
                            <p style="margin: 0;font-family: inherit;font-size: 16px;line-height: 32px" data-mce-style="font-family: inherit; font-size: 16px; line-height: 32px;">Responder<br></p>
                            <!--<![endif]-->
                            <!--[if mso]>
                            Responder
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
						
						
			
			
				$contato = new SendEmail();
				//$contato->nomeEmail = $o_Pessoa->getPrimeiroNomePes(); // Nome do destinatário que vai receber o E-Mail
				//$contato->paraEmail = $o_Pessoa->getEmailPes(); // Email destinatário que vai receber o E-Mail
				
				$contato->nomeEmail = $o_Transportador->getPrimeiroNomePes(); // Nome do destinatário que vai receber o E-Mail
				$contato->paraEmail = $o_Transportador->getEmailPes(); // Email destinatário que vai receber o E-Mail
				
				//$contato->copiaEmail = $o_Transportador->getEmailPes();
				//$contato->copiaNome = $o_Transportador->getPrimeiroNomePes();
				
				$contato->remetenteNome = "Savicki";	// Nome do remetente que vai enviar o E-Mail
				$contato->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
				
				$contato->assuntoEmail = "Você recebeu uma nova mensagem!"; // Assunto da mensagem
				
				$contato->conteudoEmail = $arquivo;// Conteudo da mensagem
				
				$contato->confirmacao = 0; // Se for 1 exibi a mensagem de confirmação
				$contato->mensagem = "erro"; // Mensagem de Confirmação		
				$contato->erroMsg = "ok";// pode colocar uma mensagem de erro aqui!!
				$contato->confirmacaoErro = 1; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
				
				$contato->enviar(); // envia a mensagem
				}

		}
			
		
	}
	
	/**
	* Gerencia a requisições de exclusão dos contatos
	*/
	public function apagarMensagemAction()
	{
		if( DataValidator::isNumeric($_GET['idMen']) )
		{
			//apagando o mensagem
			$o_mensagem= new MensagemModel();
			$o_mensagem->loadById($_GET['idMen']);
			$o_mensagem->delete();
			
		
			Application::redirect('?controle=Mensagem&acao=listarMensagem');
            exit;
		}	
	}

}
?>