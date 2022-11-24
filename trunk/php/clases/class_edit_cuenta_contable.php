<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_cuenta_contable extends edit_text {
	var $agrupacion;
	var $solo_positivos;
	var $con_separador_miles;
	
	function edit_cuenta_contable($field, $agrupacion=array(2,2,2)) {
		/* ej. $agrupacion=array(2,2,2)
		 * 	las cuentas serian 10-12-12
		 * 
		 * $agrupacion=array(2,3)
		 * 	las cuentas serian 10-123
		 */
		$size =0;
		for ($i=0; $i<count($agrupacion); $i++)
			$size += $agrupacion[$i];
		parent::edit_text($field, $size, $size);
		$this->agrupacion = $agrupacion;
		$this->set_onKeyPress('return onlyNumbers(this, event,0, true);');
		$this->class = 'input_num';
	}
	function draw_entrable($dato, $record) {
		return parent::draw_entrable($dato, $record);
	}
	function draw_no_entrable($dato, $record) {
		if ($dato!='') {
			$dato_fmt = '';
			$ind = 0;
			for ($i=0; $i < count($this->agrupacion); $i++) {
				$dato_fmt .= substr($dato, $ind, $this->agrupacion[$i]).'-';
				$ind += $this->agrupacion[$i];
			}
			$dato = substr($dato_fmt, 0, strlen($dato_fmt) - 1);		// borra ultimo guion
		}
		return parent::draw_no_entrable($dato, $record);
	}
}
?>