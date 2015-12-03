<?php

require_once 'models/DokKat.php';
require_once 'models/Noark4Base.php';

class DokKatDAO extends Noark4Base {
	
	var $XMLfilename = 'DOKKAT.XML';	
	var $uttrekksBase = null;
	var $srcBase = null;
	var $SRC_TABLE_NAME = null;
	var $selectQuery = null;
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('DOKKAT'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select DOKKAT, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$dokKat = new DokKat();
		$dokKat->DK_KODE = "UKJENT";
		$dokKat->DK_BETEGN = "Lagt til ved generering av uttrekk fordi andre tabeller trenger en verdi";
		$this->writeToDestination($dokKat);

		$this->logger->log($this->XMLfilename, "DK_KODE, DK_BETEGN values (UKJENT - Lagt til ved generering av uttrekk fordi andre tabeller trenger en verdi) added here as DOKBESK has a mandatory requirement that DOKKAT has a values. This is not true in all cases so UKJENT is added here and used in DOKBESK", Constants::LOG_INFO);
	

		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$dokKat = new DokKat();
				$dokKat->DK_KODE = $result['DOKKAT'];
				$dokKat->DK_BETEGN = $result['BESKRIVELSE'];
				$this->writeToDestination($dokKat);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO DOKKAT (DK_KODE, DK_BETEGN) VALUES (";

		$sqlInsertStatement .= "'" . $data->DK_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->DK_BETEGN . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	}


  	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM DOKKAT";
		$mapping = array ('idColumn' => 'dk_kode', 
					'rootTag' => 'DOKKATEGORI.TAB',	
						'rowTag' => 'DOKKATEGORI',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'DK.KODE' => 'dk_kode',
									'DK.BETEGN' => 'dk_betegn'
									) 
							) ;
			
    		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");

    }
 }
