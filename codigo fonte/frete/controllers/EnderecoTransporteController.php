<?php
//incluindo classes da camada Model
require_once 'models/EnderecoTransporteModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - EnderecoTransporteController.php
 * 

 *
 */
class EnderecoTransporteController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de contatos
	*/
	public function listarEnderecoTransporteAction()
	{
		$o_Endtransporte = new EnderecotransporteModel();
		
		//Listando os enderecos de transporte cadastrados
		$v_endtransportes = $o_Endtransporte->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarEnderecoTransporte.phtml');
		
		//Passando os dados do endereco de transporte para a View
		$o_view->setParams(array('v_endtransportes' => $v_endtransportes));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos enderecos de transportes
	*/
	public function manterEnderecoTransporteAction()
	{
		$o_endtransporte = new EnderecoTransporteModel();
		
		//verificando se o id do contato foi passado
		if( isset($_REQUEST['idEndTran']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idEndTran']) )
				//buscando dados do contato
				$o_endtransporte->loadById($_REQUEST['idEndTran']);
			
		if(count($_POST) > 0)
		{
			$o_endtransporte->setCepOrigemEndTran(DataFilter::cleanString($_POST['cepOrigemEndTran']));
			$o_endtransporte->setCepDestinoEndTran(DataFilter::cleanString($_POST['cepDestinoEndTran']));
			$o_endtransporte->setRuaOrigemEndTran(DataFilter::cleanString(protecao($_POST['ruaOrigemEndTran'])));
			$o_endtransporte->setRuaDestinoEndTran(DataFilter::cleanString(protecao($_POST['ruaDestinoEndTran'])));
			$o_endtransporte->setBairroOrigemEndTran(DataFilter::cleanString(protecao($_POST['bairroOrigemEndTran'])));
			$o_endtransporte->setBairroDestinoEndTran(DataFilter::cleanString(protecao($_POST['bairroDestinoEndTran'])));
			$o_endtransporte->setTb_cidadeOrigem_IdCid(DataFilter::cleanString($_POST['tb_cidadeOrigem_IdCid']));
			$o_endtransporte->setTb_cidadeDestino_IdCid(DataFilter::cleanString($_POST['tb_cidadeDestino_IdCid']));
		
			
			//salvando dados e redirecionando para a lista de enderecos de transportes
			if($o_endtransporte->save() > 0)
				Application::redirect('?controle=EnderecoTransporte&acao=listarEnderecoTransporte');
                exit;
		}
			
		$o_view = new View('views/manterEnderecoTransporte.phtml');
		$o_view->setParams(array('o_endtransporte' => $o_endtransporte));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos enderecos de transportes
	*/
	public function apagarEnderecoTransporteAction()
	{
		if( DataValidator::isNumeric($_GET['idEndTran']) )
		{
			//apagando o endereco de transporte
			$o_enderecoTransporte = new EnderecoTransporteModel();
			$o_enderecoTransporte->loadById($_GET['idEndTran']);
			$o_enderecoTransporte->delete();

			Application::redirect('?controle=EnderecoTransporte&acao=listarEnderecoTransporte');
            exit;
		}	
	}

}
?>