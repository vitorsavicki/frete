<?php
require_once 'models/DadoAdicionalModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - DadoAdicionalModel
*
* Responsável por gerenciar e persistir os dados dos  
* Dados Adicionais
**/
class DadoAdicionalModel extends PersistModelAbstract
{
	private $idDadAdi;
  	private $comentarioDadAdi;
  	private $precoMaximoDadAdi;
  	private $numAjudantesDadAdi;
  	private $tb_transporte_idTransp;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe DadoAdicionalModel
	 */
	
	public function setIdDadAdi( $idDadAdi)
	{
		$this->idDadAdi = $idDadAdi;
		return $this;
	}
	
	public function getIdDadAdi()
	{
		return $this->idDadAdi;
	}
	
	public function setComentarioDadAdi( $comentarioDadAdi )
	{
		$this->comentarioDadAdi = $comentarioDadAdi;
		return $this;
	}
	
	public function getComentarioDadAdi()
	{
		return $this->comentarioDadAdi;
	}
	
	public function setPrecoMaximoDadAdi( $precoMaximoDadAdi )
	{
		$this->precoMaximoDadAdi = $precoMaximoDadAdi;
		return $this;
	}
	
	public function getPrecoMaximoDadAdi()
	{
		return $this->precoMaximoDadAdi;
	}
	
	public function setNumAjudantesDadAdi( $numAjudantesDadAdi)
	{
		$this->numAjudantesDadAdi = $numAjudantesDadAdi;
		return $this;
	}
	
	public function getNumAjudantesDadAdi()
	{
		return $this->numAjudantesDadAdi;
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
	* Retorna um array contendo os Dados Adicionais
	* @param string $idDadAdi
	* @return Array
	*/
	public function _list( $comentarioDadAdi = null )
	{
		if(!is_null($comentarioDadAdi))
			$st_query = "SELECT * FROM tb_dado_adicional WHERE comentarioDadAdi LIKE '%comentarioDadAdi%';";
		else
			$st_query = 'SELECT * FROM tb_dado_adicional;';	
		
		$v_dados = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_dado = new DadoAdicionalModel();
				$o_dado->setIdDadAdi($o_ret->idDadAdi);
				$o_dado->setComentarioDadAdi($o_ret->comentarioDadAdi);
				$o_dado->setPrecoMaximoDadAdi($o_ret->precoMaximoDadAdi);
				$o_dado->setNumAjudantesDadAdi($o_ret->numAjudantesDadAdi);
				$o_dado->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				
				array_push($v_dados, $o_dado);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_dados;
	}
	
	/**
	* Retorna os dados de um dado adicional referente
	* a um determinado Id
	* @param integer $idDadAdi
	* @return DadoAdicionalModel
	*/
	public function loadById( $idDadAdi )
	{
		$v_dados = array();
		$st_query = "SELECT * FROM tb_dado_adicional WHERE idDadAdi = $idDadAdi;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdDadAdi($o_ret->idDadAdi);
		$this->setComentarioDadAdi($o_ret->comentarioDadAdi);
		$this->setPrecoMaximoDadAdi($o_ret->precoMaximoDadAdi);
		$this->setNumAjudantesDadAdi($o_ret->numAjudantesDadAdi);
		$this->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);	
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de dado adicional. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idDadAdi))
			$st_query = "INSERT INTO tb_dado_adicional
						(
							 comentarioDadAdi,
							 precoMaximoDadAdiL,
							 numAjudantesDadAdi,
							 tb_transporte_idTransp
						
						)
						VALUES
						(
							'$this->comentarioDadAdi',
							'$this->precoMaximoDadAdi'
							'$this->numAjudantesDadAdi',
							'$this->tb_transporte_idTransp'
						);";
		else
			$st_query = "UPDATE
							tb_dado_adicional
						SET
							 comentarioDadAdi = '$this->comentarioDadAdi',
							 precoMaximoDadAdiL = '$this->precoMaximoDadAdi',
							 numAjudantesDadAdi = '$this->numAjudantesDadAdi',
							 tb_transporte_idTransp = '$this->tb_transporte_idTransp'
						WHERE
							idDadAdi = $this->idDadAdi";
		try
		{
			
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
	* dado adicional usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idDadAdi))
		{
			$st_query = "DELETE FROM
							tb_dado_adicional
						WHERE idDadAdi = $this->idDadAdi";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	

	
}
?>