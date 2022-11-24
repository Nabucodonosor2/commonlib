<?php
require_once(dirname(__FILE__)."/auto_load.php");

$tabla = $_REQUEST['tabla'];
$cod = $_REQUEST['cod'];
$cod_usuario = session::get("COD_USUARIO");	// viene del login

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$db->BEGIN_TRANSACTION();
if ($db->EXECUTE_SP("spd_lock_table", $cod.", '".$tabla."', ".$cod_usuario)) {
	$db->COMMIT_TRANSACTION();
}
else {
	$db->ROLLBACK_TRANSACTION();
}
?>