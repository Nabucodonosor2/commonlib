<?php
require_once(dirname(__FILE__)."/auto_load.php");

$nom_header = $_REQUEST['nom_header'];
$valor_filtro1 = $_REQUEST['valor_filtro1'];
$valor_filtro2 = $_REQUEST['valor_filtro2'];

$temp = new Template_appl(session::get('K_ROOT_DIR').'html/dlg_find_date.htm');	
$temp->setVar("PROMPT", 'Filtrar por '.$nom_header);

$control = new edit_date('VALOR1');
$control->usa_calendario = true;
$html = $control->draw_entrable($valor_filtro1, 0);
$temp->setVar("VALOR1", $html);

$control = new edit_date('VALOR2');
$control->usa_calendario = true;
$html = $control->draw_entrable($valor_filtro2, 0);
$temp->setVar("VALOR2", $html);

print $temp->toString();

?>