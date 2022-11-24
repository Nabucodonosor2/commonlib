<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_text_multiline extends edit_text {
	var $cols;
	var $rows;
	
	function edit_text_multiline($field, $cols, $rows) {
		parent::edit_control($field);
		$this->cols = $cols;
		$this->rows = $rows;
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		$ctrl = '<textarea name="'.$field.'" id="'.$field.'" cols="'.$this->cols.'" rows="'.$this->rows.'">'.$dato.'</textarea>';			
		return $ctrl;
	}
	function draw_no_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		$ctrl = '<textarea name="'.$field.'" id="'.$field.'" cols="'.$this->cols.'" rows="'.$this->rows.'" disabled="disabled">'.$dato.'</textarea>';			
		return $ctrl;
	}
}
?>