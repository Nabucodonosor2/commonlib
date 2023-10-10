<?php
/*
 * Created on Dec 04, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once("../../class.RParser.php");
require_once("../../class.PDF.php");
require_once("class.MySQLRD.php");
require_once("../../class.Database.php");

$cfg = parse_ini_file('config.ini',true);

$p = &new ReportParser();
$p->parseRP('addresses.xml');
$data = array("Manager"=>"John Smith");
$rdata = new MySQLRD("select * from address");

$db=Database::instance();
$res=$db->select("select name,value from parameter");
$params=array();
while($row=$res->fetchRow())
	$params["str".$row['name']]=$row['value'];


$pdf=PDF::makePDF(array($p),array(array_merge($data,$params)),array($rdata));
$pdf->Output("simplecount.pdf","D");
?>