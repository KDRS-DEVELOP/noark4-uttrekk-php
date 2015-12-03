<?php

require_once 'models/JenArkd.php';
require_once 'models/Noark4Base.php';

class JenArkdDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('JENARKDEL'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select JOURENHET, FYSARK from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$jenArkd = new JenArkd();
				$jenArkd->JA_JENHET = $result['JOURENHET'];
				$jenArkd->JA_ARKDEL = $result['FYSARK'];
				$this->writeToDestination($jenArkd);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO JENARKDEL (JA_JENHET, JA_ARKDEL) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->JA_JENHET . "', ";
		$sqlInsertStatement .= "'" . $data->JA_ARKDEL . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    	}

	function createXML($extractor) {  
		$sqlQuery = "SELECT * FROM JENARKDEL";
		$mapping = array ('idColumn' => 'ja_jenhet', 
					'rootTag' => 'JENARKDEL.TAB',	
						'rowTag' => 'JENARKDEL',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'JA.JENHET' => 'ja_jenhet',
									'JA.ARKDEL' => 'ja_arkdel'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	}
 }