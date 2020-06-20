<?php
include '../lib/conexao.php';
try {


$where =" 1=1 ";
$order_by="tb_transporte.dataRetiradaTransp DESC";
$rows=25;
$current=1;
$limit_l=($current * $rows) - ($rows);
$limit_h=$limit_l + $rows  ;


//Handles Sort querystring sent from Bootgrid
if (isset($_REQUEST['sort']) && is_array($_REQUEST['sort']) )
  {
    $order_by="";
    foreach($_REQUEST['sort'] as $key=> $value)
		$order_by.=" $key $value";
	}

//Handles search  querystring sent from Bootgrid 
if (isset($_REQUEST['searchPhrase']) )
  {
    $search=trim($_REQUEST['searchPhrase']);
  	$where.= " AND ( dataCadastroTransp LIKE '".$search."%' OR  descricaoTransp LIKE '".$search."%' ) "; 
	}

//Handles determines where in the paging count this result set falls in
if (isset($_REQUEST['rowCount']) )  
  $rows=$_REQUEST['rowCount'];

 //calculate the low and high limits for the SQL LIMIT x,y clause
  if (isset($_REQUEST['current']) )  
  {
   $current=$_REQUEST['current'];
	$limit_l=($current * $rows) - ($rows);
	$limit_h=$rows ;
   }

if ($rows==-1)
$limit="";  //no limit
else   
$limit="OFFSET $limit_l ROWS FETCH NEXT $limit_h ROWS ONLY ";
   
//NOTE: No security here please beef this up using a prepared statement - as is this is prone to SQL injection.
$sql="SELECT idTransp, descricaoTransp, dataCadastroTransp  FROM tb_transporte WHERE $where ORDER BY $order_by $limit";

$o_data = $o_db->query($sql);
$results_array = $o_data->fetchAll();

$json=json_encode( $results_array );

$count = "SELECT descricaoTransp, dataCadastroTransp  FROM tb_transporte WHERE $where";
$linhas =  $o_db->query($count);
$nRows = count($linhas->fetchAll());

header('Content-Type: application/json'); //tell the broswer JSON is coming

if (isset($_REQUEST['rowCount']) )  //Means we're using bootgrid library
echo "{ \"current\":  $current, \"rowCount\":$rows,  \"rows\": ".$json.", \"total\": $nRows }";
else
echo $json;  //Just plain vanillat JSON output 
exit;
}
catch(PDOException $e) {
    echo 'SQL PDO ERROR: ' . $e->getMessage();
}
?>