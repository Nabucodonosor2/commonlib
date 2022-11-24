<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class datawindow_tree extends datawindow {
	var $folders = array();		// array de campos que se consideran carpetas o grupos
	var $documento ;
	var $documento_field_display;
	var $controls_level = array();	// controls de los leves distintos a detalle
	var $accumulate_tree = array();
	var $onclick = '';
	const K_LEVEL_DETALLE = 999;
	const css_folder = "dwt_folder";
	
	function datawindow_tree($sql, $label_record) {
		parent::datawindow($sql, $label_record);
	}	
	function add_folder() {
		/* La cantidad de parametros es variable
		 * param1 = el campo por el cual se agrupa
		 * param2 = campo que debe ser desplegado cuando se digçbuja el grupo
		 * param3..n : campos que deben modificarse en paralelo (todos lo del grupo cuando se lee desde el POST
		 */
		$keys = func_get_args();
		$f = new stdClass;
		$f->key_group = $keys[0];
		$f->name_group = $keys[1];
		$f->fields_group = array();
		for($i=2; $i<count($keys); $i++)
			$f->fields_group[] = $keys[$i]; 
		$this->folders[] = $f;
	}
	function set_documento($field, $field_display='', $onclick='') {
		$this->documento = $field;
		$this->onclick = $onclick;
		if ($field_display=='')
			$this->documento_field_display = $field;
		else
			$this->documento_field_display = $field_display;
	}
	function add_control_level(&$control, $level, $accumulate_tree='') {
		$this->controls_level[$control->field][$level] = $control;
		if ($accumulate_tree != '')
			$this->accumulate_tree[$control->field][$level] = $accumulate_tree;
	}
	function es_menor($field, $valor1, $valor2) {
		// dado el campo se puede reimplementar para comparar de forma distinta
		return $valor1 < $valor2;
	}
	function es_mayor($field, $valor1, $valor2) {
		// dado el campo se puede reimplementar para comparar de forma distinta
		return $valor1 > $valor2;
	}
	function get_item_level($record, $field, $level) {
		if (!isset($this->accumulate_tree[$field][$level])) 
			return $this->get_item($record, $field);

		$operacion = $this->accumulate_tree[$field][$level];
		for ($j=0; $j <= $level; $j++)
			$cod_ini[$j] = $this->get_item($record, $this->folders[$j]->key_group);
		switch ($operacion) {
			case 'SUM': 
			case 'AVG': 
				$suma = 0;
				$count = 0;
				$break = false;
				for ($i=$record; $i < $this->row_count();$i++) {
					for ($j=0; $j <= $level; $j++)
						$cod[$j] = $this->get_item($i, $this->folders[$j]->key_group);

					for ($j=0; $j <= $level; $j++) {
						if ($cod[$j] != $cod_ini[$j]) {							
							$break = true; 
							break;
						}
					}
					if ($break) break; 

					$value = $this->get_item($i, $field);
					$suma += $value;
					$count++; 
				}
				return ($operacion=='SUM') ? $suma : $suma / $count;
				break;			
			case 'MIN': 
			case 'MAX': 
				$min = null;
				$max = null;
				$break = false;
				for ($i=$record; $i < $this->row_count();$i++) {
					for ($j=0; $j <= $level; $j++)
						$cod[$j] = $this->get_item($i, $this->folders[$j]->key_group);
					
					for ($j=0; $j <= $level; $j++) {
						if ($cod[$j] != $cod_ini[$j]) {
							$break = true; 
							break;
						}
					}
					if ($break) break; 
					
					$value = $this->get_item($i, $field);
					if ($min===null)
						$min = $value;
					else if ($this->es_menor($field, $value, $min))
						$min = $value;
							
					if ($max===null)
						$max = $value;
					else if ($this->es_mayor($field, $value, $max))
						$max = $value;
				}
				return ($operacion=='MIN') ? $min : $max;
				break;			
		}
		return 'NO_IMPLEMENTADO';
	}
	function draw_field_level($field, $record, $level) {
		// Solo aquellos campos que tienen asociado un control se dibujan
		if (!isset($this->controls_level[$field][$level]))
			return '';
		
		$dato = $this->get_item_level($record, $field, $level);
		$row = $this->redirect($record);
		if ($this->entrable)
			$dato_con_formato = $this->controls_level[$field][$level]->draw_entrable($dato, $row);					
		else
			$dato_con_formato = $this->controls_level[$field][$level]->draw_no_entrable($dato, $row);
		return $dato_con_formato;
	}
	function fill_record(&$temp, $record, $level=self::K_LEVEL_DETALLE) {
		parent::fill_record($temp, $record);

		if ($level!=self::K_LEVEL_DETALLE) {
			// dibuja los campos
			for ($j=0; $j < count($this->fields); $j++) {
				$field = $this->fields[$j]->name;
				$dato_con_formato = $this->draw_field_level($field, $record, $level);
				$temp->setVar($this->label_record.'.'.$field, $dato_con_formato);
			}
		}
		
		// obtiene los values de los labels
		$values = array();
		for($j=0; $j < count($this->folders); $j++)
			$values[$this->folders[$j]->key_group] = $this->get_item($record, $this->folders[$j]->key_group);

		// arma el id del TR
		$key_group = $values[$this->folders[0]->key_group];
		// Si veiene un '-' se cambia por 'X' (debe ser cualquier caracter distinto de '-' y de '_
		$key_group = str_replace("-", "X", $key_group);
		$id = $this->label_record.'_'.$key_group;
		$level_final = $level==self::K_LEVEL_DETALLE ? count($this->folders) - 1 : $level;
		for($j=1; $j <= $level_final; $j++) {
			$key_group = $values[$this->folders[$j]->key_group];
			// Si veiene un '-' se cambia por 'X' (debe ser cualquier caracter distinto de '-' y de '_'
			$key_group = str_replace("-", "X", $key_group);
			$id .= '-'.$key_group;
		}
		if ($level==self::K_LEVEL_DETALLE)
			$id .= '-'.$this->redirect($record);
		$temp->setVar($this->label_record.'.DW_TR_ID', $id);
				
		// arma el html del DWT_LEVEL
		if ($level==self::K_LEVEL_DETALLE) {
			if ($this->onclick == '')
				$onclick ='';
			else {
				$onclick = 'onclick="'.$this->onclick.'"';
			}
			$html_folder = '<div class="tier'.(count($this->folders) + 1).'"><a href="#" '.$onclick.' class="doc"></a>'.$this->draw_field($this->documento_field_display, $record).'</div>';
		}
		else {
			$temp->setVar($this->label_record.".DW_TR_CSS", self::css_folder);
			$name_group = $this->folders[$level]->name_group;			
			$html_folder = '<div class="tier'.($level + 1).'"><a href="#" onclick="toggleRows(this)" class="folder"></a>'.$this->draw_field_level($name_group, $record, $level).'</div>';
		}
		$temp->setVar($this->label_record.'.DWT_LEVEL', $html_folder);
	}
	function fill_template(&$temp) {
		// en $values_old queda el valor anterior de las columnas consideradas folders
		$values_old = array();
		for($j=0; $j < count($this->folders); $j++)
			$values_old[$this->folders[$j]->key_group] = '';
			 			
		for ($i=0; $i < $this->row_count(); $i++) {
			// obtiene los valores
			$values = array();
			for($j=0; $j < count($this->folders); $j++)
				$values[$this->folders[$j]->key_group] = $this->get_item($i, $this->folders[$j]->key_group);
				
			// compara los valores para determinar si existe cambio de level
			for($j=0; $j < count($this->folders); $j++) {				
				if ($values_old[$this->folders[$j]->key_group] != $values[$this->folders[$j]->key_group]) {
					$temp->gotoNext($this->label_record);
					$this->fill_record($temp, $i, $j);
					
					$values_old[$this->folders[$j]->key_group] = $values[$this->folders[$j]->key_group];
					
					// limpia todos los valores old para los sgtes niveles
					for($k=$j+1; $k < count($this->folders); $k++)			
						$values_old[$this->folders[$k]->key_group] = '';
				}
			}
			
			// dibuja el record
			$temp->gotoNext($this->label_record);
			$this->fill_record($temp, $i);
		}
	}
	function get_values($ind_read_dato, $ind_write_dato) {
		// los constrosl obtienen su valor del array $_POST y deben usar el indice absoluto (pueden existir eliminaciones de lineas)
		$row = $this->redirect($ind_read_dato);
		for ($k=0; $k < count($this->fields); $k++) {
			$field = $this->fields[$k]->name;
			for ($level=0; $level <= count($this->folders); $level++) {
				if (!isset($this->controls_level[$field][$level]))
					continue;
				if ($this->controls_level[$field][$level]->have_POST) {
					$protect = $this->eval_protect($ind_read_dato, $field);						
					if (!$protect) {
						$value_post = $this->controls_level[$field][$level]->get_values_from_POST($row);
						$value_post = $this->parsearparametros($value_post);
						$this->set_item($ind_write_dato, $field, $value_post);
					}
				}
			}
		}
	}
	function get_values_from_POST() {
		if (!$this->entrable)
			return ;		
		
		// Los controls del nivel de detalle
		parent::get_values_from_POST();
		
		// en $values_old queda el valor anterior de las columnas consideradas folders
		$values_old = array();
		$ind_old = array();
		for($j=0; $j < count($this->folders); $j++) {
			$values_old[$this->folders[$j]->key_group] = '';
			$ind_old[$this->folders[$j]->key_group] = '';
		}
		
		// Obtiene valores de los controls a nivel de folders
		for ($i=0; $i < $this->row_count(); $i++) {
			// obtiene los valores
			$values = array();
			for($j=0; $j < count($this->folders); $j++)
				$values[$this->folders[$j]->key_group] = $this->get_item($i, $this->folders[$j]->key_group);
			
			// compara los valores para determinar si existe cambio de level
			for($j=0; $j < count($this->folders); $j++) {
				if ($values_old[$this->folders[$j]->key_group] == $values[$this->folders[$j]->key_group]) {
					$this->get_values($ind_old[$this->folders[$j]->key_group], $i);
				}
				else {
					$this->get_values($i, $i);
					$values_old[$this->folders[$j]->key_group] = $values[$this->folders[$j]->key_group];
					$ind_old[$this->folders[$j]->key_group] = $i; 
				}
			}
		}
	}
}
?>