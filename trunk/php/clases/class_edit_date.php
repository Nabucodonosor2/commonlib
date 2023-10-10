<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_date extends edit_text {
	var $usa_calendario = false;
	
	function edit_date($field, $size=10, $maxlen=10, $usa_calendario = false) {
		parent::edit_text($field, $size, $maxlen, 'text');
		$this->onKeyUp = 'vl_fecha = filter_edit_date(this.value); if (this.value != vl_fecha) this.value = vl_fecha;';
		$this->usa_calendario = $usa_calendario;
	}
	function validate($valor) {
		if ($valor=='')		// Vacio esta permitido
			return '';
			
		$date = explode('/', $valor);
		$day = isset($date[0]) ? $date[0] : 0;
		if ($day =='') $day = 0;
		$month = isset($date[1]) ? $date[1] : 0; 
		if ($month =='') $month = 0;
		$year = isset($date[2]) ? $date[2] : 0; 
		if ($year =='') $year = 0;
		
		if (is_numeric($day) && is_numeric($month) && is_numeric($year)) {
			if (checkdate($month, $day, $year))
				return '';		// no error
		}
		
		return 'La fecha es invalida.';
	}
	function draw_entrable($dato, $record) {
		$ctrl = parent::draw_entrable($dato, $record);
		if ($this->usa_calendario) {
			$btn = '<button type="button" id="B_'.$this->field.'_'.$record.'" style="background-image:url('.$this->root_url.'../../commonlib/trunk/script_js/calendario/css/img/icono_calendario.png);height:20px;width:28px;"></button>';
			$btn .= '<script type="text/javascript">
						new Calendar({
							inputField: "'.$this->field.'_'.$record.'",
							dateFormat: "%d/%B/%Y",
							trigger: "B_'.$this->field.'_'.$record.'",
							bottomBar: false,
							onSelect: function() {
								var date = Calendar.intToDate(this.selection.get());
								this.hide();
							}
							});
					</script>';
			$ctrl .= $btn; 
		}
		
										      
		
		return $ctrl;
	}
}
?>