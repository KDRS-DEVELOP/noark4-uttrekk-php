<?php

require_once 'models/Postnr.php';
require_once 'models/Noark4Base.php';


class PostnrDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('POSTNR'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select POSTNR, POSTSTED, KOMMUNENR, KOMMUNE  from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$postnr = new Postnr();
				$postnr->PO_POSTNR = $result['POSTNR'];
				$postnr->PO_POSTSTED = $result['POSTSTED'];
				$postnr->PO_KOMNR = $result['KOMMUNENR'];
				$postnr->PO_KOMMUNE = $result['KOMMUNE'];
				
				$this->writeToDestination($postnr);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO POSTNR (PO_POSTNR, PO_POSTSTED, PO_KOMNR, PO_KOMMUNE) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->PO_POSTNR . "', ";
		$sqlInsertStatement .= "'" . $data->PO_POSTSTED . "', ";		
		$sqlInsertStatement .= "'" . $data->PO_KOMNR . "', ";		
		$sqlInsertStatement .= "'" . $data->PO_KOMMUNE . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
	
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM POSTNR";
    	$mapping = array ('idColumn' => 'po_postnr', 
  				'rootTag' => 'POSTNR.TAB',	
			    		'rowTag' => 'POSTNR',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
								'PO.POSTNR' => 'po_postnr',
								'PO.POSTSTED' => 'po_poststed',
								'PO.KOMNR' => 'po_komnr',
								'PO.KOMMUNE' => 'po_kommune'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }