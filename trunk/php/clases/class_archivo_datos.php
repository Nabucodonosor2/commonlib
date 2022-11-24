<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class archivo_datos extends archivo {
	/*
	 Se define la estructura de las columnas que deben venir en el archivo
	 Para cada columna se debe indicar Nombre, Tipo_dato, formato
	 Nombre : nombre de la columna, ejemplo "COD_BOLETA"
	 TIPO: Los tipos de datos permitidos son NUMBER, DATE, TEXT
	 FORMATO: Para number se especifica la cantidad de digitos de la parte entrea y la cantidad de digitos de la parte decimal
	 Para date se especifica la combinacion de dd mm yyyy, ej "dd/mm/yyyy'
	 NULL: puede indicarse 'S' (acepta valores null) o N' (no acepta valores null).  Se considera null si viene string ''
	 Si no se especifica NULL se asume que acepta null
	  
	 Ejemplo: Si el archivo tiene 3 columnas
	 $array['COD_BOLETA']['TIPO'] = 'NUMBER';
	 $array['COD_BOLETA']['FORMATO'] = '10';		// 10 enteros sin decimales
	  
	 $array['FECHA_BOLETA']['TIPO'] = 'DATE';
	 $array['FECHA_BOLETA']['FORMATO'] = 'dd/mm/yyyy';
	  
	 $array['MONTO_BOLETA']['TIPO'] = 'NUMBER';
	 $array['MONTO_BOLETA']['FORMATO'] = '10.4';		// 10 enteros y 4 decimeales, largo total 15 considerarndo el punto => 1234567890.1234
	  
	 $array['GLOSA_BOLETA']['TIPO'] = 'TEXT';
	  
	 Otro ejemplo:
		$columnas = array('COD_BOLETA' 		=> array('TIPO' => 'NUMBER', 'FORMATO' => '10',			'NULL' => 'N')
		,'RUT' 	   		=> array('TIPO' => 'NUMBER', 'FORMATO' => '10',			'NULL' => 'N')
		,'FECHA_PAGO' 		=> array('TIPO' => 'DATE',   'FORMATO' => 'dd/mm/yyyy',	'NULL' => 'N')
		,'MONTO_PAGO' 		=> array('TIPO' => 'NUMBER', 'FORMATO' => '14',			'NULL' => 'N')
		,'NRO_SUB_BOLETA' 	=> array('TIPO' => 'NUMBER', 'FORMATO' => '10',			'NULL' => 'N')
		);
		);
	 */
	var $columnas = array();
	var $data = array();		// contenido del archivo
	var $tipo_dato_permitido = array('NUMBER', 'DATE', 'TEXT');
	var $end_field = "\t";		// separador entre campos
	var $handle = 0;

	var $K_MAX_ERROR = 20;
	var $error = array();		// Lista de errores detectados

	public function archivo_datos($file, $dir, $columnas) {
		$a		= $_FILES[$file]['name'];
		$d		= $dir;
		$e		= array($this->getTipoArchivo($a));
		$t		= $_FILES[$file]['size'];
		$tmp	= $_FILES[$file]['tmp_name'];
		parent::archivo($a,$d,$e,$t,$tmp);

		$this->columnas = $columnas;
		foreach ($this->columnas as &$value) {
			if (!isset($value['TIPO']))
			$this->error('archivo_datos necesita el tipo del dato, en la columna: '.key($value));
				
			$tipo = $value['TIPO'];
			if (!in_array($tipo, $this->tipo_dato_permitido ))
			$this->error('archivo_datos no soporta el tipo de datos: '.$tipo);
		}
	}
	function open() {
		$this->handle = fopen($this->directorio.$this->nombre.".".$this->tipoArchivo, "r");
	}
	function close() {
		if ($this->handle)
		fclose($this->handle);
	}
	function feof() {
		if ($this->handle)
		return feof($this->handle);
		return true;
	}
	function valida_record($record, $num_linea) {
		$indices = array_keys($this->columnas);
		for ($i=0; $i < count($this->columnas); $i++) {
			$field = $indices[$i];
			$dato = $record[$field];
			$tipo = $this->columnas[$field]['TIPO'];
			$formato = $this->columnas[$field]['FORMATO'];
			if ($this->columnas[$field]['NULL']=='N' && $dato=='')
				return $this->registra_error("Linea $num_linea: La columna '$field' esta vacía");

			if ($this->columnas[$field]['NULL']=='S'  && $dato=='')
				continue;
				
			switch ($tipo) {
				case 'NUMBER':
					// si viene "," en vez de ".", lo cambia a "." para validar 
					$dato = str_replace(",", ".", $dato);
					
					if (!is_numeric($dato)){
						return $this->registra_error("Linea $num_linea: Se esperaba un dato numerico en la columna $field");
					}
					$aux = explode('.', $formato);
					$parte_entera_max = $aux[0];
					$parte_decimal_max = isset($aux[1]) ? $aux[1] : 0;
						
					$aux = explode('.', $dato);
					$parte_entera = $aux[0];
					$parte_decimal = isset($aux[1]) ? $aux[1] : '';
						
					if (strlen($parte_entera) > $parte_entera_max)
						return $this->registra_error("Linea $num_linea: La parte entera de $field tiene demasiados digitos (máximo: $parte_entera_max)");
					if (strlen($parte_decimal) > $parte_decimal_max)
						return $this->registra_error("Linea $num_linea: La parte decimal de $field tiene demasiados digitos (máximo: $parte_decimal_max)");
					break;
				case 'DATE':
					if (strlen($formato) != strlen($dato))
						return $this->registra_error("Linea $num_linea: La longintud $field no coincide con el formato definito ($formato)");

					$dia = '';
					$mes = '';
					$ano = '';
					for ($j=0; $j < strlen($dato); $j++)
					switch ($formato[$j]) {
						case 'd': 	$dia .= $dato[$j];
						break;
						case 'm': 	$mes .= $dato[$j];
						break;
						case 'y': 	$ano .= $dato[$j];
						break;
					}
					if (!checkdate($mes, $dia, $ano))
						return $this->registra_error("Linea $num_linea: La fecha de $field ($dato) no es valida. (formato: $formato)");

					break;
						case 'TEXT':
							// No existe validación para TEXT
							break;
			}
		}
		return $record;
	}
	function registra_error($error) {
		if (count($this->error) < $this->K_MAX_ERROR)
		$this->error[] = $error;
		if (count($this->error) == $this->K_MAX_ERROR)
		$this->error[] = 'Demasiados errores.';
		return $error;
	}
	function display_error() {
		if (count($this->error)) {
			$error = 'Algunos registros no fueron cargados porque el archivo contenia errores.\n\n';
			for ($i=0; $i < count($this->error); $i++) {
				$error .= $this->error[$i].'\n';
			}
			$this->alert($error);
		}
	}
	function read_record($num_linea) {
		$linea = fgets($this->handle);
		if ($linea=='')
			return false;
		$linea = explode($this->end_field, $linea);
		$result = array();
		$indices = array_keys($this->columnas);
		for ($i=0; $i < count($this->columnas); $i++) {
			if (isset($linea[$i])) {
				$field = $indices[$i];
				if ($this->columnas[$field]['TIPO']=='NUMBER') {
					// si viene "," en vez de ".", lo cambia a "."  
					$linea[$i] = str_replace(",", ".", $linea[$i]);
				}				
				$result[$indices[$i]] = trim($linea[$i]);
			}
			else
				return $this->registra_error('Linea '.$num_linea.': Faltan columnas. No se encontro datos para: '.$indices[$i]);
		}
		$result = $this->valida_record($result, $num_linea);
		if (!is_array($result))
			return $result;
			
		return $result;
	}
}
?>