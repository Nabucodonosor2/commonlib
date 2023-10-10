<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class drop_down_dw extends drop_down_list {
		/*
		 En el select con que se construye el drop down, puede tener 3 campos, siendo el 3ro opcional
		CAMPO1 = codigo, este queda en el value del "option"
		CAMPO2 = descripción, este queda en el innerHTML del "option"
		CAMPO3 = data adicional, opcional, este queda en el label del "option"
		ej.
			select  COD_FORMA_PAGO
					NOM_FORMA_PAGO		
					CANT_DOC
			from FORMA_PAGO
			order by ORDEN

		supongamos que COD_FORMA_PAGO=3, NOM_FORMA_PAGO='dos cheques', CANT_DOC = 2
		<option label="3" value=2>dos cheques</option>
		 */
	var $sql;
	var $add_vacio = true;	// TRUE si se agrega el campo vacio al comienzo en un retrieve
	
	function drop_down_dw($field, $sql, $width_px=0, $drop_down_dependiente='', $add_vacio=true) {
		$this->sql = $sql;
		$this->add_vacio = $add_vacio;
		
		parent::drop_down_list($field, array(), array(), $width_px, $drop_down_dependiente);
		if (strpos($this->sql, '{KEY1}')===false)
			$this->retrieve();
	}
	function set_sql($sql) {
		$this->sql = $sql;
	}
	function get_sql() {
		return $this->sql;
	}
		
	function retrieve() {
		$keys = func_get_args();
		$sql = $this->replace_KEYS($this->sql, $keys);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql);
		$aValues = array();
		$aLabels = array();
		$aData_adicional = array();
		if ($this->add_vacio) {
			$aValues[] = '';
			$aLabels[] = '';
			$aData_adicional[] = '';
		}
		for($i=0; $i<count($result); $i++) {
			$aValues[] = $result[$i][0];
			$aLabels[] = $result[$i][1];
			if (isset($result[$i][2]))	// la data adcional no es obligatoria
				$aData_adicional[] = $result[$i][2];
		}
		$this->aValues = $aValues;
		$this->aLabels = $aLabels;
		$this->aData_adicional = $aData_adicional;
		
	}
}
?>