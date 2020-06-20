<?php

require_once 'models/LogModel.php';
require_once 'models/PerfilModel.php';
include './lib/function.php';
class LogController
{

	public function listarLogAction()
	{
		$o_Log = new LogModel();
		
		//Listando as situações cadastradas
		$v_logs = $o_Log->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de situações de mensalidade
		$o_view = new View('views/listarLog.phtml');
		
		//Passando os dados da mmenalidade para a View
		$o_view->setParams(array('v_logs' => $v_logs));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}

	public function manterLogAction()
	{
		$o_log = new LogModel();
		
		//verificando se o id da situacao da mensalidade foi passado
		if( isset($_REQUEST['idLog']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idLog']) )
				//buscando dados do contato
				$o_log->loadById($_REQUEST['idLog']);
			
		if(count($_POST) > 0)
		{
			$o_log->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
			//salvando dados e redirecionando para a lista de situações de mensalidade
			$o_log->save();
            exit;
            
		}
	}

	public function apagarVoucherAction()
	{
		if( DataValidator::isNumeric($_GET['idLog']) )
		{
			//apagando a situação da mensalidade
			$o_log = new LogModel();
			$o_log->loadById($_GET['idLog']);
			$o_log->delete();
            exit;
		}	
	}
}
?>