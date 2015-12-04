<?php

class Logger {

	protected $logFile = null;
	protected $filename = "logg.txt"; 
	protected $printInfoToSTDOUT;
 	protected $printWarningToSTDOUT;
	protected $printErrorToSTDOUT;

	public function __construct($logDir, $printInfoToSTDOUT, $printWarningToSTDOUT, $printErrorToSTDOUT) {
		
		$this->printInfoToSTDOUT = $printInfoToSTDOUT;
		$this->printWarningToSTDOUT = $printWarningToSTDOUT;
		$this->printErrorToSTDOUT = $printErrorToSTDOUT;

		$this->logFile = fopen($logDir .  DIRECTORY_SEPARATOR . $this->filename, "w");
		if (!$this->logFile) {
			throw new Exception("Could not open logfile " . $logDir .  DIRECTORY_SEPARATOR . $this->filename); 
		}
	}

	public function log ($table, $description, $type) {
		$str = "[" . date("", mktime()) . "(" . $type . ", " . $table . ")]" . $description . "\n";
		fwrite($this->logFile, $str);
		flush($this->logFile);

		if ($this->printInfoToSTDOUT == true) {
			if (strcmp($type, Constants::LOG_INFO) == 0) {
				echo $str;
			}
		}

		if ($this->printWarningToSTDOUT == true) {
			if (strcmp($type, Constants::LOG_WARNING) == 0) {
				echo $str;
			}
		}

		if ($this->printErrorToSTDOUT == true) {
			if (strcmp($type, Constants::LOG_ERROR) == 0) {
				echo $str;
			}
		}
	}

	public function __destruct() {
		
			$this->close();
	}

	public function close() {

		if (is_resource($this->logFile))
			fclose($this->logFile);
	}
}

?>