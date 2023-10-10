<?php 
class docfile_api {
/*
 * para obtener y grabar archivos en integradoc_file
 * 
 */    
    public $db_pg;
    
    function hextobin($hexstr) 
	{ 
	        $n = strlen($hexstr); 
	        $sbin="";   
	        $i=0; 
	        while($i<$n) 
	        {       
	            $a =substr($hexstr,$i,2);           
	            $c = pack("H*",$a); 
	            if ($i==0){$sbin=$c;} 
	            else {$sbin.=$c;} 
	            $i+=2; 
	        } 
	        return $sbin; 
	} 
    
    function get_docfile_api($url, $op='VER') {
    	$username = 'admin';
		$password = '1234';
        $opciones = array(
		  'http'=>array(
			'header'=>"Content-type: application/json\r\n" .
		              "Authorization: Basic ".base64_encode("$username:$password")."\r\n",
		    'method'=>"GET",
			'ignore_errors' => 1
		  )
		);
		$contexto = stream_context_create($opciones);
		// Abre el fichero usando las cabeceras HTTP establecidas arriba
		$json = file_get_contents($url, false, $contexto);
		$row = json_decode($json,true);
		if (function_exists('hex2bin')) {
			$file = hex2bin(substr($row[0]['data_file'], 2));  
		}
		else{
			$file = $this->hextobin(substr($row[0]['data_file'], 2)); 
		}

        header("Cache-control: private");
        header("Content-type:" .$row[0]['f_type']);
        if($op=='BAJAR') {
            header("Content-Disposition: attachment; filename=".$row[0]['f_name']);
        }
        header("Content-length: ".$row[0]['f_size']);
        header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");

        # Imprime el contenido del archivo
        print $file;
		
    }
    function put_docfile_api($url, $cod_tabla_bd, $cod_origen, $f_name, $f_type, $f_size, $data_file) {
        /* $cod_tabla_bd : id tabla registrada en integra_sistema (ej representa a HELEN_UTEM.DECLARACION_JURADA) (solicitar a dba postgre)
         * $cod_origen: id de la tabla que apunta al docfile (ej DECLARACION_ARCHIVO.COD_DECLARACION_ARCHIVO)
         * $f_name : nombre del archivo
         * $f_type : tipo del archivo
         * $f_size : size del archivo
         * $data_file : buffer con la data del archivo (normalmente obtenerido con un fread)
         */
        $username = 'admin';
		$password = '1234';
		//reemplaza las comillas simples
		$f_name = str_replace("'", "''", $f_name); 
		$buffer=base64_encode($data_file);

		$data = array("cod_tabla_bd" => $cod_tabla_bd, "cod_origen" => $cod_origen, "f_name" => $f_name, "f_type" => $f_type, "f_size" => $f_size, "data_file" => $buffer);
		$data_string = json_encode($data);

		$opciones = array(
		  'http'=>array(
			'header'=>"Content-type: application/json\r\n" .
		              "Authorization: Basic ".base64_encode("$username:$password")."\r\n",
		    'method'=>"POST",
			'ignore_errors' => 1, 
			'content' => $data_string
		  )
		);
		$contexto = stream_context_create($opciones);
		// Abre el fichero usando las cabeceras HTTP establecidas arriba
		$json = file_get_contents($url, false, $contexto);
		$row = json_decode($json,true);
		$COD_DOCFILE = 0;
		for($i=0; $i < count($row); $i++){
			$COD_DOCFILE= $row[0]['cod_docfile'];
		}
		
		$status_line = $http_response_header[0];
		preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
		$status = $match[1];
		
		if ($status !== "200") {
		    $COD_DOCFILE = 0;
		}
        
        return $COD_DOCFILE;
    }
}