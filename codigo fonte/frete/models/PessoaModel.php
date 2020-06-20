<?php
require_once 'models/PessoaModel.php';
require_once 'models/EstadoModel.php';
require_once 'models/CidadeModel.php';
require_once 'models/StatusModel.php';
require_once 'models/EnderecoModel.php';
require_once 'models/AvaliacaoModel.php';
require_once 'models/PerguntaPesquisaModel.php';
require_once 'models/CidadeModel.php';
include ("lib/email.php");

/**

 *
 * Camada - Modelo ou Model.
 * Diretório Pai - models
 * Arquivo - TelefoneModel
 *
 * Responsável por gerenciar e persistir os dados dos
 * Clientes
 **/
class PessoaModel extends PersistModelAbstract {
	private $idPes;
	private $primeiroNomePes;
	private $sobreNomePes;
	private $emailPes;
	private $senhaPes;
	private $cpfCnpjPes;
	private $fotoPes;
	private $dataCadastroPes;
	private $telefoneFixoPes;
	private $telefoneCelularPes;
	private $tb_endereco_idEnd;
	private $tb_perfil_idPer;
	private $tb_Status_idSta;
	private $idEnd;
	private $cepEnd;
	private $ruaEnd;
	private $bairroEnd;
	private $complementoEnd;
	private $tb_Cidade_idCid;
	private $tb_Estado_idEst;
	private $nomeEst;
	private $nomeCid;
	private $media;
	private $qtdeLances;
	private $qtdeTransportes;
	private $nomeSta;
	private $nomePer;
	private $codigoSta;
	private $codigoPer;

	function __construct() {
		parent::__construct();

	}

	/**
	 * Setters e Getters da
	 * classe TelefoneModel
	 */
	public function setIdPes($idPes) {
		$this -> idPes = $idPes;
		return $this;
	}

	public function getIdPes() {
		return $this -> idPes;
	}

	public function setPrimeiroNomePes($primeiroNomePes) {
		$this -> primeiroNomePes = $primeiroNomePes;
		return $this;
	}

	public function getPrimeiroNomePes() {
		return $this -> primeiroNomePes;
	}

	public function setSobreNomePes($sobreNomePes) {
		$this -> sobreNomePes = $sobreNomePes;
		return $this;
	}

	public function getSobreNomePes() {
		return $this -> sobreNomePes;
	}

	public function setEmailPes($emailPes) {
		$this -> emailPes = $emailPes;
		return $this;
	}

	public function getEmailPes() {
		return $this -> emailPes;
	}

	public function setSenhaPes($senhaPes) {
		$this -> senhaPes = $senhaPes;
		return $this;
	}

	public function getSenhaPes() {
		return $this -> senhaPes;
	}

	public function setCpfCnpjPes($cpfCnpjPes) {
		$this -> cpfCnpjPes = $cpfCnpjPes;
		return $this;
	}

	public function getCpfCnpjPes() {
		return $this -> cpfCnpjPes;
	}

	public function setFotoPes($fotoPes) {
		$this -> fotoPes = $fotoPes;
		return $this;
	}

	public function getFotoPes() {
		return $this -> fotoPes;
	}

	public function setDataCadastroPes($dataCadastroPes) {
		$this -> dataCadastroPes = $dataCadastroPes;
		return $this;
	}

	public function getDataCadastroPes() {
		return $this -> dataCadastroPes;
	}

	public function setTelefoneFixoPes($telefoneFixoPes) {
		$this -> telefoneFixoPes = $telefoneFixoPes;
		return $this;
	}

	public function getTelefoneFixoPes() {
		return $this -> telefoneFixoPes;
	}

	public function setTelefoneCelularPes($telefoneCelularPes) {
		$this -> telefoneCelularPes = $telefoneCelularPes;
		return $this;
	}

	public function getTelefoneCelularPes() {
		return $this -> telefoneCelularPes;
	}

	public function setNomeSta($nomeSta) {
		$this -> nomeSta = $nomeSta;
		return $this;
	}

	public function getNomeSta() {
		return $this -> nomeSta;
	}

	public function setTb_Status_idSta($tb_Status_idSta) {
		$this -> tb_Status_idSta = $tb_Status_idSta;
		return $this;
	}

	public function getTb_Status_idSta() {
		return $this -> tb_Status_idSta;
	}

	public function setTb_endereco_idEnd($tb_endereco_idEnd) {
		$this -> tb_endereco_idEnd = $tb_endereco_idEnd;
		return $this;
	}

	public function getTb_endereco_idEnd() {
		return $this -> tb_endereco_idEnd;
	}

	public function setTb_perfil_idPer($tb_perfil_idPer) {
		$this -> tb_perfil_idPer = $tb_perfil_idPer;
		return $this;
	}

	public function getTb_perfil_idPer() {
		return $this -> tb_perfil_idPer;
	}

	public function setNomePer($nomePer) {
		$this -> nomePer = $nomePer;
		return $this;
	}

	public function getNomePer() {
		return $this -> nomePer;
	}

	public function setIdEnd($idEnd) {
		$this -> idEnd = $idEnd;
		return $this;
	}

	public function getIdEnd() {
		return $this -> idEnd;
	}

	public function setCepEnd($cepEnd) {
		$this -> cepEnd = $cepEnd;
		return $this;
	}

	public function getCepEnd() {
		return $this -> cepEnd;
	}

	public function setRuaEnd($ruaEnd) {
		$this -> ruaEnd = $ruaEnd;
		return $this;
	}

	public function getRuaEnd() {
		return $this -> ruaEnd;
	}

	public function setBairroEnd($bairroEnd) {
		$this -> bairroEnd = $bairroEnd;
		return $this;
	}

	public function getBairroEnd() {
		return $this -> bairroEnd;
	}

	public function setComplementoEnd($complementoEnd) {
		$this -> complementoEnd = $complementoEnd;
		return $this;
	}

	public function getComplementoEnd() {
		return $this -> complementoEnd;
	}

	public function setTb_Cidade_idCid($tb_Cidade_idCid) {
		$this -> tb_Cidade_idCid = $tb_Cidade_idCid;
		return $this;
	}

	public function getTb_Cidade_idCid() {
		return $this -> tb_Cidade_idCid;
	}

	public function setTb_Estado_idEst($tb_Estado_idEst) {
		$this -> tb_Estado_idEst = $tb_Estado_idEst;
		return $this;
	}

	public function getTb_Estado_idEst() {
		return $this -> tb_Estado_idEst;
	}

	public function setNomeEst($nomeEst) {
		$this -> nomeEst = $nomeEst;
		return $this;
	}

	public function getNomeEst() {
		return $this -> nomeEst;
	}

	public function setNomeCid($nomeCid) {
		$this -> nomeCid = $nomeCid;
		return $this;
	}

	public function getNomeCid() {
		return $this -> nomeCid;
	}

	public function setMedia($media) {
		$this -> media = $media;
		return $this;
	}

	public function getMedia() {
		return $this -> media;
	}

	public function setQtdeLances($qtdeLances) {
		$this -> qtdeLances = $qtdeLances;
		return $this;
	}

	public function getQtdeLances() {
		return $this -> qtdeLances;
	}

	public function setQtdeTransportes($qtdeTransportes) {
		$this -> qtdeTransportes = $qtdeTransportes;
		return $this;
	}

	public function getQtdeTransportes() {
		return $this -> qtdeTransportes;
	}

	public function setCodigoSta($codigoSta) {
		$this -> codigoSta = $codigoSta;
		return $this;
	}

	public function getCodigoSta() {
		return $this -> codigoSta;
	}

	public function setCodigoPer($codigoPer) {
		$this -> codigoPer = $codigoPer;
		return $this;
	}

	public function getCodigoPer() {
		return $this -> codigoPer;
	}

	/**
	 * Retorna um array contendo as pessoas
	 * @param string $primeiroNomePes
	 * @return Array
	 */
	public function _list($primeiroNomePes = null, $codigoPer = null) {
		$st_query = "SELECT * FROM tb_pessoa AS P
					INNER join tb_perfil AS PE on PE.idPer = P.tb_perfil_idper 
					INNER JOIN tb_status AS ST ON ST.idSta = P.tb_Status_idSta 
					LEFT JOIN tb_endereco AS E ON E.IdEnd = P.tb_endereco_idEnd 
					LEFT JOIN tb_cidade AS C ON E.tb_Cidade_idCid = C.idCid 
					LEFT JOIN tb_estado AS ES ON E.tb_Estado_idEst = ES.idEst";
		$st_query .= " where 0 = 0 ";
		$st_where = null;
		if (!is_null($primeiroNomePes))
			$st_where = " AND primeiroNomePes LIKE '%$primeiroNomePes%' ";
		if (!is_null($codigoPer))
			$st_where .= " AND codigoPer = '$codigoPer' ";
		$st_query .= $st_where . ";";
		//echo $st_query;
		//exit;
		$v_pessoas = array();
		try {
			$o_data = $this -> o_db -> query($st_query);
			while ($o_ret = $o_data -> fetchObject()) {
				$o_pessoa = new PessoaModel();
				$o_pessoa -> setIdPes($o_ret -> idPes);
				$o_pessoa -> setPrimeiroNomePes($o_ret -> primeiroNomePes);
				$o_pessoa -> setSobreNomePes($o_ret -> sobreNomePes);
				$o_pessoa -> setEmailPes($o_ret -> emailPes);
				$o_pessoa -> setSenhaPes($o_ret -> senhaPes);
				$o_pessoa -> setCpfCnpjPes($o_ret -> cpfCnpjPes);
				$o_pessoa -> setFotoPes($o_ret -> fotoPes);
				$o_pessoa -> setDataCadastroPes($o_ret -> dataCadastroPes);
				$o_pessoa -> setTelefoneFixoPes($o_ret -> telefoneFixoPes);
				$o_pessoa -> setTelefoneCelularPes($o_ret -> telefoneCelularPes);
				$o_pessoa -> setTb_Status_idSta($o_ret -> tb_Status_idSta);
				$o_pessoa -> setTb_perfil_idPer($o_ret -> tb_perfil_idPer);
				$o_pessoa -> setNomePer($o_ret -> nomePer);
				$o_pessoa -> setNomeSta($o_ret -> nomeSta);
				$o_pessoa -> setTb_endereco_idEnd($o_ret -> tb_endereco_idEnd);
				$o_pessoa -> setIdEnd($o_ret -> idEnd);
				$o_pessoa -> setCepEnd($o_ret -> cepEnd);
				$o_pessoa -> setRuaEnd($o_ret -> ruaEnd);
				$o_pessoa -> setBairroEnd($o_ret -> bairroEnd);
				$o_pessoa -> setComplementoEnd($o_ret -> complementoEnd);
				$o_pessoa -> setNomeCid($o_ret -> nomeCid);
				$o_pessoa -> setNomeEst($o_ret -> nomeEst);
				$o_pessoa -> setCodigoSta($o_ret -> codigoSta);
				$o_pessoa -> setCodigoPer($o_ret -> codigoPer);
				array_push($v_pessoas, $o_pessoa);
			}
		} catch(PDOException $e) {
		}
		return $v_pessoas;
	}

	public function _listEmailTransportadores($codigoPer = null, $estadoOrigem = null) {
		$st_query = "SELECT * FROM tb_pessoa AS P
					INNER join tb_perfil AS PE on PE.idPer = P.tb_perfil_idper 
					INNER JOIN tb_Status AS ST ON ST.idSta = P.tb_Status_idSta 
					LEFT JOIN tb_endereco AS E ON E.IdEnd = P.tb_endereco_idEnd 
					LEFT JOIN tb_cidade AS C ON E.tb_Cidade_idCid = C.idCid 
					LEFT JOIN tb_estado AS ES ON E.tb_Estado_idEst = ES.idEst";
		$st_query .= " where 0 = 0 ";
		$st_where = null;
		if (!is_null($codigoPer))
			$st_where .= " AND codigoPer = '$codigoPer' AND ES.ufEst = '$estadoOrigem' ";
		$st_query .= $st_where . ";";
		//echo $st_query;
		//exit;
		$v_pessoas = array();
		try {
			$o_data = $this -> o_db -> query($st_query);
			while ($o_ret = $o_data -> fetchObject()) {
				$o_pessoa = new PessoaModel();
				$o_pessoa -> setIdPes($o_ret -> idPes);
				$o_pessoa -> setPrimeiroNomePes($o_ret -> primeiroNomePes);
				$o_pessoa -> setSobreNomePes($o_ret -> sobreNomePes);
				$o_pessoa -> setEmailPes($o_ret -> emailPes);
				$o_pessoa -> setSenhaPes($o_ret -> senhaPes);
				$o_pessoa -> setCpfCnpjPes($o_ret -> cpfCnpjPes);
				$o_pessoa -> setFotoPes($o_ret -> fotoPes);
				$o_pessoa -> setDataCadastroPes($o_ret -> dataCadastroPes);
				$o_pessoa -> setTelefoneFixoPes($o_ret -> telefoneFixoPes);
				$o_pessoa -> setTelefoneCelularPes($o_ret -> telefoneCelularPes);
				$o_pessoa -> setTb_Status_idSta($o_ret -> tb_Status_idSta);
				$o_pessoa -> setTb_perfil_idPer($o_ret -> tb_perfil_idPer);
				$o_pessoa -> setNomePer($o_ret -> nomePer);
				$o_pessoa -> setNomeSta($o_ret -> nomeSta);
				$o_pessoa -> setTb_endereco_idEnd($o_ret -> tb_endereco_idEnd);
				$o_pessoa -> setIdEnd($o_ret -> idEnd);
				$o_pessoa -> setCepEnd($o_ret -> cepEnd);
				$o_pessoa -> setRuaEnd($o_ret -> ruaEnd);
				$o_pessoa -> setBairroEnd($o_ret -> bairroEnd);
				$o_pessoa -> setComplementoEnd($o_ret -> complementoEnd);
				$o_pessoa -> setTb_Cidade_idCid($o_ret -> tb_Cidade_idCid);
				$o_pessoa -> setTb_Estado_idEst($o_ret -> tb_Estado_idEst);
				$o_pessoa -> setNomeCid($o_ret -> nomeCid);
				$o_pessoa -> setNomeEst($o_ret -> nomeEst);
				$o_pessoa -> setCodigoSta($o_ret -> codigoSta);
				$o_pessoa -> setCodigoPer($o_ret -> codigoPer);
				array_push($v_pessoas, $o_pessoa);
			}
		} catch(PDOException $e) {
		}
		return $v_pessoas;
	}

	/**
	 * Retorna os dados de um pessoa referente
	 * a um determinado Id
	 * @param integer $idPes
	 * @return PessoaModel
	 */
	public function loadById($idPes = null, $emailPes = null) {
		$v_pessoas = array();
		$st_query = "SELECT  * FROM tb_pessoa ";
		$st_query .= "INNER join tb_perfil on idPer = tb_perfil_idper ";
		$st_query .= "LEFT JOIN tb_endereco ON IdEnd = tb_endereco_idEnd ";
		$st_query .= "LEFT JOIN tb_cidade ON IdCid = tb_endereco.tb_Cidade_idCid ";
		$st_query .= "LEFT JOIN tb_estado ON IdEst = tb_endereco.tb_Estado_idEst ";
		$st_query .= "INNER JOIN tb_status ON idSta = tb_status_idSta ";
		if ($idPes !== NULL)
			$st_query .= " AND idPes = $idPes";
		elseif ($emailPes !== NULL)
			$st_query .= " and emailPes = '$emailPes'";
		$st_query .= ";";
		//echo $st_query;
		//exit;
		$o_data = $this -> o_db -> query($st_query);
		$o_ret = $o_data -> fetchObject();
		if (!isset($o_ret -> idPes)) {

			return FALSE;
		} else {

			$this -> setIdPes($o_ret -> idPes);
			$this -> setPrimeiroNomePes($o_ret -> primeiroNomePes);
			$this -> setSobreNomePes($o_ret -> sobreNomePes);
			$this -> setEmailPes($o_ret -> emailPes);
			$this -> setSenhaPes($o_ret -> senhaPes);
			$this -> setCpfCnpjPes($o_ret -> cpfCnpjPes);
			$this -> setFotoPes($o_ret -> fotoPes);
			$this -> setDataCadastroPes($o_ret -> dataCadastroPes);
			$this -> setTelefoneFixoPes($o_ret -> telefoneFixoPes);
			$this -> setTelefoneCelularPes($o_ret -> telefoneCelularPes);
			$this -> setTb_Status_idSta($o_ret -> tb_Status_idSta);
			$this -> setTb_perfil_idPer($o_ret -> tb_perfil_idPer);
			$this -> setNomePer($o_ret -> nomePer);
			$this -> setNomeSta($o_ret -> nomeSta);

			$this -> setCodigoSta($o_ret -> codigoSta);
			$this -> setCodigoPer($o_ret -> codigoPer);
			return $this;
		}

	}

	public function _listTransportadorLance($idTransp = null, $idPes = null) {
		if (!is_null($idTransp))
			$st_query = "SELECT DISTINCT TQ.*,
			tb_pessoa.*
			FROM tb_lance TQ 
			INNER JOIN tb_pessoa ON tb_pessoa.idPes = TQ.tb_pessoa_idPes 
			INNER JOIN tb_transporte ON TQ.tb_transporte_idTransp = tb_transporte.idTransp 
			LEFT JOIN tb_avaliacao ON tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp 
			WHERE 0=0
			AND TQ.dataLan = (SELECT MAX(dataLan) FROM tb_lance WHERE tb_pessoa_idPes = TQ.tb_pessoa_idPes AND tb_lance.tb_transporte_idTransp = '$idTransp' GROUP BY tb_pessoa_idPes) 
			AND TQ.vencedorLan = 'N'
			";
		if (!is_null($idTransp))
			$st_query .= "AND TQ.Tb_transporte_idTransp ='$idTransp' ";
		if (!is_null($idPes))
			$st_query .= "AND tb_pessoa_idPes = '$idPes'";
		$st_query .= " GROUP BY 
			TQ.dataLan,
			TQ.idLan,
			TQ.tb_pessoa_idPes,
			TQ.tb_transporte_idTransp,
			TQ.valorLan, TQ.vencedorLan,
			tb_pessoa.cpfCnpjPes,
			tb_pessoa.dataCadastroPes,
			tb_pessoa.emailPes,
			tb_pessoa.fotoPes,
			tb_pessoa.idPes,
			tb_pessoa.primeiroNomePes,
			tb_pessoa.senhaPes,
			tb_pessoa.sobreNomePes,
			tb_pessoa.tb_endereco_idEnd,
			tb_pessoa.tb_perfil_idPer,
			tb_pessoa.tb_Status_idSta,
			tb_pessoa.telefoneCelularPes,
			tb_pessoa.telefoneFixoPes
			order by TQ.dataLan desc;";

		//echo $st_query;
		//exit;
		$v_pessoas = array();
		try {
			$o_data = $this -> o_db -> query($st_query);
			while ($o_ret = $o_data -> fetchObject()) {
				$o_pessoa = new PessoaModel();
				$o_pessoa -> setIdPes($o_ret -> idPes);
				$o_pessoa -> setPrimeiroNomePes($o_ret -> primeiroNomePes);
				$o_pessoa -> setSobreNomePes($o_ret -> sobreNomePes);
				$o_pessoa -> setEmailPes($o_ret -> emailPes);
				$o_pessoa -> setSenhaPes($o_ret -> senhaPes);
				$o_pessoa -> setCpfCnpjPes($o_ret -> cpfCnpjPes);
				$o_pessoa -> setFotoPes($o_ret -> fotoPes);
				$o_pessoa -> setDataCadastroPes($o_ret -> dataCadastroPes);
				$o_pessoa -> setTelefoneFixoPes($o_ret -> telefoneFixoPes);
				$o_pessoa -> setTelefoneCelularPes($o_ret -> telefoneCelularPes);
				$o_pessoa -> setTb_Status_idSta($o_ret -> tb_Status_idSta);
				$o_pessoa -> setTb_perfil_idPer($o_ret -> tb_perfil_idPer);

				array_push($v_pessoas, $o_pessoa);
			}
		} catch(PDOException $e) {
		}
		return $v_pessoas;
	}

	public function loadByIdPerfil($tb_pessoa_idPes) {
		$v_pessoas = array();
		$st_query = "SELECT ";
		$st_query .= "tb_pessoa.*,tb_endereco.*,tb_cidade.*,tb_estado.*,tb_perfil.*,tb_status.*,";
		$st_query .= "(round((AVG(tb_avaliacao.valorAva1 + ";
		$st_query .= "tb_avaliacao.valorAva2  + ";
		$st_query .= "tb_avaliacao.valorAva3  + ";
		$st_query .= "tb_avaliacao.valorAva4  + ";
		$st_query .= "tb_avaliacao.valorAva5) / 5))) as media, ";
		$st_query .= "(select count(*) from tb_lance  where tb_lance.vencedorLan = 'S' AND tb_lance.tb_pessoa_idPes = '$tb_pessoa_idPes')  AS qtdeTransportes, ";
		$st_query .= "(select count(*) from tb_lance  WHERE tb_lance.tb_pessoa_idPes = '$tb_pessoa_idPes' )  AS qtdeLances ";
		$st_query .= "FROM tb_pessoa ";
		$st_query .= "INNER JOIN tb_lance ON tb_pessoa.idPes = tb_lance.tb_pessoa_idPes ";
		$st_query .= "INNER JOIN tb_transporte ON tb_lance.tb_transporte_idTransp = tb_transporte.idTransp  ";
		$st_query .= "LEFT JOIN tb_avaliacao ON tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
		$st_query .= "INNER join tb_perfil on idPer = tb_perfil_idper ";
		$st_query .= "LEFT JOIN tb_endereco ON IdEnd = tb_endereco_idEnd ";
		$st_query .= "LEFT JOIN tb_cidade ON IdCid = tb_endereco.tb_Cidade_idCid ";
		$st_query .= "LEFT JOIN tb_estado ON IdEst = tb_endereco.tb_Estado_idEst ";
		$st_query .= "INNER JOIN tb_status ON idSta = tb_status_idSta ";
		$st_query .= "WHERE tb_lance.tb_pessoa_idPes = '$tb_pessoa_idPes' ";
		$st_query .= "GROUP BY tb_pessoa.idPes, ";
		$st_query .= "tb_pessoa.cpfCnpjPes, ";
		$st_query .= "tb_pessoa.dataCadastroPes, ";
		$st_query .= "tb_pessoa.emailPes, ";
		$st_query .= "tb_pessoa.fotoPes, ";
		$st_query .= "tb_pessoa.primeiroNomePes, ";
		$st_query .= "tb_pessoa.senhaPes, ";
		$st_query .= "tb_pessoa.sobreNomePes, ";
		$st_query .= "tb_pessoa.tb_endereco_idEnd, ";
		$st_query .= "tb_pessoa.tb_perfil_idPer, ";
		$st_query .= "tb_pessoa.tb_Status_idSta, ";
		$st_query .= "tb_pessoa.telefoneCelularPes, ";
		$st_query .= "tb_pessoa.telefoneFixoPes, ";
		$st_query .= "tb_endereco.idEnd, ";
		$st_query .= "tb_endereco.bairroEnd, ";
		$st_query .= "tb_endereco.cepEnd, ";
		$st_query .= "tb_endereco.complementoEnd, ";
		$st_query .= "tb_endereco.ruaEnd, ";
		$st_query .= "tb_endereco.tb_Cidade_idCid, ";
		$st_query .= "tb_endereco.tb_Estado_idEst, ";
		$st_query .= "tb_cidade.idCid, ";
		$st_query .= "tb_cidade.nomeCid, ";
		$st_query .= "tb_cidade.tb_Estado_idEst, ";
		$st_query .= "tb_estado.idEst, ";
		$st_query .= "tb_estado.nomeEst, ";
		$st_query .= "tb_estado.tb_Pais_idPais, ";
		$st_query .= "tb_estado.ufEst, ";
		$st_query .= "tb_perfil.idPer, ";
		$st_query .= "tb_perfil.codigoPer, ";
		$st_query .= "tb_perfil.nomePer, ";
		$st_query .= "tb_status.idSta, ";
		$st_query .= "tb_status.nomeSta, ";
		$st_query .= "tb_status.codigoSta ";
		$st_query .= ";";
		//echo $st_query;
		//exit;
		$o_data = $this -> o_db -> query($st_query);
		$o_ret = $o_data -> fetchObject();

		if (!isset($o_ret -> idPes)) {

			return FALSE;

		} else {

			$this -> setIdPes($o_ret -> idPes);
			$this -> setPrimeiroNomePes($o_ret -> primeiroNomePes);
			$this -> setSobreNomePes($o_ret -> sobreNomePes);
			$this -> setEmailPes($o_ret -> emailPes);
			$this -> setSenhaPes($o_ret -> senhaPes);
			$this -> setCpfCnpjPes($o_ret -> cpfCnpjPes);
			$this -> setFotoPes($o_ret -> fotoPes);
			$this -> setDataCadastroPes($o_ret -> dataCadastroPes);
			$this -> setTelefoneFixoPes($o_ret -> telefoneFixoPes);
			$this -> setTelefoneCelularPes($o_ret -> telefoneCelularPes);
			$this -> setTb_Status_idSta($o_ret -> tb_Status_idSta);
			$this -> setTb_perfil_idPer($o_ret -> tb_perfil_idPer);
			$this -> setNomePer($o_ret -> nomePer);
			$this -> setNomeSta($o_ret -> nomeSta);
			$this -> setTb_endereco_idEnd($o_ret -> tb_endereco_idEnd);
			$this -> setIdEnd($o_ret -> idEnd);
			$this -> setCepEnd($o_ret -> cepEnd);
			$this -> setruaEnd($o_ret -> ruaEnd);
			$this -> setBairroEnd($o_ret -> bairroEnd);
			$this -> setComplementoEnd($o_ret -> complementoEnd);
			$this -> setTb_Estado_idEst($o_ret -> tb_Estado_idEst);
			$this -> setMedia($o_ret -> media);
			$this -> setQtdeLances($o_ret -> qtdeLances);
			$this -> setQtdeTransportes($o_ret -> qtdeTransportes);

			return $this;
		}

	}

	public function loadByIdAcesso($idPes = null, $emailPes = null) {
		$v_pessoas = array();
		$st_query = "SELECT  * FROM tb_pessoa ";
		$st_query .= "INNER join tb_perfil on idPer = tb_perfil_idper ";
		$st_query .= "LEFT JOIN tb_endereco ON IdEnd = tb_endereco_idEnd ";
		$st_query .= "LEFT JOIN tb_cidade ON IdCid = tb_endereco.tb_Cidade_idCid ";
		$st_query .= "LEFT JOIN tb_estado ON IdEst = tb_endereco.tb_Estado_idEst ";
		$st_query .= "INNER JOIN tb_status ON idSta = tb_status_idSta ";
		if ($idPes !== NULL)
			$st_query .= " AND idPes = $idPes";
		elseif ($emailPes !== NULL)
			$st_query .= " and emailPes = '$emailPes'";
		$st_query .= ";";
		//echo $st_query;
		//exit;
		$o_data = $this -> o_db -> query($st_query);
		$o_ret = $o_data -> fetchObject();
		$this -> setIdPes($o_ret -> idPes);
		$this -> setPrimeiroNomePes($o_ret -> primeiroNomePes);
		$this -> setSobreNomePes($o_ret -> sobreNomePes);
		$this -> setEmailPes($o_ret -> emailPes);
		$this -> setSenhaPes($o_ret -> senhaPes);
		$this -> setCpfCnpjPes($o_ret -> cpfCnpjPes);
		$this -> setFotoPes($o_ret -> fotoPes);
		$this -> setDataCadastroPes($o_ret -> dataCadastroPes);
		$this -> setTelefoneFixoPes($o_ret -> telefoneFixoPes);
		$this -> setTelefoneCelularPes($o_ret -> telefoneCelularPes);
		$this -> setTb_Status_idSta($o_ret -> tb_Status_idSta);
		$this -> setTb_perfil_idPer($o_ret -> tb_perfil_idPer);
		$this -> setNomePer($o_ret -> nomePer);
		$this -> setNomeSta($o_ret -> nomeSta);
		$this -> setTb_endereco_idEnd($o_ret -> tb_endereco_idEnd);
		$this -> setIdEnd($o_ret -> idEnd);
		$this -> setCepEnd($o_ret -> cepEnd);
		$this -> setruaEnd($o_ret -> ruaEnd);
		$this -> setBairroEnd($o_ret -> bairroEnd);
		$this -> setComplementoEnd($o_ret -> complementoEnd);
		$this -> setTb_Cidade_idCid($o_ret -> tb_Cidade_idCid);
		$this -> setTb_Estado_idEst($o_ret -> tb_Estado_idEst);
		$this -> setCodigoPer($o_ret -> codigoPer);
		return $this;
	}

	/**
	 * Salva dados contidos na instancia da classe
	 * na tabela de pessoa. Se o ID for passado,
	 * um UPDATE será executado, caso contrário, um
	 * INSERT será executado
	 * @throws PDOException
	 * @return integer
	 */
	public function save() {

		if (is_null($this -> idPes))

			$st_query = "INSERT INTO tb_pessoa
						(	
							
						  primeiroNomePes,
						  sobreNomePes,
						  emailPes,
						  senhaPes,
						  cpfCnpjPes,
						  fotoPes,
						  dataCadastroPes,
						  telefoneFixoPes,
						  telefoneCelularPes,
						  tb_endereco_idEnd,
						  tb_perfil_idPer,
						  tb_Status_idSta
						    
						
						)
						VALUES
						(	
							'$this->primeiroNomePes',
							'$this->sobreNomePes',
							'$this->emailPes',
							'$this->senhaPes',
							'$this->cpfCnpjPes',
							'$this->fotoPes',
							 NOW(),
							'$this->telefoneFixoPes',
							'$this->telefoneCelularPes',
							'$this->tb_perfil_idPer',
							'$this->tb_endereco_idEnd',
							'$this->tb_Status_idSta'
									
						);";
		
else
			$st_query = "UPDATE
							tb_pessoa
						SET
						
								
						  primeiroNomePes = '$this->primeiroNomePes',
						  sobreNomePes = '$this->sobreNomePes',
						  emailPes = '$this->emailPes',
						  senhaPes = '$this->senhaPes',
						  cpfCnpjPes = '$this->cpfCnpjPes',
						  fotoPes = '$this->fotoPes',
						  dataCadastroPes = '$this->dataCadastroPes',
						  telefoneFixoPes = '$this->telefoneFixoPes',
						  telefoneCelularPes = '$this->telefoneCelularPes',
						  tb_endereco_idEnd = '$this->tb_perfil_idPer',
						  tb_perfil_idPer ='$this->tb_endereco_idEnd',
						  tb_Status_idSta = '$this->tb_Status_idSta'
						WHERE
							idPes = $this->idPes";
		$st_query .= " select @@IDENTITY as id;";
		try {

			$objSth = $this -> o_db -> query($st_query);
			$objSth -> nextRowset();
			$rowTd = $objSth -> fetch(PDO::FETCH_NUM);
			return $rowTd[0];
		} catch (PDOException $e) {
			throw $e;

		}
		return false;
	}

	/**
	 * Salva dados contidos na instancia da classe
	 * na tabela de pessoa. Se o ID for passado,
	 * um UPDATE será executado, caso contrário, um
	 * INSERT será executado
	 * @throws PDOException
	 * @return integer
	 */
	public function saveCli() {

		if (is_null($this -> idPes)) {

			$st_query = "INSERT INTO tb_pessoa
			(
				
			primeiroNomePes,
			sobreNomePes,
			emailPes,
			senhaPes,
			cpfCnpjPes,
			fotoPes,
			dataCadastroPes,
			telefoneFixoPes,
			telefoneCelularPes,
			tb_perfil_idPer,
			tb_Status_idSta
	
	
			)
			VALUES
			(
			'$this->primeiroNomePes',
			'$this->sobreNomePes',
			'$this->emailPes',
			'$this->senhaPes',
			'$this->cpfCnpjPes',
			'$this->fotoPes',
			NOW(),
			'$this->telefoneFixoPes',
			'$this->telefoneCelularPes',
			'3',
			'1'
				
			);";
			$st_query .= " SELECT LAST_INSERT_ID();";
		} else {
			if ($this -> fotoPes) {
				$st_query = "UPDATE
				tb_pessoa
				SET
		
		
				primeiroNomePes = '$this->primeiroNomePes',
				sobreNomePes = '$this->sobreNomePes',
				emailPes = '$this->emailPes',
				senhaPes = '$this->senhaPes',
				cpfCnpjPes = '$this->cpfCnpjPes',
				fotoPes = '$this->fotoPes',
				telefoneFixoPes = '$this->telefoneFixoPes',
				telefoneCelularPes = '$this->telefoneCelularPes',
				tb_perfil_idPer = '$this->tb_perfil_idPer',
				tb_Status_idSta = '$this->tb_Status_idSta'
				WHERE
				idPes = $this->idPes";
				$st_query .= " SELECT LAST_INSERT_ID();";
			} else {
				$st_query = "UPDATE
				tb_pessoa
				SET
		
		
				primeiroNomePes = '$this->primeiroNomePes',
				sobreNomePes = '$this->sobreNomePes',
				emailPes = '$this->emailPes',
				senhaPes = '$this->senhaPes',
				cpfCnpjPes = '$this->cpfCnpjPes',
				telefoneFixoPes = '$this->telefoneFixoPes',
				telefoneCelularPes = '$this->telefoneCelularPes',
				tb_perfil_idPer = '$this->tb_perfil_idPer',
				tb_Status_idSta = '$this->tb_Status_idSta'
				WHERE
				idPes = $this->idPes";
			}
		}

		try {
			//echo $st_query.'<br><br>';
			//exit;
			//$this->o_db->exec($st_query);
			$objSth = $this -> o_db -> query($st_query);
			$objSth -> nextRowset();
			$rowTd = $objSth -> fetch(PDO::FETCH_NUM);
			return $rowTd[0];
		} catch (PDOException $e) {
			throw $e;

		}
		return false;
	}

	public function saveSenha() {

		if (is_null($this -> idPes)) {

			$st_query = "INSERT INTO tb_pessoa
			(

			senhaPes

			)
			VALUES
			(
			'
			'$this->senhaPes'
				
			);";
		} else {
			$st_query = "UPDATE
				tb_pessoa
				SET

				senhaPes = '$this->senhaPes'
			
				WHERE
				idPes = $this->idPes";
		}

		try {
			//echo $st_query;
			//exit;
			$this -> o_db -> exec($st_query);
		} catch (PDOException $e) {
			throw $e;

		}
		return false;
	}

	/**
	 * Salva dados contidos na instancia da classe
	 * na tabela de pessoa. Se o ID for passado,
	 * um UPDATE será executado, caso contrário, um
	 * INSERT será executado
	 * @throws PDOException
	 * @return integer
	 */
	public function saveTran() {

		if (is_null($this -> idPes))

			$st_query = "INSERT INTO tb_pessoa
			(
				
			primeiroNomePes,
			sobreNomePes,
			emailPes,
			senhaPes,
			cpfCnpjPes,
			fotoPes,
			dataCadastroPes,
			telefoneFixoPes,
			telefoneCelularPes,
			tb_endereco_idEnd,
			tb_perfil_idPer,
			tb_Status_idSta
	
	
			)
			VALUES
			(
			'$this->primeiroNomePes',
			'$this->sobreNomePes',
			'$this->emailPes',
			'$this->senhaPes',
			'$this->cpfCnpjPes',
			'$this->fotoPes',
			NOW(),
			'$this->telefoneFixoPes',
			'$this->telefoneCelularPes',
			'$this->tb_endereco_idEnd',
			'1',
			'1'
				
			);";
		
else {
			if ($this -> fotoPes) {
				$st_query = "UPDATE
						tb_pessoa
						SET
				
				
						primeiroNomePes = '$this->primeiroNomePes',
						sobreNomePes = '$this->sobreNomePes',
						emailPes = '$this->emailPes',
						senhaPes = '$this->senhaPes',
						cpfCnpjPes = '$this->cpfCnpjPes',
						fotoPes = '$this->fotoPes',
						telefoneFixoPes = '$this->telefoneFixoPes',
						telefoneCelularPes = '$this->telefoneCelularPes',
						tb_Status_idSta = '$this->tb_Status_idSta'
						WHERE
						idPes = $this->idPes";
			} else {
				$st_query = "UPDATE
						tb_pessoa
						SET
				
				
						primeiroNomePes = '$this->primeiroNomePes',
						sobreNomePes = '$this->sobreNomePes',
						emailPes = '$this->emailPes',
						senhaPes = '$this->senhaPes',
						cpfCnpjPes = '$this->cpfCnpjPes',
						telefoneFixoPes = '$this->telefoneFixoPes',
						telefoneCelularPes = '$this->telefoneCelularPes',
						tb_Status_idSta = '$this->tb_Status_idSta'
						WHERE
						idPes = $this->idPes";

			}
		}
		$st_query .= " SELECT LAST_INSERT_ID();";
		try {

			//echo $st_query.'<br><br>';
			//exit;
			//$this->o_db->exec($st_query);
			$objSth = $this -> o_db -> query($st_query);
			$objSth -> nextRowset();
			$rowTd = $objSth -> fetch(PDO::FETCH_NUM);
			return $rowTd[0];
		} catch (PDOException $e) {
			throw $e;

		}
		return false;
	}

	/**
	 * Deleta os dados persistidos na tabela de
	 * pessoa usando como referencia, o id da classe.
	 */
	public function delete() {
		if (!is_null($this -> idPes)) {
			$st_query = "DELETE FROM
							tb_pessoa
						WHERE idPes = $this->idPes";
			if ($this -> o_db -> exec($st_query) > 0)
				return $this -> tb_endereco_idEnd;
		}
		return false;
	}

	public function bloquearAcesso() {
		if (!is_null($this -> idPes)) {
			$st_query = "UPDATE
			tb_pessoa
			SET

			tb_Status_idSta = '2'
			WHERE
			idPes = $this->idPes";
			try {

				$this -> o_db -> exec($st_query);
			} catch (PDOException $e) {
				throw $e;

			}
			return false;
		}
		return false;
	}

	public function liberarAcesso() {
		if (!is_null($this -> idPes)) {
			$st_query = "UPDATE
			tb_pessoa
			SET

			tb_Status_idSta = '1'
			WHERE
			idPes = $this->idPes";
			try {

				$this -> o_db -> exec($st_query);
			} catch (PDOException $e) {
				throw $e;

			}
			return false;
		}
		return false;
	}

}
?>