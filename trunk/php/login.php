<?php
require_once(dirname(__FILE__)."/auto_load.php");


/**************
Clase : W_LOGIN
**************/
class w_login extends w_base {
	function w_login() {
		// No llama al parent, sino q llama al abuelo
		parent::base();
		$this->work_directory = getcwd();
		session::un_set("COD_USUARIO");
	}
	function login($login, $password) {
		$t = new Template_appl($this->root_dir."html/login.htm");
		$t->setVar('W_LOGIN', $login);
		$t->setVar('W_PASSOWRD', $password);
		$t->setVar("W_FECHA_ACTUAL", $this->current_date());
		print $t->toString();

		session::un_set("COD_USUARIO");
		print '<script type="text/javascript">
						var username = document.getElementById("login"); 
						username.focus();
					 </script>';
	}
	function validate_login() {
		$login = base::parsearParametros(strtoupper($_POST['login']));
		$password = $_POST['password'];
		$passwd = $this->f_encriptar($password);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select 	PASSWORD,
           				AUTORIZA_INGRESO,
           				COD_USUARIO,
           				NOM_USUARIO,
           				COD_PERFIL
				from 	USUARIO
    			where 	LOGIN = '$login'";
		$result = $db->build_results($sql);
		if (count($result)==0) {
			$this->login(str_replace("''", "'", $login), $password);
			$this->message('Usuario inexistente.');
			return;
		}
		$passwd_bd = $result[0]['PASSWORD'];
		$autorizado_ingreso = $result[0]['AUTORIZA_INGRESO'];
		$cod_usuario = $result[0]['COD_USUARIO'];
		$nom_usuario = $result[0]['NOM_USUARIO'];
		$cod_perfil = $result[0]['COD_PERFIL'];
		if ($autorizado_ingreso=='N') {
			$this->login(str_replace("''", "'", $login), $password);
			$this->message('Usuario no autorizado a ingresar.');
			return;
		}
		elseif ($passwd <> $passwd_bd) {
			$this->login(str_replace("''", "'", $login), $password);
			$this->message('Password incorrecto');
			print '<script type="text/javascript">
							var passwd = document.getElementById("password"); 
							passwd.focus();
						 </script>';
			return;
		}
		session::set("COD_USUARIO", $cod_usuario);
		session::set("NOM_USUARIO", $nom_usuario);
		session::set("COD_PERFIL", $cod_perfil);
		base::spd_lock_table_user();
		
		$db->EXECUTE_SP("sp_log_cambio", "'LOGIN', '', $cod_usuario, 'L'");
		
		$this->presentacion();
	}
}
$w = new w_login();
if (!isset($_POST['b_ingresar']))
	$w->login('','');
else
	$w->validate_login();
?>