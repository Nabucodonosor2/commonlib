<?php
require_once(dirname(__FILE__)."/auto_load.php");
$t = new Template_appl("banner.htm");
print $t->toString();
?>