<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_dig_verif extends edit_text {
	private $name_rut;
	
	function edit_dig_verif($field, $name_rut = 'RUT') {
		parent::edit_text($field, 1, 1, 'text');
		////modificaciones supervisadas por VM. para IExplorer.
		$this->name_rut = $name_rut;
		$this->set_onChange("valida_digito(this, '".$this->name_rut."'); this.focus();");
		
		$this->set_onKeyPress("return filter_edit_dig_verif(this, event)");
		$this->style = "text-transform: uppercase";		
	}
}
?>