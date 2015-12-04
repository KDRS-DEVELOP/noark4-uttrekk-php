<?php

require_once 'models/EmneOrd.php';
require_once "utility/Utility.php";
require_once 'models/Noark4Base.php';

class EmneOrdDAO extends Noark4Base {
	
	public function  __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('EMNEORD'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {
	
	}
	

	function addEmneOrd($ord) {
		$emneOrd = new EmneOrd();
		$emneOrd->EO_EMNEORD = $ord;
		$this->writeToDestination($emneOrd);
	}


	function writeToDestination($data) {		
		$this->uttrekksBase->printErrorIfDuplicateFail = false;

		$sqlInsertStatement = "INSERT INTO EMNEORD (EO_EMNEORD) VALUES (";
		$sqlInsertStatement .= "'" . $data->EO_EMNEORD . "'";		
		$sqlInsertStatement.= ");";

		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				$this->logger->log($this->XMLfilename, "Known duplicate on primary key detected. Values are " . $data->EO_EMNEORD . ". Duplicate entry ignored." , Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }

  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM EMNEORD";
    	$mapping = array ('idColumn' => 'ar_arkiv', 
				'rootTag' => 'EMNEORD.TAB',	
			    		'rowTag' => 'EMNEORD',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'EO.EMNEORD' => 'eo_emneord'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }