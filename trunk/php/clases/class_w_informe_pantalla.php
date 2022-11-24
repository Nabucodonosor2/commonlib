<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class w_informe_pantalla extends w_output {
	var $b_print_visible = true;
	
	function w_informe_pantalla($nom_tabla, $sql, $cod_item_menu) {
		parent::w_output($nom_tabla, $sql, $cod_item_menu);
		$this->b_add_visible = false;
	}
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($boton=='print' && $habilita) {
			$temp->setVar("WO_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib/trunk/images/b_'.$boton.'.jpg" type="image" '.
																							'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
																							'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
																							'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
																							'onClick="dlg_print();" '.
																							'/>');
		}
		else
			parent::habilita_boton($temp, $boton, $habilita);
	}
	function redraw(&$temp) {
		if ($this->b_print_visible)
			$this->habilita_boton($temp, 'print', true);			
	}
	function print_informe() {
		$this->error('Se debe implementar el print en cada informe.');			
	}
	function procesa_event() {
		if(isset($_POST['b_print_x']))
			$this->print_informe();
		else 
			parent::procesa_event();
	}
}
?>