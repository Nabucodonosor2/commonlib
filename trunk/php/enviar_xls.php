<?php
// Va a la ventana presentacion pero gatilla el envio de un xls
// Es usadoi en w_param_informe
require_once(dirname(__FILE__)."/auto_load.php");

$dw = session::get('DATAWINDOW_XLS');
$sp = session::get('DATAWINDOW_XLS_SP');
$param = session::get('DATAWINDOW_XLS_PARAM');

if (K_TIPO_BD=="mssql") {
	$dw->set_sql("exec $sp $param");
}
elseif (K_TIPO_BD=="oci") {
	if ($sp != '') {			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->EXECUTE_SP($sp, $param);
	}		
}
$dw->retrieve();
$dw->export_to_excel($dw->nom_tabla);	// en $dw->nom_tabla viene el nombre del informe	
?>