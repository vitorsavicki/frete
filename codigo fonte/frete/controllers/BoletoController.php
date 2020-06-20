<?php
//incluindo classes da camada Model
require_once 'models/BoletoModel.php';


/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - BoletoController.php
 * 

 *
 */
class BoletoController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de boletos
	*/
	public function listarBoletoAction()
	{
		$o_Boleto = new BoletoModel();
		
		//Listando os boletos cadastrados
		$v_boletos = $o_Boleto->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarBoleto.phtml');
		
		//Passando os dados do contato para a View
		$o_view->setParams(array('v_boletos' => $v_boletos));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	public function listarBoletoTransportadorAction()
	{
		$o_Boleto = new BoletoModel();
		
		$idPes = $_REQUEST['idPes'];
		//Listando os boletos cadastrados
		$v_boletos = $o_Boleto->_listTransportador($idPes);
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de beltos
		$o_view = new View('views/listarBoleto.phtml');
		
		//Passando os dados do contato para a View
		$o_view->setParams(array('v_boletos' => $v_boletos));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	/**
	* Gerencia a  de criação e edição dos boletos 
	*/
	public function manterBoletoAction()
	{
		$o_boleto = new BoletoModel();
		
		//verificando se o id do boleto foi passado
		if( isset($_REQUEST['idBol']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idBol']) )
				//buscando dados do contato
				$o_boleto->loadById($_REQUEST['idBol']);
			
		if(count($_POST) > 0)
		{
			$o_boleto->setDataBol(DataFilter::cleanString($_POST['dataBol']));
			
			//salvando dados e redirecionando para a lista de boletos
			if($o_boleto->save() > 0)
				$o_boleto->save();
				Application::redirect('?controle=Boleto&acao=listarBoleto');
                exit;
		}
			
		$o_view = new View('views/manterBoleto.phtml');
		$o_view->setParams(array('o_boleto' => $o_boleto));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a  de criação e edição dos boletos 
	*/
	public function gerarBoletoAction()
	{
		$o_boleto = new BoletoModel();
		
		//verificando se o id do boleto foi passado
		if( isset($_REQUEST['idMensa']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idMensa']) )
				//buscando dados do contato
				$o_boleto->loadByIdMensa($_REQUEST['idMensa']);
		
		$o_view = new View('boletophp/boleto_hsbc.php');
		$o_view->setParams(array('o_boleto' => $o_boleto));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos boletos
	*/
	public function apagarBoletoAction()
	{
		if( DataValidator::isNumeric($_GET['idBol']) )
		{
			//apagando o boleto
			$o_boleto = new BoletoModel();
			$o_boleto->loadById($_GET['idBol']);
			$o_boleto->delete();
			
			Application::redirect('?controle=Boleto&acao=listarBoleto');
            exit;
			
		}	
	}
}
?>