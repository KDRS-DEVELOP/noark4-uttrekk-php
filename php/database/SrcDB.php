<?php

class SrcBase {

	var $ora_db_host;
	var $ora_db_port;
	var $ora_db_name;
	var $ora_db_user;
	var $ora_db_pswd;
	var $ora_db_sid;
	var $ora_db_location;
	var $dbConnection;
	var $queries;
	
	function SrcBase ($ora_db_host, $ora_db_port, 
								$ora_db_name, $ora_db_user, 
									$ora_db_pswd, $ora_db_sid) {
										
		$this->ora_db_host = $ora_db_host;
		$this->ora_db_port = $ora_db_port;
		$this->ora_db_name = $ora_db_name;
		$this->ora_db_user = $ora_db_user;
		$this->ora_db_pswd = $ora_db_pswd;
		$this->ora_db_sid = $ora_db_sid;
		
		$ora_db_location = "(DESCRIPTION =
			(ADDRESS = (PROTOCOL = TCP)(HOST = $ora_db_host)(PORT = $ora_db_port))
				(CONNECT_DATA = (SID = $ora_db_sid)))";
		
		$this->dbConnection = oci_connect("$ora_db_user", "$ora_db_pswd", $ora_db_location, "AL32UTF8");
		
		if (!$this->dbConnection) {
			throw new Exception("Could not connect to ESA database with user($this->ora_db_user) passwd($this->ora_db_pswd) location($this->ora_db_location) E_USER_ERROR (" . E_USER_ERROR . ")");
		}
	}
	
	function close() {
		return oci_close($this->dbConnection);	
	}	
	
	
	function addEntry ($rootFolder, $subdir, $fileName, $fileExtension, $fileOriginalSize, $hashValue) {
      $sqlStatement = "INSERT INTO ConvertProcessInfo (rootFolder, subdir, fileName, fileExtension, fileOriginalSize, md5HashValue) VALUES ('$rootFolder', '$subdir', '$fileName', '$fileExtension', '$fileOriginalSize', '$hashValue')";
       $this->executeStatement ($sqlStatement);
    }
    
    function addEntryConverted ($rootFolder, $subdir, $fileName, $fileExtension, $fileOriginalSize, $fileConvertedExtension, $fileConvertedSize, $hashValue, $fileConvertedHashValue) {
      $sqlStatement = "INSERT INTO ConvertProcessInfo (rootFolder, subdir, fileName, fileExtension, fileOriginalSize, fileConvertedExtension, fileConvertedSize, md5HashValue, fileConvertedHashValue) VALUES ('$rootFolder', '$subdir', '$fileName', '$fileExtension', '$fileOriginalSize', '$fileConvertedExtension', '$fileConvertedSize', '$hashValue', '$fileConvertedHashValue')";
      $this->executeStatement  ($sqlStatement);
    }
	
	         
	function executeQueryAndGetResult($sqlQuery) {
		// Remember the SQL Query should not contain a ";" at the end for oracle
		$oracleSQLQuery = oci_parse($this->dbConnection, $sqlQuery);
		oci_execute($oracleSQLQuery);
		$result = oci_fetch_array($oracleSQLQuery, OCI_ASSOC+OCI_RETURN_NULLS);;
		 
		return $result; 
	}

	function endQuery($sqlQuery) {
		// TODO : Check this return value
		$OK = oci_free_statement($this->queries[$sqlQuery]);
		if ($OK != true)
			echo "******** Unable to clean oci resource " . $this->queries[$sqlQuery]; 
		unset ($this->queries[$sqlQuery]);
	}
	
	function createAndExecuteQuery ($sqlQuery) {
	//	echo "$sqlQuery \n";		
		$oracleSQLQuery = oci_parse($this->dbConnection, $sqlQuery);
		oci_execute($oracleSQLQuery);
		$this->queries[$sqlQuery] = $oracleSQLQuery;
	}
	
	//put in the cleanup operation before we do anything else!!!
	
	function getQueryResult ($sqlQuery) {

		//echo "---- $sqlQuery \n";				
		return oci_fetch_array($this->queries[$sqlQuery], OCI_ASSOC+OCI_RETURN_NULLS);
	}
}

?>