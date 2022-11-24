<?php
function __autoload($class_name)
{
	$ROOT = dirname(__FILE__);
	if (substr($class_name, 0, 3)=='wi_' || substr($class_name, 0, 3)=='wo_'  || substr($class_name, 0, 4)=='sql_') {
		if (substr($class_name, 0, 4)=='sql_')
			$folder = substr($class_name, 4);
		else		
			$folder = substr($class_name, 3);
			
		$appl = session::get('K_APPL');
		$file_name = $ROOT.'/../../../'.$appl.'/trunk/appl/'.$folder.'/class_'.$class_name . '.php';
		if (file_exists($file_name)) {
			require_once($file_name);
    		return;
		}
		if (session::is_set('K_APPL_PARENT')) {
			$appl_parent = session::get('K_APPL_PARENT');
			$file_name = $ROOT.'/../../../'.$appl_parent.'/trunk/appl/'.$folder.'/class_'.$class_name . '.php';
			if (file_exists($file_name)) {
				require_once($file_name);
	    		return;
			}
		}
		$file_name = $ROOT.'/../../../commonlib/trunk/appl/'.$folder.'/class_'.$class_name . '.php';
		if (file_exists($file_name)) {
			require_once($file_name);
    		return;
		}
	}
	if(file_exists($ROOT.'/clases/class_'.$class_name . '.php')) {
		require_once($ROOT.'/clases/class_'.$class_name . '.php');
    	return;
	}
	
	if(file_exists($ROOT.'/FPDF/class_'.$class_name . '.php')) {
		require_once($ROOT.'/FPDF/class_'.$class_name . '.php');
    	return;
	}
	if(file_exists($ROOT.'/php_writeexcel-0.3.0/class_'.$class_name . '.php')) {
		require_once($ROOT.'/php_writeexcel-0.3.0/class_'.$class_name . '.php');
    	return;
	}
	if(file_exists($ROOT.'/phpreport/class_'.$class_name . '.php')) {
		require_once($ROOT.'/phpreport/class_'.$class_name . '.php');
    	return;
	}	

	if ($class_name=='Numbers_Words') {
		require_once($ROOT.'/pear/Numbers/Words.php');
    	return;
	}
	
	// Buscar un auto_load() en la appl
	$appl = session::get('K_APPL');
	$file_name = $ROOT.'/../../../'.$appl.'/trunk/auto_load.php';
	if (file_exists($file_name)) {
		require_once($file_name);
		auto_load($class_name);
    	return;
	}
}
?>