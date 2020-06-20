<?php
require_once 'models/ConteudoTransporteModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - ConteudoTransporteModel
*
* Responsável por gerenciar e persistir os dados dos  
*Conteudos do Transporte 
**/
class ConteudoTransporteModel extends PersistModelAbstract
{
	private $idConTran;
  	private $descricaoItemConTran;
	private $qtdeConTran;
  	private $alturaConTran;
  	private $larguraConTran;
  	private $comprimentoConTran;
  	private $pesoConTran;
  	private $tb_item_idItem;
  	private $tb_transporte_idTransp;
	
	
	function __construct()
	{
		parent::__construct();
		;
	}
	
	
	/**
	 * Setters e Getters da
	 * classe ConteudoTransporteModel
	 */
	
	public function setIdConTran( $idConTran )
	{
		$this->idConTran = $idConTran;
		return $this;
	}
	
	public function getIdConTran()
	{
		return $this->idConTran;
	}
	
	public function setDescricaoItemConTran( $descricaoItemConTran )
	{
		$this->descricaoItemConTran = $descricaoItemConTran;
		return $this;
	}
	
	public function getDescricaoItemConTran()
	{
		return $this->descricaoItemConTran;
	}
	
	public function setAlturaConTran( $alturaConTran )
	{
		$this->alturaConTran = $alturaConTran;
		return $this;
	}
	
	public function getAlturaConTran()
	{
		return $this->alturaConTran;
	}
	
	public function setQtdeConTran( $qtdeConTran )
	{
		$this->qtdeConTran = $qtdeConTran;
		return $this;
	}
	
	public function getQtdeConTran()
	{
		return $this->qtdeConTran;
	}
	
	public function setLarguraConTran( $larguraConTran )
	{
		$this->larguraConTran = $larguraConTran;
		return $this;
	}
	
	public function getLarguraConTran()
	{
		return $this->larguraConTran;
	}
	
	public function setComprimentoConTran( $comprimentoConTran )
	{
		$this->comprimentoConTran = $comprimentoConTran;
		return $this;
	}
	
	public function getComprimentoConTran()
	{
		return $this->comprimentoConTran;
	}
	
	public function setPesoConTran( $pesoConTran )
	{
		$this->pesoConTran = $pesoConTran;
		return $this;
	}
	
	public function getPesoConTran()
	{
		return $this->pesoConTran;
	}
	
	public function setTb_item_idItem( $tb_item_idItem )
	{
		$this->tb_item_idItem = $tb_item_idItem;
		return $this;
	}
	
	public function getTb_item_idItem()
	{
		return $this->tb_item_idItem;
	}
	
	public function setTb_transporte_idTransp( $tb_transporte_idTransp )
	{
		$this->tb_transporte_idTransp = $tb_transporte_idTransp;
		return $this;
	}
	
	public function getTb_transporte_idTransp()
	{
		return $this->tb_transporte_idTransp;
	}
	/**
	* Retorna um array contendo os conteudos do transporte
	* @param string $idConTran
	* @return Array
	*/
	
	public function _list( $descricaoItemConTran = null, $Tb_transporte_idTransp = null)
	{
		$st_query = "SELECT * FROM tb_conteudo_transporte";
		$st_where = null;
		if(!is_null($descricaoItemConTran))
			$st_where = " WHERE descricaoItemConTran = '$descricaoItemConTran' ";
		if(!is_null($Tb_transporte_idTransp))
			if(!is_null($st_where))
				$st_where .= " AND Tb_transporte_idTransp = '$Tb_transporte_idTransp' ";
			else
				$st_where = " WHERE Tb_transporte_idTransp = '$Tb_transporte_idTransp' ";
		$st_query .= $st_where . ";";
		//echo $st_query;
		$v_conteudos = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_conteudo = new ConteudoTransporteModel();
				$o_conteudo->setIdConTran($o_ret->idConTran);
				$o_conteudo->setDescricaoItemConTran($o_ret->descricaoItemConTran);
				$o_conteudo->setQtdeConTran($o_ret->qtdeConTran);
				$o_conteudo->setAlturaConTran($o_ret->alturaConTran);
				$o_conteudo->setLarguraConTran($o_ret->larguraConTran);
				$o_conteudo->setComprimentoConTran($o_ret->comprimentoConTran);
				$o_conteudo->setPesoConTran($o_ret->pesoConTran);
				$o_conteudo->setTb_item_idItem($o_ret->tb_item_idItem);			
				array_push($v_conteudos, $o_conteudo);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_conteudos;
	}
	
	/**
	* Retorna os dados de um conteudo do transporte referente
	* a um determinado Id
	* @param integer $idConTran
	* @return ConteudoTransporteModel
	*/
	public function loadById( $idConTran )
	{
		$v_conteudos = array();
		$st_query = "SELECT * FROM tb_conteudo_transporte WHERE idConTran = $idConTran;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdConTran($o_ret->idConTran);
		$this->setDescricaoItemConTran($o_ret->descricaoItemConTran);
		$this->setQtdeConTran($o_ret->qtdeConTran);
		$this->setAlturaConTran($o_ret->alturaConTran);
		$this>setLarguraConTran($o_ret->larguraConTran);
		$this->setComprimentoConTran($o_ret->comprimentoConTran);
		$this->setPesoConTran($o_ret->pesoConTran);
		$this->setTb_item_idItem($o_ret->tb_item_idItem);
		$this->setTb_transporte_idTransp($o_ret->Tb_transporte_idTransp);
				
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de conteudo do transporte. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idConTran))
			$st_query = "INSERT INTO tb_conteudo_transporte
						(
							  	descricaoItemConTran,
								qtdeConTran,
							    alturaConTran,
							  	larguraConTran,
							  	comprimentoConTran,
							  	pesoConTran,
							  	tb_item_idItem,
							    tb_transporte_idTransp
						)
						VALUES
						(
							  	'$this->descricaoItemConTran',
								'$this->qtdeConTran',
							  	'$this->alturaConTran',
							  	'$this->larguraConTran',
							  	'$this->comprimentoConTran',
							  	'$this->pesoConTran',
							  	'$this->tb_item_idItem',
							  	'$this->tb_transporte_idTransp'
						);";
		else
			$st_query = "UPDATE
							tbl_conteudo_transporte
						SET
								descricaoItemConTran = '$this->descricaoItemConTran',
								qtdeConTran = '$this->qtdeConTran',
							    alturaConTran = '$this->alturaConTran',
							  	larguraConTran = '$this->larguraConTran',
							  	comprimentoConTran = '$this->comprimentoConTran',
							  	pesoConTran = '$this->pesoConTran',
							  	tb_item_idItem = '$this->tb_item_idItem',
							    tb_transporte_idTransp = '$this->tb_transporte_idTransp'
						WHERE
							idConTran = $this->idConTran;";
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
	* conteudo do transporte usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idConTran))
		{
			$st_query = "DELETE FROM
							tb_conteudo_transporte
						WHERE idConTran = $this->idConTran";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

}
?>