<?php

require_once 'models/Avsmot.php';
require_once 'models/Noark4Base.php';

class AvsmotDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('AVSMOT'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select ADRID, REFAARNR, IHTYPE, U1, NAVNKODE, ADRESSE, ADRESSE2, POSTNR, POSTSTED, EPOSTADR, KONTAKT, ADRGRUPPE, FAX, TELEFON, LAND FROM " . $SRC_TABLE_NAME . " WHERE REGISTER = 'J'";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$avsMot = new Avsmot();
				$avsMot->AM_ID = $result['ADRID'];
				$avsMot->AM_JPID = $result['REFAARNR'];
				$avsMot->AM_IHTYPE = $result['IHTYPE'];
; 
				$avsMot->AM_U1 = $result['U1'];
				$avsMot->AM_KORTNAVN = $result['NAVNKODE'];

				$avsMot->AM_ADRESSE = mysql_real_escape_string($result['ADRESSE']) . mysql_real_escape_string($result['ADRESSE2']);

				if (strpos($result['POSTNR'], "'") !== FALSE) {
					$commaAtPos = strpos($result['POSTNR'], "'"); 
					$avsMot->AM_POSTNR = substr($result['POSTNR'], 0 , $commaAtPos);
					$this->logger->log($this->XMLfilename, "Apostrophe detected in  AM.POSTNR with value (" . $avsMot->AM_POSTNR . "). This comes from AM.ID (" . $avsMot->AM_ID . ").  Apostrophe removed", Constants::LOG_INFO);
					$this->infoIssued = true;
				}
				else {
					$avsMot->AM_POSTNR = $result['POSTNR'];
				}

				$avsMot->AM_POSTSTED = $result['POSTSTED'];

				if (is_null($result['LAND']) == false) {
					
					$land = trim($result['LAND']);
					if (strcmp($land, 'NO') == 0 || strcmp($land, 'N') == 0) {
						// Norway do nothing
					}
					else {
						
						if (is_null($result['POSTNR']) == false && is_null($result['POSTSTED']) == false) {
							if ($this->checkPostnrPostSted ($avsMot->AM_POSTNR , $avsMot->AM_POSTSTED) == false) {
							// This is a foreign address
							 $avsMot->UTLAND = $avsMot->AM_POSTNR  . " "  .$avsMot->AM_POSTSTED . " " . $land;  
							 $avsMot->AM_POSTNR  = null;
							 $avsMot->AM_POSTSTED  = null;
							}
						}
					}
				}

				
				$avsMot->AM_EPOSTADR = mysql_real_escape_string($result['EPOSTADR']);
				$avsMot->AM_FAKS = $result['FAX'];
				$avsMot->AM_TELEFON = $result['TELEFON'];
				
				$this->writeToDestination($avsMot);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {

		$sqlInsertStatement = "INSERT INTO AVSMOT (AM_ID, AM_JPID, AM_IHTYPE, AM_U1, AM_KORTNAVN, AM_ADRESSE, AM_POSTNR, AM_POSTSTED, AM_UTLAND, AM_EPOSTADR) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->AM_ID . "',";
		$sqlInsertStatement .= "'" . $data->AM_JPID . "',";
		$sqlInsertStatement .= "'" . $data->AM_IHTYPE . "',";
		$sqlInsertStatement .= "'" . $data->AM_U1 . "',";
		$sqlInsertStatement .= "'" . $data->AM_KORTNAVN . "',";
		$sqlInsertStatement .= "'" . $data->AM_ADRESSE . "',";
		$sqlInsertStatement .= "'" . $data->AM_POSTNR . "',";
		$sqlInsertStatement .= "'" . $data->AM_POSTSTED . "',";
		$sqlInsertStatement .= "'" . $data->AM_UTLAND . "',";
		$sqlInsertStatement .= "'" . $data->AM_EPOSTADR . "'";		

		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

	}


	function checkPostnrPostSted ($postnr, $poststed) {
		$postnrQuery = "SELECT POSTNR, POSTSTED FROM DGJHPO WHERE POSTNR = '" . $postnr ."' AND POSTSTED = '" . $poststed . "'"; 
		$this->srcBase->createAndExecuteQuery ($postnrQuery);
		$result = $this->srcBase->getQueryResult ($postnrQuery);

		if ($result != false && strcasecmp($result['POSTNR'], $postnr) ) {
			$this->srcBase->endQuery($postnrQuery);
			return true;
		}

		$this->srcBase->endQuery($postnrQuery);
		return false;
	}

  	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM AVSMOT";
		$mapping = array ('idColumn' => 'am_id', 
					'rootTag' => 'AVSMOT.TAB',	
						'rowTag' => 'AVSMOT',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'AM.ID' => 'am_id',
									'AM.JPID' => 'am_jpid',
									'AM.IHTYPE' => 'am_jpid',
									'AM.U1' => 'am_u1',
									'AM.KORTNAVN' => 'am_kortnavn',
									'AM.ADRESSE' => 'am_adresse',
									'AM.POSTNR' => 'am_postnr',
									'AM.POSTSTED' => 'am_poststed',
									'AM.UTLAND' => 'am_utland',
									'AM.EPOSTADR' => 'am_epostadr',
									'AM.ADMID'  => 'am_admid',
									'AM.SBHID' => 'am_sbhid'
									) 
							) ;			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
}