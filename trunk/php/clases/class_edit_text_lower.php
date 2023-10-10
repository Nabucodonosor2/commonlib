<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_text_lower extends edit_text {
	function edit_text_lower($field, $size, $maxlen, $type='text') {
		parent::edit_text($field, $size, $maxlen, $type);
		$this->style = "text-transform: lowercase";
	}
	function get_values_from_POST($record) {
		$value = parent::get_values_from_POST($record);
		return strtolower($value);
	}
}
?>