<?php
require_once 'models/RespostaPesquisaModel.php';


class RespostaPesquisaModel extends PersistModelAbstract
{
      private $idResPes;
	  private $tb_pergunta_pesquisa_idPqa;
	  private $tb_pessoa_idPes;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe CidadeModel
	 */
	
	public function setIdResPes( $idResPes )
	{
		$this->idResPes = $idResPes;
		return $this;
	}
	
	public function getIdResPes()
	{
		return $this->idResPes;
	}
	
	public function setTb_pergunta_pesquisa_idPqa( $tb_pergunta_pesquisa_idPqa )
	{
		$this->tb_pergunta_pesquisa_idPqa = $tb_pergunta_pesquisa_idPqa;
		return $this;
	}
	
	public function getTb_pergunta_pesquisa_idPqa()
	{
		return $this->tb_pergunta_pesquisa_idPqa;
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

	/**
	* Retorna um array contendo os Status
	* @param string $IdCid
	* @return Array
	*/
	public function _list( $idPqa = null )
	{	
		if(!is_null($idPqa))
			$st_query = "SELECT * FROM tb_resposta_pesquisa
			INNER JOIN tb_pergunta_pesquisa ON tb_pergunta_pesquisa.idPqa = tb_resposta_pesquisa.tb_pergunta_pesquisa_idPqa
			 WHERE tb_resposta_pesquisa.tb_pergunta_pesquisa_idPqa = $idPqa;";
		else
			$st_query = 'SELECT * FROM tb_resposta_pesquisa
            INNER JOIN tb_pergunta_pesquisa ON tb_pergunta_pesquisa.idPqa = tb_resposta_pesquisa.tb_pergunta_pesquisa_idPqa;';	
		
		$v_resposta_pesquisa = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_resposta_pesquisa = new RespostaPesquisaModel();
				$o_resposta_pesquisa->setIdResPes($o_ret->idResPes);
				$o_resposta_pesquisa->setTb_pergunta_pesquisa_idPqa($o_ret->tb_pergunta_pesquisa_idPqa);
				$o_resposta_pesquisa->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				array_push($v_resposta_pesquisa, $o_resposta_pesquisa);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_resposta_pesquisa;
	}
	
	public function loadById( $idResPes )
	{
		$v_resposta_pesquisa = array();
		$st_query = "SELECT * FROM tb_voucher WHERE idResPes = $idResPes;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdResPes($o_ret->idResPes);
		$this->setTb_pergunta_pesquisa_idPqa($o_ret->tb_pergunta_pesquisa_idPqa);
		$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
		return $this;
	}

	public function save()
	{
		if(is_null($this->idResPes))
			$st_query = "INSERT INTO tb_resposta_pesquisa
						(
								tb_pergunta_pesquisa_idPqa,
								tb_pessoa_idPes
						)
						VALUES
						(
								'$this->tb_pergunta_pesquisa_idPqa',
								'$this->tb_pessoa_idPes'
								
						);";
		else
			$st_query = "UPDATE
							tb_resposta_pesquisa
						SET
								tb_pergunta_pesquisa_idPqa = '$this->tb_pergunta_pesquisa_idPqa',
								tb_pessoa_idPes = '$this->tb_pessoa_idPes'
						WHERE
							idResPes = $this->idResPes;";
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
		if(!is_null($this->idResPes))
		{
			$st_query = "DELETE FROM
							tb_tb_resposta_pesquisa
						WHERE idResPes = $this->idResPes";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>