<?php
		/*
		$st_host = 'diogoromeo.com.br';
		$st_banco = 'BD_FRETE_IMEDIATO';
		$st_usuario = 'techresult';
		$st_senha = 'Tech@123'; 
		*/
		/*
		$st_host = 'localhost';
		$st_banco = 'freteimediato';
		$st_usuario = 'teste';
		$st_senha = '123456'; 
		*/
		
		$st_host = 'localhost';
		$st_banco = 'frete_imediato';
		$st_usuario = 'root';
		$st_senha = ''; 
		
		/*
		$st_host = 'mysql.hostinger.com.br';
		$st_banco = 'u751250467_frete';
		$st_usuario = 'u751250467_root';
		$st_senha = '123456'; 
		*/
	//$st_dsn = "sqlsrv:server=$st_host;database=$st_banco"; 
	//$st_dsn = "dblib:host=$st_host;database=$st_banco"; 
	$st_dsn = "mysql:host=localhost;dbname=".$st_banco.";";
	//$st_dsn = "odbc:Driver={SQL Server}; Server=".$st_host."; Database=".$st_banco.";";
	$o_db = new PDO
	(
		$st_dsn,
		$st_usuario,
		$st_senha
	);
?>