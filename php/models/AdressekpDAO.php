<?php

require_once 'models/Adressekp.php';
require_once 'models/Noark4Base.php';

// TODO: ADRGRUPE and KORTNAVN

class AdressekpDAO extends Noark4Base {
	
	public function  __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ADRESSEK'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select ADRID, ADRTYPE, NAVNKODE, NAVN, ADRESSE, ADRESSE2, POSTNR, POSTSTED, EPOSTADR, FAX, TELEFON, ORGNR from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$adressekp = new Adressekp();
				$adressekp->AK_ADRID = $result['ADRID'];
				$adressekp->AK_TYPE = $result['ADRTYPE'];
				$adressekp->AK_KORTNAVN = mysql_real_escape_string($result['NAVNKODE']);
				$adressekp->AK_NAVN = $result['NAVN'];
				$adressekp->AK_POSTADR = $result['ADRESSE'] . $result['ADRESSE2'];
				$adressekp->AK_POSTNR = $result['POSTNR'];
				$adressekp->AK_POSTSTED = $result['POSTSTED'];
				$adressekp->AK_EPOST = $result['EPOSTADR'];
				$adressekp->AK_FAKS = $result['FAX'];
				$adressekp->AK_TLF = $result['TELEFON'];
				$adressekp->AK_ORGNR = $result['ORGNR'];
				
				$this->writeToDestination($adressekp);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO ADRESSEK (AK_ADRID, AK_TYPE, AK_KORTNAVN, AK_NAVN, AK_POSTADR, AK_POSTNR, AK_POSTSTED, AK_EPOST, AK_FAKS, AK_TLF, AK_ORGNR) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->AK_ADRID . "', ";						
		$sqlInsertStatement .= "'" . $data->AK_TYPE . "', ";
		$sqlInsertStatement .= "'" . $data->AK_KORTNAVN . "', ";
		$sqlInsertStatement .= "'" . $data->AK_NAVN . "', ";
		$sqlInsertStatement .= "'" . $data->AK_POSTADR . "', ";
		$sqlInsertStatement .= "'" . $data->AK_POSTNR . "', ";
		$sqlInsertStatement .= "'" . $data->AK_POSTSTED . "', ";
		$sqlInsertStatement .= "'" . $data->AK_EPOST . "', ";
		$sqlInsertStatement .= "'" . $data->AK_FAKS . "', ";
		$sqlInsertStatement .= "'" . $data->AK_TLF . "', ";
		$sqlInsertStatement .= "'" . $data->AK_ORGNR . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				$this->logger->log($this->XMLfilename, "Duplicate AK.ADRID(" . $data->AK_ADRID . ") with following values:  AK.NAVN (" . $data->AK_NAVN . "), AK.ADRESSE (" . $data->AK_POSTADR . "), AK.POSTNR  (" . $data->AK_POSTNR . "), AK.POSTSTED" . $data->AK_POSTSTED .  "). This duplicate value is logged here and otherwise ignored", Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail  = true;


    }

	function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ADRESSEK";
    	$mapping = array ('idColumn' => 'ak_adrid', 
				'rootTag' => 'ADRESSEKP.TAB',	
			    		'rowTag' => 'ADRESSEKP',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
						'AK.ADRID' => 'ak_adrid',
						'AK.TYPE' => 'ak_type',
    						'AK.KORTNAVN' => 'ak_kortnavn',
    						'AK.NAVN' => 'ak_navn',
    						'AK.POSTADR' => 'ak_postadr',
    						'AK.POSTNR' => 'ak_postnr',
    						'AK.POSTSTED' => 'ak_poststed',
    						'AK.EPOST' => 'ak_epost',
    						'AK.FAKS' => 'ak_faks',
    						'AK.TLF' => 'ak_tlf',
    						'AK.ORGNR' => 'ak_orgnr'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");

    }
 }