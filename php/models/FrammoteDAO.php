<?php

require_once 'models/Frammote.php';

class FrammoteDAO {
	
	var $XMLfilename = 'FRAMMOTE.XML';	
	var $uttrekksBase = null;
	var $srcBase = null;
	var $SRC_TABLE_NAME = null;
	var $selectQuery = null;
	
	public function UtvMedlFunkDAO  ($srcBase, $uttrekksBase, $SRC_TABLE_NAME) {		
		$this->srcBase = $srcBase;
		$this->uttrekksBase = $uttrekksBase;		
		$this->SRC_TABLE_NAME = $SRC_TABLE_NAME;
		$this->selectQuery = "select MOTEID, FROM " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$utvMedlFunk = new UtvMedlFunk();
				$utvMedlFunk->FU_MOID = $result['MOTEID'];
				$utvMedlFunk->FU_PNID = $result[''];
				$utvMedlFunk->FU_SORT = $result[''];
				$utvMedlFunk->FU_FUNK = $result[''];				
				$utvMedlFunk->FU_SEKR = $result[''];
				$this->writeToDestination($utvMedlFunk);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO (FU_MOID, FU_PNID, FU_SORT, FU_FUNK, FU_SEKR) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->FU_MOID . "', ";						
		$sqlInsertStatement .= "'" . $data->FU_PNID.  "', ";
		$sqlInsertStatement .= "'" . $data->FU_SORT . "', ";
		$sqlInsertStatement .= "'" . $data->FU_FUNK . "', ";
		$sqlInsertStatement .= "'" . $data->FU_SEKR . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
 
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM FRAMMOTE";
    	$mapping = array ('idColumn' => 'fu_moid', 
  						'rootTag' => 'FRAMMOTE.TAB',	
			    		'rowTag' => 'FRAMMOTE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'FU.MOID' => 'fu_moid',
							'FU.PNID' => 'fu_pnid',
							'FU.SORT' => 'fu_sort',
							'FU.FUNK' => 'fu_funk',
							'FU.SEKR' => 'fu_sekr'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			