<?php
require_once 'models/EnderecoModel.php';


/**
* @package Exemplo simples com MVC 

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - EnderecoModel
*
* Responsável por gerenciar e persistir os dados dos  
* Enderecos
**/
class EnderecoModel extends PersistModelAbstract
{
	 private $idEnd;
  	 private $cepEnd;
  	 private $ruaEnd;
  	 private $numeroEnd;
  	 private $bairroEnd;
 	 private $complementoEnd;
 	 private $tb_Cidade_idCid;
 	 private $tb_Estado_idEst;
 	 private $nomeEst;
 	 private $nomeCid;

	function __construct()
	{
		parent::__construct();
		
	}
	
	
	/**
	 * Setters e Getters da
	 * classe EnderecoModel
	 */
	
	public function setIdEnd( $idEnd )
	{
		$this->idEnd = $idEnd;
		return $this;
	}
	
	public function getIdEnd()
	{
		return $this->idEnd;
	}
	
	public function setCepEnd( $cepEnd )
	{
		$this->cepEnd = $cepEnd;
		return $this;
	}
	
	public function getCepEnd()
	{
		return $this->cepEnd;
	}
	
	public function setRuaEnd( $ruaEnd )
	{
		$this->ruaEnd = $ruaEnd;
		return $this;
	}
	
	public function getRuaEnd()
	{
		return $this->ruaEnd;
	}
	
	public function setBairroEnd( $bairroEnd )
	{
		$this->bairroEnd= $bairroEnd;
		return $this;
	}
	
	public function getBairroEnd()
	{
		return $this->bairroEnd;
	}
	public function setComplementoEnd ( $complementoEnd )
	{
		$this->complementoEnd = $complementoEnd;
		return $this;
	}
	
	public function getComplementoEnd()
	{
		return $this->complementoEnd;
	}
	
	public function setTb_Cidade_idCid( $tb_Cidade_idCid )
	{
		$this->tb_Cidade_idCid = $tb_Cidade_idCid;
		return $this;
	}
	
	public function getTb_Cidade_idCid()
	{
		return $this->tb_Cidade_idCid;
	}
	
	public function setTb_Estado_idEst( $tb_Estado_idEst )
	{
		$this->tb_Estado_idEst = $tb_Estado_idEst;
		return $this;
	}
	
	public function getTb_Estado_idEst()
	{
		return $this->tb_Estado_idEst;
	}
	
	public function setNomeEst ( $nomeEst  )
	{
		$this->nomeEst = $nomeEst;
		return $this;
	}
	
	public function getNomeEst()
	{
		return $this->nomeEst;
	}
	
	public function setNomeCid ( $nomeCid )
	{
		$this->nomeCid = $nomeCid;
		return $this;
	}
	
	public function getNomeCid()
	{
		return $this->nomeCid;
	}
	
	/**
	* Retorna um array contendo os contatos
	* @param string $st_nome
	* @return Array
	*/
	public function _list( $tb_Cidade_idCid, $idPes)
	{
		if(!is_null($tb_Cidade_idCid) or !is_null($idPes)){
			$st_query = "SELECT * FROM tb_endereco as E
			INNER JOIN tb_pessoa AS P ON P.tb_endereco_idEnd = E.idEnd
			INNER JOIN tb_cidade AS C ON E.tb_Cidade_idCid = C.idCid
			INNER JOIN tb_estado AS ES ON E.tb_Estado_idEst = ES.idEst
			WHERE E.tb_Cidade_idCid = $tb_Cidade_idCid
			OR P.idPes = $idPes;";
		}
		else
			$st_query = 'SELECT * FROM tb_endereco;';	
		
		$v_enderecos = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_endereco = new EnderecoModel();
				$o_endereco->setIdEnd($o_ret->idEnd);
				$o_endereco->setCepEnd($o_ret->cepEnd);
				$o_endereco->setRuaEnd($o_ret->ruaEnd);
				$o_endereco->setBairroEnd($o_ret->bairroEnd);
				$o_endereco->setComplementoEnd($o_ret->complementoEnd);
				$o_endereco->setTb_Cidade_idCid($o_ret->tb_Cidade_idCid);
				$o_endereco->setTb_Estado_idEst($o_ret->tb_Estado_idEst);
				$o_endereco->setNomeCid($o_ret->nomeCid);
				$o_endereco->setNomeEst($o_ret->nomeEst);
				array_push($v_enderecos, $o_endereco);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_enderecos;
	}
	
	/**
	* Retorna os dados de um endereco referente
	* a um determinado Id
	* @param integer $idEnd
	* @return ContatoModel
	*/
	public function loadById($idEnd = null,$idPes = null)
	{
		$v_enderecos = array();
		try
		{
			$st_query = "SELECT  * FROM tb_endereco as E
			INNER JOIN tb_pessoa AS P ON P.tb_endereco_idEnd = E.idEnd
			INNER JOIN tb_cidade AS C ON E.tb_Cidade_idCid = C.idCid
			INNER JOIN tb_estado AS ES ON E.tb_Estado_idEst = ES.idEst
			WHERE E.idEnd = '$idEnd'
			OR P.idPes = '$idPes';";
		}
		catch (PDOException $e)
		{
			throw $e;
		}
		
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdEnd($o_ret->idEnd);
				$this->setCepEnd($o_ret->cepEnd);
				$this->setruaEnd($o_ret->ruaEnd);
				$this->setBairroEnd($o_ret->bairroEnd);
				$this->setComplementoEnd($o_ret->complementoEnd);
				$this->setTb_Cidade_idCid($o_ret->tb_Cidade_idCid);
				$this->setTb_Estado_idEst($o_ret->tb_Estado_idEst);
				$this->setNomeCid($o_ret->nomeCid);
				$this->setNomeEst($o_ret->nomeEst);
		return $this;

	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de endereco. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idEnd))
			$st_query = "INSERT INTO tb_endereco
						(
							cepEnd,
							ruaEnd,
							bairroEnd,
							complementoEnd,
							tb_Cidade_idCid,
							tb_Estado_idEst
						)
						VALUES
						(
							'$this->cepEnd',
							'$this->ruaEnd',
							'$this->bairroEnd',
							'$this->complementoEnd',
							'$this->tb_Cidade_idCid',
							'$this->tb_Estado_idEst'
							);";
		else
			$st_query = "UPDATE
							tb_endereco
						SET
							cepEnd = '$this->cepEnd',
							ruaEnd = '$this->ruaEnd',
							bairroEnd = '$this->bairroEnd',
							complementoEnd ='$this->complementoEnd',
							tb_Cidade_idCid = '$this->tb_Cidade_idCid',
							tb_Estado_idEst = '$this->tb_Estado_idEst'
						WHERE
							idEnd = $this->idEnd";
		$st_query .= " select @@IDENTITY as id;";
		try
		{
			//echo $st_query.'<br><br>';
			//exit;
			//$this->o_db->exec($st_query);
			$objSth = $this->o_db->query($st_query);
			$objSth->nextRowset();
			$rowTd = $objSth->fetch(PDO::FETCH_NUM);
			return $rowTd[0];
		}
		catch (PDOException $e)
		{
			throw $e;
		}
		return false;				
	}

	/**
	* Deleta os dados persistidos na tabela de
	* contato usando como referencia, o id da classe.
	*/

	public function delete()
	{
		if(!is_null($this->idEnd))
		{
			$st_query = "DELETE FROM
			tb_endereco
			WHERE idEnd = $this->idEnd";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

}
?>