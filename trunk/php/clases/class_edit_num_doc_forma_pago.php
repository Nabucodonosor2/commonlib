<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_num_doc_forma_pago extends edit_text_java {
	function edit_num_forma_pago($field) {
		parent::edit_text_java($field, 2, 2, 'text');
		$this->set_onKeyPress('return onlyNumbers(this, event,0,true);');
	}	
}
?>