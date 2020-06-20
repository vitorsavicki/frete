<?php
	include '../lib/conexao.php';
	$estado = isset($_REQUEST['estado']) ? $_REQUEST['estado'] : '18';
	if($estado == null){
		$html = '<option value="0">Selecione uma Cidade</option>';
	}
	else{
	$st_query = "SELECT * FROM tb_cidade WHERE tb_Estado_idEst = $estado;";
	$o_data = $o_db->query($st_query);
	$html = '<option value="0">Selecione uma Cidade</option>';
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	foreach ($o_rows as $o_ret => $value)
	{
		//$array = get_object_vars($o_ret);
		$idCid = $value->idCid;// $array['idCid'];
		$nomeCid = $value->nomeCid;
		$registro = array(
				"idCid"=>$idCid,
				"nomeCid"=>$nomeCid
				);
		$var[] = $registro;
		$html .= '<option value='.utf8_encode($idCid).'>'.utf8_encode($nomeCid).'</option>';
	}
	}
	//echo json_encode($var);
	echo $html;
?>