<?php
require_once(dirname(__FILE__)."/../auto_load.php");

/* Crea un objeto tipo input text pero hidden.
 * Tanto en modo entrable como no entrable existe el objeto 
 * y por lo tanto se puede accesar desde java
 */
class edit_text_hidden extends edit_text {
	function edit_text_hidden($field) {
		parent::edit_text($field, 100, 100, 'hidden');
	}
	function draw_no_entrable($dato, $record) {
		return $this->draw_entrable($dato, $record);
	}
}
?>