<?php

require_once 'models/UtDokTyp.php';
require_once 'models/Noark4Base.php';

class UtDokTypeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTDOKTYP'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select * from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {

		echo "ERROR !!  Not handled yet!!!!"; 
		return;
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
			$utDokType = new SakType();
			$utDokType->DU_KODE = $result[''];
			$utDokType->DU_BETEGN = $result[''];

			$this->writeToDestination($utDokType);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO SAKTYPE (DU_KODE, DU_BETEGN, ST_KLAGEADG, ST_UOFF) VALUES (";

		$sqlInsertStatement .= "'" . $data->DU_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->DU_BETEGN . "'";
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	}  

	
	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM UTDOKTYP";
		$mapping = array ('idColumn' => 'du_kode', 
					'rootTag' => 'UTDOKTYP.TAB',	
						'rowTag' => 'UTDOKTYP',
							'encoder' => 'utf8_decode',
								'elements' => array(
										'DU.KODE' => 'du_kode',
										'DU.BETEGN' => 'du_betegn'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
		
	}
 }