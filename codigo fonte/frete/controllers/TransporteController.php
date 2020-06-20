<?php
//incluindo classes da camada Model
require_once 'models/TransporteModel.php';
require_once 'models/ItemModel.php';
require_once 'models/CategoriaModel.php';
require_once 'models/EnderecoTransporteModel.php';
require_once 'models/ConteudoTransporteModel.php';
require_once 'models/ImagemTransporteModel.php';
require_once 'models/LanceModel.php';
require_once 'models/MensagemModel.php';
require_once 'models/AlertaMensagemModel.php';
require_once 'models/AlertaLanceModel.php';
require_once 'models/ImagemTransporteModel.php';
require_once 'models/CidadeModel.php';
include './lib/function.php';
session_start();

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - TransporteController.php
 * 

 *
 */
class TransporteController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de transportes
	*/
	public function listarTransporteAction()
	{
		$o_Transporte = new TransporteModel();
		
		$v_transportes = array();
		
		//Listando os transportes cadastrados
		//$v_transportes = $o_Transporte->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de transportes
		$o_view = new View('views/listarTransporte.phtml');
		
		//Passando os dados do transporte para a View
		$o_view->setParams(array('v_transportes' => $v_transportes));
		
		//Imprimindo código HTML
		$o_view->showContents();
        exit;
	}


	public function cadastrarLanceAction()
	{
		$o_Transporte = new TransporteModel();
		
		$v_transportes = array();
		
		//Listando os transportes cadastrados
		//$v_transportes = $o_Transporte->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de transportes
		$o_view = new View('views/cadastrarLance.phtml');
		
		//Passando os dados do transporte para a View
		$o_view->setParams(array('v_transportes' => $v_transportes));
		
		//Imprimindo código HTML
		$o_view->showContents();
        exit;
	}
	
	public function listarTransporteAtivoAction()
	{
		$o_Transporte = new TransporteModel();
		
		$v_transportes = array();
		
		$v_transportes = $o_Transporte->_listAtivo();
		
		//Listando os transportes cadastrados
		//$v_transportes = $o_Transporte->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de transportes
		$o_view = new View('views/listarTransporteAtivo.phtml');
		
		//Passando os dados do transporte para a View
		$o_view->setParams(array('v_transportes' => $v_transportes));
		
		//Imprimindo código HTML
		$o_view->showContents();
        exit;
	}
	
	public function listarTransporteIndexAction()
	{
		$o_Transporte = new TransporteModel();
		
		$v_transportes = array();
		
		//Listando os transportes cadastrados
		//$v_transportes = $o_Transporte->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de transportes
		$o_view = new View('index.php');
		
		//Passando os dados do transporte para a View
		$o_view->setParams(array('v_transportes' => $v_transportes));
		
		//Imprimindo código HTML
		$o_view->showContents();
         exit;
	}
	
	/**
	* Gerencia a  de criação
	* e edição dos transportes 
	*/
	public function manterTransporteAction()
	{
		$o_transporte = new TransporteModel();	
		//verificando se o id do transporte foi passado
		if( isset($_REQUEST['idTransp']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idTransp']) )
				//buscando dados do contato
				$o_transporte->loadById($_REQUEST['idTransp']);
			
		if(count($_POST) > 0)
		{
			/*incluir o endereço antes*/
			$o_endtransporte = new EnderecoTransporteModel();
			
			if( isset($_REQUEST['idEndTran']) )
				if( DataValidator::isNumeric($_REQUEST['idEndTran']) )
					$o_endtransporte->loadById($_REQUEST['idEndTran']);
            $transporte = new TransporteController();
			$o_endtransporte->setCepOrigemEndTran(DataFilter::cleanString($_POST['cepOrigemEndTran']));
			$o_endtransporte->setCepDestinoEndTran(DataFilter::cleanString($_POST['cepDestinoEndTran']));
			$o_endtransporte->setRuaOrigemEndTran(DataFilter::cleanString(protecao($_POST['ruaOrigemEndTran'])));
			$o_endtransporte->setRuaDestinoEndTran(DataFilter::cleanString(protecao($_POST['ruaDestinoEndTran'])));
			$o_endtransporte->setBairroOrigemEndTran(DataFilter::cleanString(protecao($_POST['bairroOrigemEndTran'])));
			$o_endtransporte->setBairroDestinoEndTran(DataFilter::cleanString(protecao($_POST['bairroDestinoEndTran'])));
			$o_endtransporte->setTb_cidadeOrigem_IdCid(DataFilter::cleanString(protecao($_POST['tb_cidadeOrigem_IdCid'])));
			$o_endtransporte->setTb_cidadeDestino_IdCid(DataFilter::cleanString(protecao($_POST['tb_cidadeDestino_IdCid'])));
			$idEndtransporte = $o_endtransporte->save();
			
			if( isset($_REQUEST['idEndTran']) )
				if( DataValidator::isNumeric($_REQUEST['idEndTran']) )
					$idEndtransporte = $_REQUEST['idEndTran'];
					
            
                    
			$o_transporte->setIdTransp(DataFilter::cleanString($_POST['idTransp']));
			$o_transporte->setDescricaoTransp(DataFilter::cleanString(protecao($_POST['descricaoTransp'])));
			$dataRetirada = date('Y-m-d', strtotime($_POST['dataRetiradaTransp']));
			//echo $_POST['dataRetiradaTransp'];
			//exit;
			$o_transporte->setDataRetiradaTransp(DataFilter::cleanString($dataRetirada));
			$horaRetirada = date('H:i', strtotime($_POST['horaRetiradaTransp']));
			$o_transporte->setHoraRetiradaTransp(DataFilter::cleanString($horaRetirada));
			//$o_transporte->setDataCadastroTransp(DataFilter::cleanString($_POST['dataCadastroTransp'])); INCLUIDA COM A DATA DO BANCO -- N()
			$o_transporte->setTb_statusTransp_idStaTransp(DataFilter::cleanString($_POST['tb_statusTransp_idStaTransp']));
			$o_transporte->setTb_categoria_idCat(DataFilter::cleanString($_POST['tb_categoria_idCat']));
			$o_transporte->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
			/*TEM QUE PEGAR O ID DA SESSION DO USUÁRIO LOGADO*/
			//$o_transporte->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
			$o_transporte->setComentarioAdiTransp(DataFilter::cleanString(protecao($_POST['comentarioAdiTransp'])));
			$o_transporte->setNumAjudantesTransp(DataFilter::cleanString($_POST['numAjudantesTransp']));
			/* pegar o id do endeço incluido */
			$o_transporte->setTb_endereco_transporte_idEndTran($idEndtransporte);
			$o_transporte->setMotivoCancelamentoTransp('');
			$o_transporte->setPrecoMaxiTransp(DataFilter::cleanString((float)str_replace(",", ".",str_replace(".", "",str_replace("R$", "", $_POST['precoMaxiTransp'])))));
			$idtransporte = $o_transporte->save();

			if( isset($_REQUEST['idTransp']) )
				if( DataValidator::isNumeric($_REQUEST['idTransp']) )
					$idtransporte = $_REQUEST['idTransp'];
					
			/*excluir os itens*/
			$o_conteudoTransportes = new ConteudoTransporteModel();
			$v_conteudoTransportes = $o_conteudoTransportes->_list(null,$idtransporte);
			foreach($v_conteudoTransportes AS $o_conteudoTransportes){
				$o_conteudoTransportes->delete();	
			}
			
			/*incluir os itens*/
			$v_arrayConteudoTransportes = array();
			if(isset($_SESSION['conteudotransporte']) && !empty($_SESSION['conteudotransporte']))
				$v_arrayConteudoTransportes = $_SESSION['conteudotransporte'];
			//echo print_l($_SESSION['conteudotransporte']);
			foreach($v_arrayConteudoTransportes AS $o_ContTransportes){

				$o_insConteudoItemqtde = str_replace(',', '.',str_replace(".", '',str_replace("un",'' ,$o_ContTransportes['itemqtde'])));
				$o_insConteudoItemAltura = str_replace(',', '.',str_replace(".", '',str_replace("m",'',$o_ContTransportes['itemAltura'])));
				$o_insConteudoItemLargura = str_replace(',', '.',str_replace(".", '',str_replace("m",'',$o_ContTransportes['itemLargura'])));
				$o_insConteudoItemComprimento = str_replace(',', '.',str_replace(".", '',str_replace("m",'',$o_ContTransportes['itemComprimento'])));
				$o_insConteudoItemPeso = str_replace(',', '.',str_replace(".", '',str_replace("Kg",'',$o_ContTransportes['itemPeso'])));
				$o_insConteudoItemvalue = $o_ContTransportes['itemvalue'];
				
				$o_insConteudoTransportes = new ConteudoTransporteModel();
				$o_insConteudoTransportes->setDescricaoItemConTran($o_ContTransportes['itemDescricao']); /*ver se é para ter um campo descrição*/ 
				$o_insConteudoTransportes->setQtdeConTran((float)$o_insConteudoItemqtde);
				$o_insConteudoTransportes->setAlturaConTran((float)$o_insConteudoItemAltura);
				$o_insConteudoTransportes->setLarguraConTran((float)$o_insConteudoItemLargura);
				$o_insConteudoTransportes->setComprimentoConTran((float)$o_insConteudoItemComprimento);
				$o_insConteudoTransportes->setPesoConTran((float)$o_insConteudoItemPeso);
				$o_insConteudoTransportes->setTb_item_idItem($o_insConteudoItemvalue);
				$o_insConteudoTransportes->setTb_transporte_idTransp($idtransporte);
				$o_insConteudoTransportes->save();
			}
			/*excluir as imagens*/
			$o_imagemTransportes = new ImagemTransporteModel();
			$v_imagemTransportes = $o_imagemTransportes->_list($idtransporte);
			foreach($v_imagemTransportes AS $o_imagemTransportes){
				$o_imagemTransportes->delete();	
				
			}
			/*incluir as fotos*/
			$v_arrayImagemTransportes = array();
			if(isset($_SESSION['imagenstransporte']) && !empty($_SESSION['imagenstransporte']))
				$v_arrayImagemTransportes = $_SESSION["imagenstransporte"];
			//for ($i=0; $i < count($v_arrayImagemTransportes); $i++){
			foreach($v_arrayImagemTransportes AS $o_imgTransportes){
				$o_insImagemCaminho =  $o_imgTransportes['caminhoImagem'];
				$o_insImagemTransportes = new ImagemTransporteModel();
				$o_insImagemTransportes->setCaminhoImgTran($o_insImagemCaminho);
				$o_insImagemTransportes->setTb_transporte_idTransp($idtransporte);
				$o_insImagemTransportes->save();
			}
			if(isset($_REQUEST['AtivacaoSemLance']) and $_REQUEST['AtivacaoSemLance'] == 'true')
			{
				if( DataValidator::isNumeric($_GET['idTransp']) )
				{
					
					
					$o_alertaMensagem = new AlertaMensagemModel();
					
					$o_alertaMensagem->delete($_GET['idTransp']);
					
					$o_alertaLance = new AlertaLanceModel();
					
					$o_alertaLance->delete($_GET['idTransp']);
					
					$o_mensagem = new MensagemModel();
					
					$o_mensagem->deleteMenLan($_GET['idTransp']); 
					//apagando o lance
					$o_lance = new LanceModel();
					//echo $_REQUEST['AtivacaoSemLance'];
					//echo $_GET['idTransp'];
					//exit;
					$o_lance->deleteLanceTransporte($_GET['idTransp']);
					
					
					
				}	
			}
			
		
			//exit;
			//salvando dados e redirecionando para a lista de transportes
			//echo $_REQUEST['tipo'];
			//exit;
			if($_REQUEST['idTransp']){
				if(isset($_REQUEST['tipo']) AND isset($_REQUEST['idTransp'])){
				                    $o_Transporte = new TransporteModel();
                $o_Transporte->loadById($idtransporte);
                
                $o_Transportadores = new PessoaModel();
                $v_Transportadores = $o_Transportadores->_listEmailTransportadores('T',$o_Transporte->getEstOrigem());
            
                /*
                ENVIO DE E-MAIL 
                NÃO ESQUECER DE COLOCAR O INCLUDE: include("lib/email.php");
                */
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
            
                 $o_imagens = new ImagemTransporteModel();
                 $o_imagens->loadByIdTop($idtransporte);
                 $o_retIMG = $o_imagens->getCaminhoImgTran();
                 $str_img = '';
                 
                 if (!empty($o_retIMG)){
                     $str_img = $o_retIMG;
                 }
                 else{
                     $str_img = 'http://www.savicki.com.br/template/images/semfoto.png';
                 }
             
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">'. $o_Transporte->getDescricaoTransp() .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endereço de Origem: '. $s_endecoOrigem .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endereço de Destino: '. $s_endecoDestino .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Data da coleta: '. date('d/m/Y', strtotime($o_Transporte->getDataRetiradaTransp())) .'</p></div>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Clique&nbsp;no bot&#227;o abaixo para mais detalhes e cadastrar um lance.</p></div>
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
                  <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" width="164">
              <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/index.php?controle=Transporte&acao=cadastrarLance&opcao=clienteativosTrans"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:164px;"
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
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #3C8DBC;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 20px;                    padding-bottom: 5px;                    padding-left: 20px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #3C8DBC;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/index.php?controle=Transporte&acao=cadastrarLance&opcao=clienteativosTrans" target="_blank">
                            <!--[if !mso]><!- - -->
                            <p style="margin: 0;font-family: inherit;font-size: 16px;line-height: 32px" data-mce-style="font-family: inherit; font-size: 16px; line-height: 32px;">Cadastrar Lance<br></p>
                            <!--<![endif]-->
                            <!--[if mso]>
                            Cadastrar Lance
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
                $contato->nomeEmail = "Savicki"; // Nome do destinatário que vai receber o E-Mail
                $contato->paraEmail = "contato@savicki.com.br"; // Email destinatário que vai receber o E-Mail
                
                $str_copia = array();
                foreach($v_Transportadores AS $o_Transportadores){
                     $str_copia[] = $o_Transportadores->getEmailPes();
                     }
                $contato->listaCopiaEmail = $str_copia;
                
                $contato->remetenteNome = "Savicki"; // Nome do remetente que vai enviar o E-Mail
                $contato->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
                
                $contato->assuntoEmail = "Novo trabalho disponível!"; // Assunto da mensagem
                
                $contato->conteudoEmail = $arquivo;// Conteudo da mensagem
                
                $contato->confirmacao = 0; // Se for 1 exibi a mensagem de confirmação
                $contato->mensagem = "ok"; // Mensagem de Confirmação       
                $contato->erroMsg = "erro";// pode colocar uma mensagem de erro aqui!!
                $contato->confirmacaoErro = 1; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
                
                echo $contato-> enviar(); // envia a mensagem
					Application::redirect('?controle=Transporte&acao=listarTransporte&opcao=clienteativos&variavel=transporteAtivado');	
                    exit;
				}
				else{
					if(isset($_REQUEST['idTransp'])){
					Application::redirect('?controle=Transporte&acao=listarTransporte&opcao=clienteativos&variavel=transporteEditado');
                        exit;
					}
				}
			}
			else{
                
                $o_Transporte = new TransporteModel();
                $o_Transporte->loadById($idtransporte);
                
                $o_Transportadores = new PessoaModel();
                $v_Transportadores = $o_Transportadores->_listEmailTransportadores('T',$o_Transporte->getEstOrigem());
            
                /*
                ENVIO DE E-MAIL 
                NÃO ESQUECER DE COLOCAR O INCLUDE: include("lib/email.php");
                */
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
            
                 $o_imagens = new ImagemTransporteModel();
                 $o_imagens->loadByIdTop($idtransporte);
                 $o_retIMG = $o_imagens->getCaminhoImgTran();
                 $str_img = '';
                 
                 if (!empty($o_retIMG)){
                     $str_img = $o_retIMG;
                 }
                 else{
                     $str_img = 'http://www.savicki.com.br/template/images/semfoto.png';
                 }
             
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">'. $o_Transporte->getDescricaoTransp() .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endereço de Origem: '. $s_endecoOrigem .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endereço de Destino: '. $s_endecoDestino .'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Data da coleta: '. date('d/m/Y', strtotime($o_Transporte->getDataRetiradaTransp())) .'</p></div>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Clique&nbsp;no bot&#227;o abaixo para mais detalhes e cadastrar um lance.</p></div>
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
                  <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top" align="center" width="164">
              <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://www.savicki.com.br/index.php?controle=Transporte&acao=cadastrarLance&opcao=clienteativosTrans"
                  style="
                    height:42px;
                    v-text-anchor:middle;
                    width:164px;"
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
                    <td style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                     -webkit-border-radius: 4px;                     -moz-border-radius: 4px;                    color: #ffffff;                    background-color: #3C8DBC;                    vertical-align: middle;                     padding-top: 5px;                     padding-right: 20px;                    padding-bottom: 5px;                    padding-left: 20px;                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;text-align: center"><!--<![endif]--><a style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #3C8DBC;color: #ffffff;line-height: 22px" href="http://www.savicki.com.br/index.php?controle=Transporte&acao=cadastrarLance&opcao=clienteativosTrans" target="_blank">
                            <!--[if !mso]><!- - -->
                            <p style="margin: 0;font-family: inherit;font-size: 16px;line-height: 32px" data-mce-style="font-family: inherit; font-size: 16px; line-height: 32px;">Cadastrar Lance<br></p>
                            <!--<![endif]-->
                            <!--[if mso]>
                            Cadastrar Lance
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
            <div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">savicki.</p></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">Travessa Percy Withers, 121, cj 91 Curitiba-PR&nbsp;CEP 80240-190</p></div><div style="font-size:12px;line-height:18px;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 18px"></div><div style="font-size:14px;line-height:21px;text-align:center;color:#959595;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;"><p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center">&nbsp;www.savicki.com.br</p></div>
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
                $contato->nomeEmail = "Savicki"; // Nome do destinatário que vai receber o E-Mail
                $contato->paraEmail = "contato@savicki.com.br"; // Email destinatário que vai receber o E-Mail
                
                $str_copia = array();
                foreach($v_Transportadores AS $o_Transportadores){
                     $str_copia[] = $o_Transportadores->getEmailPes();
                     }
                $contato->listaCopiaEmail = $str_copia;
                
                $contato->remetenteNome = "Savicki"; // Nome do remetente que vai enviar o E-Mail
                $contato->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
                
                $contato->assuntoEmail = "Novo trabalho disponível!"; // Assunto da mensagem
                
                $contato->conteudoEmail = $arquivo;// Conteudo da mensagem
                
                $contato->confirmacao = 0; // Se for 1 exibi a mensagem de confirmação
                $contato->mensagem = "ok"; // Mensagem de Confirmação       
                $contato->erroMsg = "erro";// pode colocar uma mensagem de erro aqui!!
                $contato->confirmacaoErro = 1; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
                
                echo $contato-> enviar(); // envia a mensagem
            
				     Application::redirect('?controle=Transporte&acao=listarTransporte&opcao=clienteativos&variavel=transporteCadastrado');
                     exit;
			}
			
		}
			
		$o_view = new View('views/cadastroTransporte.phtml');
		$o_view->setParams(array('o_transporte' => $o_transporte));
		$o_view->showContents();
        exit;
	}
	
	
	public function detalhesTransporteAction()
	{
		$o_transporte = new TransporteModel();	
		//verificando se o id do transporte foi passado
		if( isset($_REQUEST['idTransp']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idTransp']) )
				//buscando dados do contato
				$o_transporte->loadById($_REQUEST['idTransp']);
			
		
		$o_view = new View('views/detalhesTransporte.phtml');
		$o_view->setParams(array('o_transporte' => $o_transporte));
		$o_view->showContents();
        exit;
	}
	
	/**
	* Gerencia a requisições de exclusão dos transportes
	*/
	public function apagarTransporteAction()
	{
		if( DataValidator::isNumeric($_GET['idTransp']) )
		{
			//apagando o transporte
			$o_transporte = new TransporteModel();
			$o_transporte->loadById($_GET['idTranspn']);
			$o_transporte->delete();
			
		
			Application::redirect('?controle=Transporte&acao=listartransporte');
            exit;
		}	
	}
	
	//public function listarTransporteAtivoAction()
	//{
		//Application::redirect('?controle=Transporte&acao=listarTransporte&opcao=clienteativos');
	//}
	
	public function listarTransporteInativoAction()
	{
		Application::redirect('?controle=Transporte&acao=listarTransporte&opcao=clienteinativos');
        exit;
	}
	
	public function ativarTransporteAction()
	{
		if( DataValidator::isNumeric($_GET['idTransp']) )
		{
		
			$o_transporte = new TransporteModel();
			$o_transporte->loadById($_GET['idTranspn']);
			$o_transporte->ativar();
			
		
			Application::redirect('?controle=Transporte&acao=listarTransporteInativo');
            exit;
		}	
	}

	public function cancelarTransporteAction()
	{
		if( DataValidator::isNumeric($_GET['idTransp']) )
		{
		
			$o_transporte = new TransporteModel();
			$o_transporte->loadById($_GET['idTranspn']);
			$o_transporte->mudarStatusTransporte('X');
			
		
			Application::redirect('?controle=Transporte&acao=listarTransporte&opcao=clienteativos&variavel=transporteCancelado');
            exit;
		}	
	}
	
	public function pesquisarTransporteAction()
	{
		$o_Transporte = new TransporteModel();
		
		$v_transportes = array();
		
		//Listando os transportes cadastrados
		$v_transportes = $o_Transporte->_listAtivo();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de transportes
		$o_view = new View('views/pesquisarTransporte.phtml');
		
		//Passando os dados do transporte para a View
		$o_view->setParams(array('v_transportes' => $v_transportes));
		
		//Imprimindo código HTML
		$o_view->showContents();
         exit;
	}
	
	public function motivoCancelamentoTransporteAction()
	{
			//echo $_REQUEST['fidTransp'];
			//echo $_POST['fmotivoCancelamentoTransp'];
			//exit;
		$o_transporte = new TransporteModel();
		//verificando se o id do transporte foi passado
		if( isset($_REQUEST['fidTransp']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['fidTransp']) )
				//buscando dados do contato
				$o_transporte->loadById($_REQUEST['fidTransp']);
			
		if(count($_POST) > 0)
		{
		        $transporte = new TransporteController();
			    $o_transporte->setMotivoCancelamentoTransp(DataFilter::cleanString($transporte->protecao($_POST['fmotivoCancelamentoTransp'])));
			
			    $o_transporte->salvarMotivoCancelamento();
				
			    $o_lance = new LanceModel();
		
				$o_lance->loadById($_REQUEST['fidTransp']);
			
				
				$o_Transporte = new TransporteModel();
				$o_Transporte->loadById($_REQUEST['fidTransp']);
				
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
			
                 $o_imagens = new ImagemTransporteModel();
                 $o_imagens->loadByIdTop($_REQUEST['fidTransp']);
                 $o_retIMG = $o_imagens->getCaminhoImgTran();
                 $str_img = '';
                 
                 if (!empty($o_retIMG)){
                     $str_img = $o_retIMG;
                 }
                 else{
                     $str_img = 'http://www.savicki.com.br/template/images/semfoto.png';
                 }
			
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

                <img class="center" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 150px;max-width: 150px" align="center" border="0" data-custom-width="150" src="http://www.savicki.com.br/template/b4cc86fef13b4bbe9dd1335704e31160.png" alt="Image" title="Image">
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">O cliente cancelou o transporte pelo seguinte motivo:<br></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">'.$o_Transporte->getMotivoCancelamentoTransp().'</p></div>
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
            <div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;">'.$o_Transporte->getDescricaoTransp().'<br></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endere&#231;o de Origem:&nbsp; '.$s_endecoOrigem.'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Endere&#231;o de Destino:&nbsp;'.$s_endecoDestino.'</p></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><br data-mce-bogus="1"></div><div style="font-size:14px;line-height:17px;color:#FFFFFF;font-family:Arial, "Helvetica Neue", Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px">Data da coleta:&nbsp;'.$o_Transporte->getDataRetiradaTransp().'</p></div>
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
			$contato->nomeEmail = $o_Pessoa->getPrimeiroNomePes(); // Nome do destinatário que vai receber o E-Mail
			$contato->paraEmail = $o_Pessoa->getEmailPes(); // Email destinatário que vai receber o E-Mail
			
			$contato->remetenteNome = "Savicki";	// Nome do remetente que vai enviar o E-Mail
			$contato->remetenteEmail = "contato@savicki.com.br"; // E-mail do remetente que vai enviar (aparecer na mensagem) o E-Mail
			
			$contato->assuntoEmail = "O cliente cancelou o transporte!"; // Assunto da mensagem
			
			$contato->conteudoEmail = $arquivo;// Conteudo da mensagem
			
			$contato->confirmacao = 1; // Se for 1 exibi a mensagem de confirmação
			$contato->mensagem = "ok"; // Mensagem de Confirmação		
			$contato->erroMsg = "erro";// pode colocar uma mensagem de erro aqui!!
			$contato->confirmacaoErro = 1; // Se voce colocar 1 ele exibi o erro que ocorreu no erro se for 0 não exibi o erro uso geralmente para verificar se ta pegando.
			
			$contato->enviar(); // envia a mensagem
				
			
		}	
	}
	
	public function cadastroTransporteAction(){
		$o_view = new View('views/cadastroTransporte.phtml');
		$o_view->showContents();
         exit;
	}

	
}
?>