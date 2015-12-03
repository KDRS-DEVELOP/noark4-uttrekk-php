<?php

abstract class Noark4Base {

	protected $XMLfilename = null;
	protected $uttrekksBase = null;
	protected $srcBase = null;
	protected $SRC_TABLE_NAME = null;
	protected $selectQuery = null;
	protected $logger = null;
	protected $infoIssued = false;
	protected $warningIssued = false;
	protected $errorIssued = false;

	public function __construct ($XMLfilename, $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
		$this->XMLfilename = $XMLfilename;
		$this->srcBase = $srcBase;
		$this->uttrekksBase = $uttrekksBase;
		$this->SRC_TABLE_NAME = $SRC_TABLE_NAME;
		$this->logger = $logger;
	}

	public function countRowsInTableBefore() {

		$query = "SELECT COUNT(*) AS COUNTROWS FROM " .  $this->SRC_TABLE_NAME;
		$result = $this->srcBase->executeQueryAndGetResult($query);
		//$result = $this->srcBase->getQueryResult ($query);
		$countRows = -1;

		if (isset($result)) {
			$countRows  =  $result['COUNTROWS'];
		}

		return $countRows;
	}

	public function countRowsInTableAfter() {
		$tableName = str_replace(".XML", "", $this->XMLfilename);
		$handle = $this->uttrekksBase->executeQueryFetchResultHandle("SELECT COUNT(*) AS COUNTROWS FROM " .  $tableName);
		$countRows = -1;

		if (isset($handle)) {
			$row = mysql_fetch_array($handle);
			if (isset($row))
				$countRows  =  $row['COUNTROWS'];
		
			$this->uttrekksBase->freeHandle($handle);
		}

		return $countRows;
	}

	public function getIssues () {
		$issues = "";
		if ($this->infoIssued == true) {
			$issues = "(I)";
		}
		if ($this->warningIssued == true) {
			$issues .= "(W)";
		}
		if ($this->errorIssued == true) {
			$issues .= "(E)";
		}
		return $issues; 
	}

	abstract function processTable ();
	abstract function writeToDestination($data);
	abstract function createXML($extractor);
}

?>