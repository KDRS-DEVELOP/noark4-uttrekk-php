<?php

require_once 'models/DokType.php';
require_once 'models/Noark4Base.php';
require_once 'utility/Constants.php';

class DokTypeDAO extends Noark4Base {

	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('DOKTYPE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select TYPE, TITTEL, EKSTPROD, INTMOT, EKSTMOT, OPPF from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {


		$dokType = new DokType();
		$dokType->ND_DOKTYPE = "IA";
		$dokType->ND_BETEGN = "I noen tilfeller mangler DOKTYPE (verdi lik null). Ser ut som det er i saker som utgår men ikke bekreftet. IA står for ikke angitt og brukes for disse";
		$dokType->ND_EKSTPROD = '1';
		$dokType->ND_INTMOT = '1';
		$dokType->ND_EKSTMOT = '1';
		$dokType->ND_OPPF = '1';
		$this->writeToDestination($dokType);
		$this->logger->log($this->XMLfilename, "In some instances DOKTYPE is mising. Adding a Not 'specified value' DOKTYPE (IA) that can be used with these instances", Constants::LOG_INFO);
		$this->infoIssued = true;


	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$dokType = new DokType();
				$dokType->ND_DOKTYPE = $result['TYPE'];
				$dokType->ND_BETEGN = $result['TITTEL'];
				$dokType->ND_EKSTPROD = $result['EKSTPROD'];
				$dokType->ND_INTMOT = $result['INTMOT'];
				$dokType->ND_EKSTMOT = $result['EKSTMOT'];
				$dokType->ND_OPPF = $result['OPPF'];
				$this->writeToDestination($dokType);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		if (strcasecmp($data->ND_DOKTYPE, 'I') == 0 && strcasecmp($data->ND_BETEGN , 'Søknad om stilling') == 0) {
			$this->logger->log($this->XMLfilename, "Removed I - Søknad om stilling coming from " . $this->SRC_TABLE_NAME . " to prevent duplicate primary key.",  Constants::LOG_WARNING);
			$this->warningIssued = true;
			return;
		} 

		$sqlInsertStatement = "INSERT INTO DOKTYPE (ND_DOKTYPE, ND_BETEGN, ND_EKSTPROD, ND_INTMOT, ND_EKSTMOT, ND_OPPF) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->ND_DOKTYPE . "', ";
		$sqlInsertStatement .= "'" . $data->ND_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->ND_EKSTPROD . "', ";
		$sqlInsertStatement .= "'" . $data->ND_INTMOT . "', ";
		$sqlInsertStatement .= "'" . $data->ND_EKSTMOT . "', ";		
		$sqlInsertStatement .= "'" . $data->ND_OPPF . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	}  
		
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM DOKTYPE";
    	$mapping = array ('idColumn' => 'nd_doktype', 
  				'rootTag' => 'NOARKDOKTYPE.TAB',	
			    		'rowTag' => 'NOARKDOKTYPE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'ND.DOKTYPE' => 'nd_doktype',
							'ND.BETEGN' => 'nd_betegn',
							'ND.EKSTPROD' => 'nd_ekstprod',
							'ND.INTMOT' => 'nd_intmot',
							'ND.EKSTMOT' => 'nd_ekstmot',
							'ND.OPPF' => 'nd_oppf'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	
    }
 }