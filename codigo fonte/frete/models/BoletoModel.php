<?php
require_once 'models/BoletoModel.php';
require_once 'models/MensalidadeModel.php';
require_once 'models/SituacaoMensalidadeModel.php';
require_once 'models/PessoaModel.php';

/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - Boleto
*
* Responsável por gerenciar e persistir os dados dos boletos
**/
class BoletoModel extends PersistModelAbstract
{
	private $idBol;
	private $diasPrazoBol;
	private $taxaBol;
	private $dataVencBol;
	private $valorBol;
	private $valorJurosBol;
	private $numeroBol;
	private $dataEmissaoBol;
	private $dataInclusaoBol;
	private $valorTotalBol;
	private $nomeClienteBol;
	private $endClienteBol;
	private $end2ClienteBol;
	private $demonstrativo1Bol;
	private $demonstrativo2Bol;
	private $demonstrativo3Bol;
	private $instrucao1Bol;
	private $instrucao2Bol;
	private $instrucao3Bol;
	private $instrucao4Bol;
	private $quantidadeBol;
	private $valorUnitBol;
	private $aceiteBol;
	private $especieBol;
	private $codigoClienteBol;
	private $carteiraBol;
	private $identificacaoBol;
	private $cnpjCedenteBol;
	private $endCedenteBol;
	private $cidadeCedenteBol;
	private $ufCedenteBol;
	private $cedenteBol;
	private $idMensa;
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe BoletoModel
	 */
   public function setIdBol($idBol)
	{
		$this->idBol = $idBol;
		return $this;
	}

	
	public function getIdBol()
	{
		return $this->idBol;
	}
	
	public function SetDiasPrazoBol($diasPrazoBol)
	{
		$this->diasPrazoBol = $diasPrazoBol;
		return $this;
	}
	
	public function getDiasPrazoBol()
	{
		return $this->diasPrazoBol;
	}

	public function SetTaxaBol($taxaBol)
	{
		$this->taxaBol = $taxaBol;
		return $this;
	}
	
	public function getTaxaBol()
	{
		return $this->taxaBol;
	}
	
	public function SetDataVencBol($dataVencBol)
	{
		$this->dataVencBol = $dataVencBol;
		return $this;
	}
	
	public function GetDataVencBol()
	{
		return $this->dataVencBol;
	}
	
	public function SetValorBol($valorBol)
	{
		$this->valorBol = $valorBol;
		return $this;
	}
	
	public function GetValorBol()
	{
		return $this->valorBol;
	}
	
	public function SetValorJurosBol($valorJurosBol)
	{
		$this->valorJurosBol = $valorJurosBol;
		return $this;
	}
	
	public function GetValorJurosBol()
	{
		return $this->valorJurosBol;
	}
	
	public function SetNumeroBol($numeroBol)
	{
		$this->numeroBol = $numeroBol;
		return $this;
	}
	
	public function GetNumeroBol()
	{
		return $this->numeroBol;
	}
	
	public function SetDataEmissaoBol($dataEmissaoBol)
	{
		$this->dataEmissaoBol = $dataEmissaoBol;
		return $this;
	}
	
	public function GetDataEmissaoBol()
	{
		return $this->dataEmissaoBol;
	}	
	
	public function SetDataInclusaoBol($dataInclusaoBol)
	{
		$this->dataInclusaoBol = $dataInclusaoBol;
		return $this;
	}
	
	public function GetDataInclusaoBol()
	{
		return $this->dataInclusaoBol;
	}
	
	public function SetValorTotalBol($valorTotalBol)
	{
		$this->valorTotalBol = $valorTotalBol;
		return $this;
	}
	
	public function GetValorTotalBol()
	{
		return $this->valorTotalBol;
	}
	
	public function SetNomeClienteBol($nomeClienteBol)
	{
		$this->nomeClienteBol = $nomeClienteBol;
		return $this;
	}
	
	public function GetNomeClienteBol()
	{
		return $this->nomeClienteBol;
	}
	
	public function SetEndClienteBol($endClienteBol)
	{
		$this->endClienteBol = $endClienteBol;
		return $this;
	}
	
	public function GetEndClienteBol()
	{
		return $this->endClienteBol;
	}
	
	public function SetEnd2ClienteBol($end2ClienteBol)
	{
		$this->end2ClienteBol = $end2ClienteBol;
		return $this;
	}
	
	public function GetEnd2ClienteBol()
	{
		return $this->end2ClienteBol;
	}
	
	public function SetDemonstrativo1Bol($demonstrativo1Bol)
	{
		$this->demonstrativo1Bol = $demonstrativo1Bol;
		return $this;
	}
	
	public function GetDemonstrativo1Bol()
	{
		return $this->demonstrativo1Bol;
	}
	
	public function SetDemonstrativo2Bol($demonstrativo2Bol)
	{
		$this->demonstrativo2Bol = $demonstrativo2Bol;
		return $this;
	}
	
	public function GetDemonstrativo2Bol()
	{
		return $this->demonstrativo2Bol;
	}
	
	public function SetDemonstrativo3Bol($demonstrativo3Bol)
	{
		$this->demonstrativo3Bol = $demonstrativo3Bol;
		return $this;
	}
	
	public function GetDemonstrativo3Bol()
	{
		return $this->demonstrativo3Bol;
	}
	
	public function SetInstrucao1Bol($instrucao1Bol)
	{
		$this->instrucao1Bol = $instrucao1Bol;
		return $this;
	}
	
	public function GetInstrucao1Bol()
	{
		return $this->instrucao1Bol;
	}
	
	public function SetInstrucao2Bol($instrucao2Bol)
	{
		$this->instrucao2Bol = $instrucao2Bol;
		return $this;
	}
	
	public function GetInstrucao2Bol()
	{
		return $this->instrucao2Bol;
	}
	
	public function SetInstrucao3Bol($instrucao3Bol)
	{
		$this->instrucao3Bol = $instrucao3Bol;
		return $this;
	}
	
	public function GetInstrucao3Bol()
	{
		return $this->instrucao3Bol;
	}
	
	public function SetInstrucao4Bol($instrucao4Bol)
	{
		$this->instrucao4Bol = $instrucao4Bol;
		return $this;
	}
	
	public function GetInstrucao4Bol()
	{
		return $this->instrucao4Bol;
	}
	
	public function SetQuantidadeBol($quantidadeBol)
	{
		$this->quantidadeBol = $quantidadeBol;
		return $this;
	}
	
	public function GetQuantidadeBol()
	{
		return $this->quantidadeBol;
	}
	
	public function SetValorUnitBol($valorUnitBol)
	{
		$this->valorUnitBol = $valorUnitBol;
		return $this;
	}
	
	public function GetValorUnitBol()
	{
		return $this->valorUnitBol;
	}
	
	public function SetAceiteBol($aceiteBol)
	{
		$this->aceiteBol = $aceiteBol;
		return $this;
	}
	
	public function GetAceiteBol()
	{
		return $this->aceiteBol;
	}
	
	public function SetEspecieBol($especieBol)
	{
		$this->especieBol = $especieBol;
		return $this;
	}
	
	public function GetEspecieBol()
	{
		return $this->especieBol;
	}
	
	public function SetCodigoClienteBol($codigoClienteBol)
	{
		$this->codigoClienteBol = $codigoClienteBol;
		return $this;
	}
	
	public function GetCodigoClienteBol()
	{
		return $this->codigoClienteBol;
	}
	
	public function SetCarteiraBol($carteiraBol)
	{
		$this->carteiraBol = $carteiraBol;
		return $this;
	}
	
	public function GetCarteiraBol()
	{
		return $this->carteiraBol;
	}
	
	public function SetIdentificacaoBol($identificacaoBol)
	{
		$this->identificacaoBol = $identificacaoBol;
		return $this;
	}
	
	public function GetIdentificacaoBol()
	{
		return $this->identificacaoBol;
	}
	
	public function SetCnpjCedenteBol($cnpjCedenteBol)
	{
		$this->cnpjCedenteBol = $cnpjCedenteBol;
		return $this;
	}
	
	public function GetCnpjCedenteBol()
	{
		return $this->cnpjCedenteBol;
	}
	
	public function SetEndCedenteBol($endCedenteBol)
	{
		$this->endCedenteBol = $endCedenteBol;
		return $this;
	}
	
	public function GetEndCedenteBol()
	{
		return $this->endCedenteBol;
	}
	
	public function SetCidadeCedenteBol($cidadeCedenteBol)
	{
		$this->cidadeCedenteBol = $cidadeCedenteBol;
		return $this;
	}
	
	public function GetCidadeCedenteBol()
	{
		return $this->cidadeCedenteBol;
	}
	
	public function SetUfCedenteBol($ufCedenteBol)
	{
		$this->ufCedenteBol = $ufCedenteBol;
		return $this;
	}
	
	public function GetUfCedenteBol()
	{
		return $this->ufCedenteBol;
	}
	
	public function SetCedenteBol($cedenteBol)
	{
		$this->cedenteBol = $cedenteBol;
		return $this;
	}
	
	public function GetCedenteBol()
	{
		return $this->cedenteBol;
	}
	
	public function SetIdMensa($idMensa)
	{
		$this->idMensa = $idMensa;
		return $this;
	}
	
	public function GetIdMensa()
	{
		return $this->idMensa;
	}
	/**
	* Retorna um array contendo os boletos
	* @param string $paramBoleto
	* @return Array
	*/
	public function _list( $paramBoleto= null )
	{
		if(!is_null($paramBoleto))
			$st_query = "SELECT * FROM tb_Boleto WHERE dataBol LIKE '%$paramBoleto%';";
		else
			$st_query = 'SELECT * FROM tb_Boleto;';	
		
		$v_boletos = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_boleto = new BoletoModel();
				$o_boleto->setIdBol($o_ret->idBol);
				$o_boleto->setDiasPrazoBol($o_ret->diasPrazoBol);
				$o_boleto->setTaxaBol($o_ret->taxaBol);
				$o_boleto->setDataVencBol($o_ret->dataVencBol);
				$o_boleto->setValorBol($o_ret->valorBol);
				$o_boleto->setValorJurosBol($o_ret->valorJurosBol);
				$o_boleto->setNumeroBol($o_ret->numeroBol);
				$o_boleto->setDataEmissaoBol($o_ret->dataEmissaoBol);
				$o_boleto->setDataInclusaoBol($o_ret->dataInclusaoBol);
				$o_boleto->setValorTotalBol($o_ret->valorTotalBol);
				$o_boleto->setNomeClienteBol($o_ret->nomeClienteBol);
				$o_boleto->setEndClienteBol($o_ret->endClienteBol);
				$o_boleto->setEnd2ClienteBol($o_ret->end2ClienteBol);
				$o_boleto->setDemonstrativo1Bol($o_ret->demonstrativo1Bol);
				$o_boleto->setDemonstrativo2Bol($o_ret->demonstrativo2Bol);
				$o_boleto->setDemonstrativo3Bol($o_ret->demonstrativo3Bol);
				$o_boleto->setInstrucao1Bol($o_ret->instrucao1Bol);
				$o_boleto->setInstrucao2Bol($o_ret->instrucao2Bol);
				$o_boleto->setInstrucao3Bol($o_ret->instrucao3Bol);
				$o_boleto->setInstrucao4Bol($o_ret->instrucao4Bol);
				$o_boleto->setQuantidadeBol($o_ret->quantidadeBol);
				$o_boleto->setValorUnitBol($o_ret->valorUnitBol);
				$o_boleto->setAceiteBol($o_ret->aceiteBol);
				$o_boleto->setEspecieBol($o_ret->especieBol);
				$o_boleto->setCodigoClienteBol($o_ret->codigoClienteBol);
				$o_boleto->setCarteiraBol($o_ret->carteiraBol);
				$o_boleto->setIdentificacaoBol($o_ret->identificacaoBol);
				$o_boleto->setCnpjCedenteBol($o_ret->cnpjCedenteBol);
				$o_boleto->setEndCedenteBol($o_ret->endCedenteBol);
				$o_boleto->setCidadeCedenteBol($o_ret->cidadeCedenteBol);
				$o_boleto->setUfCedenteBol($o_ret->ufCedenteBol);
				$o_boleto->setCedenteBol($o_ret->cedenteBol);
				$o_boleto->setIdMensa($o_ret->idMensa);

				
				array_push($v_boletos, $o_boleto);
	
			}
		}
		catch(PDOException $e)
		{}				
		return $v_boletos;
	}
	
	public function _listTransportador( $idPes = null )
	{
		if(!is_null($idPes))
			$st_query = "
SELECT * FROM tb_Boleto B
						INNER JOIN tb_mensalidade M ON B.idMensa = M.idMensa
						INNER JOIN tb_pessoa P ON M.tb_pessoa_idPes = P.idPes
						WHERE idPes = $idPes AND  M.tb_situacaoMensalidade_idSit = 6
						ORDER BY dataVencBol DESC;";
		else
			$st_query = 'SELECT * FROM tb_Boleto;';	
		
		//echo $st_query;
		//exit;
		$v_boletos = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_boleto = new BoletoModel();
				$o_boleto->setIdBol($o_ret->idBol);
				$o_boleto->setDiasPrazoBol($o_ret->diasPrazoBol);
				$o_boleto->setTaxaBol($o_ret->taxaBol);
				$o_boleto->setDataVencBol($o_ret->dataVencBol);
				$o_boleto->setValorBol($o_ret->valorBol);
				$o_boleto->setValorJurosBol($o_ret->valorJurosBol);
				$o_boleto->setNumeroBol($o_ret->numeroBol);
				$o_boleto->setDataEmissaoBol($o_ret->dataEmissaoBol);
				$o_boleto->setDataInclusaoBol($o_ret->dataInclusaoBol);
				$o_boleto->setValorTotalBol($o_ret->valorTotalBol);
				$o_boleto->setNomeClienteBol($o_ret->nomeClienteBol);
				$o_boleto->setEndClienteBol($o_ret->endClienteBol);
				$o_boleto->setEnd2ClienteBol($o_ret->end2ClienteBol);
				$o_boleto->setDemonstrativo1Bol($o_ret->demonstrativo1Bol);
				$o_boleto->setDemonstrativo2Bol($o_ret->demonstrativo2Bol);
				$o_boleto->setDemonstrativo3Bol($o_ret->demonstrativo3Bol);
				$o_boleto->setInstrucao1Bol($o_ret->instrucao1Bol);
				$o_boleto->setInstrucao2Bol($o_ret->instrucao2Bol);
				$o_boleto->setInstrucao3Bol($o_ret->instrucao3Bol);
				$o_boleto->setInstrucao4Bol($o_ret->instrucao4Bol);
				$o_boleto->setQuantidadeBol($o_ret->quantidadeBol);
				$o_boleto->setValorUnitBol($o_ret->valorUnitBol);
				$o_boleto->setAceiteBol($o_ret->aceiteBol);
				$o_boleto->setEspecieBol($o_ret->especieBol);
				$o_boleto->setCodigoClienteBol($o_ret->codigoClienteBol);
				$o_boleto->setCarteiraBol($o_ret->carteiraBol);
				$o_boleto->setIdentificacaoBol($o_ret->identificacaoBol);
				$o_boleto->setCnpjCedenteBol($o_ret->cnpjCedenteBol);
				$o_boleto->setEndCedenteBol($o_ret->endCedenteBol);
				$o_boleto->setCidadeCedenteBol($o_ret->cidadeCedenteBol);
				$o_boleto->setUfCedenteBol($o_ret->ufCedenteBol);
				$o_boleto->setCedenteBol($o_ret->cedenteBol);
				$o_boleto->setIdMensa($o_ret->idMensa);

				
				array_push($v_boletos, $o_boleto);
	
			}
		}
		catch(PDOException $e)
		{}				
		return $v_boletos;
	}
	/**
	* Retorna os dados de um boleto referente a um determinado Id
	* @param integer $idBoleto
	* @return BoletoModel
	*/
	public function loadById( $idBol )
	{
		$v_boletos = array();
		$st_query = "SELECT * FROM tb_boleto WHERE idbol = $idBol;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdBol($o_ret->idBol);
		$this->setDiasPrazoBol($o_ret->diasPrazoBol);
		$this->setTaxaBol($o_ret->taxaBol);
		$this->setDataVencBol($o_ret->dataVencBol);
		$this->setValorBol($o_ret->valorBol);
		$this->setValorJurosBol($o_ret->valorJurosBol);
		$this->setNumeroBol($o_ret->numeroBol);
		$this->setDataEmissaoBol($o_ret->dataEmissaoBol);
		$this->setDataInclusaoBol($o_ret->dataInclusaoBol);
		$this->setValorTotalBol($o_ret->valorTotalBol);
		$this->setNomeClienteBol($o_ret->nomeClienteBol);
		$this->setEndClienteBol($o_ret->endClienteBol);
		$this->setEnd2ClienteBol($o_ret->end2ClienteBol);
		$this->setDemonstrativo1Bol($o_ret->demonstrativo1Bol);
		$this->setDemonstrativo2Bol($o_ret->demonstrativo2Bol);
		$this->setDemonstrativo3Bol($o_ret->demonstrativo3Bol);
		$this->setInstrucao1Bol($o_ret->instrucao1Bol);
		$this->setInstrucao2Bol($o_ret->instrucao2Bol);
		$this->setInstrucao3Bol($o_ret->instrucao3Bol);
		$this->setInstrucao4Bol($o_ret->instrucao4Bol);
		$this->setQuantidadeBol($o_ret->quantidadeBol);
		$this->setValorUnitBol($o_ret->valorUnitBol);
		$this->setAceiteBol($o_ret->aceiteBol);
		$this->setEspecieBol($o_ret->especieBol);
		$this->setCodigoClienteBol($o_ret->codigoClienteBol);
		$this->setCarteiraBol($o_ret->carteiraBol);
		$this->setIdentificacaoBol($o_ret->identificacaoBol);
		$this->setCnpjCedenteBol($o_ret->cnpjCedenteBol);
		$this->setEndCedenteBol($o_ret->endCedenteBol);
		$this->setCidadeCedenteBol($o_ret->cidadeCedenteBol);
		$this->setUfCedenteBol($o_ret->ufCedenteBol);
		$this->setCedenteBol($o_ret->cedenteBol);
		$this->setIdMensa($o_ret->idMensa);
	
		return $this;
	}
	
	public function loadByIdMensa( $idMensa )
	{
		$v_boletos = array();
		$st_query = "SELECT * FROM tb_boleto WHERE idMensa = $idMensa;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		if(!empty($o_ret))
		{
			$this->loadById($o_ret->idBol);
		}
		else
		{
			$o_mensalidade = new MensalidadeModel();
			$o_mensalidade->loadById($idMensa);
			
			$o_pessoa = new PessoaModel();
			$o_pessoa->loadById($o_mensalidade->getTb_pessoa_idPes());
			
			$this->setDiasPrazoBol(5);
			$this->setTaxaBol(0);
			$this->setValorBol($o_mensalidade->getValorMensa());
			$this->setValorJurosBol(0);
			$this->setNumeroBol(date('Y', strtotime($o_mensalidade->getDataVencimentoMensa())) . str_pad($idMensa, 5, "0", STR_PAD_LEFT));
			$this->setValorTotalBol($o_mensalidade->getValorMensa());
			
			$this->setNomeClienteBol($o_pessoa->getPrimeiroNomePes() . ' ' . $o_pessoa->getSobreNomePes());
			$this->setEndClienteBol('Endereço do transportador');
			$this->setEnd2ClienteBol('Endereço2 do transportador');
			$this->setDemonstrativo1Bol('Mensalidade do site Savicki');
			$this->setDemonstrativo2Bol('');
			$this->setDemonstrativo3Bol('');
			$this->setInstrucao1Bol('- Sr. Caixa, não receber após o vencimento');
			$this->setInstrucao2Bol('');
			$this->setInstrucao3Bol('');
			$this->setInstrucao4Bol('');
			$this->setQuantidadeBol('1');
			$this->setValorUnitBol($o_mensalidade->getValorMensa());
			$this->setAceiteBol('');
			$this->setEspecieBol('R$');
			$this->setCodigoClienteBol('1122334'); //agência e conta
			$this->setCarteiraBol('CNR');
			$this->setIdentificacaoBol('Mensalidade do site Savicki');
			$this->setCnpjCedenteBol('01.001.001/0001-01');
			$this->setEndCedenteBol('Endereço do cedente');
			$this->setCidadeCedenteBol('Cidade do cedente');
			$this->setUfCedenteBol('PR');
			$this->setCedenteBol('Savicki'); //razão social
			$this->setIdMensa($idMensa);
			
			if($this->save() > 0)
				$this->save();
				
			$this->loadByIdMensa($idMensa);
		}
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe na tabela de boleto. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idBol))
			$st_query = "INSERT INTO tb_boleto
						(	
							diasPrazoBol,
							taxaBol,
							dataVencBol,
							valorBol,
							valorJurosBol,
							numeroBol,
							dataEmissaoBol,
							dataInclusaoBol,
							valorTotalBol,
							nomeClienteBol,
							endClienteBol,
							end2ClienteBol,
							demonstrativo1Bol,
							demonstrativo2Bol,
							demonstrativo3Bol,
							instrucao1Bol,
							instrucao2Bol,
							instrucao3Bol,
							instrucao4Bol,
							quantidadeBol,
							valorUnitBol,
							aceiteBol,
							especieBol,
							codigoClienteBol,
							carteiraBol,
							identificacaoBol,
							cnpjCedenteBol,
							endCedenteBol,
							cidadeCedenteBol,
							ufCedenteBol,
							cedenteBol,
							idMensa
						)
						VALUES
						(	
							'$this->diasPrazoBol',
							'$this->taxaBol',
							DATEADD(day, $this->diasPrazoBol, NOW()),
							'$this->valorBol',
							'$this->valorJurosBol',
							'$this->numeroBol',
							NOW(),
							NOW(),
							'$this->valorTotalBol',
							'$this->nomeClienteBol',
							'$this->endClienteBol',
							'$this->end2ClienteBol',
							'$this->demonstrativo1Bol',
							'$this->demonstrativo2Bol',
							'$this->demonstrativo3Bol',
							'$this->instrucao1Bol',
							'$this->instrucao2Bol',
							'$this->instrucao3Bol',
							'$this->instrucao4Bol',
							'$this->quantidadeBol',
							'$this->valorUnitBol',
							'$this->aceiteBol',
							'$this->especieBol',
							'$this->codigoClienteBol',
							'$this->carteiraBol',
							'$this->identificacaoBol',
							'$this->cnpjCedenteBol',
							'$this->endCedenteBol',
							'$this->cidadeCedenteBol',
							'$this->ufCedenteBol',
							'$this->cedenteBol',
							'$this->idMensa'
						);";
		else
			$st_query = "UPDATE
							tb_boleto
						SET
							diasPrazoBol = '$this->diasPrazoBol',
							taxaBol = '$this->taxaBol',
							dataVencBol = '$this->dataVencBol',
							valorBol = '$this->valorBol',
							valorJurosBol = '$this->valorJurosBol',
							numeroBol = '$this->numeroBol',
							dataEmissaoBol = '$this->dataEmissaoBol',
							dataInclusaoBol = '$this->dataInclusaoBol',
							valorTotalBol = '$this->valorTotalBol',
							nomeClienteBol = '$this->nomeClienteBol',
							endClienteBol = '$this->endClienteBol',
							end2ClienteBol = '$this->end2ClienteBol',
							demonstrativo1Bol = '$this->demonstrativo1Bol',
							demonstrativo2Bol = '$this->demonstrativo2Bol',
							demonstrativo3Bol = '$this->demonstrativo3Bol',
							instrucao1Bol = '$this->instrucao1Bol',
							instrucao2Bol = '$this->instrucao2Bol',
							instrucao3Bol = '$this->instrucao3Bol',
							instrucao4Bol = '$this->instrucao4Bol',
							quantidadeBol = '$this->quantidadeBol',
							valorUnitBol = '$this->valorUnitBol',
							aceiteBol = '$this->aceiteBol',
							especieBol = '$this->especieBol',
							codigoClienteBol = '$this->codigoClienteBol',
							carteiraBol = '$this->carteiraBol',
							identificacaoBol = '$this->identificacaoBol',
							cnpjCedenteBol = '$this->cnpjCedenteBol',
							endCedenteBol = '$this->endCedenteBol',
							cidadeCedenteBol = '$this->cidadeCedenteBol',
							ufCedenteBol = '$this->ufCedenteBol',
							cedenteBol = '$this->cedenteBol',
							idMensa = '$this->idMensa'
						WHERE
							idBol = $this->idBol";
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
	* Deleta os dados persistidos na tabela de boleto usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idBol))
		{
			$st_query = "DELETE FROM
							tb_boleto
						WHERE idBol = $this->idBol";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	

}
?>