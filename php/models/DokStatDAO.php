<?php

require_once 'models/DokStat.php';
require_once 'models/Noark4Base.php';
require_once 'utility/Constants.php';

class DokStatDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('DOKSTAT'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select DOKSTATUS, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$dokStat = new DokStat();
				$dokStat->DS_STATUS = $result['DOKSTATUS'];
				$dokStat->DS_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($dokStat);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO DOKSTAT (DS_STATUS, DS_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->DS_STATUS . "', ";
		$sqlInsertStatement .= "'" . $data->DS_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM DOKSTAT";
    	$mapping = array ('idColumn' => 'ds_betegn', 
				'rootTag' => 'DOKSTATUS.TAB',	
			    		'rowTag' => 'DOKSTATUS',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'DS.STATUS' => 'ds_status',
							'DS.BETEGN' => 'ds_betegn'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			