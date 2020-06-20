<?php
//incluindo classes da camada Model
require_once 'models/DadoAdicionalModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - DadoAdicionalController.php
 * 

 *
 */
class DadoAdicionalController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de dados adicionais
	*/
	public function listarDadoAdicionalAction()
	{
		$o_Dado = new DadoAdicionalModel();
		
		//Listando os dados adcionais cadastrados
		$v_dados = $o_Dado->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarDadoAdicional.phtml');
		
		//Passando os dados do contato para a View
		$o_view->setParams(array('v_dados' => $v_dados));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos dados adicionais
	*/
	public function manterDadoAdicionalAction()
	{
		$o_dado = new DadoAdicionalModel();
		
		//verificando se o id do dado adicional foi passado
		if( isset($_REQUEST['idDadAdi']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idDadAdi']) )
				//buscando dados do contato
				$o_dado->loadById($_REQUEST['idDadAdi']);
			
		if(count($_POST) > 0)
		{
			$o_dado->setIdDadAdi(DataFilter::cleanString($_POST['idDadAdi']));
			$o_dado->setComentarioDadAdi(DataFilter::cleanString(protecao($_POST['comentarioDadAdi'])));
			$o_dado->setPrecoMaximoDadAdi(DataFilter::cleanString($_POST['precoMaximoDadAdi']));
			$o_dado->setNumAjudantesDadAdi(DataFilter::cleanString($_POST['numAjudantesDadAdi']));
			$o_dado->setTb_transporte_idTransp(DataFilter::cleanString($_POST['tb_transporte_idTransp']));
				
			
			//salvando dados e redirecionando para a lista de contatos
			if($o_dado->save() > 0)
				Application::redirect('?controle=DadoAdicional&acao=listarDadoAdicional');
                exit;
		}
			
		$o_view = new View('views/manterDadoAdicional.phtml');
		$o_view->setParams(array('o_dado' => $o_dado));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos dados adicionais
	*/
	public function apagarDadoAdicionalAction()
	{
		if( DataValidator::isNumeric($_GET['idDadAdi']) )
		{
			//apagando o dado Adicional
			$o_dado = new DadoAdcionalModel();
			$o_dado->loadById($_GET['idDadAdi']);
			$o_dado->delete();
			
	
			Application::redirect('?controle=DadoAdicional&acao=listarDadoAdicional');
            exit;
		}	
	}

}
?>