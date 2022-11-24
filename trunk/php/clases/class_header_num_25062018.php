<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class header_num extends  header_output {
	public	$valor_filtro2='';
	private	$cant_decimal;
	private	$solo_positivos;
	
	function header_num($field, $field_bd, $nom_header, $cant_decimal=0, $solo_positivos=true, $operacion_accumulate='') {
		parent::header_output($field, $field_bd, $nom_header, $operacion_accumulate);
		$this->cant_decimal = $cant_decimal;
		$this->solo_positivos = $solo_positivos;
	}
	function make_java_script() {
		$solo_positivos = $this->solo_positivos ? 1 : 0;
		return '"return dlg_find_num(\''.$this->nom_header.'\', \''.$this->valor_filtro.'\', \''.$this->valor_filtro2.'\', '.$this->cant_decimal.', '.$solo_positivos.', this);"';		
	}
	function set_value_filtro($valor_filtro) {
		if ($valor_filtro == '__BORRAR_FILTRO__') {
			$this->valor_filtro = '';
			$this->valor_filtro2 = '';
		}
		else {
			$values = explode("|", $valor_filtro);
			$this->valor_filtro = str_replace(",", ".", $values[0]);
			$this->valor_filtro2 = str_replace(",", ".", $values[1]);
		}
	}
	function make_filtro() {
		if (strlen($this->valor_filtro)==0)
			return '';
			
		if ($this->valor_filtro2=='')
			return "(".$this->field_bd." = ".$this->valor_filtro.") and ";		
		else		
			return "(".$this->field_bd." between ".$this->valor_filtro." and ".$this->valor_filtro2.") and ";		
	}
	function make_nom_filtro() {
		if ($this->valor_filtro=='')
			return '';
		
		if ($this->valor_filtro2=='')
			return $this->nom_header.": ".$this->valor_filtro;
		else
			return $this->nom_header.": ".$this->valor_filtro." a ".$this->valor_filtro2;
	}	
}
?>