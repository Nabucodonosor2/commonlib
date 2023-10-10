<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class header_drop_down_string extends  header_drop_down {
	function header_drop_down_string($field, $field_bd, $nom_header, $sql, $operacion_accumulate='') {
		parent::header_drop_down($field, $field_bd, $nom_header, $sql, $operacion_accumulate);
	}
	function make_filtro() {
		if (strlen($this->valor_filtro)==0)
			return '';
		elseif ($this->valor_filtro=='__NULL__')
			return "(".$this->field_bd." is null) and ";
		else				
			return "(".$this->field_bd." = '".$this->valor_filtro."') and ";		
	}
}
?>