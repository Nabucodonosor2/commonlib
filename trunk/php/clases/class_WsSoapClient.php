<?php
require_once(dirname(__FILE__)."/../auto_load.php");
function objectToArray($d) {
	if (is_object($d)) {
		$d = get_object_vars($d);
	}
	 
	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}

class WsSoapClient extends base {
	
	static function Login_Ws($soapAction,$method,$params) {
		$client = new SoapClient (
			$soapAction."?wsdl", 
			array (
            	"trace" => 1,
                "location" => $soapAction."?wsdl",
                "uri" => $soapAction 
            )
        );
       $array = objectToArray($client->{$method}($params));
       return $array;
	}
	
	static function Envia_XmlDtes_Ws($soapAction,$method,$params) {
		$client = new SoapClient (
			$soapAction."?wsdl", 
			array (
            	"trace" => 1,
                "location" => $soapAction."?wsdl",
                "uri" => $soapAction 
            )
        );
       //return $client->{$method}($params);
       $array = objectToArray($client->{$method}($params));
       return $array; 
	}
	static function viewByRut_Ws($soapAction,$method,$params) {
		$client = new SoapClient (
			$soapAction."?wsdl", 
			array (
            	"trace" => 1,
                "location" => $soapAction."?wsdl",
                "uri" => $soapAction 
            )
        );
       //return $client->{$method}($params);
       $array = objectToArray($client->{$method}($params));
       return $array; 
	}
}
?>