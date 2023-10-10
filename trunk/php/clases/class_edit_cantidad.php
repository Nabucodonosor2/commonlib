<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_cantidad extends edit_num {
	function edit_cantidad($field, $size = 12, $maxlen = 12) {
		parent::edit_num($field, $size, $maxlen, 1, true);
	}
}
?>