<?php
//incluindo classes da camada Model
require_once 'models/ItemModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - ItemController.php

 *
 */
class ItemController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de contatos
	*/
	public function listarItemAction()
	{
		$o_Item = new ItemModel();
		
		//Listando os itens cadastrados
		$v_itens = $o_Item->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de itens
		$o_view = new View('views/listarItem.phtml');
		
		//Passando os dados do item para a View
		$o_view->setParams(array('v_itens' => $v_itens));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos itens
	*/
	public function manterItemAction()
	{
		$o_item = new ItemModel();
		
		//verificando se o id do item foi passado
		if( isset($_REQUEST['idItem']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idItem']) )
				//buscando dados do contato
				$o_item->loadById($_REQUEST['idItem']);
			
		if(count($_POST) > 0)
		{
			$o_item->setNomeItem(DataFilter::cleanString(protecao($_POST['nomeItem'])));
			$o_item->setTb_categoria_idCat(DataFilter::cleanString($_POST['tb_categoria_idCat']));
			
			//salvando dados e redirecionando para a lista de itens
			if($o_item->save() > 0)
				$o_item->save();
				Application::redirect('?controle=Item&acao=listarItem&variavel=cadastrada');
                exit;
		}
			
		$o_view = new View('views/manterItem.phtml');
		$o_view->setParams(array('o_item' => $o_item));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos itens
	*/
	public function apagarItemAction()
	{
		if( DataValidator::isNumeric($_GET['idItem']) )
		{
			//apagando o item
			$o_item = new ItemModel();
			$o_item->loadById($_GET['idItem']);
            if($o_item->delete()){
                Application::redirect('?controle=Item&acao=listarItem&variavel=excluida');
                exit;
            }
            else{
                Application::redirect('?controle=Item&acao=listarItem&variavel=vinculo');
                exit;
            }
			
		}	
	}

}
?>