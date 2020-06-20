<?php
//incluindo classes da camada Model
require_once 'models/ImagemTransporteModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - ImagemTransporteController.php
 * 

 */
class ImagemTransporteController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de imagens dos transportes
	*/
	public function listarImagemTransporteAction()
	{
		$o_Imagem = new ImagemTransporteModel();
		
		//Listando as imagens dos transportes cadastrados
		$v_imagens = $o_Imagem->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de imagens dos transportes
		$o_view = new View('views/listarImagemTransporte.phtml');
		
		//Passando os dados da imagem do transporte para a View
		$o_view->setParams(array('v_imagens' => $v_imagens));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição das imagens dos transportes 
	*/
	public function manterImagemTransporteAction()
	{
		$o_imagem = new ImagemTransporteModel();
		
		//verificando se o id da imagem do transporte foi passado
		if( isset($_REQUEST['idImgTran']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idImgTran']) )
				//buscando dados da imagem do Transporte
				$o_imagem->loadById($_REQUEST['idImgTran']);
			
		if(count($_POST) > 0)
		{
			$o_imagens->setCaminhoImg(DataFilter::cleanString($_POST['caminhoImg']));
			$o_imagens->setTb_transporte_idTransp(DataFilter::cleanString($_POST['tb_transporte_idTransp']));
		
			
			//salvando dados e redirecionando para a lista de imagens do transportadores
			if($o_imagem->save() > 0)
				Application::redirect('?controle=ImagemTransporte&acao=listarImagemTransporte');
            exit;
		}
			
		$o_view = new View('views/manterImagemTransporte.phtml');
		$o_view->setParams(array('o_imagem' => $o_imagem));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão das imagens dos transportes
	*/
	public function apagarImagemTransporteAction()
	{
		if( DataValidator::isNumeric($_GET['idImgTran']) )
		{
			//apagando a imagem do transporte
			$o_imagem = new ImagemTransporteModel();
			$o_imagem->loadById($_GET['idImgTran']);
			$o_imagem->delete();
			unlink($o_imagem->getCaminhoImg());
		
			
	
			//Application::redirect('?controle=ImagemTransporte&acao=listarImagemTransporte');
		}	
	}
}
?>