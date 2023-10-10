<?php
require_once(dirname(__FILE__)."/auto_load.php");

$nom_header = $_REQUEST['nom_header'];
$valor_filtro1 = $_REQUEST['valor_filtro1'];
$valor_filtro2 = $_REQUEST['valor_filtro2'];
$sql = "select COD_MES, NOM_MES from MES order by COD_MES";

$temp = new Template_appl(session::get('K_ROOT_DIR').'html/dlg_find_mes.htm');	
$temp->setVar("PROMPT", 'Filtrar por '.utf8_encode($nom_header));

$control = new drop_down_dw('VALOR1', $sql);
$html = $control->draw_entrable($valor_filtro1, 0);
$temp->setVar("VALOR1", $html);

$control = new drop_down_dw('VALOR2', $sql);
$html = $control->draw_entrable($valor_filtro2, 0);
$temp->setVar("VALOR2", $html);

print $temp->toString();

?>