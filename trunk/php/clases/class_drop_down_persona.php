<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class drop_down_persona extends drop_down_dw {
	function drop_down_persona($field) {
		$sql = "select 		P.COD_PERSONA,
							P.NOM_PERSONA 
				from		PERSONA P,SUCURSAL S
				where		P.COD_SUCURSAL = S.COD_SUCURSAL
				and			S.COD_EMPRESA = {KEY1}
				order by	P.NOM_PERSONA ASC";	
		parent::drop_down_dw($field, $sql, 150);
		$this->set_onChange("mail_cargo_persona(this);");
	}
	function draw_entrable_help($cod_empresa) {
		/* VMC, 26-2-09
		   Al llamar a draw_entrable() se pasa como nro de registro 0
		   esto implica que si se esta en una dw tipo items donde se deban ingresar las sucursales va a FALLAR!!
		   ahora si es una dw tipo dw_cotizacion u otra parecida va a funcionar sin problemas pues el record es siempre 0
		   
		   lo mismo en drop_down_sucursal
		*/
		$this->retrieve($cod_empresa);
		$drop_down = $this->draw_entrable('', 0);
		return $drop_down;
	}
}
?>