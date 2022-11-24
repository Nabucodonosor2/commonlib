<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class header_date extends  header_output {
	var $valor_filtro2;
	
	function header_date($field, $field_bd, $nom_header, $operacion_accumulate='') {
		parent::header_output($field, $field_bd, $nom_header, $operacion_accumulate);
	}
	function make_java_script() {
		return '"return dlg_find_date(\''.$this->nom_header.'\', \''.$this->valor_filtro.'\', \''.$this->valor_filtro2.'\', this);"';		
	}
	function set_value_filtro($valor_filtro) {
		if ($valor_filtro == '__BORRAR_FILTRO__') {
			$this->valor_filtro = '';
			$this->valor_filtro2 = '';
		}
		else {
			$values = explode("|", $valor_filtro);
			$this->valor_filtro = $values[0];
			$this->valor_filtro2 = $values[1];
		}
	}
	function make_filtro() {
		if (strlen($this->valor_filtro)==0)
			return '';
			
		if ($this->valor_filtro2=='')
			return "(".$this->field_bd." between ".$this->str2date($this->valor_filtro)." and ".$this->str2date($this->valor_filtro, '23:59:59').") and ";
		else		
			return "(".$this->field_bd." between ".$this->str2date($this->valor_filtro)." and ".$this->str2date($this->valor_filtro2, '23:59:59').") and ";
	}
	function make_nom_filtro() {
		if ($this->valor_filtro=='')
			return '';
		
		return $this->nom_header.": ".$this->valor_filtro." a ".$this->valor_filtro2;
	}	
}
?>