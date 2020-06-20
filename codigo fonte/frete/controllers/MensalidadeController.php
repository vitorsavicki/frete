/<?php
//incluindo classes da camada Model
require_once 'models/MensalidadeModel.php';
require_once 'models/BoletoModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - MensalidadeController.php
 * 

 *
 */
class MensalidadeController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de mensalidades
	*/
	public function listarMensalidadeClienteAction()
	{
		$o_Mensalidade = new MensalidadeModel();
		
		if (!isset($_SESSION)) session_start();
		$idPes = $_SESSION['idPes'];
		//Listando os mensalidades cadastrados
		$v_mensalidades = $o_Mensalidade->_list($idPes);
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de mensalidades
		$o_view = new View('views/historicoFinanceiro.phtml');
		
		//Passando os dados da mmenalidade para a View
		$o_view->setParams(array('v_mensalidades' => $v_mensalidades));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}

	public function listarMensalidadeAction()
	{
		$o_Mensalidade = new MensalidadeModel();
		
		//Listando os mensalidades cadastrados
		$v_mensalidades = $o_Mensalidade->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de mensalidades
		$o_view = new View('views/listarMensalidade.phtml');
		
		//Passando os dados da mmenalidade para a View
		$o_view->setParams(array('v_mensalidades' => $v_mensalidades));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	public function controlarMensalidadeAction()
	{
		$o_Mensalidade = new MensalidadeModel();
		
		//Listando os mensalidades cadastrados
		$v_mensalidades = $o_Mensalidade->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de mensalidades
		$o_view = new View('views/controlarMensalidade.phtml');
		
		//Passando os dados da mmenalidade para a View
		$o_view->setParams(array('v_mensalidades' => $v_mensalidades));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	/**
	* Gerencia a  de criação
	* e edição das mensalidades
	*/
	public function manterMensalidadeAction()
	{
		$o_mensalidade = new MensalidadeModel();
		
		//verificando se o id da mensalidade foi passado
		if( isset($_REQUEST['idMensa']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idMensa']) )
				//buscando dados do contato
				$o_mensalidade->loadById($_REQUEST['idMensa']);
			
		if(count($_POST) > 0)
		{
			
			if (isset($_POST['dataVencimentoMensa']) != NULL){	
				$originalDateVen = $_POST['dataVencimentoMensa'];
				$dataVen = date("Y-m-d", strtotime($originalDateVen));
				$o_mensalidade->setDataVencimentoMensa($dataVen);
			}
			if (isset($_POST['dataPagamentoMensa']) != NULL){
				$originalDatePag = $_POST['dataPagamentoMensa'];
				$dataPag = date("Y-m-d", strtotime($originalDatePag));
				$o_mensalidade->setDataPagamentoMensa($dataPag);
			}
			$o_mensalidade->setValorMensa(DataFilter::cleanString((float)str_replace(",", ".",str_replace(".", "",str_replace("R$", "", $_POST['valorMensa'])))));
			$o_mensalidade->setTb_situacaoMensalidade_idSit(DataFilter::cleanString($_POST['tb_situacaoMensalidade_idSit']));
			//echo $_POST['tb_situacaoMensalidade_idSit'];
			//exit;
			$o_mensalidade->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
			//salvando dados e redirecionando para a lista de mensalidades
			if($o_mensalidade->save() > 0)
				$o_mensalidade->save();
				Application::redirect('?controle=Mensalidade&acao=controlarMensalidade&variavel=baixa');
                exit;
		}
			
		$o_view = new View('views/manterMensalidade.phtml');
		$o_view->setParams(array('o_mensalidade' => $o_mensalidade));
		$o_view->showContents();
	}

	public function gerarMensalidadeAction()
	{
		$o_mensalidade = new MensalidadeModel();
		
		//verificando se o id da mensalidade foi passado
		if( isset($_REQUEST['idMensa']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idMensa']) )
				//buscando dados do contato
				$o_mensalidade->loadById($_REQUEST['idMensa']);
			
		if(count($_POST) > 0)
		{
			
			if (isset($_POST['dataVencimentoMensa']) != NULL){	
				$originalDateVen = $_POST['dataVencimentoMensa'];
				$dataVen = date("Y-m-d", strtotime($originalDateVen));
				$o_mensalidade->setDataVencimentoMensa($dataVen);
			}
	
			$o_mensalidade->setValorMensa(DataFilter::cleanString((float)str_replace(",", ".",str_replace(".", "",str_replace("R$", "", $_POST['valorMensa'])))));
			$o_mensalidade->setTb_situacaoMensalidade_idSit(DataFilter::cleanString($_POST['tb_situacaoMensalidade_idSit']));
			//echo $_POST['tb_situacaoMensalidade_idSit'];
			//exit;
			$o_mensalidade->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
			//salvando dados e redirecionando para a lista de mensalidades
			if($o_mensalidade->save() > 0){
			
				$idMensalidade =  $o_mensalidade->save();
				$o_boleto = new BoletoModel();
				//verificando se o id do boleto foi passado
				//buscando dados do contato
				$o_boleto->loadByIdMensa($idMensalidade);
				if($o_boleto->save() > 0){
				$o_boleto->save();
				}	
			}
				Application::redirect('?controle=Mensalidade&acao=controlarMensalidade&variavel=gerada');
                exit;
		}
		
		
		
		$o_view = new View('views/gerarMensalidade.phtml');
		$o_view->setParams(array('o_mensalidade' => $o_mensalidade));
		$o_view->showContents();
	}
	
	public function mensalidadeTransportadorAction()
	{
		$o_mensalidade = new MensalidadeModel();
		
		//verificando se o id da mensalidade foi passado
		if( isset($_REQUEST['idMensa']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idMensa']) )
				//buscando dados do contato
				$o_mensalidade->loadById($_REQUEST['idMensa']);
			
		if(count($_POST) > 0)
		{
			
			if ($_POST['dataVencimentoMensa'] != NULL){	
				$originalDateVen = $_POST['dataVencimentoMensa'];
				$dataVen = date("Y-m-d", strtotime($originalDateVen));
				$o_mensalidade->setDataVencimentoMensa($dataVen);
			}
			if ($_POST['dataPagamentoMensa'] != NULL){
				$originalDatePag = $_POST['dataPagamentoMensa'];
				$dataPag = date("Y-m-d", strtotime($originalDatePag));
				$o_mensalidade->setDataPagamentoMensa($dataPag);
			}
			$o_mensalidade->setValorMensa(DataFilter::cleanString((float)str_replace(",", ".",str_replace(".", "",str_replace("R$", "", $_POST['valorMensa'])))));
			$o_mensalidade->setTb_situacaoMensalidade_idSit(DataFilter::cleanString($_POST['tb_situacaoMensalidade_idSit']));
			//echo $_POST['tb_situacaoMensalidade_idSit'];
			//exit;
			$o_mensalidade->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
			//salvando dados e redirecionando para a lista de mensalidades
			if($o_mensalidade->save() > 0)
				$o_mensalidade->save();
				Application::redirect('?controle=Mensalidade&acao=controlarMensalidade&variavel=baixa');
                exit;
		}
			
		$o_view = new View('views/mensalidadeTransportador.phtml');
		$o_view->setParams(array('o_mensalidade' => $o_mensalidade));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão das mensalidades
	*/
	public function apagarMensalidadeAction()
	{
		if( DataValidator::isNumeric($_GET['idMensa']) )
		{
			//apagando a mensalidade
			$o_mensalidade = new MensalidadeModel();
			$o_mensalidade->delete($_GET['idMensa']);
			
	
			Application::redirect('?controle=Mensalidade&acao=controlarMensalidade&variavel=excluido');
            exit;
		}	
	}
}
?>