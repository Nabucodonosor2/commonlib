<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class header_mes extends  header_drop_down {
	public	$valor_filtro2='';
	
	function header_mes($field, $field_bd, $nom_header, $operacion_accumulate='') {
		$sql = "select COD_MES, NOM_MES from MES order by COD_MES";
		parent::header_drop_down($field, $field_bd, $nom_header, $sql, $operacion_accumulate);
	}
	function make_java_script() {
		return '"return dlg_find_mes(\''.$this->nom_header.'\', \''.$this->valor_filtro.'\', \''.$this->valor_filtro2.'\', this);"';		
	}
	function set_value_filtro($valor_filtro) {
		if ($valor_filtro == '__BORRAR_FILTRO__') {
			$this->valor_filtro = '';
			$this->valor_filtro2 = '';
		}
		else {
			$values = explode("|", $valor_filtro);
			$this->valor_filtro = $values[0];
			$this->valor_filtro2 = $values[1];
		}
	}
	function make_filtro() {
		if (strlen($this->valor_filtro)==0)
			return '';
		
		if ($this->valor_filtro2=='')
			return "(".$this->field_bd." = ".$this->valor_filtro.") and ";		
		else		
			return "(".$this->field_bd." between ".$this->valor_filtro." and ".$this->valor_filtro2.") and ";		
	}
	function make_nom_filtro() {
		if ($this->valor_filtro=='')
			return '';
		
		$valor_filtro = $this->valor_filtro;
		$valor_filtro2 = $this->valor_filtro2;
		
		// Busca el nom del valor filtro
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		// En los headers en el constructor se cambia los ' por \' para poder pasar bien como parametro js
		// Ahora se debe revertir para poder ejecutar el sql
		$sql = str_replace("\'", "'",$this->sql);
		$result = $db->build_results($sql);	
		for ($i=0; $i<count($result); $i++) {
			if ($result[$i][0]==$valor_filtro)
				$valor_filtro = $result[$i][1];	// nom_
			if ($this->valor_filtro2!='') {
				if ($result[$i][0]==$valor_filtro2)
					$valor_filtro2 = $result[$i][1];	// nom_
			}
		}
		if ($this->valor_filtro2=='')
			return $this->nom_header.": ".$valor_filtro;
		else
			return $this->nom_header.": ".$valor_filtro." a ".$valor_filtro2;
	}	
}
?>