<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class sql_tabla extends base {
	/*
	 * Para cada mantendor de la carpeta appl en una aplicación se debe crear una clase que herede de esta y 
	 * reimplementar la funcion get_sql
	 * Dentro de todos los php no debe existir ningún sql, todos deben ir dentro de esta función
	 * Los sql para oci y mssql deben ir juntos, y cuando se necesite modificar uno se debe hacer la correción en  el otro inmediatamente
	 */
	static function get_sql($selector_sql) {
		base::error("sql_tabla::get_sql, selector: '$selector_sql' no definido");
	}
	static function replace_KEYS($sql, $keys) {
		// Se salta el 1er elemento quer indica el selector del SQL
		for($i=1; $i<count($keys); $i++)
			$sql = str_replace("{PARAM".$i."}", $keys[$i], $sql);
		return $sql;
	}
}
?>