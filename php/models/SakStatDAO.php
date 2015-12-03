<?php

require_once 'models/SakStat.php';
require_once 'models/Noark4Base.php';

class SakStatDAO extends Noark4Base {

	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('SAKSTAT'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select STATUS, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$sakStat = new SakStat();

				$sakStat->SS_STATUS = $result['STATUS'];
				$sakStat->SS_BETEGN = $result['BESKRIVELSE'];

				$this->writeToDestination($sakStat);
		}
		$this->srcBase->endQuery($this->selectQuery);
		$this->logger->log($this->SRC_TABLE_NAME, "Non mandatory values SS_MIDLERTIDIG, SS_LUKKET, SS_UTG not present in source table", Constants::LOG_INFO);
		$this->infoIssued = true;
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO SAKSTAT (SS_STATUS, SS_BETEGN) VALUES (";

		$sqlInsertStatement .= "'" . $data->SS_STATUS . "', ";
		$sqlInsertStatement .= "'" . $data->SS_BETEGN . "'";
		
		$sqlInsertStatement.= ");";

		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->SRC_TABLE_NAME, "Known duplicate value detected. Value is " . $data->SS_STATUS, Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}	
    }  

	
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM SAKSTAT";
    	$mapping = array ('idColumn' => 'ss_status', 
				'rootTag' => 'SAKSTATUS.TAB',	
			    		'rowTag' => 'SAKSTATUS',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'SS.STATUS' => 'ss_status',
							'SS.BETEGN' => 'ss_betegn'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
}
