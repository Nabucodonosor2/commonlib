<?php
/*
 * LIBRERIA PARA EL API DE LIBRE_DT
 * LICENCIA: GNU FREE DOCUMENTATION LICENSE 1.3
 * FECHA CREACIÓN:09/06/2016
 * ULTIMA MODIFICACIÓN: 09/06/2016
 * Nota:
 * para el ambiente oficial se cambia el:http://192.168.2.200/ por http://dte.integrasystem.cl/
*/
require_once(dirname(__FILE__)."/../auto_load.php");
/**HACER COMMIT OFICIAL****/
class dte {
	var $hash;
	
	function post_emitir_dte($objEnJson) { //DTE ENITIDO
		//$CURL REQUERIBLE PARA ENVIAR LA INFO CON EL METODO POST HACIA EL SERVIDOR DE LIBRE_DTE
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $objEnJson);
		//EN EL HEADER ES REQUERIDO QUE  SEA UN ARREGLO Y DE TIPO JSON Y DEBE LLEVAR EN BASE64_ENCODE EL USUARIO Y CONTRASEÑA DEL INGRESO AL SERVIDOR *ERROR400
		if($this->hash == '')
			$hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
		else
			$hash = $this->hash;

		$header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_URL, 'https://dte.integrasystem.cl/api/dte/documentos/emitir');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		
		//RESPUESTA DEL CURL
		$response = curl_exec($curl);
		
		//CERRAMOS EL PRIMER CURL
		curl_close($curl);
		
		//Imprimimos la respuesta.
		return $response;
	}
	
	function respuesta_emitir_dte($response) {//RESPUESTA_DTE
		//SEPARAMOS EL HEADER DEL BODY '{' 		   
		$header_body = explode("{", $response);

		//SI LA RESPUESTA ES UN 200 ENTONCES SEGUIMOS DE LO CONTRARIO RETORNA EL ERROR
		$Header_response = explode("200 OK", $header_body[0]);
		
		if ($Header_response[1] != '') {//REALIZAMOS LA SEPARACIÓN DE LA RESPUESTA AL EMITIR.
			$body_sp = explode(':', $header_body[1]);
			foreach ($body_sp as &$body_sp) {
				$body_sc = explode('"', $body_sp);
				foreach ($body_sc as &$body_sc) {
					$body_sca = explode(',', $body_sc);
					if($body_sca[0] <> ''){
						$devol = $devol.'|'.$body_sca[0]; 
					}
				}
			 }
			
			 $respuesta_dte = explode('|', $devol);
			 
			//REALIZO EL JSON PARA ENVIAR A LA FUNCION GENERA
			$resultado_genera[$respuesta_dte[1]] = $respuesta_dte[2]; //$resultado_genera['emisor'] = $rut_emisor
			$resultado_genera[$respuesta_dte[3]] = $respuesta_dte[4]; //$resultado_genera['receptor'] = $rut_receptor;
			$resultado_genera[$respuesta_dte[5]] = $respuesta_dte[6]; //$resultado_genera['dte'] = $cod_dte;
			$resultado_genera[$respuesta_dte[7]] = $respuesta_dte[8]; //$resultado_genera['codigo'] = "$str_codigo";
			
			$objEnJson_genera = json_encode($resultado_genera);
			
			return $objEnJson_genera;
		}else{
			Print($response);
			return;
		}
	}

	function post_genera_dte($ve_objEnJson){	//FUNCION GENERA_DTE
		$curl_genera = curl_init();
		curl_setopt($curl_genera, CURLOPT_POST, 1);
		curl_setopt($curl_genera, CURLOPT_POSTFIELDS, $ve_objEnJson);
		if($this->hash == '')
			$hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
		else
			$hash = $this->hash;	
		
		$header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
		curl_setopt($curl_genera, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl_genera, CURLOPT_URL, 'https://dte.integrasystem.cl/api/dte/documentos/generar?getXML=1');
		curl_setopt($curl_genera, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_genera, CURLOPT_HEADER, 1);
		$response_genera = curl_exec($curl_genera);
		//CERRAMOS EL PRIMER CURL
		curl_close($curl_genera);
		
		return $response_genera;
	}
	function respuesta_genera_dte($ve_respuesta_genera_dte) {//
		//SEPARAMOS EL HEADER DEL BODY '{' 		   
		$header_body = explode("{", $ve_respuesta_genera_dte);
		//SI LA RESPUESTA ES UN 200 ENTONCES SEGUIMOS DE LO CONTRARIO RETORNA EL ERROR
		$Header_response = explode("200 OK", $header_body[0]);
		if ($Header_response[1] != ''){
			$body_sp = explode(':', $header_body[1]);
			foreach ($body_sp as &$body_sp) {
				$body_sc = explode('"', $body_sp);
				foreach ($body_sc as &$body_sc) {
					$body_sca = explode(',', $body_sc);
					if($body_sca[0] <> ''){
						$devol = $devol.'|'.$body_sca[0]; 
					}
				}
			 }
			
			 $respuesta_genera_dte = explode('|', $devol);
			
			 return $respuesta_genera_dte;
			 
		}else{
			Print($response);
			return;
		}
	}
	function post_genera_pdf($ve_dte,$ve_folio,$ve_emisor){
				
		$curl_pdf = curl_init();
		if($this->hash == '')
			$hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
		else
			$hash = $this->hash;	
		
		$header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
		curl_setopt($curl_pdf, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl_pdf, CURLOPT_URL, 'https://dte.integrasystem.cl/api/dte/dte_emitidos/pdf/'.$ve_dte.'/'.$ve_folio.'/'.$ve_emisor.'?usarWebservice=true');
		curl_setopt($curl_pdf, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_pdf, CURLOPT_HEADER, 1);
		$response_pdf = curl_exec($curl_pdf);
		//CERRAMOS EL CURL
		curl_close($curl_pdf);
		
		$Header_response = explode("200 OK", $response_pdf);
		if ($Header_response[1] != ''){
			
			return $response_pdf;
			
		}else{
			return 'ERROR: '.$response_pdf;
		}	
	}
	function actualizar_estado($ve_dte,$ve_folio,$ve_emisor){
		
		$ch = curl_init();  
 		
		if($this->hash == '')
			$hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
		else
			$hash = $this->hash;	
			
		$header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch,CURLOPT_URL,'https://dte.integrasystem.cl/api/dte/dte_emitidos/actualizar_estado/'.$ve_dte.'/'.$ve_folio.'/'.$ve_emisor.'?usarWebservice=true');
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	    $output=curl_exec($ch);
	 
	    curl_close($ch);
	    return $output;
	    
	}
	function respuesta_actualizar_estado($ve_actualiza_estado) {
		$header_body = explode("{", $ve_actualiza_estado);
		$body_sp = explode(':', $header_body[1]);
		
		foreach ($body_sp as &$body_sp) {
			$body_sc = explode('"', $body_sp);
			foreach ($body_sc as &$body_sc) {
				$body_sca = explode(',', $body_sc);
				if($body_sca[0] <> ''){
					$devol = $devol.'|'.$body_sca[0]; 
				}
			}
		}
		$respuesta_ae = explode('|', $devol);
		return $respuesta_ae;
	}
	function enviar_sii($ve_folio,$ve_tipo_doc,$ve_emisor){
		
		$ch = curl_init();  
 		
		if($this->hash == '')
			$hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
		else
			$hash = $this->hash;	
			
		$header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch,CURLOPT_URL,'https://dte.integrasystem.cl/api/dte/dte_emitidos/enviar_sii/'.$ve_tipo_doc.'/'.$ve_folio.'/'.$ve_emisor.'');
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	    $output=curl_exec($ch);
	 
	    curl_close($ch);
	    return $output;
	}
	function eliminar_dte($ve_folio,$ve_tipo_doc,$ve_emisor){
		$ch = curl_init();  
 		
		if($this->hash == '')
			$hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
		else
			$hash = $this->hash;	
			
		$header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch,CURLOPT_URL,'https://dte.integrasystem.cl/api/dte/dte_emitidos/eliminar/'.$ve_tipo_doc.'/'.$ve_folio.'/'.$ve_emisor.'');
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	    $output=curl_exec($ch);
	 
	    curl_close($ch);
	    return $output;
	}
	function acuse_comercial($ve_folio,$ve_tipo_doc,$ve_emisor){
	    $ch = curl_init();
	    
	    if($this->hash == '')
	        $hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
	    else
            	$hash = $this->hash;
            
            $header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch,CURLOPT_URL,'https://dte.integrasystem.cl/api/dte/dte_emitidos/info/'.$ve_tipo_doc.'/'.$ve_folio.'/'.$ve_emisor.'');
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            
            $output=curl_exec($ch);
            
            curl_close($ch);
            return $output;
	}
	function listar_dte($objEnJson,$rutemisor) { //DTE ENITIDO
	    //$CURL REQUERIBLE PARA ENVIAR LA INFO CON EL METODO POST HACIA EL SERVIDOR DE LIBRE_DTE
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $objEnJson);
	    //EN EL HEADER ES REQUERIDO QUE  SEA UN ARREGLO Y DE TIPO JSON Y DEBE LLEVAR EN BASE64_ENCODE EL USUARIO Y CONTRASEÑA DEL INGRESO AL SERVIDOR *ERROR400
	    if($this->hash == '')
	        $hash = 'nLAwgOlKsPH5I3VCAO8BibDnl2pr5Q5j';
	        else
	            $hash = $this->hash;
	            
	            $header = Array("Content-Type: application/json","User-Agent: SowerPHP Network_Http_Rest","Authorization: Basic ".base64_encode($hash.':X'));
	            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	            curl_setopt($curl, CURLOPT_URL, 'https://dte.integrasystem.cl/api/dte/dte_emitidos/buscar/'.$rutemisor);
	            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	            curl_setopt($curl, CURLOPT_HEADER, 1);
	            curl_setopt($curl,CURLOPT_POST,true);
	            
	            //RESPUESTA DEL CURL
	            $response = curl_exec($curl);
	            
	            //CERRAMOS EL PRIMER CURL
	            curl_close($curl);
	            
	            //Imprimimos la respuesta.
	            return $response;
	}
}
?>
