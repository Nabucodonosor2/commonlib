<?php
require_once(dirname(__FILE__)."/auto_load.php");

$nom_header = $_REQUEST['nom_header'];
$valor_filtro1 = $_REQUEST['valor_filtro1'];
$valor_filtro2 = $_REQUEST['valor_filtro2'];
$valor_filtro3 = $_REQUEST['valor_filtro3'];
$cant_decimal = $_REQUEST['cant_decimal'];
$solo_positivos = $_REQUEST['solo_positivos'];

$temp = new Template_appl(session::get('K_ROOT_DIR').'html/dlg_find_num.htm');	
$temp->setVar("PROMPT", 'Filtro por Rango de '.$nom_header);
$temp->setVar("PROMPT2", 'Filtro por Valores de '.$nom_header);

$control = new edit_num_header('VALOR1', 16, 16, $cant_decimal, $solo_positivos);
$html = $control->draw_entrable($valor_filtro1, 0);
$temp->setVar("VALOR1", $html);

$control = new edit_num_header('VALOR2', 16, 16, $cant_decimal, $solo_positivos);
$html = $control->draw_entrable($valor_filtro2, 0);
$temp->setVar("VALOR2", $html);

$control = new edit_text('VALOR3', 43, 200);
$control->set_onKeyPress("return onlyNumbersSpecial(this, event,1, 1);");
$html = $control->draw_entrable($valor_filtro3, 0);
$temp->setVar("VALOR3", $html);

print $temp->toString();

?>