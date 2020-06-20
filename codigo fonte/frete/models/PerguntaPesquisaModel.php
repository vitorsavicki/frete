<?php
require_once 'models/PerguntaPesquisaModel.php';


/**
* @package Exemplo simples com MVC 

* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - StatusModel
*
**/
class PerguntaPesquisaModel extends PersistModelAbstract
{
      private $idPqa;
	  private $nomePqa;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 */
	
	public function setIdPqa( $idPqa )
	{
		$this->idPqa = $idPqa;
		return $this;
	}
	
	public function getIdPqa()
	{
		return $this->idPqa;
	}
	
	public function setNomePqa( $nomePqa )
	{
		$this->nomePqa = $nomePqa;
		return $this;
	}
	
	public function getNomePqa()
	{
		return $this->nomePqa;
	}
	


	/**
	* @return Array
	*/
	public function _list( $nomePqa = null )
	{	
		if(!is_null($nomePqa))
			$st_query = "SELECT * FROM tb_pergunta_pesquisa WHERE nomePqa = $nomePqa;";
		else
			$st_query = 'SELECT * FROM tb_pergunta_pesquisa;';	
		
		$v_pergunta_pesquisa = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_pergunta_pesquisa = new PerguntaPesquisaModel();
				$o_pergunta_pesquisa->setIdPqa($o_ret->idPqa);
				$o_pergunta_pesquisa->setNomePqa($o_ret->nomePqa);
				array_push($v_pergunta_pesquisa, $o_pergunta_pesquisa);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_pergunta_pesquisa;
	}
	
	/**

	*/
	public function loadById( $idPqa )
	{
		$v_pergunta_pesquisa = array();
		$st_query = "SELECT * FROM tb_pqergunta_pesquisa WHERE idPqa = $idPqa;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdPqa($o_ret->idPqa);
		$this->setNomePqa($o_ret->nomePqa);
		return $this;
	}
	
	/**
	*/
	public function save()
	{
		if(is_null($this->idPqa))
			$st_query = "INSERT INTO tb_pergunta_pesquisa
						(
								nomePqa
						)
						VALUES
						(
								'$this->nomePqa'
								
						);";
		else
			$st_query = "UPDATE
							tb_pergunta_pesquisa
						SET
								nomePqa= '$this->nomePqa'
						WHERE
							idPqa = $this->idPqa;";
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
		if(!is_null($this->idPqa))
		{
			$st_query = "DELETE FROM
							tb_pergunta_pesquisa
						WHERE idPqa = $this->idPqa";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>