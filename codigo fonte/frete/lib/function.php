<?php
	function remover_acentos($string) {
		$string = preg_replace("/[áàâãä]/", "a", $string);
		$string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
		$string = preg_replace("/[éèê]/", "e", $string);
		$string = preg_replace("/[ÉÈÊ]/", "E", $string);
		$string = preg_replace("/[íì]/", "i", $string);
		$string = preg_replace("/[ÍÌ]/", "I", $string);
		$string = preg_replace("/[óòôõö]/", "o", $string);
		$string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
		$string = preg_replace("/[úùü]/", "u", $string);
		$string = preg_replace("/[ÚÙÜ]/", "U", $string);
		$string = preg_replace("/ç/", "c", $string);
		$string = preg_replace("/Ç/", "C", $string);
		$string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
		$string = preg_replace("/ /", "_", $string);
		return $string;
	}
    
      function protecao($string){
      $string = str_replace(" or ", "", $string);
      $string = str_replace("select ", "", $string);
      $string = str_replace("delete ", "", $string);
      $string = str_replace("create ", "", $string);
      $string = str_replace("drop ", "", $string);
      $string = str_replace("update ", "", $string);
      $string = str_replace("drop table", "", $string);
      $string = str_replace("show table", "", $string);
      $string = str_replace("applet", "", $string);
      $string = str_replace("object", "", $string);
      $string = str_replace("'", "", $string);
      $string = str_replace("#", "", $string);
      $string = str_replace("=", "", $string);
      $string = str_replace("--", "", $string);
      $string = str_replace("-", "", $string);
      $string = str_replace(";", "", $string);
      $string = str_replace("*", "", $string);
      $string = strip_tags($string);
    
      return $string;
    }
?>