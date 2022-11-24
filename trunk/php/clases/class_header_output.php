<?php
require_once(dirname(__FILE__)."/../auto_load.php");

abstract class header_output extends base {
	public			$field;
	public			$field_bd;		// Nombre de la columna para hacer la condicion del where, normalmente es igual a $field
	public			$field_bd_order;// Nombre de la columna para hacer la condicion del order by, normalmente es igual a $field
	public			$nom_header;
	public			$valor_filtro = '';
	public			$operacion_accumulate;
	public			$valor_accumulate;
	public			$sorteable = true;
	
	function header_output($field, $field_bd, $nom_header, $operacion_accumulate='') {
		/* select NOM_TABLA,
		 * 		  dbo.f_get_valor() VALOR
		 * from tabla
		 * 
		 * entonces para los 2 header se llama de la sgte forma:
		 * header_output('NOM_TABLA', 'NOM_TABLA', 'Nombre de la tabla');
		 * header_output('VALOR', 'dbo.f_get_valor()', 'Valor');
		 * 
		 */
		$this->field = $field; 
		$this->field_bd = $field_bd;
		$this->field_bd_order = $this->field_bd;	// por defecto es el mismo, pero se puede cambiar. Normalmente debe ser necesario cambiarlo en los dropdown
		$this->nom_header = $nom_header;
		$this->operacion_accumulate = $operacion_accumulate;
	}
	abstract function make_java_script();
	function draw_header(&$temp, $field_sort='') {
		if ($this->field==$field_sort) 
			$nom_header = "<u>".$this->nom_header."</u>";
		else 
			$nom_header = $this->nom_header;
		
		if ($this->sorteable)
			$onclik = ' onclick="set_order(\''.$this->field.'\')"'; 
		else
			$onclik = ''; 
		
		$html = '<table width="100%"><tr><td class="encabezado_center" align="center" width="95%"><label '.$onclik.'";>'.$nom_header.'</label></td><td align="right"><input id="b_header_'.$this->field.'" type="button" name="b_header_'.$this->field.'"';
		$html .= ' onclick='.$this->make_java_script();
		if (strlen($this->valor_filtro) == 0)
			$html .= ' src="../../../../commonlib/trunk/images/off_filter.jpg" style="background-image:url(../../../../commonlib/trunk/images/off_filter.jpg); border-color:#919191; border-style: solid; cursor: pointer; background-repeat:no-repeat;background-position:center;"/></td></tr></table>';
		else
			$html .= ' src="../../../../commonlib/trunk/images/on_filter.jpg" style="background-image:url(../../../../commonlib/trunk/images/on_filter.jpg); border-color:#919191; border-style: solid; cursor: pointer; background-repeat:no-repeat;background-position:center;"/></td></tr></table>';
		$temp->setVar('H_'.$this->field, $html);
	}
	function set_value_filtro($valor_filtro) {
		if ($valor_filtro == '__BORRAR_FILTRO__')
			$this->valor_filtro = '';
		else
			$this->valor_filtro = $valor_filtro;
	}
	function get_value_filtro() {
		return $this->valor_filtro;
	}
	abstract function make_filtro();
	abstract function make_nom_filtro();
	function draw_valor_accumulate() {
		return number_format($this->valor_accumulate, 0, ',', '.');
	}
/******************* FILTRO EN EL INPUT     ******/
	function draw_header_input(&$temp) {
		$nom_header = $this->nom_header;
		
		$html = '<table width="100%"><tr><td class="encabezado_center" align="center" width="95%">'.$nom_header.'</td><td align="right">';
		$html .= '<image id="b_header_'.$this->field.'"';
		$html .= ' onclick='.$this->make_java_script();
		if (strlen($this->valor_filtro) == 0)
			$html .= ' src="../../../../commonlib/trunk/images/off_filter.jpg"/></td></tr></table>';
		else
			$html .= ' src="../../../../commonlib/trunk/images/on_filter.jpg"/></td></tr></table>';
		$temp->setVar('H_'.$this->field, $html);
	}
/******************* FIN EN EL INPUT     ******/
}
?>