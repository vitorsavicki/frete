<?php
//incluindo classes da camada Model
require_once 'models/CategoriaModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - CategoriaController.php
 * 

 */
class CategoriaController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de categorias
	*/
	public function listarCategoriaAction()
	{
		$o_Categoria = new CategoriaModel();
		
		//Listando os categorias cadastrados
		$v_categorias = $o_Categoria->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de categorias
		$o_view = new View('views/listarCategoria.phtml');
		
		//Passando os dados da categoria para a View
		$o_view->setParams(array('v_categorias' => $v_categorias));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos categorias
	*/
	public function manterCategoriaAction()
	{
		$o_categoria = new CategoriaModel();
		
		//verificando se o id do categoria foi passado
		if( isset($_REQUEST['idCat']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idCat']) )
				//buscando dados do contato
				$o_categoria->loadById($_REQUEST['idCat']);
			
		if(count($_POST) > 0)
		{
			$o_categoria->setNomeCat(DataFilter::cleanString(protecao($_POST['nomeCat'])));
			
			//salvando dados e redirecionando para a lista de categorias
			   $o_categoria->save();
				Application::redirect('?controle=Categoria&acao=listarCategoria&variavel=cadastrada');
                exit;
			
		}
			
		$o_view = new View('views/manterCategoria.phtml');
		$o_view->setParams(array('o_categoria' => $o_categoria));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão das categorias
	*/
	public function apagarCategoriaAction()
	{
		if( DataValidator::isNumeric($_GET['idCat']) )
		{
			//apagando a categoria
			$o_categoria = new CategoriaModel();
			$o_categoria->loadById($_GET['idCat']);
			if($o_categoria->delete()){
               Application::redirect('?controle=Categoria&acao=listarCategoria&variavel=excluida');
               exit; 
            }
            else{
                Application::redirect('?controle=Categoria&acao=listarCategoria&variavel=vinculo');
                exit;
            }
		}	
	}


}
?>