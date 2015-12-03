<?php

require_once 'models/ArkivPer.php';
require_once "utility/Utility.php";
require_once 'models/Noark4Base.php';

class ArkivPeriodeDAO extends Noark4Base {
	
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ARKIVPER'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select ARKIV, PERIODE, STATUS, FRADATO, TILDATO, MERKNAD from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$arkivper = new ArkivPer();
				
				$arkivper->AP_ARKIV  = $result['ARKIV'];
				$arkivper->AP_PERIODE = $result['PERIODE'];
				$arkivper->AP_STATUS = $result['STATUS'];
				$arkivper->AP_FRADATO = Utility::fixDateFormat($result['FRADATO']);
				$arkivper->AP_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				$arkivper->AP_MERKNAD = $result['MERKNAD'] ;
				$this->writeToDestination($arkivper);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO ARKIVPER (AP_ARKIV, AP_PERIODE, AP_STATUS, AP_FRADATO, AP_TILDATO, AP_MERKNAD) VALUES (";

		$sqlInsertStatement .= "'" . $data->AP_ARKIV . "', ";
		$sqlInsertStatement .= "'" . $data->AP_PERIODE . "', ";
		$sqlInsertStatement .= "'" . $data->AP_STATUS . "', ";
		$sqlInsertStatement .= "'" . $data->AP_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->AP_TILDATO . "', ";
		$sqlInsertStatement .= "'" . $data->AP_MERKNAD . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }

	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM ARKIVPER";
		$mapping = array ('idColumn' => 'ap_arkiv', 
					'rootTag' => 'ARKIVPERIODE.TAB',	
						'rowTag' => 'ARKIVPERIODE',
							'encoder' => 'utf8_decode',
							'elements' => array(
								'AP.ARKIV' => 'ap_arkiv',
								'AP.PERIODE' => 'ap_periode',
								'AP.STATUS' => 'ap_status',
								'AP.FRADATO' => 'ap_fradato',
								'AP.TILDATO' => 'ap_tildato',
								'AP.MERKNAD' => 'ap_merknad'
								) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	
	}
 }