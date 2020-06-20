<?php
//incluindo classes da camada Model
require_once 'models/EstadoModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - EstadoController.php
 * 

 *
 */
class EstadoController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de contatos
	*/
	public function listarEstadoAction()
	{
		$o_Estado = new EstadoModel();
		
		//Listando os estados cadastrados
		$v_estados = $o_Estado->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarEstado.phtml');
		
		//Passando os dados do estado para a View
		$o_view->setParams(array('v_estados' => $v_estados));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos estados
	*/
	public function manterEstadoAction()
	{
		$o_estado = new EstadoModel();
		
		//verificando se o id do contato foi passado
		if( isset($_REQUEST['idEst']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idEst']) )
				//buscando dados do contato
				$o_estado->loadById($_REQUEST['idEst']);
			
		if(count($_POST) > 0)
		{
			$o_estado->setIdEst(DataFilter::cleanString($_POST['idEst']));
			$o_estado->setNomeEst(DataFilter::cleanString(protecao($_POST['nomeEst'])));
			$o_estado->setUfEst(DataFilter::cleanString($_POST['ufEst']));
			$o_estado->setTb_Pais_idPais(DataFilter::cleanString($_POST['tb_Pais_idPais']));
		
			
			//salvando dados e redirecionando para a lista de estado
			if($o_estado->save() > 0)
				Application::redirect('?controle=Estado&acao=listarEstado');
                exit;
		}
			
		$o_view = new View('views/manterEestado.phtml');
		$o_view->setParams(array('o_estado' => $o_estado));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos estados
	*/
	public function apagarEstadoAction()
	{
		if( DataValidator::isNumeric($_GET['idEst']) )
		{
			//apagando o estado
			$o_estado = new EstadoModel();
			$o_estado->loadById($_GET['idEst']);
			$o_estado->delete();

			Application::redirect('?controle=Estado&acao=listarEstado');
            exit;
		}	
	}

}
?>