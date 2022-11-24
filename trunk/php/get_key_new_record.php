<?php
require_once(dirname(__FILE__)."/auto_load.php");

$tabla = strtoupper($_REQUEST['tabla']);

if (session::is_set('COD_'.$tabla)) {
	$cod = session::get('COD_'.$tabla);
	session::un_set('COD_'.$tabla);
}
else
	$cod = '';
print $cod;
?>