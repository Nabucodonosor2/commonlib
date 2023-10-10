<?php
require_once(dirname(__FILE__)."/../auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

// status de un campo
define("K_ROW_NOT_MODIFIED", 'K_ROW_NOT_MODIFIED');
define("K_ROW_MODIFIED", 'K_ROW_MODIFIED');
define("K_ROW_NEW", 'K_ROW_NEW');
define("K_ROW_NEW_MODIFIED", 'K_ROW_NEW_MODIFIED');

class datawindow extends base {
	// BD
	var $sql = '';					// sql de la dw
	var $data = array();					// array con los datos
	private $redirect = array();
	private $redirect_delete = array();
	var $fields = array();			// array con los nombres de fields
	public $row_count = 0;			// cantidad de rows    *** es usada en w_output, exitiran en php5 las var friend ??
	private $row_count_no_filter = 0;
	private $row_count_delete = 0;
	var $entrable = false;		// indica si se debe usar formatos entrables o no entrables
	private $datawindow_entrable = true;	// true si permite pasar a entrable la dw, false en otro caso
	var $is_modified = false;
	var $label_record = '';
	var	$filter = '';
	var $fields_visible = array();
	var $fields_protect = array();
	var $fields_entrable = array();
	// para dibujar
	const css_claro = "claro";
	const css_oscuro = "oscuro";
	var $css_claro = self::css_claro;
	var $css_oscuro = self::css_oscuro;
	
	// botones add y del
	var $b_add_line_visible = false;
	var $b_del_line_visible = false;
	var $controls = array();
	var $nom_tabla = '_NO_SE_HA_ASIGNADO_(VER_DOCUMENTACION)';		// Solo se llena si la dw pertenece a un input, OJO se llena en w_input._habilitar()
	var $first_focus = '';		// Si se le asigna valor el add_line lo retorana al ajax para que deje el focus en el campo
	protected	$accumulate = array();
	protected $computed = array();

	// para verificado campos mandatory
	private $mandatorys = array();
	private $nom_fields = array();
	
	var $cod_usuario;
	var $nom_usuario;
	
	function datawindow($sql, $label_record='', $b_add_line_visible=false, $b_del_line_visible=false) {
		parent::base();
		$this->set_sql($sql);
		$this->filter = '';
		$this->label_record = $label_record;
		$this->b_add_line_visible = $b_add_line_visible;
		$this->b_del_line_visible = $b_del_line_visible;
		$this->cod_usuario = session::get("COD_USUARIO");	// viene del login
		$this->nom_usuario = session::get("NOM_USUARIO");	// viene del login
	}
	function get_sql() {
		return $this->sql;
	}
	function set_sql($sql) {
		$this->sql = $sql;
	}
	function add_field($field, $tipo=1) {
		if (!$this->field_exists($field)) {
			$pos = count($this->fields);
			$something = new StdClass();
			$something->name = $field;
			$something->numeric = $tipo;
			$this->fields[$pos] = $something;
		}
	}
	function add_fields_computed() {
		$indices = array_keys($this->computed);		// Obtiene los nombres de los computed
		for ($i=0; $i<count($this->computed); $i++) {
			$field = $indices[$i];
			$this->add_field($field);		// los computed son siempre numericos 		
		}
	}
	function add_fields_accumulate() {
		for ($i=0; $i<count($this->accumulate); $i++) {
			$field = $this->accumulate[$i];
			$field = 'SUM_'.$field;
			$this->add_field($field);		// los SUM son siempre numericos 		
		}
	}
	function retrieve_bd($sql) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->data = $db->build_results($sql);		
		$this->fields = $db->get_fields();
		$this->add_fields_computed();
		$this->add_fields_accumulate();
		
		for($i=0; $i<count($this->data); $i++)
			$this->data[$i]['__STATUS__'] = K_ROW_NOT_MODIFIED;			
		$this->row_count = count($this->data);
		$this->filter();
		$this->is_modified = false;		
		
		// valida que no existena campos con nombre "_n" donde n puede ser un nro de registro
		// porque en java function del_line() busca y reemplaza los "_n" y poderia producirse un error
		for($i=0; $i<count($this->fields); $i++) {
			$name = $this->fields[$i]->name;
			for ($j=0; $j<100; $j++) {
				$sufijo = '_'.$j;
				$len_sufijo = strlen($sufijo);
				if (strcmp(substr($name, strlen($name) - $len_sufijo, $len_sufijo), $sufijo)==0) {
					$this->error("No pueden existir campos que contengan en su nombre _".$j." Se debe cambiar el nombre del campo.");
					return;
				}
			}
		}
	}
	function reset() {
		$this->data = array();				
		$this->row_count_no_filter = 0;
		$this->row_count = 0;
		$this->row_count_delete = 0;
		$this->is_modified = false;		
	}
	function add_control($control) {
		$this->controls[$control->field] = $control;
	}
	function remove_control($field) {
		if (isset($this->controls[$field]))
			unset($this->controls[$field]);
	}	
	function retrieve() {
		/* IMPORTANTE:
		Dentro del sql definido para el input, debe designarese con el string "{KEY1} {KEY2}" donde se desee 
		que se reemplace por la llave leida desde el output
		*/
		$keys = func_get_args();
		$sql = $this->replace_KEYS($this->sql, $keys);
		$this->retrieve_bd($sql);
		$this->calc_computed();
	}
	function set_entrable_dw($entrable) {
		$this->datawindow_entrable = $entrable;
	}
	function set_entrable($field, $entrable) {
		$this->fields_entrable[$field] = $entrable;
	}
	function set_visible($field, $visible) {
		$this->fields_visible[$field] = $visible;
	}
	function set_protect($field, $protect) {
		$this->fields_protect[$field] = $protect;
	}
	function unset_protect($field) {
		if (isset($this->fields_protect[$field]))
			unset($this->fields_protect[$field]);
	}
	function eval_protect($record, $field) {
		if (!isset($this->fields_protect[$field]))
			return false;
		$eval = $this->fields_protect[$field];
		for ($j=0; $j < count($this->fields); $j++) {
			$dato = $this->get_item($record, $this->fields[$j]->name);
			$dato = str_replace("'", "\'", $dato);
			if ($this->fields[$j]->numeric==0)
				$dato = "'".$dato."'";	// los string deben ir entre comillas
			$eval = str_replace('['.$this->fields[$j]->name.']', $dato, $eval);
		}
		eval("\$res = ".$eval." ? true : false;");
		return $res;
	}
	function is_entrable($field) {
		if (!isset($this->fields_entrable[$field]))
			return true;
		return $this->fields_entrable[$field];
	}
	function is_visible($field) {
		if (!isset($this->fields_visible[$field]))
			return true;
		return $this->fields_visible[$field];
	}	
	function draw_field($field, $record) {
		if (!$this->is_visible($field))
			return '';
		
		if ($this->entrable && $this->is_entrable($field)) {
			$usar_formato_entrable = true;
			$protect = $this->eval_protect($record, $field);
			if ($protect)
				$usar_formato_entrable = false;
		}
		else
			$usar_formato_entrable = false;
		
		$dato = $this->get_item($record, $field);
		if (!isset($this->controls[$field]))
			$dato_con_formato = $dato;
		else {
			$row = $this->redirect($record);
			if ($usar_formato_entrable )
				$dato_con_formato = $this->controls[$field]->draw_entrable($dato, $row);					
			else
				$dato_con_formato = $this->controls[$field]->draw_no_entrable($dato, $row);
		}		
		return $dato_con_formato;
	}
	function fill_record(&$temp, $record) {
		$label_record = ($this->label_record == '') ? '' : $this->label_record.".";
		if ($record % 2 == 0) 
			$temp->setVar($label_record."DW_TR_CSS", $this->css_claro);
		else
			$temp->setVar($label_record."DW_TR_CSS", $this->css_oscuro);

		// DW_ROWNUM, es correlativo automatico que puede ser usado en las dws
		$temp->setVar($label_record.'DW_ROWNUM', $record + 1);

		// DISABLE_BUTTON, puede ser usado en los botones que se desee que queden habilitados o no de acuerdo
		// si la dw esta entrable o no
		if ($this->entrable)
			$temp->setVar($label_record.'DISABLE_BUTTON', '');
		else
			$temp->setVar($label_record.'DISABLE_BUTTON', 'disabled="disabled"');
		
		// los constrosl obtienen su valor del array $_POST y deben usar el indice absoluto (pueden existir eliminaciones de lineas)
		$row = $this->redirect($record);
		$temp->setVar($label_record.'DW_RECORD', $row);
		$temp->setVar($label_record.'DW_TR_ID', $this->label_record.'_'.$row);
		 
		if ($this->b_del_line_visible) {
			if ($this->entrable)
				$eliminar = '<img src="../../../../commonlib/trunk/images/b_delete_line.jpg" onClick="del_line(\''.$this->label_record.'_'.$row.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer"/>';
			else
				$eliminar = '<img src="../../../../commonlib/trunk/images/b_delete_line_d.jpg"/>';
			$temp->setVar($label_record."ELIMINAR_".strtoupper($this->label_record), $eliminar);
		}
		// dibuja los campos
		for ($j=0; $j < count($this->fields); $j++) {
			$field = $this->fields[$j]->name;
			$dato_con_formato = $this->draw_field($field, $record);
			$temp->setVar($label_record.$field, $dato_con_formato);
		}
	}
	function fill_template(&$temp) {
		for ($i=0; $i < $this->row_count(); $i++) {
			if ($this->label_record != '') {
				$temp->gotoNext($this->label_record);
				$this->fill_record($temp, $i);
			}
			else
				$this->fill_record($temp, $i);	
		}
		if ($this->b_add_line_visible) {
			if ($this->entrable)
				$agregar = '<img src="../../../../commonlib/trunk/images/b_add_line.jpg" onClick="add_line(\''.$this->label_record.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer"/>';
			else 
				$agregar = '<img src="../../../../commonlib/trunk/images/b_add_line_d.jpg"/>';
			$temp->setVar("AGREGAR_".strtoupper($this->label_record), $agregar);
		}

		// dibuja los datos de los los accumulate
		for ($i=0; $i<count($this->accumulate); $i++) {		
			$field = $this->accumulate[$i];
			$field = 'SUM_'.$field;
			if ($this->row_count() > 0)
				$dato_con_formato = $this->controls[$field]->draw_entrable($this->get_item(0, $field), 0);
			else
				$dato_con_formato = $this->controls[$field]->draw_entrable(0, 0);			
			$temp->setVar($field, $dato_con_formato);
		}
	}
	function habilitar(&$temp, $habilita) {
		if ($this->datawindow_entrable)
			$this->entrable = $habilita;
		else
			$this->entrable = false;
		$this->fill_template($temp);
	}
	function field_exists($field) {
		/* Retorna true si el campo existe en la dw; false sino */
		for($i=0; $i< count($this->fields); $i++)
			if ($this->fields[$i]->name==$field)
				return true;
		return false;		
	}
	function redirect($row, $row_buffer='primary') {
		if ($row_buffer=='primary')
			return $this->redirect[$row];
		elseif ($row_buffer=='delete')
			return $this->redirect_delete[$row];
		else
			$this->error('Buffer no implementado');
	}
	function un_redirect($row) {
		$pos = array_search($row, $this->redirect);
		if ($pos===false)
			$pos = array_search($row, $this->redirect_delete);
		return $pos;
	}
	function valida_row_field($action, $row, $field, $row_buffer='primary') {
		if ($row < 0 || $row >= $this->row_count($row_buffer)) {
			$this->error(get_class($this).'.'.$action.'. ROW OUT RANGE, row:'.$row.' field:'.$field.' TOT_COUNT=:'.$this->row_count($row_buffer));
			return false;
		}
		if ($field=='__DELETE__' || $field=='__VISIBLE__' || $field=='__STATUS__')
			return true;			

		if ($this->field_exists($field))
			return true;

		$this->error(get_class($this).'.'.$action.'. FIELD INEXISTENTE, row:'.$row.' field:'.$field);
		return false;
	}
	function get_item($row, $field, $row_buffer='primary') {
		$field = strtoupper($field);
		if (substr($field, 0, 4)=='SUM_' && $this->row_count()==0)	// es un campo acumulate
			return 0;
		$this->valida_row_field($this->label_record.'.get_item', $row, $field, $row_buffer);
		
		$var = $this->data[$this->redirect($row, $row_buffer)][$field];
		if ($var instanceof DateTime) {
		    // true
		    $date = $var;
		    $result_fecha = $date->format('d/m/Y');
		    return $result_fecha;
		}
		else{
		    return $this->data[$this->redirect($row, $row_buffer)][$field];
		}
		
	}
	function set_item($row, $field, $dato, $row_buffer='primary', $actualiza_status=true) {
		$field = strtoupper($field);
		$this->valida_row_field($this->label_record.'.set_item', $row, $field, $row_buffer);

		// si no existe cambio no hace nada		
		if ($field != '__DELETE__' && $field != '__VISIBLE__' && $field != '__STATUS__') {
			$dato_old = $this->get_item($row, $field, $row_buffer);
			/*
			 * VMC, 4/01/2010 
			 * Cuando viene el valor cero '0' y al comprar $valor=='' da true, al comprar $valor=='0' tambien da true
			 * por esta razon se debe comparar el largo del string tambien
			 */
			if ($dato_old == $dato && strlen($dato_old)==strlen($dato))
				return;
		}
		// setea el dato
		$row_final = $this->redirect($row, $row_buffer);
		$this->data[$row_final][$field] = $dato;
		
		// actualiza el status
		if ($field != '__DELETE__' && $field != '__VISIBLE__' && $field != '__STATUS__' && $actualiza_status) {
			$this->is_modified = true;		
			$status_old = $this->data[$row_final]['__STATUS__'];
			if ($status_old==K_ROW_NEW || $status_old==K_ROW_NEW_MODIFIED )
				$status = K_ROW_NEW_MODIFIED;
			else
				$status = K_ROW_MODIFIED;
			$this->data[$row_final]['__STATUS__'] = $status;
		}
	}
	function get_fields_sin_where($sql) {
		// elimina el where
		$pos = strrpos(strtoupper($sql), 'WHERE');
		if ($pos === false)
			$sql = $sql;						// nada
		else
			$sql = substr($sql, 0, $pos);			
		
		// inserta un top 1 para que no se muy lento 
		$pos = strpos(strtoupper($sql), 'SELECT');
		if ((K_TIPO_BD=="mssql")||(K_TIPO_BD=="sqlsrv")) {
			if ($pos!==false)
				$sql = 'SELECT top 1 '.substr($sql, $pos + strlen('SELECT'));
		}			
		elseif (K_TIPO_BD=="mysql")
			$sql = $sql.' limit 1';			
		elseif (K_TIPO_BD=="oci")
			$sql = $sql.' WHERE ROWNUM = 1';			
		else
			$this->error('datawindow::get_fields_sin_where, top 1 NO implementado para '.K_TIPO_BD);
		
		// ejecuta el select
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db-> query($sql);
		return $db->get_fields();
	}
	function insert_row($row = -1) {
		// -1 indica al final
		if (count($this->fields)==0) {
			$this->fields = $this->get_fields_sin_where($this->sql);
			$this->add_fields_computed();
			$this->add_fields_accumulate();
		}
		$registro = array();
		for ($i=0; $i < count($this->fields); $i++) {
			$field = $this->fields[$i]->name;
			$registro[$field]	= '';
			// Caso especial para los checkbox, que debe incializarse en el valor false
			if (isset($this->controls[$field])) {
				if (get_class($this->controls[$field]) == 'edit_check_box')
					$registro[$field] = $this->controls[$field]->value_false;
			}
		}
		$registro['__STATUS__'] = K_ROW_NEW;
		if ($row == -1) {
			$this->data[] = $registro;
			$this->data[$this->row_count]['__VISIBLE__'] = 'S';
			$this->redirect[$this->row_count_no_filter] = $this->row_count;
			$row = $this->row_count();
		}
		else {
			$this->redirect[] = 0;	// agrega 1 elemento
			for ($i=$this->row_count_no_filter; $i >= $row; $i--)
				$this->redirect[$i + 1] = $this->redirect[$i];
			
			$this->data[] = $registro;
			$this->data[$this->row_count]['__VISIBLE__'] = 'S';
			$this->redirect[$row] = $this->row_count; 
		}			
		$this->row_count++;
		$this->row_count_no_filter++;	
		return $row;
	}
	function set_filter($filter) {
		$this->filter = $filter;
	}
	function filter() {
		$this->row_count_no_filter = 0;
		$this->row_count_delete = 0;
		for ($i=0; $i < $this->row_count; $i++) {
			if ($this->filter == '')
				$this->data[$i]['__VISIBLE__'] = 'S';
			else {
				$eval = $this->filter;
				for ($j=0; $j < count($this->fields); $j++) {
					$dato = $this->data[$i][$this->fields[$j]->name];
					$dato = str_replace("'", "\'", $dato);
					if ($this->fields[$j]->numeric==0)
						$dato = "'".$dato."'";	// los string deben ir entre comillas
					$eval = str_replace($this->fields[$j]->name, $dato, $eval);
				}

				eval("\$res = ".$eval." ? true : false;");
				if ($res)
					$this->data[$i]['__VISIBLE__'] = 'S';
				else
					$this->data[$i]['__VISIBLE__'] = 'N';
			}
			if (isset($this->data[$i]['__DELETE__'])) {
				$this->redirect_delete[$this->row_count_delete] = $i;
				$this->row_count_delete++;			
			}
			elseif ($this->data[$i]['__VISIBLE__'] == 'S') {
				$this->redirect[$this->row_count_no_filter] = $i;
				$this->row_count_no_filter++;			
			}
		}
		$this->calc_computed();
	}
	function delete_row($row) {
		$this->set_item($row, '__DELETE__', 'S');		
		$this->is_modified = true;		
		$this->filter();
	}
	function row_count($row_buffer = 'primary') {
		if ($row_buffer == 'primary')
			return $this->row_count_no_filter;
		elseif ($row_buffer == 'filter')
			return $this->row_count - $this->row_count_no_filter;
		if ($row_buffer == 'delete')
			return $this->row_count_delete;
		else
			$this->error("Tipo de buffer no implementado, buffer=".$row_buffer);		
	}
	function get_status_row($row, $row_buffer = 'primary') {
		return $this->get_item($row, '__STATUS__', $row_buffer);
	}
	function set_status_row($row, $status, $row_buffer = 'primary') {
		return $this->set_item($row, '__STATUS__', $status, $row_buffer, false);
	}
	function get_values_from_POST() {
		if (!$this->entrable)
			return;		
		for ($i=0; $i < $this->row_count(); $i++) {
			for ($j=0; $j < count($this->fields); $j++) {
				$field = $this->fields[$j]->name;
				if (!$this->is_visible($field) || !$this->is_entrable($field) || !isset($this->controls[$field]))
					continue;
				
				// Los campos tipo SUM_ solo tienen valor en $i==0
				if ($i==0 && substr($field, 0, 4)=='SUM_')
					$row = 0;
				else
					// los constrosl obtienen su valor del array $_POST y deben usar el indice absoluto (pueden existir eliminaciones de lineas)
					$row = $this->redirect($i);
				
				// Los campos tipo SUM_ solo tienen valor en $i==0
				if ($row>0 && substr($field, 0, 4)=='SUM_')
					continue;
					
				if ($this->controls[$field]->have_POST) {
					$protect = $this->eval_protect($i, $field);						
					if (!$protect) {
						$value_post = $this->controls[$field]->get_values_from_POST($row);
						$value_post = $this->parsearparametros($value_post);
						$this->set_item($i, $field, $value_post);
					}
				}
			}
		}
	}
	function validate() {
		for ($i=0; $i < $this->row_count(); $i++) {
			for ($j=0; $j < count($this->fields); $j++) {
				$field = $this->fields[$j]->name;
				if (!$this->is_visible($field) || !$this->is_entrable($field) || !isset($this->controls[$field]))
					continue;

				$value = $this->get_item($i, $field);
				$err = $this->controls[$field]->validate($value);
				if ($err != '')
					return $err;
			}
		}
		
		return '';	
	}
	function export_to_excel($file_name) {
		require_once dirname(__FILE__)."/../php_writeexcel-0.3.0/class.writeexcel_workbook.inc.php";
		require_once dirname(__FILE__)."/../php_writeexcel-0.3.0/class.writeexcel_worksheet.inc.php";
		
		$fname = tempnam("/tmp", "export.xls");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = $workbook->addworksheet($file_name);
		
		// escribe encabezados
		# Create a format for the column headings
		$header =& $workbook->addformat();
		$header->set_bold();
		//$header->set_size(12);
		$header->set_color('blue');

		// titulos
		$columna = 0;
		for ($j=0; $j < count($this->fields); $j++) { 
			$field = $this->fields[$j]->name;
			if ($this->is_visible($field)) {
				$worksheet->write(0, $columna,  $field, $header);
				$columna++;
			}
		}
		
		//datos
		for ($i=0; $i < $this->row_count(); $i++) {
			$columna = 0;
			for ($j=0; $j < count($this->fields); $j++) {
				$field = $this->fields[$j]->name;
				if ($this->is_visible($field)) {
					$worksheet->write($i + 1, $columna,  $this->get_item($i, $field));
					$columna++;
				}
			}
		}

		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=\"$file_name\"");
		header("Content-Disposition: inline; filename=\"$file_name.xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	}
	
	function export_to_excel_dos($file_name) {
		include_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/PHP_XLSXWriter_master/xlsxwriter.class.php");

		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		
		$writer = new XLSXWriter();
		$writer->setAuthor('Integrasystem');
		$sheet_name = $file_name;
		
		// titulos
		for ($j=0; $j < count($this->fields); $j++) { 
			$field = $this->fields[$j]->name;
			$header[] = $field;
		}
		$writer->writeSheetRow($sheet_name, $header);
		
		//datos
		for ($i=0; $i < $this->row_count(); $i++){
			$my_row = "";
			for ($j=0; $j < count($this->fields); $j++){
				$field	= $this->fields[$j]->name;
				$dato	= $this->get_item($i, $field);
				
				$my_row[] = utf8_encode($dato);
			}
			
			$writer->writeSheetRow($sheet_name, $my_row);
		}
		$writer->writeToStdOut();
		exit(0);

		unlink($fname);
	}
	
	function find_control($field) {
		$indices = array_keys($this->controls);
		for ($i=0; $i < count($this->controls); $i++) {
			if ($indices[$i]==$field)
				return $this->controls[$field];
		}
		return false;
	}
	function find_row($field, $valor_buscado, $row_buffer = 'primary') {
		// Busca $valor_buscado en $field, retorna la row donde lo encuentra  o -1 en otro caso
		for($i=0; $i < $this->row_count($row_buffer); $i++) {
			$valor = $this->get_item($i, $field, $row_buffer);
			if ($valor==$valor_buscado)
				return $i; 
		}
		return -1;
	}
	function set_first_focus($first_focus) { 
		$this->first_focus = $first_focus; 
	}
	function set_mandatory($field, $nom_field) {
		/* Marca un campo como mandatory
		   $field : identificador del campo
		   $nom_field : nombre del campo con el cual se desplegará el mensaje al usuario
		   $mandatory : True si se deseqa que el campo que marcado comomandatory; false en otro caso
		   ejemplo: 
		   set_mandatory('COD_EMPRESA', 'Código empresa')
		   // COD_EMPRESA es obligatorio y si no tiene valor el mensaje hara referencia a 'Código empresa'
		*/
		$this->mandatorys[] = $field;
		$this->nom_fields[] = utf8_encode($nom_field);
	}
	function unset_mandatory($field) {
		/* Desmarca un campo como mandatory
		   $field : identificador del campo
		   ejemplo: unset_mandatory('COD_EMPRESA');		// COD_EMPRESA deja de ser mandatory
		*/
		$pos = array_search($field, $this->mandatorys);
		if ($pos===false) return;
		
		$mandatorys = array();
		$nom_fields = array();
		for ($i=0; $i < count($this->mandatorys); $i++) {
			if ($i == $pos)
				continue;

			$mandatorys[] = $this->mandatorys[$i];
			$nom_fields[] = $this->nom_fields[$i];
		}
		$this->mandatorys = $mandatorys;
		$this->nom_fields = $nom_fields;
	}
	function mandatorys() {
		$script = '';
		for ($i=0; $i < count($this->mandatorys); $i++)
			$script .= 'mandatorys[mandatorys.length] = new campo_mandatory("'.$this->mandatorys[$i].'", "'.$this->nom_fields[$i].'");';
		return $script;
	}
	function computeds() {
		$script = '';
		$indices = array_keys($this->computed);
		for ($i=0; $i < count($this->computed); $i++) {
			$field = $indices[$i];
			$num_dec = $this->controls[$field]->num_dec;
			$script .= 'computeds[computeds.length] = new campo_computed("'.$field.'", "'.$this->computed[$field].'", '.$num_dec.');';
		}
		return $script;
	}
	function accumulate($field, $recalcular='', $validar_si_es_computed=true) {
		/* $field : campo sobre el que seralizar el sum
		 * $recalcular : codigo java script que se debe ejecutar cuando cambia el valor del campo SUM 
		 * $validar_si_es_computed: indica si debe hacerse la validacion de que solo acepte campos computed
		 *
		   	Los campos que debe ir sumando en el display quedan en el array accumulate.
			 el modo de uso es el siguiente:
			 supongamos una dw tiene un campo "MONTO" y se desea que se despliegue la suma del campo
			 en el htm se debe agregar "{SUM_MONTO}" donde se desea que se despliegue la suma
			 y en php $dw->accumulate('MONTO');	

			En el script $recalcular se puede incluir codigo que se debe ejecutar cada vez que se actaulice el campo SUM

			RESTRICCION: solo se puede hacer SUM de un campo COMPUTED.
						La complicación en un campo no computed pasa por obtener el valor OLD del campo,
						para actualizar el sum = sum - old_value + new_value

						Si no se usan computed, obligatoriamente deben ser control no modificable o fallara al editar los campos.	 
		*/
		if (!$this->is_computed($field) && $validar_si_es_computed)
			$this->alert('No se puede usar accumulate de un campo que no sea computed, campo: '.$this->label_record.'.'.$field.' o debe indicarse en los parametros de accumulate $validar_si_es_computed=false');
		$this->accumulate[] = $field;
		if (isset($this->controls[$field]->num_dec))
			$num_dec = $this->controls[$field]->num_dec;
		else
			$num_dec = 0;
		$this->add_control($control = new computed('SUM_'.$field, $num_dec));		

		// Agrega el script para reclacular si es necesario
		if ($recalcular!='')
			$control->edit_hidden->set_onChange($recalcular);
	}
	function set_computed($field, $formula, $num_dec=0, $add_control=true) {
		/* Los campos computed quedan en el array computed, 
		   el indice del array es el campoo computed y el contenido del array es la formula.
			 el modo de uso es el siguiente:
			 supongamos una dw tiene un campo "TOTAL" y es igual a CANTIDAD * PRECIO
			 y en php $dw->set_computed('TOTAL', '[CANTIDAD] * [PRECIO]');
			 
			 NOTA: Los computed solo pueden ser numericos !!!
		*/
		$this->computed[$field] = $formula;
		if ($add_control)
			$this->add_control(new computed($field, $num_dec));

		$res = preg_match_all("/\(? \[\w+\]/x", $formula, $coincidencias);
		for ($i=0; $i<$res; $i++) {
			$field_formula = $coincidencias[0][$i];
			$field_formula = substr($field_formula, 1, strlen($field_formula) - 2);	// elimina los "[" "]" del inicio y del final
			if (isset($this->controls[$field_formula])) {
				$java_script = $this->controls[$field_formula]->get_onChange();
				$java_script .= "computed(get_num_rec_field(this.id), '".$field."');";
				$this->controls[$field_formula]->set_onChange($java_script);				
			}
		}
	}
	function remove_computed($field) {
	    if (isset($this->computed[$field]))
	        unset($this->computed[$field]);
	}
	function is_computed($field) {
		return isset($this->computed[$field]); 	
	}	
	function calc_computed() {
		// calcula los computed
		$indices = array_keys($this->computed);		// Obtiene los nombres de los computed
		for($i=0; $i<$this->row_count(); $i++) {
			for ($j=0; $j<count($this->computed); $j++)	{
				$field = $indices[$j];
				$row = $this->redirect($i);
				$this->data[$row][$field] = 0;		// crea el campo
				$formula = $this->computed[$field];
				for ($k=0; $k < count($this->fields); $k++) {
					$pos = strpos($formula, '['.$this->fields[$k]->name.']');
					if ($pos!==false) {
						$dato = $this->data[$i][$this->fields[$k]->name];
						$dato = str_replace("'", "\'", $dato);
						// Se asume que los datos de un computed son siempre numericos
						if ($dato=='') $dato = 0;
						$formula = str_replace('['.$this->fields[$k]->name.']', $dato, $formula);
					}
				}
				$pos = strpos($formula, '[');
				if ($pos===false) {		// si quedan campos por evaluar  no ejecuta  eval
					// Pueden quedar campos por evaluar cuando el computed hace referencia a campos de otra dw o existe error
					eval("\$res = ".$formula.";");
					$this->data[$row][$field] = round($res, $this->controls[$field]->num_dec);
				}
			}
		}		
		
		// calcula los accumulate
		// inicializa las sumas
		if ($this->row_count()>0) {
			$row_0 = $this->redirect(0);
			for($i=0; $i<$this->row_count(); $i++) {
				for ($j=0; $j<count($this->accumulate); $j++) {		
					$field = 'SUM_'.$this->accumulate[$j];
					$row = $this->redirect($i);
					$this->data[$row][$field] = 0;		// crea el campo
					$this->data[$row_0][$field] += $this->get_item($i, $this->accumulate[$j]);
				}
			}
		}
	}
	function add_controls_producto_help() {
		/* Agrega los constrols standar para manejar la selección de productos con help					
			 Los anchos y maximos de cada campo quedan fijos, la idea es que sean iguales en todos los formularios
			 si se desean tamaños distintos se debe reiimplementar esta función
		*/
		
		if (isset($this->controls['PRECIO']))
			$num_dec = $this->controls['PRECIO']->num_dec;
		else
			$num_dec = 0;
		$java_script = "help_producto(this, ".$num_dec.");";

		$this->add_control($control = new edit_text_upper('COD_PRODUCTO', 25, 30));
		$control->set_onChange($java_script);
		$this->add_control($control = new edit_text_upper('NOM_PRODUCTO', 55, 100));
		$control->set_onChange($java_script);

		// Se guarda el old para los casos en que una validación necesite volver al valor OLD  
		$this->add_control($control = new edit_text_upper('COD_PRODUCTO_OLD', 30, 30, 'hidden'));
		
		// mandatorys
		$this->set_mandatory('COD_PRODUCTO', 'Código del producto');
		$this->set_mandatory('NOM_PRODUCTO', 'Descripción del producto');
	}
	function make_tabla_htm($nom_template) {
		/* Esta funcion se gatilla normalmente desde un js a traves de un ajax
		 * La idea es que se ejecute el retrieve y el resultado del mismo se tradusza usando el template recibido como parametro
		 * en codigo html que corresponda a la tabla con los datos del retirve.
		 * Se complementa con la funcion js, que esta en general.js copy_tabla_htm() donde se recibe esta tabla desde el ajax
		 * y se agrega al innerHTML de la tabla del DOM
		 */
		$this->retrieve();
		//chdir($wi->work_directory);
		$temp = new Template_appl($nom_template);
		$this->habilitar($temp, true);
		$label_record = $this->label_record;
		if (!isset($temp->content["blocks"][$label_record]))
			return '';
		$tabla = "";
		for ($i=0; $i<count($temp->content["blocks"][$label_record]); $i++) {
			$ind_block = array_keys($temp->content["blocks"][$label_record][$i]['values']);
			$linea = $temp->structure["blocks"][$label_record]['body'];
			for ($j=0; $j<count($temp->content["blocks"][$label_record][$i]['values']); $j++) {
				$linea = str_replace('{'.$ind_block[$j].'}', $temp->content["blocks"][$label_record][$i]['values'][$ind_block[$j]], $linea);
			}
			$tabla .= $linea;
		}
		// retorna para el ajax el html 
		print $tabla;
	}
}
?>