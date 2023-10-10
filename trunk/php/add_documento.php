<?php
require_once(dirname(__FILE__)."/auto_load.php");

$tabla = $_REQUEST['tabla'];
$cod_item_menu = $_REQUEST['cod_item_menu'];

$wo = new w_output($tabla, '', $cod_item_menu);
$wo->add_record_desde();
?>