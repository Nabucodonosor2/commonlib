<?php
require_once(dirname(__FILE__)."/../auto_load.php");
require_once(dirname(__FILE__)."/class_datos_server.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");


class w_input extends w_base {
	var $current_record = K_UNDEFINED_RECORD;
	var	$wo;
	var $modify;		// indica si la ventana esta en modo modify
	// botones
	var $b_delete_visible = true;
	var $b_save_visible = true;
	var $b_no_save_visible = true;
	var $b_modify_visible = true;
	var $b_print_visible = true;
	// validaciones
	var	$valida_llave = false;		// Si la tabla tiene identity => se deb setear en false para evitar qeu valide la llave
	var $valida_FK = true;			// true si se desea que al eliminara valide las FK
	// FK que no se deben validar en delete, porque se supone que se eliminan en cascada
	var $FK_delete_cascada = array();
	var $dws = array();	// array con las dw del input
	var $tiene_wo = true;

	var $current_tab_page = 0;
	var $first_focus = '';

	/*	TRUE si se desea que no aparezca el menu cuando se use un xx_from; FALSE en otro caso
	 *  en general cuando se crea desde, pero en un anueva ventana (boton crear Empresa en cotizacion) se desea que 
	 *  este hidden el menu  
	 */
	var $hide_menu_when_from = true;	
	var $add_from = false;	// TRUE solo cuando se llega desde un link sin pasar por el output
	var $mod_from = false;	// TRUE solo cuando se llega desde un link sin pasar por el output
	var $goto_from = false; // similar a $mod_from, pero no cambia el modo a modificar en forma automatica
	
	// lista de campos a los cuales se les debe llevar auditoria
	private $auditoria = array();
	private $old_values = array();
	private $new_values = array();
	private $auditoria_relacionada = array();
	private $old_values_relacionada = array();
	private $new_values_relacionada = array();
	
	var	$js_onload = '';	// Usado para agregar un js adicional al body onload, ej en helen->declaracion
	
	// usados cuando se llega desde un static_link
	var $desde_link = false;
	var $modulo_origen;
		
	static function f_viene_del_output($modulo) {
		if (session::is_set('DESDE_wo_'.$modulo)) {
			session::un_set('DESDE_wo_'.$modulo);
			return true;
		}
		return false;
	}
	function w_input($nom_tabla, $cod_item_menu) {
		parent::w_base($nom_tabla, $cod_item_menu);
		if (session::is_set("add_".$this->nom_tabla."_desde")) {
			session::un_set("add_".$this->nom_tabla."_desde");
			$this->add_from = true;
			$this->tiene_wo = false;
		} 
		elseif (session::is_set("mod_".$this->nom_tabla."_desde")) {
			session::un_set("mod_".$this->nom_tabla."_desde");
			$this->mod_from = true;
		} 
		elseif (session::is_set("goto_".$this->nom_tabla."_desde")) {
			session::un_set("goto_".$this->nom_tabla."_desde");
			$this->goto_from = true;
		} 
		elseif (session::is_set("DESDE_link_wi")) {
			$dato = session::get("DESDE_link_wi");
			session::un_set("DESDE_link_wi");
			$dato = explode('|', $dato);
			$this->desde_link = true;
			$this->modulo_origen = $dato[0];
		}

		// Cuando se vuelve desde un link en la session esta grabada el tab desde donde se origino (no siempre: depende si se paso parametro en href del link) 
		if (session::is_set('wi_CURRENT_TAB_'.$this->nom_tabla)) {
			$this->current_tab_page = session::get('wi_CURRENT_TAB_'.$this->nom_tabla);
			session::un_set('wi_CURRENT_TAB_'.$this->nom_tabla);
		}
			
		
		$this->load_wo();
		
		// template
		$this->nom_template = "wi_".$this->nom_tabla.".htm";
		if (defined('K_CLIENTE')) {
			if (file_exists(K_CLIENTE.'/'.$this->nom_template))
				$this->nom_template = K_CLIENTE.'/'.$this->nom_template;
		}
		
		$this->set_modify(false);

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select TIENE_IMPRESION
		        from ITEM_MENU
		        where COD_ITEM_MENU = '".$cod_item_menu."'";
		$result = $db-> build_results($sql);
		if ($result[0]['TIENE_IMPRESION'] =='N')
			$this->b_print_visible = false;
	}
	function load_wo() {
		if ($this->tiene_wo)
		    $this->wo = session::get("wo_".$this->nom_tabla);
	}
	function get_item_wo($row, $field) {
		$row_ini = $this->wo->dw->get_item(0, 'ROW');
		$row_fin = $this->wo->dw->get_item($this->wo->dw->row_count() - 1, 'ROW');
		if ($row < $row_ini || $row > $row_fin) {
			$page = floor($row / $this->wo->row_per_page) + 1;
			$this->wo->set_current_page($page);
		}

		// busca la row
		for($i=0; $i<$this->wo->dw->row_count();$i++)
			if ($this->wo->dw->get_item($i, 'ROW')==$row) {
				$row = $i;
				break;
			}

		$dato = $this->wo->dw->get_item($row, $field);
		return $dato;
	}
	function habilita_boton(&$temp, $boton, $habilita) {
		//busca si tiene Imagen en K_CLIENTE => /images_appl/'.K_CLIENTE.'/images/*.jpg
		$ruta_imag = '../../../../commonlib/trunk/images/';
		if (defined('K_CLIENTE')) {
			if (file_exists('../../images_appl/'.K_CLIENTE.'/images/b_'.$boton.'.jpg')){
				$ruta_imag = '../../images_appl/'.K_CLIENTE.'/images/';
			}
		}
		
		if ($habilita) {
			$control = '<input name="b_'.$boton.'" id="b_'.$boton.'" src="'.$ruta_imag.'b_'.$boton.'.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\''.$ruta_imag.'b_'.$boton.'_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\''.$ruta_imag.'b_'.$boton.'_over.jpg\',1)" ';
			switch ($boton) {
		    case 'delete':
					$control .= 'onClick="return confirmDel();" ';
					break;
		    case 'save':
				if ($this->add_from)
					$control .= 'onClick="if (validate_save()) {
											return true;
										} else 
											return false;" ';
				elseif ($this->mod_from || $this->goto_from) 
					$control .= 'onClick="if (validate_save()) {
											var cod = document.getElementById(\'COD_'.strtoupper($this->nom_tabla).'_0\');
											if (cod){ 
												returnValue = get_value(cod.id);
												if($dlg){ setWindowReturnValue(returnValue); }
											}else {
												cod = document.getElementById(\'COD_'.strtoupper($this->nom_tabla).'_H_0\');
												returnValue = get_value(cod.id);
												if($dlg){ setWindowReturnValue(returnValue); }
											} 
											return true;
										} else 
											return false;" ';
				else
					$control .= 'onClick="var vl_tab = document.getElementById(\'wi_current_tab_page\'); if (TabbedPanels1 && vl_tab) vl_tab.value =TabbedPanels1.getCurrentTabIndex(); return validate_save();" ';
					break;
		    case 'print':
					$control .= ' target="_blank" onClick="var vl_tab = document.getElementById(\'wi_current_tab_page\'); if (TabbedPanels1 && vl_tab) vl_tab.value =TabbedPanels1.getCurrentTabIndex();
											 if (document.getElementById(\'b_save\')) {
												 if (validate_save()) {
												 		document.getElementById(\'wi_hidden\').value = \'save_desde_print\';
												 		document.getElementById(\'b_save\').click();
												 		return true;
												 	}
												 	else
												 		return false;
											 }
										 	 else
										 	 		return dlg_print();"';
					break;
				case 'back':
				case 'no_save':
					if ($this->add_from || $this->mod_from || $this->goto_from) {  
						$control .= 'onClick="window.close(); returnValue=null; if($dlg){ setWindowReturnValue(returnValue); } return true;" ';
						break;
					}
				case 'first':
				case 'prev':
				case 'next':
				case 'last':
				case 'modify':
					$control .= 'onClick="var vl_tab = document.getElementById(\'wi_current_tab_page\'); if (TabbedPanels1 && vl_tab) vl_tab.value =TabbedPanels1.getCurrentTabIndex();" ';
					break;
			}
			$control .= '/>';
			$temp->setVar("WI_".strtoupper($boton), $control);
		}
		else{
			$temp->setVar("WI_".strtoupper($boton), '<img src="'.$ruta_imag.'b_'.$boton.'_d.jpg"/>');
		}
	}
	function last_modif($tabla, $key) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select max(COD_LOG_CAMBIO) AS COD_LOG_CAMBIO
		        from LOG_CAMBIO
		        where NOM_TABLA = '".$tabla."' and
		              KEY_TABLA = '$key'";
		$result_cod = $db->build_results($sql);
		$cod_log_cambio = $result_cod[0]['COD_LOG_CAMBIO'];
		if ($cod_log_cambio == '')
			return 'Sin Modificaciones';
			
		if (K_TIPO_BD=='mssql'||(K_TIPO_BD=='sqlsrv'))
			$sql = "select convert(varchar(20), FECHA_CAMBIO, 103) + ' ' + convert(varchar(20), FECHA_CAMBIO, 108) FECHA,
			               NOM_USUARIO
			        from LOG_CAMBIO, USUARIO
			        where COD_LOG_CAMBIO = ".$cod_log_cambio." and
			              USUARIO.COD_USUARIO = LOG_CAMBIO.COD_USUARIO";
		elseif (K_TIPO_BD=='mysql')
			$sql = "select DATE_FORMAT(FECHA_CAMBIO, '%d/%m/%Y %T') FECHA,
			               NOM_USUARIO
			        from LOG_CAMBIO, USUARIO
			        where COD_LOG_CAMBIO = ".$cod_log_cambio." and
			              USUARIO.COD_USUARIO = LOG_CAMBIO.COD_USUARIO";
		elseif (K_TIPO_BD=='oci')
			$sql = "select to_char(FECHA_CAMBIO, 'dd/mm/yyyy hh:mm:ss') FECHA,
			               NOM_USUARIO
			        from LOG_CAMBIO, USUARIO
			        where COD_LOG_CAMBIO = ".$cod_log_cambio." and
			              USUARIO.COD_USUARIO = LOG_CAMBIO.COD_USUARIO";
		else
			$this->error('w_input.last_modif(), tipo base de datos no soportada');

		$result_fecha = $db-> build_results($sql);
		$fecha_cambio = $result_fecha[0]['FECHA'];
		$nom_usuario = $result_fecha[0]['NOM_USUARIO'];
		return $fecha_cambio.' '.$nom_usuario;
	}
	function set_modify($modify) {
		$this->modify = $modify;
	}
	function get_key_para_ruta_menu() {
		/* Esta funcion por defecto retorna lo mismo que el get_key, y es usada para completar el ruta menu
		 * si se desea que en el encabezado se despliegue otro dato diferente al key de la base de datos se debe reimplementar esta funcion
		 * Por ejemplo en la tabla FACTURA, la llave es COD_FACTURA pero para el usuario se necesita desplegar NRO_FACTURA
		 * entonces en el mantenedor de factura se debe reimplementar esta funcion y retornar NRO_FACTURA
		 */
		return $this->get_key();
	}
	function navegacion(&$temp) 	{
		$temp->setVar("WI_RUTA_MENU", $this->ruta_menu.$this->get_key_para_ruta_menu());

		$temp->setVar("WI_FECHA_ACTUAL", 'Fecha Actual: '.$this->current_date());
		$key = $this->limpia_key($this->get_key());
		$temp->setVar("WI_FECHA_MODIF", utf8_encode('Ultima Modificación: ').$this->last_modif($this->nom_tabla, $key));
		
		$this->habilita_boton($temp, 'back', true);
		if ($this->current_record == K_NEW_RECORD) {
			$this->habilita_boton($temp, 'first', false);
			$this->habilita_boton($temp, 'prev', false);
			$this->habilita_boton($temp, 'next', false);
			$this->habilita_boton($temp, 'last', false);
		}
		else {	// botones de navegacion
			$this->habilita_boton($temp, 'first', ($this->current_record > 0));
			$this->habilita_boton($temp, 'prev', ($this->current_record > 0));

			$this->habilita_boton($temp, 'next', ($this->current_record < $this->wo->row_count_output - 1));
			$this->habilita_boton($temp, 'last', ($this->current_record < $this->wo->row_count_output - 1));
		}
	}
	function tabs_visibles(&$temp) {
 		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select COD_PERFIL 
					from USUARIO 
				where COD_USUARIO = $this->cod_usuario";
		$result = $db->build_results($sql);
		$cod_perfil = $result[0]['COD_PERFIL'];
		
		if (K_TIPO_BD=='oci')
			$sql = "select I.COD_ITEM_MENU, I.VISIBLE, A.AUTORIZA_MENU from ITEM_MENU I, AUTORIZA_MENU A
					where I.COD_ITEM_MENU like '$this->cod_item_menu' || '%'
						and I.COD_ITEM_MENU <> '$this->cod_item_menu' and
						Length(I.COD_ITEM_MENU) = Length('$this->cod_item_menu') + 2 and 
						A.COD_ITEM_MENU like I.COD_ITEM_MENU and
						A.COD_PERFIL = $cod_perfil and
						I.TIPO_ITEM_MENU = 'T'";
		else
			$sql = "select I.COD_ITEM_MENU, I.VISIBLE, A.AUTORIZA_MENU from ITEM_MENU I, AUTORIZA_MENU A
					where I.COD_ITEM_MENU like '$this->cod_item_menu' + '%'
						and I.COD_ITEM_MENU <> '$this->cod_item_menu' and
						len(I.COD_ITEM_MENU) = len('$this->cod_item_menu') + 2 and 
						A.COD_ITEM_MENU = I.COD_ITEM_MENU and
						A.COD_PERFIL = $cod_perfil and
						I.TIPO_ITEM_MENU = 'T'";
					
		$result = $db->build_results($sql);
		for($i=0; $i<count($result); $i++){
			$autoriza_menu = $result[$i]['AUTORIZA_MENU'];
			$visible = $result[$i]['VISIBLE'];				
			$cod_item_menu = $result[$i]['COD_ITEM_MENU'];		
			if ($autoriza_menu == 'E'&& $visible == 'S')
				$temp->setVar('TAB_'.$cod_item_menu, '');
			else
				$temp->setVar('TAB_'.$cod_item_menu, 'none');
				
		}		
	}
	function habilitar(&$temp, $habilita) { } // funcion virtual
	function draw_dws(&$temp, $habilita) {
		// habilita las dws del input
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			// ****
			// VMC, 17-2-09 se copia siempre el nom_tabla.
			// Otra posibilidad seria hacer creado una funcion add_dw y en esta funcion asihnar el nom_table
			$this->dws[$indices[$i]]->nom_tabla	= $this->nom_tabla;		// se copia siempre

			$this->dws[$indices[$i]]->habilitar($temp, $habilita);
		}
	}
	function _habilitar(&$temp, $habilita) {
		if ($this->b_save_visible)
			$this->habilita_boton($temp, 'save', $habilita);
		if ($this->b_no_save_visible)
			$this->habilita_boton($temp, 'no_save', $habilita);
		if ($this->b_delete_visible && !$this->is_new_record())
			$this->habilita_boton($temp, 'delete', $habilita);
		if ($this->b_modify_visible)
			$this->habilita_boton($temp, 'modify', (!$habilita && $this->priv_autorizacion=='E'));	// privilegio de escritura
		if ($this->b_print_visible)
			$this->habilita_boton($temp, 'print', ($this->priv_impresion=='S'));	// privilegio de impresion

		$this->tabs_visibles($temp);
			
		$this->habilitar($temp, $habilita);	// llamado a funcion virtual
		// habilita las dws del input
		$this->draw_dws($temp, $habilita);
		
		$this->draw_btns($temp, $habilita);
		
		$this->navegacion($temp);
		$this->save_SESSION();
	}
	function save_SESSION() {
	    session::set("wi_".$this->nom_tabla, $this);
	}
	function delete_SESSION() {
		session::un_set("wi_".$this->nom_tabla);
	}
	function mandatorys() {
		/* Crea un array con la lista de campos que son mandatorys para que se pueda realizar la
		   validacion en js
		*/
		$script = 'var mandatorys = new Array();';
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$script .= $this->dws[$indices[$i]]->mandatorys();
		}
		return $script;
	}
	function computeds() {
		/* Crea un array con la lista de campos que son computed para que se pueda realizar la
		   el calculo de los computed en caddena en js
		*/
		$script = 'var computeds = new Array();';
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$script .= $this->dws[$indices[$i]]->computeds();
		}
		return $script;
	}
	function set_first_focus($first_focus) {
		$this->first_focus = $first_focus;
	}
	function make_menu(&$temp) {
		if ($this->hide_menu_when_from && ($this->add_from || $this->mod_from || $this->goto_from))
			$temp->setVar('W_MENU', '');			
		else
			parent::make_menu($temp);
	}
	function redraw() {
		chdir($this->work_directory);
		$temp = new Template_appl($this->nom_template, $this->js_onload);
		$this->make_menu($temp);
		$this->_habilitar($temp, $this->modify);
		$script  = '<script type="text/javascript">
function stopRKey(evt) {
var evt = (evt) ? evt : ((event) ? event : null);
var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
}
document.onkeypress = stopRKey;
';
		if ($this->modify) {
			$script .= $this->mandatorys().'
';
			$script .= $this->computeds().'
';
		}
		$script .= 'var TabbedPanels1 = null; </script>';
		
		/////////////////////////////
		// Si existe js para K_CLIENTE se incluye
		if (defined('K_CLIENTE')) {
			$file_name = $this->work_directory."/".K_CLIENTE."/".$this->nom_tabla.".js";
			if (file_exists($file_name)) {
				$include_js = '
<script charset="iso-8859-1" src="'.K_CLIENTE.'/'.$this->nom_tabla.'.js" type="text/javascript"></script>';
				$script .= $include_js;  				 
			}
		}
		/////////////////////////		
		
		
		$temp->setVar("WI_JAVA_SCRIPT", $script);
		print $temp->toString();

		// script al final del html
		$script = '<script type="text/javascript">';
		$script .= 'if (TabbedPanels1) TabbedPanels1.showPanel('.$this->current_tab_page.');';
		if ($this->modify) {
			// setea el focusa en el 1er campo
			$script .= "var campo_id; ";
			if ($this->first_focus!='')
				$script .= "campo_id = '".$this->first_focus."_0';";
			else {
				$indices_dw = array_keys($this->dws);
				if (count($indices_dw) > 0) {
					$indices_ctrl = array_keys($this->dws[$indices_dw[0]]->controls);
					if (count($indices_ctrl) > 0) 
						$script .= "campo_id = '".$this->dws[$indices_dw[0]]->controls[$indices_ctrl[0]]->field."_0';";
				}
			}
			$script .= "var campo = document.getElementById(campo_id); ";
			$script .= "if (campo) campo.focus(); ";
		}
		$script .= '</script>';
		print $script;
	}
	function load_record() { } // funcion virtual
	function _load_record() {
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$indices_controls = array_keys($this->dws[$indices[$i]]->controls);
			for ($j=0; $j<count($indices_controls); $j++) {
				$nom_control = $this->dws[$indices[$i]]->controls[$indices_controls[$j]]->field;
				for ($k=$i+1; $k < count($this->dws); $k++) {
					$indices_controls2 = array_keys($this->dws[$indices[$k]]->controls);
					$pos = array_search($nom_control, $indices_controls2);
					if ($pos!==false)
						$this->error('No pueden existir 2 controls con el mismo nombre. dw='.$indices[$k].' campo='.$nom_control);
				}
			}
		}
		$this->load_record();	// llamado a funcion virtual
		$this->redraw();
	}
	function get_key() { // funcion virtual
		$this->error("get_key() no implementado");
	}
	function lock_record() {
		$key = $this->get_key();	// obtiene la llave
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT 	USUARIO.NOM_USUARIO,
 			            LOCK_TABLE.NOM_MODULO
                 FROM 	LOCK_TABLE, USUARIO
                 WHERE  USUARIO.COD_USUARIO = LOCK_TABLE.COD_USUARIO and
			            NRO_MODULO = $key and
				        NOM_MODULO = '$this->nom_tabla' and
				        USUARIO.COD_USUARIO <> ".$this->cod_usuario;
		$result = $db->build_results($sql);
		if (count($result) == 0) {
			$db->BEGIN_TRANSACTION();
			if ($db->EXECUTE_SP("spd_lock_table", $key.", '".$this->nom_tabla."', ".$this->cod_usuario) &&
				$db->EXECUTE_SP("spi_lock_table", $key.", '".$this->nom_tabla."', ".$this->cod_usuario)) {
				$db->COMMIT_TRANSACTION();
				return true;
			}
			else {
				$db->ROLLBACK_TRANSACTION();
				return false;
			}
		}
		else {
			$this->redraw();
			$nom_usuario = $result[0]['NOM_USUARIO'];
			$this->message("No se puede modificar. El usuario '".$nom_usuario."', está ocupando este registro.");
			return false;
		}
	}
	function unlock_record() {
		if ($this->current_record == K_UNDEFINED_RECORD) return true;
		if ($this->current_record == K_NEW_RECORD) return true;
		
		$key = $this->get_key();	// obtiene la llave
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		if ($db->EXECUTE_SP("spd_lock_table", $key.", '".$this->nom_tabla."', ".$this->cod_usuario)) {
			$db->COMMIT_TRANSACTION();
			return true;
		}
		else {
			$db->ROLLBACK_TRANSACTION();
			return false;
		}
	}
	function modify_record() {
		if (!$this->lock_record())
			return false;
		$this->set_modify(true);
		$this->_load_record();
		return true;
	}
	function add_FK_delete_cascada($tabla) {
		$this->FK_delete_cascada[] = strtoupper($tabla);
	}
	function validate_delete($db) { return ''; }		// funcion virtuatl
	function _validate_delete($db) {
		// Validar las FK
		if ($this->valida_FK) {
		    if (K_TIPO_BD=='mssql' ||(K_TIPO_BD=='sqlsrv'))
				$sql = "select so.name TABLA,
		 	                   syscolumns.name COLUMNA
		                from sysobjects, sysforeignkeys, sysobjects so, syscolumns
		                WHERE sysobjects.xtype = 'U'AND
		                      sysobjects.name = '".strtoupper($this->nom_tabla)."'  AND
		                      rkeyid = sysobjects.id and
		                      so.id = fkeyid and
		                      syscolumns.colid = sysforeignkeys.fkey and
		                      syscolumns.id = fkeyid";
			elseif (K_TIPO_BD=='mysql')
				$sql = "SELECT TABLE_NAME TABLA,
											 COLUMN_NAME COLUMNA
								FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
								WHERE REFERENCED_TABLE_SCHEMA = '".K_BD."' AND
								      REFERENCED_TABLE_NAME = '".strtoupper($this->nom_tabla)."'";
			elseif (K_TIPO_BD=='oci')
				$sql = "select 	c.TABLE_NAME TABLA,
								cc.COLUMN_NAME COLUMNA
							from 	dba_constraints c, 
								dba_constraints r, 
								dba_cons_columns cc
							where 	c.CONSTRAINT_TYPE = 'R'
							and 	c.OWNER not in ('SYS','SYSTEM')
							and 	c.R_OWNER = r.OWNER
							and 	c.R_CONSTRAINT_NAME = r.CONSTRAINT_NAME
							and 	c.CONSTRAINT_NAME = cc.CONSTRAINT_NAME
							and 	c.OWNER = cc.OWNER
							AND r.TABLE_NAME = '".strtoupper($this->nom_tabla)."'
							AND c.OWNER='HELEN'							
							order 	by c.OWNER, c.TABLE_NAME, c.CONSTRAINT_NAME, cc.POSITION";
			else
				$this->error('w_input._validate_delete(), K_TIPO_BD no soportada');
			$result = $db->build_results($sql);
			for ($i=0; $i < count($result); $i++) {
				$tabla = strtoupper($result[$i]['TABLA']);
				if (!in_array($tabla, $this->FK_delete_cascada)) {
					$columna = $result[$i]['COLUMNA'];
					$sql = "SELECT COUNT(*) CANT
					        FROM $tabla
						    WHERE $columna = ".$this->get_key();
					$result_count = $db->build_results($sql);
					if ($result_count[0]['CANT'] > 0) {
						return 'Se registra movimientos para el registro '.strtoupper($this->nom_tabla).' en la tabla '.$tabla.'.\nNo se puede eliminar el registro.';
					}
				}
			}
		}
		return $this->validate_delete($db);
	}
	function delete_record($db) {
		return $db->EXECUTE_SP("spu_".$this->nom_tabla, "'DELETE', ".$this->get_key());
	}
	function _delete_record() {
		if ($this->is_new_record())
			$this->goto_list();

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$err = $this->_validate_delete($db);
		if ($err!='') {
			$this->_load_record();
			$this->message($err);
			return;
		}
		$this->get_old_values_auditoria($db);
		
		$db->BEGIN_TRANSACTION();
		if ($this->delete_record($db)) {
			if ($this->registra_auditoria($db, true))
				$db->COMMIT_TRANSACTION();
			else
				$err = 'No pudo registrar la aduitoria de la eliminacion.\n\n'.$db->make_msg_error_bd();
		}
		else
			$err = 'No pudo eliminar el registro.\n\n'.$db->make_msg_error_bd();
		if ($err == '') {
			$this->wo->set_count_output();
			$this->goto_list();
		}
		else {
			$db->ROLLBACK_TRANSACTION();
			$this->_load_record();
			$this->message($err);
		}
	}
	function limpia_key($key) {
		if (strlen($key)==0)
			return $key;

		// Si las llave es tipo string, entonces viene de la forma "'valor'" y se deben eliminar las
		if ($key[0]=="'")
			$key = substr($key, 1, strlen($key) - 2);
		return $key;
	}
	function validate_record() { return ''; }	// funcion virtual
	function _validate_record()	{
		// valida las dws del input
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$error = $this->dws[$indices[$i]]->validate();
			if ($error != '') {
				return $error;
			}
		}
		/////////////////


		$err = $this->validate_record();	// llamado a la funcion virtual
		if ($err != '')
			return $err;

		if ($this->is_new_record() && $this->valida_llave) {
			// Se deberia validar 1ro que exista la tabla $this->nom_tabla y el cambo "cod_".$this->nom_tabla
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = "select count(*) CANT
					from ".strtoupper($this->nom_tabla).
				  " where COD_".strtoupper($this->nom_tabla). " = ".$this->get_key();
			$result = $db->build_results($sql);
			$cant = $result[0]['CANT'];
			if ($cant > 0)
				return "Ya existe un registro con la llave ".$this->get_key();
		}
		return '';
	}
	function get_values_from_POST() {
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++)
			$this->dws[$indices[$i]]->get_values_from_POST();
	}
	function is_modified() {
		$indices = array_keys($this->dws);
		$modified = false;
		for ($i=0; $i < count($this->dws); $i++)
			$modified = $modified || $this->dws[$indices[$i]]->is_modified;
		return $modified;
	}
	function add_auditoria($field_name, $field_bd='') {
		/* Se agrega a la lista de campos para auditoria
			Cada vez que se haga un cambio en alguno de los cambios marcados para auditoria se dejara registro en DETALLE_CAMBIO

			$field_name : nombre del campo
			$field_bd : forma de obtener el dato en la base de datos puede ser una funcion o una formula
			ejemplo1:
				Para la tabla NOTA_VENTA, que se quiere dejar auditoria del campo REFERENCIA
				en el constuctor de wi_nota_venta 
					$this->add_auditoria('REFERENCIA');
				el 2do parametro que es opcional no se usa en este caso

			ejemplo2
				Para la tabla NOTA_VENTA, que se quiere dejar auditoria del campo FECHA_CIERRE
				en el constuctor de wi_nota_venta 
					$this->add_auditoria('FECHA_CIERRE', 'convert(varchar, FECHA_CIERRE, 103)');
		*/
		$f = new stdClass;
		$f->field_name = $field_name;
		$f->field_bd = $field_bd;
		$this->auditoria[] = $f; 
	}
	function remove_auditoria($field_name) {
		for ($i=0; $i < count($this->auditoria); $i++) {
			if ($this->auditoria[$i]->field_name == $field_name) {
				unset($this->auditoria[$i]);
				return;
			}
		}
	}
	
	
	function make_sql_auditoria() {
		// arma el SQL para obtener los valores old o new de la tabla
		// este select se usa en get_old_values_auditoria() y get_new_values_auditoria
		if (count($this->auditoria)==0) return '';
			
		$sql = 'select ';
		for ($i=0; $i < count($this->auditoria); $i++)
			if ($this->auditoria[$i]->field_bd != '')
				$sql .= $this->auditoria[$i]->field_bd.' '.$this->auditoria[$i]->field_name.',';
			else
				$sql .= $this->auditoria[$i]->field_name.',';
			$sql = substr($sql, 0, strlen($sql) - 1);		// Elimina la ultima coma
		
		$sql .= ' from '.$this->nom_tabla.' where COD_'.$this->nom_tabla.' = '.$this->get_key();
		return $sql;
	}
	function get_values_auditoria($db) {
		// Obtiene los valores OLD para registrar en la auditoria
		if (count($this->auditoria)==0) return array();
		
		$sql = $this->make_sql_auditoria();
		return $db->build_results($sql);
	}
	function get_values_auditoria_relacionada($db) {		
		// tablas relacionadas
		$values_relacionada = array();
		$lista_tablas = array_keys($this->auditoria_relacionada);
		for ($i=0; $i < count($lista_tablas); $i++) {
			$tabla = $lista_tablas[$i];
			$sql = $this->make_sql_auditoria_relacionada($tabla);
			$values_relacionada[$tabla] = $db->build_results($sql);			
		}
		return $values_relacionada;
	}
	function get_old_values_auditoria($db) {
		$this->old_values = $this->get_values_auditoria($db);
		$this->old_values_relacionada = $this->get_values_auditoria_relacionada($db);
	}
	function get_new_values_auditoria($db) {
		$this->new_values = $this->get_values_auditoria($db);
		$this->new_values_relacionada = $this->get_values_auditoria_relacionada($db);
	}
	function make_sql_auditoria_relacionada($tabla) {
		// arma el SQL para obtener los valores old o new de la tabla relacionada
		// este select se usa en get_old_values_auditoria() y get_new_values_auditoria
		if (count($this->auditoria_relacionada)==0) return '';
			
		$sql = 'select B.COD_'.$tabla.', ';
		for ($i=0; $i < count($this->auditoria_relacionada[$tabla]); $i++)
			if ($this->auditoria_relacionada[$tabla][$i]->field_bd != '')
				$sql .= $this->auditoria_relacionada[$tabla][$i]->field_bd.' '.$this->auditoria_relacionada[$tabla][$i]->field_name.',';
			else
				$sql .= 'B.'.$this->auditoria_relacionada[$tabla][$i]->field_name.',';
		$sql = substr($sql, 0, strlen($sql) - 1);		// Elimina la ultima coma
		
		$sql .= ' from '.strtoupper($this->nom_tabla).' A, '.$tabla.' B';
		$sql .= ' where A.COD_'.strtoupper($this->nom_tabla).' = '.$this->get_key();
		$sql .= '   and B.COD_'.strtoupper($this->nom_tabla).' = '.'A.COD_'.strtoupper($this->nom_tabla);
		return $sql;
	}
	function add_auditoria_relacionada($tabla, $field_name, $field_bd='') {
		/* Se usa igual que add_auditoria() pero para las tablas relacionadas y se debe indicar el nombre de la tabla relacionada */
		$f = new stdClass;
		$f->field_name = $field_name;
		$f->field_bd = $field_bd;
		$this->auditoria_relacionada[$tabla][] = $f;
	}
	function registra_auditoria($db, $es_eliminacion) {
		// Registra en el log de auditorias
		// Esta rutina es llamada despues del save_record()
		// retorna TRUE si logro registrar sin errores; FALSE en otro caso
		        
		$key = $this->limpia_key($this->get_key());
		$sp = "sp_log_cambio";
		$param = "'".strtoupper($this->nom_tabla)."','".$key."',".$this->cod_usuario;
		
		if ($es_eliminacion)
			$param .= ",'D'";
		elseif ($this->is_new_record())
			$param .= ",'I'";
		else
			$param .= ",'U'";

		if (K_CLIENTE=="UTEM"){	
			$ds = new datos_server();
			$browser = $ds->browser();
			$ip = $ds->getRealIP();
			$so = $ds->so();
			
			$param .= ", '$browser', '$ip', '$so'";
		}
		
		if ($db->EXECUTE_SP($sp, $param)) {
			if (count($this->auditoria)==0 && count($this->auditoria_relacionada)==0) return true;
			
			$sp = "sp_detalle_cambio";
			$cod_log_cambio = $db->IDENT_CURRENT('log_cambio');
			$this->get_new_values_auditoria($db);
			for ($i=0; $i < count($this->auditoria); $i++) {
				$field = $this->auditoria[$i]->field_name;
				if ($this->is_new_record())
					$old_value = '';
				else 
					$old_value = str_replace("'", "''", $this->old_values[0][$field]);
				if ($es_eliminacion)
					$new_value = '';
				else 
					$new_value = str_replace("'", "''", $this->new_values[0][$field]); 
				
				if ($old_value==$new_value)
					continue;
					
				$param = "$cod_log_cambio, '$field', '$old_value', '$new_value'";
				if (!$db->EXECUTE_SP($sp, $param))
					return false; 
			}
			// tablas relacionadas
			$sp = "sp_detalle_cambio_relacionada";
			$lista_tablas = array_keys($this->auditoria_relacionada);
			for ($i=0; $i < count($lista_tablas); $i++) {
				$tabla = $lista_tablas[$i];
				// Eliminaciones y modificaciones
				if (!$this->is_new_record()) {
					for ($j=0; $j < count($this->old_values_relacionada[$tabla]); $j++) {
						if ($es_eliminacion)
							$found = false;
						else {
							$cod_old = $this->old_values_relacionada[$tabla][$j]['COD_'.$tabla];
							$found = false;
							for ($k=0; $k < count($this->new_values_relacionada[$tabla]); $k++) {
								$cod_new = $this->new_values_relacionada[$tabla][$k]['COD_'.$tabla];
								if ($cod_old==$cod_new) {
									$found = true;
									$ind_found = $k;
									break;
								}
							}
						}
						if ($found) {
							for ($k=0; $k < count($this->auditoria_relacionada[$tabla]); $k++) {
								$field = $this->auditoria_relacionada[$tabla][$k]->field_name;
								$old_value = str_replace("'", "''", $this->old_values_relacionada[$tabla][$j][$field]);
								$new_value = str_replace("'", "''", $this->new_values_relacionada[$tabla][$ind_found][$field]);
								if ($old_value==$new_value)
									continue;
								$param = "$cod_log_cambio, '$tabla', '$field', '$cod_old', '$old_value', '$new_value', 'U'";
								if (!$db->EXECUTE_SP($sp, $param))
									return false; 
							}
						}
						else { // se elimino
							$new_value = '';
							for ($k=0; $k < count($this->auditoria_relacionada[$tabla]); $k++) {
								$field = $this->auditoria_relacionada[$tabla][$k]->field_name;
								$old_value = str_replace("'", "''", $this->old_values_relacionada[$tabla][$j][$field]);
								$param = "$cod_log_cambio, '$tabla', '$field', '$cod_old', '$old_value', '$new_value', 'D'";
								if (!$db->EXECUTE_SP($sp, $param))
									return false; 
							}
						}
					}
				}
				// Inserciones
				for ($j=0; $j < count($this->new_values_relacionada[$tabla]); $j++) {
					$cod_new = $this->new_values_relacionada[$tabla][$j]['COD_'.$tabla];
					$found = false;
					if (!$this->is_new_record()) {
						for ($k=0; $k < count($this->old_values_relacionada[$tabla]); $k++) {
							$cod_old = $this->old_values_relacionada[$tabla][$k]['COD_'.$tabla];
							if ($cod_old==$cod_new) {
								$found = true;
								break;
							}
						}
					}
					if (!$found) { // se agrego
						$old_value = '';
						for ($k=0; $k < count($this->auditoria_relacionada[$tabla]); $k++) {
							$field = $this->auditoria_relacionada[$tabla][$k]->field_name;
							$new_value= str_replace("'", "''", $this->new_values_relacionada[$tabla][$j][$field]);
							$param = "$cod_log_cambio, '$tabla', '$field', '$cod_new', '$old_value', '$new_value', 'I'";
							if (!$db->EXECUTE_SP($sp, $param))
								return false; 
						}
					}
				}
			}
			return true;
		}
		else
			return false;
	}	
	function save_record($db) { return true;  } // funcion virtual
	function _save_record() {
		$this->get_values_from_POST();
		$modified = $this->is_modified();
		
		if (!$modified && !$this->is_new_record()) {
			$this->unlock_record();
			$this->set_modify(false);
			$this->_load_record();
			return true;
		}
		// validacióN
		$error = $this->_validate_record();
		if ($error != '') {
			$this->redraw();
			$this->message($error);
			return false;
		}

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if (!$this->is_new_record())
			$this->get_old_values_auditoria($db);
		$db->BEGIN_TRANSACTION();
		$err = false;

		if ($this->save_record($db)) { // llamado a funcion virtual
			if ($this->registra_auditoria($db, false)) {
				$db->COMMIT_TRANSACTION();
				$this->unlock_record();
			}
			else
				$err = true;
		}
		else
			$err = true;

		$this->set_modify(false);

		if ($err) {
			$db->ROLLBACK_TRANSACTION();
			$error_sp = $db->GET_ERROR();
			$this->unlock_record();
			// VMC, 9-9-9 OJO se pierde la informacion ingresada!!
			$this->goto_record($this->current_record);
			$this->message('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
			return false;
		}
		
		if (!$this->is_new_record()){
			$this->_load_record();
		}
		else {		// new record
			/* si viene desde add_from, la ventana ya se ha cerrado y se debe retornar el valor del cod_tabla
			 * para ello se graba en la session para luego ser rescatado por un ajax
			 */
			if ($this->add_from)
				session::set('COD_'.strtoupper($this->nom_tabla), $this->limpia_key($this->get_key()));
			if ($this->tiene_wo) {
				$this->wo->set_count_output();
				$this->wo->last_page = Ceil($this->wo->row_count_output / $this->wo->row_per_page);
				$this->wo->set_current_page($this->wo->current_page);
	
				$key = $this->get_key();
				if ((K_TIPO_BD=='mssql')||(K_TIPO_BD=='sqlsrv')) {
					/* 
					a partir de :
					select 	COD_CIUDAD,
									NOM_CIUDAD
									from CIUDAD
					ORDER BY COD_CIUDAD
	
					Arma un select de la forma:
					select ROW
					from (select	ROW_NUMBER() OVER (ORDER BY COD_CIUDAD)-1 AS ROW,
												COD_CIUDAD,
		      							NOM_CIUDAD
								from CIUDAD) A
					where cod_ciudad = {KEY}
		
					y se obtiene el row
					*/
					if ($this->desde_link)
						$result = array();
					else {
						$sql = $this->wo->dw->sql;
						$pos = strpos(strtoupper($sql), 'ORDER BY');
						if ($pos===false)
							$this->error('Los output deben tener un ORDER BY !!');
						$orderby = substr($sql, $pos);
						$sql = substr($sql, 0, $pos);
			
						// se asume que parte por SELECT
						$sql = 'SELECT ROW_NUMBER() OVER ('.$orderby.')-1 AS R,'.substr($sql, 6);
						$sql = 'SELECT R FROM ('.$sql.') A where '.$this->wo->dw->fields[0]->name.'='.$key;
						$result = $db->build_results($sql);
					}
				}
				elseif (K_TIPO_BD=='oci') {
					/*
					a partir de :
					select 	COD_CIUDAD,
									NOM_CIUDAD
									from CIUDAD
					ORDER BY COD_CIUDAD
	
					Arma un select de la forma:
					SELECT r
					FROM 
					(SELECT cod_usuario, nom_usuario, ROWNUM - 1 AS r
					FROM usuario
					ORDER BY cod_usuario)
					where cod_usuario = 10
		
					y se obtiene el row
					 */
					if ($this->desde_link)
						$result = array();
					else {
						$sql = $this->wo->dw->sql;
						$pos = strpos(strtoupper($sql), 'ORDER BY');
						if ($pos===false){
							$this->error('Los output deben tener un ORDER BY !!');
						}
						$orderby = substr($sql, $pos);
						$sql = substr($sql, 0, $pos);
			
						// se asume que parte por SELECT
						$sql = 'SELECT ROW_NUMBER() OVER ('.$orderby.')-1 AS R,'.substr($sql, 6);
						$sql = 'SELECT R FROM ('.$sql.') A where '.$this->wo->dw->fields[0]->name.'='.$key;
						$result = $db->build_results($sql);
					}
				}
				if (count($result))
					$row = $result[0]['R'];
				else {
					// El nuevo registro queda filtrado en el output!, por eso se agrega al final del wo para evitar que se caiga
					// Pero al volver al wo el registro no se vera.
					$row = $this->wo->dw->insert_row();
					// NOTA: se asume que en wo el primer campo es la llave
					$this->wo->dw->set_item($row, $this->wo->dw->fields[0]->name, $this->get_key());
					$this->wo->dw->set_item($row, 'ROW', $row);
					$this->wo->row_count_output += 1;
				}
				$this->goto_record($row);
			}
			if ($this->add_from)//se agrego esto para que se pueda cerrar la ventana que se abrió desde la funcion add_documento hecha en js
				$this->redraw();
		}
		return true;
	}
	function no_save_record() {
		if ($this->is_new_record())
			$this->goto_list();
		else {
			$this->unlock_record();
			$this->set_modify(false);
			$this->_load_record();
		}
	}
	function print_record() {
		$this->error("print no implementado");
	}
	function is_new_record() {
		return ($this->current_record == K_NEW_RECORD);
	}
	function new_record() {}	//  funcion virtual
	function _new_record() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$nom_class = get_class($this);
		
		$ds = new datos_server();
		$browser = $ds->browser();
		$ip = $ds->getRealIP();
		$so = $ds->so();
		
		if (K_CLIENTE=="UTEM"){
        	$db->EXECUTE_SP("sp_log_cambio", "'$nom_class', '', $this->cod_usuario, 'C', '$browser', '$ip', '$so'");
        }else{
        	$db->EXECUTE_SP("sp_log_cambio", "'$nom_class', '', $this->cod_usuario, 'C'");
        }
        
		
		$this->current_record = K_NEW_RECORD;
		$this->new_record();	// llamado a funcion virtual
		$this->set_modify(true);
		$this->redraw();
	}
	// NAVEGACION
	function next_record() {
		$this->unlock_record();
		$this->set_modify(false);
		$this->current_record = $this->current_record + 1;
		$this->_load_record();
	}
	function prev_record() {
		$this->unlock_record();
		$this->set_modify(false);
		$this->current_record = $this->current_record - 1;
		$this->_load_record();
	}
	function first_record() {
		$this->unlock_record();
		$this->set_modify(false);
		$this->current_record = 0;
		$this->_load_record();
	}
	function last_record() {
		$this->unlock_record();
		$this->set_modify(false);
		$this->current_record = $this->wo->row_count_output - 1;
		$this->_load_record();
	}
	function goto_record($record) {
		if ($this->goto_from) {
			$this->current_record = $record;
			$this->_load_record();
		}
		elseif ($this->mod_from) {
			$this->current_record = $record;
			$this->load_record();
			$this->modify_record();
		}
		else {
			$this->unlock_record();
			if ($record==K_NEW_RECORD) {
				$this->_new_record();
				return;
			}
			$this->set_modify(false);
			$this->current_record = $record;
			$this->_load_record();
		}
	}
	function goto_list() {
		if ($this->desde_link) {
			$this->unlock_record();
			if ($this->modulo_origen=='')
				base::presentacion();
			else {
				if (session::is_set('wi_DESDE_OUTPUT_'.$this->modulo_origen))	{
					//viene desde un output
					session::un_set('wi_DESDE_OUTPUT_'.$this->modulo_origen);
					
					$wo = session::get('wo_'.$this->modulo_origen);
					$url = $wo->get_url_mantenedor().'/wo_'.$this->modulo_origen.'.php';
					header ('Location:'.$url);
				}
				else {
					$wi = session::get('wi_'.$this->modulo_origen);
		    		if ($wi->desde_link)
		    			session::set('DESDE_link_wi', $wi->modulo_origen);
		    		$wi->wo->detalle_record($wi->current_record);
				}
			}
    		return;
		}
		if ($this->is_new_record())
			$page = $this->wo->current_page;
		else {
			$this->unlock_record();
			$page = floor($this->current_record / $this->wo->row_per_page) + 1;
		}
		// vuelve a cargar el output
		$this->wo->retrieve_totales();
		$this->wo->set_current_page($page);
		$this->wo->save_SESSION();
		
		$url = $this->get_url_wo();
		header ('Location:'.$url);

		session::set('W_OUTPUT_RECNO_'.$this->nom_tabla, $this->current_record);	// para indicar el registro donde se clickeo
	}
	function get_url_wo() {
		return $this->get_url_mantenedor().'/wo_'.$this->nom_tabla.'.php';
	}
	function need_redraw() {
		session::set('REDRAW_'.$this->nom_tabla, 'redraw');
	}
	function add_line($label_record) {
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$dw =& $this->dws[$indices[$i]];
			if ($dw->label_record==$label_record) {
				$row = $dw->insert_row();
				$this->save_SESSION();
				// retornamos el html de la nueva linea (todo el tr)
				chdir($this->work_directory);
				$temp = new Template_appl($this->nom_template);
				$linea = $temp->structure["blocks"][$dw->label_record]['body'];

				// crea los datos del ultimo record recien agregado
				$temp->gotoNext($dw->label_record);
				$dw->fill_record($temp, $row);	// Ultimo registro

				$ind_block = array_keys($temp->content["blocks"][$dw->label_record][0]['values']);
				for ($j=0; $j<count($temp->content["blocks"][$dw->label_record][0]['values']); $j++)
					$linea = str_replace('{'.$ind_block[$j].'}', $temp->content["blocks"][$dw->label_record][0]['values'][$ind_block[$j]], $linea);

					// retorna para el ajax el html del nuevo "tr" y el campo de focus si existe
				if ($dw->first_focus != '')
					return $linea.'|'.$dw->first_focus.'_'.$dw->redirect($row);
				else
					return $linea.'|';
			}
		}
		return 'error label_record no encontrado: '.$label_record;
	}
	function del_line($label_record, $record) {
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$dw =& $this->dws[$indices[$i]];
			if ($dw->label_record==$label_record) {
				$dw->delete_row($dw->un_redirect($record));
				$this->save_SESSION();
			}
		}
		return;
	}
	function load_drop_down($field, $row) {} //funcion virtual (VMC 14-01-2011 no recuerdo que se use en algun lado ¿?)
	function find_control($field) {
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$dw =& $this->dws[$indices[$i]];
			$found = $dw->find_control($field);
			if ($found)
				return $found;
		}
		return false;
	}
	
	/////////////////////////////////////
	/* VMC, 14-01-2011
	 * Similar al array de dws, se crea un array de boton btns 
	 * helen.ingreso.btns[DISTRIBUIR]
	 */
	var $btns = array();
	
	function add_button($button_ctrl) {
		$this->btns[$button_ctrl->field] = $button_ctrl;
	}
	function draw_1_btn($button, &$temp, $habilita) {
		if ($habilita)
			$ctrl = $this->btns[$button]->draw_entrable();
		else
			$ctrl = $this->btns[$button]->draw_no_entrable();
		$temp->setVar("BTN_".$this->btns[$button]->field, $ctrl);
	}
	function draw_btns(&$temp, $habilita) {
		$indices = array_keys($this->btns);
		for ($i=0; $i < count($this->btns); $i++) {
			$this->draw_1_btn($indices[$i], $temp, $habilita);
		}
	}
	/////////////////////////////////////
	
	
	function procesa_event() {
		if (isset($_POST['wi_current_tab_page']))
			$this->current_tab_page = $_POST['wi_current_tab_page'];

		if(isset($_POST['b_save_x'])) {
			if ($this->_save_record()) {
				if ($_POST['wi_hidden']=='save_desde_print')		// Si el save es gatillado desde el boton print, se fuerza que se ejecute nuevamente el print
					print '<script type="text/javascript"> document.getElementById(\'b_print\').click(); </script>';
				if ($this->add_from || $this->mod_from || $this->goto_from)
					print '<script type="text/javascript"> window.close();  </script>';	
			}
		}
		elseif(isset($_POST['b_no_save_x'])) {
			$this->no_save_record();
		}
		elseif(isset($_POST['b_delete_x']))
			$this->_delete_record();
		elseif(isset($_POST['b_modify_x'])) {
			$this->modify_record();
		}
		elseif(isset($_POST['b_print_x']))  {
			$this->print_record();
		}
		elseif(isset($_POST['b_back_x']))
			$this->goto_list();
		elseif(isset($_POST['b_prev_x']))  {
			$this->prev_record();
		}
		elseif(isset($_POST['b_next_x'])) {
			$this->next_record();
		}
		elseif(isset($_POST['b_first_x']))  {
			$this->first_record();
		}
		elseif(isset($_POST['b_last_x']))  {
			$this->last_record();
		}
		elseif (session::is_set('REDRAW_'.$this->nom_tabla)) {
			session::un_set('REDRAW_'.$this->nom_tabla);
			$this->redraw();
		}
	}
}
?>