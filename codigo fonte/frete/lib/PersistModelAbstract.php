<?php
 /**
 * Diretório Pai - lib
 * Arquivo - PersistModelAbstract.php

 */
abstract class PersistModelAbstract
{
	/**
	* Variável responsável por guardar dados da conexão do banco
	* @var resource
	*/
	protected $o_db;
	
	function __construct()
	{
		
		include 'conexao.php';
		$this->o_db = $o_db;
		$this->o_db->setAttribute ( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
	}
}
?>