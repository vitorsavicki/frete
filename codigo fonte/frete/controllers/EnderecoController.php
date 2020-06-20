<?php
//incluindo classes da camada Model
require_once 'models/EnderecoModel.php';
require_once 'models/PessoaModel.php';
include './lib/function.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - EnderecoController.php
 * 

 *
 */
class EnderecoController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de enderecos
	*/
	public function listarEnderecoAction()
	{
		$o_Endereco = new EnderecoModel();
		
		//Listando os enderecos cadastrados
		$v_enderecos = $o_Endereco->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de enderecos
		$o_view = new View('views/listarEndereco.phtml');
		
		//Passando os dados do endereco para a View
		$o_view->setParams(array('v_enderecos' => $v_enderecos));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	public function listarEnderecoTransportadorAction()
	{
		if( isset($_REQUEST['idPes']) )
			if( DataValidator::isNumeric($_REQUEST['idPes']) )
			{
				$o_pessoa = new PessoaModel();
				$o_pessoa->loadById($_REQUEST['idPes']);
	
				$v_enderecos = new EnderecoModel();
				$v_enderecos->loadById(null,$_REQUEST['idPes']);
				$o_view = new View('views/listarEndereco.phtml');
				$o_view->setParams(array('o_pessoa' => $o_pessoa,'v_enderecos' => $v_enderecos));
				$o_view->showContents();
			}
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos enderecos 
	*/
	public function manterEnderecoAction()
	{
		$o_endereco = new EnderecoModel();
		
		//verificando se o id do endereco foi passado
		if( isset($_REQUEST['idEnd']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idEnd']) )
				//buscando dados do contato
				$o_endereco->loadById($_REQUEST['idEnd'],null);
			
		if(count($_POST) > 0)
		{
			$o_endereco->setCepEnd(DataFilter::cleanString($_POST['cepEnd']));
			$o_endereco->setRuaEnd(DataFilter::cleanString(protecao($_POST['ruaEnd'])));
			$o_endereco->setNumeroEnd(DataFilter::cleanString($_POST['numeroEnd']));
			$o_endereco->setBairroEnd(DataFilter::cleanString(protecao($_POST['bairroEnd'])));
			$o_endereco->setComplementoEnd(DataFilter::cleanString(protecao($_POST['complementoEnd'])));
			$o_endereco->setTb_Cidade_idCid(DataFilter::cleanString($_POST['tb_Cidade_idCid']));
			$o_endereco->setTb_Estado_idEst(DataFilter::cleanString($_POST['tb_Estado_idEst']));
             $o_endereco->save();
				Application::redirect('?controle=Pessoa&acao=listarTransportador');
                exit;
		}
			
		$o_view = new View('views/manterEndereco.phtml');
		$o_view->setParams(array('o_endereco' => $o_endereco));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos enderecos
	*/

	
}
?>