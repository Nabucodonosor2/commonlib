<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class header_drop_down extends  header_output {
	var	$sql;
	
	function header_drop_down($field, $field_bd, $nom_header, $sql, $operacion_accumulate='') {
		parent::header_output($field, $field_bd, $nom_header, $operacion_accumulate);

		// Borra los cambios de lineas del sql para evitar errores al pasar parametros al js
		$sql = str_replace("\r\n", " ", $sql);
		$sql = str_replace("\r", " ", $sql);
		$sql = str_replace("\n", " ", $sql);
		$sql = str_replace("'", "\'", $sql);
		
		$this->sql = $sql;
		$sql = str_replace("\r\n", " ", $sql);
	}
	function make_java_script() {
		return '"return dlg_find_drop_down(\''.$this->nom_header.'\', \''.$this->valor_filtro.'\', \''.$this->sql.'\', this);"';		
	}
	function set_value_filtro($valor_filtro) {
		if ($valor_filtro == '__BORRAR_FILTRO__')
			$this->valor_filtro = '';
		elseif (strlen($valor_filtro) == 0)
			$this->valor_filtro = '__NULL__';
		else
			$this->valor_filtro = $valor_filtro;
	}
	function make_filtro() {
		if ($this->valor_filtro=='')
			return '';
		elseif ($this->valor_filtro=='__NULL__')
			return "(".$this->field_bd." is null) and ";
		else				
			return "(".$this->field_bd." = ".$this->valor_filtro.") and ";		
	}
	function make_nom_filtro() {
		if ($this->valor_filtro=='')
			return '';
		
		$valor_filtro = $this->valor_filtro;
		
		// Busca el nom del valor filtro
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		// En los headers en el constructor se cambia los ' por \' para poder pasar bien como parametro js
		// Ahora se debe revertir para poder ejecutar el sql
		$sql = str_replace("\'", "'",$this->sql);
		$result = $db->build_results($sql);	
		for ($i=0; $i<count($result); $i++)
			if ($result[$i][0]==$valor_filtro)
				$valor_filtro = $result[$i][1];	// nom_
		return $this->nom_header.": ".$valor_filtro;
	}	
}
?>