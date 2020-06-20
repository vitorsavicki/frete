<?php
require_once 'models/MensalidadeModel.php';
require_once 'models/SituacaoMensalidadeModel.php';
require_once 'models/PessoaModel.php';
/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - MensalidadeModel
*
* Responsável por gerenciar e persistir os dados das 
* Mensalidades 
**/
class MensalidadeModel extends PersistModelAbstract
{
	private $idMensa;
  	private $dataVencimentoMensa;
  	private $dataPagamentoMensa;
  	private $valorMensa;
  	private $tb_situacaoMensalidade_idSit;
	private $tb_pessoa_idPes;
	private $primeiroNomePes;
	private $cpfCnpjPes;
	private $sobreNomePes;
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	
	/**
	 * Setters e Getters da
	 * classe MensalidadeModel
	 */
	
	public function setIdMensa( $idMensa )
	{
		$this->idMensa = $idMensa;
		return $this;
	}
	
	public function getIdMensa()
	{
		return $this->idMensa;
	}
	
	public function setDataVencimentoMensa( $dataVencimentoMensa )
	{
		$this->dataVencimentoMensa = $dataVencimentoMensa;
		return $this;
	}
	
	public function getDataVencimentoMensa()
	{
		return $this->dataVencimentoMensa;
	}
	
	public function setDataPagamentoMensa( $dataPagamentoMensa )
	{
		$this->dataPagamentoMensa = $dataPagamentoMensa;
		return $this;
	}
	
	public function getDataPagamentoMensa()
	{
		return $this->dataPagamentoMensa;
	}
	
	public function setValorMensa( $valorMensa )
	{
		$this->valorMensa = $valorMensa;
		return $this;
	}
	
	public function getValorMensa()
	{
		return $this->valorMensa;
	}
	
	public function setTb_situacaoMensalidade_idSit( $tb_situacaoMensalidade_idSit )
	{
		$this->tb_situacaoMensalidade_idSit = $tb_situacaoMensalidade_idSit;
		return $this;
	}
	
	public function getTb_situacaoMensalidade_idSit()
	{
		return $this->tb_situacaoMensalidade_idSit;
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
	
	public function setPrimeiroNomePes( $primeiroNomePes )
	{
		$this->primeiroNomePes = $primeiroNomePes;
		return $this;
	}
	
	public function getPrimeiroNomePes()
	{
		return $this->primeiroNomePes;
	}
	
	public function setSobreNomePes( $sobreNomePes )
	{
		$this->sobreNomePes = $sobreNomePes;
		return $this;
	}
	
	public function getSobreNomePes()
	{
		return $this->sobreNomePes;
	}
	
	public function setNomeSta( $nomeSta )
	{
		$this->nomeSta = $nomeSta;
		return $this;
	}
	
	public function getNomeSta()
	{
		return $this->nomeSta;
	}
	
	public function setCpfCnpjPes( $cpfCnpjPes )
	{
		$this->cpfCnpjPes = $cpfCnpjPes;
		return $this;
	}
	
	public function getCpfCnpjPes()
	{
		return $this->cpfCnpjPes;
	}
	/**
	* Retorna um array contendo as mensalidades
	* @param string $idMensa
	* @return Array
	*/
	public function _list($tb_pessoa_idPes = null)
	{
		if(!is_null($tb_pessoa_idPes ))
			$st_query = "SELECT * FROM tb_mensalidade 
				INNER JOIN tb_pessoa ON idPes = tb_pessoa_idPes
				INNER JOIN tb_status ON idSta = tb_Status_idSta
				WHERE tb_pessoa_idPes = '$tb_pessoa_idPes';";
		else
			$st_query = "SELECT * FROM tb_mensalidade
			INNER JOIN tb_pessoa ON idPes = tb_pessoa_idPes
			INNER JOIN tb_status ON idSta = tb_Status_idSta;";	
			//echo $st_query;
			//exit;
		$v_mensalidades = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_mensalidade = new MensalidadeModel();
				$o_mensalidade->setIdMensa($o_ret->idMensa);
				$o_mensalidade->setDataVencimentoMensa($o_ret->dataVencimentoMensa);
				$o_mensalidade->setDataPagamentoMensa($o_ret->dataPagamentoMensa);
				$o_mensalidade->setValorMensa($o_ret->valorMensa);
				$o_mensalidade->setTb_situacaoMensalidade_idSit($o_ret->tb_situacaoMensalidade_idSit);
				$o_mensalidade->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				$o_mensalidade->setPrimeiroNomePes($o_ret->primeiroNomePes);
				$o_mensalidade->setsobreNomePes($o_ret->sobreNomePes);
				$o_mensalidade->setNomeSta($o_ret->nomeSta);
				$o_mensalidade->setCpfCnpjPes($o_ret->cpfCnpjPes);
				array_push($v_mensalidades, $o_mensalidade);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_mensalidades;
	}
	
	/**
	* Retorna os dados de uma mensalidade referente
	* a um determinado Id
	* @param integer $idMensa
	* @return MensalidadeModel
	*/
	public function loadById( $idMensa )
	{
		$v_mensagens = array();
		$st_query = "SELECT * FROM tb_mensalidade
					INNER JOIN tb_pessoa ON idPes = tb_pessoa_idPes
					INNER JOIN tb_status ON idSta = tb_Status_idSta
		 			WHERE idMensa = $idMensa;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdMensa($o_ret->idMensa);
				$this->setDataVencimentoMensa($o_ret->dataVencimentoMensa);
				$this->setDataPagamentoMensa($o_ret->dataPagamentoMensa);
				$this->setValorMensa($o_ret->valorMensa);
				$this->setTb_situacaoMensalidade_idSit($o_ret->tb_situacaoMensalidade_idSit);
				$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);	
				$this->setPrimeiroNomePes($o_ret->primeiroNomePes);
				$this->setSobreNomePes($o_ret->sobreNomePes);
				$this->setCpfCnpjPes($o_ret->cpfCnpjPes);
				$this->setNomeSta($o_ret->nomeSta);
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de mensalidade. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idMensa)){
			$st_query = "INSERT INTO tb_mensalidade
						(
							dataVencimentoMensa,
							valorMensa,
							tb_situacaoMensalidade_idSit,
							tb_pessoa_idPes
						)
						VALUES
						(
							'$this->dataVencimentoMensa',
							'$this->valorMensa',
							'$this->tb_situacaoMensalidade_idSit',
							'$this->tb_pessoa_idPes'
						);";
						$st_query .= " SELECT LAST_INSERT_ID();";
		}
		else{
			$st_query = "UPDATE
							tb_mensalidade
						SET
							dataVencimentoMensa = '$this->dataVencimentoMensa',
							dataPagamentoMensa = '$this->dataPagamentoMensa',
							valorMensa = '$this->valorMensa',
							tb_situacaoMensalidade_idSit = '$this->tb_situacaoMensalidade_idSit',
							tb_pessoa_idPes = '$this->tb_pessoa_idPes'
						WHERE
							idMensa = $this->idMensa";
		}							
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
	* mensalidade usando como referencia, o id da classe.
	*/
	public function delete($idMensa)
	{
		if(!is_null($idMensa))
		{
			$st_queryBol = "DELETE FROM
							tb_boleto
						WHERE idMensa = $idMensa";
						
			$st_queryMensa = "DELETE FROM
							tb_mensalidade
						WHERE idMensa = $idMensa";		
				$this->o_db->exec($st_queryBol);
				if($this->o_db->exec($st_queryMensa) > 0)
				return true;
				
		
		}
		return false;
	}
	
	
}
?>