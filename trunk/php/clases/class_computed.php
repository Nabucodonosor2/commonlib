<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class computed extends static_num {
	var $edit_hidden;

	function computed($field, $num_dec=0) {
		parent::static_num($field, $num_dec);
		$this->edit_hidden = new edit_text($field.'_H', 100, 100, 'hidden');
		$this->edit_hidden->forzar_js = true;		// Si existe js, se agrega al html aunque este hidden 
		$this->have_POST = true;
	}
	function draw_entrable($dato, $record) {
		$ctrl = parent::draw_entrable($dato, $record);
		$ctrl .= $this->edit_hidden->draw_entrable($dato, $record);
		return $ctrl;
	}
	function get_values_from_POST($record) {
		// Obtiene el valor desde el hidden
		$field_post = $this->field.'_H_'.$record;
		return $_POST[$field_post];
	}
}
?>