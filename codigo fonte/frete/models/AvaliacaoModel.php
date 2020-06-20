<?php
require_once 'models/AvaliacaoModel.php';
require_once 'models/PessoaModel.php';
/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - AvaliacaoModel
*
* Responsável por gerenciar e persistir os dados das 
* avaliacoes
**/
class AvaliacaoModel extends PersistModelAbstract
{	
	private $idAva;
  	private $dataAva;
 	private $conteudoAva;
 	private $tb_status_idStaAva;
  	private $tb_transporte_idTransp;
	private $valorAva1;
	private $valorAva2;
	private $valorAva3;
	private $valorAva4;
	private $valorAva5;
	private $valorAva6;
	private $valorAva7;
	private $valorAva8;
	private $valorAva9;
	private $valorAva10;
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
	private $media;
	private $descricaoTransp;
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	/**
	 * Setters e Getters da
	 * classe AvaliacaoModel
	 */
	
	public function setIdAva( $idAva )
	{
		$this->idAva = $idAva;
		return $this;
	}
	
	public function getIdAva()
	{
		return $this->idAva;
	}
	
	public function setDataAva( $dataAva )
	{
		$this->dataAva = $dataAva;
		return $this;
	}
	
	public function getDataAva()
	{
		return $this->dataAva;
	}
	
	
	public function setConteudoAva( $conteudoAva )
	{
		$this->conteudoAva = $conteudoAva;
		return $this;
	}
	
	public function getConteudoAva()
	{
		return $this->conteudoAva;
	}
	
	public function setTb_status_idStaAva( $tb_status_idStaAva )
	{
		$this-> tb_status_idStaAva = $tb_status_idStaAva;
		return $this;
	}
	
	public function getTb_status_idStaAva()
	{
		return $this->tb_status_idStaAva;
	}
	
	public function setTb_transporte_idTransp( $tb_transporte_idTransp )
	{
		$this->tb_transporte_idTransp = $tb_transporte_idTransp;
		return $this;
	}
	
	public function getTb_transporte_idTransp()
	{
		return $this->tb_transporte_idTransp;
	}
	
	public function setValorAva1( $valorAva1 )
	{
		$this->valorAva1 = $valorAva1;
		return $this;
	}
	
	public function getValorAva1()
	{
		return $this->valorAva1;
	}
	
	public function setValorAva2( $valorAva2 )
	{
		$this->valorAva2 = $valorAva2;
		return $this;
	}
	
	public function getValorAva2()
	{
		return $this->valorAva2;
	}
	
	public function setValorAva3( $valorAva3 )
	{
		$this->valorAva3 = $valorAva3;
		return $this;
	}
	
	public function getValorAva3()
	{
		return $this->valorAva3;
	}
	
	public function setValorAva4( $valorAva4 )
	{
		$this->valorAva4 = $valorAva4;
		return $this;
	}
	
	public function getValorAva4()
	{
		return $this->valorAva4;
	}
	
	public function setValorAva5( $valorAva5 )
	{
		$this->valorAva5 = $valorAva5;
		return $this;
	}
	
	public function getValorAva5()
	{
		return $this->valorAva5;
	}
	
	public function setValorAva6( $valorAva6 )
	{
		$this->valorAva6 = $valorAva6;
		return $this;
	}
	
	public function getValorAva6()
	{
		return $this->valorAva6;
	}
	
	public function setValorAva7( $valorAva7 )
	{
		$this->valorAva7 = $valorAva7;
		return $this;
	}
	
	public function getValorAva7()
	{
		return $this->valorAva7;
	}
	
	public function setValorAva8( $valorAva8 )
	{
		$this->valorAva8 = $valorAva8;
		return $this;
	}
	
	public function getValorAva8()
	{
		return $this->valorAva8;
	}
	
	public function setValorAva9( $valorAva9 )
	{
		$this->valorAva9 = $valorAva9;
		return $this;
	}
	
	public function getValorAva9()
	{
		return $this->valorAva9;
	}
	
	public function setValorAva10( $valorAva10 )
	{
		$this->valorAva10 = $valorAva10;
		return $this;
	}
	
	public function getValorAva10()
	{
		return $this->valorAva10;
	}
	
	public function setIdPes ($idPes)
	{
		$this->idPes = $idPes;
		return $this;
	}

	
	public function getIdPes()
	{
		return $this->idPes;
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
	
	public function setEmailPes( $emailPes )
	{
		$this->emailPes = $emailPes;
		return $this;
	}
	
	public function getEmailPes()
	{
		return $this->emailPes;
	}
	
	public function setSenhaPes( $senhaPes )
	{
		$this->senhaPes = $senhaPes;
		return $this;
	}
	
	public function getSenhaPes()
	{
		return $this->senhaPes;
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
	
	public function setFotoPes( $fotoPes )
	{
		$this->fotoPes = $fotoPes;
		return $this;
	}
	
	public function getFotoPes()
	{
		return $this->fotoPes;
	}
	
	public function setDataCadastroPes( $dataCadastroPes )
	{
		$this->dataCadastroPes = $dataCadastroPes;
		return $this;
	}
	
	public function getDataCadastroPes()
	{
		return $this->dataCadastroPes;
	}
	
	public function setTelefoneFixoPes( $telefoneFixoPes )
	{
		$this->telefoneFixoPes = $telefoneFixoPes;
		return $this;
	}
	
	public function getTelefoneFixoPes()
	{
		return $this->telefoneFixoPes;
	}
	
	public function setTelefoneCelularPes( $telefoneCelularPes)
	{
		$this->telefoneCelularPes = $telefoneCelularPes;
		return $this;
	}
	
	public function getTelefoneCelularPes()
	{
		return $this->telefoneCelularPes;
	}
	
	public function setTb_Status_idSta( $tb_Status_idSta )
	{
		$this->tb_Status_idSta = $tb_Status_idSta;
		return $this;
	}
	
	public function getTb_Status_idSta()
	{
		return $this->tb_Status_idSta;
	}
	public function setTb_endereco_idEnd( $tb_endereco_idEnd )
	{
		$this->tb_endereco_idEnd  = $tb_endereco_idEnd;
		return $this;
	}
	
	public function getTb_endereco_idEnd()
	{
		return $this->tb_endereco_idEnd ;
	}
	
	public function setTb_perfil_idPer( $tb_perfil_idPer )
	{
		$this->tb_perfil_idPer   = $tb_perfil_idPer ;
		return $this;
	}
	
	public function getTb_perfil_idPer()
	{
		return $this->tb_perfil_idPer  ;
	}
	
	public function setMedia ( $media )
	{
		$this->media = $media;
		return $this;
	}
	
	public function getMedia()
	{
		return $this->media;
	}
	
	public function setDescricaoTransp ( $descricaoTransp )
	{
		$this->descricaoTransp = $descricaoTransp;
		return $this;
	}
	
	public function getDescricaoTransp()
	{
		return $this->descricaoTransp;
	}
	
	/**
	* Retorna um array contendo os contatos
	* @param string $idItem
	* @return Array
	*/
	public function _list( $dataAva = null, $tituloAva = null )
	{
		$st_query .= "SELECT * FROM tb_avaliacao";
		$st_query .= "INNER JOIN tb_status ON tb_status_idStaAva = idSta";
		$st_query .= "INNER JOIN tb_transporte ON idTransp =  tb_transporte_idTransp";
		$st_query .= "INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_transporte.tb_pessoa_idPes";
		$st_query .= "INNER JOIN tb_lance ON tb_lance.tb_pessoa_idPes = tb_pessoa.idPes";
		
		$st_where .= null;
		if(!is_null($nomeItem)){
			$st_where .= "WHERE nomeItem LIKE '%$dataAva%';";
		}
		if(!is_null($categoria)){
			if(!is_null($st_where)){
				$st_where .= " AND $tituloAva LIKE '%$tituloAva%' ";
			}
			else{
				$st_where .= " where $tituloAva LIKE '%$tituloAva%' ";
			}
		}
		$st_where .=  "tb_lance.vencedorlan = 'S'";
		$st_query .= $st_where . ";";
		$v_avaliacoes = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_avaliacao = new AvaliacaoModel();
				$o_avaliacao->setIdAva($o_ret->idAva);
				$o_avaliacao->setDataAva($o_ret->dataAva);
				$o_avaliacao->setConteudoAva($o_ret->conteudoAva);
				$o_avaliacao->setTb_status_idStaAva($o_ret->tb_status_idStaAva);
				$o_avaliacao->setValorAva1($o_ret->valorAva1);
				$o_avaliacao->setValorAva2($o_ret->valorAva2);
				$o_avaliacao->setValorAva3($o_ret->valorAva3);
				$o_avaliacao->setValorAva4($o_ret->valorAva4);
				$o_avaliacao->setValorAva5($o_ret->valorAva5);
				$o_avaliacao->setValorAva6($o_ret->valorAva6);
				$o_avaliacao->setValorAva7($o_ret->valorAva7);
				$o_avaliacao->setValorAva8($o_ret->valorAva8);
				$o_avaliacao->setValorAva9($o_ret->valorAva9);
				$o_avaliacao->setValorAva10($o_ret->valorAva10);
				$o_avaliacao->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				array_push($v_avaliacoes, $o_avaliacao);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_avaliacoes;
	}
	
	public function _listAvaliacaoTran( $idPes )
	{
		   $st_query =  " SELECT tb_avaliacao.*,tb_pessoa.*,tb_transporte.*, ";
		   $st_query .= "(round((AVG(tb_avaliacao.valorAva1 + tb_avaliacao.valorAva2 + tb_avaliacao.valorAva3 + tb_avaliacao.valorAva4 + tb_avaliacao.valorAva5) / 5),1)) as media "; 
		   $st_query .= "FROM tb_avaliacao ";
		   $st_query .= "INNER JOIN tb_transporte ON tb_transporte.idTransp = tb_avaliacao.tb_transporte_idTransp ";
		   $st_query .= "INNER JOIN tb_lance ON tb_lance.tb_transporte_idTransp = tb_transporte.idTransp ";
		   $st_query .= "INNER JOIN tb_pessoa ON tb_pessoa.idPes = tb_transporte.tb_pessoa_idPes ";
		   $st_query .= "WHERE tb_lance.vencedorlan = 'S' ";
		   $st_query .= "and tb_lance.tb_pessoa_idPes = '$idPes' "; 
		   $st_query .= "GROUP BY ";
		   $st_query .= "tb_pessoa.idPes, ";
		   $st_query .= "tb_pessoa.cpfCnpjPes, ";
		   $st_query .= "tb_pessoa.dataCadastroPes, ";
		   $st_query .= "tb_pessoa.emailPes, tb_pessoa.fotoPes, ";
		   $st_query .= "tb_pessoa.primeiroNomePes, tb_pessoa.senhaPes, ";
		   $st_query .= "tb_pessoa.sobreNomePes, tb_pessoa.tb_endereco_idEnd, ";
		   $st_query .= "tb_pessoa.tb_perfil_idPer, tb_pessoa.tb_Status_idSta, ";
		   $st_query .= "tb_pessoa.telefoneCelularPes, tb_pessoa.telefoneFixoPes, ";
		   $st_query .= "tb_avaliacao.conteudoAva, ";
		   $st_query .= "tb_avaliacao.dataAva, ";
		   $st_query .= "tb_avaliacao.idAva, ";
		   $st_query .= "tb_avaliacao.tb_status_idStaAva, ";
		   $st_query .= "tb_avaliacao.tb_transporte_idTransp, ";
		   $st_query .= "tb_avaliacao.valorAva1, ";
		   $st_query .= "tb_avaliacao.valorAva2, ";
		   $st_query .= "tb_avaliacao.valorAva3, ";
		   $st_query .= "tb_avaliacao.valorAva4, ";
		   $st_query .= "tb_avaliacao.valorAva5, ";
		   $st_query .= "tb_avaliacao.valorAva6, ";
		   $st_query .= "tb_avaliacao.valorAva7, ";
		   $st_query .= "tb_avaliacao.valorAva8, ";
		   $st_query .= "tb_avaliacao.valorAva9, ";
		   $st_query .= "tb_avaliacao.valorAva10, ";
		   $st_query .= "tb_transporte.comentarioAdiTransp, ";
		   $st_query .= "tb_transporte.dataCadastroTransp, ";
		   $st_query .= "tb_transporte.dataRetiradaTransp, ";
		   $st_query .= "tb_transporte.descricaoTransp, ";
		   $st_query .= "tb_transporte.horaRetiradaTransp, ";
		   $st_query .= "tb_transporte.idTransp, ";
		   $st_query .= "tb_transporte.numAjudantesTransp, ";
		   $st_query .= "tb_transporte.precoMaxiTransp, ";
		   $st_query .= "tb_transporte.tb_categoria_idCat, ";
		   $st_query .= "tb_transporte.tb_endereco_transporte_idEndTran, ";
		   $st_query .= "tb_transporte.tb_pessoa_idPes, ";
		   $st_query .= "tb_transporte.tb_statusTransp_idStaTransp, ";
		   $st_query .= "tb_transporte.motivoCancelamentoTransp ";
		   $v_avaliacoes = array();
		   //echo $st_query;
		   //exit;
		   $v_avaliacoes = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_avaliacao = new AvaliacaoModel();
				$o_avaliacao->setIdPes($o_ret->idPes);
				$o_avaliacao->setPrimeiroNomePes($o_ret->primeiroNomePes);
				$o_avaliacao->setSobreNomePes($o_ret->sobreNomePes);
				$o_avaliacao->setEmailPes($o_ret->emailPes);
				$o_avaliacao->setSenhaPes($o_ret->senhaPes);
				$o_avaliacao->setCpfCnpjPes($o_ret->cpfCnpjPes);
				$o_avaliacao->setFotoPes($o_ret->fotoPes);
				$o_avaliacao->setDataCadastroPes($o_ret->dataCadastroPes);
				$o_avaliacao->setTelefoneFixoPes($o_ret->telefoneFixoPes);
				$o_avaliacao->setTelefoneCelularPes($o_ret->telefoneCelularPes);
				$o_avaliacao->setTb_Status_idSta($o_ret->tb_Status_idSta);
				$o_avaliacao->setTb_perfil_idPer($o_ret->tb_perfil_idPer);
				$o_avaliacao->setIdAva($o_ret->idAva);
				$o_avaliacao->setDataAva($o_ret->dataAva);
				$o_avaliacao->setConteudoAva($o_ret->conteudoAva);
				$o_avaliacao->setTb_status_idStaAva($o_ret->tb_status_idStaAva);
				$o_avaliacao->setValorAva1($o_ret->valorAva1);
				$o_avaliacao->setValorAva2($o_ret->valorAva2);
				$o_avaliacao->setValorAva3($o_ret->valorAva3);
				$o_avaliacao->setValorAva4($o_ret->valorAva4);
				$o_avaliacao->setValorAva5($o_ret->valorAva5);
				$o_avaliacao->setValorAva6($o_ret->valorAva6);
				$o_avaliacao->setValorAva7($o_ret->valorAva7);
				$o_avaliacao->setValorAva8($o_ret->valorAva8);
				$o_avaliacao->setValorAva9($o_ret->valorAva9);
				$o_avaliacao->setValorAva10($o_ret->valorAva10);
				$o_avaliacao->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				$o_avaliacao->setMedia($o_ret->media);
				$o_avaliacao->setDescricaoTransp($o_ret->descricaoTransp);
				array_push($v_avaliacoes, $o_avaliacao);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_avaliacoes;
	}

	/**
	* Retorna os dados de um item referente
	* a um determinado Id
	* @param integer $idItem
	* @return ItemModel
	*/
	public function loadById( $idAva )
	{
		$v_v_avaliacoes = array();
		$st_query = "SELECT * FROM tb_avaliacao WHERE idAva = $idAva;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdAva($o_ret->idAva);
		$this->setDataAva($o_ret->dataAva);
		$this->setConteudoAva($o_ret->conteudoAva);
		$this->setTb_status_idStaAva($o_ret->tb_status_idStaAva);
		$this->setValorAva1($o_ret->valorAva1);
		$this->setValorAva2($o_ret->valorAva2);
		$this->setValorAva3($o_ret->valorAva3);
		$this->setValorAva4($o_ret->valorAva4);
		$this->setValorAva5($o_ret->valorAva5);
		$this->setValorAva6($o_ret->valorAva6);
		$this->setValorAva7($o_ret->valorAva7);
		$this->setValorAva8($o_ret->valorAva8);
		$this->setValorAva9($o_ret->valorAva9);
		$this->setValorAva10($o_ret->valorAva10);
		$this->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de item. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		
		if(is_null($this->idAva))
			$st_query = "INSERT INTO tb_avaliacao
						(
							
							  	dataAva
					           ,conteudoAva
					           ,tb_status_idStaAva
					           ,valorAva1
					           ,tb_transporte_idTransp
					           ,valorAva2
					           ,valorAva3
					           ,valorAva4
					           ,valorAva5
					           ,valorAva6
					           ,valorAva7
					           ,valorAva8
					           ,valorAva9
					           ,valorAva10
						)
						VALUES
						(
								 NOW()
					           ,'$this->conteudoAva'
					           ,'$this->tb_status_idStaAva'
					           ,'$this->valorAva1'
					           ,'$this->tb_transporte_idTransp'
					           ,'$this->valorAva2'
					           ,'$this->valorAva3'
					           ,'$this->valorAva4'
					           ,'$this->valorAva5'
					           ,'$this->valorAva6'
					           ,'$this->valorAva7'
					           ,'$this->valorAva8'
					           ,'$this->valorAva9'
					           ,'$this->valorAva10'
						);";
		else
			$st_query = "UPDATE
							tb_avaliacao
						SET
					            conteudoAva = '$this->conteudoAva'
					           ,tb_status_idStaAva = '$this->tb_status_idStaAva'
					           ,valorAva1  = '$this->valorAva1'
					           ,valorAva2  = '$this->valorAva2'
					           ,valorAva3  = '$this->valorAva3'
					           ,valorAva4  = '$this->valorAva4'
					           ,valorAva5  = '$this->valorAva5'
					           ,valorAva6  = '$this->valorAva6'
					           ,valorAva7  = '$this->valorAva7'
					           ,valorAva8  = '$this->valorAva8'
					           ,valorAva9  = '$this->valorAva9'
					           ,valorAva10 = '$this->valorAva10'
						WHERE
							idAva = $this->idAva";
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
	* item usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idAva))
		{
			$st_query = "DELETE FROM
							tb_avaliacao
						WHERE idAva = $this->idAva";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
}
?>