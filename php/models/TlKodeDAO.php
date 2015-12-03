<?php

require_once 'models/TlKode.php';
require_once 'models/Noark4Base.php';

class TlKodeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('TLKODE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select TILLEGSK, EKODE3, ETEXT3  from " . $SRC_TABLE_NAME .  " group by TILLEGSK, EKODE3, ETEXT3 ORDER BY TILLEGSK";
		$this->logger->log($this->XMLfilename, "Created based on following query. select TILLEGSK, EKODE3, ETEXT3  from dgjmjo group by TILLEGSK, EKODE3, ETEXT3 ORDER BY TILLEGSK", Constants::LOG_INFO);
		$this->logger->log($this->XMLfilename, "This will not be correct if tilleggskoder are used that are not part of K-Koder. Manual check in DGJMJO.TILLEGSK advised.", Constants::LOG_TODO);
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

				$tlkode = new TlKode();
				$aliasAdm->TL_KODE = $result['TILLEGSK'];
				$aliasAdm->TL_BETEGN = $result['ETEXT3'];

				$this->writeToDestination($aliasAdm);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		
		$sqlInsertStatement = "INSERT INTO TLKODE (TL_KODE, TL_BETEGN) VALUES (";

		$sqlInsertStatement .= "'" . $data->TL_KODE . "',";			
		$sqlInsertStatement .= "'" . $data->TL_BETEGN . "'";
		
		$sqlInsertStatement.= ");";


		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				$this->logger->log($this->XMLfilename, "Known duplicate detected. Value is " . $data->TL_KODE . ", " . $data->TL_BETEGN . " value ignored. This duplicate value is logged here and otherwise ignored", Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail  = true;		
    }

	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM TLKODE";
		$mapping = array ('idColumn' => 'tl_kode', 
					'rootTag' => 'TLKODE.TAB',	
						'rowTag' => 'TLKODE',
							'encoder' => 'utf8_decode',
								'elements' => array(
										'TL.KODE' => 'tl_kode',
										'TL.BETEGN' => 'tl_betegn'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }