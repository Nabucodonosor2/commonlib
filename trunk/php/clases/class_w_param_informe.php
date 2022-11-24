<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class w_param_informe extends w_input {
	var $nom_informe;
	var $sp ='';
	var $param='';
	var $xml='';
	var $sql_informe='';
	
	function w_param_informe($nom_tabla, $cod_item_menu, $nom_informe, $xml, $sql_informe, $sp='') {
		$this->tiene_wo = false;
		parent::w_input($nom_tabla, $cod_item_menu);
		$this->set_modify(true);
		$this->nom_informe = $nom_informe;
		$this->xml = $xml;
		$this->sql_informe = $sql_informe;
		$this->sp = $sp;
	}
	function load_record() {
		$this->dws['dw_param']->retrieve();
	}
	function _habilitar(&$temp, $habilita) {
		$this->habilitar($temp, $habilita);	// llamado a funcion virtual
		// habilita las dws del input
		$indices = array_keys($this->dws);
		for ($i=0; $i < count($this->dws); $i++) {
			$this->dws[$indices[$i]]->nom_tabla	= $this->nom_tabla;		// se copia siempre
			$this->dws[$indices[$i]]->habilitar($temp, $habilita);
		}
		$this->habilita_boton($temp, 'back', true);
		$this->save_SESSION();
	}
	function get_key() {
		return 0;
	}
	function make_filtro() {
		$this->filtro = '';
	}
	function ejecuta_informe() {
		$this->get_values_from_POST();

		// validacióN
		$error = $this->_validate_record();
		if ($error != '') {
			$this->redraw();
			$this->message($error);
			return false;
		}
		
		// arma el filtro
		$this->make_filtro();
		
		return true;
	}
	function genera_pdf($labels = array(), $con_logo = true,$orientation='P',$unit='pt',$format='letter') {
		$labels['str_filtro'] = $this->filtro;
		$rpt = new reporte($this->sql_informe, $this->xml, $labels, $this->nom_informe, $con_logo, true, $this->sp, $this->param,$orientation,$unit,$format);
	}
	function genera_xls() { 
		$dw = new datawindow($this->sql_informe);
		$dw->nom_tabla = $this->nom_informe;	//	 Se utiliza campo $dw->nom_tabla de modo auxiliar para pasar el nombre del informe
		session::set('DATAWINDOW_XLS', $dw);
		session::set('DATAWINDOW_XLS_SP', $this->sp);
		session::set('DATAWINDOW_XLS_PARAM', $this->param);
		
		$this->presentacion('', " <script>window.open('enviar_xls.php','Informe'); </script>");
	}
	function genera_xls_dos() { 
		$dw = new datawindow($this->sql_informe);
		$dw->nom_tabla = $this->nom_informe;	//	 Se utiliza campo $dw->nom_tabla de modo auxiliar para pasar el nombre del informe
		session::set('DATAWINDOW_XLS_DOS', $dw);
		session::set('DATAWINDOW_XLS_SP_DOS', $this->sp);
		session::set('DATAWINDOW_XLS_PARAM_DOS', $this->param);
		
		$this->presentacion('', " <script>window.open('enviar_xls_dos.php','Informe'); </script>");
	}
	
	function procesa_event() {
		if(isset($_POST['b_pdf'])) {
			if ($this->ejecuta_informe())
				$this->genera_pdf();
		}
		elseif(isset($_POST['b_excel'])) {
			if ($this->ejecuta_informe())
				$this->genera_xls();
		}
		elseif(isset($_POST['b_excel_dos'])) {
			if ($this->ejecuta_informe())
				$this->genera_xls_dos();
		}
		elseif(isset($_POST['b_back_x'])) {
			$this->presentacion();
		}
		else
			parent::procesa_event();
	}
}
?>