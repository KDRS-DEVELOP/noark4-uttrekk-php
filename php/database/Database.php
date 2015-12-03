<?php

class Database {

	protected $parameters;
	protected $db_conn;
	public $logger;

	public function __construct  ($parameters, $logger) {
		$this->parameters = $parameters;
		$this->logger = $logger;
    } 

	public function close() {
	
	}

	public function __destruct () {
		$this->close();
	}

	// Save me having to clean all my other code
	// oracle calls use this syntax, mysql use executeStatement
	// should fix it but time is an issue right now
	public function createAndExecuteQuery($sqlStatement) {

	}	
	
	public function executeStatement($sqlStatement) {

	}
	
	public function endQuery($sqlQuery) {
		
	}
	
}




?>

