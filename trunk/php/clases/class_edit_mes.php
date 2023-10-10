<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class edit_mes extends drop_down_list {
	function edit_mes($field, $width_px=0) {
		$aValues = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
		for($i=0; $i<count($aValues); $i++)
			$aLabels[] = $this->nom_mes($aValues[$i]);
		parent::drop_down_list($field, $aValues, $aLabels, $width_px=0);
	}
}
?>