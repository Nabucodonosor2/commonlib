<?php
/*
 * Created on Apr 29, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once("../../class.RParser.php");
require_once("../../class.PDF.php");
require_once("../../class.ReportData.php");
//require_once("class.Database.php");
//require_once('class.Peters_Page.php');

//$cfg = parse_ini_file('../config.ini',true);
//$page = new Peters_Page();
//$page->setLogger(false);

$p = &new ReportParser();
$p->parseRP('kiszla.xml');
$data = array("nyomat"=>"Eredeti nyomat","peldany"=>"1/2. példány","sbszsz"=>"12345678-12345678-12345678"
			,"Szlasz"=>"valami masodik sor","SzlaMegjegyzesek"=>"A konfigurálásban benne foglaltatott szolgáltatások:\nvírus írtás, ideiglenes állományok törlése, operációs rendszer, valamint alkalmazások karbantartása  fjkdlaéfj dksafjéd slajf désalfj dséalfj désalfj dséafj déasljf dsaél\n\nKöszönjük, hogy igénybe vette szolgáltatásunkat!");
//$db=Database::instance();
//$res=$db->select("select p.name,p.value from reportparameters rp,parameter p where rp.parameter_id=p.parameter_id and report_id=1");
$params=array();
//while($row=$res->fetchRow())
//	$params["str".$row['name']]=$row['value'];
//$rdata = new ReportData("select megnevezes as Kshkod,rovidkod as Megn from afakod");
//$rdata = new ReportData("select name as Kshkod,value as Megn from parameter");
$rdata = new ReportData(array(
		array("Kshkod"=>"123")
		,array("Kshkod"=>"854")
		,array("Kshkod"=>"658")
		,array("Kshkod"=>"125")
		,array("Kshkod"=>"2154")
		,array("Kshkod"=>"25632")
		,array("Kshkod"=>"12")
		,array("Kshkod"=>"1235")
		));

$p1 = &new ReportParser();
$p1->parseRP('kiszla.xml');
$data1 = array("nyomat"=>"Eredeti nyomatka","peldany"=>"2/2. példány","sbszsz"=>"12345678-12345678-12345678"
			,"Szlasz"=>"valami masodik sor","SzlaMegjegyzesek"=>"A konfigurálásban benne foglaltatott szolgáltatások:\nvírus írtás, ideiglenes állományok törlése, operációs rendszer, valamint alkalmazások karbantartása  fjkdlaéfj dksafjéd slajf désalfj dséalfj désalfj dséafj déasljf dsaél\n\nKöszönjük, hogy igénybe vette szolgáltatásunkat!");
//$rdata1 = new ReportData("select nev as Kshkod,irszam as Megn from partner");
//$rdata1 = new ReportData("select megnevezes as Kshkod,rovidkod as Megn from afakod");
$rdata1 = new ReportData(array(
		array("Kshkod"=>"1234")
		,array("Kshkod"=>"8541")
		,array("Kshkod"=>"6583")
		,array("Kshkod"=>"1256")
		,array("Kshkod"=>"2154")
		,array("Kshkod"=>"25632")
		,array("Kshkod"=>"12")
		,array("Kshkod"=>"1235")
		));


$pdf=PDF::makePDF(array($p,$p1),array(array_merge($data,$params),array_merge($data1,$params)),array($rdata,$rdata1));
$pdf->Output("test.pdf","D");
?>