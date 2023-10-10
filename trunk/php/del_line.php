<?php
require_once(dirname(__FILE__)."/auto_load.php");

$nom_tabla = $_REQUEST['nom_tabla'];
$label_record = $_REQUEST['label_record'];
$record = $_REQUEST['record'];

if(file_exists('../../appl/'.$nom_tabla.'/class_wi_'.$nom_tabla. '.php'))
	require_once('../../appl/'.$nom_tabla.'/class_wi_'.$nom_tabla. '.php');



$wi = session::get('wi_'.$nom_tabla);
$wi->del_line($label_record, $record);
$wi->save_SESSION();
?>