<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_rut extends edit_num {
	private $name_dig_verif;
	
	function edit_rut($field, $size = 10, $maxlen = 10, $name_dig_verif = 'DIG_VERIF') {
		parent::edit_num($field, $size, $maxlen, 0, true);
		$this->name_dig_verif = $name_dig_verif;
		$this->set_onChange("clear_dig_verif(this, '".$this->name_dig_verif."');");

		if (defined('K_CLIENTE') && K_CLIENTE=='UTEM')
			$this->con_separador_miles = false; 
	}
}
?>