<?php

require_once 'models/VarForm.php';
require_once 'models/Noark4Base.php';

class VarFormDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('VARFORM'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select VARIANT, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$varForm = new VarForm();
				$varForm->VF_KODE = $result['VARIANT'];
				$varForm->VF_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($varForm);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO VARFORM (VF_KODE, VF_BETEGN) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->VF_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->VF_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM VARFORM";
    	$mapping = array ('idColumn' => 'vf_kode', 
  				'rootTag' => 'VARIANTFORMAT.TAB',	
			    		'rowTag' => 'VARIANTFORMAT',
  						'encoder' => 'utf8_decode',
							'elements' => array(
								'VF.KODE' => 'vf_kode',
								'VF.BETEGN' => 'vf_betegn'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			