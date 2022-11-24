<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class static_text extends edit_control {
	var $type;
	
	function static_text($field, $type='') {
		parent::edit_control($field);
		$this->have_POST = false;
		$this->set_onBlur('');
		$this->set_onFocus('');
		$this->type = $type;
	}
	function draw_no_entrable($dato, $record) {
		if ($this->type == 'hidden')
			return '';
		return static_text::draw_entrable($dato, $record);
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->type == 'hidden')
			$ctrl = '<label id="'.$field.'" style="display:none">'.$dato.'</label>';
		else
			$ctrl = '<label id="'.$field.'" '.$this->make_java_script().'>'.$dato.'</label>';		
		return $ctrl;
	}
}
?>