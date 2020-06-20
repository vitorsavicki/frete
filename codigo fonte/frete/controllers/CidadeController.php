<?php
//incluindo classes da camada Model
require_once 'models/CidadeModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - CidadeController.php
 * 

 *
 */
class CidadeController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de contatos
	*/
	public function listarCidadeAction()
	{
		$o_Cidade = new CidadeModel();
		
		//Listando as cidades cadastradas
		$v_cidades = $o_Cidade->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarCidade.phtml');
		
		//Passando os dados da cidade para a View
		$o_view->setParams(array('v_cidades' => $v_cidades));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição das cidades
	*/
	public function manterCidadeAction()
	{
		$o_cidade = new CidadeModel();
		
		//verificando se o id do contato foi passado
		if( isset($_REQUEST['idCid']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idCid']) )
				//buscando dados do contato
				$o_cidade->loadById($_REQUEST['idCid']);
			
		if(count($_POST) > 0)
		{
			$o_cidade->setIdCid(DataFilter::cleanString($_POST['idCid']));
			$o_cidade->setNomeCid(DataFilter::cleanString(protecao($_POST['nomeCid'])));
			$o_cidade->setTb_Estado_IdEst(DataFilter::cleanString($_POST['tb_Estado_IdEst']));
			
			//salvando dados e redirecionando para a lista de cidades
			if($o_cidade->save() > 0)
				Application::redirect('?controle=Cidade&acao=listarCidade');
                exit;
		}
			
		$o_view = new View('views/manterCidade.phtml');
		$o_view->setParams(array('o_cidade' => $o_cidade));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão das cidades
	*/
	public function apagarCidadeAction()
	{
		if( DataValidator::isNumeric($_GET['idCid']) )
		{
			//apagando a cidade
			$o_cidade = new CidadeModel();
			$o_cidade->loadById($_GET['idCid']);
			$o_cidade->delete();

			Application::redirect('?controle=Cidade&acao=listarCidade');
            exit;
		}	
	}
    
}
?>