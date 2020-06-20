<?php
//incluindo classes da camada Model
require_once 'models/AdministradorModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - AdministradorController.php
 * 
 *
 */
class AdministradorController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de administradores
	*/
	public function listarAdministradorAction()
	{
		$o_Administrador = new AdministradorModel();
		
		//Listando os administradores cadastrados
		$v_administradores = $o_Administrador->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarAdministrador.phtml');
		
		//Passando os dados do administrador para a View
		$o_view->setParams(array('v_administradores' => $v_administradores));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos administradores 
	*/
	public function manterAdministradorAction()
	{
		$o_administrador = new AdministradorModel();
		
		//verificando se o id do administrador foi passado
		if( isset($_REQUEST['idAdm']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idAdm']) )
				//buscando dados do administrador
				$o_administrador->loadById($_REQUEST['idAdm']);
			
		if(count($_POST) > 0)
		{
			$o_administrador->setPrimeiroNomeAdm(DataFilter::cleanString(protecao($_POST['primeiroNomeAdm'])));
			$o_administrador->setSobreNomeAdm(DataFilter::cleanString(protecao($_POST['sobreNomeAdm'])));
			$o_administrador->setDataCadastroAdm(DataFilter::cleanString($_POST['dataCadastroAdm']));
			$o_administrador->setStatusAdm(DataFilter::cleanString($_POST['statusAdm']));
			$o_administrador->setTb_usuario_idUsu(DataFilter::cleanString($_POST['tb_usuario_idUsu']));
		
			//salvando dados e redirecionando para a lista de administradores
			if($o_administrador->save() > 0)
				Application::redirect('?controle=Administrador&acao=listarAdministrador');
            exit;
		}
			
		$o_view = new View('views/manterAdministrador.phtml');
		$o_view->setParams(array('o_administrador' => $o_administrador));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos administradores
	*/
	public function apagarAdministradorAction()
	{
		if( DataValidator::isNumeric($_GET['idAdm']) )
		{
			//apagando o administrador
			$o_administrador = new AdministradorModel();
			$o_administrador->loadById($_GET['idAdm']);
			$o_administrador->delete();
			
		
			Application::redirect('?controle=Administrador&acao=listarAdministrador');
            exit;
		}	
	}


}
?>