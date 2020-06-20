<?php
	session_start();
	include '../lib/conexao.php';
    include '../lib/function.php';
	$palavraChave = isset($_POST['palavraChave']) ? protecao($_POST['palavraChave']) : '0';
	$status  =  isset($_POST['status']) ? $_POST['status']  :  '';
	$dataini = isset($_POST['dataini']) ? $_POST['dataini'] : '';
	$datafim = isset($_POST['datafim']) ? $_POST['datafim'] : '';
	if($datafim !== '')
	$datafim = date('d-m-Y', strtotime($datafim));
	if($dataini !== '')
	$dataini = date('d-m-Y', strtotime($dataini));
	$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : '0';
	$estado = isset($_POST['estado']) ? $_POST['estado'] : '0';
	$tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
	//$_SESSION['idPes'] = 3;
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	
		$st_query =  "SELECT tb_pessoa.*, tb_endereco.*, tb_cidade.*, tb_estado.*, tb_status.* FROM tb_pessoa ";
		$st_query .= " INNER JOIN tb_status ON tb_status.idSta = tb_pessoa.tb_Status_idSta ";
		$st_query .= " LEFT JOIN tb_endereco ON tb_endereco.idEnd = tb_pessoa.tb_endereco_idEnd ";
		$st_query .= " INNER JOIN tb_cidade ON tb_cidade.idCid = tb_endereco.tb_Cidade_idCid ";
		$st_query .= " INNER JOIN tb_estado ON tb_estado.idEst = tb_endereco.tb_Estado_idEst ";
		$st_query .= " INNER JOIN tb_perfil ON tb_perfil.idPer = tb_pessoa.tb_perfil_idPer ";
		$st_query .= " where 0 = 0 ";
		$st_query .= "and tb_perfil.codigoPer = 'T' ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(tb_pessoa.dataCadastroPes as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(tb_pessoa.dataCadastroPes as Date) <= '$datafim' ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_pessoa.primeiroNomePes like '%$palavraChave%' ";
	if (isset($status) and $status !== NULL and $status !== '')
		$st_query .= " and tb_status.idSta = $status";
	if (isset($cidade) and $cidade !== NULL and $cidade !== '0')
		$st_query .= " and tb_endereco.tb_Cidade_idCid = $cidade";
	if (isset($estado) and $estado !== NULL and $estado !== '0')
		$st_query .= " and tb_endereco.tb_Estado_idEst = $estado";
	$st_query .= "ORDER BY tb_pessoa.primeiroNomePes DESC ;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	
		$st_query =  "SELECT tb_pessoa.*, tb_endereco.*, tb_cidade.*, tb_estado.*, tb_status.* FROM tb_pessoa ";
		$st_query .= " INNER JOIN tb_status ON tb_status.idSta = tb_pessoa.tb_Status_idSta ";
		$st_query .= " LEFT join tb_endereco ON tb_endereco.idEnd = tb_pessoa.tb_endereco_idEnd ";
		$st_query .= " INNER JOIN tb_cidade ON tb_cidade.idCid = tb_endereco.tb_Cidade_idCid ";
		$st_query .= " INNER JOIN tb_estado ON tb_estado.idEst = tb_endereco.tb_Estado_idEst ";
		$st_query .= " INNER JOIN tb_perfil ON tb_perfil.idPer = tb_pessoa.tb_perfil_idPer ";
		$st_query .= " where 0 = 0 ";
		$st_query .= "and tb_perfil.codigoPer = 'T' ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and tb_pessoa.dataCadastroPes >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and tb_pessoa.dataCadastroPes <= '$datafim' ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_pessoa.primeiroNomePes like '%$palavraChave%' ";
	if (isset($status) and $status !== NULL and $status !== '')
		$st_query .= " and tb_status.idSta = $status";
	if (isset($cidade) and $cidade !== NULL and $cidade !== '0')
		$st_query .= " and tb_endereco.tb_Cidade_idCid = $cidade";
	if (isset($estado) and $estado !== NULL and $estado !== '0')
		$st_query .= " and tb_endereco.tb_Estado_idEst = $estado";
	$st_query .= "ORDER BY tb_pessoa.primeiroNomePes ASC LIMIT $registros OFFSET $inicio;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';
	$html .= '<div class="table-responsive">';
	$html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="example" style="font-size: 10px">';
    $html .= '<thead>';
    $html .= '<tr>';
	$html .= '<th>Codigo</th>';
    $html .= '<th>Foto</th>';
    $html .= '<th>Primeiro Nome</th>';
    $html .= '<th>Sobrenome</th>';
    $html .= '<th>E-mail</th>';
    $html .= '<th>CNPJ/CPF</th>';
    $html .= '<th>Data Cadastro</th>';
    $html .= '<th>Telefone Fixo</th>';
    $html .= '<th>Telefone Celular</th>';
    $html .= '<th>Status</th>';
    $html .= '<th>Endereço</th>';
    $html .= '<th class="text-center" colspan="1">Ações</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		
		$html .= '<tr>';
		$html .= '<td style="text-align:center;" >'.$value->idPes.'</td>';
		$html .= '<td style="text-align:center;">';
		$html .= '<div class="text-center">';
		$html .= '<div>';
		if($value->fotoPes)
		{
		$html .= '<img class="img-rounded" height="50px" width="50px" src="'.$value->fotoPes.'">';
		}else{
		$html .= '<img class="img-rounded" height="50px" width="50px" src="http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png">';	  	
		}
		$html .= '</div>';
		$html .= '<div>';
        $html .= '</td>';
        $html .= '<td style="text-align:center;">'.$value->primeiroNomePes.'</td>';
        $html .= '<td style="text-align:center;">'.$value->sobreNomePes.'</td>';
        $html .= '<td style="text-align:center;">'.$value->emailPes.'</td>';
        $html .= '<td style="text-align:center;">'.$value->cpfCnpjPes.'</td>';
        $html .= '<td style="text-align:center;">'.date('d/m/Y', strtotime($value->dataCadastroPes)).'</td>';
        $html .= '<td style="text-align:center;">'.$value->telefoneFixoPes.'</td>';
        $html .= '<td style="text-align:center;">'.$value->telefoneCelularPes.'</td>';
        $html .= '<td style="text-align:center;">'.$value->nomeSta.'</td>';
		$html .= '<td>'.utf8_encode($value->ruaEnd) .' - '.  utf8_encode($value->bairroEnd) .' '. utf8_encode($value->complementoEnd) . ' - ' .$value->cepEnd .' - ' .utf8_encode($value->nomeCid).' - ' .utf8_encode($value->nomeEst) .'</td>';
        $html .= '<td align="center">';
        $html .= '<a href="?controle=Pessoa&acao=manterTransportador&idPes='.$value->idPes.'" class="btn btn-primary btn-xs">Editar</a>';
        $html .= '</td>';
        //$html .= '<td align="center">';
        //$html .= '<a href="?controle=Pessoa&acao=apagarTransportador&idPes='.$value->idPes.'" class="btn btn-danger btn-xs">Apagar</a>';
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
		$html .= '<td colspan="12">';
		$html .= '<div class="text-center"><h4>Nenhum registro econtrado!</h4></div>';
		$html .= '</td>';
		$var[] = $html;
	}
   			echo json_encode($var);
?>