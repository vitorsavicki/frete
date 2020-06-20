<?php
//incluindo classes da camada Model
require_once 'models/SituacaoMensalidadeModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - SituacaoMensalidadeController.php
 * 

 *
 */
class SituacaoMensalidadeController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de situações de mensalidades
	*/
	public function listarSituacaoMensalidadeAction()
	{
		$o_SituacaoMensalidade = new SituacaoMensalidadeModel();
		
		//Listando as situações cadastradas
		$v_situacaomensalidades = $o_SituacaoMensalidade->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de situações de mensalidade
		$o_view = new View('views/listarSituacaoMensalidade.phtml');
		
		//Passando os dados da mmenalidade para a View
		$o_view->setParams(array('v_situacaomensalidades' => $v_situacaomensalidades));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição das situações de mensalidade
	*/
	public function manterSituacaoMensalidadeAction()
	{
		$o_situacaomensalidade = new SituacaoMensalidadeModel();
		
		//verificando se o id da situacao da mensalidade foi passado
		if( isset($_REQUEST['idSit']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idSit']) )
				//buscando dados do contato
				$o_situacaomensalidade->loadById($_REQUEST['idSit']);
			
		if(count($_POST) > 0)
		{
			$o_situacaomensalidade->setDescricaoSit(DataFilter::cleanString(protecao($_POST['descricaoSit'])));
			
			//salvando dados e redirecionando para a lista de situações de mensalidade
			if($o_situacaomensalidade->save() > 0)
				Application::redirect('?controle=SituacaoMensalidade&acao=listarSituacaoMensalidade');
            exit;
		}
			
		$o_view = new View('views/manterSituacaoMensalidade.phtml');
		$o_view->setParams(array('o_situacaomensalidade' => $o_situacaomensalidade));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão das situações de mensalidade
	*/
	public function apagarSituacaoMensalidadeAction()
	{
		if( DataValidator::isNumeric($_GET['idSit']) )
		{
			//apagando a situação da mensalidade
			$o_situacaomensalidade = new SituacaoMensalidadeModel();
			$o_situacaomensalidade->loadById($_GET['idSit']);
			$o_situacaomensalidade->delete();
			
	
			Application::redirect('?controle=SituacaoMensalidade&acao=listarSituacaoMensalidade');
            exit;
		}	
	}

}
?>