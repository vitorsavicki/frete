<?php
require_once 'models/MensagemModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - MensagemModel
*
* Responsável por gerenciar e persistir os dados das 
* Mensagens da Agenda Telefônica 
**/
class MensagemModel extends PersistModelAbstract
{
	private $idMen;
    private $conteudoMen;
  	private $dataMen;
  	private $tb_pessoa_idPes;
  	private $tb_lance_idLan;
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe MensagemModel
	 */
	
	public function setIdMen( $idMen )
	{
		$this->idMen = $idMen;
		return $this;
	}
	
	public function getIdMen()
	{
		return $this->idMen;
	}
	
	public function setConteudoMen( $conteudoMen )
	{
		$this->conteudoMen = $conteudoMen;
		return $this;
	}
	
	public function getConteudoMen()
	{
		return $this->conteudoMen;
	}
	
	public function setDataMen( $dataMen)
	{
		$this->dataMen = $dataMen;
		return $this;
	}
	
	public function getDataMen()
	{
		return $this->dataMen;
	}
	
	public function setTb_pessoa_idPes( $tb_pessoa_idPes )
	{
		$this->tb_pessoa_idPes = $tb_pessoa_idPes;
		return $this;
	}
	
	public function getTb_pessoa_idPes()
	{
		return $this->tb_pessoa_idPes;
	}
	
	public function setTb_lance_idLan( $tb_lance_idLan )
	{
		$this->tb_lance_idLan = $tb_lance_idLan;
		return $this;
	}
	
	public function getTb_lance_idLan()
	{
		return $this->tb_lance_idLan;
	}
  
 
	/**
	* Retorna um array contendo as mensagens
	* @param string $idMen
	* @return Array
	*/
	public function _list( $conteudoMen = null )
	{
		if(!is_null($conteudoMen))
			$st_query = "SELECT * FROM tb_mensagem WHERE conteudoMen LIKE '%$conteudoMen%';";
		else
			$st_query = 'SELECT * FROM tb_mensagem;';	
		
		$v_mensagens = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_mensagem = new MensagemModel();
				$o_mensagem->setIdMen($o_ret->idMen);
				$o_mensagem->setConteudoMen($o_ret->conteudoMen);
				$o_mensagem->setDataMen($o_ret->dataMen);
				$o_mensagem->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				$o_mensagem->setTb_lance_idLan($o_ret->tb_lance_idLan);
				array_push($v_mensagens, $o_mensagem);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_mensagens;
	}
	
	/**
	* Retorna os dados de uma mensagem referente
	* a um determinado Id
	* @param integer $idMen
	* @return MensagemModel
	*/
	public function loadById( $idMen )
	{
		$v_mensagens = array();
		$st_query = "SELECT * FROM tb_mensagem WHERE idMen= $idMen;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdMen($o_ret->idMen);
				$this->setConteudoMen($o_ret->conteudoMen);
				$this->setDataMen($o_ret->dataMen);
				$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				$this->setTb_lance_idLan($o_ret->tb_lance_idLan);	
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de mensgaem. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save(){
		
		if(is_null($this->idMen))
			$st_query = "INSERT INTO tb_mensagem
						(
							  conteudoMen,
							  dataMen,
							  tb_pessoa_idPes,
							  tb_lance_idLan
						)
						VALUES
						(
							  '$this->conteudoMen',
							   NOW(),
							  '$this->tb_pessoa_idPes',
							  '$this->tb_lance_idLan'
						);";
		else
			$st_query = "UPDATE
							tbl_mensagem
						SET
							  conteudoMen = '$this->conteudoMen',
							  tb_pessoa_idPes = '$this->tb_pessoa_idPes',
							  tb_lance_idLan = '$this->tb_lance_idLan'	
						WHERE
							idMen = $this->idMen";
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
	* mensagem usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idMen))
		{
			$st_query = "DELETE FROM
							tb_mensagem
						WHERE idMen = $this->idMen";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	
	public function deleteMenLan($idTransp)
	{
		if(!is_null($idTransp))
		{
			$st_query = "DELETE tb_mensagem FROM
							tb_mensagem
						INNER JOIN tb_lance ON tb_lance.idLan =  tb_mensagem. tb_lance_idLan
						INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_lance.tb_transporte_idTransp
						WHERE tb_transporte.idTransp = $idTransp";
			//echo $st_query;
			//exit;
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	
}
?>
