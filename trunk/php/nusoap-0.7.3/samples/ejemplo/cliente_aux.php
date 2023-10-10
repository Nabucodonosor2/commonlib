<?php
//require_once("nusoap/nusoap.php");
require_once('../../lib/nusoap.php');
// Crear un cliente apuntando al script del servidor (Creado con WSDL)
$serverURL = 'http://192.168.2.13/desarrolladores/vmelo/commonlib/trunk/php/nusoap-0.7.3/samples/ejemplo';
$serverScript = 'server_aux.php';
$metodoALlamar = 'getXML';

$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');
// Se pudo conectar?
$error = $cliente->getError();
if ($error) {
    echo '<pre style="color: red">' . $error . '</pre>';
    echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
    die();
}

// 1. Llamar a la funcion getRespuesta del servidor
$param=array('id_cod'=>'60.00',);
$result = $cliente->call(
    "$metodoALlamar",                     // Funcion a llamar
    $param,    // Parametros pasados a la funcion
    "uri:$serverURL/$serverScript",                   // namespace
    "uri:$serverURL/$serverScript/$metodoALlamar"       // SOAPAction
);
// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
    echo '<b>Error: ';
    print_r($result);
    echo '</b>';
} else {
    $error = $cliente->getError();
    if ($error) {
        echo '<b style="color: red">-Error: ' . $error . '</b>';
        $xml_result = '';
    } else {
        $xml_result = base64_decode($result);

    }
} 

if($xml_result != ''){
	print_r ($xml_result);
}
?>