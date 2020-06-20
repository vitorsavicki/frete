<?php
require_once 'models/AdministradorModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - AdminsitradorModel
*
* Responsável por gerenciar e persistir os dados dos  
* administrador 
**/
class AdministradorModel extends PersistModelAbstract
{
	private $idAdm;
  	private $primeiroNomeAdm;
  	private $sobreNomeAdm;
  	private $dataCadastroAdm;
  	private $statusAdm;
  	private $tb_usuario_idUsu;
	
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	
	/**
	 * Setters e Getters da
	 * classe AdministradorModel
	 */
	
	public function setIdAdm( $idAdm )
	{
		$this->idAdm = $idAdm;
		return $this;
	}
	
	public function getIdAdm()
	{
		return $this->idAdm;
	}
	
	public function setPrimeiroNomeAdm( $primeiroNomeAdm )
	{
		$this->primeiroNomeAdm = $primeiroNomeAdm;
		return $this;
	}
	
	public function getPrimeiroNomeAdm()
	{
		return $this->primeiroNomeAdm;
	}
	
	public function setSobreNomeAdm( $sobreNomeAdm )
	{
		$this->sobreNomeAdm = $sobreNomeAdm;
		return $this;
	}
	
	public function getSobreNomeAdm()
	{
		return $this->sobreNomeAdm;
	}
	
	public function setDataCadastroAdm( $dataCadastroAdm )
	{
		$this->dataCadastroAdm = $dataCadastroAdm;
		return $this;
	}
	
	public function getDataCadastroAdm()
	{
		return $this->dataCadastroAdm;
	}
	
	public function setStatusAdm( $statusAdm )
	{
		$this->statusAdm = $statusAdm;
		return $this;
	}
	
	public function getStatusAdm()
	{
		return $this->statusAdm;
	}
	
	public function setTb_usuario_idUsu( $tb_usuario_idUsu )
	{
		$this->tb_usuario_idUsu = $tb_usuario_idUsu;
		return $this;
	}
	
	public function getTb_usuario_idUsu()
	{
		return $this->tb_usuario_idUsu;
	}
	
	/**
	* Retorna um array contendo os administradores
	* @param string $idAdm
	* @return Array
	*/
	public function _list( $primeiroNomeAdm = null )
	{
		if(!is_null($primeiroNomeAdm))
			$st_query = "SELECT * FROM tb_administrador WHERE primeiroNomeAdm LIKE '%$primeiroNomeAdm%';";
		else
			$st_query = 'SELECT * FROM tb_administrador;';	
		
		$v_administradores = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_administrador = new AdministradorModel();
				$o_administrador->setIdAdm($o_ret->idAdm);
				$o_administrador->setPrimeiroNomeAdm($o_ret->primeiroNomeAdm);
				$o_administrador->setSobreNomeAdm($o_ret->sobreNomeAdm);
				$o_administrador->setDataCadastroAdm($o_ret->dataCadastroAdm);
				$o_administrador->setStatusAdm($o_ret->statusAdm);
				$o_administrador->setTb_usuario_idUsu($o_ret->tb_usuario_idUsu);
				array_push($v_administradores, $o_administrador);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_administradores;
	}
	
	/**
	* Retorna os dados de um administrador referente
	* a um determinado Id
	* @param integer $idAdm
	* @return ContatoModel
	*/
	public function loadById( $idAdm )
	{
		$v_administrador = array();
		$st_query = "SELECT * FROM tb_administrador WHERE idAdm = $idAdm;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdAdm($o_ret->idAdm);
				$this->setPrimeiroNomeAdm($o_ret->primeiroNomeAdm);
				$this->setSobreNomeAdm($o_ret->sobreNomeAdm);
				$this->setDataCadastroAdm($o_ret->dataCadastroAdm);
				$this->setStatusAdm($o_ret->statusAdm);
				$this->setTb_usuario_idUsu($o_ret->tb_usuario_idUsu);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de administrador. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idAdm))
			$st_query = "INSERT INTO tb_administrador
						(
							primeiroNomeAdm,
							sobreNomeAdm,
							dataCadastroAdm,
							statusAdm,
							tb_usuario_idUsu	
						)
						VALUES
						(
							'$this->primeiroNomeAdm',
							'$this->sobreNomeAdm',
							'$this->dataCadastroAdm',
							'$this->statusAdm',
							'$this->tb_usuario_idUsu'	
						);";
		else
			$st_query = "UPDATE
							tb_administrador
						SET
							primeiroNomeAdm = '$this->primeiroNomeAdm',
							sobreNomeAdm = '$this->sobreNomeAdm',
							dataCadastroAdm = '$this->dataCadastroAdm',
							statusAdm = '$this->statusAdm',
							tb_usuario_idUsu = '$this->tb_usuario_idUsu'
						WHERE
							idAdm = $this->idAdm";
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
	* administrador usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idAdm))
		{
			$st_query = "DELETE FROM
							tb_administrador
						WHERE con_idAdm = $this->idAdm";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	
}
?>