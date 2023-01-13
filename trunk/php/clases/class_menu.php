<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class menu extends base {
	public  $menu = array();
	public	$ancho_completa_menu;		// Es el ancho del objeto que esta despues del ultimo menu y antes del icono home para que el menu de el ancho deseado

	function menu($menu, $ancho_completa_menu) {
		$this->menu = $menu;
		$this->ancho_completa_menu = $ancho_completa_menu;
	}
	function set_visible($cod_item_menu, $visible) {
	}
	function set_link($cod_item_menu, $link) {
	}
	function make_java() {
		$script = '';
		return $script;
	}
	function get_privilegio_opcion($cod_item_menu, $cod_usuario) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select a.AUTORIZA_MENU
		        from   AUTORIZA_MENU a, USUARIO u
		        where  u.COD_USUARIO = ".$cod_usuario." and
		               a.COD_PERFIL = u.COD_PERFIL and 
		               a.COD_ITEM_MENU = '".$cod_item_menu."'";
		$result = $db->build_results($sql);
		if (count($result)==0)
			return 'N';
		else
			return $result[0]['AUTORIZA_MENU'];
	}
	function draw_menu(&$menu, $nivel, $parent) {
		$script = '';
		$cod_usuario = session::get("COD_USUARIO");
		for ($i=0; $i < count($menu); $i++) {
			if (!$menu[$i]->visible)
				continue;
				
			$priv = $this->get_privilegio_opcion($menu[$i]->cod_item_menu, $cod_usuario);
			if ($priv == 'N')	// disable
				$enable = false;
			else
				$enable = true;				
				
			if ($nivel==0)
				$script .= 'it='.$parent.'.addItemWithImages(4,5,5,"'.$menu[$i]->name.'",n,n,"",n,n,n,3,3,3,2,2,2,"",n,n,n,n,n,0,0,2,n,5,5,n,6,6,0,1,1,0,0,n,n,n,0,0,0,4,n);';
			else if ($menu[$i]->name == '-')
				$script .= 'it='.$parent.'.addItemWithImages(11,12,12,"","","","",16,16,16,3,3,3,n,n,n,"",n,n,n,n,n,0,2,2,n,n,n,n,n,n,0,0,0,0,0,n,n,n,0,0,0,9,n);';
			else if (count($menu[$i]->children))
				$script .= 'it=s1.addItemWithImages(9,10,10," &nbsp; &nbsp;'.$menu[$i]->name.'",n,n,"",7,7,7,3,3,3,8,8,8,"#",n,n,n,"#",n,0,0,2,11,12,12,13,14,14,1,1,1,0,0,n,n,n,0,0,0,n,n);';
			else {
				//MH 13/01/2023: Se hace este cambio para que algunos usuarios no puedan ver el nombre de la opcion deshabilitada
				// Como tambien ciertos usuarios de biggi puedan visualizarlo de todas formas pero en disable
				if ($enable)
					$script .= 'it='.$parent.'.addItemWithImages(9,10,10," &nbsp; &nbsp;'.$menu[$i]->name.'",n,n,"",7,7,7,3,3,3,n,n,n,"'.$menu[$i]->link.'",n,n,n,"'.$menu[$i]->link.'",n,0,0,2,11,12,12,13,14,14,1,1,1,0,0,n,n,n,0,0,0,10,n);';
				else if($enable == false && ($cod_usuario == 1 || $cod_usuario == 2 || $cod_usuario == 4 || $cod_usuario == 71 || $cod_usuario == 40 || $cod_usuario == 32))
					$script .= 'it='.$parent.'.addItemWithImages(14,15,15," &nbsp; &nbsp;'.$menu[$i]->name.'",n,n,"",7,7,7,3,3,3,n,n,n,"",n,n,n,n,n,0,0,2,11,12,12,13,14,14,1,1,1,0,0,n,n,n,0,0,0,n,n);';
				else
					$script .= 'it='.$parent.'.addItemWithImages(14,15,15," &nbsp; &nbsp;",n,n,"",7,7,7,3,3,3,n,n,n,"",n,n,n,n,n,0,0,2,11,12,12,13,14,14,1,1,1,0,0,n,n,n,0,0,0,n,n);';
			}
			if (count($menu[$i]->children)) {
				$script .= 'var s'.($nivel+1).'=it.addSubmenu(0,0,-1,0,0,0,0,8,0,1,0,n,n,100,0,4,0,-1,1,200,200,0,0,"0,0,0",0,"0",1);';
				$script .= $this->draw_menu($menu[$i]->children, $nivel + 1, 's'.($nivel+1));
			}
		}
		return $script;
	}
	function draw_menu_IPAD(&$temp) {
		// Este menu se activa en iPad y solo esta hecho para salir del paso en Biggi
		$root_url = session::get('K_ROOT_URL');
		$ipad_menu = '<a href="'.$root_url.'../../commonlib/trunk/php/mantenedor.php?modulo=empresa&cod_item_menu=1005">Empresa</a>&nbsp&nbsp';
		$ipad_menu .= '<a href="'.$root_url.'../../commonlib/trunk/php/mantenedor.php?modulo=producto&cod_item_menu=1010">Producto</a>&nbsp&nbsp';
		$ipad_menu .= '<a href="'.$root_url.'../../commonlib/trunk/php/mantenedor.php?modulo=cotizacion&cod_item_menu=1505">Cotización</a>';
		$temp->setVar('IPAD_MENU', $ipad_menu);	
	}
	function draw(&$temp) {
		$os = base::get_tipo_dispositivo();
		if($os == 'IPAD'){
            $this->draw_menu_IPAD($temp);
            return;
		}
		///////
		
		
$script = 'function awmBuildMenu(){
if (awmSupported){
awmImagesColl=["main-header.jpg",4,32,"main-footer.jpg",4,32,"indicator.png",9,32,"main-button-tile.jpg",21,32,"main-buttonOver-tile.jpg",21,32,"main-buttonOver-left.jpg",21,32,"main-buttonOver-right.jpg",21,32,"icon-awmlite.gif",16,16,"hassubmenu.gif",4,7,"sub-button-tile.jpg",20,26,"sub-buttonOver-tile.jpg",20,26,"sub-button-left.jpg",34,26,"sub-buttonOver-left.jpg",34,26,"sub-button-right.jpg",34,26,"sub-buttonOver-right.jpg",34,26,"separator.jpg",227,2,"spacer.gif",1,1,"home.png",20,20,"cerrar.png",20,20];';
$script .= "awmCreateCSS(1,2,1,'#FFFFFF',n,n,'14px sans-serif',n,'none','0','#000000','0px 0px 0px 0',0);
awmCreateCSS(0,2,1,'#FFFFFF',n,n,'14px sans-serif',n,'none','0','#000000','0px 0px 0px 0',0);
awmCreateCSS(1,2,1,'#000000',n,n,'14px sans-serif',n,'none','0','#000000','0px 0px 0px 0',0);
awmCreateCSS(0,1,0,n,n,n,n,n,'none','0','#000000',0,0);
awmCreateCSS(1,2,1,'#FFFFFF',n,3,'12px Verdana',n,'none','0','#000000','0px 15px 0px 25',1);
awmCreateCSS(0,2,1,'#FFFFFF',n,4,'12px Verdana',n,'none','0','#000000','0px 15px 0px 25',1);
awmCreateCSS(1,2,1,'#FFFFFF',n,3,'12px Verdana',n,'none','0','#000000','0px 0px 0px 10',1);
awmCreateCSS(0,2,1,'#FFFFFF',n,4,'12px Verdana',n,'none','0','#000000','0px 0px 0px 10',1);
awmCreateCSS(0,1,0,n,n,n,n,n,'solid','1','#808080',0,0);
awmCreateCSS(1,2,0,'#000000',n,9,'12px Verdana',n,'none','0','#000000','0px 10px 0px 7',1);
awmCreateCSS(0,2,0,'#000000',n,10,'12px Verdana',n,'none','0','#000000','0px 10px 0px 7',1);
awmCreateCSS(1,2,0,'#000000',n,15,'12px Verdana',n,'none','0','#000000','0px 0px 0px 0',1);
awmCreateCSS(0,2,0,'#000000',n,15,'12px Verdana',n,'none','0','#000000','0px 0px 0px 0',1);
awmCreateCSS(0,2,0,'#000000',n,15,'13px Verdana',n,'none','0','#000000','0px 0px 0px 0',1);
awmCreateCSS(1,2,0,'#AAAAAA',n,9,'13px Verdana',n,'none','0','#000000','0px 10px 0px 7',1);
awmCreateCSS(0,2,0,'#AAAAAA',n,10,'13px Verdana',n,'none','0','#000000','0px 10px 0px 7',1);
awmCreateCSS(1,2,0,'#000000',n,9,'13px Verdana',n,'none','0','#000000','0px 10px 0px 7',1);
awmCreateCSS(0,2,0,'#000000',n,10,'13px Verdana',n,'none','0','#000000','0px 10px 0px 7',1);
";

$script .= 'var s0=awmCreateMenu(0,0,0,0,1,0,0,0,0,0,5,0,1,3,0,0,1,n,n,100,1,0,0,0,511,-1,1,200,200,0,0,0,"0,0,0",n,n,n,n,n,n,n,n,0,0,0,0,0,0,0,0,1);
it=s0.addItemWithImages(0,1,1,"","","","",0,0,0,3,3,3,n,n,n,"",n,n,n,n,n,0,0,0,n,n,n,n,n,n,0,0,0,0,0,n,n,n,0,0,0,n,n);';
$script .= $this->draw_menu($this->menu, 0, 's0');

$script .= 'it=s0.addItemWithImages(6,7,7,"",n,n,"",n,n,n,3,3,3,n,n,n,"",n,n,n,n,n,'.$this->ancho_completa_menu.',0,2,n,5,5,n,6,6,0,1,1,0,0,n,n,n,0,0,0,49,n);
it=s0.addItemWithImages(6,7,7,"","","","",17,17,17,3,3,3,n,n,n,"../../../commonlib/trunk/php/presentacion.php",n,n,n,"../../../commonlib/trunk/php/presentacion.php",n,80,0,2,n,5,5,n,6,6,0,1,1,0,0,n,n,n,0,0,0,50,n);
it=s0.addItemWithImages(6,7,7,"","","","",18,18,18,3,3,3,n,n,n,"../../../commonlib/trunk/php/cerrar_sesion.php",n,n,n,"../../../commonlib/trunk/php/cerrar_sesion.php",n,0,0,2,n,5,5,n,6,6,0,1,1,0,0,n,n,n,0,0,0,51,n);
it=s0.addItemWithImages(2,1,1,"","","","",1,1,1,3,3,3,n,n,n,"",n,n,n,n,n,0,0,0,n,n,n,n,n,n,0,0,0,0,0,n,n,n,0,0,0,n,n);
s0.pm.buildMenu();
}}
awmBuildMenu();';

		$temp->setVar('W_MENU', $script);	
	}
}
?>