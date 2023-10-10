<?php
require_once(dirname(__FILE__)."/../auto_load.php");
//se creo nueva clase para poder verificar solo en los output, que se ingresen solo numeros
class edit_num_header extends edit_text {
	var $num_dec;
	var $solo_positivos;
	var $con_separador_miles;
	
	function edit_num_header($field, $size = 16, $maxlen = 16, $num_dec=0, $solo_positivos = true, $readonly=false, $con_separador_miles=true) {
		parent::edit_text($field, $size, $maxlen, $type='text', $readonly);
		$this->num_dec = $num_dec;
		$this->solo_positivos = $solo_positivos;
		$this->con_separador_miles = $con_separador_miles;
		$this->set_onKeyPress('return onlyNumbers(this, event,'.$this->num_dec.', '.($this->solo_positivos ? 1 : 0).');');
		$this->set_onChange("compruebanumeros(this)");
		$this->class = 'input_num';
	}
	function set_num_dec($num_dec) {
		$this->num_dec = $num_dec;
		$this->set_onKeyPress('return onlyNumbers(this, event,'.$this->num_dec.', '.($this->solo_positivos ? 1 : 0).');');
	}
	function draw_entrable($dato, $record) {
		if ($dato!='')
			$dato = number_format($dato, $this->num_dec, ',', '');
		return parent::draw_entrable($dato, $record);
	}
	function draw_no_entrable($dato, $record) {
		if ($dato!='') {
			if ($this->con_separador_miles)
				$dato = number_format($dato, $this->num_dec, ',', '.');
			else
				$dato = number_format($dato, $this->num_dec, ',', '');
		}
		return parent::draw_no_entrable($dato, $record);
	}
	function get_values_from_POST($record) {
		$field_post = $this->field.'_'.$record;
		$value_post = $_POST[$field_post];
		$value_post = str_replace(",", ".", $value_post);								
		return $value_post;
	}
}
?>