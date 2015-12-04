<?php

require_once 'models/EnhType.php';
require_once 'models/Noark4Base.php';

class EnhTypeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ENHTYPE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 


	function processTable () {

		$this->logger->log($this->XMLfilename, "Can't determine values for ENHTYPE from database, added by script", Constants::LOG_INFO);

		$enhType = new EnhType();
		$enhType->ET_KODE = "ORG";
		$enhType->ET_UNDEREN = null; // Highest level
		$enhType->ET_BETEGN = "Organisasjon";
		$this->writeToDestination($enhType);
		$this->logger->log($this->XMLfilename, "Added ENHTYPE.ET.KODE with value ORG", Constants::LOG_INFO);

		$enhType = new EnhType();
		$enhType->ET_KODE = "AVD";
		$enhType->ET_UNDEREN = "ORG";
		$enhType->ET_BETEGN = "Avdeling";
		$this->writeToDestination($enhType);
		$this->logger->log($this->XMLfilename, "Added ENHTYPE.ET.KODE with value AVD", Constants::LOG_INFO);

		$enhType = new EnhType();
		$enhType->ET_KODE = "SEK";
		$enhType->ET_UNDEREN = "AVD";
		$enhType->ET_BETEGN = "Seksjon";
		$this->writeToDestination($enhType);
		$this->logger->log($this->XMLfilename, "Added ENHTYPE.ET.KODE with value SEK", Constants::LOG_INFO);
	}

  	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO ENHTYPE (ET_KODE, ET_UNDEREN, ET_BETEGN) VALUES (";

		$sqlInsertStatement .= "'" . $data->ET_KODE . "',";
		$sqlInsertStatement .= "'" . $data->ET_UNDEREN . "',";
		$sqlInsertStatement .= "'" . $data->ET_BETEGN . "'";
		
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	}

  function createXML($extractor) {
    	$sqlQuery = "SELECT * FROM ENHTYPE";
    	$mapping = array ('idColumn' => 'et_kode', 
  				'rootTag' => 'ENHETSTYPE.TAB',	
			    		'rowTag' => 'ENHETSTYPE',
  						'encoder' => 'utf8_decode',
							'elements' => array(
								'ET.KODE' => 'et_kode',
								'ET.UNDEREN' => 'et_underen',
								'ET.BETEGN' => 'et_betegn'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			