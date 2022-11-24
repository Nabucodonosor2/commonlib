<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class w_parametrica extends w_output {
	private 	$cod_item_menu_parametro;
	
	function w_parametrica($nom_tabla, $sql, $cod_item_menu, $cod_item_menu_parametro) {
		parent::w_output($nom_tabla, $sql, $cod_item_menu);
		$this->cod_item_menu_parametro = $cod_item_menu_parametro;
	}
	function procesa_event() {
		if(isset($_POST['b_back_x']))
			header('Location:' . $this->root_url . 'appl/parametro/wi_parametro.php?cod_item_menu='.$this->cod_item_menu_parametro);			
		else
			parent::procesa_event();	
	}
}
?>