<?php

	session_start();
	include '../lib/conexao.php';
	
	$dataini = isset($_POST['dataini']) ? $_POST['dataini'] : '';
	$datafim = isset($_POST['datafim']) ? $_POST['datafim'] : '';
	if($datafim !== '')
	$datafim = date('d-m-Y', strtotime($datafim));
	if($dataini !== '')
	$dataini = date('d-m-Y', strtotime($dataini));
	$tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
	//$_SESSION['idPes'] = 3;
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	
	$st_query =  'SELECT * FROM tb_boleto B ';
	$st_query .=  'INNER JOIN tb_mensalidade M ON B.idMensa = M.idMensa ';
	$st_query .=  'INNER JOIN tb_pessoa P ON M.tb_pessoa_idPes = P.idPes ';
	$st_query .=  "WHERE idPes = '$idPes' AND  M.tb_situacaoMensalidade_idSit = 6 ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(B.dataVencBol as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(B.dataVencBol as Date) <= '$datafim' ";
	$st_query .= "ORDER BY dataVencBol DESC;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	$st_query .=  'SELECT * FROM tb_boleto B ';
	$st_query .=  'INNER JOIN tb_mensalidade M ON B.idMensa = M.idMensa ';
	$st_query .=  'INNER JOIN tb_pessoa P ON M.tb_pessoa_idPes = P.idPes ';
	$st_query .=  "WHERE idPes = '$idPes' AND  M.tb_situacaoMensalidade_idSit = 6 ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(B.dataVencBol as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(B.dataVencBol as Date) <= '$datafim' ";
	$st_query .= "ORDER BY dataVencBol DESC LIMIT $registros OFFSET $inicio;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';
	$html .= '<div class="table-responsive">';
	$html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="example">';
    $html .= '<thead>';
    $html .= '<tr>';
	$html .= '<th class="text-center">ID</th>';
	$html .= '<th class="text-center">Data de Vencimento</th>';
	$html .= '<th class="text-center">Valor</th>';
	$html .= '<th class="text-center">A&ccedil;&otilde;es</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		
		$html .= '<tr>';
		$html .= '<td align="center">';
		$html .= $value->idBol;
		$html .= '</td>';
		$html .= '<td align="center">';
		$html .= date('d/m/Y', strtotime($value->dataVencBol));
		$html .= '</td>';
		$html .= '<td align="center">';
		$html .= 'R$ ' .number_format($value->valorMensa, 2, ',', '.');
		$html .= '</td>';
		$html .= '<td align="center">';
		$html .= '<a href="?controle=Boleto&acao=gerarBoleto&idMensa='.$value->idMensa.'" class="btn btn-primary btn-xs" id="imprimirBoleto" name="imprimirBoleto" target="_blank">Imprimir Boleto</a>';
		$html .= '</td>';                             
		$html .=  '</tr>';
		$var[] = $html;
		
	}
	
			$html .= '</tbody>';
			$html .= '</table>';
			$html .=  '</div>';
			
			$html .= '<div id="pagination" class="text-center">';
			$html .= '<ul class="pagination">';
 
			if ($pagina_atual == 1){ 
			$html .= '<li class="active"><a>Primeira</a></li>';
			$html .= '<li class="active"><a>Anterior</a></li>';
			}
			else{
			$html .= '<li>';
			$html .= '<a onclick=fncJson(1);>Primeira</a>';
			$html .= '</li>';
			$html .= '<li>';
			$pagina_atual = $pagina_atual - 1;
			$html .= '<a onclick=fncJson('. $pagina_atual .');>Anterior';
			$pagina_atual = $pagina_atual + 1;
			$html .= '</a>';	
			$html .= '</li>';
			}
			 
			foreach(array_reverse(range($pagina_atual-1, $pagina_atual-5)) as $pagina){
				if ($pagina > 0){
					$html .= '<li>';
					$html .= '<a onclick=fncJson('.$pagina.');>';
					$html .= $pagina;
					$html .= '</a>';
					$html .= '</li>';
				}
			}
			$html .= '<li class="active"><a>'.$pagina_atual.'</a></li>';
			$html .= '<input type="hidden" id="pagina" name="pagina" value="'.$pagina_atual.'">'; 
			 
			foreach( range($pagina_atual+1, $pagina_atual+5) as $pagina){
				if ($pagina < $paginas){
					$html .= '<li>';
					$html .= '<a onclick=fncJson('.$pagina.');>';
					$html .= $pagina;
					$html .= '</a>';
					$html .= '</li>';
				}
			}

			if ($pagina_atual == $paginas){
			 
			$html .= '<li class="active"><a>Pr&oacute;xima</a></li>';
			$html .= '<li class="active"><a>&Uacute;ltima</a></li>';
			}
			else{
			 
			$html .= '<li>';
			$pagina_atual = $pagina_atual + 1;
			$html .= '<a onclick=fncJson('. $pagina_atual .');>Pr&oacute;xima</a>';
			$pagina_atual = $pagina_atual - 1;
			$html .= '</li>';
			$html .= '<li>';
			$html .= '<a onclick=fncJson('. $paginas .');>&Uacute;ltima';
			$html .= '</a>';
			$html .= '</li>';
			 
			}
			$html .= '</ul>';
			$html .= '</div>';
			$var[] = $html;
			}
		else{
		$html .= '<td colspan="10">';
		$html .= '<div class="text-center"><h4>Nenhum registro econtrado!</h4></div>';
		$html .= '</td>';
		$var[] = $html;
	}
   			echo json_encode($var);
?>