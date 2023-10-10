<?php
require_once(dirname(__FILE__)."/auto_load.php");

$informe = $_REQUEST['informe'];
$cod_item_menu = $_REQUEST['cod_item_menu'];
// Borra todas las sesiones de mantendor e informes
session::un_set_all_modulo();
session::un_set($informe);

// Busca la carpeta del mantenedor
// Primero busca si existe mantenedor en appl
$ROOT = session::get('K_ROOT_URL');
$ROOT_DIR = session::get('K_ROOT_DIR');
if (file_exists($ROOT_DIR.'appl/'.$informe)) {
	header ('Location:'.$ROOT.'appl/'.$informe.'/wi_'.$informe.'.php?cod_item_menu='.$cod_item_menu);
	return;
}

/*
// Segundo busca en appl_parent
$appl = session::get('K_APPL');
if (!session::is_set('K_APPL_PARENT')) 
	$appl_parent = $appl;
else {
	$appl_parent = session::get('K_APPL_PARENT');
	$ROOT = str_replace("/".$appl."/", "/".$appl_parent."/", $ROOT);
	$ROOT_DIR = str_replace("/".$appl."/", "/".$appl_parent."/", $ROOT_DIR);
	if (file_exists($ROOT_DIR.'appl/'.$modulo)) {
		header ('Location:'.$ROOT.'appl/'.$modulo.'/wo_'.$modulo.'.php?cod_item_menu='.$cod_item_menu);
		return;
	}
}

// Tercero buscar en appl common
$ROOT = str_replace("/".$appl_parent."/", "/commonlib/", $ROOT);
$ROOT_DIR = str_replace("/".$appl_parent."/", "/commonlib/", $ROOT_DIR);
if (file_exists($ROOT_DIR.'appl/'.$modulo)) {
	header ('Location:'.$ROOT.'appl/'.$modulo.'/wo_'.$modulo.'.php?cod_item_menu='.$cod_item_menu);
	return;
}
*/
?>