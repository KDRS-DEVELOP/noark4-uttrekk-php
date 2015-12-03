<?php

require_once 'models/PerRolle.php';
require_once 'models/Noark4Base.php';

class PerRolleDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('PERROLLE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ID, PEID, STDROLLE, TITTEL, ADMID, JOURENHET, FYSARK, DATO, TILDATO from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$perRolle = new PerRolle();
				$perRolle->PR_ID = $result['ID'];
				$perRolle->PR_PEID = $result['PEID'];
				$perRolle->PR_STDROLLE = $result['STDROLLE'];
				$perRolle->PR_TITTEL = $result['TITTEL'];
				$perRolle->PR_ADMID = $result['ADMID'];
				$perRolle->PR_JENHET = $result['JOURENHET'];
				$perRolle->PR_ARKDEL = $result['FYSARK'];
				$perRolle->PE_FRADATO = Utility::fixDateFormat($result['DATO']);
				$perRolle->PE_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				
				$this->writeToDestination($perRolle);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO PERROLLE (PR_ID, PR_PEID, PR_STDROLLE, PR_TITTEL, PR_ADMID, PR_JENHET, PR_ARKDEL, PR_FRADATO, PR_TILDATO) VALUES (";

		$sqlInsertStatement .= "'" . $data->PR_ID . "', ";
		$sqlInsertStatement .= "'" . $data->PR_PEID . "', ";
		$sqlInsertStatement .= "'" . $data->PR_STDROLLE . "', ";
		$sqlInsertStatement .= "'" . $data->PR_TITTEL . "', ";
		$sqlInsertStatement .= "'" . $data->PR_ADMID . "', ";
		$sqlInsertStatement .= "'" . $data->PR_JENHET . "', ";
		$sqlInsertStatement .= "'" . $data->PR_ARKDEL . "', ";			
		$sqlInsertStatement .= "'" . $data->PR_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->PR_TILDATO . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	}

	function createXML($extractor) { 
		$sqlQuery = "SELECT * FROM PERROLLE";
		$mapping = array ('idColumn' => 'pr_id', 
					'rootTag' => 'PERROLLE.TAB',	
						'rowTag' => 'PERROLLE',
							'encoder' => 'utf8_decode',
							'elements' => array(
								'PR.ID' => 'pr_id',
								'PR.PEID' => 'pr_peid',
								'PR.STDROLLE' => 'pr_stdrolle',
								'PR.TITTEL' => 'pr_tittel',
								'PR.ADMID' => 'pr_admid',
								'PR.JENHET' => 'pr_jenhet',
								'PR.ARKDEL' => 'pr_arkdel',
								'PR.FRADATO' => 'pr_fradato',
								'PR.TILDATO' => 'pr_tildato'
								) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
		
	}    
 }
