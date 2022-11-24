<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class header_text extends  header_output {
	function header_text($field, $field_bd, $nom_header, $operacion_accumulate='') {
		parent::header_output($field, $field_bd, $nom_header, $operacion_accumulate);
	}
	function make_java_script() {
		return '"return dlg_find_text(\''.$this->nom_header.'\', \''.$this->valor_filtro.'\', this);"';		
	}
	function make_filtro() {
		if (strlen($this->valor_filtro)==0)
			return '';
			
		return "(Upper(".$this->field_bd.") COLLATE SQL_LATIN1_GENERAL_CP1_CI_AI like '%".strtoupper($this->valor_filtro)."%') and ";		
	}
	function make_nom_filtro() {
		if ($this->valor_filtro=='')
			return '';
		
		return $this->nom_header.": ".$this->valor_filtro;
	}	
}
?>