<?php
require_once(dirname(__FILE__)."/auto_load.php");

$sql = $_REQUEST['sql'];
$sql = str_replace("\\'", "'", $sql);		// Las comillas simples ', vuelven como \'

help_producto::draw_htm_lista_producto($sql);
?>