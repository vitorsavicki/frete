<?php
require_once 'models/LogModel.php';


class LogModel extends PersistModelAbstract
{
      private $idLog;
	  private $dataLog;
	  private $tb_pessoa_idPes;
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	public function setIdLog( $idLog )
	{
		$this->idLog = $idLog;
		return $this;
	}
	
	public function getiIdLog()
	{
		return $this->idLog;
	}
	
	public function setDataLog( $dataLog )
	{
		$this->dataLog = $dataLog;
		return $this;
	}
	
	public function getDataLog()
	{
		return $this->dataLog;
	}
	
	public function setTb_pessoa_idPes( $tb_pessoa_idPes )
	{
		$this->tb_pessoa_idPes = $tb_pessoa_idPes;
		return $this;
	}
	
	public function getDataValidadeVou()
	{
		return $this->tb_pessoa_idPes;
	}

	/**
	* Retorna um array contendo os Status
	* @param string $IdCid
	* @return Array
	*/
	public function _list( $dataLog = null )
	{	
		if(!is_null($dataLog))
			$st_query = "SELECT * FROM tb_log WHERE dataLog  = '$dataLog';";
		else
			$st_query = 'SELECT * FROM tb_log;';	
		
		$v_logs = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_log = new LogModel();
				$o_log->setIdLog($o_ret->idLog);
				$o_log->setDataLog($o_ret->dataLog);
				$o_log->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				array_push($v_logs, $o_log);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_logs;
	}
	
	public function loadById( $idLog )
	{
		$v_logs = array();
		$st_query = "SELECT * FROM tb_log WHERE idLog = $idLog;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdLog($o_ret->idLog);
		$this->setDataLog($o_ret->dataLog);
		$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
		return $this;
	}

	public function save()
	{
		if(is_null($this->idLog))
			$st_query = "INSERT INTO tb_log
						(
								dataLog,
								tb_pessoa_idPes
						)
						VALUES
						(
								'$this->dataLog',
								'$this->tb_pessoa_idPes'
								
						);";
		else
			$st_query = "UPDATE
							tb_log
						SET
								dataLog = '$this->dataLog',
								tb_pessoa_idPes = '$this->tb_pessoa_idPes'
						WHERE
							idLog = $this->idLog;";
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
		if(!is_null($this->idLog))
		{
			$st_query = "DELETE FROM
							tb_log
						WHERE idLog = $this->idLog";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>