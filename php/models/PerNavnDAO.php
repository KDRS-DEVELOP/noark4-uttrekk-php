<?php

require_once 'models/PerNavn.php';
require_once 'models/Noark4Base.php';

class PerNavnDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('PERNAVN'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ID, PEID, AKTIV, INITIALER, FULLTNAVN, ETTERNAVN, FORNAVN, DATO, TILDATO from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		$perNavn = new PerNavn();
		$perNavn->PN_ID = Constants::INGENBRUKER_ID;
		$perNavn->PN_PEID = Constants::INGENBRUKER_ID;
		$perNavn->PN_AKTIV = "1";
		$perNavn->PN_INIT = "IBK";
		$perNavn->PN_NAVN = "INGEN BRUKER";
		$perNavn->PN_ENAVN = "BRUKER";
		$perNavn->PN_FORNAVN = "INGEN";
		$perNavn->PE_FRADATO = Constants::DATE_AUTO_START;
		$perNavn->PE_TILDATO = Constants::DATE_AUTO_END;

		$this->logger->log($this->XMLfilename, "Creating a PERNANVN with  PN_ID (" . Constants::INGENBRUKER_ID. ") to be used where we need a PERNAVN identifier but it's missing in the database", Constants::LOG_WARNING);
		$this->warningIssued = true;
		$this->writeToDestination($perNavn);


		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$perNavn = new PerNavn();
				$perNavn->PN_ID = $result['ID'];
				$perNavn->PN_PEID = $result['PEID'];
				$perNavn->PN_AKTIV = $result['AKTIV'];
				$perNavn->PN_INIT = $result['INITIALER'];
				$perNavn->PN_NAVN = $result['FULLTNAVN'];
				$perNavn->PN_ENAVN = $result['ETTERNAVN'];
				$perNavn->PN_FORNAVN = $result['FORNAVN'];
				$perNavn->PN_FRADATO = Utility::fixDateFormat($result['DATO']);
				$perNavn->PN_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				
				$this->writeToDestination($perNavn);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}

		
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO PERNAVN (PN_ID, PN_PEID, PN_AKTIV, PN_INIT, PN_NAVN, PN_ENAVN, PN_FORNAVN, PN_FRADATO, PN_TILDATO) VALUES (";

		$sqlInsertStatement .= "'" . $data->PN_ID . "', ";
		$sqlInsertStatement .= "'" . $data->PN_PEID . "', ";
		$sqlInsertStatement .= "'" . $data->PN_AKTIV . "', ";
		$sqlInsertStatement .= "'" . $data->PN_INIT . "', ";
		$sqlInsertStatement .= "'" . $data->PN_NAVN . "', ";
		$sqlInsertStatement .= "'" . $data->PN_ENAVN . "', ";
		$sqlInsertStatement .= "'" . $data->PN_FORNAVN . "', ";								
		$sqlInsertStatement .= "'" . $data->PN_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->PN_TILDATO . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }


  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM PERNAVN";
    	$mapping = array ('idColumn' => 'pn_id', 
				'rootTag' => 'PERNAVN.TAB',	
			    		'rowTag' => 'PERNAVN',
  						'encoder' => 'utf8_decode',
	  						'elements' => array(
								'PN.ID' => 'pn_id',
								'PN.PEID' => 'pn_peid',
								'PN.AKTIV' => 'pn_aktiv',
								'PN.INIT' => 'pn_init',
								'PN.NAVN' => 'pn_navn',
								'PN.FORNAVN' => 'pn_fornavn',
								'PN.ENAVN' => 'pn_enavn',
								'PN.FRADATO' => 'pn_fradato',
								'PN.TILDATO' => 'pn_tildato'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			
