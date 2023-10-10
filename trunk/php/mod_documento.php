<?php
require_once(dirname(__FILE__)."/auto_load.php");

$tabla = $_REQUEST['tabla'];
$cod = $_REQUEST['cod'];
$cod_item_menu = $_REQUEST['cod_item_menu'];
$mod = $_REQUEST['mod'];

$sql = 'select '.$cod.' COD_'.strtoupper($tabla).' ORDER BY COD_'.strtoupper($tabla);
$wo = new w_output($tabla, $sql, $cod_item_menu);
$wo->detalle_record_desde($mod=='S');
?>