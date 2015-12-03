<?php

class OracleDB extends Database {

	protected $queries = array();
	
	public function __construct  ($parameters, $logger) {
		parent::__construct ($parameters, $logger);
			    
		$this->db_conn = oci_connect($this->parameters->db_user, $this->parameters->db_pswd, 
										$this->parameters->oracle_db_location, "AL32UTF8");
                
        if (isset($this->parameters->oracle_db_location) == false) {
			
			$this->parameters->oracle_db_location = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)" . 
													"(HOST = " . $this->parameters->oracle_db_host . ")". 
													"(PORT = " . $this->parameters->oracle_db_port . "))" .
													"(CONNECT_DATA = (SID = " . $this->parameters->oracle_db_sid . ")))";
		}
                
		if (!$this->db_conn) {
			throw new Exception("Could not connect to Oracle database with user($this->ora_db_user) " . 
								"with password provided location($this->ora_db_location) E_USER_ERROR ("
								. E_USER_ERROR . ")");
       }						
    }

	function createAndExecuteQuery ($sqlQuery) {
			$oracleSQLQuery = oci_parse($this->db_conn, $sqlQuery);
			oci_execute($oracleSQLQuery);
			$this->queries[$sqlQuery] = $oracleSQLQuery;
	}


	// why am i making a copy of the query???? I probably don't need to!

	public function endQuery($sqlQuery) {
		if (!oci_free_statement($this->queries[$sqlQuery])) {
			echo "******** Unable to clean oci resource " . $this->queries[$sqlQuery]; 
		}
		unset ($this->queries[$sqlQuery]);
	}


	public function hasResult() {

	}
	
	public function nextResult() {
	
	}

	public function executeStatement($sqlStatement) {

	}

	public function close() {	
		return oci_close($this->$db_conn);
	}
}




?>
