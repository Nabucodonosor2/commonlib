<?php
class session  {
   public function __construct ()   {
	   self :: commence ();
   }

   public function commence ()
   {
    if ( !isset( $_SESSION ['ready'] ) )
     {
     	/* ejemplo para pruebas
     	ini_set("session.cookie_lifetime","10"); 
     	ini_set("session.gc_maxlifetime","10");
		*/ 

      session_start ();
      $_SESSION ['ready'] = TRUE;
     }
   }

   public function set ( $fld , $val )
   {
	   self :: commence ();
	   $_SESSION [ $fld]  = $val;
   }
   public function un_set ( $fld ) 
   {
	   self :: commence ();
	   unset( $_SESSION [$fld] );
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
	   return $_SESSION [$fld];
   }
   public function is_set ( $fld ) {
	   self :: commence ();
	   return isset( $_SESSION [$fld] );
   } 
   
   // Agregadas por VM
   public function un_set_all_modulo() {
		// Usada en mantenedor.php para borrar todos los obtjetos asociados a algun modulo, se borran todos wo_ y wi_
	   	self :: commence ();
		$indices = array_keys($_SESSION);
		for ($i=0; $i < count($indices); $i++) {
			if (substr($indices[$i], 0, 3)=='wo_' || substr($indices[$i], 0, 3)=='wi_')
				session::un_set($indices[$i]);
		}			
   }
}
?>