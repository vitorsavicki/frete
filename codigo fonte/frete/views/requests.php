<?php 
	session_start();
	include '../lib/conexao.php';
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	$GET = strip_tags(trim($_GET['acao']));

	switch ($GET) {

		case 'verificar':
			
			$st_queryAlerta = '';
			$st_queryAlerta.= "SELECT tb_lance.*, tb_alertaLance.*, tb_pessoa.* FROM tb_alertaLance 
			INNER JOIN tb_lance ON tb_lance.idLan = tb_alertaLance.tb_Lance_idLan
			INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_lance.tb_pessoa_idPes
			INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_lance.tb_transporte_idTransp
			WHERE statusAleLan = 'N' AND tb_transporte.tb_pessoa_idPes = $idPes";
			$o_data = $o_db->query($st_queryAlerta);
			$o_rowsAlerta = $o_data->fetchAll(PDO::FETCH_OBJ);
			$total = count($o_rowsAlerta);

			echo $total;

			break;

		case 'getnots' :
			
			$st_queryAlerta = '';
			$st_queryAlerta.= "SELECT tb_lance.*, tb_alertaLance.*, tb_pessoa.*, tb_transporte.* FROM tb_alertaLance 
			INNER JOIN tb_lance ON tb_lance.idLan = tb_alertaLance.tb_Lance_idLan
			INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_lance.tb_pessoa_idPes
			INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_lance.tb_transporte_idTransp
			WHERE  tb_transporte.tb_pessoa_idPes = $idPes
			ORDER BY dataRecebidaAleLan DESC LIMIT 5";
			$o_data = $o_db->query($st_queryAlerta);
			$o_rowsAlerta = $o_data->fetchAll(PDO::FETCH_OBJ);
			$total = count($o_rowsAlerta);

			$li = '';
			
			if($total <> 0){
			foreach($o_rowsAlerta as $o_ret => $value){
					  $li .='<li id="'.$value->idAleLan.'"style="background-color: white;">
                        <a href="?controle=Lance&acao=ListarLance&tb_transporte_idTransp='.$value->idTransp.'&opcao=clienteativos" id="'.$value->idAleLan.'">
                          <div class="pull-left">
                            <img src="'.$value->fotoPes.'" class="img-circle" alt="User Image" id="'.$value->idAleLan.'">
                          </div>
                          <h4>
                            '.$value->primeiroNomePes.'
                            <small><i class="fa fa-clock-o"></i> '.date('d/m/Y H:i', strtotime($value->dataRecebidaAleLan)).'</small>
                          </h4>
                          <p id="'.$value->idAleLan.'"style="font-size: 8px;">Ofereceu um novo lance<br> no valor de '.'R$ '.number_format($value->valorLan,2).'</p>';
						  $vis    = ($value->statusAleLan == 'N') ? 'vis' : 'vis2';
 						  $title  = ($value->statusAleLan == 'N') ? 'Não Lido' : 'Lido';
						  $li .= '<div class="'.$vis.'" id="'.$value->idAleLan.'" title="'.$title.'"></div><br>';
						  $li .= '</a>';
                     	  $li .= '</li>';
			}

			echo $li;
			}
			else {
				$li = '';
				$li =  '<li class="text-center" style="background-color: white;><p style="font-size: 12px;">Nenhum lance encontrada!</p></li>';
				echo $li;
			}

			break;
		
		case 'vis':
			$idnot = (int)$_GET['idnot'];
			
			if(!empty($idnot) and is_numeric($idnot)){
				
				$st_queryAlerta = '';
				$st_queryAlerta.= "UPDATE tb_alertaLance SET statusAleLan = 'V' WHERE idAleLan = '$idnot' ";
				$o_data = $o_db->query($st_queryAlerta);
				$atualiza = $o_data->fetchAll(PDO::FETCH_OBJ);

				if($atualiza): echo 'Visualizado!'; endif;
			}
			break;

		default:
			echo 'Erro';
			break;
			
	} 
 