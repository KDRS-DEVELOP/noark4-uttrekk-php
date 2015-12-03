<?php

require_once "DBParameters.php";

class MySQLDBParameters extends DBParameters {

	public function __construct ($db_host, $db_port, $db_name, $db_user, $db_pswd) {
		parent::__construct($db_host, $db_port, $db_name, $db_user, $db_pswd);
	}
}



?>
