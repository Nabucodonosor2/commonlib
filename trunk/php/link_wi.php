<?php
require_once(dirname(__FILE__)."/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

// obtiene los valores del REQUEST
$modulo_origen = $_REQUEST['modulo_origen'];
$modulo_destino = $_REQUEST['modulo_destino'];
$modulo_uuper = strtoupper($modulo_destino);
$cod_modulo_destino = $_REQUEST['cod_modulo_destino'];
$cod_item_menu = $_REQUEST['cod_item_menu'];
if (isset($_REQUEST['current_tab_page'])) {
	session::set('wi_CURRENT_TAB_'.$modulo_origen, $_REQUEST['current_tab_page']);
}
if (isset($_REQUEST['DESDE_OUTPUT'])) {
	session::set('wi_DESDE_OUTPUT_'.$modulo_origen, 'DESDE_OUTPUT');
}


// ********* validar si tiene acceso a la opcion

// output auxiliar
$sql = "select $cod_modulo_destino COD_$modulo_uuper";
if (K_TIPO_BD=="oci")
	$sql .= " from DUAL";
if ($cod_modulo_destino==K_NEW_RECORD) // Para que el output quede con ero registros
	$sql .= " where 1=2";
$sql .= " ORDER BY COD_$modulo_uuper";
$wo = new w_output($modulo_destino, $sql, $cod_item_menu); 
// No se llama al retrieve() en forma normal porque se deben no se debe ejecutar por completo para evitar el redraw del output 
$wo->retrieve_totales();
$wo->set_current_page(0);
$wo->save_SESSION();

// va al detalle
session::set('DESDE_link_wi', $modulo_origen);
if ($cod_modulo_destino==K_NEW_RECORD) {
	$wo->detalle_record(K_NEW_RECORD);
}
else
	$wo->detalle_record(0);
?>
