<?php

require_once 'models/AvgrKode.php';
require_once 'models/Noark4Base.php';

class AvgrKodeDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('AVGRKODE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select AVGRADER, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
		$this->logger = $logger;
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$avgKode = new AvgrKode();
				$avgKode->AG_KODE = $result['AVGRADER'];
				$avgKode->AG_BETEGN = $result['BESKRIVELSE'];
			
				$this->writeToDestination($avgKode);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO AVGRKODE (AG_KODE, AG_BETEGN) VALUES (";

		$sqlInsertStatement .= "'" . $data->AG_KODE . "', ";		
		$sqlInsertStatement .= "'" . $data->AG_BETEGN . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }

 	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM AVGRKODE";
		$mapping = array ('idColumn' => 'ag_kode', 
					'rootTag' => 'AVGRADKODE.TAB',	
						'rowTag' => 'AVGRADKODE',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'AG.KODE' => 'ag_kode',
									'AG.BETEGN' => 'ag_betegn'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }
	