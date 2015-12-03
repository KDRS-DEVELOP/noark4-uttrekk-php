<?php

require_once 'models/LagrEnh.php';
require_once 'models/Noark4Base.php';

class LagrEnhDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('LAGRENH'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {
		$lagrEnh = new LagrEnh();
		$lagrEnh->LA_KODE = 'ENHET1'; 
		$lagrEnh->LA_BESKRIV = 'Standard enhet lagt til ved avlevering';
		$this->writeToDestination($lagrEnh);
		$this->logger->log($this->XMLfilename, "Adding (LA_KODE, LA_BESKRIV) value (ENHET1, Standard enhet lagt til ved avlevering)", Constants::LOG_INFO);
		$this->infoIssued = true;
	}

	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO LAGRENH (LA_KODE, LA_BESKRIV) VALUES (";

		$sqlInsertStatement .= "'" . $data->LA_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->LA_BESKRIV . "'";
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }    

  	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM LAGRENH";
		$mapping = array ('idColumn' => 'la_kode', 
					'rootTag' => 'LAGRENHET.TAB',	
						'rowTag' => 'LAGRENHET',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'LA_KODE' => 'la_kode',
									'LA_BESKRIV' => 'la_beskriv'
									) 
								) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
		
	}    
 }
			