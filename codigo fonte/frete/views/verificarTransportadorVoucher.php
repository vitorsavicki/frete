<?php
	session_start();
	include '../lib/conexao.php';
	$idPes = isset($_POST['idPes']) ? $_POST['idPes'] : '';


	$st_query = "SELECT tb_pessoa.*, tb_voucher.* FROM tb_pessoa
	             INNER JOIN tb_pessoa_voucher ON tb_pessoa_voucher.tb_pessoa_idPes = tb_pessoa.idPes
	             INNER JOIN tb_voucher ON tb_voucher.idVou = tb_pessoa_voucher.tb_voucher_idVou  
	             WHERE idPes = $idPes AND tb_voucher.dataValidadeVou >= NOW()";

	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	$var = null;
	if($o_rows){

	       $var =  true;
	}
	else{
		   $var = false;
	}
	
    echo json_encode($var);
?>
