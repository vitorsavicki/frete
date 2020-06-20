<?php
require_once 'models/ItemModel.php';
require_once 'models/CategoriaModel.php';
/**

*  
* Camada - Modelo ou Model.
* Diretório Pai - models  
* Arquivo - ItemModel
*
* Responsável por gerenciar e persistir os dados dos  
* itens
**/
class ItemModel extends PersistModelAbstract
{
	private $idItem;
  	private $nomeItem;
  	private $tb_categoria_idCat;
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	
	/**
	 * Setters e Getters da
	 * classe ItemModel
	 */
	
	public function setIdItem( $idItem )
	{
		$this->idItem = $idItem;
		return $this;
	}
	
	public function getIdItem()
	{
		return $this->idItem;
	}
	
	public function setNomeItem( $nomeItem )
	{
		$this->nomeItem = $nomeItem;
		return $this;
	}
	
	public function getNomeItem()
	{
		return $this->nomeItem;
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
	
	/**
	* Retorna um array contendo os contatos
	* @param string $idItem
	* @return Array
	*/
	public function _list( $nomeItem = null, $categoria = null )
	{
		$st_query = "SELECT * FROM tb_item inner join tb_categoria on tb_categoria_idCat = idCat";
		$st_where = null;
		if(!is_null($nomeItem))
			$st_where = "WHERE nomeItem LIKE '%$nomeItem%';";
		if(!is_null($categoria))
			if(!is_null($st_where))
				$st_where .= " AND idCat = '$categoria' ";
			else
				$st_where = " where idCat = '$categoria' ";
		$st_query .= $st_where . ";";
		$v_itens = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_item = new ItemModel();
				$o_item->setIdItem($o_ret->idItem);
				$o_item->setNomeItem($o_ret->nomeItem);
				$o_item->setTb_categoria_idCat($o_ret->tb_categoria_idCat);
				array_push($v_itens, $o_item);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_itens;
	}
	
	/**
	* Retorna os dados de um item referente
	* a um determinado Id
	* @param integer $idItem
	* @return ItemModel
	*/
	public function loadById( $idItem )
	{
		$v_itens = array();
		$st_query = "SELECT * FROM tb_item WHERE idItem = $idItem;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdItem($o_ret->idItem);
		$this->setNomeItem($o_ret->nomeItem);
		$this->setTb_categoria_idCat($o_ret->tb_categoria_idCat);		
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
		if(is_null($this->idItem))
			$st_query = "INSERT INTO tb_item
						(
							
							  	nomeItem,
							  	tb_categoria_idCat
						)
						VALUES
						(
							'$this->nomeItem',
							'$this->tb_categoria_idCat'
						);";
		else
			$st_query = "UPDATE
							tb_item
						SET
							nomeItem = '$this->nomeItem',
							tb_categoria_idCat = '$this->tb_categoria_idCat'
						WHERE
							idItem = $this->idItem";
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
	* item usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idItem))
		{
			$st_query = "DELETE FROM
							tb_item
						WHERE idItem = $this->idItem";
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