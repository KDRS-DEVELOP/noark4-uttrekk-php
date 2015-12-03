<?php

require_once 'models/AdminDel.php';
require_once 'models/Noark4Base.php';

class AdminDelDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ADMINDEL'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ORGENHET, NAVN, EIER, FULLKODE, TYPE, DATO, TILDATO FROM " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		//Create the first entry with no value See Page 85 of standard
		$adminDel0 = new AdminDel();
		$adminDel0->AI_ID = Constants::ADMININDEL_TOPNIVA ;
		$adminDel0->AI_ADMBET = 'Ikke angitt';
		$this->writeToDestination($adminDel0);
		
		$this->logger->log($this->SRC_TABLE_NAME, "Adding a (AI_ID, AI_ADMBET) = (0, Ikke angitt) ",  Constants::LOG_INFO);
		$this->infoIssued = true;
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				// There is a dummy entry in the table with $result['ORGENHET'] == -1  
				if ($result['ORGENHET'] >= 0) {

					$adminDel = new AdminDel();
					
					$adminDel->AI_ID = $result['ORGENHET'];
					$adminDel->AI_ADMBET = $result['NAVN'];
					
					// The very first entry has $result['ORGENHET'] == 1 and only the above should be used
					if ($result['ORGENHET'] > 0) { 
						$adminDel->AI_FORKDN = $result['FULLKODE'];
						$adminDel->AI_TYPE = $result['TYPE'];
						$adminDel->AI_IDFAR = $result['EIER'];			
						$adminDel->AI_ADMKORT = $result['FULLKODE'];
						$adminDel->AI_FRADATO = Utility::fixDateFormat($result['DATO']);
						$adminDel->AI_TILDATO = Utility::fixDateFormat($result['TILDATO']);
					}
					$this->writeToDestination($adminDel);
				}
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO ADMINDEL (AI_ID, AI_FORKDN, AI_ADMBET, AI_TYPE, AI_IDFAR, AI_ADMKORT, AI_FRADATO, AI_TILDATO) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->AI_ID . "', ";
		$sqlInsertStatement .= "'" . $data->AI_FORKDN . "', ";
		$sqlInsertStatement .= "'" . $data->AI_ADMBET . "', ";
		$sqlInsertStatement .= "'" . $data->AI_TYPE . "', ";
		$sqlInsertStatement .= "'" . $data->AI_IDFAR . "', ";
		$sqlInsertStatement .= "'" . $data->AI_ADMKORT . "', ";
		$sqlInsertStatement .= "'" . $data->AI_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->AI_TILDATO . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

		 

    }
	

	
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ADMINDEL";
    	$mapping = array ('idColumn' => 'ai_id', 
  				'rootTag' => 'ADMINDEL.TAB',	
			    		'rowTag' => 'ADMINDEL',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'AI.ID' => 'ai_id',
							'AI.FORKDN' => 'ai_forkdn',
							'AI.ADMBET' => 'ai_admbet',
							'AI.TYPE' => 'ai_type',
							'AI.FRADATO' => 'ai_fradato',
							'AI.TILDATO' => 'ai_tildato'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");

    }
 }