<?php
require_once 'models/TransporteModel.php';
require_once 'models/ItemModel.php';
require_once 'models/CategoriaModel.php';
require_once 'models/EnderecoTransporteModel.php';
require_once 'models/EstadoModel.php';
require_once 'models/CidadeModel.php';
require_once 'models/PessoaModel.php';
require_once 'models/ImagemTransporteModel.php';

/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - TransporteModel
*
* Responsável por gerenciar e persistir os dados dos  
* Transportes 
**/
class TransporteModel extends PersistModelAbstract
{
	
   private $idTransp;
   private $descricaoTransp;
   private $dataRetiradaTransp;
   private $dataCadastroTransp;
   private $tb_statusTransp_idStaTransp;
   private $tb_categoria_idCat;
   private $tb_pessoa_idPes;
   private $comentarioAdiTransp;
   private $numAjudantesTransp;
   private $tb_endereco_transporte_idEndTran;
   private $precoMaxiTransp;
   private $horaRetiradaTransp;
   private $ruaOrigemEndTran;
   private $bairroOrigemEndTran;
   private $cidOrigem;
   private $estOrigem;
   private $ruaDestinoEndTran;
   private $bairroDestinoEndTran;
   private $cidDestino;
   private $estDestino;
   private $qtdeLan;
   private $menorLan;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe Transporte
	 */
	
	public function setIdTransp( $idTransp )
	{
		$this->idTransp = $idTransp;
		return $this;
	}
	
	public function getIdTransp()
	{
		return $this->idTransp;
	}
	
	public function setDescricaoTransp( $descricaoTransp )
	{
		$this->descricaoTransp = $descricaoTransp;
		return $this;
	}
	
	public function getDescricaoTransp()
	{
		return $this->descricaoTransp;
	}
	
	public function setDataRetiradaTransp( $dataRetiradaTransp )
	{
		$this->dataRetiradaTransp = $dataRetiradaTransp;
		return $this;
	}
	
	public function getDataRetiradaTransp()
	{
		return $this->dataRetiradaTransp;
	}
	
	public function setHoraRetiradaTransp( $horaRetiradaTransp )
	{
		$this->horaRetiradaTransp = $horaRetiradaTransp;
		return $this;
	}
	
	public function getHoraRetiradaTransp()
	{
		return $this->horaRetiradaTransp;
	}
	
	public function setDataCadastroTransp( $dataCadastroTransp )
	{
		$this->dataCadastroTransp = $dataCadastroTransp;
		return $this;
	}
	
	public function getDataCadastroTransp()
	{
		return $this->dataCadastroTransp;
	}
	
	public function setTb_statusTransp_idStaTransp( $tb_statusTransp_idStaTransp )
	{
		$this->tb_statusTransp_idStaTransp = $tb_statusTransp_idStaTransp;
		return $this;
	}
	
	public function getTb_statusTransp_idStaTransp()
	{
		return $this->tb_statusTransp_idStaTransp;
	}
	
	public function setTb_categoria_idCat( $tb_categoria_idCat )
	{
		$this->tb_categoria_idCat = $tb_categoria_idCat;
		return $this;
	}
	
	public function getTb_categoria_idCat()
	{
		return $this->tb_categoria_idCat;
	}
	
	public function setTb_pessoa_idPes( $tb_pessoa_idPes )
	{
		$this->tb_pessoa_idPes  = $tb_pessoa_idPes;
		return $this;
	}
	
	public function getTb_pessoa_idPes ()
	{
		return $this->tb_pessoa_idPes ;
	}
	
	public function setComentarioAdiTransp( $comentarioAdiTransp )
	{
		$this->comentarioAdiTransp  = $comentarioAdiTransp;
		return $this;
	}
	
	public function getComentarioAdiTransp ()
	{
		return $this->comentarioAdiTransp ;
	}
	
	public function setNumAjudantesTransp( $numAjudantesTransp )
	{
		$this->numAjudantesTransp  = $numAjudantesTransp;
		return $this;
	}
	
	public function getNumAjudantesTransp ()
	{
		return $this->numAjudantesTransp ;
	}
	
	public function setTb_endereco_transporte_idEndTran( $tb_endereco_transporte_idEndTran )
	{
		$this->tb_endereco_transporte_idEndTran  = $tb_endereco_transporte_idEndTran;
		return $this;
	}
	
	public function getTb_endereco_transporte_idEndTran ()
	{
		return $this->tb_endereco_transporte_idEndTran ;
	}

	public function setPrecoMaxiTransp( $precoMaxiTransp )
	{
		$this->precoMaxiTransp  = $precoMaxiTransp;
		return $this;
	}
	
	public function getPrecoMaxiTransp ()
	{
		return $this->precoMaxiTransp ;
	}
	
	public function setRuaOrigemEndTran( $ruaOrigemEndTran )
	{
		$this->ruaOrigemEndTran  = $ruaOrigemEndTran;
		return $this;
	}
	
	public function getRuaOrigemEndTran ()
	{
		return $this->ruaOrigemEndTran ;
	}
	
	public function setBairroOrigemEndTran( $bairroOrigemEndTran )
	{
		$this->bairroOrigemEndTran  = $bairroOrigemEndTran;
		return $this;
	}
	
	public function getBairroOrigemEndTran ()
	{
		return $this->bairroOrigemEndTran ;
	}
	
	public function setCidOrigem( $cidOrigem )
	{
		$this->cidOrigem  = $cidOrigem;
		return $this;
	}
	
	public function getCidOrigem ()
	{
		return $this->cidOrigem ;
	}
 
 	public function setEstOrigem( $estOrigem )
	{
		$this->estOrigem  = $estOrigem;
		return $this;
	}
	
	public function getEstOrigem ()
	{
		return $this->estOrigem ;
	}
	
	public function setRuaDestinoEndTran( $ruaDestinoEndTran )
	{
		$this->ruaDestinoEndTran  = $ruaDestinoEndTran;
		return $this;
	}
	
	public function getRuaDestinoEndTran ()
	{
		return $this->ruaDestinoEndTran ;
	}

	public function setBairroDestinoEndTran( $bairroDestinoEndTran )
	{
		$this->bairroDestinoEndTran  = $bairroDestinoEndTran;
		return $this;
	}
	
	public function getBairroDestinoEndTran ()
	{
		return $this->bairroDestinoEndTran ;
	}

	public function setCidDestino( $cidDestino )
	{
		$this->bairroDestinoEndTran  = $cidDestino;
		return $this;
	}
	
	public function getCidDestino ()
	{
		return $this->cidDestino ;
	}
	
	public function setEstDestino( $estDestino )
	{
		$this->estDestino  = $estDestino;
		return $this;
	}
	
	public function getEstDestino ()
	{
		return $this->estDestino ;
	}
	
	public function setQtdeLan( $qtdeLan )
	{
		$this->qtdeLan  = $qtdeLan;
		return $this;
	}
	
	public function getQtdeLan ()
	{
		return $this->qtdeLan ;
	}
	
	public function setMenorLan( $menorLan )
	{
		$this->menorLan  = $menorLan;
		return $this;
	}
	
	public function getMenorLan ()
	{
		return $this->menorLan ;
	}
	
	
	public function setMotivoCancelamentoTransp( $motivoCancelamentoTransp )
	{
		$this->motivoCancelamentoTransp  = $motivoCancelamentoTransp;
		return $this;
	}
	
	public function getMotivoCancelamentoTransp()
	{
		return $this->motivoCancelamentoTransp;
	}
  
  
	/**
	* Retorna um array contendo os contatos
	* @param string $st_nome
	* @return Array
	*/
	
	public function _list( $tb_categoria_idCat = null, $dataIniRetiradaTransp = null, $dataFimRetiradaTransp = null )
	{
		$st_query = "SELECT * FROM tb_transporte";
		$st_where = "";
		if(!is_null($tb_categoria_idCat ))
			 $st_where = " WHERE tb_categoria_idCat = $tb_categoria_idCat";
		if(!is_null($dataIniRetiradaTransp) and !is_null($dataFimRetiradaTransp))
			if(!is_null($st_where))
				$st_where .= " and dataRetiradaTransp between '$dataIniRetiradaTransp' and '$dataFimRetiradaTransp'";
			else
				$st_where = " where dataRetiradaTransp between '$dataIniRetiradaTransp' and '$dataFimRetiradaTransp'";
		$st_query .= $st_where . ";";
		//echo $st_query;
		//exit;
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_transporte = new TransporteModel();
				$o_transporte->setIdTransp($o_ret->idTransp);
				$o_transporte->setDescricaoTransp($o_ret->descricaoTransp);
				$o_transporte->setDataRetiradaTransp($o_ret->dataRetiradaTransp);
				$o_transporte->setHoraRetiradaTransp($o_ret->horaRetiradaTransp);
				$o_transporte->setDataCadastroTransp($o_ret->dataCadastroTransp);
				$o_transporte->setTb_statusTransp_idStaTransp($o_ret->tb_statusTransp_idStaTransp);
				$o_transporte->setTb_categoria_idCat($o_ret->tb_categoria_idCat);
				$o_transporte->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				$o_transporte->setComentarioAdiTransp($o_ret->comentarioAdiTransp);
				$o_transporte->setNumAjudantesTransp($o_ret->numAjudantesTransp);
				$o_transporte->setTb_endereco_transporte_idEndTran($o_ret->tb_endereco_transporte_idEndTran);
				$o_transporte->setPrecoMaxiTransp($o_ret->precoMaxiTransp);
				$o_transporte->setMotivoCancelamentoTransp($o_ret->motivoCancelamentoTransp);
				array_push($v_transportes, $o_transporte);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_transportes;
	}

	
	
	/**
	* Retorna os dados de um transporte referente
	* a um determinado Id
	* @param integer $idTransp
	* @return ContatoModel
	*/
	public function loadById( $idTransp )
	{
		$v_transportes = array();
		$st_query = "select ";
		$st_query .= "		tb_transporte.*, ";
		$st_query .= "		tb_statusTransp.nomeStaTransp, ";
		$st_query .= "		tb_endereco_transporte.*, ";
		$st_query .= "		tb_categoria.nomeCat, ";
		$st_query .= "		cidOri.nomeCid as cidOrigem, ";
		$st_query .= "		estOri.ufEst as estOrigem, ";
		$st_query .= "		cidDes.nomeCid as cidDestino, ";
		$st_query .= "		estDes.ufEst as estDestino ";
		$st_query .= "	from tb_transporte ";
		$st_query .= "	inner join tb_statusTransp on tb_statusTransp.idStaTransp = tb_transporte.tb_statusTransp_idStaTransp ";
		$st_query .= "	inner join tb_categoria on tb_transporte.tb_categoria_idcat = tb_categoria.idcat ";
		$st_query .= "	left join tb_endereco_transporte on tb_transporte.tb_endereco_transporte_idEndTran =  tb_endereco_transporte.idEndTran ";
		$st_query .= "	left join tb_cidade as cidOri on cidOri.idCid = tb_endereco_transporte.tb_cidadeOrigem_IdCid ";
		$st_query .= "	left join tb_estado as estOri on estOri.idEst = cidOri.tb_Estado_idEst ";
		$st_query .= "	left join tb_cidade as cidDes on cidDes.idCid = tb_endereco_transporte.tb_cidadeDestino_IdCid ";
		$st_query .= "	left join tb_estado as estDes on estDes.idEst = cidOri.tb_Estado_idEst ";
		$st_query .= "   WHERE tb_transporte.idTransp = '$idTransp'";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdTransp($o_ret->idTransp);
		$this->setDescricaoTransp($o_ret->descricaoTransp);
		$this->setDataRetiradaTransp($o_ret->dataRetiradaTransp);
		$this->setHoraRetiradaTransp($o_ret->horaRetiradaTransp);
		$this->setDataCadastroTransp($o_ret->dataCadastroTransp);
		$this->setTb_statusTransp_idStaTransp($o_ret->tb_statusTransp_idStaTransp);
		$this->setTb_categoria_idCat($o_ret->tb_categoria_idCat);
		$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);	
		$this->setComentarioAdiTransp($o_ret->comentarioAdiTransp);
		$this->setNumAjudantesTransp($o_ret->numAjudantesTransp);
		$this->setTb_endereco_transporte_idEndTran($o_ret->tb_endereco_transporte_idEndTran);
		$this->setPrecoMaxiTransp($o_ret->precoMaxiTransp);
		$this->setRuaOrigemEndTran($o_ret->ruaOrigemEndTran);
		$this->setBairroOrigemEndTran($o_ret->bairroOrigemEndTran);
		$this->setCidOrigem($o_ret->cidOrigem);
		$this->setEstOrigem($o_ret->estOrigem);
		$this->setRuaDestinoEndTran($o_ret->ruaDestinoEndTran);
		$this->setBairroDestinoEndTran($o_ret->bairroDestinoEndTran);
		$this->setCidDestino($o_ret->cidDestino);
		$this->setEstDestino($o_ret->estDestino);
		$this->setMotivoCancelamentoTransp($o_ret->motivoCancelamentoTransp);
	  
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de transporte. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/

	public function save()
	{
		$b_insert = false;
		if(is_null($this->idTransp) || $this->idTransp == NULL)
		{
			$b_insert = true;
			$st_query = "INSERT INTO tb_transporte
						(
						  descricaoTransp
						  ,dataRetiradaTransp
						  ,dataCadastroTransp
						  ,tb_statusTransp_idStaTransp
						  ,comentarioAdiTransp
						  ,numAjudantesTransp
						  ,tb_categoria_idCat
						  ,tb_pessoa_idPes
						  ,tb_endereco_transporte_idEndTran
						  ,precoMaxiTransp
						  ,horaRetiradaTransp
						  ,motivoCancelamentoTransp							
						)
						VALUES
						(
							  '$this->descricaoTransp',
							  '$this->dataRetiradaTransp',
							   NOW(),
							  '$this->tb_statusTransp_idStaTransp',
							  '$this->comentarioAdiTransp',
							  '$this->numAjudantesTransp',
							  '$this->tb_categoria_idCat',
							  '$this->tb_pessoa_idPes',
							  '$this->tb_endereco_transporte_idEndTran',
							  '$this->precoMaxiTransp',
							  '$this->horaRetiradaTransp',
							  '$this->motivoCancelamentoTransp'
						);";
		}
		else
			$st_query = "UPDATE
							tb_transporte
						SET
							  descricaoTransp = '$this->descricaoTransp',
							  dataRetiradaTransp = '$this->dataRetiradaTransp',
							  tb_statusTransp_idStaTransp = '$this->tb_statusTransp_idStaTransp',
							  comentarioAdiTransp = '$this->comentarioAdiTransp',
							  numAjudantesTransp = '$this->numAjudantesTransp',
							  tb_categoria_idCat = '$this->tb_categoria_idCat',
							  tb_pessoa_idPes = '$this->tb_pessoa_idPes',
							  tb_endereco_transporte_idEndTran = '$this->tb_endereco_transporte_idEndTran',
							  precoMaxiTransp = '$this->precoMaxiTransp',
							  horaRetiradaTransp = '$this->horaRetiradaTransp',
							  motivoCancelamentoTransp = '$this->motivoCancelamentoTransp'
						WHERE
							idTransp = $this->idTransp;";
		$st_query .= " SELECT LAST_INSERT_ID();";	
		try
		{
			//echo $st_query.'<br><br>';
			//exit;
			//$this->o_db->exec($st_query);
			$objSth = $this->o_db->query($st_query);
			$objSth->nextRowset();
			$rowTd = $objSth->fetch(PDO::FETCH_NUM);
			$idTransporte = $rowTd[0];
			
			return $idTransporte;
			
		}
		catch (PDOException $e)
		{
			throw $e;
		}
		return 0;			
	}

	public function salvarMotivoCancelamento()
	{
		if(is_null($this->idTransp))
			$st_query = "INSERT INTO tb_transporte
						(
							motivoCancelamentoTransp
						)
						VALUES
						(
							'$this->motivoCancelamentoTransp'
						);";
		else
			$st_query = "UPDATE
							tb_transporte
						SET
							motivoCancelamentoTransp = '$this->motivoCancelamentoTransp'
							
						WHERE
							idtransp = $this->idTransp";
			$st_query .= " select @@IDENTITY as id;";
			//echo $st_query;
			//exit;							
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
	* transporte usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idTransp))
		{
			$st_query = "DELETE FROM
							tb_transporte
						WHERE con_idTransp = $this->idTransp";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	//inicio das minhas modificações
	public function mudarStatusTransporte($statusTransp = null)
	{
		

		$st_query = "update tb_transporte set tb_statusTransp_idStaTransp = ";
		$st_query .= "(SELECT idStaTransp FROM tb_statusTransp where codigoStaTransp = '$statusTransp') ";
		$st_query .= "where idTransp = '$this->idTransp' LIMIT 1";
	
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


	}
	
	//Fim das minhas modificações
?>