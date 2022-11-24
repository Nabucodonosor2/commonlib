<?php
require_once(dirname(__FILE__)."/auto_load.php");

$prompt = $_REQUEST['prompt'];
$valor =  $_REQUEST['valor'];
$temp = new Template_appl(session::get('K_ROOT_DIR').'html/request.htm');	
$temp->setVar("PROMPT", $prompt);

$edit_num = new edit_num('VALOR', 6, 6);
$temp->setVar("VALOR", $edit_num->draw_entrable($valor, 0));

print $temp->toString();
?>