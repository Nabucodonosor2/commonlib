<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_precio extends edit_num {
	function edit_precio($field, $size = 16, $maxlen = 16, $readonly=false) {
		parent::edit_num($field, $size, $maxlen, 0, true, $readonly);
	}
}
?>