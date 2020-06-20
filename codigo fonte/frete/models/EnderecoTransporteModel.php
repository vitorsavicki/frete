<?php
require_once 'models/EnderecoTransporteModel.php';

/**
 * @package Exemplo simples com MVC

 * Camada - Modelo ou Model.
 * Diretório Pai - models
 * Arquivo - EnderecoTransporteModel
 *
 * Responsável por gerenciar e persistir os dados dos
 * Endereco do Transporte
 **/
class EnderecoTransporteModel extends PersistModelAbstract {
	
	private $idEndTran;
	private $cepOrigemEndTran;
	private $cepDestinoEndTran;
	private $ruaOrigemEndTran;
	private $ruaDestinoEndTran;
	private $bairroOrigemEndTran;
	private $bairroDestinoEndTran;
	private $tb_cidadeOrigem_IdCid;
	private $tb_cidadeDestino_idCid;

	function __construct() {
		parent::__construct();

	}

	/**
	 * Setters e Getters da
	 * classe EnderecoTransporteModel
	 */
	public function setIdEndTran($idEndTran) {
		$this -> idEndTran = $idEndTran;
		return $this;
	}

	public function getIdEndTran() {
		return $this -> idEndTran;
	}

	public function setCepOrigemEndTran($cepOrigemEndTran) {
		$this -> cepOrigemEndTran = $cepOrigemEndTran;
		return $this;
	}

	public function getCepOrigemEndTran() {
		return $this -> cepOrigemEndTran;
	}

	public function setCepDestinoEndTran($cepDestinoEndTran) {
		$this -> cepDestinoEndTran = $cepDestinoEndTran;
		return $this;
	}

	public function getCepDestinoEndTran() {
		return $this -> cepDestinoEndTran;
	}

	public function setRuaOrigemEndTran($ruaOrigemEndTran) {
		$this -> ruaOrigemEndTran = $ruaOrigemEndTran;
		return $this;
	}

	public function getRuaOrigemEndTran() {
		return $this -> ruaOrigemEndTran;
	}

	public function setRuaDestinoEndTran($ruaDestinoEndTran) {
		$this -> ruaDestinoEndTran = $ruaDestinoEndTran;
		return $this;
	}

	public function getRuaDestinoEndTran() {
		return $this -> ruaDestinoEndTran;
	}

	public function setBairroOrigemEndTran($bairroOrigemEndTran) {
		$this -> bairroOrigemEndTran = $bairroOrigemEndTran;
		return $this;
	}

	public function getBairroOrigemEndTran() {
		return $this -> bairroOrigemEndTran;
	}

	public function setBairroDestinoEndTran($bairroDestinoEndTran) {
		$this -> bairroDestinoEndTran = $bairroDestinoEndTran;
		return $this;
	}

	public function getBairroDestinoEndTran() {
		return $this -> bairroDestinoEndTran;
	}

	public function setTb_cidadeOrigem_IdCid($tb_cidadeOrigem_IdCid) {
		$this -> tb_cidadeOrigem_IdCid = $tb_cidadeOrigem_IdCid;
		return $this;
	}

	public function getTb_cidadeOrigem_IdCid() {
		return $this -> tb_cidadeOrigem_IdCid;
	}

	public function setTb_cidadeDestino_IdCid($tb_cidadeDestino_IdCid) {
		$this -> tb_cidadeDestino_IdCid = $tb_cidadeDestino_IdCid;
		return $this;
	}

	public function getTb_cidadeDestino_IdCid() {
		return $this -> tb_cidadeDestino_IdCid;
	}

	/**
	 * Retorna um array contendo os Enderecos dos Transportes
	 * @param string $IdEndTran
	 * @return Array
	 */
	public function _list($ruaOrigemEndTran = null) {
		if (!is_null($ruaOrigemEndTran))
			$st_query = "SELECT * FROM tb_endereco_transporte WHERE ruaOrigemEndTran LIKE '%$ruaOrigemEndTran%';";
		else
			$st_query = 'SELECT * FROM tb_endereco_transporte;';

		$v_endtransportes = array();
		try {
			$o_data = $this -> o_db -> query($st_query);
			while ($o_ret = $o_data -> fetchObject()) {
				$o_endtransporte = new EnderecoTransporteModel();
				$o_endtransporte -> setIdEndTran($o_ret -> idEndTran);
				$o_endtransporte -> setCepOrigemEndTran($o_ret -> cepOrigemEndTran);
				$o_endtransporte -> setCepDestinoEndTran($o_ret -> cepDestinoEndTran);
				$o_endtransporte -> setRuaOrigemEndTran($o_ret -> ruaOrigemEndTran);
				$o_endtransporte -> setRuaDestinoEndTran($o_ret -> ruaDestinoEndTran);
				$o_endtransporte -> setBairroOrigemEndTran($o_ret -> bairroOrigemEndTran);
				$o_endtransporte -> setBairroDestinoEndTran($o_ret -> bairroDestinoEndTran);
				$o_endtransporte -> setTb_cidadeOrigem_IdCid($o_ret ->tb_cidadeOrigem_IdCid);
				$o_endtransporte -> setTb_cidadeDestino_IdCid($o_ret ->$tb_cidadeDestino_IdCid);
				array_push($v_endtransportes, $o_endtransporte);
			}
		} catch(PDOException $e) {
		}
		return $v_endtransportes;
	}

	/**
	 * Retorna os dados de um endereco de transporte referente
	 * a um determinado Id
	 * @param integer $idEndTran
	 * @return ContatoModel
	 */
	public function loadById($idEndTran) {
		$v_contatos = array();
		$st_query = "SELECT * FROM tb_endereco_transporte WHERE idEndTran = $idEndTran;";
		$o_data = $this -> o_db -> query($st_query);
		$o_ret = $o_data -> fetchObject();
		$this -> setIdEndTran($o_ret -> idEndTran);
		$this -> setCepOrigemEndTran($o_ret -> cepOrigemEndTran);
		$this -> setCepDestinoEndTran($o_ret -> cepDestinoEndTran);
		$this -> setRuaOrigemEndTran($o_ret -> ruaOrigemEndTran);
		$this -> setRuaDestinoEndTran($o_ret -> ruaDestinoEndTran);
		$this -> setBairroOrigemEndTran($o_ret -> bairroOrigemEndTran);
		$this -> setBairroDestinoEndTran($o_ret -> bairroDestinoEndTran);
		$this -> setTb_cidadeOrigem_IdCid($o_ret -> tb_cidadeOrigem_IdCid);
		$this -> setTb_cidadeDestino_IdCid($o_ret -> tb_cidadeDestino_IdCid);
		return $this;
	}

	/**
	 * Salva dados contidos na instancia da classe
	 * na tabela de endereco de transporte. Se o ID for passado,
	 * um UPDATE será executado, caso contrário, um
	 * INSERT será executado
	 * @throws PDOException
	 * @return integer
	 */
	public function save() {
		if (is_null($this -> idEndTran))
			$st_query = "INSERT INTO tb_endereco_transporte
						(
								cepOrigemEndTran,
								cepDestinoEndTran,
								ruaOrigemEndTran,
								ruaDestinoEndTran,
								bairroOrigemEndTran,
								bairroDestinoEndTran,
								tb_cidadeOrigem_IdCid,
								tb_cidadeDestino_IdCid
						)
						VALUES
						(
								'$this->cepOrigemEndTran',
								'$this->cepDestinoEndTran',
								'$this->ruaOrigemEndTran',
								'$this->ruaDestinoEndTran',
								'$this->bairroOrigemEndTran',
								'$this->bairroDestinoEndTran',
								'$this->tb_cidadeOrigem_IdCid',
								'$this->tb_cidadeDestino_IdCid'
						);";
		else
			$st_query = "UPDATE
							tb_endereco_transporte
						SET
								cepOrigemEndTran = '$this->cepOrigemEndTran',
								cepDestinoEndTran = '$this->cepDestinoEndTran',
								ruaOrigemEndTran = '$this->ruaOrigemEndTran',
								ruaDestinoEndTran = '$this->ruaDestinoEndTran',
								bairroOrigemEndTran = '$this->bairroOrigemEndTran',
								bairroDestinoEndTran = '$this->bairroDestinoEndTran',
								tb_cidadeOrigem_IdCid = '$this->tb_cidadeOrigem_IdCid',
								tb_cidadeDestino_IdCid = '$this->tb_cidadeDestino_IdCid'
						WHERE
							idEndTran = $this->idEndTran;";
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
		return 0;
	}

	/**
	 * Deleta os dados persistidos na tabela de
	 * endereco de transporte usando como referencia, o id da classe.
	 */
	public function delete() {
		if (!is_null($this -> idEndTran)) {
			$st_query = "DELETE FROM
							tb_endereco_transporte
						WHERE idEndTran = $this->idEndTran";
			if ($this -> o_db -> exec($st_query) > 0)
				return true;
		}
		return false;
	}

}
?>