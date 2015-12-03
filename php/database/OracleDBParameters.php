<?php

class OracleDBParameters extends DBParameters {

	var $oracle_db_sid;
	var $oracle_db_location;

	public function __construct ($db_host, $db_port, $db_name, $db_user, $db_pswd) {

		parent::__construct($db_host, $db_port, $db_name, $db_user, $db_pswd);
		$this->ora_db_sid = $ora_db_sid;
		$this->ora_db_location = $ora_db_location;
	}

}



?>
