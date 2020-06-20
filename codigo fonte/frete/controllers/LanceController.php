<?php
//incluindo classes da camada Model
require_once 'models/LanceModel.php';
require_once 'models/TransporteModel.php';
require_once 'models/PessoaModel.php';
require_once 'models/EnderecoTransporteModel.php';
require_once 'models/MensagemModel.php';
require_once 'models/AlertaLanceModel.php';
require_once 'models/AlertaMensagemModel.php';
require_once 'models/StatusTransporteModel.php';
include './lib/function.php';
session_start();

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - LanceController.php
 * 

 *
 */
class LanceController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de lances
	*/
	public function listarLanceAction()
	{
		$o_lance = new LanceModel();
		$idTransp = 0;
		if(isset($_REQUEST['tb_transporte_idTransp']))
		{
			$idTransp = $_REQUEST['tb_transporte_idTransp'];
		}
		elseif (isset($_REQUEST['idLan'])) 
		{
			$idTransp = $_REQUEST['idLan'];
		}
		
		//Listando os lances cadastrados
		$v_lances = $o_lance->_list($idTransp);
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/verLance.phtml');
		
		//Passando os dados do contato para a View
		$o_view->setParams(array('v_lances' => $v_lances));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de lances
	*/
	public function meusLancesAction()
	{
		$o_lance = new LanceModel();
		$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
		if(isset($_REQUEST['tb_pessoa_idPes']))
		{
			$idPes = $_REQUEST['tb_pessoa_idPes'];
		}
		
		//Listando os lances cadastrados
		$v_lances = $o_lance->_list(null, $idPes);
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/meusLances.phtml');
		
		//Passando os dados do contato para a View
		$o_view->setParams(array('v_lances' => $v_lances));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	/**
	* Gerencia a  de criação
	* e edição dos lances 
	*/
	public function manterLanceAction()
	{
		$o_lance = new LanceModel();
		
		//verificando se o id do lance foi passado
		if( isset($_REQUEST['idLan']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idLan']) )
				//buscando dados do lance
				$o_lance->loadById($_REQUEST['idLan']);
			
		if(count($_POST) > 0)
		{
			$o_lance->setValorLan(DataFilter::cleanString($_POST['valorLan']));
			$o_lance->setDataLan(DataFilter::cleanString($_POST['dataLan']));
			$o_lance->setTb_transporte_idTransp(DataFilter::cleanString($_POST['tb_transporte_idTransp']));
			$o_lance->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
			
			//salvando dados e redirecionando para a lista de lances
			if($o_lance->save() > 0)
				Application::redirect('?controle=Lance&acao=listarLance');
                exit;
		}
			
		$o_view = new View('views/manterLance.phtml');
		$o_view->setParams(array('o_lance' => $o_lance));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos lances
	*/
	public function apagarLanceAction()
	{
		if( DataValidator::isNumeric($_GET['idLan']) )
		{
			//apagando o lance
			$o_lance = new LanceModel();
			$o_lance->loadById($_GET['idLan']);
			$o_lance->delete();
			
			Application::redirect('?controle=Lance&acao=listarLance');
            exit;
		}	
	}
	

	
	public function cadastrarLanceAction()
	{
		$o_lance = new LanceModel();
		
		//verificando se o id do lance foi passado
		if( isset($_REQUEST['idLan']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idLan']) )
				//buscando dados do lance
				$o_lance->loadById($_REQUEST['idLan']);
			
		if(count($_POST) > 0)
		{
			$o_lance->setValorLan(DataFilter::cleanString((float)str_replace(",", ".",str_replace(".", "",str_replace("R$", "", $_POST['fValorLan'])))));
			$o_lance->setTb_transporte_idTransp(DataFilter::cleanString($_POST['fTb_transporte_idTransp']));
			$o_lance->setTb_pessoa_idPes(DataFilter::cleanString($_POST['fTb_pessoa_idPes']));
			$o_lance->setVencedorLan('A');
			//salvando dados de lances
	
			$idLance = $o_lance->save();
			
			$o_Lance = new LanceModel();
			$o_Lance->loadById($idLance);
			
			if( isset($_POST['fconteudoMen']) AND $_POST['fconteudoMen'] <> "" ){
			$o_mensagem = new MensagemModel();
			$o_mensagem->setConteudoMen(DataFilter::cleanString(protecao($_POST['fconteudoMen'])));
			$o_mensagem->setTb_pessoa_idPes(DataFilter::cleanString($_POST['fTb_pessoa_idPes']));
			$o_mensagem->setTb_lance_idLan(DataFilter::cleanString($idLance));
			$idMensagem = $o_mensagem->save();
			$o_alertaMen = new AlertaMensagemModel();
			$o_alertaMen->setTb_Mensagem_idMen($idMensagem);
			$o_alertaMen->setStatusAleMen('N');
			$o_alertaMen->save();
			}
			
			$o_alertaLan = new AlertaLanceModel();
			$o_alertaLan->setTb_Lance_idLan($idLance);
			$o_alertaLan->setStatusAleLan('N');
			$o_alertaLan->save();
			
			
			
			$o_Transporte = new TransporteModel();
			$o_Transporte->loadById($o_Lance->getTb_Transporte_idTransp());
                 $o_imagens = new ImagemTransporteModel();
                 $o_imagens->loadByIdTop($o_Transporte->getIdTransp());
                 $o_retIMG = $o_imagens->getCaminhoImgTran();
                 $str_img = '';
                 
                 if (!empty($o_retIMG)){
                     $str_img = $o_retIMG;
                 }
                 else{
                     $str_img = 'http://www.savicki.com.br/template/images/semfoto.png';
                 }

			$o_Pessoa = new PessoaModel();
			$o_Pessoa->loadById($o_Transporte->getTb_pessoa_idPes());
			
			$o_Transportador = new PessoaModel();
			$o_Transportador->loadByIdPerfil($o_Lance->getTb_pessoa_idPes());
			$o_categoria = new CategoriaModel();
				$o_categoria->loadById($o_Transporte->getTb_categoria_idCat());
				
				$o_enderecoTransporte = new EnderecoTransporteModel();
				$o_cidadeOrigem = new CidadeModel();
				$o_cidadeDestino = new CidadeModel();
				$o_enderecoTransporte->loadById($o_Transporte->getTb_endereco_transporte_idEndTran());
				$o_cidadeOrigem->loadById($o_enderecoTransporte->getTb_cidadeOrigem_IdCid());
				$o_cidadeDestino->loadById($o_enderecoTransporte->getTb_cidadeDestino_IdCid());
				
				$o_estado = new EstadoModel();
				$o_estado->loadById($o_cidadeOrigem->getTb_Estado_idEst());
				$s_endecoOrigem = $o_enderecoTransporte->getRuaOrigemEndTran() . ' - ' .$o_enderecoTransporte->getBairroOrigemEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeOrigem->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst()) . ' - ' . 'CEP: ' . $o_enderecoTransporte->getCepOrigemEndTran();
			
				$s_endecoOrigemGoogle = $o_enderecoTransporte->getRuaOrigemEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeOrigem->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst());
			
				$o_estado->loadById($o_cidadeDestino->getTb_Estado_idEst());
				$s_endecoDestino = $o_enderecoTransporte->getRuaDestinoEndTran() . ' - ' .$o_enderecoTransporte->getBairroDestinoEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeDestino->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst()) . ' - ' . 'CEP: ' . $o_enderecoTransporte->getCepDestinoEndTran();
			
				$s_endecoDestinoGoogle = $o_enderecoTransporte->getRuaDestinoEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeDestino->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst());
			/*
			ENVIO DE E-MAIL 
			NÃO ESQUECER DE COLOCAR O INCLUDE: include("lib/email.php");
			*/
			$historico = "";
			                                                                $o_transLance = new PessoaModel();
                                                                        	$listaLance = $o_Lance->_listEmail($_POST['fTb_transporte_idTransp'],null);
                                                                        	foreach ($listaLance as $lance){
                                                                        	    $o_transLance->loadById($lance->getTb_pessoa_idPes());
                                                                                $historico .= '<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #18BC9C" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
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
                                        <table class="block-grid three-up" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: #FFFFFF" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="167"><![endif]--><div class="col num4" style="display: inline-block;vertical-align: top;width: 166px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#3AAEE0;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;color:#3AAEE0;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">'.$o_transLance->getPrimeiroNomePes().'</p></div>
        </div>
    </td>
  </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]--> <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="167"<![endif]--><div class="col num4" style="display: inline-block;vertical-align: top;width: 166px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#3AAEE0;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;color:#3AAEE0;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">'.date('d-m-Y', strtotime($lance->getdataLan())).'</p></div>
        </div>
    </td>
  </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]--> <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="167"><![endif]--><div class="col num4" style="display: inline-block;vertical-align: top;width: 166px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
        <div style="color:#3AAEE0;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;color:#3AAEE0;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">R$ ' . number_format($lance->getValorLan(), 2, ',', '.').'<br></div>
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
                </tbody></table>';
                                                                                            
                                                                                        
                                                                                                
                                                                                    
																			}
			
			$arquivo =' <!DOCTYPE html "-//w3c//dtd xhtml 1.0 transitional //en" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">'.$o_Transporte->getDescricaoTransp().'</span><br></div>
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
                                        <table class="block-grid three-up" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 500px;color: #000000;background-color: transparent" cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                            <tbody><tr style="vertical-align: top">
                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                    <![endif]-->
 <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="167"><![endif]--><div class="col num4" style="display: inline-block;vertical-align: top;width: 166px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody><tr style="vertical-align: top">
        <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;width: 100%;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px" align="center">
            <div align="center">

                <img class="center fullwidth" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 100% !important;max-width: 166.666666666667px" align="center" border="0" data-custom-width="166.666666666667" src="http://www.savicki.com.br/'.$o_Transportador->getFotoPes().'" alt="Image" title="Image">
                '. $o_Transportador->getPrimeiroNomePes() .'
            </div>
        </td>
    </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]--> <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="167"><![endif]--><div class="col num4" style="display: inline-block;vertical-align: top;width: 166px;text-align: center">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                                                            <tbody><tr style="vertical-align: top">
                                                                <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr style="vertical-align: top">
    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px">
        <div style="color:#FFFFFF;line-height:120%;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">            
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">Lance: R$   '. number_format($o_Lance->getValorlan(), 2, ',', '.') .'<br></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">&nbsp;</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Trabalhos Efetuados: '.$o_Transportador->getQtdeTransportes().'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Lances Ofertados: '.$o_Transportador->getQtdeLances().'</p></div>
        </div>
    </td>
  </tr>
</tbody></table>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                    </div><!--[if (gte mso 9)|(IE)]> </td><![endif]--> <!--[if (gte mso 9)|(IE)]><td class="" valign="top" width="167"><![endif]--><div class="col num4" style="display: inline-block;vertical-align: top;width: 166px;text-align: center">
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
                  <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" width="138">
              <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/?controle=Lance&amp;acao=ListarLance&amp;tb_transporte_idTransp=13188&amp;opcao=clienteativos"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:138px;"
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
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #3AAEE0;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 30px;                    padding-bottom: 5px;                    padding-left: 30px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #3AAEE0;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/?controle=Lance&acao=ListarLance&tb_transporte_idTransp=13188&opcao=clienteativos" target="_blank">
                            <!--[if !mso]><!- - -->
                            <p style="margin: 0;font-family: inherit;font-size: 16px;line-height: 32px" data-mce-style="font-family: inherit; font-size: 16px; line-height: 32px;">Ver Lance<br></p>
                            <!--<![endif]-->
                            <!--[if mso]>
                            Ver Lance
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
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/?controle=Mensagem&amp;acao=listarMensagemCli&amp;opcao=Mensagens"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:124px;"
                  arcsize="10%" 
                  strokecolor="#E0C83A"
                  fillcolor="#E0C83A" >
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
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #E0C83A;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 20px;                    padding-bottom: 5px;                    padding-left: 20px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #E0C83A;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/?controle=Mensagem&acao=listarMensagemCli&opcao=Mensagens" target="_blank">
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">Hist&#243;rico de Lances</span></div>
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
  <tbody><div class="CSSTableGenerator" >
     
                                                          '.$historico.'
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
                  <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" width="114">
              <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/?controle=Lance&amp;acao=ListarLance&amp;tb_transporte_idTransp=13188&amp;opcao=clienteativos"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:114px;"
                  arcsize="10%" 
                  strokecolor="#008D4C"
                  fillcolor="#008D4C" >
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
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #008D4C;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 20px;                    padding-bottom: 5px;                    padding-left: 20px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #008D4C;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/?controle=Lance&acao=ListarLance&tb_transporte_idTransp=13188&opcao=clienteativos" target="_blank">
                            <!--[if !mso]><!- - -->
                            <p style="margin: 0;font-family: inherit;font-size: 16px;line-height: 32px" data-mce-style="font-family: inherit; font-size: 16px; line-height: 32px;">Ver todos</p>
                            <!--<![endif]-->
                            <!--[if mso]>
                            Ver todos
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
						
						
			// Envia o E-Mail
			$contato = new SendEmail();
			$contato->nomeEmail = $o_Pessoa->getPrimeiroNomePes(); // Nome do destinatário que vai receber o E-Mail
			$contato->paraEmail = $o_Pessoa->getEmailPes(); // Email destinatário que vai receber o E-Mail
			
			//$contato->copiaEmail = $o_Transportador->getEmailPes();
			//$contato->copiaNome  = $o_Transportador->getPrimeiroNomePes();
			
			$contato->remetenteNome = "Frete";	// Nome do remetente que vai enviar o E-Mail
			$contato->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
			
			$contato->assuntoEmail = "Você recebeu um lance!"; // Assunto da mensagem
			
			$contato->conteudoEmail = $arquivo;// Conteudo da mensagem
			
			$contato->confirmacao = 0; // Se for 1 exibi a mensagem de confirmação
			$contato->mensagem = ""; // Mensagem de Confirmação		
			$contato->erroMsg = "";// pode colocar uma mensagem de erro aqui!!
			$contato->confirmacaoErro = 0; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
			
			$contato->enviar(); // envia a mensagem
		}
			
		
	}

	public function aceitarLanceAction()
	{
		$o_lance = new LanceModel();
		
		//verificando se o id do lance foi passado
		if( isset($_REQUEST['idLan']) )
		{
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idLan']) )
			{
				//buscando dados do lance
				$o_lance->loadById($_REQUEST['idLan']);
			
			
				$o_lance->aceitar();
				
				

				$o_Transporte = new TransporteModel();
				$o_Transporte->loadById($o_lance->getTb_Transporte_idTransp());
				
				$o_Transporte->mudarStatusTransporte('E');
                $o_imagens = new ImagemTransporteModel();
                $o_imagens->loadByIdTop($o_Transporte->getIdTransp());
                $o_retIMG = $o_imagens->getCaminhoImgTran();
                $str_img = '';
                 
                 if (!empty($o_retIMG)){
                     $str_img = $o_retIMG;
                 }
                 else{
                     $str_img = 'http://www.savicki.com.br/template/images/semfoto.png';
                 }
	
				$o_Pessoa = new PessoaModel();
				$o_Pessoa->loadById($o_Transporte->getTb_pessoa_idPes());

				$o_Transportador = new PessoaModel();
				$o_Transportador->loadById($o_lance->getTb_pessoa_idPes());
				$o_categoria = new CategoriaModel();
				$o_categoria->loadById($o_Transporte->getTb_categoria_idCat());
				
				$o_enderecoTransporte = new EnderecoTransporteModel();
				$o_cidadeOrigem = new CidadeModel();
				$o_cidadeDestino = new CidadeModel();
				$o_enderecoTransporte->loadById($o_Transporte->getTb_endereco_transporte_idEndTran());
				$o_cidadeOrigem->loadById($o_enderecoTransporte->getTb_cidadeOrigem_IdCid());
				$o_cidadeDestino->loadById($o_enderecoTransporte->getTb_cidadeDestino_IdCid());
				
				$o_estado = new EstadoModel();
				$o_estado->loadById($o_cidadeOrigem->getTb_Estado_idEst());
				$s_endecoOrigem = $o_enderecoTransporte->getRuaOrigemEndTran() . ' - ' .$o_enderecoTransporte->getBairroOrigemEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeOrigem->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst()) . ' - ' . 'CEP: ' . $o_enderecoTransporte->getCepOrigemEndTran();
			
				$s_endecoOrigemGoogle = $o_enderecoTransporte->getRuaOrigemEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeOrigem->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst());
			
				$o_estado->loadById($o_cidadeDestino->getTb_Estado_idEst());
				$s_endecoDestino = $o_enderecoTransporte->getRuaDestinoEndTran() . ' - ' .$o_enderecoTransporte->getBairroDestinoEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeDestino->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst()) . ' - ' . 'CEP: ' . $o_enderecoTransporte->getCepDestinoEndTran();
			
				$s_endecoDestinoGoogle = $o_enderecoTransporte->getRuaDestinoEndTran() . ' - ' . iconv('ISO-8859-1', 'UTF-8',$o_cidadeDestino->getNomeCid()).' - '.iconv('ISO-8859-1', 'UTF-8',$o_estado->getNomeEst());

				$arquivo = '
							<!DOCTYPE html "-//w3c//dtd xhtml 1.0 transitional //en" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;" mce-data-marked="1">Parab&#233;ns seu lance foi escolhido.</span></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;" mce-data-marked="1"><br data-mce-bogus="1"></span></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;" mce-data-marked="1">Lance Vencedor : R$ ' . number_format($o_lance->getValorlan(), 2, ',', '.') .'</span></div>
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

                <img class="center fullwidth" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 100% !important;max-width: 250px" align="center" border="0" data-custom-width="250" src="http://www.savicki.com.br/'.$str_img.'" alt="Image" title="Image">
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">'.$o_Transporte->getDescricaoTransp().'<br></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endere&#231;o de Origem: '.$s_endecoOrigem.'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endere&#231;o de Destino: '.$s_endecoDestino.'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Data de coleta: '.date('d/m/Y', strtotime($o_Transporte->getDataRetiradaTransp())) .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br></div>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;" mce-data-marked="1">Entre em contato com o cliente para concluir o processo.</span></div>
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

                <img class="center fullwidth" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 100% !important;max-width: 250px" align="center" border="0" data-custom-width="250" src="http://www.savicki.com.br/'.$o_Pessoa->getFotoPes().'" alt="Image" title="Image">
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
            <div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:14px; line-height:17px;">Nome: '.$o_Pessoa->getPrimeiroNomePes().'</span><br></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:14px; line-height:17px;"><br data-mce-bogus="1"></span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:14px; line-height:17px;">Telefones: <br>'.$o_Pessoa->getTelefoneFixoPes() .'<br>'.$o_Pessoa->getTelefoneCelularPes().'</span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:14px; line-height:17px;"><br data-mce-bogus="1"></span></div><div style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:14px; line-height:17px;">Email: '.$o_Pessoa->getEmailPes().'</span></div>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><span style="font-size:18px; line-height:22px;">Obrigado por utilizar a Savicki.</span><br></div>
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
							
				// Envia o E-Mail Vencedor
				$contato = new SendEmail();
				$contato->nomeEmail = $o_Transportador->getPrimeiroNomePes(); // Nome do destinatário que vai receber o E-Mail
				$contato->paraEmail = $o_Transportador->getEmailPes();// Email destinatário que vai receber o E-Mail
				
				$contato->remetenteNome = "Savicki";	// Nome do remetente que vai enviar o E-Mail
				$contato->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
				
				$contato->assuntoEmail = "Lance Aceito!"; // Assunto da mensagem
				
				$contato->conteudoEmail = $arquivo;// Conteudo da mensagem
				
				$contato->confirmacao = 0; // Se for 1 exibi a mensagem de confirmação
				$contato->mensagem = "erro"; // Mensagem de Confirmação		
				$contato->erroMsg = "ok";// pode colocar uma mensagem de erro aqui!!
				$contato->confirmacaoErro = 1; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
				
				$contato->enviar(); // envia a mensagem
				
				$o_lance->mudarStatusLance($o_lance->getTb_Transporte_idTransp(), $o_lance->getIdLan());
				
				$o_Transportadores = new PessoaModel();
				$v_Transportadores = $o_Transportadores->_listTransportadorLance($o_lance->getTb_Transporte_idTransp(),null);
				
				$arquivoLances = '<!DOCTYPE html "-//w3c//dtd xhtml 1.0 transitional //en" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
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
            <div style="font-size:14px;line-height:17px;text-align:center;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;">Infelizmente seu lance n&#227;o foi escolhido para esse transporte.</span></div><div style="font-size:14px;line-height:17px;text-align:center;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;"><br data-mce-bogus="1"></span></div>
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

                <img class="center fullwidth" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 100% !important;max-width: 250px" align="center" border="0" data-custom-width="250" src="http://www.savicki.com.br/'.$str_img .'" alt="Image" title="Image">
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
            <div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;">'.$o_Transporte->getDescricaoTransp().'<br></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">&nbsp;<br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left">Endere&#231;o de Origem:&nbsp;'.$s_endecoOrigem.'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">&nbsp;<br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left">Endere&#231;o de Destino:&nbsp;'.$s_endecoDestino.'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">&nbsp;<br></div><div style="font-size:14px;line-height:17px;text-align:left;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left">Data de coleta: '.$o_Transporte->getDataRetiradaTransp().'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br></div>
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
            <div style="font-size:14px;line-height:17px;text-align:center;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><span style="font-size:18px; line-height:22px;">Obrigado por utilizar a SSavicki.</span><br></div>
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
							
				//Envia o E-Mail Não Escolhidos
				$contatoLances = new SendEmail();
				$contatoLances->nomeEmail = "Savicki"; // Nome do destinatário que vai receber o E-Mail
				$contatoLances->paraEmail = "conato@savicki.com.br"; // Email destinatário que vai receber o E-Mail
				
				$str_copia = array();
				foreach($v_Transportadores AS $o_Transportadores){
					 $str_copia[] = $o_Transportadores->getEmailPes();
					 }
				$contatoLances->listaCopiaEmail = $str_copia;
				
				$contatoLances->remetenteNome = "Savicki";	// Nome do remetente que vai enviar o E-Mail
				$contatoLances->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
				
				$contatoLances->assuntoEmail = "Lance não Aceito!"; // Assunto da mensagem
				
				$contatoLances->conteudoEmail = $arquivoLances;// Conteudo da mensagem
				
				$contatoLances->confirmacao = 0; // Se for 1 exibi a mensagem de confirmação
				$contatoLances->mensagem = "ok"; // Mensagem de Confirmação		
				$contatoLances->erroMsg = "erro";// pode colocar uma mensagem de erro aqui!!
				$contatoLances->confirmacaoErro = 1; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
				
				$contatoLances-> enviar(); // envia a mensagem
				
							
				
				
				Application::redirect('./?controle=Transporte&acao=listarTransporte&opcao=transpandamento&variavel=emailEnviado');
                exit;
			}
		}
			
	}


}
?>