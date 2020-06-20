<?php 
	session_start();
	include '../lib/conexao.php';
	$idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
	$GET = strip_tags(trim($_GET['acao']));

	switch ($GET) {

		case 'verificar':
			
			$st_queryAlerta = '';
			$st_queryAlerta.= "SELECT tb_alertaMensagem.*, tb_lance.*, tb_pessoa.*, tb_mensagem.* FROM tb_alertaMensagem
			INNER JOIN tb_mensagem ON tb_mensagem.idMen = tb_alertaMensagem.tb_mensagem_idMen
			INNER JOIN tb_lance ON tb_lance.idLan = tb_mensagem.tb_lance_idLan
			INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_lance.tb_pessoa_idPes
			INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_lance.tb_transporte_idTransp
			INNER JOIN tb_perfil ON tb_perfil.idPer = tb_pessoa.tb_perfil_idPer
			WHERE statusAleMen = 'N' AND tb_transporte.tb_pessoa_idPes = $idPes AND tb_perfil.codigoPer = 'T'  AND  tb_mensagem.tb_pessoa_idPes <> $idPes";
			
			$o_data = $o_db->query($st_queryAlerta);
			$o_rowsAlerta = $o_data->fetchAll(PDO::FETCH_OBJ);
			$total = count($o_rowsAlerta);

			echo $total;

			break;

		case 'getnots' :
			
			$st_queryAlerta = '';
			$st_queryAlerta.= "SELECT  tb_alertaMensagem.*, tb_lance.*, tb_pessoa.*, tb_mensagem.* FROM tb_alertaMensagem
			INNER JOIN tb_mensagem ON tb_mensagem.idMen = tb_alertaMensagem.tb_mensagem_idMen
			INNER JOIN tb_lance ON tb_lance.idLan = tb_mensagem.tb_lance_idLan
			INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_lance.tb_pessoa_idPes
			INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_lance.tb_transporte_idTransp
			INNER JOIN tb_perfil ON tb_perfil.idPer = tb_pessoa.tb_perfil_idPer
			WHERE tb_transporte.tb_pessoa_idPes = $idPes AND tb_perfil.codigoPer = 'T'  AND  tb_mensagem.tb_pessoa_idPes <> $idPes
			ORDER BY dataRecebidaAleMen DESC LIMIT 5";
			$o_data = $o_db->query($st_queryAlerta);
			$o_rowsAlerta = $o_data->fetchAll(PDO::FETCH_OBJ);
			$total = count($o_rowsAlerta);

			$li = '';

			if($total <> 0){
			foreach($o_rowsAlerta as $o_ret => $value){
				 $li .='<li id="'.$value->idAleMen.'" style="background-color: white;">
                        <a href="?controle=Mensagem&acao=listarMensagemCli&opcao=Mensagens" id="'.$value->idAleMen.'">
                          <div class="pull-left">
                            <img src="'.$value->fotoPes.'" class="img-circle" alt="User Image" id="'.$value->idAleMen.'">
                          </div>
                          <h4>
                            '.$value->primeiroNomePes.'
                            <small><i class="fa fa-clock-o"></i> '.date('d/m/Y H:i', strtotime($value->dataRecebidaAleMen)).'</small>
                          </h4>
                          <p style="font-size: 8px;" id="'.$value->idAleMen.'">'.trim(substr($value->conteudoMen, 0, 30)) . "...".'</p>';
						  $vis    = ($value->statusAleMen == 'N') ? 'vis' : 'vis2';
 						  $title  = ($value->statusAleMen == 'N') ? 'Não Lido' : 'Lido';
						  $li .= '<div class="'.$vis.'" id="'.$value->idAleMen.'" title="'.$title.'"></div><br>';
						  $li .= '</a>';
                     	  $li .= '</li>';
                     	  }
			

			echo $li;
			}
			else {
				$li =  '<li class="text-center" style="background-color: white;><p style="font-size: 12px;">Nenhuma mensagem encontrada!</p></li>';
				echo $li;
			}

			break;
		
		case 'vis':
			$idnot = (int)$_GET['idnot'];
			
			if(!empty($idnot) and is_numeric($idnot)){
				
				$st_queryAlerta = '';
				$st_queryAlerta.= "UPDATE tb_alertaMensagem SET statusAleMen = 'V' WHERE idAleMen = '$idnot' ";
				$o_data = $o_db->query($st_queryAlerta);
				$atualiza = $o_data->fetchAll(PDO::FETCH_OBJ);

				if($atualiza): echo 'Visualizado!'; endif;
			}
			break;

		default:
			echo 'Erro';
			break;
	} 
 