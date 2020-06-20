<?php
	include '../lib/conexao.php';
	$tb_categoria_idCat = isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : '';
	if($tb_categoria_idCat == null){
		$html = '<option value="0">Selecione um Item</option>';
	}
	else{
	echo $st_query = "SELECT * FROM tb_item WHERE tb_categoria_idCat = $tb_categoria_idCat;";
	
	$o_data = $o_db->query($st_query);
	$html = '<option value="0">Selecione um Item</option>';
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	foreach ($o_rows as $o_ret => $value)
	{
		//$array = get_object_vars($o_ret);
		$idItem = $value->idItem;// $array['idCid'];
		$nomeItem = $value->nomeItem;
		$registro = array(
				"idItem"=>$idItem,
				"nomeItem"=>$nomeItem
				);
		$var[] = $registro;
		$html .= '<option value='.$idItem.'>'.$nomeItem.'</option>';
	}
	}
	//echo json_encode($var);
	echo $html;
?>