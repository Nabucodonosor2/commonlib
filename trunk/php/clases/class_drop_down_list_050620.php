<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class drop_down_list extends edit_control {
	var $aValues;
	var $aLabels;
	var $aData_adicional;		// La informacion adicional va se deja en el label de cada option
	var $width_px;
	var $nom_tabla;
	var $drop_down_dependiente;
	var $enabled = true;
	
	function drop_down_list($field, $aValues, $aLabels, $width_px=0, $aData_adicional=array()) {
		parent::edit_control($field);
		$this->aValues = $aValues;
		$this->aLabels = $aLabels;
		$this->width_px = $width_px;
		$this->aData_adicional = $aData_adicional;
		$this->class = 'drop_down';
	}
	function set_drop_down_dependiente($nom_tabla, $drop_down_dependiente) {
		$this->nom_tabla = $nom_tabla;
		$this->drop_down_dependiente = $drop_down_dependiente;
		$this->set_onChange(" load_drop_down('".$this->nom_tabla."', this.id, '".$this->drop_down_dependiente."'); ");
	}
	function get_values_from_POST($record) {
		$field_post = $this->field.'_'.$record;
		if ($this->enabled) {	
			/* VMC, 21-07-2009
			 * Se valida si existe el valor en POST porque en puede ser que el dropdown este disabled
			 * desde java y en ese caso no existe el valor en POST.  
			 * Sucedio en la Biggi.NV forma de pago al seleccionar "Efectivo"
			 */ 
			if (isset($_POST[$field_post]))
				return $_POST[$field_post];
		}
		else
			return '';
	}
	function draw_entrable($dato, $record) {
		$name = $this->field.'_'.$record;
		$dropdown = '<select name="'.$name.'" id="'.$name.'" '.$this->make_java_script().' class = "'.$this->class.'" ';
		if (!$this->enabled)
			$dropdown .= ' disabled="" '; 
		if ($this->width_px != 0)
			$dropdown .= 'style="width: '.$this->width_px.'px;"';
		$dropdown .= ">";									
			
		for($k=0; $k < count($this->aValues); $k++) {
			if (isset($this->aData_adicional[$k]))
				$data_adic = 'label="'.$this->aData_adicional[$k].'"';
			else
				$data_adic = '';
			
			if (strlen($dato) != 0 && $dato == $this->aValues[$k])
				$dropdown .= '<option value="'.$this->aValues[$k].'" selected '.$data_adic.'>'.htmlentities($this->aLabels[$k]).'</option>';
			else
				$dropdown .= '<option value="'.$this->aValues[$k].'" '.$data_adic.'>'.htmlentities($this->aLabels[$k]).'</option>';
		}
		$dropdown .= '</select>';
		return $dropdown;
	}
	function get_label_from_value($dato) {
		$pos = array_search($dato, $this->aValues);
		if ($pos===false)
			return '';
		else
			return $this->aLabels[$pos];
	}
	function draw_no_entrable($dato, $record) {
		return $this->get_label_from_value($dato);
	}
}
?>