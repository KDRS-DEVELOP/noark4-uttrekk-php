<?php

require_once 'models/SakPart.php';
require_once 'models/Noark4Base.php';

class SakPartDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('SAKPART'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select REFAARNR, U1, NAVNKODE, ADRESSE, ADRESSE2, POSTNR, POSTSTED, EPOSTADR, KONTAKT, ADRGRUPPE, FAX, TELEFON, LAND FROM " . $SRC_TABLE_NAME . " WHERE REGISTER = 'S'";		
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$sakPart = new SakPart();
				
				$sakPart->SP_ID = $result['REFAARNR']; 
				$sakPart->SP_U1 = $result['U1'];
				$sakPart->SP_KORTNAVN = $result['NAVNKODE'];

				$sakPart->SP_ADRESSE = $result['ADRESSE'] . $result['ADRESSE2'];
				// It's known one POSTNR contains a " ' ". So checking for it 
				$sakPart->SP_POSTNR = str_replace("'", "", $result['POSTNR']);

				



				$sakPart->SP_POSTSTED = $result['POSTSTED'];

				if (is_null($result['LAND']) == false) {
					
					$land = trim($result['LAND']);
					if (strcmp($land, 'NO') == 0 || strcmp($land, 'N') == 0) {
						// Norway do nothing
					}
					else {
						if (is_null($result['POSTNR']) == false && is_null($result['POSTSTED']) == false) {
							if ($this->checkPostnrPostSted ($sakPart->SP_POSTNR, $sakPart->SP_POSTSTED ) == false) {
							// This is a foreign address
							 $sakPart->UTLAND = $sakPart->SP_POSTNR  . " "  .$sakPart->SP_POSTSTED . " " . $land;  
							 $sakPart->SP_POSTNR  = null;
							 $sakPart->SP_POSTSTED  = null;
							}
						}
					}
				}

				$sakPart->SP_EPOSTADR = $result['EPOSTADR'];
				$sakPart->SP_KONTAKT = $result['KONTAKT'];
				$sakPart->SP_ROLLE = $result['ADRGRUPPE'];
				$sakPart->SP_FAKS = $result['FAX'];
				$sakPart->SP_TELEFON = $result['TELEFON'];
				
				$this->writeToDestination($sakPart);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {

		$sqlInsertStatement = "INSERT INTO SAKPART (SP_SAID, SP_U1, SP_KORTNAVN, SP_ADRESSE, SP_POSTNR, SP_POSTSTED, SP_UTLAND, SP_EPOSTADR, SP_KONTAKT, SP_ROLLE, SP_FAKS, SP_TLF) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->SP_SAID . "',";
		$sqlInsertStatement .= "'" . $data->SP_U1 . "',";
		$sqlInsertStatement .= "'" . $data->SP_KORTNAVN . "',";
		$sqlInsertStatement .= "'" . mysql_real_escape_string($data->SP_ADRESSE) . "',";
		$sqlInsertStatement .= "'" . $data->SP_POSTNR . "',";
		$sqlInsertStatement .= "'" . $data->SP_POSTSTED . "',";
		$sqlInsertStatement .= "'" . $data->SP_UTLAND . "',";
		$sqlInsertStatement .= "'" . $data->SP_EPOSTADR . "',";
		$sqlInsertStatement .= "'" . $data->SP_KONTAKT . "',";
		$sqlInsertStatement .= "'" . $data->SP_ROLLE . "',";
		$sqlInsertStatement .= "'" . $data->SP_FAKS . "',";
		$sqlInsertStatement .= "'" . $data->SP_TLF . "'";
		
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
		$sqlQuery = "SELECT * FROM SAKPART";
		$mapping = array ('idColumn' => 'sp_said', 
					'rootTag' => 'SAKPART.TAB',	
						'rowTag' => 'SAKPART',
							'encoder' => 'utf8_decode',
							'elements' => array(
								'SP.SAID' => 'sp_said',
								'SP.U1' => 'sp_u1',
								'SP.KORTNAVN' => 'sp_kortnavn',
								'SP.ADRESSE' => 'sp_adresse',
								'SP.POSTNR' => 'sp_postnr',
								'SP.POSTSTED' => 'sp_poststed',
								'SP.UTLAND' => 'sp_utland',
								'SP.EPOSTADR' => 'sp_epostadr',
								'SP.KONTAKT' => 'sp_kontakt',
								'SP.ROLLE' => 'sp_rolle',
								'SP.FAKS' => 'sp_faks',
								'SP.TLF' => 'sp_tlf'
								) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");		
	}    
 }
			