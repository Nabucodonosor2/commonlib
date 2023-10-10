<?php
require_once(dirname(__FILE__)."/auto_load.php");

$nom_tabla = $_REQUEST["nom_tabla"]; 
$selectDestino = $_REQUEST["select"]; 
$opcionSeleccionada =$_REQUEST["opcion"];

$len = strlen($selectDestino);
$i = $len - 1;
while (substr($selectDestino, $i, 1) != '_')
	$i--;
$field = substr($selectDestino, 0, $i);
$record = substr($selectDestino, $i + 1, $len - $i - 1);

$wi = session::get('wi_'.$nom_tabla);
$control = $wi->find_control($field);
if ($control) {
	// En empresa al eligir un pasi distinto de chile, se caia porque no existen ciudades 
	if ($opcionSeleccionada=='')
		$opcionSeleccionada = 0;
		 
	$control->retrieve($opcionSeleccionada);
	$drop_down = $control->draw_entrable('', $record);
	if ($control->drop_down_dependiente!='')
		$drop_down = '/'.$control->drop_down_dependiente.'_'.$record.'/'.$drop_down;
	print $drop_down;
}	
?>