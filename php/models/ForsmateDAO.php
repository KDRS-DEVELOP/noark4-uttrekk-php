<?php

require_once 'models/Forsmate.php';
require_once 'models/Noark4Base.php';

class ForsMateDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('FORSMATE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select SENDTSOM, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {

		$forsMate = new ForsMate();
		$forsMate->FM_KODE = 'I';
		$forsMate->FM_BETEGN = 'Ikke angitt';
		$this->writeToDestination($forsMate);
		$this->logger->log($this->XMLfilename, "Adding Key/Value pair (I - Ikke angitt) for AVSMOT with null values under SENDTSOM" , Constants::LOG_INFO);

		$forsMate->FM_KODE = 'B';
		$forsMate->FM_BETEGN = 'Med bud';
		$this->writeToDestination($forsMate);
		$this->logger->log($this->XMLfilename, "Adding Key/Value pair (B - Med bud). N4 specifies it and it could be in use" , Constants::LOG_INFO);

		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$forsMate = new ForsMate();
				$forsMate->FM_KODE = $result['SENDTSOM'];
				$forsMate->FM_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($forsMate);
		}

		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		
		
		$sqlInsertStatement = "INSERT INTO FORSMATE (FM_KODE, FM_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->FM_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->FM_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		
		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table i	s know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Known duplicate value detected. Value is " . $data->FM_KODE, Constants::LOG_WARNING);
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail = true;
		
		

    }  
	
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM FORSMATE";
    	$mapping = array ('idColumn' => 'fm_kode', 
				'rootTag' => 'FORSMAATE.TAB',	
			    		'rowTag' => 'FORSMAATE',
  						'encoder' => 'utf8_decode',
	  						'elements' => array(
								'FM.KODE' => 'fm_kode',
								'FM.BETEGN' => 'fm_betegn'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
