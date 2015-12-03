<?php

class UtrekkMySQLBase {

	var $uttrekk_db_host;
 	var $uttrekk_db_user;
	var $uttrekk_db_pswd; 
	var $uttrekk_db_database;
	var $dbConnection;
	var $printErrorIfErrorOccurs = true;
	var $printErrorIfFKFail = true;
	var $printErrorIfDuplicateFail = true;
	
	function __construct ($uttrekk_db_host, $uttrekk_db_user, 
								$uttrekk_db_pswd, $uttrekk_db_database)
	{
		$this->uttrekk_db_host = $uttrekk_db_host;
		$this->uttrekk_db_user = $uttrekk_db_user;
		$this->uttrekk_db_pswd = $uttrekk_db_pswd;
		$this->uttrekk_db_database = $uttrekk_db_database;


		$this->dbConnection = mysql_connect($uttrekk_db_host, $uttrekk_db_user, $uttrekk_db_pswd);

		if (!$this->dbConnection) {
			throw new Exception("Could not connect to Uttrekks database with host ($uttrekk_db_host), user($uttrekk_db_user) passwd($uttrekk_db_pswd) mysql error (" . mysql_error() . ")");
		}
		if (mysql_select_db($uttrekk_db_database) == false) {
			echo "Databasen " . $this->uttrekk_db_database . " finnes, må opprettes før du kan fortsette\n " ;
			 
		} 
	}
	
	function close() {
		return mysql_close($this->dbConnection);
	}
	
	function setDefaultDatabase() {
		if (mysql_select_db($this->uttrekk_db_database) == false) {
			 throw new Exception("Feil med kobling til database " . $uttrekk_db_database . " mysql error " . mysql_error() . ")");
		} 
	}

	function executeQuery ($sqlQuery) {
		$result = mysql_query($sqlQuery);
		if (!$result) {
			print ("mysql error " . mysql_error() . " " . $sqlQuery .  "\n");
			return null;

		}
		else
			return mysql_fetch_assoc($result);
	}


	function executeQueryFetchResultHandle ($sqlQuery) {
		$result = mysql_query($sqlQuery);
		if (!$result) {
			print ("mysql error " . mysql_error() . " " . $sqlQuery .  "\n");
			return null;
		}
		else
			return $result;
	}

	function freeHandle($handle) {
		return mysql_free_result($handle);
	}	
	
	function executeQueryNoProcess ($sqlQuery) {
		return  mysql_query($sqlQuery);
	}


	// The calling function has to handle any errors		

	function executeStatement ($sqlStatement) {
		$result = mysql_query($sqlStatement);
		if (!$result) {
			if ($this->printErrorIfErrorOccurs == true) {

				if (mysql_errno() == Constants::MY_SQL_DUPLICATE && $this->printErrorIfDuplicateFail == true) {
					print ("Duplicate PK on entry " . mysql_error() . " " . $sqlStatement .  "\n");
				} 
				else if (mysql_errno() == Constants::MY_SQL_DUPLICATE && $this->printErrorIfDuplicateFail == false) {
					//do nothing
				}else if (mysql_errno() == Constants::MY_SQL_MISSING_FK_VALUE && $this->printErrorIfFKFail == true) {
					print ("Cannot update FK relationship on entry " . mysql_error() . " " . $sqlStatement .  "\n");
				}
				else if (mysql_errno() == Constants::MY_SQL_MISSING_FK_VALUE && $this->printErrorIfFKFail == false) {
					// do nothing
				}
				else
					print ("mysql error " . mysql_error() . " " . $sqlStatement .  "\n");
			}
		    return false;	
		}
		else
		     return true;		

	}

	
	
	function getHost() {
		return  $this->uttrekk_db_host;	
	}
	
	function getUser() {
		return  $this->uttrekk_db_user;
	}
	function getPswd() {
		return  $this->uttrekk_db_pswd;
	}
	
	function getDatabaseName() {
		return  $this->uttrekk_db_database;
	}
}