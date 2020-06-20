<?php

	session_start();
	include '../lib/conexao.php';
	include '../lib/function.php';
	$opcao = isset($_POST['opcao']) ? $_POST['opcao'] : '';
	$dataini = isset($_POST['dataini']) ? $_POST['dataini'] : '';
	$datafim = isset($_POST['datafim']) ? $_POST['datafim'] : '';
	if($datafim !== '')
	$datafim = date('d-m-Y', strtotime($datafim));
	if($dataini !== '')
	$dataini = date('d-m-Y', strtotime($dataini));
	$palavraChave = isset($_POST['palavraChave']) ? protecao($_POST['palavraChave']) : '0';
	$tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
	//$_SESSION['idPes'] = 3;
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	
	
	
	$st_query  = "SELECT DISTINCT TQ.*, ";
	$st_query .= "tb_pessoa.*, ";
	$st_query .="tb_transporte.*, ";
	$st_query .= "(round((AVG(tb_avaliacao.valorAva1 + "; 
	$st_query .= "tb_avaliacao.valorAva2  + ";
	$st_query .= "tb_avaliacao.valorAva3  + ";
	$st_query .= "tb_avaliacao.valorAva4  + ";
	$st_query .= "tb_avaliacao.valorAva5) / 5))) as media, ";
	$st_query .= "(select count(*) from tb_lance t1 where t1.vencedorLan = 'S' ";
	$st_query .= "and t1.tb_pessoa_idPes = TQ.tb_pessoa_idPes) AS qtdeTransportes ";
	$st_query .= "FROM tb_lance ";
	$st_query .= "TQ INNER JOIN tb_pessoa ON tb_pessoa.idPes = TQ.tb_pessoa_idPes ";
	$st_query .= "INNER JOIN tb_transporte ON TQ.tb_transporte_idTransp = tb_transporte.idTransp "; 
	$st_query .= "LEFT JOIN tb_avaliacao ON tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
	$st_query .= "WHERE 0 = 0 ";
	$st_query .= "AND TQ.dataLan = (SELECT MAX(dataLan) FROM tb_lance WHERE tb_pessoa_idPes = TQ.tb_pessoa_idPes AND tb_lance.tb_transporte_idTransp = tb_transporte.idTransp GROUP BY tb_pessoa_idPes) ";
	$st_query .= "AND TQ.tb_pessoa_idPes = '$idPes' ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(TQ.dataLan as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(TQ.dataLan as Date) <= '$datafim' ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_pessoa.primeiroNomePes like '%$palavraChave%'";
		$st_query .= "GROUP BY TQ.dataLan, ";
		$st_query .= "TQ.idLan, ";
		$st_query .= "TQ.tb_pessoa_idPes, ";
		$st_query .= "TQ.tb_transporte_idTransp, ";
		$st_query .= "TQ.valorLan, ";
		$st_query .= "TQ.vencedorLan, ";
		$st_query .= "tb_pessoa.cpfCnpjPes, ";
		$st_query .= "tb_pessoa.dataCadastroPes, ";
		$st_query .= "tb_pessoa.emailPes, ";
		$st_query .= "tb_pessoa.fotoPes, ";
		$st_query .= "tb_pessoa.idPes, ";
		$st_query .= "tb_pessoa.primeiroNomePes, ";
		$st_query .= "tb_pessoa.senhaPes, ";
		$st_query .= "tb_pessoa.sobreNomePes, ";
		$st_query .= "tb_pessoa.tb_endereco_idEnd, ";
		$st_query .= "tb_pessoa.tb_perfil_idPer, ";
		$st_query .= "tb_pessoa.tb_Status_idSta, ";
		$st_query .= "tb_pessoa.telefoneCelularPes, ";
		$st_query .= "tb_pessoa.telefoneFixoPes, ";
		$st_query .= "tb_transporte.comentarioAdiTransp, ";
		$st_query .= "tb_transporte.dataCadastroTransp, ";
		$st_query .= "tb_transporte.dataRetiradaTransp, ";
		$st_query .= "tb_transporte.descricaoTransp, ";
		$st_query .= "tb_transporte.horaRetiradaTransp, ";
		$st_query .= "tb_transporte.idTransp, ";
		$st_query .= "tb_transporte.motivoCancelamentoTransp, ";
		$st_query .= "tb_transporte.numAjudantesTransp, ";
		$st_query .= "tb_transporte.precoMaxiTransp, ";
		$st_query .= "tb_transporte.tb_categoria_idCat, ";
		$st_query .= "tb_transporte.tb_endereco_transporte_idEndTran, ";
		$st_query .= "tb_transporte.tb_pessoa_idPes, ";
		$st_query .= "tb_transporte.tb_statusTransp_idStaTransp ";
		$st_query .= " order by TQ.dataLan desc; ";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	
	$st_query  = "SELECT DISTINCT TQ.*, ";
	$st_query .= "tb_pessoa.*, ";
	$st_query .="tb_transporte.*, ";
    $st_query .= "(SELECT round((AVG(tb_avaliacao.valorAva1 ";
   	$st_query .= "+ tb_avaliacao.valorAva2 ";
    $st_query .= "+ tb_avaliacao.valorAva3 ";
	$st_query .= "+ tb_avaliacao.valorAva4 ";
	$st_query .= "+ tb_avaliacao.valorAva5) / 5)) ";
	$st_query .= "FROM tb_avaliacao ";
	$st_query .= "INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
	$st_query .= "INNER JOIN tb_lance ON tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
	$st_query .= "where tb_lance.tb_pessoa_idPes = TQ.tb_pessoa_idPes) as media, ";
	$st_query .= "(select count(*) from tb_lance t1 where t1.vencedorLan = 'S' ";
	$st_query .= "and t1.tb_pessoa_idPes = TQ.tb_pessoa_idPes) AS qtdeTransportes ";
	$st_query .= "FROM tb_lance TQ ";
	$st_query .= "INNER JOIN tb_transporte ON TQ.tb_transporte_idTransp = tb_transporte.idTransp "; 
	$st_query .= "INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_transporte.tb_pessoa_idPes ";
	$st_query .= "WHERE 0 = 0 ";
	$st_query .= "AND TQ.tb_pessoa_idPes = '$idPes' ";
	$st_query .= "AND TQ.dataLan = (SELECT MAX(dataLan) FROM tb_lance WHERE tb_pessoa_idPes = TQ.tb_pessoa_idPes AND tb_lance.tb_transporte_idTransp = tb_transporte.idTransp GROUP BY tb_pessoa_idPes) ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(TQ.dataLan as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(TQ.dataLan as Date) <= '$datafim' ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_pessoa.primeiroNomePes like '%$palavraChave%'";
		$st_query .= "GROUP BY TQ.dataLan, ";
		$st_query .= "TQ.idLan, ";
		$st_query .= "TQ.tb_pessoa_idPes, ";
		$st_query .= "TQ.tb_transporte_idTransp, ";
		$st_query .= "TQ.valorLan, ";
		$st_query .= "TQ.vencedorLan, ";
		$st_query .= "tb_pessoa.cpfCnpjPes, ";
		$st_query .= "tb_pessoa.dataCadastroPes, ";
		$st_query .= "tb_pessoa.emailPes, ";
		$st_query .= "tb_pessoa.fotoPes, ";
		$st_query .= "tb_pessoa.idPes, ";
		$st_query .= "tb_pessoa.primeiroNomePes, ";
		$st_query .= "tb_pessoa.senhaPes, ";
		$st_query .= "tb_pessoa.sobreNomePes, ";
		$st_query .= "tb_pessoa.tb_endereco_idEnd, ";
		$st_query .= "tb_pessoa.tb_perfil_idPer, ";
		$st_query .= "tb_pessoa.tb_Status_idSta, ";
		$st_query .= "tb_pessoa.telefoneCelularPes, ";
		$st_query .= "tb_pessoa.telefoneFixoPes, ";
		$st_query .= "tb_transporte.comentarioAdiTransp, ";
		$st_query .= "tb_transporte.dataCadastroTransp, ";
		$st_query .= "tb_transporte.dataRetiradaTransp, ";
		$st_query .= "tb_transporte.descricaoTransp, ";
		$st_query .= "tb_transporte.horaRetiradaTransp, ";
		$st_query .= "tb_transporte.idTransp, ";
		$st_query .= "tb_transporte.motivoCancelamentoTransp, ";
		$st_query .= "tb_transporte.numAjudantesTransp, ";
		$st_query .= "tb_transporte.precoMaxiTransp, ";
		$st_query .= "tb_transporte.tb_categoria_idCat, ";
		$st_query .= "tb_transporte.tb_endereco_transporte_idEndTran, ";
		$st_query .= "tb_transporte.tb_pessoa_idPes, ";
		$st_query .= "tb_transporte.tb_statusTransp_idStaTransp ";
	    $st_query .= " order by dataLan desc LIMIT $registros OFFSET $inicio; ";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';
	$html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="example">';
	$html .= '<link rel="stylesheet" href="template/css/star-ratingview.css" media="all" rel="stylesheet" type="text/css"/>';
	$html .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>';
	$html .= '<script src="template/js/star-ratingview.js" type="text/javascript"></script>';
    $html .= '<thead>';
    $html .= '<tr>';
	$html .= '<th style="text-align:center;">Assunto</th>';
    $html .= '<th style="text-align:center;">Cliente</th>';
    $html .= '<th style="text-align:center;">Meu Lance</th>';
    $html .= '<th style="text-align:center;">Última Mensagem as</th>';
    $html .= '<th class="text-center" colspan="2" style="text-align:center;">Ações</th>';                                                      
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		
		$html .= '<tr>';
		$html .= '<td style="text-align:center;">';
    	$html .=  $value->descricaoTransp;
    	$html .=  '</td>';
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
		$html .= '<br>';
		$html .= '</div>';
		$html .= '</div>';
    	$html .= '</td>';
    	$html .= '<td style="text-align:center;">';
    	$html .=  'R$ '.number_format($value->valorLan, 2, ',', '.');
    	$html .=  '</td>';
		$html .= '<td style="text-align:center;">';
		$st_queryData = "select dataMen from tb_mensagem where tb_mensagem.tb_lance_idLan = $value->idLan ORDER BY dataMen DESC LIMIT 1";
		$o_dataData = $o_db->query($st_queryData);
		$o_retData = $o_dataData->fetchObject();
		$str_Data = '';
		if (!empty($o_retData))
		{
			$str_Data = $o_retData->dataMen;
			$html .=  date('d/m/Y H:i:s', strtotime($str_Data));
		}
    	
    	$html .= '</td>';
    	//$html .=  '<td align="center">';
   		//$html .=   '<a href="?controle=Lance&acao=aceitarLance&idLan='.$value->idLan.'" class="btn btn btn-success btn-xs">Aceitar</a>';
   		//$html .= '<td style="text-align:center;"><button type="button" class="btn" data-toggle="collapse" data-target="#collapseme'.$value->idLan.'">';
   		$html .= '</td>';
    	//$html .= '</td> ';
    	$html .= '<td style="text-align:center;"><a type="button" id="packageDetails'.$value->idLan.'" name="packageDetails'.$value->idLan.'" class="btn btn-info btn-xs" class="accordion-toggle" data-toggle="collapse" data-parent="#OrderPackages" data-target=".packageDetails'.$value->idLan.'" onclick=fncJsonMensagem('.$value->idLan.');>Ver Mensagens</a></td>';
		$html .=  '</tr>';
		//$html .= '<tr id="collapseme'.$value->idLan.'" class="collapse out"><td><div>Should be collapsed</div></td></tr>';
		$html .= '<tr>';
        $html .= '<td colspan="10" class="hiddenRow">';
		$html .= '<div class="accordion-body collapse packageDetails'.$value->idLan.'" id="accordion'.$value->idLan.'">';
		$html .= '<div class="tab-pane" id="add-comment">';
        $html .= '<form  role="form"  id="form'.$value->idLan.'" name="form'.$value->idLan.'" enctype="multipart/form-data">';
        $html .= '<div class="form-group">';
        $html .= '<label for="email" class="col-sm-2 control-label">Mensagem</label>';
        $html .= '<div class="col-sm-10">';
        $html .= '<textarea class="form-control" name="addComment'.$value->idLan.'" id="addComment'.$value->idLan.'" rows="5"></textarea>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<div class="col-sm-offset-2 col-sm-10">';                   
        $html .= '<a class="btn btn-success btn-circle text-uppercase"  id="submitComment'.$value->idLan.'" mame="submitComment'.$value->idLan.'" onclick=fncCadastrarMensagem('.$value->idLan.');><span class="glyphicon glyphicon-send"></span>Confirmar</a>';
        $html .= '</div>';
        $html .= '</div>';          
        $html .= '</form>';
        $html .= '</div>';
		$html .= '<div id="mensagemLance'.$value->idLan.'">';
		$html .= '</div>';
		$html .= '</div>';
       	$html .= '</td>';
		$html .= '</tr>';
		$var[] = $html;
		
	}
	
			$html .= '</tbody>';
			$html .= '</table>';
			
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