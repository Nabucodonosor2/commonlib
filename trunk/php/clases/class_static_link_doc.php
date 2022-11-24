<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class static_link_doc extends static_link {
	private $tabla;
	private	$cod_item_menu;
	
	function static_link_doc($field, $tabla, $cod_item_menu) {
		parent::static_link($field);
		$this->tabla = $tabla;
		$this->cod_item_menu = $cod_item_menu;
	}
	function draw_no_entrable($dato, $record) {
		// En dato vienen 2 valores "valor1|valor2"
		// donde valor1 es el valor visible y valor2 es el valor que va en el link
		$valores = explode("|", $dato);
		$field = $this->field.'_'.$record;
		
		//SC - 26/10/2009 Comentado para presentacion
		//$ctrl = '<a id="'.$field.'" href="#" onClick = "mod_documento(\''.$this->tabla.'\', \''.$valores[1].'\', \''.$this->cod_item_menu.'\', \'N\');" >'.$valores[0].'</a>';
		$ctrl = $valores[0];
		return $ctrl;

	}
	function draw_entrable($dato, $record) {
		return static_link_doc::draw_no_entrable($dato, $record);
	}
}
?>