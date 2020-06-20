<?php
require_once 'models/TelefoneModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - TelefoneModel
*
* Responsável por gerenciar e persistir os dados dos  
* Clientes
**/
class ClienteModel extends PersistModelAbstract
{
	private $idCli;
	private $primeiroNomeCli;
	private $sobreNomeCli;
	private $emailCli;
	private $cpfCnpjCli;
	private $fotoCli;
	private $dataCadastroCli;
	private $telefoneFixoCli;
	private $telefoneCelularCli;
	private $statusCli;
	private $tb_usuario_idUsu;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe TelefoneModel
	 */
	public function setIdCli ($idCli)
	{
		$this->idCli = $idCli;
		return $this;
	}

	
	public function getIdCli()
	{
		return $this->idCli;
	}
	
	public function setPrimeiroNomeCli( $primeiroNomeCli )
	{
		$this->primeiroNomeCli = $primeiroNomeCli;
		return $this;
	}
	
	public function getPrimeiroNomeCli()
	{
		return $this->primeiroNomeCli;
	}
	
	public function setSobreNomeCli( $sobreNomeCli )
	{
		$this->sobreNomeCli = $sobreNomeCli;
		return $this;
	}
	
	public function getSobreNomeCli()
	{
		return $this->sobreNomeCli;
	}
	
	public function setEmailCli( $emailCli )
	{
		$this->emailCli = $emailCli;
		return $this;
	}
	
	public function getEmailCli()
	{
		return $this->emailCli;
	}
	
	public function setCpfCnpjCli( $cpfCnpjCli )
	{
		$this->cpfCnpjCli = $cpfCnpjCli;
		return $this;
	}
	
	public function getCpfCnpjCli()
	{
		return $this->cpfCnpjCli;
	}
	
	public function setFotoCli( $fotoCli )
	{
		$this->fotoCli = $fotoCli;
		return $this;
	}
	
	public function getFotoCli()
	{
		return $this->fotoCli;
	}
	
	public function setDataCadastroCli( $dataCadastroCli )
	{
		$this->dataCadastroCli = $dataCadastroCli;
		return $this;
	}
	
	public function getDataCadastroCli()
	{
		return $this->dataCadastroCli;
	}
	
	public function setTelefoneFixoCli( $telefoneFixoCli )
	{
		$this->telefoneFixoCli = $telefoneFixoCli;
		return $this;
	}
	
	public function getTelefoneFixoCli()
	{
		return $this->telefoneFixoCli;
	}
	
	public function setTelefoneCelularCli( $telefoneCelularCli)
	{
		$this->telefoneCelularCli = $telefoneCelularCli;
		return $this;
	}
	
	public function getTelefoneCelularCli()
	{
		return $this->telefoneCelularCli;
	}
	
	public function setStatusCli( $statusCli )
	{
		$this->statusCli = $statusCli;
		return $this;
	}
	
	public function getStatusCli()
	{
		return $this->statusCli;
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
	* Retorna um array contendo os clientes
	* @param string $primeiroNomeCli
	* @return Array
	*/
	public function _list( $primeiroNomeCli = null )
	{
		if(!is_null($primeiroNomeCli))
			$st_query = "SELECT * FROM tb_cliente WHERE primeiroNomeCli LIKE '%$primeiroNomeCli%';";
		else
			$st_query = 'SELECT * FROM tb_cliente;';	
		
		$v_cliente = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_cliente = new ClienteModel();
				$o_cliente->setIdCli($o_ret->idCli);
				$o_cliente->setPrimeiroNomeCli($o_ret->primeiroNomeCli);
				$o_cliente->setSobreNomeCli($o_ret->sobreNomeCli);
				$o_cliente->setEmailCli($o_ret->emailCli);
				$o_cliente->setCpfCnpjCli($o_ret->cpfCnpjCli);
				$o_cliente->setFotoCli($o_ret->fotoCli);
				$o_cliente->setDataCadastroCli($o_ret->dataCadastroCli);
				$o_cliente->setTelefoneFixoCli($o_ret->telefoneFixoCli);
				$o_cliente->setTelefoneCelularCli($o_ret->telefoneCelularCli);
				$o_cliente->setStatusCli($o_ret->statusCli);
				$o_cliente->setTb_usuario_idUsu($o_ret->tb_usuario_idUsu);
				array_push($v_cliente, $o_cliente);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_cliente;
	}
	
	/**
	* Retorna os dados de um cliente referente
	* a um determinado Id
	* @param integer $idCli
	* @return ClienteModel
	*/
	public function loadById( $idCli )
	{
		$v_cliente = array();
		$st_query = "SELECT * FROM tb_cliente WHERE idCli = $idCli;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdCli($o_ret->idCli);
		$this->setPrimeiroNomeCli($o_ret->primeiroNomeCli);
		$this->setSobreNomeCli($o_ret->sobreNomeCli);	
		$this->setEmailCli($o_ret->emailCli);
		$this->setCpfCnpjCli($o_ret->cpfCnpjCli);
		$this->setFotoCli($o_ret->fotoCli);
		$this->setDataCadastroCli($o_ret->dataCadastroCli);
		$this->setTelefoneFixoCli($o_ret->telefoneFixoCli);
		$this->setTelefoneCelularCli($o_ret->telefoneCelularCli);
		$this->setStatusCli($o_ret->statusCli);
		$this->setTb_usuario_idUsu($o_ret->tb_usuario_idUsu);
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de contato. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		
		if(is_null($this->idCli))
		{

		
			$idUsu = (int)$_SESSION['idUsuario'];
			$st_query = "INSERT INTO tb_cliente
						(	
							
							primeiroNomeCli,
						    sobreNomeCli,
						    emailCli,
						    cpfCnpjCli,
						    fotoCli,
						    dataCadastroCli,
						    telefoneFixoCli,
						    telefoneCelularCli,
						    statusCli,
						    tb_usuario_idUsu
						    
						
						)
						VALUES
						(	
							'$this->primeiroNomeCli',
							'$this->sobreNomeCli',	
							'$this->emailCli',
							'$this->cpfCnpjCli',
							'$this->fotoCli',
							'$this->dataCadastroCli',
							'$this->telefoneFixoCli',
							'$this->telefoneCelularCli',
							'$this->statusCli',
							'$idUsu'
						
						);";
		}
		else
			$st_query = "UPDATE
							tb_cliente
						SET
						
							primeiroNomeCli = '$this->primeiroNomeCli',
							sobreNomeCli = '$this->sobreNomeCli',	
							emailCli = '$this->emailCli',
							cpfCnpjCli = '$this->cpfCnpjCli',
							fotoCli = '$this->fotoCli',
							dataCadastroCli = '$this->dataCadastroCli',
							telefoneFixoCli = '$this->telefoneFixoCli',
							telefoneCelularCli = '$this->telefoneCelularCli',
							statusCli = '$this->statusCli',
							tb_usuario_idUsu = '$this->tb_usuario_idUsu'
						WHERE
							idCli = $this->idCli";
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
	* cliente usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idCli))
		{
			$st_query = "DELETE FROM
							tb_cliente
						WHERE idCli = $this->idCli";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	

}
?>