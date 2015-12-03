<?php

require_once 'models/AdrType.php';
require_once 'utility/Constants.php';
require_once 'models/Noark4Base.php';


class AdrTypeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ADRTYPE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ADRTYPE, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$adrType = new AdrType();
				$adrType->AT_KODE = $result['ADRTYPE'];
				$adrType->AT_BETEGN = $result['BESKRIVELSE'];
				
				$this->writeToDestination($adrType);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO ADRTYPE (AT_KODE, AT_BETEGN) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->AT_KODE . "', ";						
		$sqlInsertStatement .= "'" . $data->AT_BETEGN . "'";			
	
		$sqlInsertStatement.= ");";

		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				$this->logger->log($this->XMLfilename, "Known duplicate on primary key detected. Values are " . $data->AT_KODE . "," . $data->AT_BETEGN . ". Duplicate entry ignored." , Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail  = true;
    	}
	
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ADRTYPE";
    	$mapping = array ('idColumn' => 'at_kode', 
				'rootTag' => 'ADRTYPE.TAB',	
			    		'rowTag' => 'ADRTYPE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'AT.KODE' => 'at_kode',
							'AT.BETEGN' => 'at_betegn'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }