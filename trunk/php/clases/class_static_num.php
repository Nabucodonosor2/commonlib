<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class static_num extends static_text {
	var $num_dec;

	function static_num($field, $num_dec=0) {
		parent::static_text($field);
		$this->num_dec = $num_dec;
	}
	function draw_no_entrable($dato, $record) {
		if ($this->type == 'hidden')
			return '';
		return static_num::draw_entrable($dato, $record);
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		
		if ($dato!='') {
			if (defined('K_CLIENTE') && $this->field=='RUT' && K_CLIENTE=='UTEM')
				$dato = $dato;
			else 
				$dato = number_format($dato, $this->num_dec, ',', '.');
		}
			
		if ($this->type == 'hidden')
			$ctrl = '<label id="'.$field.'" style="display:none">'.$dato.'</label>';
		else
			$ctrl = '<label id="'.$field.'" '.$this->make_java_script().'>'.$dato.'</label>';		
		return $ctrl;
	}
}
?>