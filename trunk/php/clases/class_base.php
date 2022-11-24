<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class base {
	public $root_dir;
	public $root_url;

	function base() {
		$this->root_dir = session::get('K_ROOT_DIR');
		$this->root_url = session::get('K_ROOT_URL');
	}
	
	function get_valor_moneda($fecha, $fecha_termino, $serie){
		
		$user = '77753520K';
		$password='1e6mI9wS';
		$frequencyCode ='DAILY';
		$wsdl="https://si3.bcentral.cl/sietews/sietews.asmx?wsdl";
		$client = new soapclient($wsdl);
		$params = new stdClass;
		$params->user = $user;
		$params->password = $password;
		$params->frequencyCode = $frequencyCode;
		$result = $client->SearchSeries($params)->SearchSeriesResult;
		
		$seriesIds = array ($serie);
		$firstDate = $fecha;
		$lastDate = $fecha_termino;
		$client = new soapclient($wsdl);
		$params = new stdClass;
		$params->user = $user;
		$params->password = $password;
		$params->firstDate = $firstDate;
		$params->lastDate = $lastDate;
		$params->seriesIds = $seriesIds;
		$result = $client->GetSeries($params)->GetSeriesResult;
		$fameSeries =$result->Series->fameSeries;
		
		return $fameSeries->obs->value;
		
	}

	function get_rango_valor_moneda($fecha_inicio, $fecha_termino, $serie){
		
		$user = '77753520K';
		$password='1e6mI9wS';
		$frequencyCode ='DAILY';
		$wsdl="https://si3.bcentral.cl/sietews/sietews.asmx?wsdl";
		$client = new soapclient($wsdl);
		$params = new stdClass;
		$params->user = $user;
		$params->password = $password;
		$params->frequencyCode = $frequencyCode;
		$result = $client->SearchSeries($params)->SearchSeriesResult;
		
		$seriesIds = array ($serie);
		
		$firstDate = $fecha_inicio;
		$lastDate = $fecha_termino;
		
		$client = new soapclient($wsdl);
		$params = new stdClass;
		$params->user = $user;
		$params->password = $password;
		$params->firstDate = $firstDate;
		$params->lastDate = $lastDate;
		$params->seriesIds = $seriesIds;
		$result = $client->GetSeries($params)->GetSeriesResult;
		$fameSeries =$result->Series->fameSeries;
		
		return $fameSeries;
	}

	function find_file($modulo, $file){
		/* El esquema general para reutilizar modulos es el siguiente
		 * dado un modulo se busca primero en la appl definida en appl.ini bajo la ruta
		 * sino se encuentra aqui se busca en appl_padre y finalmente si no se encuentra se busca en common
		 * Por ejemplo en el sistema serbinor cuya appl padre es biggi para el mantenedor de "cargo"
		 * 1.- se busca en /serbinor/trunk/appl/cargo
		 * 2.- se busca en /biggi/trunk/appl/cargo
		 * 3.- se busca en /commonlib/trunk/cargo
		 * 
		 * en este ejemplo lo encuentra en el paso 3
		 * 
		 * En general para cualquier archivo se debe utilizar el mismo mecanismo de busqueda por ejemplo
		 * como ej. en factura para buscar el xml de impresion se usa esta funcion
		 */
		$file_name = $this->root_dir.'appl/'.$modulo.'/'.$file;
		if (!file_exists($file_name)) {
			if (session::is_set('K_APPL_PARENT')) {
				$appl_parent = session::get('K_APPL_PARENT');
				$file_name = $this->root_dir.'../../'.$appl_parent.'/trunk/appl/'.$modulo.'/'.$file;
			}
			else
				$file_name = 'error';
		}
		return $file_name;
	}
	static function halt(){
		die ("Session halted.");
	}
	static function error($msg_error) {
		print '<script type="text/javascript">
          alert("ERROR: '.$msg_error.'");
					</script>';
		self::halt();
	}
	static function message($msg) {
		$msg = str_replace  ('"', "'", $msg);
		print '<script type="text/javascript">
          alert("'.$msg.'");
					</script>';
	}
	// 4D compatible
	static function alert($msg) {
		self::message($msg);
	}
	function save_SESSION() {
		session::set(get_class($this), $this);
	}
	function delete_SESSION() {
		session::un_set(get_class($this));
	}
	function current_date() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		return $db->current_date();
	}
	function current_date_time() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		return $db->current_date_time();
	}
	function current_time() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		return $db->current_time();
	}
	static function presentacion($mess = '', $print='', $root_url='') {
		session::set('PRESENTACION_MESSAGE', $mess);		// mensaje que se desea aparezca al desplegar la ventana de presentacion con un alert
		session::set('PRESENTACION_PRINT', $print);		// codigo javascrip o texto que se desea enviar con un print desde presentacion.php
		if ($root_url=='')
			$root_url = session::get('K_ROOT_URL');
		else 
			$root_url = $this->root_url;
		header ('Location:'.$root_url."../../commonlib/trunk/php/presentacion.php");
	}
	function nom_mes($mes) {
		switch ($mes) {
			case 1: return 'Enero';
			case 2: return 'Febrero';
			case 3: return 'Marzo';
			case 4: return 'Abril';
			case 5: return 'Mayo';
			case 6: return 'Junio';
			case 7: return 'Julio';
			case 8: return 'Agosto';
			case 9: return 'Septiembre';
			case 10: return 'Octubre';
			case 11: return 'Noviembre';
			case 12: return 'Diciembre';
		}
	}
	function nom_dia($dia) {
		switch ($dia) {
			case 1: return 'Lunes';
			case 2: return 'Martes';
			case 3: return 'Miércoles';
			case 4: return 'Jueves';
			case 5: return 'Viernes';
			case 6: return 'Sábado';
			case 7: return 'Domingo';
		}
	}
	function f_rotateHEX($string, $n) {
		//for more security, randomize this string
		$chars="abcdef1234567890";
		$str="";
		for ($i=0;$i<strlen($string);$i++) {
			$pos = strpos($chars,$string[$i]);
			$pos += $n;
			if ($pos>=strlen($chars)){
				$pos = $pos % strlen($chars);
			}
			$str.=$chars[$pos];
		}
		return $str;
	}
	function f_encriptar($password) {
		$salt = "caTalIna_inteGra_sYstem.Ltda";
		//encrypt the password, rotate characters by length of original password
		$len = strlen($password);
		$password = md5($password);
		$password = $this->f_rotateHEX($password,$len);
		return md5($salt.$password);
	}
	static function get_parametro($cod_parametro) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results("SELECT VALOR FROM PARAMETRO WHERE COD_PARAMETRO = ".$cod_parametro);
		if (count($result)==0){
			$this->error('Parametro inexistente, cod_parametro = '.$cod_parametro);
		}
		return $result[0]['VALOR'];
	}
	static function get_constante($cod_constante) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results("SELECT VALOR FROM CONSTANTE WHERE COD_CONSTANTE = '".$cod_constante."'");
		if (count($result)==0){
			$this->error('Cosntante inexistente, cod_constante = '.$cod_constante);
		}
		return $result[0]['VALOR'];
	}
	static function spd_lock_table_user() {
		$cod_usuario = session::get("COD_USUARIO");
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		if ($db->EXECUTE_SP("spd_lock_table_user", $cod_usuario)){
			$db->COMMIT_TRANSACTION();
		}
		else{
			$db->ROLLBACK_TRANSACTION();
		}
	}
	static function get_SO() {
		$user_browser = strtolower($_SERVER['HTTP_USER_AGENT']);

		$this_platform = '';
		if (strpos($user_browser, 'linux'))
		{
			$this_platform = 'linux';
		}
		elseif (strpos($user_browser, 'macintosh') || strpos($user_browser, 'mac platform x'))
		{
			$this_platform = 'mac';
		}
		else if (strpos($user_browser, 'windows') || strpos($user_browser, 'win32'))
		{
			$this_platform = 'windows';
		}
			
		return $this_platform;
	}
	static function get_tipo_dispositivo() {
		$equipo_ipad  = "ipad";
		$equipo_WINXP = "windows nt 5.1";
		$equipo_WIN7  = "windows nt 6.1";
		$equipo_MAC   = "macintosh";
		$user_browser = strtolower($_SERVER['HTTP_USER_AGENT']);

		$tipo_dispositivo = '';
		if (strpos($user_browser, $equipo_ipad) !== false){
			$tipo_dispositivo = "IPAD";
		}else if (strpos($user_browser, $equipo_WINXP) !== false){
			$tipo_dispositivo = "XP";
		}else if (strpos($user_browser, $equipo_WIN7) !== false){
			$tipo_dispositivo = "WIN_7";
		}else if (strpos($user_browser, $equipo_MAC) !== false){
			$tipo_dispositivo = "MAC";
		}
		
		return $tipo_dispositivo;
	}
	/******************** 
 	VM 09-05-2013
	 para todoinox se hizo esto!!
	 solucion parche!
	 lo curioso es que en la oficina si funciona
	 yo creo que es la version de "pcre"
	 en biggi y antuco
PCRE (Perl Compatible Regular Expressions) Support 	enabled
PCRE Library Version 	7.8 2008-09-05

	en la oficina
PCRE (Perl Compatible Regular Expressions) Support 	enabled
PCRE Library Version 	8.00 2009-10-19

*/
	 
	function parsearparametros( $param ) {
		// la Ñ la interpreta como separador de palabras
		 // entonces 'monseñor' son 2 palabras 'moneñ' y 'or'
		 // luego elimina 'or' porque piuensa que es inyeccion de codigo
		 
		$param =str_replace("ñ", "__ene__", $param );
		$param =str_replace("Ñ", "__ENE__", $param );

		$cross_site_scripting = array ( '@<script[^>]*?>.*?</script>@si', 	// Remover javascript
									   '@<[\/\!]*?[^<>]*?>@si' ); 			// Remover etiquetas HTML
		$inyeccion_sql = array ( '/\bAND\b/i', '/\bOR\b/i', '/\bSELECT\b/i',
							 '/\bFROM\b/i', '/\bWHERE\b/i', '/\bUPDATE\b/i',
							 '/\bDELETE\b/i', '/\b\*\b/i', '/\bCREATE\b/i',
							 '/\bDROP\b/i', '/\bTRUNCATE\b/i' );
		$retorno = preg_replace ( $inyeccion_sql, "", $param );
		$retorno = preg_replace ( $cross_site_scripting, "", $retorno );
		$retorno = str_replace("'", "''",  $retorno);

		$retorno =str_replace("__ene__", "ñ", $retorno );
		$retorno =str_replace("__ENE__", "Ñ", $retorno );

		return $retorno;
	}
	
	
	/*static function parsearparametros( $param ) {
		$cross_site_scripting = array ( '@<script[^>]*?>.*?</script>@si', 	// Remover javascript
									   '@<[\/\!]*?[^<>]*?>@si' ); 			// Remover etiquetas HTML
		$inyeccion_sql = array ( '/\bAND\b/i', '/\bOR\b/i', '/\bSELECT\b/i',
							 '/\bFROM\b/i', '/\bWHERE\b/i', '/\bUPDATE\b/i',
							 '/\bDELETE\b/i', '/\b\*\b/i', '/\bCREATE\b/i',
							 '/\bDROP\b/i', '/\bTRUNCATE\b/i' );
		$retorno = preg_replace ( $inyeccion_sql, "", $param );
		$retorno = preg_replace ( $cross_site_scripting, "", $retorno );
		$retorno = str_replace("'", "''",  $retorno);
		/*
		$retorno = stripslashes($retorno);
		$retorno = htmlentities( $retorno, ENT_QUOTES, 'iso-8859-1' );
		$retorno = trim( $retorno );
		/*
		return $retorno;
	}*/
	static function traducehtml( $param ) {
		$param = str_replace( "&Aacute;", "Á", $param );
		$param = str_replace( "&aacute;", "á", $param );
		$param = str_replace( "&Eacute;", "É", $param );
		$param = str_replace( "&eacute;", "é", $param );
		$param = str_replace( "&Iacute;", "Í", $param );
		$param = str_replace( "&iacute;", "í", $param );
		$param = str_replace( "&Oacute;", "Ó", $param );
		$param = str_replace( "&oacute;", "ó", $param );
		$param = str_replace( "&Uacute;", "Ú", $param );
		$param = str_replace( "&uacute;", "ú", $param );
		$param = str_replace( "&agrave;", "à", $param );
		$param = str_replace( "&Agrave;", "À", $param );
		$param = str_replace( "&egrave;", "è", $param );
		$param = str_replace( "&Egrave;", "È", $param );
		$param = str_replace( "&igrave;", "ì", $param );
		$param = str_replace( "&Igrave;", "Ì", $param );
		$param = str_replace( "&ograve;", "ò", $param );
		$param = str_replace( "&Ograve;", "Ò", $param );
		$param = str_replace( "&ugrave;", "ù", $param );
		$param = str_replace( "&Ugrave;", "Ù", $param );
		$param = str_replace( "&deg;", "°", $param );
		$param = str_replace( "&ntilde;", "ñ", $param );
		$param = str_replace( "&Ntilde;", "Ñ", $param );
		$param = str_replace( "&ordm;", "º", $param );
		$param = str_replace( "&ordf;", "ª", $param );
		$param = str_replace( "&uuml;", "ü", $param );
		$param = str_replace( "&Uuml;", "Ü", $param );
		$param = str_replace( "&#039;", "'", $param );
		$param = str_replace( "&acute;", "´", $param );
		$param = str_replace( "&Ccedil;", "Ç", $param );
		$param = str_replace( "&ccedil;", "ç", $param );
		$param = str_replace( "&quot;", '"', $param );
		return $param;
	}
	static function replace_KEYS($sql, $keys) {
		for($i=0; $i<count($keys); $i++)
			$sql = str_replace("{KEY".($i+1)."}", $keys[$i], $sql);
		return $sql;
	}
	static function str2date($fecha_str, $hora_str='00:00:00') {
		if ($fecha_str=='')
			return 'null';
		// Entra la fecha en formato dd/mm/yyyy		
		if (K_TIPO_BD=='mssql') {
			$res = explode('/', $fecha_str);
			if (strlen($res[2])==2)
				$res[2] = '20'.$res[2];
			return sprintf("{ts '$res[2]-$res[1]-$res[0] $hora_str.000'}");
		}
		else if (K_TIPO_BD=='oci')
			return "to_date('$fecha_str $hora_str', 'dd/mm/yyyy hh24:mi:ss')";
		else
			base::error("base.str2date, no soportado para ".K_TIPO_BD);
	}
	static function get_real_IP() {
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}
	static function clean_files($dir, $extension) {
	    //Borrar los ficheros temporales, que empiecen con "tmp" y cuya extension sea $extension
	    // y tengan más de 1 hora 
	    $t = time();
	    $h = opendir($dir);
	    while($file=readdir($h))
	    {
	        if(substr($file,0,3)=='tmp' && substr($file,-4)==".$extension")
	        {
	            $path = $dir.'/'.$file;
	            if($t-filemtime($path)>3600)
	                @unlink($path);
	        }
	    }
	    closedir($h);
	}
	
	static function valida_digito($num) {
	    $x=2;
 		$sumatorio=0;

 		while($num>0){
     		if ($x>7){
     			$x=2;
     		}
     		$a=$num%10;
      		$sumatorio=$sumatorio+($a*$x);
      		$x++;
      		$num = $num /10;
  		}
  		
  		$digito=$sumatorio%11;
	 	$digito=11-$digito;
	  
	   	switch ($digito) {
	    	case 10:
	    		$digito='K';
	       	break;
	     	case 11:
	        	$digito='0';
	       	break;
	    }
	return $digito;
	}
}
?>