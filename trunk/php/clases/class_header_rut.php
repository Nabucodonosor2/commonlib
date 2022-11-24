<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class header_rut extends  header_text {
	var $tabla;

	function header_rut($field, $tabla) {
		/*
		 En vez de guardar field_db, se guarda la tabla o alias de la tabla para armar la condicion correspondiente para el where
		 ejemplo
		 select E.RUT
		 ....
		 from EMPRESA E
		 where ....

		 se debe usar
		 header_rut('RUT', 'E', 'Rut')
		 */
		parent::header_text($field, $tabla.'.'.$field, 'Rut');
		$this->tabla = $tabla;
	}
	function make_filtro() {
		if (strlen($this->valor_filtro)==0)
			return '';

		$valor = str_replace(".", "", $this->valor_filtro);
		$valor = str_replace(" ", "", $valor);
		$pos = strpos($valor, '-');
		if ($pos===false) {
			$rut = $valor;
			$dig_verif = '';
		}
		else {
			$valores = explode('-', $valor);
			$rut = $valores[0];
			$dig_verif = $valores[1];
		}

		// arma la busqueda
		if (K_TIPO_BD=="oci"){
			if ($dig_verif==''){
				return "(ltrim(rtrim(".$this->tabla.".RUT)) || ".$this->tabla.".DIG_VERIF like '%".$rut."%') and ";
			}
			else{
				return "(".$this->tabla.".RUT like '%".$rut."%') and (".$this->tabla.".DIG_VERIF='".$dig_verif."') and ";
			}

		}
		else if (K_TIPO_BD=="mssql"){
			if ($dig_verif==''){
				return "(ltrim(rtrim(CAST(".$this->tabla.".RUT as char(10)))) + ".$this->tabla.".DIG_VERIF like '%".$rut."%') and ";
			}
			else{
				return "(CAST(".$this->tabla.".RUT as char(10))like '%".$rut."%') and (".$this->tabla.".DIG_VERIF='".$dig_verif."') and ";
			}
		}
	}
}
?>