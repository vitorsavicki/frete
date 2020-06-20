<?php
require_once 'models/PessoaModel.php';
require_once 'models/LogModel.php';
include './lib/function.php';
/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - ClienteController.php

 *
 */
class LoginController
{
	
	public $login_error;
	
	public function loginAction()
	{
        
		$usuario = protecao($_POST['emailPes']);
		$senha = md5($_POST['senhaPes']);

		$o_pessoa = new PessoaModel();
		$o_pessoa->loadById(null, $usuario);
	

		//$st_query = "SELECT * FROM tb_pessoa WHERE emailPes = '$usuario' AND senhaPes = '$senha' AND tb_Status_idSta = '1'";
		//$st_query = "SELECT top 1 * FROM tb_pessoa WHERE emailPes = '$usuario'";
		//echo $st_query;
		//exit;
		//$o_data = $o_db->query($st_query);
		
		if ($o_pessoa->getIdPes() == NULL || $o_pessoa->getSenhaPes() !== $senha ) {
			// Mensagem de erro quando os dados são inválidos e/ou o usuário não foi encontrado
			echo "Mensagem de erro quando os dados são inválidos e/ou o usuário não foi encontrado<br>";
			echo $o_pessoa->getIdPes()."<br>";
			echo $o_pessoa->getSenhaPes()."<br>";
			echo $usuario."<br>";
			echo $senha."<br>";;
			echo $o_pessoa->getCodigoPer();
			//exit;
			Application::redirect('?controle=Pessoa&acao=paginaLogin&variavel=loginInvalido');
            exit;
			
			
		} else {
			// Se a sessão não existir, inicia uma
			if (!isset($_SESSION)) session_start();
	
			// Salva os dados encontrados na sessão
			$_SESSION['idPes'] = $o_pessoa->getIdPes();
			$_SESSION['emailPes'] = $o_pessoa->getEmailPes();
			$_SESSION['primeiroNomePes'] = $o_pessoa->getPrimeiroNomePes();
			$_SESSION['fotoPes'] = $o_pessoa->getFotoPes();
			$_SESSION['tb_perfil_idPer'] = $o_pessoa->getTb_perfil_idPer();
			$_SESSION['Tb_Status_idSta'] = $o_pessoa->getTb_Status_idSta();	
			$_SESSION['codigoSta'] = $o_pessoa->getCodigoSta();	
			$_SESSION['codigoPer'] = $o_pessoa->getCodigoPer();
			
			
			$o_log = new LogModel();
			$o_log->setTb_pessoa_idPes($o_pessoa->getIdPes());
            date_default_timezone_set('America/Sao_Paulo');
            $date = date('Y-m-d H:i:s');
            $o_log->setDataLog($date);
            $o_log->save();
			
			
			
			if($_SESSION['codigoPer'] == 'U')
			{
				$o_view = new View('views/areaCliente.php');
				$o_view->showContents();
				exit;
			}
			elseif($_SESSION['codigoPer'] == 'T')
			{
				$o_view = new View('views/areaTransportador.php');
				$o_view->showContents();
				exit;
			}
			elseif ($_SESSION['codigoPer'] == 'A')
			{
				$o_view = new View('views/areaAdministrador.php');
				$o_view->showContents();
				exit;
			}
			
		}
	}
	
	public function logoutAction()
	{
		session_start(); // Inicia a sess�o
		session_destroy(); // Destr�i a sess�o limpando todos os valores salvos
	    echo "<meta HTTP-EQUIV='Refresh' CONTENT='0;URL=http://www.savicki.com.br'>";

	}

	
	

}
?>