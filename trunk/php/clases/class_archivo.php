<?php
require_once(dirname(__FILE__)."/../auto_load.php");

/**
 * Clase para subir y borrar archivos
 * @uses Instanciar la clase con los datos de la variable $_FILE más los adicionales
 *       no incluidos en esta variable global.
 * @param String    $archivo            Archivo a manipular
 * @param String    $directorio         Directorio de destino del archivo 
 * @param String    $tipoArchivo        Extensión que identifica el nombre del archivo
 * @param Array     $tipoPermitido     	Arreglo con las extensiones permitidas
 * @param int       $tamanoArchivo      Tamaño del archivo (en bytes)
 * @param String    $tmp                Directorio temporal de localización del archivo
 * @param String    $nombre             Nombre del archivo a manipular
 * @param int       $tamanoMaximo       Máximo tamaño aceptado 
 *
 * @author Jorge Andrade M.
 */
class archivo extends base {	
	var $archivo;
	var $directorio;	
	var $tipoArchivo;
	var $tipoPermitido;
	var $tamanoArchivo;
	var $tmp;
	var $nombre;
	var $tamanoMaximo;	
	
	/**
	 * Constructor de la clase Archivo
	 *
	 * @param string $archivo
	 * @param string $dir	 
	 * @param array $extPermitida
	 * @param int $tamano
	 * @param string $tmp
	 * @param string $nombre
	 * @param int $tamPermitido	 
	 */
	public function archivo($archivo,$dir,$extPermitida=array(),$tamano,$tmp,$nombre='',$tamPermitido=''){
		$this->archivo			= $archivo;
		$this->directorio		= $dir;		
		$this->tipoArchivo		= $this->getTipoArchivo($archivo);
		$this->tipoPermitido	= $extPermitida;
		$this->tamanoArchivo	= $tamano;		
		$this->nombre			= empty($nombre) ? str_replace(".".$this->tipoArchivo,"",$archivo) : $nombre;		
		$this->tamanoMaximo		= empty($tamPermitido) ? ini_get('upload_max_filesize')*1048576 : $tamPermitido*1048576;
		$this->tmp				= $tmp;
	}
	
	/**
	 * Devuelve la extensión de un archivo
	 * @param String $archivo Cadena con el nombre original del archivo
	 * @return String $extension
     * @author Jorge Andrade M.
	 */
	static function getTipoArchivo($archivo){
		if($archivo!=''){
			$extension=end(explode('.',$archivo));
			return $extension;
		}
	}
	/**
	 * Revisa si el tipo del archivo está dentro de lo permitido
	 * @return boolean Si cumple o no con lo establecido
     * @author Jorge Andrade M.
	 */
	function checkType(){
		if(in_array($this->tipoArchivo,$this->tipoPermitido)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * Revisa si el archivo es del tamaño permitido
	 * @return boolean Si cumple o no con lo establecido
     * @author Jorge Andrade M.
	 */
	function checkSize(){
		if($this->tamanoArchivo > $this->tamanoMaximo){
			return false;
		}else{
			return true;
		}
	}	
	/**
	 * Sube los archivos, revisa si no sobrepasa el tamaño máximo permitido,
	 *  si está dentro de los tipos aceptados y si no exite
	 *
	 * @return boolean indicando el resultado del proceso
	 */
	function upLoadFile(){		
		if($this->checkSize()==false){
			$this->alert("El tamano del archivo sobrepasa el permitido que es de ".round(($this->tamanoMaximo/1048576),2)."MB");
			return false;
		}
		if($this->checkType()==false){
			$this->alert("El archivo no corresponde a un formato permitido. Los permitidos son: ".(implode(",",$this->tipoPermitido)));
			return false;
		}
		/*
		if(file_exists($this->directorio.$this->archivo)){
			$this->alert("El archivo ya existe");
			return false;		
		}
		*/
		move_uploaded_file($this->tmp,$this->directorio.$this->nombre.".".$this->tipoArchivo);
		return true;
	}
	/**
	 * Borra el archivo del servidor
	 *
	 * @return boolean
	 */
	function delFile(){
		if(file_exists($this->directorio.$this->archivo)){
			unlink($this->directorio.$this->archivo);
			return true;
		}else{
			return false;
		}		
	}
	/*
	* Renombra el archivo del servidor
	*
	* @return boolean
	*/
	function renFile(){
		$fecha = getdate();
		$nom_archivo = $fecha['mday'].'-'.$fecha['mon'].'-'.$fecha['year'].' '.$fecha["hours"].$fecha["minutes"].'.txt';
		if(file_exists($this->directorio.$this->archivo)){
			rename($this->directorio.$this->archivo,$this->directorio.$nom_archivo);
			return true;
		}else{
			return false;
		}		
	}
}
?>