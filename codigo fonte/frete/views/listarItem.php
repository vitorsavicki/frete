<?php
	session_start();
	include '../lib/conexao.php';
    include '../lib/function.php';
	$palavraChave = isset($_POST['palavraChave']) ? protecao($_POST['palavraChave']) : '0';
	$categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '0';
	$tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
	//$_SESSION['idPes'] = 3;
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	
	$st_query =  "SELECT * FROM tb_item ";
		$st_query .= " INNER JOIN tb_categoria ON tb_categoria.idCat = tb_item.tb_categoria_idCat ";
		$st_query .= "where 0 = 0 ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_item.nomeItem like '%$palavraChave%' ";
	if (isset($categoria) and $categoria !== NULL and $categoria !== '0')
		$st_query .= " and tb_categoria.idCat = $categoria";
	$st_query .= "ORDER BY tb_item.nomeItem DESC ;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	
	$st_query =  "SELECT * FROM tb_item ";
		$st_query .= " INNER JOIN tb_categoria ON tb_categoria.idCat = tb_item.tb_categoria_idCat ";
		$st_query .= "where 0 = 0 ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_item.nomeItem like '%$palavraChave%' ";
	if (isset($categoria) and $categoria !== NULL and $categoria !== '0')
		$st_query .= " and tb_categoria.idCat = $categoria";
	$st_query .= " ORDER BY tb_item.nomeItem DESC LIMIT $registros OFFSET $inicio;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';
	$html .= '<div class="table-responsive">';
	$html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="example">';
    $html .= '<thead>';
    $html .= '<tr>';
	$html .= '<th style="text-align:center;">ID</th>';                                  
	$html .= '<th style="text-align:center;">Nome</th>';                               
	$html .= '<th style="text-align:center;">Categoria</th>';                            
	$html .= '<th class="text-center" colspan="2">Ações</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		
		$html .= '<tr>';
		$html .= '<td style="text-align:center;">'.$value->idItem.'</td>';                                                                                                                        
		$html .= '<td style="text-align:center;">'.utf8_encode($value->nomeItem).'</td>';
		$html .= '<td style="text-align:center;">'.utf8_encode($value->nomeCat).'</td>';                                                                                                                                                                                                                                                      
		$html .= '<td align="center"><a href="?controle=Item&acao=manterItem&idItem='.$value->idItem.'" class="btn btn-primary btn-xs">Editar</a></td>';
		$html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idItem.'" title="Deletar Item" class="open-AddBookDialog btn btn-danger btn-xs" href="#addBookDialog">Deletar</a></td>';                                                                                                                          
		//$html .= '<td align="center"><a href="?controle=Item&acao=apagarItem&idItem='.$value->idItem.'" class="btn btn-danger btn-xs">Apagar</a></td>';  
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