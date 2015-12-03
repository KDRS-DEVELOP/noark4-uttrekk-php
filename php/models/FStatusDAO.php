<?php

require_once 'models/FStatus.php';
require_once 'models/Noark4Base.php';

class FStatusDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('FSTATUS'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select FSSTATUS, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$fsStatus = new FStatus();
				$fsStatus->FS_STATUS = $result['FSSTATUS'];
				$fsStatus->FS_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($fsStatus);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO FSTATUS (FS_STATUS, FS_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->FS_STATUS . "', ";
		$sqlInsertStatement .= "'" . $data->FS_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }  
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM FSTATUS";
    	$mapping = array ('idColumn' => 'fs_status', 
  				'rootTag' => 'FSTATUS.TAB',	
			    		'rowTag' => 'FSTATUS',
  						'encoder' => 'utf8_decode',
							'elements' => array(
								'FS.STATUS' => 'fs_status',
								'FS.BETEGN' => 'fs_betegn'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			