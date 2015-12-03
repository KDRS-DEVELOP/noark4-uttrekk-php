<?php

require_once 'models/UtvMote.php';
require_once 'utility/Utility.php';
require_once 'models/Noark4Base.php';

class UtvMoteDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVMOTE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ID, MOTENR, UTVID, LUKKET, MOTEDATO, MOTETID, FRIST, SAKSKART, PROTOKOLL FROM " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$utvMote = new UtvMote();
				$utvMote->MO_ID = $result['ID'];
				$utvMote->MO_NR = $result['MOTENR'];
				$utvMote->MO_UTVID = $result['UTVID'];

				if (strcmp($result['LUKKET'], 'Å') == 0) {
					$utvMote->MO_LUKKET = '1';
				}
				else if (strcmp($result['LUKKET'], 'L') == 0) {
					$utvMote->MO_LUKKET = '0';
				}
				else if (strcmp($result['LUKKET'], 'S') == 0) {
					$this->logger->log($this->XMLfilename, "MO.LUKKET has unknown value (S), assuming  S == 1 for " . $utvMote->MO_ID, Constants::LOG_WARNING);
					$this->warningIssued = true;
					$utvMote->MO_LUKKET = '1';
				}
				else {
					$this->logger->log($this->XMLfilename, "MO.LUKKET has unknown value (" . $result['LUKKET'] . "), setting to 1 for " . $utvMote->MO_ID, Constants::LOG_WARNING);
					$this->warningIssued = true;
					$utvMote->MO_LUKKET = '1';
				}
				
				$utvMote->MO_DATO = Utility::fixDateFormat($result['MOTEDATO']);
				$utvMote->MO_START = Utility::fixTimeFormat($result['MOTETID']);
				$utvMote->MO_FRIST= $result['FRIST'];
				if (is_null($result['SAKSKART']) == true) {
					$utvMote->MO_SAKSART = '0';
				}
				else {
					$utvMote->MO_SAKSART = $result['SAKSKART'];
				}

				if (is_null($result['PROTOKOLL']) == true) {
					$utvMote->MO_PROTOKOLL = '0';
				}
				else {
					$utvMote->MO_PROTOKOLL = $result['PROTOKOLL'];
				}
					
				
				$this->writeToDestination($utvMote);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO  UTVMOTE (MO_ID, MO_NR, MO_UTVID, MO_LUKKET, MO_DATO, MO_START, MO_FRIST, MO_SAKSKART, MO_PROTOKOLL) VALUES (";
		$sqlInsertStatement .= "'" . $data->MO_ID . "', ";						
		$sqlInsertStatement .= "'" . $data->MO_NR . "', ";
		$sqlInsertStatement .= "'" . $data->MO_UTVID . "', ";
		$sqlInsertStatement .= "'" . $data->MO_LUKKET . "', ";
		$sqlInsertStatement .= "'" . $data->MO_DATO . "', ";
		$sqlInsertStatement .= "'" . $data->MO_START . "', ";
		$sqlInsertStatement .= "'" . $data->MO_FRIST . "', ";
		$sqlInsertStatement .= "'" . $data->MO_SAKSKART . "', ";
		$sqlInsertStatement .= "'" . $data->MO_PROTOKOLL . "'";
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }
 
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM UTVMOTE";
    	$mapping = array ('idColumn' => 'MO.ID', 
  				'rootTag' => 'UTVMOTE.TAB',	
			    		'rowTag' => 'UTVMOTE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'MO.ID;' => 'mo_id',
							'MO.NR' => 'mo_nr',
							'MO.UTVID' => 'mo_utvid',
							'MO.LUKKET' => 'mo_lukket',
							'MO.DATO' => 'mo_dato',
							'MO.START' => 'mo_start',
							'MO.FRIST' => 'mo_frist',
							'MO.SAKSKART' => 'mo_sakskart',
							'MO.PROTOKOLL' => 'mo_protokoll'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	
    }
}