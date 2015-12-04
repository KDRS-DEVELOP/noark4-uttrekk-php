<?php

require_once 'models/DokVers.php';
require_once 'utility/Constants.php';

class DokVersDAO extends Noark4Base {



	public function DokVersDAO  ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $kommuneName, $logger) {
		parent::__construct (Constants::getXMLFilename('DOKVERS'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

	}
	
	function processDokVers($dokVers) {

		if (is_null($dokVers->VE_AKTIV)) {
			$this->logger->log($this->XMLfilename, "VE_AKTIV is null for DB.DOKID (" . $dokVers->VE_DOKID . ") Value mandatory, set to 1", Constants::LOG_WARNING);
 			$this->warningIssued = true;

			$dokVers->VE_AKTIV = "1";
		}

		if (is_null($dokVers->VE_REGAV)) {
			$this->logger->log($this->XMLfilename, "VE_REGAV takes value from NOARKSAK SA.ANSVID as DOKVERS table is not implemented as per standard. This value is null for VE_DOKID (" . $dokVers->VE_DOKID . ") Value mandatory,  set to PERSON NOUSER Value (" . Constants::INGENBRUKER_ID  . ")", Constants::LOG_WARNING);
			$dokVers->VE_REGAV = Constants::INGENBRUKER_ID;
 			$this->warningIssued = true;
		}	

 
		$this->writeToDestination($dokVers);
	}

	function processTable(){
		echo "DokVerskDAO::processTable should not be called!!! I am handled from DokLinkDAO\n";
		die;
	}


	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO DOKVERS (VE_DOKID, VE_VERSJON, VE_VARIANT, VE_AKTIV, VE_DOKFORMAT, VE_REGAV, VE_TGKODE, VE_LAGRENH, VE_FILREF) VALUES (";

		$sqlInsertStatement .= "'" . $data->VE_DOKID . "', ";
		$sqlInsertStatement .= "'" . $data->VE_VERSJON . "', ";
		$sqlInsertStatement .= "'" . $data->VE_VARIANT . "', ";
		$sqlInsertStatement .= "'" . $data->VE_AKTIV . "', ";
		$sqlInsertStatement .= "'" . $data->VE_DOKFORMAT . "', ";
		$sqlInsertStatement .= "'" . $data->VE_REGAV . "', ";
		$sqlInsertStatement .= "'" . $data->VE_TGKODE . "', ";
		$sqlInsertStatement .= "'" . $data->VE_LAGRENH . "', ";
		$sqlInsertStatement .= "'" . $data->VE_FILREF . "' ";

		$sqlInsertStatement.= ");";


//		echo $sqlInsertStatement . "\n";

		$this->uttrekksBase->printErrorIfFKFail = false;

		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_MISSING_FK_VALUE) {

				$errorString = mysql_error();
				
				if (strpos($errorString, "person") !== FALSE) {
					$this->logger->log($this->XMLfilename, "Missing PERSON with ID VE.REGAV(" . $data->VE_REGAV . ") for DOKID (" . $data->VE_DOKID . "). PERSON identified in VE_REGAV set to NOUSER Value (" . Constants::INGENBRUKER_ID . ")", Constants::LOG_WARNING);

					$this->warningIssued = true;

					$data->VE_REGAV = Constants::INGENBRUKER_ID;					
					$this->writeToDestination($data);
				}			
				else {
				  die;
					echo "DOKVERS proces error " . $errorString;

				}
			}
		}
		$this->uttrekksBase->printErrorIfFKFail = true;
    	}


	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM DOKVERS";
		$mapping = array ('idColumn' => 've_dokid', 
					'rootTag' => 'DOKVERS.TAB',	
						'rowTag' => 'DOKVERSJON',
							'encoder' => 'utf8_decode',
								'elements' => array(
										'VE.DOKID' => 've_dokid',
										'VE.VERSJON' => 've_versjon',
										'VE.VARIANT' => 've_variant',
										'VE.AKTIV' => 've_aktiv',
										'VE.DOKFORMAT' => 've_dokformat',
										'VE.REGAV' => 've_regav',
										'VE.TGKODE' => 've_tgkode',
										'VE.LAGRENH' => 've_lagrenh',
										'VE.FILREF' => 've_filref'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }
