<?php

require_once 'models/Tgmedlem.php';
require_once 'utility/Utility.php';
require_once 'utility/Constants.php';
require_once 'models/Noark4Base.php';

class TgmedlemDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
		parent::__construct (Constants::getXMLFilename('TGMEDLEM'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select PEID, GRUPPEID, KLAVID from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$tgMedlem = new Tgmedlem();
				$tgMedlem->PG_PEID = $result['PEID'];
				$tgMedlem->PG_GRUPPEID = $result['GRUPPEID'];

				if (is_null($result['KLAVID'])) {
					$tgMedlem->PG_INNMAV =  Constants::INGENBRUKER_ID;
					$this->logger->log($this->XMLfilename, "PG_INNMAV is null for (PG_PEID, PG_GRUPPEID) (" . $tgMedlem->PG_PEID . ", ". $tgMedlem->PG_GRUPPEID . ") setting it to unkown user " . Constants::INGENBRUKER_ID, Constants::LOG_WARNING);
				}
				else { 
					$tgMedlem->PG_INNMAV = $result['KLAVID'];
				}

				$this->writeToDestination($tgMedlem);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}

	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO TGMEDLEM (PG_PEID, PG_GRUPPEID, PG_INNMAV) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->PG_PEID . "', ";
		$sqlInsertStatement .= "'" . $data->PG_GRUPPEID  . "', ";
		$sqlInsertStatement .= "'" . $data->PG_INNMAV . "' ";
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }  	
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM TGMEDLEM";
    	$mapping = array ('idColumn' => 'pg_peid', 
  				'rootTag' => 'TGMEDLEM.TAB',	
			    		'rowTag' => 'TGMEDLEM',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'PG.PEID' => 'pg_peid',
							'PG.GRUPPEID' => 'pg_gruppeid',
							'PG.INNMAV' => 'pg_innmav'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			