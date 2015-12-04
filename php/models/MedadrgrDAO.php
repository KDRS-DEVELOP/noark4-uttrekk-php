<?php

require_once 'models/Medadrgr.php';
require_once 'models/Noark4Base.php';
/*

<MEDLADRGR>
   <MG.GRID>403</MG.GRID>
  </MEDLADRGR>

*/



class MedAdrGrDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('MEDADRGR'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ADRID, GRUPPEID, UNAVN from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$medAdrgr = new MedAdrGr();

				$medAdrgr->MG_GRID = $result['ADRID'];
				$medAdrgr->MG_MEDLID = $result['GRUPPEID'];

				if (isset($result['ADRID']) == true && isset($result['GRUPPEID'])) {
					$this->writeToDestination($medAdrgr);
				}
				else {
					$this->logger->log($this->XMLfilename, "One of MG_GRID (" . $result['ADRID'] . ") and  (" . $result['ADRID'] . ") is null. These values are ignored in the extraction. In ESA additional field UNANV contains " . $result['UNAVN'], Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
		}
		$this->srcBase->endQuery($this->selectQuery);
	}

	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO MEDADRGR (MG_GRID, MG_MEDLID) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->MG_GRID . "', ";
		$sqlInsertStatement .= "'" . $data->MG_MEDLID  . "'";
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				$this->logger->log($this->XMLfilename, "Duplicate (ADRID, GRUPPEID) with values (" .$data->MG_GRID . ", " . $data->MG_MEDLID .  "). Duplicate values logged here and removed", Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail  = true;
    }  	
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM MEDADRGR";
    	$mapping = array ('idColumn' => 'mg_grid', 
  				'rootTag' => 'MEDLADRGR.TAB',	
			    		'rowTag' => 'MEDLADRGR',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'MG.GRID' => 'mg_grid',
							'MG.MEDLID' => 'mg_medlid'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    

 }
			