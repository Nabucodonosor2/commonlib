<?php
require_once(dirname(__FILE__)."/auto_load.php");

$token = $_REQUEST['token'];

$rpt = session::get($token);
session::un_set($token);
$rpt->make_reporte();
?>