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
class AlertaMensagemModel extends PersistModelAbstract
{
	private $idAleMen;
	private $statusAleMen;
	private $dataRecebidaAleMen;
	private $tb_Mensagem_idMen;
	
	
	
	
	/**
	 * Setters e Getters da
	 * classe TelefoneModel
	 */
	
	public function setIdAleMen( $idAleMen )
	{
		$this->idAleMen = $idAleMen;
		return $this;
	}
	
	public function getIdAleMen()
	{
		return $this->idAleMen;
	}
	
	public function setStatusAleMen( $statusAleMen )
	{
		$this->statusAleMen = $statusAleMen;
		return $this;
	}
	
	public function getStatusAleMen()
	{
		return $this->statusAleMen;
	}
	
	public function setDataRecebidaAleMen( $dataRecebidaAleMen )
	{
		$this->dataRecebidaAleMen = $dataRecebidaAleMen;
		return $this;
	}
	
	public function getDataRecebidaAleMen()
	{
		return $this->dataRecebidaAleMen;
	}
	
	public function setTb_Mensagem_idMen( $tb_Mensagem_idMen )
	{
		$this->tb_Mensagem_idMen = $tb_Mensagem_idMen;
		return $this;
	}
	
	public function getTb_Mensagem_idMen()
	{
		return $this->tb_Mensagem_idMen;
	}
	/**
	* Retorna um array contendo os contatos
	* @param string $st_nome
	* @return Array
	*/
	public function _list( $idAleMen = null )
	{
		if(!is_null($st_nome))
			$st_query = "SELECT * FROM tb_alertaMensagem WHERE idAleMen = '$idAleMen';";
		else
			$st_query = 'SELECT * FROM tb_alertaMensagem;';	
		
		$v_alertas = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_alerta = new AlertaMensagemModel();
				$o_alerta->setIdAleMen($o_ret->idAleMen);
				$o_alerta->setStatusAleMen($o_ret->statusAleMen);
				$o_alerta->setDataRecebidaAleMen($o_ret->dataRecebidaAleMen);
				$o_alerta->setTb_Mensagem_idMen($o_ret->tb_Mensagem_idMen);
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
	public function loadById( $idAleMen )
	{
		$v_contatos = array();
		$st_query = "SELECT * FROM tb_alertaMensagem WHERE idAleMen = '$idAleMen';";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdAleMen($o_ret->idAleMen);
		$this->setStatusAleMen($o_ret->statusAleMen);
		$this->setDataRecebidaAleMen($o_ret->dataRecebidaAleMen);
		$this->setTb_Mensagem_idMen($o_ret->tb_Mensagem_idMen);		
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
		
		if(is_null($this->idAleMen))
			$st_query = "INSERT INTO tb_alertaMensagem
						(
							  	statusAleMen,
								dataRecebidaAleMen,
								tb_mensagem_idMen
						)
						VALUES
						(
							  	'$this->statusAleMen',
								NOW(),
								'$this->tb_Mensagem_idMen'			   	
						);";
		else
			$st_query = "UPDATE
							tb_alertaMensagem
						SET
							  	statusAleMen = '$this->statusAleMen',
								tb_mensagem_idMen = '$this->tb_Mensagem_idMen'	
						WHERE
							idMen = $this->idAleMen";
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
			$st_query = "DELETE tb_alertaMensagem  FROM tb_alertaMensagem
			INNER JOIN tb_mensagem ON tb_mensagem.idMen = tb_alertaMensagem.tb_mensagem_idMen
			INNER JOIN tb_lance ON tb_lance.idLan = tb_mensagem.tb_lance_idLan
			WHERE tb_lance.tb_transporte_idTransp = $idTransp";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	

}
?>