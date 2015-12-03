<?php

require_once 'models/StatMDok.php';
require_once 'models/Noark4Base.php';

class StatMDokDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('STATMDOK'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {
		$this->logger->log($this->XMLfilename, "Did not find STATUSMDOK. They probably get values from JOURNSTA, but N4 standard has listed some values. Using those. Adding hardcoded values via this script", Constants::LOG_INFO);

		$statMDok = new StatMDok();
		$statMDok->MS_STATUS = 'R'; 
		$statMDok->MS_BETEGN = 'Reservert av saksbehandler';
		$this->writeToDestination($statMDok);
		$this->logger->log($this->XMLfilename, "Missing values (MS_STATUS, MS_BETEGN) (R, Reservert av saksbehandler) Added", Constants::LOG_INFO);

		$statMDok = new StatMDok();
		$statMDok->MS_STATUS = 'F'; 
		$statMDok->MS_BETEGN = 'Ferdig fra saksbehandler, klar for godkjenning av leder';
		$this->logger->log($this->XMLfilename, "Missing values (MS_STATUS, MS_BETEGN) (F,  Ferdig fra saksbehandler, klar for godkjenning av leder)  Added", Constants::LOG_INFO);	
		$this->writeToDestination($statMDok);

		$statMDok = new StatMDok();
		$statMDok->MS_STATUS = 'G'; 
		$statMDok->MS_BETEGN = 'Godkjent av leder';
		$this->logger->log($this->XMLfilename, "Missing values (MS_STATUS, MS_BETEGN) (G, Godkjent av leder) Added", Constants::LOG_INFO);
		$this->writeToDestination($statMDok);

		$statMDok = new StatMDok();
		$statMDok->MS_STATUS = 'A'; 
		$statMDok->MS_BETEGN = 'Arkiveksemplar tilgjengelig';
		$this->logger->log($this->XMLfilename, "Missing values (MS_STATUS, MS_BETEGN) (A, Arkiveksemplar tilgjengelig) Added", Constants::LOG_INFO);
		$this->writeToDestination($statMDok);

	}
	
	function writeToDestination($data) {

		$sqlInsertStatement = "INSERT INTO STATMDOK (MS_STATUS, MS_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->MS_STATUS . "',";
		$sqlInsertStatement .= "'" . $data->MS_BETEGN . "'";
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM STATMDOK";
    	$mapping = array ('idColumn' => 'ms_status', 
  				'rootTag' => 'STATMDOK.TAB',	
			    		'rowTag' => 'STATMDOK',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'MS.STATUS' => 'ms_status',
							'MS.BETEGN' => 'ms_betegn'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			