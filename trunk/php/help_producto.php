<?php
///////////////////////
// php llamado con ajax en el javascript function help_producto()
////////////////////////
require_once(dirname(__FILE__)."/auto_load.php");

$cod_producto = $_REQUEST['cod_producto'];
$nom_producto = $_REQUEST['nom_producto'];

help_producto::find_producto($cod_producto, $nom_producto);
?>