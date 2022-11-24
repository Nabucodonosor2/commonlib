<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_text extends edit_control {
	var $size;
	var $maxlen;
	var $type;
	var $readonly = false;
	var $forzar_js = false;			// Es true cuando se desea forzar a que se agregue  js aunque este hidden el objeto
	var $forzar_control = false;	//	en modo lectura draw_no_entrable(), Si es true se dibuje el control.
									// Usado para edit_text escondidos, tipo hidden pero que en modo lectura se desea obtener el datos desde js
									// Ver ejemplo de uso en: helen -> wi_class_deudor.php -> dw_declaracion
	var $nom_tabla;
	var $drop_down_dependiente;
	
	function edit_text($field, $size, $maxlen, $type='text', $readonly=false, $forzar_control = false) {		
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
		else		//if(base::get_SO() == 'windows')
			$this->size = $size * 1;
		
		$this->maxlen = $maxlen;
		$this->type = $type;
		$this->readonly = $readonly;
		$this->forzar_control = $forzar_control;
		$this->class = 'input_text';
	}
	function set_readonly($readonly) {
		$this->readonly = $readonly;
	}
	function set_type($type) {
		$this->type = $type;
	}
	function draw_entrable($dato, $record) {
		$dato = str_replace('"', '&quot;', $dato);
		$field = $this->field.'_'.$record;
		if ($this->readonly)
			$ctrl = '<input class="'.$this->class.'" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" readonly="true" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		else
			$ctrl = '<input class="'.$this->class.'" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		
		if ($this->type != 'hidden' || $this->forzar_js)
			$ctrl .= $this->make_java_script();

		if ($this->style!='')
			$ctrl .= ' style="'.$this->style.'"';
			
		$ctrl .= '/>';
			
		return $ctrl;
	}
	function draw_no_entrable($dato, $record) {
		if ($this->forzar_control)
			return edit_text::draw_entrable($dato, $record);
			
		if ($this->type=='hidden')
			return '';
		else
			return parent::draw_no_entrable($dato, $record);
	}
	function set_drop_down_dependiente($nom_tabla, $drop_down_dependiente) {
		$this->nom_tabla = $nom_tabla;
		$this->drop_down_dependiente = $drop_down_dependiente;
		$this->set_onChange(" load_drop_down_from_text('".$this->nom_tabla."', this.id, '".$this->drop_down_dependiente."'); ");
	}
}
?>