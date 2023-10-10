<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_mail extends edit_text {
	function edit_mail($field, $size = 60, $maxlen = 60) {
		parent::edit_text($field, $size, $maxlen, 'text');
		$this->onKeyUp = '"this.value = filter_edit_mail(this.value);" ';
		
		// NOTA: en el validate de3l form se debe volver a llamar a validate_mail() para
		// asegurar que no ingrese un valor malo
		$this->set_onChange("validate_mail(this);");
	}
}
?>