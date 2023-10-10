<?php
error_reporting(0);
$rawPost = strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0? (isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input")) : NULL;
require_once('../../lib/nusoap.php');
$server = new soap_server;
$server->debug_flag = 1;
$server->register("hola");
$server->service($rawPost);

function hola( $name ){
		return empty( $name )  ? new soap_fault('Client','','Ingrese un nombre válido') : "Hola " . $name;
}
?>