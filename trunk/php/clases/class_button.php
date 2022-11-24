<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class button extends edit_control {
	var $habilitado_en_modo_lectura = false;
	var $name = 'botón';
	var $visible = true;	// deberia ser una caracteristica de edit_control !!
	
	function button($field, $name) {
		parent::edit_control($field);
		$this->name = $name;
		$this->class = 'BUTTON';
		$this->onBlur = ""; 
		$this->onFocus = "";	
		$this->have_POST = false;	// no se para que pueda usarse aun
	}
	function set_visible($visible) {
		$this->visible = $visible;
	}
	function draw_entrable($dato=null, $record=null) {
		if (!$this->visible)
			return '';
			
		$ctrl = '<input type="button" class="Button" ';
		$id = ' id="BTN_'.$this->field.'"';
		$ctrl .= $id;
		$name = ' value="'.$this->name.'"';
		$ctrl .= $name;
		$java = $this->make_java_script();
		$ctrl .= $java;
		$ctrl .= '>'; 
		return $ctrl;
	}
	function draw_no_entrable($dato=null, $record=null) {
		if (!$this->visible)
			return '';
			
		$ctrl = $this->draw_entrable();
		$ctrl = substr($ctrl, 0, strlen($ctrl)-1);		// borra el ultimo caracter ">"
		$ctrl .= ' disabled="disabled"';
		$ctrl .= '>'; 
		return $ctrl;
	}
}
?>