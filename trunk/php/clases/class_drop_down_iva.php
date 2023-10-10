<?php
require_once(dirname(__FILE__)."/../auto_load.php");
class drop_down_iva extends drop_down_list{
	function drop_down_iva($cod_parametro = 1) {
		$porc_iva = $this->get_parametro($cod_parametro);
		$porc_iva = number_format($porc_iva, 1, ',', '.');
		parent::drop_down_list('PORC_IVA',array($porc_iva,0),array($porc_iva,'0'),52);
	}
	function get_values_from_POST($record) {
		// Cambia la coma decimal por punto
		$value = parent::get_values_from_POST($record);
		$value = str_replace(',', '.', $value);
		return $value;
	}
}
?>