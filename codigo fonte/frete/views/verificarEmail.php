<?php
	session_start();
	include '../lib/conexao.php';
	$emailPes = isset($_POST['emailPes']) ? $_POST['emailPes'] : '';


	$st_query = "SELECT * FROM tb_pessoa WHERE emailPes = '$emailPes' ";

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
