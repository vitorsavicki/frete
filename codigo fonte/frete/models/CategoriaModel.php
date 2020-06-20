<?php
require_once 'models/CategoriaModel.php';


/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - CategoriaModel
*
* Responsável por gerenciar e persistir os dados das  
* Categorias  
**/
class CategoriaModel extends PersistModelAbstract
{
	private $idCat;
  	private $nomeCat;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe CategoriaModel
	 */
	
	public function setIdCat( $idCat )
	{
		$this->idCat = $idCat;
		return $this;
	}
	
	public function getIdCat()
	{
		return $this->idCat;
	}
	
	public function setNomeCat( $nomeCat)
	{
		$this->nomeCat = $nomeCat;
		return $this;
	}
	
	public function getNomeCat()
	{
		return $this->nomeCat;
	}
	

	
	/**
	* Retorna um array contendo as categorias
	* @param string $idCat
	* @return Array
	*/
	public function _list( $idCat = null )
	{
		if(!is_null($idCat))
			$st_query = "SELECT * FROM tb_categoria WHERE idCat =  '$idCat';";
		else
			$st_query = 'SELECT * FROM tb_categoria;';	
		
		$v_categorias = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_categoria = new CategoriaModel();
				$o_categoria->setIdCat($o_ret->idCat);
				$o_categoria->setNomeCat($o_ret->nomeCat);
				array_push($v_categorias, $o_categoria);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_categorias;
	}
	
	/**
	* Retorna os dados de uma categoria referente
	* a um determinado Id
	* @param integer $idCat
	* @return CategoriaModel
	*/
	public function loadById( $idCat )
	{
		$v_categorias = array();
		$st_query = "SELECT * FROM tb_categoria WHERE idCat = $idCat;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdCat($o_ret->idCat);
		$this->setNomeCat($o_ret->nomeCat);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de categoria. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->idCat))
			$st_query = "INSERT INTO tb_categoria
						(
  							nomeCat
						)
						VALUES
						(
							'$this->nomeCat'
						);";
		else
			$st_query = "UPDATE
							tb_categoria
						SET
							nomeCat = '$this->nomeCat'
						
						WHERE
							idCat = $this->idCat";
		try
		{
			echo $st_query;
			$this->o_db->exec($st_query);
			echo $st_query;
		}
		catch (PDOException $e)
		{
			throw $e;
		}
		return false;				
	}

	/**
	* Deleta os dados persistidos na tabela de
	* categoria usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idCat))
		{
			$st_query = "DELETE FROM
							tb_categoria
						WHERE idCat = $this->idCat";
			 try
        {
            
            if($this->o_db->exec($st_query) > 0){
                return true;
                
                
            }
        }
        
      catch (PDOException $e) {
           if (isset($e->errorInfo[1]) && $e->errorInfo[1] == '1451') {
              return false;
           }
        }
		}
	}
	
}
?>