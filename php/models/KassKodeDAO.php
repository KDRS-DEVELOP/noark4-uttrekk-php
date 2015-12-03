<?php

require_once 'models/KassKode.php';
require_once 'models/Noark4Base.php';

class KassKodeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('KASSKODE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select KASSKODE, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$kassKode= new KassKode();
				$kassKode->KK_KODE = $result['KASSKODE'];
				$kassKode->KK_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($kassKode);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO KASSKODE (KK_KODE, KK_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->KK_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->KK_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

	}  
	
	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM KASSKODE";
		$mapping = array ('idColumn' => 'kk_kode', 
					'rootTag' => 'KASSKODE.TAB',	
						'rowTag' => 'KASSKODE',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'KK.KODE' => 'kk_kode',
									'KK.BETEGN' => 'kk_betegn'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
		
	}    
 }
			