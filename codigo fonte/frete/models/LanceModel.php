<?php
require_once 'models/LanceModel.php';

/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - LanceModel
*
* Responsável por gerenciar e persistir os dados dos  
* Lances 
**/
class LanceModel extends PersistModelAbstract
{
	private  $idLan;
	private  $valorLan;
	private  $dataLan;
	private  $tb_transporte_idTransp;
	private  $tb_pessoa_idPes;
	private  $tb_pessoa_TransporteEfetuado;
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	
	/**
	 * Setters e Getters da
	 * classe LanceModel
	 */
	
	public function setIdLan( $idLan )
	{
		$this->idLan = $idLan;
		return $this;
	}
	
	public function getIdLan()
	{
		return $this->idLan;
	}
	
	public function setValorLan( $valorLan )
	{
		$this->valorLan = $valorLan;
		return $this;
	}
	
	public function getValorLan()
	{
		return $this->valorLan;
	}
	
	public function setDataLan( $dataLan )
	{
		$this->dataLan = $dataLan;
		return $this;
	}
	
	public function getDataLan()
	{
		return $this->dataLan;
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
	
	public function setTb_pessoa_idPes( $tb_pessoa_idPes )
	{
		$this->tb_pessoa_idPes = $tb_pessoa_idPes;
		return $this;
	}
	
	public function getTb_pessoa_idPes()
	{
		return $this->tb_pessoa_idPes;
	}
	
	public function setVencedorLan( $vencedorLan )
	{
		$this->vencedorLan = $vencedorLan;
		return $this;
	}
	
	public function getVencedorLan()
	{
		return $this->vencedorLan;
	}
	
	public function setTb_pessoa_TransporteEfetuado( $tb_pessoa_TransporteEfetuado )
	{
		$this->tb_pessoa_TransporteEfetuado = $tb_pessoa_TransporteEfetuado;
		return $this;
	}
	
	public function getTb_pessoa_TransporteEfetuado()
	{
		return $this->tb_pessoa_TransporteEfetuado;
	}
	
	
	/**
	* Retorna um array contendo os Lance
	* @param string $idLan
	* @return Array
	*/
	public function _list( $idTransp = null, $idPes = null)
	{
		if(!is_null($idTransp))
			$st_query = "SELECT DISTINCT TQ.*,
			(select count(*) from tb_lance t1 where t1.vencedorLan = 'S' and t1.tb_pessoa_idPes = TQ.tb_pessoa_idPes) qtdeTransportes
			FROM tb_lance TQ
			WHERE Tb_transporte_idTransp ='$idTransp' AND TQ.dataLan = (SELECT MAX(dataLan) 
			                                   FROM tb_lance
			                                   WHERE tb_pessoa_idPes = TQ.tb_pessoa_idPes
			                                   AND tb_lance.tb_transporte_idTransp = '$idTransp'
			                                   GROUP BY tb_pessoa_idPes)";
		else
			$st_query = "SELECT DISTINCT TQ.*,
			(select count(*) from tb_lance t1 where t1.vencedorLan = 'S' and t1.tb_pessoa_idPes = TQ.tb_pessoa_idPes) qtdeTransportes
			FROM tb_lance TQ";
		if(!is_null($idPes))
			if(!is_null($idTransp))
				$st_query .= " and tb_pessoa_idPes = '$idPes'";
			else
				$st_query .= " where tb_pessoa_idPes = '$idPes'";
		$st_query .= " order by dataLan desc;";
		//echo $st_query;
		//exit;
		$v_lances = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_lance = new LanceModel();
				$o_lance->setIdLan($o_ret->idLan);
				$o_lance->setValorLan($o_ret->valorLan);
				$o_lance->setDataLan($o_ret->dataLan);
				$o_lance->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				$o_lance->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				$o_lance->setTb_pessoa_TransporteEfetuado($o_ret->qtdeTransportes);
				array_push($v_lances, $o_lance);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_lances;
	}
	
	public function _listEmail( $idTransp = null, $idPes = null)
	{
		if(!is_null($idTransp))
			$st_query = "SELECT  TQ.*
			FROM tb_lance TQ
			WHERE TQ.tb_transporte_idTransp ='$idTransp'";
		else
			$st_query = "SELECT DISTINCT TQ.*
			FROM tb_lance TQ";
		if(!is_null($idPes))
			if(!is_null($idTransp))
				$st_query .= " and tb_pessoa_idPes = '$idPes'";
			else
				$st_query .= " where tb_pessoa_idPes = '$idPes'";
		$st_query .= " order by TQ.dataLan desc;";
		//echo $st_query;
		//exit;
		$v_lances = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_lance = new LanceModel();
				$o_lance->setIdLan($o_ret->idLan);
				$o_lance->setValorLan($o_ret->valorLan);
				$o_lance->setDataLan($o_ret->dataLan);
				$o_lance->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				$o_lance->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				$o_lance->setTb_pessoa_TransporteEfetuado($o_ret->qtdeTransportes);
				array_push($v_lances, $o_lance);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_lances;
	}
	
	/**
	* Retorna os dados de um lance referente
	* a um determinado Id
	* @param integer $idLan
	* @return ContatoModel
	*/
	public function loadById( $idLan)
	{
		$v_lances = array();
		$st_query = "SELECT * FROM tb_lance WHERE idLan = $idLan;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdLan($o_ret->idLan);
				$this->setValorLan($o_ret->valorLan);
				$this->setDataLan($o_ret->dataLan);
				$this->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);	
				
		return $this;
	}
	
	public function loadByIdTransp( $idTransp)
	{
		$v_lances = array();
		$st_query = "SELECT * FROM tb_lance WHERE tb_transporte_idTransp = $idTransp;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdLan($o_ret->idLan);
				$this->setValorLan($o_ret->valorLan);
				$this->setDataLan($o_ret->dataLan);
				$this->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);	
				
		return $this;
	}
	
	public function loadByVencedor( $idTransp)
	{
		$v_lances = array();
		$st_query = "SELECT * FROM tb_lance WHERE tb_transporte_idTransp = $idTransp AND vencedorLan = 'S';";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
				$this->setIdLan($o_ret->idLan);
				$this->setValorLan($o_ret->valorLan);
				$this->setDataLan($o_ret->dataLan);
				$this->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);	
				
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de lance. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idLan))
			$st_query = "INSERT INTO tb_lance
						(
							
							valorLan,
							dataLan,
							tb_transporte_idTransp,
							tb_pessoa_idPes,
							vencedorLan	
						)
						VALUES
						(
						
							'$this->valorLan',
							NOW(),
							'$this->tb_transporte_idTransp',
							'$this->tb_pessoa_idPes',
							'$this->vencedorLan'	
						);";
		else
			$st_query = "UPDATE
							tb_lance
						SET
							valorLan = '$this->valorLan',
							dataLan = '$this->dataLan',
							tb_transporte_idTransp = '$this->tb_transporte_idTransp',
							tb_pessoa_idPes	= '$this->tb_pessoa_idPes',
							vencedorLan = '$this->vencedorLan'
						WHERE
							idLan = $this->idLan";
		$st_query .= " select @@IDENTITY as id;";
		try
		{
			//$this->o_db->exec($st_query);
			//echo $st_query;
			//exit;
			$objSth = $this->o_db->query($st_query);
			$objSth->nextRowset();
			$rowTd = $objSth->fetch(PDO::FETCH_NUM);
			return $rowTd[0];
				
		}
		catch (PDOException $e)
		{
			throw $e;
		}
		return 0;				
	}

	/**
	* Deleta os dados persistidos na tabela de
	* lance usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idLan))
		{
			$st_query = "DELETE FROM
							tb_lance
						WHERE idLan = $this->idLan";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	public function deleteLanceTransporte($idTransp)
	{
		if(!is_null($idTransp))
		{

			$st_query = "DELETE FROM
							tb_lance
						WHERE tb_transporte_idTransp = $idTransp";
						//echo $st_query;
						//exit;
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	public function aceitar()
	{
			$st_query = "UPDATE
							tb_lance
						SET
							vencedorLan = 'S'
						WHERE
							idLan = $this->idLan";
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
    
    public function mudarStatusLance($idTransp, $idLan)
    {
            $st_query = "UPDATE
                            tb_lance
                        SET
                            vencedorLan = 'N'
                        WHERE
                            tb_transporte_idTransp = $idTransp AND idLan <> $idLan ";
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
?>