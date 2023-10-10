<?php

	class datos_server {
		
		function datos_server(){
			/*
			require_once(dirname(__FILE__)."/../common_appl/Class_email.php");
			$ds = new datos_server();
			echo $ds->getRealIP();
			echo "<br>".$ds->so();
			echo "<br>".$ds->browser();
			*/
		}
		function getRealIP() {
			if (!empty($_SERVER['HTTP_CLIENT_IP']))
				return $_SERVER['HTTP_CLIENT_IP'];
				
			if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			
			return $_SERVER['REMOTE_ADDR'];
		}
		private function detect()
		{
			$browser=array("IE","OPERA","MOZILLA","NETSCAPE","FIREFOX","SAFARI","CHROME");
			$os=array("WINDOWS","MAC","LINUX");
			
			# definimos unos valores por defecto para el navegador y el sistema operativo
			$info['browser'] = "OTHER";
			$info['os'] = "OTHER";
			
			# buscamos el navegador con su sistema operativo
			foreach($browser as $parent)
			{
				$s = strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $parent);
				$f = $s + strlen($parent);
				$version = substr($_SERVER['HTTP_USER_AGENT'], $f, 15);
				$version = preg_replace('/[^0-9,.]/','',$version);
				if ($s)
				{
					$info['browser'] = $parent;
					$info['version'] = $version;
				}
			}
			
			# obtenemos el sistema operativo
			foreach($os as $val)
			{
				if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']),$val)!==false)
					$info['os'] = $val;
			}
			
			# devolvemos el array de valores
			return $info;
		}
		function so()
		{
			$so = $this->detect();
			return $so["os"];
		}
		function browser()
		{
			$browser = $this->detect();
			return $browser["browser"]." ".$browser["version"];
		}
	}
?>