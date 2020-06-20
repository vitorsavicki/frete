<?php
require_once 'models/StatusTransporteModel.php';


/**
* @package Exemplo simples com MVC 

* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - StatusModel
*
* Responsável por gerenciar e persistir os dados dos Status
**/
class StatusTransporteModel extends PersistModelAbstract
{
      private $idStaTransp;
      private $nomeStaTransp;
      private $codigoStaTransp;
    
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe CidadeModel
	 */
	
	public function setIdStaTransp( $idStaTransp )
	{
		$this->idStaTransp = $idStaTransp;
		return $this;
	}
	
	public function getIdStaTransp()
	{
		return $this->idStaTransp;
	}
	
	public function setNomeStaTransp( $nomeStaTransp )
	{
		$this->nomeStaTransp = $nomeStaTransp;
		return $this;
	}
	
	public function getNomeStaTransp()
	{
		return $this->nomeStaTransp;
	}
	
	public function setCodigoStaTransp( $codigoStaTransp )
	{
		$this->codigoStaTransp = $codigoStaTransp;
		return $this;
	}
	
	public function getCodigoStaTransp()
	{
		return $this->codigoStaTransp;
	}

	/**
	* Retorna um array contendo os Status
	* @param string $IdCid
	* @return Array
	*/
	public function _list( $nomeStaTransp = null )
	{	
		if(!is_null($nomeStaTransp))
			$st_query = "SELECT * FROM tb_statusTransp WHERE nomeStaTransp = $nomeStaTransp;";
		else
			$st_query = 'SELECT * FROM tb_statusTransp;';	
		
		$v_status = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_status = new StatusTransporteModel();
				$o_status->setIdStaTransp($o_ret->idStaTransp);
				$o_status->setNomeStaTransp($o_ret->nomeStaTransp);
				$o_status->setCodigoStaTransp($o_ret->codigoStaTransp);
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
	public function loadById( $idStaTransp )
	{
		$v_contatos = array();
		$st_query = "SELECT * FROM tb_statusTransp WHERE idStaTransp = $idStaTransp;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdStaTransp($o_ret->idStaTransp);
		$this->setNomeStaTransp($o_ret->nomeStaTransp);
		$this->setCodigoStaTransp($o_ret->codigoStaTransp);
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
		if(is_null($this->idStaTransp))
			$st_query = "INSERT INTO tb_statusTransp
						(
								nomeStaTransp,
								codigoStaTransp
						)
						VALUES
						(
								'$this->idStaTransp',
								'$this->nomeStaTransp'
								
						);";
		else
			$st_query = "UPDATE
							tb_statusTransp
						SET
								idStaTransp = '$this->idStaTransp',
								nomeStaTransp = '$this->nomeStaTransp',
								codigoStaTransp = '$this->codigoStaTransp'
						WHERE
							idStaTransp = $this->idStaTransp;";
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
		if(!is_null($this->idStaTransp))
		{
			$st_query = "DELETE FROM
							tb_statusTransp
						WHERE idCid = $this->idStaTransp";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>