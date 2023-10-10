<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class w_output extends w_base {
	var $dw;
	var $sql_original;
	
	// paginación
	var $row_per_page = 18;
	var $current_page = 1;
	var $first_page = 1;
	var $last_page = 0;
	var $cant_page_visible = 10;
	public $row_count_output = 0;
	var $headers = array();
	
	// botones
	var $b_add_visible = true;
	var $b_export_visible = true;
	
	var $nom_filtro = '';		 // string con los filtros activados
	var $field_sort = '';
	var $sort_asc_desc = 'ASC';
	
	var $css_claro = "claro";//datawindow::css_claro;
	var $css_oscuro = "oscuro";//datawindow::css_oscuro;
	
	static function f_viene_del_menu($modulo) {
		if (!session::is_set($modulo)) {
			return true;
		}
		return false;
	}	
	function w_output($nom_tabla, $sql, $cod_item_menu) {
		parent::w_base($nom_tabla, $cod_item_menu);

		// template
		$this->nom_template = "wo_".$this->nom_tabla.".htm";
		if (defined('K_CLIENTE')) {
			if (file_exists(K_CLIENTE.'/'.$this->nom_template))
				$this->nom_template = K_CLIENTE.'/'.$this->nom_template;
		}
		
		$this->dw = new datawindow($sql, "wo_registro");
		$this->sql_original = $sql;

		// No puede venir ningun {KEYn} en los outputs, no se usan parametros por ahora
		$pos = strpos($sql, '{KEY1}');
		if ($pos!==false)
			$this->error("VM = No esta implementado el recibir parametros {KEYn} en los outputs.");
			
		// valida que el select del output parta con "select " y no contenga espacios en blanco u otro caracter
		// Esto es necesario porque en el save del input obtiene el select del output y se salta los 6 1ros caracteres 
		// y se asume que parte con "select "
		if ($sql!='' && substr(strtoupper($sql), 0, 6) != 'SELECT')
			$this->error("El select del output debe partir con 'select' y no puede tener blancos antes de la palabra select");
	}
	function set_count_output() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->row_count_output = $db->count_rows_sql($this->dw->sql);
	}
	function retrieve_totales() {
		$this->set_count_output();
		$this->get_totales();
		$this->last_page = Ceil($this->row_count_output / $this->row_per_page);		
	}
	function retrieve() {
		$this->retrieve_totales();
		$this->goto_page(1);
	}
	function add_record_desde() {
		session::set("add_".$this->nom_tabla."_desde", "add_".$this->nom_tabla."_desde"); 
		$this->detalle_record(K_NEW_RECORD);
	}
	function add_record_desde_new() {
		session::set("add_".$this->nom_tabla."_desde", "add_".$this->nom_tabla."_desde"); 
		$this->detalle_record_new(K_NEW_RECORD);
	}
	function detalle_record_desde($modificar = false) {
		/* Usada en boton modifcar empresa en las ventanas de cot, nv, gd, etc
		 * En general se debe usar en vez de
		 * 	$this->retrieve()
		 * 	$this->detalle_record(0)
		 * 
		 * esta funcion
		 * 	$this->detalle_record(true o false)
		 * 
		 * Esto porque el retrieve hace un redraw el cual evita que se ejecute la 2da linea $this->detalle_record(0)
		 * y no funciona.
		 */
		if ($modificar)
			session::set("mod_".$this->nom_tabla."_desde", "mod_".$this->nom_tabla."_desde");
		else 
			session::set("goto_".$this->nom_tabla."_desde", "goto_".$this->nom_tabla."_desde");
		
		// retrieve
		$this->retrieve_totales();
		$this->set_current_page(0);
		$this->save_SESSION();
		$this->detalle_record(0);	// Se va siempre al primer registro
		// NO se debe borrar el wo de la sesion porque es usado en el input
		//$this->delete_SESSION();		
	}
	
	function habilita_boton(&$temp, $boton, $habilita) {
		//busca si tiene Imagen en K_CLIENTE
		$ruta_imag = '../../../../commonlib/trunk/images/';
		if (defined('K_CLIENTE')) {
			if (file_exists('../../images_appl/'.K_CLIENTE.'/images/b_'.$boton.'.jpg')){
				$ruta_imag = '../../images_appl/'.K_CLIENTE.'/images/';
			}
		}
		
		if ($habilita){
			$temp->setVar("WO_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="'.$ruta_imag.'b_'.$boton.'.jpg" type="image" '.
																							'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\''.$ruta_imag.'b_'.$boton.'_click.jpg\',1)" '.
																							'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
																							'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\''.$ruta_imag.'b_'.$boton.'_over.jpg\',1)" '.
																							'/>');
		}else{
			$temp->setVar("WO_".strtoupper($boton), '<img src="'.$ruta_imag.'b_'.$boton.'_d.jpg"/>');
		}
	}
	function paginacion(&$temp) {
		$this->habilita_boton($temp, 'back', true);

		$this->habilita_boton($temp, 'first', ($this->current_page > 1));
		$this->habilita_boton($temp, 'prev', ($this->current_page > 1));
			
		// paginacion
		$paginacion = "";
		for ($i=$this->first_page; ($i < $this->first_page + $this->cant_page_visible) && ($i <= $this->last_page); $i++) {
			if ($i == $this->current_page)
				$paginacion .= '<font color="#FF0000"><strong>'.$i.'</strong></font> ';
			else
				$paginacion .= '<a href="#" onClick="document.getElementById(\'wo_hidden\').value='.$i.';  document.output.submit();" class="cant_paginas">'.$i.'</a> ';
		}
		$temp->setVar("WO_PAGES", $paginacion);
			
		$this->habilita_boton($temp, 'next', ($this->current_page < $this->last_page));
		$this->habilita_boton($temp, 'last', ($this->current_page < $this->last_page));		
	}
	function redraw_item(&$temp, $ind, $record) {
		$temp->gotoNext("wo_registro");
		if ($ind % 2 == 0) {
			$temp->setVar("wo_registro.WO_TR_CSS", $this->css_claro);
			$temp->setVar("wo_registro.WO_DETALLE", '<input name="b_detalle_'.$ind.'" id="b_detalle_'.$ind.'" value="'.$ind.'" src="../../../../commonlib/trunk/images/lupa1.jpg" type="image">');
		}
		else {
			$temp->setVar("wo_registro.WO_TR_CSS", $this->css_oscuro);
			$temp->setVar("wo_registro.WO_DETALLE", '<input name="b_detalle_'.$ind.'" id="b_detalle_'.$ind.'" value="'.$ind.'" src="../../../../commonlib/trunk/images/lupa2.jpg" type="image">');
		}
		$this->dw->fill_record($temp, $record);
		
		
		
		//////////////////
		// llama al js para grabar scrol
		$temp->setVar("wo_registro.WO_DETALLE", '<input name="b_detalle_'.$ind.'" id="b_detalle_'.$ind.'" value="'.$ind.'" src="../../../../commonlib/trunk/images/lupa2.jpg" type="image" onClick="graba_scroll(\''.$this->nom_tabla.'\');">');
		
		if (session::is_set('W_OUTPUT_RECNO_'.$this->nom_tabla)) {	
			$rec_no = session::get('W_OUTPUT_RECNO_'.$this->nom_tabla);	
			if ($rec_no==$ind) {
				session::un_set('W_OUTPUT_RECNO_'.$this->nom_tabla);	
				$temp->setVar("wo_registro.WO_TR_CSS", 'linea_selected');
			}
		}
		//////////////////
	}
	function redraw_item_empty(&$temp, $ind) {
		$temp->gotoNext("wo_registro");
		if ($ind % 2 == 0)
			$temp->setVar("wo_registro.WO_TR_CSS", $this->css_claro);
		else
			$temp->setVar("wo_registro.WO_TR_CSS", $this->css_oscuro);
	}
	function add_header($header_columna) {
		  $this->headers[$header_columna->field] = $header_columna;
	}
	function remove_header($field) {
		unset($this->headers[$field]);
	}	
	function make_filtros() {
		$this->nom_filtro = '';
		$filtro_total = '';
		$indices = array_keys($this->headers);
		for ($i=0; $i<count($this->headers); $i++) {
			$filtro = $this->headers[$indices[$i]]->make_filtro();
			if ($filtro != '') {
				$filtro_total .= $filtro;
				$this->nom_filtro .= $this->headers[$indices[$i]]->make_nom_filtro()."; ";
			}
		}
		// Elimina ; final
		if ($this->nom_filtro != '')
			$this->nom_filtro = substr($this->nom_filtro, 0, strlen($this->nom_filtro)-2);
		
		$sql = $this->sql_original;
		if ($filtro_total != '') {
			$pos = strrpos(strtoupper($sql), 'WHERE');
			if ($pos === false) {
				$pos = strrpos(strtoupper($sql), 'GROUP');
				if ($pos === false) {
					$pos = strrpos(strtoupper($sql), 'ORDER');
					if ($pos===false)
						$sql = $sql.' WHERE '.substr($filtro_total, 0, strlen($filtro_total) - 4);	// borra 'and '
					else
						$sql = substr($sql, 0, $pos).' WHERE '.substr($filtro_total, 0, strlen($filtro_total) - 4).' '.substr($sql, $pos);	// borra 'and ' y agrega el resto
				}
				else
					$sql = substr($sql, 0, $pos).' WHERE '.substr($filtro_total, 0, strlen($filtro_total) - 4).' '.substr($sql, $pos);	// borra 'and ' y agrega el resto
			}
			else
				$sql = substr($sql, 0, $pos).' WHERE '.$filtro_total.' '.substr($sql, $pos + 5);
		}

		// Aplica un order by si ha sido seleccionado por el usuario
		if ($this->field_sort != '') {
			$pos_order = strrpos(strtoupper($sql), 'ORDER');	// posible error si es que existe un nombre de campo que contenga la palabra ORDER !!
			if ($pos_order===false)
				$pos_order = strlen($sql);
			$sql = substr($sql, 0, $pos_order - 1);
			
			$sql .= ' ORDER BY ';
			$lista = explode(",", $this->headers[$this->field_sort]->field_bd_order);
			for ($i=0; $i<count($lista); $i++)
				$sql .= $lista[$i].' '.$this->sort_asc_desc.",";
			$sql = substr($sql, 0, strlen($sql)-1);
		}
		$this->dw->set_sql($sql);
	}
	function find_header($field, $value) {
		$this->headers[$field]->set_value_filtro($value);
		$this->make_filtros();
		$this->retrieve();		
	}
	function get_totales() {
		$sql = strtoupper($this->dw->sql);
		$sql_final = "";
		$indices = array_keys($this->headers);
		for ($i=0; $i<count($this->headers); $i++) {
			$operacion = $this->headers[$indices[$i]]->operacion_accumulate;
			if ($operacion != '')
				$sql_final .= $operacion."(".$this->headers[$indices[$i]]->field_bd.") ".$indices[$i].",";
		}
		if ($sql_final=='')
			return;
			
		$pos_from = strrpos($sql, 'FROM');		// posible error si es que existe un nombre de campo que contenga la palabra FROM !!
		$sql_final = "select ".substr($sql_final, 0, strlen($sql_final) - 1)." ".substr($sql, $pos_from);
		$pos_order = strrpos($sql_final, 'ORDER');	// posible error si es que existe un nombre de campo que contenga la palabra ORDER !!
		if ($pos_order===false)
			$pos_order = strlen($sql);
		$sql_final = substr($sql_final, 0, $pos_order - 1);
			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql_final);
		for ($i=0; $i<count($this->headers); $i++) {
			$operacion = $this->headers[$indices[$i]]->operacion_accumulate;
			if ($operacion != '')
				$this->headers[$indices[$i]]->valor_accumulate = $result[0][$indices[$i]];
		}
	}
	function _redraw() {
		chdir($this->work_directory);
		$temp = new Template_appl($this->nom_template);
		$this->make_menu($temp);

		$temp->setVar("WO_RUTA_MENU", $this->ruta_menu);
		$temp->setVar("WO_FECHA_ACTUAL", 'Fecha Actual: '.$this->current_date());
		$temp->setVar("WO_NOM_USUARIO", $this->nom_usuario);
		
		// Botones de arriba		
		if ($this->b_add_visible)
			$this->habilita_boton($temp, 'add', $this->get_privilegio_opcion_usuario($this->cod_item_menu, $this->cod_usuario)=='E');
		if ($this->b_export_visible)
			$this->habilita_boton($temp, 'export', true);			

		// headers
		for ($j=0; $j < count($this->dw->fields); $j++) {
			$field = $this->dw->fields[$j]->name;
			if (isset($this->headers[$field])) {
				$this->headers[$field]->draw_header($temp, $this->field_sort);
				$operacion = $this->headers[$field]->operacion_accumulate;
				if ($operacion != '') 
					$temp->setVar($operacion."_".$field, $this->headers[$field]->draw_valor_accumulate());
			}
		}
		$temp->setVar("WO_NOM_FILTRO", $this->nom_filtro);
		
		
		// Busca cual debe ser el primer link visible y lo deja en first_page
		if ($this->current_page < $this->first_page)
			$this->first_page = $this->current_page - ($this->cant_page_visible / 2);
		elseif (($this->current_page - $this->first_page) > ($this->cant_page_visible / 2))
			$this->first_page = $this->current_page - ($this->cant_page_visible / 2);
		if ($this->first_page < 1)
			$this->first_page = 1;

			
		// primer registro de la pagina
		$ind = $this->row_per_page * ($this->current_page - 1);
		
		// loop en los registros de la pagina visible
		$i = 0;
		while (($i < $this->row_per_page) && ($ind < $this->row_count_output)){
			$this->redraw_item($temp, $ind, $i);
			$i++;
			$ind++;
		}
		while ($i < $this->row_per_page){
			$this->redraw_item_empty($temp, $i);
			$i++;
		}

		$this->paginacion($temp);
		$this->redraw($temp);
		print $temp->toString();

		///////////
		if (session::is_set('W_OUTPUT_SCROLL_'.$this->nom_tabla)) {
			$scroll = session::get('W_OUTPUT_SCROLL_'.$this->nom_tabla);
			session::un_set('W_OUTPUT_SCROLL_'.$this->nom_tabla);
			print '<script type="text/javascript">haga_scroll('.$scroll.');</script>';
		}
		///////////
	}	
	function redraw(&$temp) {}	// funcion virtual por si se necesitan redibujar algo mas
	function field_key() {
		//esta funcion se implementa para cuando el nom_tabla es distinto al código KEY1, ejemplo en: alianse_tt/trunk/appl/gestion_operador/class_wo_gestion_operador.php
		return "COD_".strtoupper($this->nom_tabla);
	}
	function set_current_page($pagina) {
		$this->current_page = $pagina;
		$ind = $this->row_per_page * ($this->current_page - 1);
		if ($ind < 0) $ind = 0;
		if ($ind + $this->row_per_page < $this->row_count_output)
			$ind_final = $ind + $this->row_per_page;
		else
			$ind_final = $this->row_count_output;

		// se aumenta en 1 los parametros porque rownum parte en 1 y no en 0
		// se usan otras variables, porque las iniciales son usadas mas adelante
		$start_row = $ind + 1;
		$end_row = $ind_final + 1; 
		///////////
		// Hace el retrieve_bd  se debe pasar a una clase especial dw_output ***********
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if (K_TIPO_BD=="oci") {
			
			//////////////
			/* JV, 03-02-2015
			 * Esto cambios son para acelerar los output
			 * El cambio consiste en el select de adentro $sql_output_aux solicitar solo el campo llave 
			 * y luego ejecutar un select que traiga los datos de las demas columnas
			 * 
			 * para que opere se deben poner 3 marcas en el sql ver ejemplo HELEN->BOLETA
			 * el sql ejemplo siguiente no esta dentro del comentario
			 */
			$sql_output = $this->dw->sql;
			$pos1 = strpos(strtoupper($sql_output), "/*INI_CAMPOS*/");
			$pos2 = strpos(strtoupper($sql_output), "/*FIN_CAMPOS*/");
			$pos3 = strpos(strtoupper($sql_output), "/*FIN_CONDICION*/");		
			if ($pos1!==false && $pos2!==false && $pos3!==false) {
				$sql_output_aux = substr($sql_output, 0, $pos1).substr($sql_output, $pos2, strlen($sql_output)-$pos2);
				$sql = "SELECT *
						FROM 
						(
							SELECT
								r.*, ROWNUM as row_number 
								FROM
									( ".$sql_output_aux." ) r
								WHERE
								ROWNUM <= $end_row
						)
						WHERE $start_row <= row_number";

				$result = $db->build_results($sql);

				$sql = $this->dw->sql;
				
				$fin_sql = substr($sql, $pos3, strlen($sql)-$pos3);
				$field_key = "COD_".strtoupper($this->nom_tabla);
				$key = $this->headers[$field_key]->field_bd;
				
				//arma el sql
				if(count($result) > 0){
					$sql = substr($sql, 0, $pos3)." and $key in (";
					for ($i=0; $i < count($result); $i++) {
						$sql .= $result[$i][$field_key]; 
						if ($i < count($result)-1)
							$sql .= ","; 
					}
				
					$sql .= ")";
					$sql .= $fin_sql;
				}
				
				///////////////////////////////////////////////////////////////////////
				$stmt = oci_parse($db->con, $sql);
				oci_execute($stmt);
				// Lee los registros
				$result_arr = array();
				while($my_row = oci_fetch_array($stmt, OCI_BOTH + OCI_RETURN_NULLS )){
					$result_arr[] = $my_row;
				}
				$this->dw->data = $result_arr;
			}
			else{
				$sql = "SELECT *
						FROM 
						(
							SELECT
								r.*, ROWNUM as row_number 
								FROM
									( ".$this->dw->sql." ) r
								WHERE
								ROWNUM <= :end_row
						)
						WHERE :start_row <= row_number";
				$stmt = oci_parse($db->con, $sql);		    
			    oci_bind_by_name($stmt, ':start_row', $start_row);
			    oci_bind_by_name($stmt, ':end_row', $end_row);
			    oci_execute($stmt);
			    
			    // Lee los registros
				$result_arr = array();
				while($my_row = oci_fetch_array($stmt, OCI_BOTH + OCI_RETURN_NULLS )){
					$result_arr[] = $my_row;
				}
				$this->dw->data = $result_arr;
				$sql = $this->dw->sql;
			}
			
			// Se ejecuta el query del select original para obtener los nombres de los campos
			$db->query($sql);	 
			$this->dw->fields = $db->get_fields();
		}
		else {
			$sql = $this->dw->sql;
			$pos = strpos(strtoupper($sql), 'ORDER BY');
			if ($pos===false)
				$this->error('Los output deben tener un ORDER BY !!');
			$orderby = substr($sql, $pos);
			$orderby = str_replace("\t", " ", $orderby);	// reemplza tab por espacio
			$orderby = str_replace(",", ", ", $orderby);	// reemplza "," por ", " para el caso en que no se pusieron espacios
			$sql_output = substr($sql, 0, $pos);
			
			// rearma $orderby cambio los alias de tabla a R.
			$lista = explode(" ", $orderby);
			$orderby = "";
			for ($i=0; $i<count($lista); $i++) {
				$word = explode(".", $lista[$i]);
				if (count($word) > 1)
					$word = "R.".$word[1];
				else
					$word = $word[0];
				$orderby .= $word." ";
			}
			//////////////
			/* VMC, 21-01-2015
			 * Esto cambios son para acelerar los output
			 * El cambio consiste en el select de adentro $sql_output_aux solicitar solo el campo llave 
			 * y luego ejecutar un select que traiga los datos de las demas columnas
			 * 
			 * para que opere se deben poner 3 marcas en el sql ver ejemplo biggi wo_nota_venta
			 * el sql ejemplo siguiente no esta dentro del comentario
			 */  
			$pos1 = strpos(strtoupper($sql), "/*INI_CAMPOS*/");
			$pos2 = strpos(strtoupper($sql), "/*FIN_CAMPOS*/");
			$pos3 = strpos(strtoupper($sql), "/*FIN_CONDICION*/");
			if ($pos1!==false && $pos2!==false && $pos3!==false) {
				$sql_output_aux = substr($sql_output, 0, $pos1).substr($sql_output, $pos2, strlen($sql_output)-$pos2);
				$sql = "SELECT * 
						FROM (
							SELECT r.*, row_number() over ($orderby)  ROWNUMBER
							FROM ($sql_output_aux) R
						) A
						WHERE ROWNUMBER between $start_row and $end_row";
				$result = $db->build_results($sql);

				$sql = $this->dw->sql;
				$fin_sql = substr($sql, $pos3, strlen($sql)-$pos3);
				
				$field_key = $this->field_key();
				
				$key = $this->headers[$field_key]->field_bd;
				
				//arcma el sql
				if(count($result) > 0){
					$sql = substr($sql, 0, $pos3)." and $key in (";
					for ($i=0; $i < count($result); $i++) {
						$sql .= $result[$i][$field_key]; 
						if ($i < count($result)-1)
							$sql .= ","; 
					}
				
					$sql .= ")";
					$sql .= $fin_sql;
				}
				
				$result = $db->build_results($sql);
			}
			//////////////
			else {
				$sql = "SELECT * 
						FROM (
							SELECT r.*, row_number() over ($orderby)  ROWNUMBER
							FROM ($sql_output) R
						) A
						WHERE ROWNUMBER between $start_row and $end_row";
				$result = $db->build_results($sql);
			}
			
			$this->dw->data = $result;
			$this->dw->fields = $db->get_fields();
		}
		$this->dw->add_fields_computed();
		
		$this->dw->add_field('ROW');
		for($i=0; $i<count($this->dw->data); $i++) {
			$this->dw->data[$i]['__STATUS__'] = K_ROW_NOT_MODIFIED;			
			$this->dw->data[$i]['ROW'] = $ind + $i;			
		}
		$this->dw->row_count = count($this->dw->data);
		$this->dw->filter();
		$this->dw->is_modified = false;		
		
		// valida que no existena campos con nombre "_n" donde n puede ser un nro de registro
		// porque en java function del_line() busca y reemplaza los "_n" y poderia producirse un error
		for($i=0; $i<count($this->dw->fields); $i++) {
			$name = $this->dw->fields[$i]->name;
			for ($j=0; $j<100; $j++) {
				$pos = strpos($name, '_'.$j);
				if ($pos!==false) {
					$this->error("No pueden existir campos que contengan en su nombre _".$j." Se debe cambiar el nombre del campo.");
					return;
				}
			}
		}
	}
	function goto_page($pagina) {
		$this->set_current_page($pagina);
		$this->save_SESSION();
		$this->_redraw();
	}
	function detalle_record($rec_no) {
		session::set('DESDE_wo_'.$this->nom_tabla, 'desde output');	// para indicar que viene del output
		$url = $this->get_url_mantenedor();
		header ('Location:'.$url.'/wi_'.$this->nom_tabla.'.php?rec_no='.$rec_no.'&cod_item_menu='.$this->cod_item_menu);
	}
	function detalle_record_new($rec_no) {//este es funciona para el sistema INGTEC
		session::set('DESDE_wo_'.$this->nom_tabla, 'desde output');	// para indicar que viene del output
		session::set('HELP_EMPRESA','INGTEC');
		$url = $this->get_url_mantenedor();
		header ('Location:'.$url.'/wi_'.$this->nom_tabla.'.php?rec_no='.$rec_no.'&cod_item_menu='.$this->cod_item_menu);
	}
	function add() {
		$this->detalle_record(K_NEW_RECORD);
	}
	function export() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 900); //900 seconds = 15 minutes

		require_once dirname(__FILE__)."/../php_writeexcel-0.3.0/class.writeexcel_workbook.inc.php";
		require_once dirname(__FILE__)."/../php_writeexcel-0.3.0/class.writeexcel_worksheet.inc.php";
		
		$fname = tempnam("/tmp", "export.xls");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = $workbook->addworksheet($this->nom_tabla);
		
		// escribe encabezados
		# Create a format for the column headings
		$header =& $workbook->addformat();
		$header->set_bold();
		$header->set_color('blue');

		// titulos
		$columna = 0;
		for ($j=0; $j < count($this->dw->fields); $j++) { 
			$field = $this->dw->fields[$j]->name;
			// Solo los campos que tienen Header
			if (isset($this->headers[$field])) {
				$nom_header = $this->headers[$field]->nom_header;
				$worksheet->write(0, $columna,  $nom_header, $header);
				$columna++;
			}
		}
		
		// Exporta la data
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->make_filtros();
		$sql = $this->dw->get_sql();
		$res = $db->query($sql);
		$i = 0;
		while($my_row = $db->get_row()){
			if ($worksheet->_datasize > 7000000)
				break;
			$columna = 0;
			for ($j=0; $j < count($this->dw->fields); $j++) {
				$field = $this->dw->fields[$j]->name;
				// 	Solo los campos que tienen Header
				if (isset($this->headers[$field])) {
					if ($field=='ROW')
						$worksheet->write($i + 1, $columna, $i + 1);
					else
						$worksheet->write($i + 1, $columna, $my_row[$field]);
					$columna++;
				}
			}
			$i++;
		}
		if ($worksheet->_datasize > 7000000) {
			$worksheet->write($i + 1, 0, utf8_encode('No se completo la exportación de datos porque excede el máximo del tamaño de archivo 7 MB'), $header);
		}
		
		if($db->database_type=="oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}
		
		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=\"$this->nom_tabla\"");
		header("Content-Disposition: inline; filename=\"$this->nom_tabla.xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	}
	function first_page() {
		$this->goto_page(1);
	}
	function prev_page() {
		$this->goto_page($this->current_page - 1);
	}
	function last_page() {
		$this->goto_page($this->last_page);
	}
	function next_page() {
		$this->goto_page($this->current_page + 1);
	}
	function save_SESSION() {
	    session::set("wo_".$this->nom_tabla, $this);
		session::set($this->nom_tabla, $this->nom_tabla);
	}
	function delete_SESSION() {
		session::un_set("wo_".$this->nom_tabla);
		session::un_set($this->nom_tabla);
	}
	function set_sort($field_sort) {
		if ($this->field_sort == $field_sort) {
			if ($this->sort_asc_desc == 'ASC')
				$this->sort_asc_desc = 'DESC';
			else
				$this->sort_asc_desc = 'ASC';
		}
		else {
			$this->field_sort = $field_sort;
			$this->sort_asc_desc = 'ASC';
		}
		$this->make_filtros();
		$this->retrieve();		
	}
	function procesa_event() {
		if ($this->clicked_boton('b_detalle', $value_boton))
			$this->detalle_record($value_boton);		
		elseif ($this->clicked_boton('b_header', $value_boton)){
			$this->find_header($value_boton, $_POST['wo_hidden']);		
		}elseif(isset($_POST['wo_hidden']) && $_POST['wo_hidden']!='') {
			$wo_hidden = $_POST['wo_hidden'];
			if (is_numeric($wo_hidden))		//  link de paginas
				$this->goto_page($wo_hidden);
			else	// link de sort
				$this->set_sort($wo_hidden);
		}
		elseif(isset($_POST['b_add_x']))
			$this->add();
		elseif(isset($_POST['b_export_x']))
			$this->export();
		elseif(isset($_POST['b_first_x'])) 
			$this->first_page();
		elseif(isset($_POST['b_prev_x']))
			$this->prev_page();
		elseif(isset($_POST['b_last_x']))
			$this->last_page();
		elseif(isset($_POST['b_next_x']))
			$this->next_page();
		elseif(isset($_POST['b_back_x']))
			$this->presentacion();
		else 
			$this->goto_page($this->current_page);
	}
}
?>