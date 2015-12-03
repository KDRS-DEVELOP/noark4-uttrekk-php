<?php

require_once 'models/ArStatus.php';
require_once 'models/Noark4Base.php';

class ArStatusDAO extends Noark4Base {
			
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ARSTATUS'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select STATUS, BESKRIVELSE, SPEFSAK, SPEFDOK, LUKKET from " . $SRC_TABLE_NAME . "";
	} 

	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$arstatus = new ArStatus();
				$arstatus->AS_STATUS = $result['STATUS'];
				$arstatus->AS_BETEGN = $result['BESKRIVELSE'];
				$arstatus->AS_SPEFSAK = $result['SPEFSAK'];
				$arstatus->AS_SPEFDOK = $result['SPEFDOK'];
				$arstatus->AS_LUKKET = $result['LUKKET'];
				$this->writeToDestination($arstatus);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO ARSTATUS (AS_STATUS, AS_BETEGN, AS_SPEFSAK, AS_SPEFDOK, AS_LUKKET) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->AS_STATUS . "', ";			
		$sqlInsertStatement .= "'" . $data->AS_BETEGN . "', ";					
		$sqlInsertStatement .= "'" . $data->AS_SPEFSAK . "', ";			
		$sqlInsertStatement .= "'" . $data->AS_SPEFDOK . "', ";					
		$sqlInsertStatement .= "'" . $data->AS_LUKKET . "'";			
	
		$sqlInsertStatement.= ");";
		
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Known duplicate value detected. Value is " . $data->AS_STATUS, Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
    	}

	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM ARSTATUS";
		$mapping = array ('idColumn' => 'as_status', 
					'rootTag' => 'ARSTATUS.TAB',	
						'rowTag' => 'ARSTATUS',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'AS.STATUS' => 'as_status',
									'AS.BETEGN' => 'as_betegn',
									'AS.SPEFSAK' => 'as_spefsak',
									'AS.SPEFDOK' => 'as_spefdok',
									'AS.LUKKET' => 'as_lukket'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }