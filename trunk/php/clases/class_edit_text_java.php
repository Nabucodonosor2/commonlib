<?php
require_once(dirname(__FILE__)."/../auto_load.php");

//clase creada para permitir crear script java, aunque el objeto este hidden. Contrario al edit_text   

class edit_text_java extends edit_control {
	var $size;
	var $maxlen;
	var $type;
	var $readonly = false;

	function edit_text($field, $size, $maxlen, $type='text', $readonly=false) {		
		parent::edit_control($field);
		if(base::get_SO() == 'linux')
			$this->size = $size * 1;
		elseif(base::get_SO() == 'mac') {
			if ($size <= 10)
				$this->size = $size;
			else	
				$this->size = $size * 0.65;
			$this->size = round($this->size, 0);
			if ($this->size == 0)
				$this->size = 1;
		}
		else //(base::get_SO() == 'windows') u otro
			$this->size = $size * 1;
		
		$this->maxlen = $maxlen;
		$this->type = $type;
		$this->readonly = $readonly;
		$this->class = 'input_text';
	}
	function set_readonly($readonly) {
		$this->readonly = $readonly;
	}
	function set_type($type) {
		$this->type = $type;
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->readonly)
			$ctrl = '<input class="'.$this->class.'" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" readonly="true" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		else
			$ctrl = '<input class="'.$this->class.'" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		
		$ctrl .= $this->make_java_script();
		
		$ctrl .= '/>';
			
		return $ctrl;
	}
	function draw_no_entrable($dato, $record) {
		if ($this->type=='hidden')
			return '';
		else
			return parent::draw_no_entrable($dato, $record);
	}
}
?>