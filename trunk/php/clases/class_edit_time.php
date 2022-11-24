<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_time extends edit_text {
	function edit_time($field) {
		parent::edit_text($field, 5, 5, 'text');
		$this->set_onKeyUp("this.value = filter_edit_time(this.value);");
	}
	function validate($valor) {
		$time = explode(':', $valor);
		$hora = isset($time[0]) ? $time[0] : 0;
		if ($hora =='') $hora = 0;
		$min = isset($time[1]) ? $time[1] : 0; 
		if ($min =='') $min = 0;
		
		if (is_numeric($hora) && is_numeric($min)) {
			if ($hora >=0 && $hora <= 23 && $min >= 0 && $min <= 59)
				return '';		// no error
		}
		
		return "La hora es invalida: $hora:$min ";
	}
	function get_values_from_POST($record) {
		// Se asegura que el dato sea retornado en formato "hh:mm"
		$field_post = $this->field.'_'.$record;
		$valor = $_POST[$field_post];
		
		// separa en hora minuto
		$time = explode(':', $valor);
		$hora = isset($time[0]) ? $time[0] : 0;
		if ($hora =='') $hora = 0;
		$min = isset($time[1]) ? $time[1] : 0; 
		if ($min =='') $min = 0;
		
		$valor = sprintf("%02d:%02d", $hora, $min);
		return $valor;
	}
}
?>