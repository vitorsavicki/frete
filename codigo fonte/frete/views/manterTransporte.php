<?php
	session_start();
	$opcao = isset($_POST['opcao']) ? $_POST['opcao'] : '';
	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$itemvalue = isset($_POST['itemvalue']) ? $_POST['itemvalue'] : '';
	$itemtext = isset($_POST['itemtext']) ? $_POST['itemtext'] : '';
	$itemDescricao = isset($_POST['itemDescricao']) ? $_POST['itemDescricao'] : '';
	$itemqtde = isset($_POST['itemqtde']) ? $_POST['itemqtde'] : '';
	$itemAltura = isset($_POST['itemAltura']) ? $_POST['itemAltura'] : '';
	$itemLargura = isset($_POST['itemLargura']) ? $_POST['itemLargura'] : '';
	$itemComprimento = isset($_POST['itemComprimento']) ? $_POST['itemComprimento'] : '';
	$itemPeso = isset($_POST['itemPeso']) ? $_POST['itemPeso'] : '';

	if(isset($_SESSION['conteudotransporte']) && !empty($_SESSION['conteudotransporte']))
		$var = $_SESSION["conteudotransporte"];
			
	if ($opcao == 'adicionar')
	{
		$registro = array(
			"id"=>$id,
			"itemvalue"=>$itemvalue,
			"itemtext"=>$itemtext,
			"itemDescricao"=>$itemDescricao,
			"itemqtde"=>$itemqtde,
			"itemAltura"=>$itemAltura,
			"itemLargura"=>$itemLargura,
			"itemComprimento"=>$itemComprimento,
			"itemPeso"=>$itemPeso
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
					"itemvalue"=>$var[$i]["itemvalue"],
					"itemtext"=>$var[$i]["itemtext"],
					"itemDescricao"=>$var[$i]["itemDescricao"],
					"itemqtde"=>$var[$i]["itemqtde"],
					"itemAltura"=>$var[$i]["itemAltura"],
					"itemLargura"=>$var[$i]["itemLargura"],
					"itemComprimento"=>$var[$i]["itemComprimento"],
					"itemPeso"=>$var[$i]["itemPeso"]
				);
				$varnovo[] = $registro;
			}
        }
		$var = $varnovo;
	}
	
	// convertemos em json e colocamos na tela
    echo json_encode($var);
	// armazenamos na session para depois salvar no banco
	$_SESSION["conteudotransporte"] = $var;
?>