<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_nro_doc extends computed {
	function edit_nro_doc($field, $table) {
		parent::computed($field);		
	}
	function draw_no_entrable($dato, $record) {
		return $this->draw_entrable($dato, $record);
	}
	function draw_entrable($dato, $record) {
		$ctrl = static_text::draw_entrable($dato, $record);
		$ctrl .= $this->edit_hidden->draw_entrable($dato, $record);
		return $ctrl;
	}
}
?>