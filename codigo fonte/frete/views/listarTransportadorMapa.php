<?php
	session_start();
	include '../lib/conexao.php';
	
	$st_query = "";
	
		$st_query =  "SELECT tb_pessoa.*, tb_endereco.*, tb_cidade.*, tb_estado.*, tb_status.* FROM tb_pessoa ";
		$st_query .= " INNER JOIN tb_status ON tb_status.idSta = tb_pessoa.tb_Status_idSta ";
		$st_query .= " LEFT join tb_endereco ON tb_endereco.idEnd = tb_pessoa.tb_endereco_idEnd ";
		$st_query .= " INNER JOIN tb_cidade ON tb_cidade.idCid = tb_endereco.tb_Cidade_idCid ";
		$st_query .= " INNER JOIN tb_estado ON tb_estado.idEst = tb_endereco.tb_Estado_idEst ";
		$st_query .= " INNER JOIN tb_perfil ON tb_perfil.idPer = tb_pessoa.tb_perfil_idPer ";
		$st_query .= " where 0 = 0 ";
		$st_query .= "and tb_perfil.codigoPer = 'T' ";
		$st_query .= "ORDER BY tb_pessoa.primeiroNomePes ASC;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';

	$var = array();
	foreach ($o_rows as $o_ret => $value)
	{
		$html .= $value->bairroEnd . ' ' . $value->cepEnd . ' ' . utf8_encode($value->nomeCid) . ' '. utf8_encode($value->nomeEst);
		$var[] = $html;	
	}
	
   			echo json_encode($var);
?>