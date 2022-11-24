<?php

//require_once('auto_load.php');
require_once('interface.ReportData.php');

class MySQLRD implements IReportData {
	
	private $dbhandle;
	private $Result;
	private $RowData;
	private $Command;
	
	public function __construct($command){
		$this->Command = $command;
	}

	/**
	 * start data retriving again from the beginning
	 */
	public function reset(){
		$this->dbhandle = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);				
		$this->Result = $this->dbhandle->query($this->Command);
	}

	public function getNextRow() {
		return $this->RowData;
	}
	
	public function hasMoreRow() {
		return ($this->RowData = $this->dbhandle->get_row());
	}

}
?>