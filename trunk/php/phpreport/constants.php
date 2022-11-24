<?php
/*
 * Created on 2005.02.28.
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


define( "CR", "\n");
define( "BR", "<br>");
define( "HR", "<hr>");
define( "SP", "&nbsp;");
define( "CB", "<center>");
define( "CE", "</center>");
define( "P", "<p>");
define( "TB", "<table>");
define( "TE", "</table>");
define( "TRB", "<tr>");
define( "TRE", "</tr>");
define( "TDB", "<td>");
define( "TDE", "</td>");

function expand($s,$l){
	$rv=$s;
	if(strlen($s)>$l){
		$rv=substr($s,0,$l);
	}else{
		for($i=0;$i<$l-strlen($s);$i++){
			$rv.=SP;
		}
	}
	return $rv;
}

function expandr($s,$l){
	$rv=$s;
	if(strlen($s)>$l){
		$rv=substr($s,0,$l);
	}else{
		$rv="";
		for($i=0;$i<$l-strlen($s);$i++){
			$rv.=SP;
		}
		$rv.=$s;
	}
	return $rv;
}

function arraykeytolower($a){
	$rv=array();
	foreach($a as $k=>$v ){
		$rv[strtolower($k)] = $v;
	}
	return $rv;
}

function writeNumberGroup( $s,$f,$g ) {
	$szazasok=array(1=>"egyszáz",2=>"kettõszáz",3=>"háromszáz",4=>"négyszáz",5=>"ötszáz"
			,6=>"hatszáz",7=>"hétszáz",8=>"nyolcszáz",9=>"kilencszáz");
	$tizesek=array(
			false=>array(1=>"tíz",2=>"húsz",3=>"harminc",4=>"negyven",5=>"ötven"
				,6=>"hatvan",7=>"hetven",8=>"nyolcvan",9=>"kilencven")
			,true=>array(1=>"tizen",2=>"huszon",3=>"harminc",4=>"negyven",5=>"ötven"
				,6=>"hatvan",7=>"hetven",8=>"nyolcvan",9=>"kilencven")
			);
	$egyesek=array(1=>"egy",2=>"kettõ",3=>"három",4=>"négy",5=>"öt"
			,6=>"hat",7=>"hét",8=>"nyolc",9=>"kilenc");
	$ezresek=array(1=>"ezer",2=>"millió");
	$rv="";
	$j;
	$ti;
	$mi=true;
	$sz = $s;
	if ($sz==0)
		$rv = ($f) ? "nulla" : "";
	else {
		if ( $sz > 99) {
			$mi = false;
			if (!$f)
				$rv = "-";
			$j= $sz/100;
			$rv.=$szazasok[$j];
			$sz %= 100;
		}
		if ( $sz > 9) {
			if (!$f && $mi)
				$rv = "-";
			$mi = false;
			$j= $sz/10;
			$ti = ($sz % 10) != 0;
			$rv.=$tizesek[$ti][$j];
			$sz %= 10;
		}
		if ( $sz != 0) {
			if (!$f && $mi)
				$rv = "-";
			$mi = false;
			$rv.=$egyesek[$sz];
		}
		$rv.=$ezresek[$g];
	}
	return $rv;
}

function writeNumber( $s ) {
	$i=0;
	$j=strlen($s) % 3;
	$k=(strlen($s)-1) / 3;
	$first=true;
	$st="";
	$rv="";
	if ($j==0)
		$j = 3;
	while($i<strlen($s)) {
		$st = substr($s,$i,min($i+$j,strlen($s) ));
		if ( $i==0 )
			$rv = writeNumberGroup($st,$first,$k);
		else
			$rv .= writeNumberGroup($st,$first,$k);
		$i += $j;
		$j = 3;
		$k--;
		$first = false;
	}
	return $rv;
}

function cutEndingZeros($in){
	for($i=0;round($in,$i)!=$in;$i++);
	return round($in,$i);
}

?>