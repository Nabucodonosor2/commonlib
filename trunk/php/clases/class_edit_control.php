<?php
require_once(dirname(__FILE__)."/../auto_load.php");

abstract class edit_control extends base {
	var $field;
	var	$onKeyUp = '';
	var	$onKeyDown = '';
	var	$onKeyPress = '';
	var	$onBlur = "this.style.borderColor = this.style.borderWidth = this.style.borderStyle = '';"; 
	var	$onFocus = "this.style.border='1px solid #FF0000'";	
	var	$onChange = '';
	var	$onClick = '';
	var	$have_POST = true;
	var $style = '';
	var $class = '';
	
	function edit_control($field) {
		parent::base();
		$this->field = $field;
	}
	function set_onKeyUp($onKeyUp) { $this->onKeyUp = $onKeyUp; }
	function get_onKeyUp() { return $this->onKeyUp; }
	
	function set_onKeyDown($onKeyDown) { $this->onKeyDown = $onKeyDown; }
	function get_onKeyDown() { return $this->onKeyDown; }
	
	function set_onKeyPress($onKeyPress) { $this->onKeyPress = $onKeyPress; }
	function get_onKeyPress() { return $this->onKeyPress; }
	
	function set_onBlur($onBlur) { $this->onBlur = $onBlur; }
	function get_onBlur() { return $this->onBlur; }
	
	function set_onChange($onChange) { $this->onChange = $onChange; }
	function get_onChange() { return $this->onChange; }
	
	function set_onFocus($onFocus) { $this->onFocus = $onFocus; }
	function get_onFocus() { return $this->onFocus; }

	function set_onClick($onClick) { $this->onClick = $onClick; }
	function get_onClick() { return $this->onClick; }
	
	function set_class($class) { $this->class = $class; }
	function get_class() { return $this->class; }
	
	function make_java_script() {
		$java = '';
		if ($this->onKeyUp != '')
			$java .= ' onKeyUp = "'.$this->onKeyUp.'"';
		if ($this->onKeyDown != '')
			$java .= ' onKeyDown = "'.$this->onKeyDown.'"';
		if ($this->onKeyPress != '')
			$java .= ' onKeyPress = "'.$this->onKeyPress.'"';
		if ($this->onBlur != '')
			$java .= ' onBlur = "'.$this->onBlur.'"';
		if ($this->onChange != '')
			$java .= ' onChange = "'.$this->onChange.'"';
		if ($this->onFocus != '')
			$java .= ' onFocus = "'.$this->onFocus.'"';
		if ($this->onClick != '')
			$java .= ' onClick = "'.$this->onClick.'"';
			
		return $java;
	}
	function draw_entrable($dato, $record) {
		// para que se vean los "$"
		$dato = str_replace("$", "&#36;", $dato);
		return $dato;
	}
	function draw_no_entrable($dato, $record) {
		// para que se vean los "$"
		$dato = str_replace("$", "&#36;", $dato);
		return $dato;
	}
	function get_values_from_POST($record) {
		$field_post = $this->field.'_'.$record;
		return $_POST[$field_post];
	}
	function validate($valor) {
		return '';
	}
}
?>