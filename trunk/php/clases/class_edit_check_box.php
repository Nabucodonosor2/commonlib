<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_check_box extends edit_control {
	var $value_true;
	var $value_false;
	var $label;
	
	function edit_check_box($field, $value_true, $value_false, $label='') {
		parent::edit_control($field);
		$this->value_true = $value_true;
		$this->value_false = $value_false;
		$this->label = $label;
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($dato == $this->value_true)
			return '<label><input name="'.$field.'" type="checkbox" id="'.$field.'" value="'.$this->value_true.'" checked="checked" '.$this->make_java_script().'/>'.$this->label.'</label>';
		else
			return '<label><input name="'.$field.'" type="checkbox" id="'.$field.'" value="'.$this->value_true.'" '.$this->make_java_script().'/>'.$this->label.'</label>';
	}
	function draw_no_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($dato == $this->value_true)
			return '<label><input name="'.$field.'" type="checkbox" id="'.$field.'" value="'.$this->value_true.'" checked="checked" disabled="disabled" '.$this->make_java_script().'/>'.$this->label.'</label>';
		else
			return '<label><input name="'.$field.'" type="checkbox" id="'.$field.'" value="'.$this->value_true.'" disabled="disabled" '.$this->make_java_script().'/>'.$this->label.'</label>';
	}
	function get_values_from_POST($record) {
		$field_post = $this->field.'_'.$record;
		if (isset($_POST[$field_post])) 
			return $this->value_true;
		else 
			return $this->value_false;
	}
}
?>