<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_password extends edit_text {
	function edit_password($field, $size, $maxlen) {
		parent::edit_text($field, $size, $maxlen, 'password');
	}
	function draw_no_entrable($dato, $record) {	
		return '•••••••••';
	}
}
?>