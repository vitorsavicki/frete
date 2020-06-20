<?php
//incluindo classes da camada Model
require_once 'models/ConteudoTransporteModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - ConteudoTransporteController.php
 * 

 */
class ConteudoTransporteController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de conteudo dos transportes
	*/
	public function listarConteudoTransporteAction()
	{
		$o_Conteudo = new ConteudoTransporteModel();
		
		//Listando os conteudos dos transportes cadastrados
		$v_conteudos = $o_Conteudo->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de conteudos dos transportes
		$o_view = new View('views/listarConteudoTransporte.phtml');
		
		//Passando os dados do conteudo do transporte para a View
		$o_view->setParams(array('v_conteudos' => $v_conteudos));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos conteudos dos transportes 
	*/
	public function manterConteudoTransporteAction()
	{
		$o_conteudo = new ConteudoTransporteModel();
		
		//verificando se o id do conteudo do transporte foi passado
		if( isset($_REQUEST['idConTran']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idConTran']) )
				//buscando dados do conteudo do Transporte
				$o_contato->loadById($_REQUEST['idConTran']);
			
		if(count($_POST) > 0)
		{
			$o_conteudo->setDescricaoItemConTran(DataFilter::cleanString(protecao($_POST['descricaoItemConTran'])));
			$o_conteudo->setQtdeConTran(DataFilter::cleanString($_POST['qtdeConTran']));
			$o_conteudo->setAlturaConTran(DataFilter::cleanString($_POST['alturaConTran']));
			$o_conteudo->setLarguraConTran(DataFilter::cleanString($_POST['larguraConTran']));
			$o_conteudo->setComprimentoConTran(DataFilter::cleanString($_POST['comprimentoConTran']));
			$o_conteudo->setPesoConTran(DataFilter::cleanString($_POST['pesoConTran']));
			$o_conteudo->setTb_item_idItem(DataFilter::cleanString($_POST['tb_item_idItem']));
			$o_conteudo->setTb_transporte_idTransp(DataFilter::cleanString($_POST['tb_transporte_idTransp']));
		
			
			//salvando dados e redirecionando para a lista de conteudos do transportadores
			if($o_conteudo->save() > 0)
				Application::redirect('?controle=ConteudoTransporte&acao=listarConteudoTransporte');
            exit;
		}
			
		$o_view = new View('views/manterConteudoTransporte.phtml');
		$o_view->setParams(array('o_conteudo' => $o_conteudo));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos conteudos dos transportes
	*/
	public function apagarConteudoTransporteAction()
	{
		if( DataValidator::isNumeric($_GET['idConTran']) )
		{
			//apagando o conteudo do transporte
			$o_conteudo = new ConteudoTransporteModel();
			$o_conteudo->loadById($_GET['idConTran']);
			$o_conteudo->delete();
			
	
			Application::redirect('?controle=ConteudoTransporte&acao=listarConteudoTransporte');
            exit;
		}	
	}

}
?>