<?php

require_once 'models/Utvalg.php';
require_once 'utility/Utility.php';
require_once 'models/Noark4Base.php';

class UtvalgDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVALG'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ID, UTV, UTVALG, AVD, FYSARK, FUNKTIL from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$utvalg = new Utvalg();
				$utvalg->UT_ID = $result['ID'];
				$utvalg->UT_KODE = $result['UTV'];
				$utvalg->UT_NAVN = $result['UTVALG'];
				
				
				if ($result['AVD'] == null)
					$utvalg->UT_ADMID = '0';
				else 
					$utvalg->UT_ADMID = $result['AVD'];
				
				$utvalg->UT_ARKDEL = $result['FYSARK'];
				if ($result['FUNKTIL'] != null)
					$utvalg->UT_NEDLAGT = Utility::fixDateFormat($result['FUNKTIL']);

				$utvalg->UT_MONUMSER = "0";
				$this->logger->log($this->XMLfilename, "NUMSER foreign key relationship missing. MONUMSER required value. Added  MONUMSER  value  = 0" , Constants::LOG_ERROR);	
				
				$this->writeToDestination($utvalg);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO UTVALG (UT_ID, UT_KODE, UT_NAVN, UT_MONUMSER, UT_ADMID, UT_ARKDEL, UT_NEDLAGT) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->UT_ID . "', ";						
		$sqlInsertStatement .= "'" . $data->UT_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->UT_NAVN . "', ";
		$sqlInsertStatement .= "'" . $data->UT_MONUMSER . "', ";						
		$sqlInsertStatement .= "'" . $data->UT_ADMID . "', ";
		$sqlInsertStatement .= "'" . $data->UT_ARKDEL . "', ";
		$sqlInsertStatement .= "'" . $data->UT_NEDLAGT . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

	}
	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM UTVALG";
		$mapping = array ('idColumn' => 'ut_id', 
					'rootTag' => 'UTVALG.TAB',	
						'rowTag' => 'UTVALG',
							'encoder' => 'utf8_decode',
							'elements' => array(
								'UT.ID' => 'ut_id',
								'UT.KODE' => 'ut_kode',
								'UT.NAVN' => 'ut_navn',
								'UT.MONUMSER' => 'ut_monumser',
								'UT.ADMID' => 'ut_admid',
								'UT.ARKDEL' => 'ut_arkdel',
								'UT.NEDLAGT' => 'ut_nedlagt'
								) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
		
    }    
 }	