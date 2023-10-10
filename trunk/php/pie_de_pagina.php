<?php
require_once(dirname(__FILE__)."/auto_load.php");
$t = new Template_appl(session::get('K_ROOT_DIR')."html/pie_de_pagina.htm");
print $t->toString();
?>