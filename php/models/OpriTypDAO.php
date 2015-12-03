<?php

require_once 'models/OpriTyp.php';
require_once 'models/Noark4Base.php';

class OpriTypDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('OPRITYP'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select OPTYPE, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 

	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$oprityp = new OpriTyp();
				$oprityp->OT_KODE = $result['OPTYPE'];
				$oprityp->OT_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($oprityp);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO OPRITYP (OT_KODE, OT_BETEGN) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->OT_KODE . "', ";						
		$sqlInsertStatement .= "'" . $data->OT_BETEGN . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
     
	
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM OPRITYP";
    	$mapping = array ('idColumn' => 'ot_kode', 
  				'rootTag' => 'ORDNPRINSTYPE.TAB',	
			    		'rowTag' => 'ORDNPRINSTYPE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'OT.KODE' => 'ot_kode',
							'OT.BETEGN' => 'ot_betegn'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			