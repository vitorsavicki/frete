<?php
require_once 'models/CidadeModel.php';

/**
* @package Exemplo simples com MVC 

* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - CidadeModel
*
* Responsável por gerenciar e persistir os dados das cidades
**/
class CidadeModel extends PersistModelAbstract
{
      private $idCid;
	  private $nomeCid;
	  private $tb_Estado_idEst;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe CidadeModel
	 */
	
	public function setIdCid( $idCid )
	{
		$this->idCid = $idCid;
		return $this;
	}
	
	public function getIdCid()
	{
		return $this->idCid;
	}
	
	public function setNomeCid( $nomeCid )
	{
		$this->nomeCid = $nomeCid;
		return $this;
	}
	
	public function getNomeCid()
	{
		return $this->nomeCid;
	}
	
	public function setTb_Estado_idEst( $tb_Estado_idEst )
	{
		$this->tb_Estado_idEst = $tb_Estado_idEst;
		return $this;
	}
	
	public function getTb_Estado_idEst()
	{
		return $this->tb_Estado_idEst;
	}

	/**
	* Retorna um array contendo as Cidades
	* @param string $IdCid
	* @return Array
	*/
	public function _list( $tb_Estado_idEst = null )
	{
		if(!is_null($tb_Estado_idEst))
			$st_query = "SELECT * FROM tb_cidade WHERE tb_Estado_idEst = $tb_Estado_idEst;";
		else
			$st_query = 'SELECT * FROM tb_cidade;';	
		
		$v_cidades = array();
		try
		{

			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_cidade = new CidadeModel();
				$o_cidade->setIdCid($o_ret->idCid);
				$o_cidade->setNomeCid($o_ret->nomeCid);
				$o_cidade->setTb_Estado_idEst($o_ret->tb_Estado_idEst);
				array_push($v_cidades, $o_cidade);
				//$v_cidades[] = $o_cidade;
			}

		}
		catch(PDOException $e)
		{}				
		return $v_cidades;
	}
	
	/**
	* Retorna os dados de uma cidade referente
	* a um determinado Id
	* @param integer $idCid
	* @return ContatoModel
	*/
	public function loadById( $idCid )
	{
		$v_contatos = array();
		$st_query = "SELECT * FROM tb_cidade WHERE idCid = $idCid;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdCid($o_ret->idCid);
		$this->setNomeCid($o_ret->nomeCid);
		$this->setTb_Estado_idEst($o_ret->tb_Estado_idEst);
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
		if(is_null($this->idCid))
			$st_query = "INSERT INTO tb_cidade
						(
								idCid,
								nomeCid,
								tb_Estado_idEst
						)
						VALUES
						(
								'$this->idCid',
								'$this->nomeCid',
								'$this->tb_Estado_idEst'
						);";
		else
			$st_query = "UPDATE
							tb_cidade
						SET
								idCid = '$this->idCid',
								nomeCid = '$this->nomeCid',
								tb_Estado_idEst = '$this->tb_Estado_idEst'
						WHERE
							idCid = $this->idCid;";
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
		if(!is_null($this->idCid))
		{
			$st_query = "DELETE FROM
							tb_cidade
						WHERE idCid = $this->idCid";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>