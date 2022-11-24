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
$p->parseRP('documents.xml');
$data = array("Manager"=>"John Smith");
$rdata = new MySQLRD("SELECT documentid,position+1 as pos,name,quantity,cost,quantity*cost as price from pos p,product pr WHERE pr.id=p.productid order by documentid,position");

$db=Database::instance();
$res=$db->select("select name,value from parameter");
$params=array();
while($row=$res->fetchRow())
	$params["str".$row['name']]=$row['value'];


$pdf=PDF::makePDF(array($p),array(array_merge($data,$params)),array($rdata));
$pdf->Output("simplesum.pdf","D");
?>