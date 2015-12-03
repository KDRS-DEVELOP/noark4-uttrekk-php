<?php

require_once 'models/AdrPerson.php';
require_once 'models/Noark4Base.php';

class AdrPersonDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ADRPERS'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select PEID, ADRID from " . $SRC_TABLE_NAME . "";
	} 

	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$adrPerson = new AdrPerson();
				$adrPerson->OP_ORDNPRI = $result['PEID'];
				$adrPerson->OP_BETEGN = $result['ADRID'];

				$this->writeToDestination($adrPerson);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO ADRPERS (PA_PEID, PA_ADRID) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->PA_PEID . "', ";		
		$sqlInsertStatement .= "'" . $data->PA_ADRID . "'";
	
		$sqlInsertStatement.= ");";
		
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Duplicate value detected. Value is " . $data->OP_ORDNPRI, Constants::LOG_WARNING);
			}
		}

    }
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ADRPERS";
    	$mapping = array ('idColumn' => 'pa_peid', 
  				'rootTag' => 'ADEPERS.TAB',	
			    		'rowTag' => 'ADRPERS',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'PA.PEID' => 'pa_peid',
							'PA.ADRID' => 'pa_adrid'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
}