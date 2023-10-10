<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_text_upper extends edit_text {
	function edit_text_upper($field, $size, $maxlen, $type='text') {
		parent::edit_text($field, $size, $maxlen, $type);
		$this->style = "text-transform: uppercase";
	}
	function get_values_from_POST($record) {
		$value = parent::get_values_from_POST($record);
		return strtoupper($value);
	}
}
?>