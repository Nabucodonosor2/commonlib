<?php
require_once(dirname(__FILE__)."/auto_load.php");
$K_ROOT_URL = session::get('K_ROOT_URL');
base::spd_lock_table_user();

$cod_usuario = session::get("COD_USUARIO");
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$db->EXECUTE_SP("sp_log_cambio", "'LOGOUT', '', $cod_usuario, 'L'");

session::destroy();

header ('Location:'.$K_ROOT_URL."index.php");
?>