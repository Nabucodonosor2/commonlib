<?php
require_once(dirname(__FILE__)."/../auto_load.php");

define("K_UNDEFINED_RECORD", -1);
define("K_NEW_RECORD", -3);

class w_base extends base {
	// template
	var $nom_template = '';

	var $nom_tabla = '';
	var $work_directory = '';
	var	$cod_usuario = -1;
	var	$nom_usuario = '';
	var $cod_item_menu;
	var $ruta_menu = '';
	// opciones del perfil
	var $priv_autorizacion = '';
	var $priv_impresion = '';

	function w_base($nom_tabla, $cod_item_menu) {
		parent::base();
		$this->nom_tabla = $nom_tabla;
		$this->work_directory = getcwd();	// ejemplo: "sitio/appl/comuna/"
		$this->cod_usuario = session::get("COD_USUARIO");	// viene del login
		$this->nom_usuario = session::get("NOM_USUARIO");	// viene del login
		$this->cod_item_menu = $cod_item_menu;
		if ($cod_item_menu != '') {
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$this->ruta_menu = $this->get_ruta_menu($this->cod_item_menu);
	
			$sql = "select a.AUTORIZA_MENU, a.IMPRESION, u.AUTORIZA_INGRESO  
			        from   AUTORIZA_MENU a, USUARIO u
			        where  u.COD_USUARIO = ".$this->cod_usuario." and
			               a.COD_PERFIL = u.COD_PERFIL and 
			               a.COD_ITEM_MENU = '".$cod_item_menu."'";
			$result = $db->build_results($sql);
			$this->priv_autorizacion = $result[0]['AUTORIZA_MENU'];
			$this->priv_impresion = $result[0]['IMPRESION'];
			$this->autoriza_ing = $result[0]['AUTORIZA_INGRESO'];
			
		}
		
		/*
		CM + IS, 24-09-2015, se resuelven dos vulnerabilidades:
		- si el usuario tiene la sesion del helen abierta y abre la sesion del portal web, al volver a helen le obligará al usuario iniciar sesión. El problema era que al volver a Helen el usuario era el COD_USUARIO = 2
		- si el usuario inicia sesion desde el portal web y luego digita, por ejemplo, lo siguiente: http://192.168.2.141/desarrolladores/isanchez/helen_utem/trunk/appl/ingreso/wo_ingreso.php
		el usuario podría hacer un ingreso de caja desde boleta. 
 		*/
		if ($this->autoriza_ing == 'N' || $cod_item_menu == ''){
			
			$K_ROOT_URL = session::get('K_ROOT_URL');
			$cod_usuario = session::get("COD_USUARIO");
			$ds = new datos_server();
			$browser = $ds->browser();
			$ip = $ds->getRealIP();
			$so = $ds->so();
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$db->EXECUTE_SP("sp_log_cambio", "'SESION_INVALIDA', '', $cod_usuario, 'L', '$browser', '$ip', '$so'");
			
			session_destroy();
			header ('Location:'.$K_ROOT_URL);
		}
		
	}
	
	function get_ruta_menu($cod_item_menu) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$ruta_menu = '';
		while (strlen($cod_item_menu) > 0) {
			$sql = "SELECT NOM_ITEM_MENU
							from ITEM_MENU 
							where COD_ITEM_MENU = '$cod_item_menu'";
			$result = $db->build_results($sql);
			$ruta_menu = $result[0]['NOM_ITEM_MENU'].'->'.$ruta_menu;
			$cod_item_menu = substr($cod_item_menu, 0, strlen($cod_item_menu) - 2);			
		}
		return $ruta_menu;
	}
	function make_menu(&$temp) {
		$menu = session::get('menu_appl');
		$menu->draw($temp);
	}
	function clicked_boton($nom_boton, &$value_boton, $es_con_imagen=true) {
		/* VMC, 6-8-2008
		se cambio mlos botones b_detalle a b_detalle_nnn porque en IE 
		los botones con imagenes no tiene value !!, entonces se decide pasar el value en el nombre del boton
		
		codigo anterior funcionaba en FF pero no en IE
		
		if(isset($_POST['b_detalle_x']))
			$this->detalle_record($_POST['b_detalle']);		// para IE no pasa el valor
				
			SOLUCION = en el nombre del boton va el dato 	
			'b_detalle_nnn_x', donde nnn es el dato
		*/
		
		$key_post = array_keys($_POST);
		for ($i=0; $i < count($key_post); $i++)
			if (strncmp($key_post[$i], $nom_boton, strlen($nom_boton))==0) {
				$value_boton = substr($key_post[$i], strlen($nom_boton) + 1);	// $nom_boton_nnn_x,  la primera n es la posicion strlen($nom_boton) + 1
				if ($es_con_imagen)
					$value_boton = substr($value_boton, 0, strlen($value_boton) - 2);	// nnn_x, elimina _x
				return true;
			}
		return false;
	}
	static function get_privilegio_opcion_usuario($cod_item_menu, $cod_usuario) {
		/* Retorna AUTORIZA_MENU.AUTORIZA_MENU para l$cod_item_menu, $cod_usuario */
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select A.AUTORIZA_MENU
		        from   AUTORIZA_MENU A, USUARIO U
		        where  U.COD_USUARIO = ".$cod_usuario." and
		               A.COD_PERFIL = U.COD_PERFIL and 
		               A.COD_ITEM_MENU = '".$cod_item_menu."'";
		$result = $db->build_results($sql);
		return $result[0]['AUTORIZA_MENU'];
	}
	static function tiene_privilegio_opcion_usuario($cod_item_menu, $cod_usuario) {
		/* Retorna TRUE si se tiene privilegios para la opcion $cod_item_menu; FALSE en otro caso 
		 * Esta función en general es usada para preguntar si se tiene acceso a una opcion especial del menu 
		 * cod_item like '99%'
		 */
		$priv = w_base::get_privilegio_opcion_usuario($cod_item_menu, $cod_usuario);
		if ($priv=='E')
			return true;
		else
			return false;
		
	}
	function tiene_privilegio_opcion($cod_item_menu) {
		return self::tiene_privilegio_opcion_usuario($cod_item_menu, $this->cod_usuario);		
	}
	function get_url_mantenedor() {
		// Busca la carpeta del mantenedor
		// Primero busca si existe mantenedor en appl
		$ROOT = $this->root_url;
		$ROOT_DIR = $this->root_dir;
		if (file_exists($ROOT_DIR.'appl/'.$this->nom_tabla))
			return $ROOT.'appl/'.$this->nom_tabla;
		
		// Segundo busca en appl_parent
		$appl = session::get('K_APPL');
		if (!session::is_set('K_APPL_PARENT'))
			$appl_parent = $appl;
		else {
			$appl_parent = session::get('K_APPL_PARENT');
			$ROOT = str_replace("/".$appl."/", "/".$appl_parent."/", $ROOT);
			$ROOT_DIR = str_replace("/".$appl."/", "/".$appl_parent."/", $ROOT_DIR);
			if (file_exists($ROOT_DIR.'appl/'.$this->nom_tabla)) 
				return $ROOT.'appl/'.$this->nom_tabla;
		}
		
		// Tercero buscar en appl common
		$ROOT = str_replace("/".$appl_parent."/", "/commonlib/", $ROOT);
		$ROOT_DIR = str_replace("/".$appl_parent."/", "/commonlib/", $ROOT_DIR);
		if (file_exists($ROOT_DIR.'appl/'.$this->nom_tabla))
			return $ROOT.'appl/'.$this->nom_tabla;
	}
}
?>