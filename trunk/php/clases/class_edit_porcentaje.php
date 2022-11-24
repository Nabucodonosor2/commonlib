<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_porcentaje extends edit_num {
	function edit_porcentaje($field, $size = 6, $maxlen = 6, $num_dec=1) {
		parent::edit_num($field, $size, $maxlen, $num_dec, true);
	}
}
?>