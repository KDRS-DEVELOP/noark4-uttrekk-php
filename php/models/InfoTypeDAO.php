<?php

require_once 'models/InfoType.php';
require_once 'models/Noark4Base.php';

class InfoTypeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('INFOTYPE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select KODE, BESKRIVELSE, LTEKST1, AUTOLOG, OPPBETID, MERKNAD from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$infoType = new InfoType();
				$infoType->IT_KODE = $result['KODE'];
				$infoType->IT_BETEGN = $result['BESKRIVELSE'];
				$infoType->IT_LTEKST1 = $result['LTEKST1'];
				$infoType->IT_AUTOLOG = $result['AUTOLOG'];
				$infoType->IT_OPPBETID = $result['OPPBETID'];
				$infoType->IT_MERKNAD = $result['MERKNAD'];
				$this->writeToDestination($infoType);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO INFOTYPE (IT_KODE, IT_BETEGN, IT_LTEKST1, IT_AUTOLOG, IT_OPPBETID, IT_MERKNAD) VALUES (";

		$sqlInsertStatement .= "'" . $data->IT_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->IT_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->IT_LTEKST1 . "', ";
		$sqlInsertStatement .= "'" . $data->IT_AUTOLOG . "', ";
		$sqlInsertStatement .= "'" . $data->IT_OPPBETID . "', ";		
		$sqlInsertStatement .= "'" . $data->IT_MERKNAD . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }  



  	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM INFOTYPE";
		$mapping = array ('idColumn' => 'it_kode', 
					'rootTag' => 'INFOTYPE.TAB',	
						'rowTag' => 'INFOTYPE',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'IT.KODE' => 'it_kode',
									'IT.BETEGN' => 'it_betegn',
									'IT.LTEKST1' => 'it_ltekst1',
									'IT.MERKNAD' => 'it_merknad',
									'IT.AUTOLOG' => 'it_autolog',
									'IT.OPPBETID' => 'it_oppbetid'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	}
 }