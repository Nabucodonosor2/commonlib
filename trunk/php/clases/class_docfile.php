<?php 
class docfile {
/*
 * para obtener y grabar archivos en integradoc_file
 * 
 */    
    public $db_pg;
    
    function docfile() {
        # Servidor de base de datos
        /*
    	$dbhost ="190.96.2.186";
        #port
        $port=5432; 
        # Nombre de la base de datos
        $dbname="integra_docfile";
        # Usuario de base de datos
        $dbuser="integradmin";
        # Password de base de datos
        $dbpwd="ctrl2021";
    	*/
    	
    	
        $dbhost ="190.96.2.187";
		# Nombre de la base de datos
		$dbname="uct_docfile";
		# Usuario de base de datos
		$dbuser="integradmin";
		# Password de base de datos
		$dbpwd="ctrl2021";
		
		$port=54321;
		
        
        $this->db_pg = pg_connect("host=$dbhost port=$port user=$dbuser password=$dbpwd dbname=$dbname") or die(pg_last_error($this->db_pg));
    }
    function get_docfile($cod_docfile, $op='VER') {
        /* $cod_docfile: id del documento
         * $op: 'VER' para ver un documento, 'BAJAR' para bajar un documento
         */
        # Recupera el archivo en base al ID
        $sql = "select data_file
                ,f_type
                ,f_name
                ,f_size
        from docfile
        where cod_docfile=$cod_docfile";
        
        $result = pg_query($this->db_pg, $sql);
        
        # Si no existe, redirecciona a la página principal
        if(!$result || pg_num_rows($result)<1){
            Echo "Error, no existe cod_docfile= $cod_docfile<br>";
            print_r($result);
            exit();
        }
        
        # Recupera los atributos del archivo
        $row = pg_fetch_array($result,0);
        pg_free_result($result);
        
        # Hace el proceso inverso a pg_escape_bytea, para que el archivo esté en su estado original
        $file = pg_unescape_bytea($row['data_file']);
        pg_close($this->db_pg);
        
        # Envío de cabeceras
        header("Cache-control: private");
        header("Content-type: $row[f_type]");
        if($op=='BAJAR') {
            header("Content-Disposition: attachment; filename=\"$row[f_name]\"");
        }
        header("Content-length: $row[f_size]");
        header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        
        # Imprime el contenido del archivo
        print $file;
    }
    function put_docfile($cod_tabla_bd, $cod_origen, $f_name, $f_type, $f_size, $data_file) {
        /* $cod_tabla_bd : id tabla registrada en integra_sistema (ej representa a HELEN_UTEM.DECLARACION_JURADA) (solicitar a dba postgre)
         * $cod_origen: id de la tabla que apunta al docfile (ej DECLARACION_ARCHIVO.COD_DECLARACION_ARCHIVO)
         * $f_name : nombre del archivo
         * $f_type : tipo del archivo
         * $f_size : size del archivo
         * $data_file : buffer con la data del archivo (normalmente obtenerido con un fread)
         */
        //reemplaza las comillas simples
        $f_name = str_replace("'", "''", $f_name);

         # Escapa el contenido del archivo para ingresarlo como bytea
        $buffer=pg_escape_bytea($data_file);
        $sql = "INSERT INTO docfile
                (cod_tabla_bd
                ,cod_origen
                ,f_name
                ,f_size
                ,f_type
                ,data_file
                )
            VALUES
                ($cod_tabla_bd               --cod_tabla_bd = DECLARACION_ARCHIVO
                ,$cod_origen   --cod_PK
                ,'$f_name'             --f_name
                ,$f_size                    --f_size
                ,'$f_type'                  --f_type
                ,'$buffer'                  --data_file
                )
            returning cod_docfile";
            
        # Ejecuta la sentencia SQL
        $result_pg = pg_query($this->db_pg, $sql) or die(pg_last_error($this->db_pg));
        $row = pg_fetch_array($result_pg);
        $COD_DOCFILE = $row['cod_docfile'];
        pg_free_result($result_pg);

        pg_close($this->db_pg);
        return $COD_DOCFILE;
    }
    function descarga_docfile($cod_docfile) {
        /* $cod_docfile: id del documento
         * $op: 'VER' para ver un documento, 'BAJAR' para bajar un documento
         */
        # Recupera el archivo en base al ID

        $sql = "select data_file
                ,f_type
                ,f_name
                ,f_size
        from docfile
        where cod_docfile=$cod_docfile";
        
        $result = pg_query($this->db_pg, $sql);
        
        # Si no existe, redirecciona a la página principal
        if(!$result || pg_num_rows($result)<1){
            Echo "Error, no existe cod_docfile= $cod_docfile<br>";
            print_r($result);
            exit();
        }
        
        # Recupera los atributos del archivo
        $row = pg_fetch_array($result,0);
        pg_free_result($result);
        
        # Hace el proceso inverso a pg_escape_bytea, para que el archivo esté en su estado original
        $file = pg_unescape_bytea($row['data_file']);
        pg_close($this->db_pg);
        
        return $file;
    }
}