<?php

require_once 'EarkKode.php';
require_once 'models/Noark4Base.php';
require_once 'utility/Constants.php';
require_once 'models/EmneOrdDAO.php';

class EarkKodeDAO extends Noark4Base {

	protected $emneOrd;	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('EARKKODE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select EKODE, STIKKORD FROM " . $SRC_TABLE_NAME . "";

		$this->emneOrd = new EmneOrdDAO($srcBase, $uttrekksBase, "UNKNOWN", $logger);
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
			$earkKode = new EarkKode();
			$ordVerdi = $result['EKODE'];

			if(preg_match('/^[0-9]{1,3}$/', $ordVerdi)) {
				$earkKode->EA_ORDNPRI = "FE";				
			}
			// If it starts with a &
			else if (preg_match("/^[&][0-9]{0,2}$/", $ordVerdi)) {				
				$earkKode->EA_ORDNPRI = "TI";
			}
			// IF it starts with a letter
			else if (preg_match("/^[A-Z][0-9]{1,2}$/" , $ordVerdi)) {
				$earkKode->EA_ORDNPRI = "FA";
			}
			else if (preg_match("/^[A-Z]$/" , $ordVerdi)) {
				$earkKode->EA_ORDNPRI = "FA";
			}		
			else {
				$this->logger->log($this->XMLfilename, "Unable to handle (" . $result['STIKKORD'] . ","  . $ordVerdi .  ") in EarkKode.php ", Constants::LOG_ERROR);	
				echo "[ERROR] Unable to handle (" . $ordVerdi . ")\n";	
			}

			$earkKode->EA_ORDNVER = $ordVerdi;
			// Hardcoded to 1. It probably is 1 for all cases in the database, but this should be documented
			$earkKode->EA_SORDFLAGG = '1';
			$this->logger->log($this->XMLfilename, "EA_SORDFLAGG value missing for EA_ORDNVER (" . $ordVerdi . ") setting it to 1", Constants::LOG_WARNING);
			$earkKode->EA_ORD = $result['STIKKORD'];

			if (strlen( $earkKode->EA_ORD) > 70) {
				$this->logger->log($this->XMLfilename, "Data loss EA_ORD (" . $earkKode->EA_ORD. ") is greater than Noark 4 stanard 70  char length ", Constants::LOG_ERROR);	
			}

			$this->emneOrd->addEmneOrd($earkKode->EA_ORD);

			$this->writeToDestination($earkKode);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO EARKKODE (EA_ORDNPRI, EA_ORDNVER, EA_SORDFLAGG, EA_ORD ) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->EA_ORDNPRI . "', ";						
		$sqlInsertStatement .= "'" . $data->EA_ORDNVER . "', ";
		$sqlInsertStatement .= "'" . $data->EA_SORDFLAGG . "', ";
		$sqlInsertStatement .= "'" . $data->EA_ORD . "'";			
	
		$sqlInsertStatement.= ");";


		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				$this->logger->log($this->XMLfilename, "Known duplicate on primary key detected. Values are " . $data->EA_ORDNPRI . "," . $data->EA_ORDNVER . "," . $data->EA_ORD . ". Duplicate entry ignored." , Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail  = true;
	}
	
     
	
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM EARKKODE";
    	$mapping = array ('idColumn' => 'ea_ordnpri', 
  				'rootTag' => 'EARKKODE.TAB',	
			    		'rowTag' => 'EARKKODE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'EA.ORDNPRI' => 'ea_ordnpri',
							'EA.ORDNVER' => 'ea_ordnver',
							'EA.SORDFLAGG' => 'ea_sordflagg',
							'EA.ORD' => 'ea_ord'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			