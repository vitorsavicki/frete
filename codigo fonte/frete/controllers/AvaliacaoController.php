<?php
//incluindo classes da camada Model
require_once 'models/AvaliacaoModel.php';
require_once 'models/CategoriaModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - AvaliacaoController.php

 *
 */
class AvaliacaoController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de contatos
	*/
	public function listarAvaliacaoAction()
	{
		$o_Avaliacao = new AvaliacaoModel();
        
        $v_avaliacoes = array();
		
		//Listando os avaliações cadastrados
		//$v_avaliacoes = $o_Avaliacao->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de avaliacoes
		$o_view = new View('views/listarAvaliacao.phtml');
		
		//Passando os dados do  para a View
		$o_view->setParams(array('v_avaliacoes' => $v_avaliacoes));
		
		//Imprimindo código HTML
		$o_view->showContents();
        exit;
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos itens
	*/
	public function manterAvaliacaoAction()
	{
		$o_avaliacao = new AvaliacaoModel();
		
		//verificando se o id do  foi passado
		if( isset($_REQUEST['idAva']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idAva']) )
				//buscando dados da avaliacao
				$o_avaliacao->loadById($_REQUEST['idAva']);
			
		if(count($_POST) > 0)
		{
			$o_avaliacao->setConteudoAva(DataFilter::cleanString(protecao($_POST['conteudoAva'])));
			$o_avaliacao->setTb_status_idStaAva(DataFilter::cleanString(1));
			$o_avaliacao->setTb_transporte_idTransp(DataFilter::cleanString($_POST['tb_tranporte_idTransp']));
			$o_avaliacao->setValorAva1(DataFilter::cleanString($_POST['valorAva1']));
			$o_avaliacao->setValorAva2(DataFilter::cleanString($_POST['valorAva2']));
			$o_avaliacao->setValorAva3(DataFilter::cleanString($_POST['valorAva3']));
			$o_avaliacao->setValorAva4(DataFilter::cleanString($_POST['valorAva4']));
			$o_avaliacao->setValorAva5(DataFilter::cleanString($_POST['valorAva5']));
			$o_avaliacao->setValorAva6(DataFilter::cleanString(0));
			$o_avaliacao->setValorAva7(DataFilter::cleanString(0));
			$o_avaliacao->setValorAva8(DataFilter::cleanString(0));
			$o_avaliacao->setValorAva9(DataFilter::cleanString(0));
			$o_avaliacao->setValorAva10(DataFilter::cleanString(0));
			//salvando dados e redirecionando para a lista de avaliacoes
			if($o_avaliacao->save() > 0)
				$o_avaliacao->save();
				Application::redirect('?controle=Transporte&acao=listarTransporte&opcao=transpconcluido&variavel=transporteAvaliado');
                exit;
		}
			
		$o_view = new View('views/manterAvaliacao.phtml');
		$o_view->setParams(array('$o_avaliacao' => $$o_avaliacao));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão das avaliacoes
	*/
	public function apagarAvaliacaoAction()
	{
		if( DataValidator::isNumeric($_GET['idAva']) )
		{
			//apagando a avaliacao
			$o_avaliacao = new AvaliacaoModel();
			$o_avaliacao->loadById($_GET['idAva']);
			$o_avaliacao->delete();
			
			Application::redirect('?controle=Avaliacao&acao=listarAvaliacao');
            exit;
		}	
	}
    
	public function avaliacaoTransportadorAction()
	{
		$o_view = new View('views/avaliacaoTransportador.php');
		$o_view->showContents();
	}

}
?>