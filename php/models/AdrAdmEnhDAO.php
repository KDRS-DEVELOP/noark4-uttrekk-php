<?php

require_once 'models/AdrAdmEnh.php';
require_once 'models/Noark4Base.php';

class AdrAdmEnhDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ADRADMENH'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		
		$this->selectQuery = "SELECT ADRID, ORGENHET FROM " .  $SRC_TABLE_NAME . "";
		$this->logger=$logger;
	} 

	public function countRowsInTableAfter() {
		$tableName = 'ADRADMENH'; 
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




	function processTable () {

		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

			$adrAdmEnh= new AdrAdmEnh();
			$adrAdmEnh->AA_ADRID = $result['ADRID'];
			$adrAdmEnh->AA_ADMID = $result['ORGENHET'];
			$this->writeToDestination($adrAdmEnh);
		}

		$this->srcBase->endQuery($this->selectQuery);
	}


  	function writeToDestination($data) {

		$sqlInsertStatement = "INSERT INTO ADRADMENH (AA_ADRID, AA_ADMID) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->AA_ADRID. "',";			
		$sqlInsertStatement .= "'" . $data->AA_ADMID . "'";
		
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ADRADMENH";
    	$mapping = array ('idColumn' => 'AA_ADRID', 
  				'rootTag' => 'ADRADMENH.TAB',	
			    		'rowTag' => 'ADRADMENH',
					'fileName' => 'ADRADMEN',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'AA.ADRID' => 'aa_adrid',
							'AA.ADMID' => 'aa_admid'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");	
    }
 }