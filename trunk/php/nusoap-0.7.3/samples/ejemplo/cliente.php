<?php
require_once('../../lib/nusoap.php');
$soapclient = new soapclient('http://192.168.2.13/desarrolladores/vmelo/commonlib/trunk/php/nusoap-0.7.3/samples/ejemplo/server.php');
$res = $soapclient->call( 'hola' , array('name' => 'Mundo') ); 
echo "*$res*"; 
?>