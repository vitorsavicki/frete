<?php
	session_start();
	include '../lib/conexao.php';
    include '../lib/function.php';
	$codigoVou = isset($_POST['codigoVou']) ? protecao($_POST['codigoVou']) : '';


	$st_query = "SELECT * FROM tb_voucher WHERE codigoVou= UPPER('$codigoVou') and dataValidadeVou >= NOW()";

	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	$var = null;
	if($o_rows){
		$var = true;
	}
	else{
		$var = false;
	}
	
    echo json_encode($var);
?>
