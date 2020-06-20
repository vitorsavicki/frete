<?php
require_once 'models/PerfilModel.php';


class PerfilModel extends PersistModelAbstract
{
      private $idPer;
	  private $nomePer;
	  private $codigoPer;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	public function setIdPer( $idPer )
	{
		$this->idPer = $idPer;
		return $this;
	}
	
	public function getIdPer()
	{
		return $this->idPer;
	}
	
	public function setNomePer( $nomePer )
	{
		$this->nomePer = $nomePer;
		return $this;
	}
	
	public function getNomePer()
	{
		return $this->nomePer;
	}
	
	public function setCodigoPer( $codigoPer )
	{
		$this->codigoPer = $codigoPer;
		return $this;
	}
	
	public function getCodigoPer()
	{
		return $this->codigoPer;
	}

	public function _list( $nomePer = null )
	{	
		if(!is_null($nomePer))
			$st_query = "SELECT * FROM tb_perfil WHERE nomePer = $nomePer;";
		else
			$st_query = 'SELECT * FROM tb_perfil;';	
		
		$v_perfis = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_perfil = new PerfilModel();
				$o_perfil->setIdPer($o_ret->idPer);
				$o_perfil->setNomePer($o_ret->nomePer);
				$o_perfil->setCodigoPer($o_ret->codigoPer);
				array_push($v_perfis, $o_perfil);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_perfis;
	}
	
	/**
	* Retorna os dados de uma cidade referente
	* a um determinado Id
	* @param integer $idCid
	* @return ContatoModel
	*/
	public function loadById( $idPer )
	{
		$v_contatos = array();
		$st_query = "SELECT * FROM tb_perfil WHERE idPer = $idPer;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdPer($o_ret->idPer);
		$this->setNomePer($o_ret->nomePer);
		$this->setCodigoPer($o_ret->codigoPer);
		return $this;
	}
	
	public function save()
	{
		if(is_null($this->idPer))
			$st_query = "INSERT INTO tb_perfil
						(
								nomePer,
								codigoPer
						)
						VALUES
						(
								'$this->idPer',
								'$this->nomePer'
								
						);";
		else
			$st_query = "UPDATE
							tb_Perfil
						SET
								nomePer = '$this->nomePer',
								codigoPer = '$this->codigoPer'
						WHERE
							idPer = $this->idPer;";
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
		if(!is_null($this->idPer))
		{
			$st_query = "DELETE FROM
							tb_Perfil
						WHERE idPer = $this->idPer";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>