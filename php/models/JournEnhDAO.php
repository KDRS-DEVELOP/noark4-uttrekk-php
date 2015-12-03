<?php

require_once 'models/JournEnh.php';
require_once 'models/Noark4Base.php';

class JournEnhDAO  extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('JOURNENH'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select JOURENHET, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

/*		$journenh = new JournEnh();
		$journenh->JE_JENHET = "";
		$journenh->JE_BETEGN = "";
		$this->writeToDestination($journenh);
*/


		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$journenh = new JournEnh();
				$journenh->JE_JENHET = $result['JOURENHET'];
				$journenh->JE_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($journenh);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO JOURNENH (JE_JENHET, JE_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->JE_JENHET . "', ";
		$sqlInsertStatement .= "'" . $data->JE_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
    
     function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM JOURNENH";
    	$mapping = array ('idColumn' => 'je_jenhet', 
  				'rootTag' => 'JOURNENH.TAB',	
			    		'rowTag' => 'JOURNENH',
  						'encoder' => 'utf8_decode',
							'elements' => array(
								'JE.JENHET' => 'je_jenhet',
								'JE.BETEGN' => 'je_betegn'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }        
 }
			