<?php

/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - TelefoneModel
*
* Responsável por gerenciar e persistir os dados dos  
* Contatos da Agenda Telefônica 
**/
class AlertaLanceModel extends PersistModelAbstract
{
	private $idAleLan;
	private $statusAleLan;
	private $dataRecebidaAleLan;
	private $tb_Lance_idLan;
	
	
	
	
	/**
	 * Setters e Getters da
	 * classe TelefoneModel
	 */
	
	public function setIdAleLan( $idAleLan )
	{
		$this->idAleLan = $idAleLan;
		return $this;
	}
	
	public function getIdAleLan()
	{
		return $this->idAleLan;
	}
	
	public function setStatusAleLan( $statusAleLan )
	{
		$this->statusAleLan = $statusAleLan;
		return $this;
	}
	
	public function getStatusAleLan()
	{
		return $this->statusAleLan;
	}
	
	public function setDataRecebidaAleLan( $dataRecebidaAleLan )
	{
		$this->dataRecebidaAleLan = $dataRecebidaAleLan;
		return $this;
	}
	
	public function getDataRecebidaAleLan()
	{
		return $this->dataRecebidaAleLan;
	}
	
	public function setTb_Lance_idLan( $tb_Lance_idLan )
	{
		$this->tb_Lance_idLan = $tb_Lance_idLan;
		return $this;
	}
	
	public function getTb_Lance_idLan()
	{
		return $this->tb_Lance_idLan;
	}
	/**
	* Retorna um array contendo os contatos
	* @param string $st_nome
	* @return Array
	*/
	public function _list( $idAleLan = null )
	{
		if(!is_null($st_nome))
			$st_query = "SELECT * FROM tb_alertaLance WHERE idAleLan = '$idAleLan';";
		else
			$st_query = 'SELECT * FROM tb_alertaLance;';	
		
		$v_alertas = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_alerta = new AlertaLanceModel();
				$o_alerta->setIdAleLan($o_ret->idAleLan);
				$o_alerta->setStatusAleLan($o_ret->statusAleLan);
				$o_alerta->setDataRecebidaAleLan($o_ret->dataRecebidaAleLan);
				$o_alerta->setTb_Lance_idLan($o_ret->tb_Lance_idLan);
				array_push($v_alertas, $o_alerta);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_alertas;
	}
	
	/**
	* Retorna os dados de um contato referente
	* a um determinado Id
	* @param integer $in_id
	* @return ContatoModel
	*/
	public function loadById( $idAleLan )
	{
		$v_contatos = array();
		$st_query = "SELECT * FROM tb_alertaLance WHERE idAleLan = '$idAleLan';";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdAleLan($o_ret->idAleLan);
		$this->setStatusAleLan($o_ret->statusAleLan);
		$this->setDataRecebidaAleLan($o_ret->dataRecebidaAleLan);
		$this->setTb_Lance_idLan($o_ret->tb_Lance_idLan);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de contato. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
		public function save(){
		
		if(is_null($this->idAleLan))
			$st_query = "INSERT INTO tb_alertaLance
						(
							  	statusAleLan,
								dataRecebidaAleLan,
								tb_Lance_idLan
						)
						VALUES
						(
							  	'$this->statusAleLan',
								NOW(),
								'$this->tb_Lance_idLan'			   	
						);";
		else
			$st_query = "UPDATE
							tb_alertaLance
						SET
							  	statusAleLan = '$this->statusAleLan',
								tb_Lance_idLan = '$this->tb_Lance_idLan'	
						WHERE
							idMen = $this->idAleLan";
			$st_query .= " select @@IDENTITY as id;";
		try
		{
			//echo $st_query;
			//exit;
			//$this->o_db->exec($st_query);
			$objSth = $this->o_db->query($st_query);
			$objSth->nextRowset();
			$rowTd = $objSth->fetch(PDO::FETCH_NUM);
			return $rowTd[0];
		
		}
		catch (PDOException $e)
		{
			throw $e;
		}
		return 0;				
	}


	/**
	* Deleta os dados persistidos na tabela de
	* contato usando como referencia, o id da classe.
	*/
	public function delete($idTransp)
	{
		if(!is_null($idTransp))
		{
			$st_query = "DELETE tb_alertaLance  FROM tb_alertaLance
			INNER JOIN tb_lance ON tb_lance.idLan = tb_alertaLance.tb_lance_idLan
			WHERE tb_lance.tb_transporte_idTransp = $idTransp";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	

}
?>