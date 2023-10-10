<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class Template_appl extends Template {	
	function Template_appl($filename, $js_onload='') {
		parent::Template($filename);
		$this->select_css();
		$this->setVar("W_CSS", session::get('K_ROOT_CSS'));
		$this->setVar('K_ROOT_URL', session::get('K_ROOT_URL'));
		if (session::is_set('K_CLIENTE')){
                  $this->setVar('K_CLIENTE', session::get('K_CLIENTE'));
        }
		
		$script = "onLoad=\"MM_preloadImages(";
		
		$script .= "'../../../../commonlib/trunk/images/b_add.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_add_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_add_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_add_d.jpg',";
				
		$script .= "'../../../../commonlib/trunk/images/b_back.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_back_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_back_click.jpg',";
		//$script .= "'../../../../commonlib/trunk/images/b_back_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_create.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_create_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_create_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_create_d.jpg',";	
				
		$script .= "'../../../../commonlib/trunk/images/b_export.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_export_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_export_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_export_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_first.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_first_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_first_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_first_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_last.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_last_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_last_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_last_d.jpg',";	
		
		$script .= "'../../../../commonlib/trunk/images/b_next.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_next_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_next_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_next_d.jpg',";
						
		$script .= "'../../../../commonlib/trunk/images/b_prev.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_prev_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_prev_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_prev_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/lupa1.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/lupa2.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/off_filter.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/on_filter.jpg'";
		
		$script .= ")\""; 
		$this->setVar('WO_ONLOAD', $script);
		
		$script = "onLoad=\"MM_preloadImages(";
		
		$script .= "'../../../../commonlib/trunk/images/b_add_line.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_add_line_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_back.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_back_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_back_click.jpg',";
		//$script .= "'../../../../commonlib/trunk/images/b_back_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_delete.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_delete_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_delete_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_delete_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_delete_line.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_delete_line_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_first.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_first_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_first_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_first_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_last.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_last_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_last_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_last_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_modify.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_modify_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_modify_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_modify_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_next.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_next_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_next_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_next_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_no_save.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_no_save_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_no_save_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_no_save_d.jpg',";		
		
		$script .= "'../../../../commonlib/trunk/images/b_prev.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_prev_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_prev_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_prev_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_print.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_print_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_print_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_print_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/b_save.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_save_over.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_save_click.jpg',";
		$script .= "'../../../../commonlib/trunk/images/b_save_d.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/calendar.png',";
		
		$script .= "'../../../../commonlib/trunk/images/ico2.gif',";
		
		$script .= "'../../../../commonlib/trunk/images/link.gif',";
		
		$script .= "'../../../../commonlib/trunk/images/lupa1.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/lupa2.jpg',";
		
		$script .= "'../../../../commonlib/trunk/images/minus.gif'";	
		
		$script .= "); $js_onload\"";
		$this->setVar('WI_ONLOAD', $script);
	}
	
	
	function select_css() {
		if(base::get_SO() == 'linux'){
			if (defined('K_CLIENTE')) {
				$file_name = session::get('K_ROOT_DIR')."css/".K_CLIENTE."/style_linux.css";
				if (file_exists($file_name)) {
					$file_name = session::get('K_ROOT_URL')."css/".K_CLIENTE."/style_linux.css";
					session::set('K_ROOT_CSS', $file_name);
					return;
				}			
			}

			// lo busca en el proyexto
			$file_name = session::get('K_ROOT_DIR')."css/style_linux.css";
			if (file_exists($file_name)) {
				$file_name = session::get('K_ROOT_URL')."css/style_linux.css";
				session::set('K_ROOT_CSS', $file_name);
				return;
			}			

			// else usa el de la commonlib
			session::set('K_ROOT_CSS', session::get('K_ROOT_URL')."../../commonlib/trunk/css/style_linux.css");		
		}
		elseif(base::get_SO() == 'mac'){
			if (defined('K_CLIENTE')) {
				$file_name = session::get('K_ROOT_DIR')."css/".K_CLIENTE."/style_mac.css";
				if (file_exists($file_name)) {
					$file_name = session::get('K_ROOT_URL')."css/".K_CLIENTE."/style_mac.css";
					session::set('K_ROOT_CSS', $file_name);
					return;
				}			
			}

			// lo busca en el proyexto
			$file_name = session::get('K_ROOT_DIR')."css/style_mac.css";
			if (file_exists($file_name)) {
				$file_name = session::get('K_ROOT_URL')."css/style_mac.css";
				session::set('K_ROOT_CSS', $file_name);
				return;
			}			

			// else usa el de la commonlib
			session::set('K_ROOT_CSS', session::get('K_ROOT_URL')."../../commonlib/trunk/css/style_mac.css");			
		}
		else {	// == 'windows' u otro
			if (defined('K_CLIENTE')) {
				$file_name = session::get('K_ROOT_DIR')."css/".K_CLIENTE."/style_win.css";
				if (file_exists($file_name)) {
					$file_name = session::get('K_ROOT_URL')."css/".K_CLIENTE."/style_win.css";
					session::set('K_ROOT_CSS', $file_name);
					return;
				}			
			}

			// lo busca en el proyexto
			$file_name = session::get('K_ROOT_DIR')."css/style_win.css";
			if (file_exists($file_name)) {
				$file_name = session::get('K_ROOT_URL')."css/style_win.css";
				session::set('K_ROOT_CSS', $file_name);
				return;
			}			

			// else usa el de la commonlib
			session::set('K_ROOT_CSS', session::get('K_ROOT_URL')."../../commonlib/trunk/css/style_win.css");			
		}
	}
}
?>