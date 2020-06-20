<?php
	session_start();
	$opcao = isset($_POST['opcao']) ? $_POST['opcao'] : '';
	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$caminhoImagem = isset($_SESSION["imagenstransporteCaminho"]) ? $_SESSION["imagenstransporteCaminho"] :'';
	
	if(isset($_SESSION['imagenstransporte']) && !empty($_SESSION['imagenstransporte']))
	$var = $_SESSION["imagenstransporte"];
	
	
				
	if ($opcao == 'adicionar')
	{
		$registro = array(
			"id"=>$id,
			"caminhoImagem"=>$caminhoImagem
			
			);
		
		$var[] = $registro;
	}
	if ($opcao == 'remover')
	{
		$varnovo = array();
		$newId = 0;
		for ($i=0; $i < count($var); $i++){
	    	if($var[$i]["id"] != $id)
			{
				$newId++;
				$registro = array(
					"id"=>$newId,
					"caminhoImagem"=>$var[$i]["caminhoImagem"]
					
				);
				$varnovo[] = $registro;
			}
        }
		$var = $varnovo;
	}
	
	// convertemos em json e colocamos na tela
    echo json_encode($var);
	// armazenamos na session para depois salvar no banco
	$_SESSION["imagenstransporte"] = $var;
?>