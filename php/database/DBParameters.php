<?php

class DBParameters {
	
	public $db_host;
	public $db_port;
	public $db_name;
	public $db_user;
	public $db_pswd;
	
	public function DBParameters($db_host, $db_port, $db_name, $db_user, $db_pswd) {

		$this->db_host = $db_host;
		$this->db_port = $db_port;
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_pswd = $db_pswd;
		
	}

	public function getHost() {
		return $this->db_host;
	}

	public function setHost($db_host) {
		$this->db_host = $db_host;
	}

	public function getPort() {
		return $this->db_port;
	}

	public function setPort($db_port) {
		$this->db_Port = $db_port;
	}


}



?>
