<?php
require_once(dirname(__FILE__)."/auto_load.php");

$nom_header = $_REQUEST['nom_header'];
$valor_filtro = $_REQUEST['valor_filtro'];

$temp = new Template_appl(session::get('K_ROOT_DIR').'html/dlg_find_text.htm');	
$temp->setVar("PROMPT", 'Filtrar por '.utf8_encode($nom_header));

$control = new edit_text('VALOR', 40, 50);
$html = $control->draw_entrable($valor_filtro, 0);
$temp->setVar("VALOR", $html);

print $temp->toString();

?>