<?php
require_once 'models/SituacaoMensalidadeModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - SituacaoMensalidadeModel
*
* Responsável por gerenciar e persistir os dados das 
* SituacaoMensalidades 
**/
class SituacaoMensalidadeModel extends PersistModelAbstract
{
	private $idSit;
  	private $descricaoSit;	
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	
	/**
	 * Setters e Getters da
	 * classe MensalidadeModel
	 */
	
	public function setIdSit( $idSit )
	{
		$this->idSit = $idSit;
		return $this;
	}
	
	public function getIdSit()
	{
		return $this->idSit;
	}
	
	public function setDescricaoSit( $descricaoSit )
	{
		$this->descricaoSit = $descricaoSit;
		return $this;
	}
	
	public function getDescricaoSit()
	{
		return $this->descricaoSit;
	}
	
	/**
	* Retorna um array contendo as situações das mensalidades
	* @param string $descricaoSit
	* @return Array
	*/
	public function _list( $descricaoSit = null )
	{
		if(!is_null($descricaoSit))
			$st_query = "SELECT * FROM tb_situacaoMensalidade WHERE descricaoSit LIKE '%$descricaoSit%';";
		else
			$st_query = 'SELECT * FROM tb_situacaoMensalidade;';	
		
		$v_situacaoMensalidades = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_situacaoMensalidade = new SituacaoMensalidadeModel();
				$o_situacaoMensalidade->setIdSit($o_ret->idSit);
				$o_situacaoMensalidade->setDescricaoSit($o_ret->descricaoSit);
				array_push($v_situacaoMensalidades, $o_situacaoMensalidade);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_situacaoMensalidades;
	}
	
	/**
	* Retorna os dados de uma situação mensalidade referente
	* a um determinado Id
	* @param integer $idSit
	* @return MensalidadeModel
	*/
	public function loadById( $idSit )
	{
		$v_mensagens = array();
		$st_query = "SELECT * FROM tb_situacaomensalidade WHERE idSit = $idSit;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdSit($o_ret->idSit);
				$this->setDescricaoSit($o_ret->descricaoSit);
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de situação mensalidade. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idSit))
			$st_query = "INSERT INTO tb_SituacaoMensalidade
						(
							descricaoSit
						)
						VALUES
						(
							'$this->descricaoSit'	
						);";
		else
			$st_query = "UPDATE
							tb_SituacaoMensalidade
						SET
							descricaoSit = '$this->descricaoSit'
						WHERE
							idSit = $this->idSit";
		try
		{
			//echo $st_query;
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
	* situação mensalidade usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idSit))
		{
			$st_query = "DELETE FROM
							tb_situacaomensalidade
						WHERE idSit = $this->idSit";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
}
?>