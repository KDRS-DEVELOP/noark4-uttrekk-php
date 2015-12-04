<?php

require_once 'models/UtvMote.php';
require_once 'utility/Utility.php';
require_once 'models/Noark4Base.php';
require_once 'models/UtvMoteDokDAO.php';

class UtvMoteDAO extends Noark4Base {
	
	protected $utvMoteDokDAO;
	 
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVMOTE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ID, MOTENR, UTVID, LUKKET, MOTEDATO, MOTETID, FRIST, SAKSKART, PROTOKOLL, JOURAARNR FROM " . $SRC_TABLE_NAME . "";
		// Yes $SRC_TABLE_NAME is wrong but I need to finish this!!!
		$this->utvMoteDokDAO = new UtvMoteDokDAO($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$utvMote = new UtvMote();
				$utvMote->MO_ID = $result['ID'];
				$utvMote->MO_NR = $result['MOTENR'];

				if (isset($result['UTVID']) == false) {
					$utvMote->MO_UTVID = '0';
					$this->logger->log($this->XMLfilename, "MO.ID (" . $utvMote->MO_ID .  ") has null value for MO_UTVID. Setting MO_UTVID to 0" . $utvMote->MO_ID, Constants::LOG_WARNING);
					$this->warningIssued = true;
				} else {
					$utvMote->MO_UTVID = $result['UTVID'];
				}

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
					
				
				$this->utvMoteDokDAO->processUtvMoteDok($utvMote->MO_UTVID, $utvMote->MO_ID, $result['JOURAARNR']);

				$this->writeToDestination($utvMote);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO  UTVMOTE (MO_ID, MO_NR, MO_UTVID, MO_LUKKET, MO_DATO) VALUES (";
		$sqlInsertStatement .= "'" . $data->MO_ID . "', ";						
		$sqlInsertStatement .= "'" . $data->MO_NR . "', ";
		$sqlInsertStatement .= "'" . $data->MO_UTVID . "', ";
		$sqlInsertStatement .= "'" . $data->MO_LUKKET . "', ";
		$sqlInsertStatement .= "'" . $data->MO_DATO . "'";
	
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
							'MO.ID' => 'mo_id',
							'MO.NR' => 'mo_nr',
							'MO.UTVID' => 'mo_utvid',
							'MO.LUKKET' => 'mo_lukket',
							'MO.DATO' => 'mo_dato'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	
    }
}