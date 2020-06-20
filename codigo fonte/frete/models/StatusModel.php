<?php
require_once 'models/StatusModel.php';


/**
* @package Exemplo simples com MVC 

* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - StatusModel
*
* Responsável por gerenciar e persistir os dados dos Status
**/
class StatusModel extends PersistModelAbstract
{
      private $idSta;
	  private $nomeSta;
	  private $codigoSta;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe CidadeModel
	 */
	
	public function setIdSta( $idSta )
	{
		$this->idSta = $idSta;
		return $this;
	}
	
	public function getIdSta()
	{
		return $this->idSta;
	}
	
	public function setNomeSta( $nomeSta )
	{
		$this->nomeSta = $nomeSta;
		return $this;
	}
	
	public function getNomeSta()
	{
		return $this->nomeSta;
	}
	
	public function setCodigoSta( $codigoSta )
	{
		$this->codigoSta = $codigoSta;
		return $this;
	}
	
	public function getCodigoSta()
	{
		return $this->codigoSta;
	}

	/**
	* Retorna um array contendo os Status
	* @param string $IdCid
	* @return Array
	*/
	public function _list( $nomeSta = null )
	{	
		if(!is_null($nomeSta))
			$st_query = "SELECT * FROM tb_status WHERE nomeSta = $nomeSta;";
		else
			$st_query = 'SELECT * FROM tb_status;';	
		
		$v_status = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_status = new StatusModel();
				$o_status->setIdSta($o_ret->idSta);
				$o_status->setNomeSta($o_ret->nomeSta);
				$o_status->setCodigoSta($o_ret->codigoSta);
				array_push($v_status, $o_status);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_status;
	}
	
	/**
	* Retorna os dados de uma cidade referente
	* a um determinado Id
	* @param integer $idCid
	* @return ContatoModel
	*/
	public function loadById( $idSta )
	{
		$v_contatos = array();
		$st_query = "SELECT * FROM tb_status WHERE idSta = $idSta;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdSta($o_ret->idSta);
		$this->setNomeSta($o_ret->nomeSta);
		$this->setCodigoSta($o_ret->codigoSta);
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de cidade. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idSta))
			$st_query = "INSERT INTO tb_status
						(
								nomeSta,
								codigoSta
						)
						VALUES
						(
								'$this->idSta',
								'$this->nomeSta'
								
						);";
		else
			$st_query = "UPDATE
							tb_status
						SET
								idSta = '$this->idSta',
								nomeSta = '$this->nomeSta',
								codigoSta = '$this->codigoSta'
						WHERE
							idSta = $this->idSta;";
		try
		{
			//echo $st_query.'<br><br>';
			//exit;
			$this->o_db->exec($st_query);
			
		}
		catch (PDOException $e)
		{
			throw $e;
		}
		return false;				
	}

	/**
	* Deleta os dados persistidos na tabela de
	* cidade usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idSta))
		{
			$st_query = "DELETE FROM
							tb_status
						WHERE idCid = $this->idSta";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>