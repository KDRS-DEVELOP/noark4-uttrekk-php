<?php

require_once 'models/Arkiv.php';
require_once "utility/Utility.php";
require_once 'models/Noark4Base.php';

class ArkivDAO extends Noark4Base {
	
	public function  __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ARKIV'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select ARKIV, BESKRIVELSE, FRADATO, TILDATO, MERKNAD from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);


		$arkiv = new Arkiv();
		$arkiv->AR_ARKIV = "RNG";
		$arkiv->AR_BETEGN = "RINGPERM added RINGPERM ARKIVDEL. Added so that structure is correct the extracttion.";
		$arkiv->AR_NUMSER = '1'; 
		$arkiv->AR_FRADATO = Utility::fixDateFormat(Constants::DATE_AUTO_START);
		$arkiv->AR_TILDATO = Utility::fixDateFormat(Constants::DATE_AUTO_END);
		$this->logger->log($this->XMLfilename, "Creating an AR_ARKIV (" . $arkiv->AR_ARKIV . ") to handle an ARKIVDEL called RINGPERM that is missing a reference to ARKIV. Setting AR.FRADATO to Constants::DATE_AUTO_START which has value (". Utility::fixDateFormat(Constants::DATE_AUTO_START) . ") Setting AR.TILDATO to Constants::DATE_AUTO_END which has value (". Utility::fixDateFormat(Constants::DATE_AUTO_END).  ")", Constants::LOG_WARNING);
		$this->warningIssued = true;

		$this->logger->log($this->XMLfilename, "AR_ARKIV (" . $arkiv->AR_ARKIV . ") has null value for AR.TILDATO. Setting AR.TILDATO to Constants::DATE_AUTO_END which has value (". Constants::DATE_AUTO_END.  ")", Constants::LOG_WARNING);
		$this->warningIssued = true;
			
		
		$this->writeToDestination($arkiv);


		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$arkiv = new Arkiv();
				$arkiv->AR_ARKIV = $result['ARKIV'];
				$arkiv->AR_BETEGN = $result['BESKRIVELSE'];
				// TODO : VERY IMPORTANT CHECK NUMSER
				$arkiv->AR_NUMSER = '1'; 
				$arkiv->AR_FRADATO = Utility::fixDateFormat($result['FRADATO']);

				if (isset($result['TILDATO']) == true)  
					$arkiv->AR_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				else {
					$arkiv->AR_TILDATO = Utility::fixDateFormat(Constants::DATE_AUTO_END);
					$this->logger->log($this->XMLfilename, "AR_ARKIV (" . $result['ARKIV'] . ") has null value for AR.TILDATO. Setting AR.TILDATO to Constants::DATE_AUTO_END which has value (". Constants::DATE_AUTO_END.  ")", Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
					
				$arkiv->AR_MERKNAD = $result['MERKNAD'];

				$this->writeToDestination($arkiv);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO ARKIV (AR_ARKIV, AR_BETEGN, AR_NUMSER, AR_FRADATO, AR_TILDATO, AR_MERKNAD) VALUES (";

		$sqlInsertStatement .= "'" . $data->AR_ARKIV . "', ";
		$sqlInsertStatement .= "'" . $data->AR_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->AR_NUMSER . "', ";
		$sqlInsertStatement .= "'" . $data->AR_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->AR_TILDATO . "', ";
		$sqlInsertStatement .= "'" . $data->AR_MERKNAD . "'";			
		
		$sqlInsertStatement.= ");";
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }

  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ARKIV";
    	$mapping = array ('idColumn' => 'ar_arkiv', 
				'rootTag' => 'ARKIV.TAB',	
			    		'rowTag' => 'ARKIV',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'AR.ARKIV' => 'ar_arkiv',
							'AR.BETEGN' => 'ar_betegn',
							'AR.NUMSER' => 'ar_numser',
							'AR.FRADATO' => 'ar_fradato',
							'AR.TILDATO' => 'ar_tildato',
							'AR.MERKNAD' => 'ar_merknad'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }