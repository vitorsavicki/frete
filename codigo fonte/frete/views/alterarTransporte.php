<?php
	include '../lib/conexao.php';
	$statusTransp = isset($_POST['statusTransp']) ? $_POST['statusTransp'] : 'A';
	$idTransp = isset($_POST['idTransp']) ? $_POST['idTransp'] : '0';

	$st_query = "update tb_transporte set tb_statusTransp_idStaTransp = ";
	$st_query .= "(SELECT idStaTransp FROM tb_statusTransp where codigoStaTransp = '$statusTransp' LIMIT 1) ";
	$st_query .= "where idTransp = '$idTransp'";
	if($statusTransp == 'X'){
	$st_query_status = "UPDATE tb_lance SET vencedorLan = 'N' WHERE tb_transporte_idTransp = '$idTransp'";
	$o_db->query($st_query_status);
	}
	
	$o_db->query($st_query);
	
	
	// convertemos em json e colocamos na tela
    echo json_encode($st_query);
?>