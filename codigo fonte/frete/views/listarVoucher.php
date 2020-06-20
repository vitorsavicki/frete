<?php
	session_start();
	include '../lib/conexao.php';
    include '../lib/function.php';
	$palavraChave = isset($_POST['palavraChave']) ? protecao($_POST['palavraChave']) : '';
    $dataini = isset($_POST['dataini']) ? $_POST['dataini'] : '';
    $datafim = isset($_POST['datafim']) ? $_POST['datafim'] : '';
    if($datafim !== '') 
    $datafim = date('d-m-Y', strtotime($datafim));
    if($dataini !== '')
    $dataini = date('d-m-Y', strtotime($dataini));
	//$_SESSION['idPes'] = 3;
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	$st_query = "";
	$st_query =  "SELECT * FROM tb_voucher ";
    $st_query .= "WHERE 0 = 0 ";
    if (isset($dataini) and $dataini !== NULL and $dataini !== '')
        $st_query .= "and Cast(tb_voucher.dataValidadeVou as Date) >= '$dataini' ";
    if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
        $st_query .= "and Cast(tb_voucher.dataValidadeVou as Date) <= '$datafim' ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= "and tb_voucher.codigoVou like '%$palavraChave%' ";
	$st_query .= "ORDER BY tb_voucher.dataValidadeVou DESC ; ";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	
    $st_query =  "  SELECT * FROM tb_voucher ";
    $st_query .= "WHERE 0 = 0 ";
    if (isset($dataini) and $dataini !== NULL and $dataini !== '')
        $st_query .= "and Cast(tb_voucher.dataValidadeVou as Date) >= '$dataini' ";
    if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
        $st_query .= "and Cast(tb_voucher.dataValidadeVou as Date) <= '$datafim' ";
    if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
        $st_query .= " and tb_voucher.codigoVou like '%$palavraChave%' ";
	$st_query .= "ORDER BY tb_voucher.dataValidadeVou DESC LIMIT $registros OFFSET $inicio;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';
	$html .= '<div class="table-responsive">';
	$html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="example">';
    $html .= '<thead>';
    $html .= '<tr>';
	$html .= '<th class="text-center">Código</th>';
    $html .= '<th class="text-center">Data de Validade</th>';
    $html .= '<th class="text-center" colspan="2">Ações</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		
		$html .= '<tr>';
		$html .= '<td align="center">'.$value->codigoVou.'</td>';
        $html .= '<td align="center">'.date('d/m/Y', strtotime($value->dataValidadeVou)).'</td>';
		$html .= '<td align="center">';
        $html .= '<a href="?controle=Voucher&acao=manterVoucher&idVou='.$value->idVou.'" class="btn btn-primary btn-xs">Editar</a>';
        $html .= '</td>';
        $html .= '<td align="center">';
		$html .= '<a data-toggle="modal" data-id="'.$value->idVou.'" title="Deletar Voucher" class="open-AddBookDialog btn btn-danger btn-xs" href="#addBookDialog">Deletar</a></td>';
        //$html .= '<a href="?controle=Categoria&acao=apagarCategoria&idCat='.$value->idCat.'" class="btn btn-danger btn-xs">Apagar</a>';
        //$html .= '</td>';   
		$html .=  '</tr>';
		$var[] = $html;
		
	}
	
			$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
			
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