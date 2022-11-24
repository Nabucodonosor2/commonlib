<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_ano extends edit_num {
	function edit_ano($field) {
		parent::edit_num($field, 4, 4);
		$this->con_separador_miles = false;		
	}
}
?>