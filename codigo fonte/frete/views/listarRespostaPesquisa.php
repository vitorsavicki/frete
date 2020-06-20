<?php
    session_start();
    include '../lib/conexao.php';

    $st_query = "";
    $st_query =  "SELECT tb_pergunta_pesquisa.nomePqa, CAST((CAST((count(tb_pergunta_pesquisa_idPqa) * 100) AS DECIMAL(10,2)) / (select count(*) FROM tb_resposta_pesquisa ) )AS DECIMAL(10,2)) as porcentagem, (select count(*) FROM tb_resposta_pesquisa ) as Total FROM tb_resposta_pesquisa
    INNER JOIN tb_pergunta_pesquisa ON tb_pergunta_pesquisa.idPqa = tb_resposta_pesquisa.tb_pergunta_pesquisa_idPqa
    GROUP BY tb_pergunta_pesquisa_idPqa,tb_pergunta_pesquisa.nomePqa ";
    
    $o_data = $o_db->query($st_query);
    $o_rows = $o_data->fetchAll(PDO::FETCH_OBJ);
    
    $nRows = count($o_rows);
    
    $html =  '';

    $var = array();
    if($nRows <> 0){
    foreach ($o_rows as $o_ret => $value)
    {
        
    $html .= '<strong>'.utf8_encode($value->nomePqa).'</strong>';
    $html .= '<span class="pull-right">'.$value->porcentagem.'%</span>';
    $html .= '<div class="progress progress-striped active">';
    $html .= '<div class="progress-bar" style="width: '.$value->porcentagem.'%;"></div>';
    $html .= '</div>';
    $total = $value->Total;

        $var[] = $html;
        
    }
    $html .= '<div class="pull-left pagination-detail">';
    $html .= '<span class="pagination-info">Total de Respostas: '. $total .'</span>';
    $html .= '</div>';
    $var[] = $html;
}
            echo json_encode($var);
?>