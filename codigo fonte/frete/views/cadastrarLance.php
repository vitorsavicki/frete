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
    $tb_perfil_idPer = isset($_POST['tb_perfil_idPer']) ? $_POST['tb_perfil_idPer'] : '0';
    $idPes = isset($_SESSION['idPes']) ? $_SESSION['idPes'] : '0';
    
    $registros = 5;
    
    $pagina_atual = isset($_POST['pagina'])? intval($_POST['pagina']) : 1;
    
    $st_query = "select";
    $st_query .= "      tb_transporte.*, ";
    $st_query .= "      tb_statusTransp.nomeStaTransp, ";
    $st_query .= "      tb_endereco_transporte.*, ";
    $st_query .= "      tb_categoria.nomeCat, ";
    $st_query .= "      cidOri.nomeCid as cidOrigem, ";
    $st_query .= "      estOri.ufEst as estOrigem, ";
    $st_query .= "      cidDes.nomeCid as cidDestino, ";
    $st_query .= "      estDes.ufEst as estDestino, ";
    $st_query .= "      tb_avaliacao.*, ";
    $st_query .= "      (select count(*) from tb_lance where tb_lance.tb_transporte_idTransp = tb_transporte.idTransp) as qtdeLan, ";
    $st_query .= "      (select min(valorlan) from tb_lance where tb_lance.tb_transporte_idTransp = tb_transporte.idTransp) as menorLan ";
    $st_query .= "  from tb_transporte ";
    $st_query .= "  inner join tb_statusTransp on tb_statusTransp.idStaTransp = tb_transporte.tb_statusTransp_idStaTransp ";
    $st_query .= "  inner join tb_categoria on tb_transporte.tb_categoria_idcat = tb_categoria.idcat ";
    $st_query .= "  left join tb_endereco_transporte on tb_transporte.tb_endereco_transporte_idEndTran =  tb_endereco_transporte.idEndTran ";
    $st_query .= "  left join tb_cidade as cidOri on cidOri.idCid = tb_endereco_transporte.tb_cidadeOrigem_IdCid ";
    $st_query .= "  left join tb_estado as estOri on estOri.idEst = cidOri.tb_Estado_idEst ";
    $st_query .= "  left join tb_cidade as cidDes on cidDes.idCid = tb_endereco_transporte.tb_cidadeDestino_IdCid ";
    $st_query .= "  left join tb_estado as estDes on estDes.idEst = cidDes.tb_Estado_idEst ";
    $st_query .= "  left join tb_avaliacao on tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
    
    if ($opcao == 'transpganhos')
    {
        $st_query .= "  left join tb_lance on tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
    }
    if ($opcao == 'transpConcluidoTrans')
    {
        $st_query .= "  left join tb_lance on tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
    }
    if ($opcao == 'transpAndamentoTrans')
    {
        $st_query .= "  left join tb_lance on tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
    }
    $st_query .= "where 0 = 0 ";
    
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
    if ($opcao == 'clienteativos')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'A'";
    }
    elseif ($opcao == 'clienteativosTrans')
    {
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'A'";
    }
    elseif ($opcao == 'clienteinativos')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'X'";
    }
    //inicio das minhas modificações
    elseif ($opcao == 'transpandamento')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'E'";
    }
    elseif ($opcao == 'transpconcluido')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'C'";
    }
    elseif ($opcao == 'transpConcluidoTrans')
    {
        
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'C'";
        $st_query .= " and tb_lance.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_lance.vencedorlan = 'S'";
    }
    elseif ($opcao == 'transpganhos')
    {
        $st_query .= " and tb_lance.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_lance.vencedorlan = 'S'";
    }
    elseif ($opcao == 'transpAndamentoTrans')
    {
        $st_query .= " and tb_lance.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_lance.vencedorlan = 'S'";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'E'";
    }
    //Fim das minhas modificações
    $st_query .= " order by tb_transporte.dataRetiradaTransp DESC;";
    
    $o_data = $o_db->query($st_query);
    $o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
    
    $nRows = count($o_rows);
    $paginas = ceil($nRows / $registros);
    
    $fim = $registros * $pagina_atual;
    $inicio = ($fim - $registros);
    
    $st_query = "";
    
    $st_query = "select";
    $st_query .= "      tb_transporte.*, ";
    $st_query .= "      tb_statusTransp.nomeStaTransp, ";
    $st_query .= "      tb_endereco_transporte.*, ";
    $st_query .= "      tb_categoria.nomeCat, ";
    $st_query .= "      cidOri.nomeCid as cidOrigem, ";
    $st_query .= "      estOri.ufEst as estOrigem, ";
    $st_query .= "      cidDes.nomeCid as cidDestino, ";
    $st_query .= "      estDes.ufEst as estDestino, ";
    $st_query .= "      tb_avaliacao.*, ";
    $st_query .= "      (select count(*) from tb_lance where tb_lance.tb_transporte_idTransp = tb_transporte.idTransp) as qtdeLan, ";
    $st_query .= "      (select min(valorlan) from tb_lance where tb_lance.tb_transporte_idTransp = tb_transporte.idTransp) as menorLan ";
    $st_query .= "  from tb_transporte ";
    $st_query .= "  inner join tb_statusTransp on tb_statusTransp.idStaTransp = tb_transporte.tb_statusTransp_idStaTransp ";
    $st_query .= "  inner join tb_categoria on tb_transporte.tb_categoria_idcat = tb_categoria.idcat ";
    $st_query .= "  left join tb_endereco_transporte on tb_transporte.tb_endereco_transporte_idEndTran =  tb_endereco_transporte.idEndTran ";
    $st_query .= "  left join tb_cidade as cidOri on cidOri.idCid = tb_endereco_transporte.tb_cidadeOrigem_IdCid ";
    $st_query .= "  left join tb_estado as estOri on estOri.idEst = cidOri.tb_Estado_idEst ";
    $st_query .= "  left join tb_cidade as cidDes on cidDes.idCid = tb_endereco_transporte.tb_cidadeDestino_IdCid ";
    $st_query .= "  left join tb_estado as estDes on estDes.idEst = cidDes.tb_Estado_idEst ";
    $st_query .= "  left join tb_avaliacao on tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
    
    if ($opcao == 'transpganhos')
    {
        $st_query .= "  left join tb_lance on tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
    }
    if ($opcao == 'transpConcluidoTrans')
    {
        $st_query .= "  left join tb_lance on tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
    }
    if ($opcao == 'transpAndamentoTrans')
    {
        $st_query .= "  left join tb_lance on tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
    }
    $st_query .= "where 0 = 0 ";
    
    if (isset($dataini) and $dataini !== NULL and $dataini !== '')
        $st_query .= "and tb_transporte.dataRetiradaTransp >= '$dataini'";
    if (isset($datafim) and $datafim !== NULL and $datafim !== '' )
        $st_query .= "and tb_transporte.dataRetiradaTransp <= '$datafim' ";
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
    if ($opcao == 'clienteativos')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'A'";
    }
    elseif ($opcao == 'clienteativosTrans')
    {
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'A'";
    }
    elseif ($opcao == 'clienteinativos')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'X'";
    }
    //inicio das minhas modificações
    elseif ($opcao == 'transpandamento')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'E'";
    }
    elseif ($opcao == 'transpconcluido')
    {
        $st_query .= " and tb_transporte.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'C'";
    }
    elseif ($opcao == 'transpConcluidoTrans')
    {
        
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'C'";
        $st_query .= " and tb_lance.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_lance.vencedorlan = 'S'";
    }
    elseif ($opcao == 'transpganhos')
    {
        $st_query .= " and tb_lance.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_lance.vencedorlan = 'S'";
    }
    elseif ($opcao == 'transpAndamentoTrans')
    {
        $st_query .= " and tb_lance.tb_pessoa_idPes = $idPes";
        $st_query .= " and tb_lance.vencedorlan = 'S'";
        $st_query .= " and tb_statusTransp.codigoStaTransp = 'E'";
    }
    //Fim das minhas modificações
    $st_query .= " order by tb_transporte.dataRetiradaTransp DESC LIMIT $registros OFFSET $inicio;";
    
    $o_data = $o_db->query($st_query);
    $o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
    
    $html = '';
    $html .=  '<div class="table-responsive">';
    $html .= '<table class="table table-bordered table-condensed table-hover table-striped" id="tblItens" style="font-size: 10px" >';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;" >Detalhes<br>do Transporte</th>';
    $html .= '<th style="text-align:center;" >Data<br>Pedido</th>';                 
    $html .= '<th style="text-align:center;" >Foto</th>';
    $html .= '<th style="text-align:center;" >Descrição</th>';
    $html .= '<th style="text-align:center;" >Endereço<br>de Origem</th>';
    $html .= '<th style="text-align:center;" >Endereço<br>de Destino</th>';             
    $html .= '<th style="text-align:center;" >Data<br>Transporte</th>';
    $html .= '<th style="text-align:center;" >Qtde.<br>Lances</th>';
    $html .= '<th style="text-align:center;" >Menor<br>Lance</th>';
    if ($opcao == 'transpconcluido') { 
    $html .= '<th class="text-center" colspan="1">Ações</th><br />';
     } 
    elseif ($opcao == 'transpAndamentoTrans') { 
    $html .= '<th class="text-center" colspan="2">Ações</th>';
    }
    elseif ($opcao == 'transpandamento') { 
    $html .= '<th class="text-center" colspan="2">Ações</th>';
    }
    elseif ($opcao == 'transpAndamentoTrans') {
    $html .= '<th class="text-center" colspan="2">Ações</th>';  
    } 
    elseif ($opcao == 'clienteativos') { 
    $html .= '<th class="text-center" colspan="3">Ações</th>';
    }
    elseif ($opcao == 'clienteinativos') { 
    $html .= '<th class="text-center" colspan="1">Ações</th>';
    }
    elseif ($opcao == 'transpganhos') { 
                            
    } else{ 
    $html .= '<th style="text-align:center;" colspan="2" width="100px">Novo lance</th>';                     
    } 
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody >';

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
        $html .= '<td style="text-align:center;"><a class="btn btn-primary btn-xs" href="?controle=Transporte&acao=detalhesTransporte&idTransp='.$value->idTransp.'&consulta=S&opcao='.$opcao.'" onclick="load()">Detalhes</a></td>';
        $html .= '<td style="text-align:center;">'.date('d/m/Y', strtotime($value->dataCadastroTransp)).'</td>';
        if ($str_img !== '' and $str_img !== NULL)
            $html .= '<td style="text-align:center;"><img class="img-rounded" style="width:80px;" src="'.$str_img.'"></td>';
        else
            $html .= '<td style="text-align:center;"><img class="img-rounded" style="width:80px;" src="template/images/semfoto.png"></td>';
        $html .= '<td>'.$value->descricaoTransp.'</td>';
        $html .= '<td>'.utf8_encode($value->ruaOrigemEndTran).' - '.utf8_encode($value->bairroOrigemEndTran).' - '.utf8_encode($value->cidOrigem).'/'.utf8_encode($value->estOrigem).'</td>';
        $html .= '<td>'.utf8_encode($value->ruaDestinoEndTran).' - '.utf8_encode($value->bairroDestinoEndTran).' - '.utf8_encode($value->cidDestino).'/'.utf8_encode($value->estDestino).'</td>';
        //$html .= '<td>'.$value->ruaOrigemEndTran.' - '.$value->bairroOrigemEndTran.'</td>';
        //$html .= '<td>'.$value->ruaDestinoEndTran.' - '.$value->bairroDestinoEndTran.'</td>';
        $html .= '<td style="text-align:center;">'.date('d/m/Y', strtotime($value->dataRetiradaTransp)).'</td>';
        $html .= '<td style="text-align:center;">'.$value->qtdeLan.'</td>';
        $html .= '<td style="text-align:right;">'.'R$ ' .number_format($value->menorLan, 2, ',', '.').'</td>';
    
        if ($opcao == 'clienteativos') 
        {
            //$html .= '<td style="text-align:center;"><a class="btn btn-danger btn-xs"  onclick=fncMudarStatus("X",'.$value->idTransp.',this);>Cancelar</a></td>'; // alterar o status do transporte para X
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Cancelar Transporte" class="open-AddBookDialog2 btn btn-danger btn-xs" href="#addBookDialog2">Cancelar</a></td>'; // alterar o status do transporte para X
            $st_query = "SELECT * FROM tb_lance WHERE tb_transporte_idTransp = '$value->idTransp' ";
            $o_data = $o_db->query($st_query);
            $o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
            if($o_rows){
                $html .= '<td style="text-align:center;"><a class="btn btn-success btn-xs" href="?controle=Lance&acao=ListarLance&tb_transporte_idTransp='.$value->idTransp.'&opcao='.$opcao.'" class="btn btn-success btn-xs">Ver Lances</a></td>';
            }
            else{
                $html .= '<td style="text-align:center;"><button type="button"  data-toggle="modal" data-target="#myModal10" class="btn btn-success btn-xs">Ver Lances</button></td>';
            }
            
            $st_query = "SELECT * FROM tb_lance WHERE tb_transporte_idTransp = '$value->idTransp' ";
            $o_data = $o_db->query($st_query);
            $o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
            if($o_rows){
                $html .= '<td style="text-align:center;"><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal8">Editar</button></td>';
            }
            else{
                $html .= '<td style="text-align:center;"><a class="btn btn-primary btn-xs" href="?controle=Transporte&acao=manterTransporte&idTransp='.$value->idTransp.'&opcao='.$opcao.'&edicao=S" onclick="load()">Editar</a></td>';
            }
            
        } 
        elseif ($opcao == 'clienteinativos') {
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Ativar Transporte" class="open-AddBookDialog6 btn btn-primary btn-xs" href="#addBookDialog6" >Ativar</a></td>';
            //$html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Ativar Transporte com Lances" class="open-AddBookDialog7 btn btn-primary btn-xs" href="#addBookDialog7">Ativar Com Lances</a></td>';
            //$html .= '<td style="text-align:center;"><a class="btn btn-primary btn-xs" onclick=fncMudarStatus("A",'.$value->idTransp.',this);>Ativar</a></td>'; // alterar o status do transporte para A
            //$html .= '<td style="text-align:center;"><a class="btn btn-success btn-xs" href="?controle=Lance&acao=ListarLance&tb_transporte_idTransp='.$value->idTransp.'&opcao='.$opcao.'">Ver Lances</a></td>';
        }
        elseif ($opcao == 'transpandamento') {
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Cancelar Transporte" class="open-AddBookDialog8 btn btn-danger btn-xs" href="#addBookDialog8">Cancelar</a></td>';
            //$html .= '<td style="text-align:center;"><a class="btn btn-danger btn-xs" onclick=fncMudarStatus("X",'.$value->idTransp.',this);>Cancelar</a></td>'; // alterar o status do transporte para x
            if(date('d/m/Y', strtotime($value->dataRetiradaTransp)) > date('d/m/Y')){
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Concluir Transporte" class="open-AddBookDialog4 btn btn-info btn-xs" href="#addBookDialog4">Concluir</a></td>'; // alterar o status do transporte para C
            }
            else{
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Concluir Transporte" class="open-AddBookDialog3 btn btn-info btn-xs" href="#addBookDialog3">Concluir</a></td>'; // alterar o status do transporte para C
            }
            //$html .= '<td style="text-align:center;"><a class="btn btn-info btn-xs" onclick=fncMudarStatus("C",'.$value->idTransp.',this);>Concluir</a></td>'; // alterar o status do transporte para C
            
        }  
        elseif ($opcao == 'transpconcluido') {
            if(!$value->idAva){
            //$html .= '<td style="text-align:center;"><a href="?controle=Avaliacao&acao=avaliacaoTransportador&opcao=transpconcluido&idTransp='.$value->idTransp.'" type="button" class="btn btn-warning btn-xs">Avaliar Transporte</a></td>';
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Avaliar Transporte" class="open-AddBookDialog5 btn btn-warning btn-xs" href="#addBookDialog5">Avaliar Transporte</a></td>';   
            }
            else{
            $html .= '<td style="text-align:center;"><a type="button" class="btn btn-success btn-xs" disable>Transporte Avaliado</a></td>';     
            }
            
            
        }  
        elseif ($opcao == 'transpConcluidoTrans') {
            $html .= '<td style="text-align:center;"><a class="btn btn-warning btn-xs" >Avaliar Cliente</a></td>';
            
        }  
        elseif ($opcao == 'transpganhos') {
            
        } 
        elseif ($opcao == 'transpAndamentoTrans') {
            $html .= '<td>'.$value->nomeStaTransp.'</td>';
        } 
        else{

            //$html .= '<td style="text-align:center;"><input type="text" name="valorLan" max="999999" min="1" style="width:70px;"></td>';
            //$html .= '<td style="text-align:center;"><a class="btn btn-success btn-xs" id="testebtn" name="testebtn" onclick=fncCadastrarLance('.$value->idTransp.','.$idPes.',this); >Cadastrar Lance</a></td>';
            $html .= '<td style="text-align:center;"><a data-toggle="modal" data-id="'.$value->idTransp.'" title="Cadastre um Lance" class="open-AddBookDialog btn btn-success btn-xs" href="#addBookDialog">Cadastrar Lance</a></td>';
        
        }   
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