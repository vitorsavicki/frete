<?php
	session_start();
	include '../lib/conexao.php';
    include '../lib/function.php';
	$opcao = isset($_POST['opcao']) ? $_POST['opcao'] : '';
	$dataini = isset($_POST['dataini']) ? $_POST['dataini'] : '';
    $_SESSION['dataini'] = $dataini;
	$datafim = isset($_POST['datafim']) ? $_POST['datafim'] : '';
    $_SESSION['datafim'] = $datafim;
	if($datafim !== '')
	$datafim = date('d-m-Y', strtotime($datafim));
	if($dataini !== '')
	$dataini = date('d-m-Y', strtotime($dataini));
	$categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '0';
    $_SESSION['categoria'] = $categoria;
	$palavraChave = isset($_POST['palavraChave']) ? protecao($_POST['palavraChave']) : '0';
    $_SESSION['palavraChave'] = $palavraChave;
	$tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
	//$_SESSION['idPes'] = 3;
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	
	       $st_query =  " SELECT tb_avaliacao.*,tb_pessoa.*,tb_transporte.*, ";
           $st_query .= "(round((AVG(tb_avaliacao.valorAva1 + tb_avaliacao.valorAva2 + tb_avaliacao.valorAva3 + tb_avaliacao.valorAva4 + tb_avaliacao.valorAva5) / 5))) as media "; 
           $st_query .= "FROM tb_avaliacao ";
           $st_query .= "INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
           $st_query .= "INNER JOIN tb_lance ON tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
           $st_query .= "INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_transporte.tb_pessoa_idPes ";
           $st_query .= "where 0 = 0 ";
           $st_query .= "AND tb_lance.vencedorlan = 'S' ";
           $st_query .= "and tb_lance.tb_pessoa_idPes = '$idPes' "; 
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(tb_avaliacao.dataAva as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(tb_avaliacao.dataAva as Date) <= '$datafim' ";
	if (isset($categoria) and $categoria !== NULL and $categoria !== '0')
		$st_query .= " and tb_transporte.tb_categoria_idcat = $categoria";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_transporte.descricaoTransp like '%$palavraChave%'";
	$st_query .= "GROUP BY ";
           $st_query .= "tb_pessoa.idPes, ";
           $st_query .= "tb_pessoa.cpfCnpjPes, ";
           $st_query .= "tb_pessoa.dataCadastroPes, ";
           $st_query .= "tb_pessoa.emailPes, tb_pessoa.fotoPes, ";
           $st_query .= "tb_pessoa.primeiroNomePes, tb_pessoa.senhaPes, ";
           $st_query .= "tb_pessoa.sobreNomePes, tb_pessoa.tb_endereco_idEnd, ";
           $st_query .= "tb_pessoa.tb_perfil_idPer, tb_pessoa.tb_Status_idSta, ";
           $st_query .= "tb_pessoa.telefoneCelularPes, tb_pessoa.telefoneFixoPes, ";
           $st_query .= "tb_avaliacao.conteudoAva, ";
           $st_query .= "tb_avaliacao.dataAva, ";
           $st_query .= "tb_avaliacao.idAva, ";
           $st_query .= "tb_avaliacao.tb_status_idStaAva, ";
           $st_query .= "tb_avaliacao.tb_transporte_idTransp, ";
           $st_query .= "tb_avaliacao.valorAva1, ";
           $st_query .= "tb_avaliacao.valorAva2, ";
           $st_query .= "tb_avaliacao.valorAva3, ";
           $st_query .= "tb_avaliacao.valorAva4, ";
           $st_query .= "tb_avaliacao.valorAva5, ";
           $st_query .= "tb_avaliacao.valorAva6, ";
           $st_query .= "tb_avaliacao.valorAva7, ";
           $st_query .= "tb_avaliacao.valorAva8, ";
           $st_query .= "tb_avaliacao.valorAva9, ";
           $st_query .= "tb_avaliacao.valorAva10, ";
           $st_query .= "tb_transporte.comentarioAdiTransp, ";
           $st_query .= "tb_transporte.dataCadastroTransp, ";
           $st_query .= "tb_transporte.dataRetiradaTransp, ";
           $st_query .= "tb_transporte.descricaoTransp, ";
           $st_query .= "tb_transporte.horaRetiradaTransp, ";
           $st_query .= "tb_transporte.idTransp, ";
           $st_query .= "tb_transporte.numAjudantesTransp, ";
           $st_query .= "tb_transporte.precoMaxiTransp, ";
           $st_query .= "tb_transporte.tb_categoria_idCat, ";
           $st_query .= "tb_transporte.tb_endereco_transporte_idEndTran, ";
           $st_query .= "tb_transporte.tb_pessoa_idPes, ";
           $st_query .= "tb_transporte.tb_statusTransp_idStaTransp, ";
           $st_query .= "tb_transporte.motivoCancelamentoTransp ";
           $st_query .= " order by tb_avaliacao.dataAva DESC ";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	
                 $st_query =  " SELECT tb_avaliacao.*,tb_pessoa.*,tb_transporte.*, ";
           $st_query .= "(round((AVG(tb_avaliacao.valorAva1 + tb_avaliacao.valorAva2 + tb_avaliacao.valorAva3 + tb_avaliacao.valorAva4 + tb_avaliacao.valorAva5) / 5))) as media "; 
           $st_query .= "FROM tb_avaliacao ";
           $st_query .= "INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
           $st_query .= "INNER JOIN tb_lance ON tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
           $st_query .= "INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_transporte.tb_pessoa_idPes ";
           $st_query .= "where 0 = 0 ";
           $st_query .= "AND tb_lance.vencedorlan = 'S' ";
           $st_query .= "and tb_lance.tb_pessoa_idPes = '$idPes' "; 
    if (isset($dataini) and $dataini !== NULL and $dataini !== '')
        $st_query .= "and Cast(tb_avaliacao.dataAva as Date) >= '$dataini'";
    if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
        $st_query .= "and Cast(tb_avaliacao.dataAva as Date) <= '$datafim' ";
    if (isset($categoria) and $categoria !== NULL and $categoria !== '0')
        $st_query .= " and tb_transporte.tb_categoria_idcat = $categoria";
    if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
        $st_query .= " and tb_transporte.descricaoTransp like '%$palavraChave%'";
    $st_query .= "GROUP BY ";
           $st_query .= "tb_pessoa.idPes, ";
           $st_query .= "tb_pessoa.cpfCnpjPes, ";
           $st_query .= "tb_pessoa.dataCadastroPes, ";
           $st_query .= "tb_pessoa.emailPes, tb_pessoa.fotoPes, ";
           $st_query .= "tb_pessoa.primeiroNomePes, tb_pessoa.senhaPes, ";
           $st_query .= "tb_pessoa.sobreNomePes, tb_pessoa.tb_endereco_idEnd, ";
           $st_query .= "tb_pessoa.tb_perfil_idPer, tb_pessoa.tb_Status_idSta, ";
           $st_query .= "tb_pessoa.telefoneCelularPes, tb_pessoa.telefoneFixoPes, ";
           $st_query .= "tb_avaliacao.conteudoAva, ";
           $st_query .= "tb_avaliacao.dataAva, ";
           $st_query .= "tb_avaliacao.idAva, ";
           $st_query .= "tb_avaliacao.tb_status_idStaAva, ";
           $st_query .= "tb_avaliacao.tb_transporte_idTransp, ";
           $st_query .= "tb_avaliacao.valorAva1, ";
           $st_query .= "tb_avaliacao.valorAva2, ";
           $st_query .= "tb_avaliacao.valorAva3, ";
           $st_query .= "tb_avaliacao.valorAva4, ";
           $st_query .= "tb_avaliacao.valorAva5, ";
           $st_query .= "tb_avaliacao.valorAva6, ";
           $st_query .= "tb_avaliacao.valorAva7, ";
           $st_query .= "tb_avaliacao.valorAva8, ";
           $st_query .= "tb_avaliacao.valorAva9, ";
           $st_query .= "tb_avaliacao.valorAva10, ";
           $st_query .= "tb_transporte.comentarioAdiTransp, ";
           $st_query .= "tb_transporte.dataCadastroTransp, ";
           $st_query .= "tb_transporte.dataRetiradaTransp, ";
           $st_query .= "tb_transporte.descricaoTransp, ";
           $st_query .= "tb_transporte.horaRetiradaTransp, ";
           $st_query .= "tb_transporte.idTransp, ";
           $st_query .= "tb_transporte.numAjudantesTransp, ";
           $st_query .= "tb_transporte.precoMaxiTransp, ";
           $st_query .= "tb_transporte.tb_categoria_idCat, ";
           $st_query .= "tb_transporte.tb_endereco_transporte_idEndTran, ";
           $st_query .= "tb_transporte.tb_pessoa_idPes, ";
           $st_query .= "tb_transporte.tb_statusTransp_idStaTransp, ";
           $st_query .= "tb_transporte.motivoCancelamentoTransp ";
	       $st_query .= " order by tb_avaliacao.dataAva DESC LIMIT $registros OFFSET $inicio;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html = '';
	$html .=  '<div class="table-responsive">';
	$html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="tblItens" style="font-size: 10px" >';
	$html .= '<link rel="stylesheet" href="template/css/star-ratingview.css" media="all" rel="stylesheet" type="text/css"/>';
    $html .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>';
    $html .= '<script src="template/js/star-ratingview.js" type="text/javascript"></script>';
	$html .= '<thead>';
	$html .= '<tr>';
	$html .= '<th style="text-align:center;" >Avaliação</th>';
	$html .= '<th style="text-align:center;" >Descrição do Transporte</th>';					
	$html .= '<th style="text-align:center;" >Cliente</th>';
	$html .= '<th style="text-align:center;" >Data da Avaliação</th>';
	$html .= '<th style="text-align:center;" >Comentário</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody >';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		
		$html .= '<td style="text-align:center;">';
        $html .= '<input id="valorAva" name="valorAva" type="number" class="rating" min="0" max="10" step="1" data-size="xs"';
        $html .= 'data-symbol="&#xf005;" data-glyphicon="false" data-rating-class="rating-fa" class="required-entry form-required aria-required form-control"';
        $html .= 'readonly="" value="'.$value->media.'" data-clearable="remove">';
        $html .= '</td>';
        $html .= '<td>';
        $html .= $value->descricaoTransp;
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<div class="text-center">';
        $html .= '<div>';
        if($value->fotoPes){
        $html .= '<img class="img-rounded" height="50px" width="50px" src="'.$value->fotoPes.'">';
        }else{
        $html .= '<img class="img-rounded" height="50px" width="50px" src="http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png">';     
        }
        $html .= '</div>';
        $html .= '<div>';
        $html .= $value->primeiroNomePes;
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '<td style="text-align:center;">';
        $html .= date('d/m/Y', strtotime($value->dataAva));
        $html .= '</td>';
        $html .= '<td>';
        $html .= $value->conteudoAva;
        $html .= '</td>';
		$html .= '</tr>';
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
			$html .= '<a  onclick=fncJson('. $pagina_atual .');>Pr&oacute;xima</a>';
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




  

