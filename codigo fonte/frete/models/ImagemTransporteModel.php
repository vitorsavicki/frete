<?php
require_once 'models/ImagemTransporteModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - ImagemTransporteModel
*
* Responsável por gerenciar e persistir os dados dos  
*Imagens do Transporte 
**/
class ImagemTransporteModel extends PersistModelAbstract
{
	private $idImgTran;
  	private $caminhoImgTran;
	private $tb_transporte_idTransp;
	
	
	function __construct()
	{
		parent::__construct();
		;
	}
	
	
	/**
	 * Setters e Getters da
	 * classe ImagemTransporteModel
	 */
	
	public function setIdImgTran( $idImgTran )
	{
		$this->idImgTran = $idImgTran;
		return $this;
	}
	
	public function getIdImgTran()
	{
		return $this->idImgTran;
	}
	
	public function setCaminhoImgTran( $caminhoImgTran )
	{
		$this->caminhoImgTran = $caminhoImgTran;
		return $this;
	}
	
	public function getCaminhoImgTran()
	{
		return $this->caminhoImgTran;
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

	/**
	* Retorna um array contendo os imagens do transporte
	* @param string $idImgTran
	* @return Array
	*/
	
	public function _list( $tb_transporte_idTransp = null)
	{
		if(!is_null($tb_transporte_idTransp))
			$st_query = "SELECT * FROM tb_imagens_transporte WHERE tb_transporte_idTransp = $tb_transporte_idTransp;";
		else
			$st_query = 'SELECT * FROM tb_imagens_transporte;';	
		
		$v_imagens = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_imagens = new ImagemTransporteModel();
				$o_imagens->setIdImgTran($o_ret->idImgTran);
				$o_imagens->setCaminhoImgTran($o_ret->caminhoImgTran);
				$o_imagens->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				array_push($v_imagens, $o_imagens);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_imagens;
	}
	
	public function _listNome( $nome = null)
	{
		if(!is_null($nome))
			$st_query = "SELECT * FROM tb_imagens_transporte WHERE caminhoImg = $nome;";
		else
			$st_query = 'SELECT * FROM tb_imagens_transporte;';	
		
		$v_imagens = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_imagens = new ImagemTransporteModel();
				$o_imagens->setIdImgTran($o_ret->idImgTran);
				$o_imagens->setCaminhoImgTran($o_ret->caminhoImgTran);
				$o_imagens->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				array_push($v_imagens, $o_imagens);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_imagens;
	}
        
    public function loadByIdTop( $idTransp = null)
    {
        $v_imagens = array();
        $st_query = "SELECT caminhoImgTran FROM tb_imagens_transporte WHERE tb_transporte_idTransp = $idTransp LIMIT 1;";
        //echo  $st_query;
        //exit;
        $o_data = $this->o_db->query($st_query);
        $o_ret = $o_data->fetchObject();
        if( ! isset($o_ret->caminhoImgTran)){
        
        
            return FALSE;
        }
        else{
        $this->setCaminhoImgTran($o_ret->caminhoImgTran);
                
        return $this;
        }
    }
    
	/**
	* Retorna os dados de uma iamgem do transporte referente
	* a um determinado Id
	* @param integer $idImgTran
	* @return ImagemTransporteModel
	*/
	public function loadById( $idImgTran )
	{
		$v_imagens = array();
		$st_query = "SELECT * FROM tb_imagens_transporte WHERE idImgTran = $idImgTran;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdImgTran($o_ret->idImgTran);
		$this->setCaminhoImgTran($o_ret->caminhoImgTran);
		$this->setTb_transporte_idTransp($o_ret->tb_transporte_idTransp);
				
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de imagens do transporte. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idImgTran))
			$st_query = "INSERT INTO tb_imagens_transporte
						(
								caminhoImgTran,
							    tb_transporte_idTransp
						)
						VALUES
						(
								'$this->caminhoImgTran',
							  	'$this->tb_transporte_idTransp'
						);";
		else
			$st_query = "UPDATE
							tb_imagens_transporte
						SET
								caminhoImgTran = '$this->caminhoImgTran',
								tb_transporte_idTransp = '$this->tb_transporte_idTransp'
						WHERE
							idImgTran = $this->idImgTran";
		try
		{
			//echo $st_query.'<br><br>';
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
	* iamgens do transporte usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idImgTran))
		{
			$st_query = "DELETE FROM
							tb_imagens_transporte
						WHERE idImgTran = $this->idImgTran";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

}
?>