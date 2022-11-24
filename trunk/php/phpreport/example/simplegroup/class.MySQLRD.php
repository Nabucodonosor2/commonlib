<?php

require_once('../../interface.ReportData.php');
require_once('../../class.Database.php');

class MySQLRD implements IReportData {
	
	private $dbhandle;
	private $Result;
	private $RowData;
	private $Command;
	
	public function __construct($command){
		$this->Command = $command;
		//$this->reset();
	}

	/**
	 * start data retriving again from the beginning
	 */
	public function reset(){
		$this->dbhandle = Database::instance();
		$this->Result = $this->dbhandle->select($this->Command);
	}

	public function getNextRow() {
		return $this->RowData;
	}
	
	public function hasMoreRow() {
		return ($this->RowData = $this->Result->fetchRow());
	}

}
?>