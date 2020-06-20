<?php

	session_start();
	include '../lib/conexao.php';
	include '../lib/function.php';
	$dataini = isset($_POST['dataini']) ? $_POST['dataini'] : '';
	$datafim = isset($_POST['datafim']) ? $_POST['datafim'] : '';
	if($datafim !== '')
	$datafim = date('d-m-Y', strtotime($datafim));
	if($dataini !== '')
	$dataini = date('d-m-Y', strtotime($dataini));
    $_SESSION['dataini'] = $dataini;
    $_SESSION['datafim'] = $datafim;
	$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : '0';
    $_SESSION['cidade'] = $cidade;
	$estado = isset($_POST['estado']) ? $_POST['estado'] : '0';
    $_SESSION['estado'] = $estado;
	$status = isset($_POST['tb_status']) ? $_POST['tb_status'] : '0';
    $_SESSION['status'] = $status;
    $statusAcesso = isset($_POST['tb_statusAcesso']) ? $_POST['tb_statusAcesso'] : '0';
    $_SESSION['statusAcesso'] = $statusAcesso;
	$palavraChave = isset($_POST['palavraChave']) ? protecao($_POST['palavraChave']) : '0';
    $_SESSION['palavraChave'] = $palavraChave;
	$tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
	//$_SESSION['idPes'] = 3;
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	
	$registros = 5;
	
	$pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
	
	$st_query  = " SELECT tb_mensalidade.*, tb_pessoa.*, tb_endereco.*, tb_cidade.*, tb_estado.*, tb_status.*, tb_situacaoMensalidade.* FROM tb_mensalidade ";
	$st_query .= "INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_mensalidade.tb_pessoa_idPes ";
	$st_query .= "INNER JOIN tb_status ON tb_status.idSta = tb_pessoa.tb_Status_idSta "; 
	$st_query .= " LEFT JOIN tb_endereco ON tb_endereco.idEnd = tb_pessoa.tb_endereco_idEnd ";
	$st_query .= " INNER JOIN tb_cidade ON tb_cidade.idCid = tb_endereco.tb_Cidade_idCid ";
	$st_query .= " INNER JOIN tb_estado ON tb_estado.idEst = tb_endereco.tb_Estado_idEst ";
	$st_query .= "INNER JOIN tb_situacaoMensalidade ON idSit = tb_situacaoMensalidade_idSit ";
	$st_query .= "WHERE 0 = 0 ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(tb_mensalidade.dataVencimentoMensa as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(tb_mensalidade.dataVencimentoMensa as Date) <= '$datafim' ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_pessoa.primeiroNomePes like '%$palavraChave%' ";
	if (isset($cidade) and $cidade !== NULL and $cidade !== '0')
		$st_query .= " and tb_endereco.tb_Cidade_idCid = $cidade";
	if (isset($estado) and $estado !== NULL and $estado !== '0')
		$st_query .= " and tb_endereco.tb_Estado_idEst = $estado";
	if (isset($status) and $status !== NULL and $status !== '0')
		$st_query .= " and tb_situacaoMensalidade.idSit = $status";
	if (isset($statusAcesso) and $statusAcesso !== NULL and $statusAcesso !== '0')
        $st_query .= " and tb_status.idSta = $statusAcesso";
	$st_query .= " order by dataVencimentoMensa desc;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$nRows = count($o_rows);
	$paginas = ceil($nRows / $registros);
	
	$fim = $registros * $pagina_atual;
	$inicio = ($fim - $registros);
	
	$st_query = "";
	
    $st_query  = " SELECT tb_mensalidade.*, tb_pessoa.*, tb_endereco.*, tb_cidade.*, tb_estado.*, tb_status.*, tb_situacaoMensalidade.* FROM tb_mensalidade ";
	$st_query .= " INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_mensalidade.tb_pessoa_idPes ";
	$st_query .= " INNER JOIN tb_status ON tb_status.idSta = tb_pessoa.tb_Status_idSta "; 
	$st_query .= " LEFT JOIN tb_endereco ON tb_endereco.idEnd = tb_pessoa.tb_endereco_idEnd ";
	$st_query .= " INNER JOIN tb_cidade ON tb_cidade.idCid = tb_endereco.tb_Cidade_idCid ";
	$st_query .= " INNER JOIN tb_estado ON tb_estado.idEst = tb_endereco.tb_Estado_idEst ";
	$st_query .= " INNER JOIN tb_situacaoMensalidade ON tb_situacaoMensalidade.idSit = tb_mensalidade.tb_situacaoMensalidade_idSit ";
	$st_query .= " WHERE 0 = 0 ";
	if (isset($dataini) and $dataini !== NULL and $dataini !== '')
		$st_query .= "and Cast(tb_mensalidade.dataVencimentoMensa as Date) >= '$dataini'";
	if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
		$st_query .= "and Cast(tb_mensalidade.dataVencimentoMensa as Date) <= '$datafim' ";
	if (isset($palavraChave) and $palavraChave !== NULL and $palavraChave !== '0' and $palavraChave !== '')
		$st_query .= " and tb_pessoa.primeiroNomePes like '%$palavraChave%' ";
	if (isset($cidade) and $cidade !== NULL and $cidade !== '0')
		$st_query .= " and tb_endereco.tb_Cidade_idCid = $cidade";
	if (isset($estado) and $estado !== NULL and $estado !== '0')
		$st_query .= " and tb_endereco.tb_Estado_idEst = $estado";
	if (isset($status) and $status !== NULL and $status !== '0')
		$st_query .= " and tb_situacaoMensalidade.idSit = $status";
	if (isset($statusAcesso) and $statusAcesso !== NULL and $statusAcesso !== '0')
        $st_query .= " and tb_status.idSta = $statusAcesso";
	$st_query .= " order by dataVencimentoMensa desc LIMIT $registros OFFSET $inicio;";
	
	$o_data = $o_db->query($st_query);
	$o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
	
	$html =  '';
	$html .= '<div class="table-responsive">';
	$html .=  '<table class="table table-bordered table-condensed table-hover table-striped" id="example" style="font-size: 10px">';
    $html .= '<thead>';
    $html .= '<tr>';
  	$html .= '<th>Codigo</th>';
	$html .= '<th>Data Vencimento</th>';
	$html .= '<th>Data Pagamento</th>';
	$html .= '<th>Valor</th>';
	$html .= '<th>Situação da Mensalidade</th>';
	$html .= '<th>Transportador</th>';
	$html .= '<th>CPF/CNPJ</th>';
	$html .= '<th>Situação de Acesso</th>';
	$html .= '<th>Boleto</th>';
	$html .= '<th class="text-center" colspan="3">A&ccedil;&otilde;es</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$var = array();
	if($nRows <> 0){
	foreach ($o_rows as $o_ret => $value)
	{
		
		$html .= '<tr>';
		$html .= '<td align="center">'.$value->idMensa.'</td>';
		$html .= '<td align="center">';
		$html .= date('d/m/Y', strtotime($value->dataVencimentoMensa));	
		$html .= '</td>';
		$html .= '<td align="center">';
		if(date('d/m/Y', strtotime($value->dataPagamentoMensa)) > '01/01/1970'){
		$html .= date('d/m/Y', strtotime($value->dataPagamentoMensa));
		}
		$html .= '</td>';
		$html .= '<td align="right">'.number_format($value->valorMensa, 2, ',', '.').'</td>';
		$html .= '<td align="center">'.$value->descricaoSit.'</td>';
		$html .= '<td>'.$value->primeiroNomePes.'</td>';
		$html .= '<td>'.$value->cpfCnpjPes.'</td>';
		$html .= '<td>'.$value->nomeSta.'</td>';
		$html .= '<td align="center"><a href="?controle=Boleto&acao=gerarBoleto&idMensa='.$value->idMensa.'"  title="Boleto" target="myModal6"><img src="template/images/icone-boleto.gif"></a></td>';
        if($value->codigoSta == "A"){
            
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->tb_pessoa_idPes.'" title="Bloquear Acesso" class="open-AddBookDialog btn btn-warning btn-xs" href="#addBookDialog">Bloquear Acesso</a></td>';
        }
        else{
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->tb_pessoa_idPes.'" title="Liberar Acesso" class="open-AddBookDialog2 btn btn-success btn-xs" href="#addBookDialog2">Liberar Acesso</a></td>'; 
        }
		 
		if($value->descricaoSit == "Aberta"){
		    $html .= '<td align="center"><a href="?controle=Mensalidade&acao=mensalidadeTransportador&idMensa='.$value->idMensa.'" class="btn btn-primary btn-xs">Baixa</a></td>';
		}
        else{
            $html .= '<td align="center"><a href="?controle=Mensalidade&acao=mensalidadeTransportador&idMensa='.$value->idMensa.'" class="btn btn-info btn-xs">Editar</a></td>';
        }
		
		$html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idMensa.'" title="Deletar Mensalidade" class="open-AddBookDialog4 btn btn-danger btn-xs" href="#addBookDialog4">Deletar</a></td>'; 
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