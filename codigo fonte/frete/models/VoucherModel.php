<?php
require_once 'models/VoucherModel.php';


class VoucherModel extends PersistModelAbstract
{
      private $idVou;
	  private $codigoVou;
	  private $dataValidadeVou;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	/**
	 * Setters e Getters da
	 * classe CidadeModel
	 */
	
	public function setIdVou( $idVou )
	{
		$this->idVou = $idVou;
		return $this;
	}
	
	public function getIdVou()
	{
		return $this->idVou;
	}
	
	public function setCodigoVou( $codigoVou )
	{
		$this->codigoVou = $codigoVou;
		return $this;
	}
	
	public function getCodigoVou()
	{
		return $this->codigoVou;
	}
	
	public function setDataValidadeVou( $dataValidadeVou )
	{
		$this->dataValidadeVou = $dataValidadeVou;
		return $this;
	}
	
	public function getDataValidadeVou()
	{
		return $this->dataValidadeVou;
	}

	/**
	* Retorna um array contendo os Status
	* @param string $IdCid
	* @return Array
	*/
	public function _list( $codigoVou = null )
	{	
		if(!is_null($codigoVou))
			$st_query = "SELECT * FROM tb_voucher WHERE codigoVou = UPPER('$codigoVou');";
		else
			$st_query = 'SELECT * FROM tb_voucher;';	
		
		$v_vouchers = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_voucher = new VoucherModel();
				$o_voucher->setIdVou($o_ret->idVou);
				$o_voucher->setCodigoVou($o_ret->codigoVou);
				$o_voucher->setDataValidadeVou($o_ret->dataValidadeVou);
				array_push($v_vouchers, $o_voucher);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_vouchers;
	}
	
	public function loadById( $idVou )
	{
		$v_vouchers = array();
		$st_query = "SELECT * FROM tb_voucher WHERE idVou = $idVou;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdVou($o_ret->idVou);
		$this->setCodigoVou($o_ret->codigoVou);
		$this->setDataValidadeVou($o_ret->dataValidadeVou);
		return $this;
	}
    
    public function loadByCodigo( $codigoVou )
    {
        $v_vouchers = array();
        $st_query = "SELECT * FROM tb_voucher WHERE codigoVou = UPPER('$codigoVou');";
        $o_data = $this->o_db->query($st_query);
        $o_ret = $o_data->fetchObject();
        $this->setIdVou($o_ret->idVou);
        $this->setCodigoVou($o_ret->codigoVou);
        $this->setDataValidadeVou($o_ret->dataValidadeVou);
        return $this;
    }

    
	public function save()
	{
		if(is_null($this->idVou))
			$st_query = "INSERT INTO tb_voucher
						(
								codigoVou,
								dataValidadeVou
						)
						VALUES
						(
								'$this->codigoVou',
								'$this->dataValidadeVou'
								
						);";
		else
			$st_query = "UPDATE
							tb_voucher
						SET
								codigoVou = '$this->codigoVou',
								dataValidadeVou = '$this->dataValidadeVou'
						WHERE
							idVou = $this->idVou;";
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
	* cidade usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->idVou))
		{
			$st_query = "DELETE FROM
							tb_voucher
						WHERE idVou = $this->idVou";
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