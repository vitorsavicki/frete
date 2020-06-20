<?php
require_once 'models/PessoaVoucherModel.php';

class PessoaVoucherModel extends PersistModelAbstract
{
      private $idPesVou;
	  private $tb_voucher_idVou;
	  private $tb_pessoa_idPes;
	
	
	function __construct()
	{
		parent::__construct();
	
	}
	
	
	public function setIdPesVou( $idPesVou )
	{
		$this->idPesVou = $idPesVou;
		return $this;
	}
	
	public function getIdPesVou()
	{
		return $this->idPesVou;
	}
	
	public function setTb_voucher_idVou( $tb_voucher_idVou )
	{
		$this->tb_voucher_idVou = $tb_voucher_idVou;
		return $this;
	}
	
	public function getTb_voucher_idVou()
	{
		return $this->tb_voucher_idVou;
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


	public function _list( $codigoVou = null )
	{	
		if(!is_null($nomeSta))
			$st_query = "SELECT * FROM tb_pessoa_voucher
			INNER JOIN tb_voucher ON tb_voucher.idVou = tb_pessoa_voucher.tb_voucher_idVou
			 WHERE codigoVou = $codigoVou;";
		else
			$st_query = "SELECT * FROM tb_pessoa_voucher
            INNER JOIN tb_voucher ON tb_voucher.idVou = tb_pessoa_voucher.tb_voucher_idVou;";	
		
		$v_pessoa_voucher = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_pessoa_voucher = new PessoaVoucherModel();
				$o_pessoa_voucher->setIdPesVou($o_ret->idPesVou);
				$o_pessoa_voucher->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
				$o_pessoa_voucher->setTb_voucher_idVou($o_ret->tb_voucher_idVou);
				array_push($v_pessoa_voucher, $o_pessoa_voucher);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_v_pessoa_voucher;
	}
	

	public function loadById( $idPesVou )
	{
		$v_pessoa_voucher = array();
		$st_query = "SELECT * FROM tb_pessoa_voucher WHERE idPesVou = $idPesVou;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setIdPesVou($o_ret->idPesVou);
		$this->setTb_pessoa_idPes($o_ret->tb_pessoa_idPes);
		$this->setTb_voucher_idVou($o_ret->tb_voucher_idVou);
		return $this;
	}
	
	public function save()
	{
		if(is_null($this->idPesVou))
			$st_query = "INSERT INTO tb_pessoa_voucher
						(
								tb_pessoa_idPes,
								tb_voucher_idVou
						)
						VALUES
						(
								'$this->tb_pessoa_idPes',
								'$this->tb_voucher_idVou'
								
						);";
		else
			$st_query = "UPDATE
							tb_pessoa_voucher
						SET
								tb_pessoa_idPes  = '$this->tb_pessoa_idPes',
								tb_voucher_idVou = '$this->tb_voucher_idVou'
						WHERE
							idPesVou = $this->idPesVou;";
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
		if(!is_null($this->idPesVou))
		{
			$st_query = "DELETE FROM
							tb_pessoa_voucher
						WHERE idPesVou = $this->idPesVou";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
}
?>