<?php

require_once 'models/Bskode.php';
require_once 'models/Noark4Base.php';

class BskodeDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('BSKODE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select BSKODE, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$bskode = new BSKODE();
				$bskode->BK_KODE = $result['BSKODE'];
				$bskode->BK_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($bskode);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO BSKODE (BK_KODE, BK_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->BK_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->BK_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
          
  	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM BSKODE";
		$mapping = array ('idColumn' => 'bk_kode', 
					'rootTag' => 'BSKODE.TAB',	
						'rowTag' => 'BSKODE',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'BS.KODE' => 'bk_kode',
									'BS.BETEGN' => 'bk_betegn'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }