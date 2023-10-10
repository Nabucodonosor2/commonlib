<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_radio_button extends edit_control {
	var $group;
	var $value_true;
	var $value_false;
	var $label;
	
	function edit_radio_button($field, $value_true, $value_false, $label='', $group='') {
		parent::edit_control($field);
		$this->value_true = $value_true;
		$this->value_false = $value_false;
		$this->label = $label;
		$this->group = $group;
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->group == '')
			$group = $field;
		else
			$group = $this->group;
		if ($dato == $this->value_true)
			 $ctrl = '<label><input name="'.$group.'" type="radio" id="'.$field.'" value="'.$this->value_true.'" checked="checked" '.$this->make_java_script().'/>'.$this->label.'</label>';
		else
			 $ctrl = '<label><input name="'.$group.'" type="radio" id="'.$field.'" value="'.$this->value_true.'" '.$this->make_java_script().'/>'.$this->label.'</label>';

		return $ctrl;
	}
	function draw_no_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->group == '')
			$group = $field;
		else
			$group = $this->group;
		if ($dato == $this->value_true)
			return '<label><input name="'.$group.'" type="radio" id="'.$field.'" value="'.$this->value_true.'" checked="checked" disabled="disabled"/>'.$this->label.'</label>';
		else
			return '<label><input name="'.$group.'" type="radio" id="'.$field.'" value="'.$this->value_true.'" disabled="disabled"/>'.$this->label.'</label>';
	}
	function get_values_from_POST($record) {
		if ($this->group == '')
			$field_post = $this->field.'_'.$record;
		else
			$field_post = $this->group;
		if (isset($_POST[$field_post]) && $_POST[$field_post]==$this->value_true)
			return $this->value_true; 	
		else 
			return $this->value_false;
	}
}
?>