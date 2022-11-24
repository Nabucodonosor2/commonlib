<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class static_link extends static_text {
	var	$href;
	
	function static_link($field, $href = '#', $onClick = '') {
		parent::static_text($field);
		$this->href = $href;
		$this->set_onClick($onClick);
	}
	function draw_no_entrable($dato, $record) {
		/* En dato pueden venir 2 valores "valor1|valor2"
		 * donde valor1 es el valor visible y valor2 es el valor que va en el link
		 * Si viene un solo dato (no esta el caracter "|" se asume que valor1 y valor2 son igaules a $dato 
		 */
		$pos = strpos($dato, '|');
		if ($pos===false) {
			$valor1 = $dato;
			$valor2 = $dato;
		}
		else {
			$valores = explode("|", $dato);
			$valor1 = $valores[0];
			$valor2 = $valores[1];
		}
		$field = $this->field.'_'.$record;

		$href = $this->href;
		$href = str_replace("[$this->field]", $valor2, $href);
		$ctrl = '<a id="'.$field.'" href="'.$href.'" '.$this->make_java_script().'>'.$valor1.'</a>';
		return $ctrl;
	}
	function draw_entrable($dato, $record) {
		return static_link::draw_no_entrable($dato, $record);
	}
}
?>