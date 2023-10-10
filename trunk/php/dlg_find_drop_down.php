<?php
require_once(dirname(__FILE__)."/auto_load.php");

$nom_header = $_REQUEST['nom_header'];
$valor_filtro = $_REQUEST['valor_filtro'];
$sql = $_REQUEST['sql'];

$so = base::get_SO();
if ($so == 'windows')
	$sql = str_replace("\'", "'",$sql);

$temp = new Template_appl(session::get('K_ROOT_DIR').'html/dlg_find_drop_down.htm');	
$temp->setVar("PROMPT", 'Filtrar por '.utf8_encode($nom_header));
$drop_down = new drop_down_dw('VALOR', $sql);
$html = $drop_down->draw_entrable($valor_filtro, 0);
$temp->setVar("VALOR", $html);

print $temp->toString();

?>