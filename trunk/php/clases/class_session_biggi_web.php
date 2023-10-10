<?php
/*
 Para tratar de evitar las caidas por abrir 2 sistemas en en el mismo PC (se pisan las sesiones)
Se modifca esta clase agregado un prefijo "WEB2" 
esta clase va en commonlib y reemplaza a la class_session.php
 */
class session  {
   public function __construct ()   {
	   self :: commence ();
   }

   public function commence ()
   {
    if ( !isset( $_SESSION ['WEB2_ready'] ) )
     {
      session_start ();
      $_SESSION ['WEB2_ready'] = TRUE;
     }
   }

   public function set ( $fld , $val )
   {
	   self :: commence ();
	   $_SESSION [ "WEB2_".$fld]  = $val;
   }
   public function un_set ( $fld ) 
   {
	   self :: commence ();
	   unset( $_SESSION ["WEB2_".$fld] );
   }
   public function destroy ()
   {
	   self :: commence ();
	   unset ( $_SESSION );
	   session_destroy ();
   }
   public function get ( $fld )
   {
	   self :: commence ();
	   return $_SESSION ["WEB2_".$fld];
   }
   public function is_set ( $fld ) {
	   self :: commence ();
	   return isset( $_SESSION ["WEB2_".$fld] );
   } 
   
   // Agregadas por VM
   public function un_set_all_modulo() {
		// Usada en mantenedor.php para borrar todos los obtjetos asociados a algun modulo, se borran todos wo_ y wi_
	   	self :: commence ();
		$indices = array_keys($_SESSION);
		for ($i=0; $i < count($indices); $i++) {
			if (substr($indices[$i], 5, 3)=='wo_' || substr($indices[$i], 5, 3)=='wi_')
	   			unset($indices[$i]);
		}			
   }
}
?>