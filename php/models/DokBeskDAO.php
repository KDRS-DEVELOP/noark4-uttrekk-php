<?php

require_once 'models/DokBesk.php';
require_once 'models/Noark4Base.php';

class DokBeskDAO extends Noark4Base {

	protected $kommuneName;

	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $kommuneName, $logger) {
                parent::__construct (Constants::getXMLFilename('DOKBESK'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->kommuneName = $kommuneName; 
	} 

	function processDokBeskrivelse($dokBesk) {
	
		if (is_null($dokBesk->DB_UTARBAV)) {
			$this->logger->log($this->XMLfilename, "DB_UTARBAV takes value from NOARKSAK SA.ANSVID as DOKBESK table is not implemented as per standard. This value is null for DB.DOKID (" . $dokBesk->DB_DOKID . ") Value mandatory,  set to PERSON NOUSER Value (" . Constants::INGENBRUKER_ID  . ")", Constants::LOG_WARNING);
 			$this->warningIssued = true;

			$dokBesk->DB_UTARBAV = Constants::INGENBRUKER_ID;
		}
		if (is_null($dokBesk->DB_STATUS)) {
			$this->logger->log($this->XMLfilename, "DB_STATUS is null for DB.DOKID (" . $dokBesk->DB_DOKID . ") Value mandatory,  set to F - Ferdig", Constants::LOG_WARNING);
 			$this->warningIssued = true;

			$dokBesk->DB_STATUS = "F";
		}

		if (is_null($dokBesk->DB_KATEGORI)) {
			$this->logger->log($this->XMLfilename, "DB_KATEGORI is null for DB.DOKID (" . $dokBesk->DB_DOKID . ") Value mandatory,  set to UKJENT", Constants::LOG_WARNING);
 			$this->warningIssued = true;

			$dokBesk->DB_KATEGORI = "UKJENT";
		}

		if (strcmp($dokBesk->DB_PAPIR, '1') == true) {
 			$dokBesk->DB_LOKPAPIR = "Kontakt arkivtjeneste i " . $this->kommuneName . " kommune";
		}
	
		$this->writeToDestination($dokBesk);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO DOKBESK  (DB_DOKID, DB_KATEGORI, DB_TITTEL, DB_PAPIR, DB_LOKPAPIR, DB_STATUS, DB_UTARBAV, DB_TGKODE, DB_TGGRUPPE, DB_AGDATO, DB_AGKODE, DB_UOFF) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->DB_DOKID . "', ";						
		$sqlInsertStatement .= "'" . $data->DB_KATEGORI . "', ";
		$sqlInsertStatement .= "'" . $data->DB_TITTEL . "', ";
		$sqlInsertStatement .= "'" . $data->DB_PAPIR . "', ";
		$sqlInsertStatement .= "'" . $data->DB_LOKPAPIR . "', ";
		$sqlInsertStatement .= "'" . $data->DB_STATUS . "', ";
		$sqlInsertStatement .= "'" . $data->DB_UTARBAV . "', ";
		$sqlInsertStatement .= "'" . $data->DB_TGKODE . "', ";
		$sqlInsertStatement .= "'" . $data->DB_TGGRUPPE . "', ";
		$sqlInsertStatement .= "'" . $data->DB_AGDATO . "', ";
		$sqlInsertStatement .= "'" . $data->DB_AGKODE . "', ";
		$sqlInsertStatement .= "'" . $data->DB_UOFF . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->printErrorIfFKFail = false;

//	echo $sqlInsertStatement . "\n";
	$this->logger->log($this->XMLfilename, "SQL som skapr problem " . $sqlInsertStatement, Constants::LOG_WARNING);


		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_MISSING_FK_VALUE) {

				$errorString = mysql_error();
				// Missing refernece to NOARKSAK. Probably UTGÅR - ingnored and logged as ERROR
				if (strpos($errorString, "PERSON") !== FALSE) {
					$this->logger->log($this->XMLfilename, "Missing PERSON with ID DB.UTARBAV(" . $data->DB_UTARBAV . ") for DOKID (" . $data->DB_UTARBAV . "). PERSON identified in DB.UTARBAV set to NOUSER Value (" . Constants::INGENBRUKER_ID . ")", Constants::LOG_WARNING);

					$this->warningIssued = true;

					$data->DB_UTARBAV= Constants::INGENBRUKER_ID;
					$this->writeToDestination($data);
				}			
				else {
					echo "DOKVERS proces error " . $errorString;
					die;
				}
			}
		}
		$this->uttrekksBase->printErrorIfFKFail = true;

    	}

	function processTable(){
		echo "DokBeskDAO::processTable should not be called!!! I am handled from DokLinkDAO\n";
		die;
	}

 	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM DOKBESK";
		$mapping = array ('idColumn' => 'db_dokid', 
					'rootTag' => 'DOKBESKRIV.TAB',	
						'rowTag' => 'DOKBESKRIV',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'DB.DOKID' => 'db_dokid',
									'DB.KATEGORI' => 'db_kategori',
									'DB.TITTEL' => 'db_tittel',
									'DB.PAPIR' => 'db_papir',
									'DB.LOKPAPIR' => 'db_lokpapir',
									'DB.STATUS' => 'db_status',
									'DB.UTARBAV' => 'db_utarbav',
									'DB.TGKODE' => 'db_tgkode',
									'DB.TGGRUPPE' => 'db_tggruppe',
									'DB.AGDATO' => 'db_agdato',
									'DB.AGKODE' => 'db_agkode',
									'DB.UOFF' => 'db_uoff'
									) 
							) ;
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
}
