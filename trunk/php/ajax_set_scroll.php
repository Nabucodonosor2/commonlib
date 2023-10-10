<?php
require_once(dirname(__FILE__)."/auto_load.php");

$scroll = $_REQUEST['scroll'];
$nom_tabla = $_REQUEST['nom_tabla'];
session::set('W_OUTPUT_SCROLL_'.$nom_tabla, $scroll);	// para indicar el registro donde se clickeo
?>