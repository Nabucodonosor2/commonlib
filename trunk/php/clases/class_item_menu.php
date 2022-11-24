<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class item_menu extends base {
	public $name = '';
	public $cod_item_menu = '';
	public $visible = true;
	public $link = '';
	public $children = array();

	function item_menu($name, $cod_item_menu = '', $link = '', $children = array()) {
		$this->name = $name;
		$this->cod_item_menu = $cod_item_menu;
		$this->link = $link;
		$this->children = $children;
		$this->visible = true;
	}
	function set_visible($visible) {
		$this->visible = $visible;
	}
	function set_link($link) {
		$this->link = $link;
	}
}
?>