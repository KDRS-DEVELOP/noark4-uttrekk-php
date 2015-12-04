<?php

require_once 'models/UtDokTyp.php';
require_once 'models/Noark4Base.php';

class UtDokTypDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTDOKTYP'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {


			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'RL';
			$utDokType->DU_BETEGN = 'RSL - Møteinkalling';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'RO';
			$utDokType->DU_BETEGN = 'RSO - offentlig saksliste';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'RB';
			$utDokType->DU_BETEGN = 'RMB - saksliste';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'DO';
			$utDokType->DU_BETEGN = 'DOKUMENT';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'NT';
			$utDokType->DU_BETEGN = 'NOTAT';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);



			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'SP';
			$utDokType->DU_BETEGN = 'PROTOKOLL';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'SF';
			$utDokType->DU_BETEGN = 'FREMLEGG';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'RP';
			$utDokType->DU_BETEGN = 'RAPPORT';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'VE';
			$utDokType->DU_BETEGN = 'VEDTAK';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'BR';
			$utDokType->DU_BETEGN = 'BREV';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$utDokType = new UtDokTyp();
			$utDokType->DU_KODE = 'UK';
			$utDokType->DU_BETEGN = 'Ukjent - informasjon mangler';
			$this->writeToDestination($utDokType);
			$this->logger->log($this->XMLfilename, "DU.KODE, DU_BETEGN values not specified, added by script. Value pair added is  ( " . $utDokType->DU_KODE . ", " . $utDokType->DU_BETEGN . ")", Constants::LOG_WARNING);

			$this->warningIssued = true;

	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO UTDOKTYP (DU_KODE, DU_BETEGN) VALUES (";

		$sqlInsertStatement .= "'" . $data->DU_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->DU_BETEGN . "'";
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	}  

	
	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM UTDOKTYP";
		$mapping = array ('idColumn' => 'du_kode', 
					'rootTag' => 'UTDOKTYP.TAB',	
						'rowTag' => 'UTDOKTYP',
							'encoder' => 'utf8_decode',
								'elements' => array(
										'DU.KODE' => 'du_kode',
										'DU.BETEGN' => 'du_betegn'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
		
	}
 }
