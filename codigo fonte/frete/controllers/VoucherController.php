<?php

require_once 'models/VoucherModel.php';
include './lib/function.php';
class VoucherController
{

	public function listarVoucherAction()
	{
		$o_Voucher = new VoucherModel();
		
		//Listando as situações cadastradas
		$v_vouchers = $o_Voucher->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de situações de mensalidade
		$o_view = new View('views/listarVoucher.phtml');
		
		//Passando os dados da mmenalidade para a View
		$o_view->setParams(array('v_vouchers' => $v_vouchers));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição das situações de mensalidade
	*/
	public function manterVoucherAction()
	{
		$o_voucher = new VoucherModel();
		
		//verificando se o id da situacao da mensalidade foi passado
		if( isset($_REQUEST['idVou']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['idVou']) )
				//buscando dados do contato
				$o_voucher->loadById($_REQUEST['idVou']);
			
		if(count($_POST) > 0)
		{
			$o_voucher->setCodigoVou(DataFilter::cleanString(protecao($_POST['codigoVou'])));
			$dataVoucher = date('Y-m-d', strtotime($_POST['dataValidadeVou']));
            $o_voucher->setDataValidadeVou(DataFilter::cleanString($dataVoucher));
			
			//salvando dados e redirecionando para a lista de situações de mensalidade
			    $o_voucher->save();
			    if($_REQUEST['idVou']){
			        Application::redirect('?controle=Voucher&acao=listarVoucher&variavel=editada');
                    exit;
			    }
                else{
                    Application::redirect('?controle=Voucher&acao=listarVoucher&variavel=cadastrada');
                    exit;
                }
				
            
		}
			
		$o_view = new View('views/manterVoucher.phtml');
		$o_view->setParams(array('o_voucher' => $o_voucher));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão das situações de mensalidade
	*/
	public function apagarVoucherAction()
	{
		if( DataValidator::isNumeric($_GET['idVou']) )
		{
			//apagando a situação da mensalidade
			$o_voucher = new VoucherModel();
			$o_voucher->loadById($_GET['idVou']);
			
			
	
            if($o_voucher->delete()){
              Application::redirect('?controle=Voucher&acao=listarVoucher&variavel=excluida');
              exit;
            }
            else{
              Application::redirect('?controle=Voucher&acao=listarVoucher&variavel=vinculo');
              exit;
            }
            
			
		}	
	}
}
?>