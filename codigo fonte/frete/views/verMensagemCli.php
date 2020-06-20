 <?php
 		session_start();
		include '../lib/conexao.php';
 		$idLan = isset($_POST['idLan']) ? $_POST['idLan'] : '0';
		$st_queryMen = '';
		$st_queryMen .= "SELECT * FROM tb_mensagem ";
		$st_queryMen .= "INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_mensagem.tb_pessoa_idPes ";
		$st_queryMen .= "INNER JOIN tb_perfil ON tb_perfil.idPer = tb_pessoa.tb_perfil_idPer ";
		$st_queryMen .= "WHERE tb_mensagem.tb_lance_idLan = '$idLan' ORDER BY tb_mensagem.dataMen DESC ";
		$o_dataMen = $o_db->query($st_queryMen);
		$o_rowsMen = $o_dataMen->fetchAll(PDO::FETCH_OBJ);
		$nRowsMen = count($o_rowsMen);
		$html = '';
		$var = array();
		 		$html .= '<div class="container">';
				$html .= '<div class="row">';
				$html .= '<div class="col-md-12">';
				$html .= '<section class="comment-list" >';
		if($nRowsMen <> 0){
			foreach ($o_rowsMen as $o_ret => $valueMen)
			{
		       
				
				if($valueMen->codigoPer == 'T'){
					
				$html .= '<article class="row">';
				$html .= '<div class="col-md-2 col-sm-2 hidden-xs">';
				$html .= '<figure class="thumbnail">';
				$html .= '<img class="img-responsive" src="'.$valueMen->fotoPes.'" />';
				$html .= '</figure>';
				$html .= '</div>';
				$html .= '<div class="col-md-10 col-sm-10">';
				$html .= '<div class="panel panel-default arrow left">';
				$html .= '<div class="panel-body">';
				$html .= '<header class="text-left">';
				$html .= '<div class="comment-user"><i class="fa fa-user"></i>'.$valueMen->primeiroNomePes.'</div>';
				$html .= '<time class="comment-date" datetime="'.$valueMen->dataMen.'"><i class="fa fa-clock-o"></i>'.date('d-m-Y H:i:s', strtotime($valueMen->dataMen)).'</time>';
				$html .= '</header>';
				$html .= '<div class="comment-post">';
				$html .= '<p>';
				$html .= $valueMen->conteudoMen;
				$html .= '</p>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</article>';
				
				}
		     	if($valueMen->codigoPer == 'U'){
		     		
				$html .= '<article class="row">';
				$html .= '<div class="col-md-10 col-sm-10">';
				$html .= '<div class="panel panel-default arrow right">';
				$html .= '<div class="panel-body">';
				$html .= '<header class="text-right">';
				$html .= '<div class="comment-user"><i class="fa fa-user"></i>'.$valueMen->primeiroNomePes.'</div>';
				$html .= '<time class="comment-date" datetime="'.$valueMen->dataMen.'"><i class="fa fa-clock-o"></i>'.date('d-m-Y H:i:s', strtotime($valueMen->dataMen)).'</time>';
				$html .= '</header>';
				$html .= '<div class="comment-post">';
				$html .= '<p>';
				$html .= $valueMen->conteudoMen;
				$html .= '</p>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div class="col-md-2 col-sm-2 hidden-xs">';
				$html .= '<figure class="thumbnail">';
				$html .= '<img class="img-responsive" src="'.$valueMen->fotoPes.'" />';
				$html .= '</figure>';
				$html .= '</div>';
				$html .= '</article>';
				$var[] = $html;
				
				}    
			}
				$html .= '</section>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
		        $html .= '</tr>';
				$var[] = $html;
		        
		}
		else{
		
		}
		echo json_encode($var);
?>