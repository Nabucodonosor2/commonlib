<?php

require_once('interface.ReportData.php');

class ReportData implements IReportData {
	
	private $ptr;
	private $Data;
	
	public function __construct($command){
		$this->Data = $command;
	}

	/**
	 * start data retriving again from the beginning
	 */
	public function reset(){
		$ptr=-1;
	}

	public function getNextRow() {
		return $this->Data[$this->ptr];
	}
	
	public function hasMoreRow() {
		$this->ptr++;
		return ($this->ptr < count($this->Data));
	}

}
?>