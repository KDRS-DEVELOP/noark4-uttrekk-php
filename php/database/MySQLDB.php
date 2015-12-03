<?php

require_once('Database.php');

class MySQLDB extends Database {

	protected $num_rows = 0;
	protected $current_row = 0;
	protected $current_result = null;
	
	public function __construct  ($parameters, $logger) {
		parent::__construct ($parameters, $logger);

		$this->db_conn = mysql_connect($parameters->db_host, 
				$parameters->db_user, 
				$parameters->db_pswd);
		if (!$this->db_conn) {
				throw new Exception("Error instaniating MySQL connection to host(" . 
				$this->parameters->db_host .") user (" . $this->parameters->db_user . 
				") mysql error (" . mysql_error() . ")");
		}
		if (mysql_select_db($parameters->db_name) == false) {
				throw new Exception("Error Database not available . (" . $parameters->db_name . ")");
		} 
   }

	public function hasResult() {		
		if ($this->current_row < $this->num_rows)
			return true;
		else
			return false;
	}
	
	public function nextResult() {
		$this->current_row++; 
		return mysql_fetch_assoc($this->current_result);
	}

	public function executeStatement($sqlStatement) {	
		
		if (isset($this->current_result)) {
			mysql_free_result($this->current_result);		
		}

		$this->current_result = mysql_query($sqlStatement);
		
		if (!$this->current_result) {
			 echo "Could not successfully run query ($sqlStatement) from DB: " . mysql_error();
		}
		$this->current_row = 0;
		$this->num_rows = mysql_num_rows($this->current_result);				
	}

	public function endQuery() {		
		mysql_free_result($this->current_result);		
		$this->num_rows = 0;
		$this->current_row = 0;
	}
	public function getNumRows() {
		return $this->num_rows;
	}
	
	public function close() {
	
	}
}




?>

