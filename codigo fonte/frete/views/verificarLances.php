<?php
	session_start();
	include '../lib/conexao.php';
	$tb_transporte_idTransp = isset($_POST['tb_transporte_idTransp']) ? $_POST['tb_transporte_idTransp'] : '';


	$st_query = "SELECT * FROM tb_lance WHERE tb_transporte_idTransp = '$tb_transporte_idTransp' ";
	

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
