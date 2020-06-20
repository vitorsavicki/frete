<?php

	session_start();
	include '../lib/conexao.php';
	include '../lib/function.php';
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
    $cidOrigem = isset($_POST['cidadeOrigem']) ? $_POST['cidadeOrigem'] : '0';
    $_SESSION['cidadeOrigem'] = $cidOrigem;
    $cidDestino = isset($_POST['cidadeDestino']) ? $_POST['cidadeDestino'] : '0';
    $_SESSION['cidadeDestino'] = $cidDestino;
    $estOrigem = isset($_POST['estadoOrigem']) ? $_POST['estadoOrigem'] : '0';
    $_SESSION['estadoOrigem'] = $estOrigem;
    $estDestino = isset($_POST['estadoDestino']) ? $_POST['estadoDestino'] : '0';
    $_SESSION['estadoDestino'] = $estDestino;
    $palavraChave = isset($_POST['palavraChave']) ? protecao($_POST['palavraChave']) : '0';
    $_SESSION['palavraChave'] = $palavraChave;
    $situacaoTransp = isset($_POST['idStaTransp']) ? $_POST['idStaTransp'] : '0';
    $_SESSION['situacaoTransp'] = $situacaoTransp;
    $quality = isset($_POST['myRadio']) ? $_POST['myRadio'] : '';
    $_SESSION['quality'] = $quality;
	$tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
	//$_SESSION['idPes'] = 3;
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	
	$st_query =  "	SELECT DISTINCT TQ.*, tb_transporte.*,tb_pessoa.*, ";
	$st_query .= "	tb_endereco_transporte.*, ";
	$st_query .= "	tb_categoria.nomeCat, ";
	$st_query .= "	cidOri.nomeCid as cidOrigem, ";
	$st_query .= "	estOri.ufEst as estOrigem, ";
	$st_query .= "	cidDes.nomeCid as cidDestino, ";
	$st_query .= "	estDes.ufEst as estDestino, ";
	$st_query .= "	tb_statusTransp.*, ";
	$st_query .= "  (select count(*) from tb_lance t1 where t1.vencedorLan = 'S' and t1.tb_pessoa_idPes = TQ.tb_pessoa_idPes) qtdeTransportes ";
	$st_query .= "	FROM tb_lance TQ ";
	$st_query .= "	INNER JOIN tb_transporte ON TQ.tb_transporte_idTransp = tb_transporte.idTransp ";
  	$st_query .= "	INNER JOIN tb_pessoa ON tb_transporte.tb_pessoa_idPes = tb_pessoa.idPes ";
	$st_query .= "	inner join tb_statusTransp on tb_statusTransp.idStaTransp = tb_transporte.tb_statusTransp_idStaTransp ";
	$st_query .= "	inner join tb_categoria on tb_transporte.tb_categoria_idcat = tb_categoria.idcat ";
	$st_query .= "	left join tb_endereco_transporte on tb_transporte.tb_endereco_transporte_idEndTran =  tb_endereco_transporte.idEndTran ";
	$st_query .= "	left join tb_cidade as cidOri on cidOri.idCid = tb_endereco_transporte.tb_cidadeOrigem_IdCid ";
	$st_query .= "	left join tb_estado as estOri on estOri.idEst = cidOri.tb_Estado_idEst ";
	$st_query .= "	left join tb_cidade as cidDes on cidDes.idCid = tb_endereco_transporte.tb_cidadeDestino_IdCid ";
	$st_query .= "	left join tb_estado as estDes on estDes.idEst = cidDes.tb_Estado_idEst ";
	$st_query .= "  left join tb_avaliacao on tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
	$st_query .= "	WHERE TQ.tb_pessoa_idPes = '$idPes' ";	
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(tb_transporte.dataRetiradaTransp as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(tb_transporte.dataRetiradaTransp as Date) <= '$datafim' ";
	if (isset($categoria) and $categoria !== NULL and $categoria !== '0')
		$st_query .= " and tb_transporte.tb_categoria_idcat = $categoria";
	if (isset($cidOrigem) and $cidOrigem !== NULL and $cidOrigem !== '0')
		$st_query .= " and tb_endereco_transporte.tb_cidadeOrigem_IdCid = $cidOrigem";
	if (isset($cidDestino) and $cidDestino !== NULL and $cidDestino !== '0')
		$st_query .= " and tb_endereco_transporte.tb_cidadeDestino_IdCid = $cidDestino";
	if (isset($estOrigem) and $estOrigem !== NULL and $estOrigem !== '0')
		$st_query .= " and estOri.idEst = $estOrigem";
	if (isset($estDestino) and $estDestino !== NULL and $estDestino !== '0')
		$st_query .= " and estDes.idEst = $estDestino";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_transporte.descricaoTransp like '%$palavraChave%'";
    if (isset($situacaoTransp) and $situacaoTransp !== NULL and $situacaoTransp !== '0')
        $st_query .= " and tb_statusTransp.idStaTransp = $situacaoTransp";
    if (isset($quality) and $quality !== NULL and $quality !== '')
        $st_query .= " and TQ.vencedorLan = '$quality'";
	$st_query .= " order by dataLan desc;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	
	$st_query =  "	SELECT DISTINCT TQ.*, tb_transporte.*,tb_pessoa.*, ";
	$st_query .= "	tb_endereco_transporte.*, ";
	$st_query .= "	tb_categoria.nomeCat, ";
	$st_query .= "	cidOri.nomeCid as cidOrigem, ";
	$st_query .= "	estOri.ufEst as estOrigem, ";
	$st_query .= "	cidDes.nomeCid as cidDestino, ";
	$st_query .= "	estDes.ufEst as estDestino, ";
	$st_query .= "	tb_statusTransp.*, ";
	$st_query .= "  (select count(*) from tb_lance t1 where t1.vencedorLan = 'S' and t1.tb_pessoa_idPes = TQ.tb_pessoa_idPes) qtdeTransportes ";
	$st_query .= "	FROM tb_lance TQ ";
	$st_query .= "	INNER JOIN tb_transporte ON TQ.tb_transporte_idTransp = tb_transporte.idTransp ";
  	$st_query .= "	INNER JOIN tb_pessoa ON tb_transporte.tb_pessoa_idPes = tb_pessoa.idPes ";
	$st_query .= "	inner join tb_statusTransp on tb_statusTransp.idStaTransp = tb_transporte.tb_statusTransp_idStaTransp ";
	$st_query .= "	inner join tb_categoria on tb_transporte.tb_categoria_idcat = tb_categoria.idcat ";
	$st_query .= "	left join tb_endereco_transporte on tb_transporte.tb_endereco_transporte_idEndTran =  tb_endereco_transporte.idEndTran ";
	$st_query .= "	left join tb_cidade as cidOri on cidOri.idCid = tb_endereco_transporte.tb_cidadeOrigem_IdCid ";
	$st_query .= "	left join tb_estado as estOri on estOri.idEst = cidOri.tb_Estado_idEst ";
	$st_query .= "	left join tb_cidade as cidDes on cidDes.idCid = tb_endereco_transporte.tb_cidadeDestino_IdCid ";
	$st_query .= "	left join tb_estado as estDes on estDes.idEst = cidDes.tb_Estado_idEst ";
	$st_query .= "  left join tb_avaliacao on tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
	$st_query .= "	WHERE TQ.tb_pessoa_idPes = '$idPes' ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(tb_transporte.dataRetiradaTransp as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(tb_transporte.dataRetiradaTransp as Date) <= '$datafim' ";
	if (isset($categoria) and $categoria !== NULL and $categoria !== '0')
		$st_query .= " and tb_transporte.tb_categoria_idcat = $categoria";
	if (isset($cidOrigem) and $cidOrigem !== NULL and $cidOrigem !== '0')
		$st_query .= " and tb_endereco_transporte.tb_cidadeOrigem_IdCid = $cidOrigem";
	if (isset($cidDestino) and $cidDestino !== NULL and $cidDestino !== '0')
		$st_query .= " and tb_endereco_transporte.tb_cidadeDestino_IdCid = $cidDestino";
	if (isset($estOrigem) and $estOrigem !== NULL and $estOrigem !== '0')
		$st_query .= " and estOri.idEst = $estOrigem";
	if (isset($estDestino) and $estDestino !== NULL and $estDestino !== '0')
		$st_query .= " and estDes.idEst = $estDestino";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_transporte.descricaoTransp like '%$palavraChave%'";
     if (isset($situacaoTransp) and $situacaoTransp !== NULL and $situacaoTransp !== '0')
        $st_query .= " and tb_statusTransp.idStaTransp = $situacaoTransp";
     if (isset($quality) and $quality !== NULL and $quality !== '')
        $st_query .= " and TQ.vencedorLan = '$quality'";
	$st_query .= " order by dataLan desc LIMIT $registros OFFSET $inicio;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';
	$html .= '<div class="table-responsive">';
	$html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="example">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;">Detalhes</th>';
    $html .= '<th style="text-align:center;">Foto</th>';
    $html .= '<th style="text-align:center;">Data do lance</th>';
    $html .= '<th style="text-align:center;">Data do transporte</th>';
    $html .= '<th style="text-align:center;">Descrição</th>';
	$html .= '<th style="text-align:center;">Endereço de Origem</th>';
	$html .= '<th style="text-align:center;">Endereço de Destino</th>';
    $html .= '<th style="text-align:center;">Valor do Lance</th>';
	$html .= '<th style="text-align:center;">Situação Transporte</th>';
	$html .= '<th style="text-align:center;">Situação Do Lance</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		$st_queryIMG = "select caminhoImgTran from tb_imagens_transporte where tb_transporte_idTransp = $value->idTransp LIMIT 1";
		$o_dataIMG = $o_db->query($st_queryIMG);
		$o_retIMG = $o_dataIMG->fetchObject();
		$str_img = '';
		if (!empty($o_retIMG))
		{
			$str_img = $o_retIMG->caminhoImgTran;
		}		
		$html .= '<tr>';
		$html .= '<td style="text-align:center;">';
		$html .= '<a class="btn btn-primary btn-xs" href="?controle=Transporte&acao=detalhesTransporte&idTransp='.$value->idTransp.'&consulta=S&opcao=meusLances" onclick="load()">Detalhes</a></td>';
		if ($str_img !== '' and $str_img !== NULL)
			$html .= '<td style="text-align:center;"><img style="width:80px;" src="'.$str_img.'"></td>';
		else
			$html .= '<td style="text-align:center;"><img class="img-rounded" style="width:80px;" src="template/images/semfoto.png"></td>';
		$html .= '<td style="text-align:center;">';
		$html .= date('d/m/Y', strtotime($value->dataLan)).'</td>';
		$html .= '<td style="text-align:center;">';
		$html .= date('d/m/Y', strtotime($value->dataRetiradaTransp)).'</td>';	
		$html .= '<td>'.$value->descricaoTransp.'</td>';
		$html .= '<td>'.utf8_encode($value->ruaOrigemEndTran).' - '.utf8_encode($value->bairroOrigemEndTran).' - '.utf8_encode($value->cidOrigem).'/'.utf8_encode($value->estOrigem).'</td>';
		$html .= '<td>'.utf8_encode($value->ruaDestinoEndTran).' - '.utf8_encode($value->bairroDestinoEndTran).' - '.utf8_encode($value->cidDestino).'/'.utf8_encode($value->estDestino).'</td>';
		$html .= '<td style="text-align:center;">';
		$html .= number_format($value->valorLan, 2, ',', '.').'</td>';
		$html .= '<td style="text-align:center;">';
		$html .= utf8_encode($value->nomeStaTransp);
		$html .= '</td>';
		if($value->vencedorLan == 'S'){
		$html .= '<td style="text-align:center; background-color: #CCFF99;">';
		$html .= 'Ganho';
		$html .= '</td>';
		}
		else if($value->vencedorLan == 'A'){
		$html .= '<td style="text-align:center;">';
		$html .= 'Aguardando';
		$html .= '</td>';
		}
		else if($value->vencedorLan == 'N'){
		$html .= '<td style="text-align:center; background-color: #FF8080">';
		$html .= 'Perdido';
		$html .= '</td>';
		}
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