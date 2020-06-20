<?php
require_once 'models/EstadoModel.php';
require_once 'lib/PersistModelAbstract.php';


/**
* @package Exemplo simples com MVC 

* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - EstadoModel
*
* Responsável por gerenciar e persistir os dados dos  
* Estados
**/
class EstadoModel extends PersistModelAbstract
{
      private $idEst;
	  private $nomeEst;
	  private $ufEst;
	  private $tb_Pais_IdPais;
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe EstadoModel
	 */
	
	public function setIdEst( $idEst)
	{
		$this->idEst = $idEst;
		return $this;
	}
	
	public function getIdEst()
	{
		return $this->idEst;
	}
	
	public function setNomeEst( $nomeEst )
	{
		$this->nomeEst = $nomeEst;
		return $this;
	}
	
	public function getNomeEst()
	{
		return $this->nomeEst;
	}
	
	public function setUfEst( $ufEst )
	{
		$this->ufEst = $ufEst;
		return $this;
	}
	
	public function getUfEst()
	{
		return $this->ufEst;
	}
	
	public function setTb_Pais_IdPais( $tb_Pais_IdPais )
	{
		$this->tb_Pais_IdPais = $tb_Pais_IdPais;
		return $this;
	}
	
	public function getTb_Pais_IdPais()
	{
		return $this->tb_Pais_IdPais;
	}
	
	/**
	* Retorna um array contendo os Estados
	* @param string $IdEst
	* @return Array
	*/
	public function _list( $tb_Pais_IdPais = null )
	{
		if(!is_null($tb_Pais_idPais))
			$st_query = "SELECT * FROM tb_estado WHERE tb_Pais_idPais = '$tb_Pais_IdPais';";
		else
			$st_query = 'SELECT * FROM tb_estado;';	
		
		$v_estados = array();
		try
		{
			//echo $st_query;
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_estado = new EstadoModel();
				$o_estado->setIdEst($o_ret->idEst);
				$o_estado->setNomeEst($o_ret->nomeEst);
				$o_estado->setUfEst($o_ret->ufEst);
				$o_estado->setTb_Pais_IdPais($o_ret->tb_Pais_IdPais);
				array_push($v_estados, $o_estado);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_estados;
	}
	
	/**
	* Retorna os dados de um estado referente
	* a um determinado Id
	* @param integer $idEst
	* @return ContatoModel
	*/
	public function loadById( $idEst )
	{
		$st_query = "SELECT * FROM tb_estado WHERE idEst = $idEst;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdEst($o_ret->idEst);
		$this->setNomeEst($o_ret->nomeEst);
		$this->setUfEst($o_ret->ufEst);
		$this->setTb_Pais_IdPais($o_ret->tb_Pais_IdPais);
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de estado. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idEst))
			$st_query = "INSERT INTO tb_estado
						(
								nomeEst,
								ufEst,
								tb_Pais_idPais
						)
						VALUES
						(
								'$this->nomeEst',
								'$this->ufEst',
								'$this->tb_Pais_idPais'
						);";
		else
			$st_query = "UPDATE
							tb_estado
						SET
								nomeEst = '$this->nomeEst',
								ufEst = '$this->ufEst',
								tb_Pais_idPais = '$this->tb_Pais_idPais'
						WHERE
							idEst = $this->idEst;";
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
	* estado usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idEst))
		{
			$st_query = "DELETE FROM
							tb_estado
						WHERE idEst = $this->idEst";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>