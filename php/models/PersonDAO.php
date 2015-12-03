<?php
 
require_once 'models/Person.php';
require_once 'utility/Utility.php';
require_once 'utility/Constants.php';
require_once 'models/Noark4Base.php';

class PersonDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('PERSON'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select PEID, INITIALER, DATO, TILDATO from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		
		// Added 0 user to help process TGGRP where OPPRAV is set to null
		$person0 = new Person();
		$person0->PE_ID = Constants::INGENBRUKER_ID;
		$person0->PE_BRUKERID = 'ikke angitt';
		$this->writeToDestination($person0);

		/*
			COMMENTED THIS OUT BUT MIGHT NEED IT. ADDING USER AS 1 INSTEAD, HARDCODED ELSEWHERE!!
			// I want to distingush between a NO USER and UTTREKKSBRUKER that actually has created all the PDF/A files
			// When creating DOKVERS we need to identify who created this version and we identify UTTREKKSBRUKER as that person 
			$sqlMaxPersonID = "select MAX(PEID) TYPE FROM PERSONER"; 
			$result = $this->srcBase->getQueryResult ($sqlMaxPersonID);
			if ($result == null) {
				echo "Unable to get an answer for  select MAX(PEID) TYPE FROM PERSONER \n";
			}
	
			$maxPEID = $result['TYPE'];
			$maxPEID = $maxPEID + 100; 
			echo "Setting UTTREKSSBRUKER PEID to " . $maxPEID . "\n";
			$this->srcBase->endQuery($sqlMaxPersonID);
	
			$person0 = new Person();
			$person0->PE_ID = $maxPEID;
		*/
		$person0 = new Person();
		$person0->PE_ID = Constants::UTTREKSBRUKER_ID;
		$person0->PE_BRUKERID = 'UTTREKSBRUKER';
		$this->writeToDestination($person0);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$person = new Person();
				$person->PE_ID = $result['PEID'];
				
				$person->PE_BRUKERID = $result['INITIALER'];
				$person->PE_FRADATO = Utility::fixDateFormat($result['DATO']);
				$person->PE_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				
				$this->writeToDestination($person);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO PERSON (PE_ID, PE_BRUKERID, PE_FRADATO, PE_TILDATO) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->PE_ID . "', ";						
		$sqlInsertStatement .= "'" . $data->PE_BRUKERID . "', ";
		$sqlInsertStatement .= "'" . $data->PE_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->PE_TILDATO . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
     	    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM PERSON";
    	$mapping = array ('idColumn' => 'pe_id', 
  				'rootTag' => 'PERSON.TAB',	
			    		'rowTag' => 'PERSON',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'PE.ID' => 'pe_id',
    							'PE.BRUKERID' => 'pe_brukerid',
							'PE.FRADATO' => 'pe_fradato',
							'PE.TILDATO' => 'pe_tildato'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    } 
 }