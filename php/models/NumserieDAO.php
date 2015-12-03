<?php

require_once 'models/NumSerie.php';
require_once 'models/Noark4Base.php';

class NumSerieDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('NUMSERIE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	
	// I don't know where this dta comes from or if it's in use so leaving it here for the moment
	// false data
	function processTable () {
		$numSerie= new NumSerie();
		
		$numSerie->NU_ID = "1";
		$numSerie->NU_BETEGN = "DUMMY";
		$numSerie->NU_SEKNR1 = "1";
		$numSerie->NU_SEKNR2 = "2";
		$numSerie->NU_AAR = "0";
		$numSerie->NU_AARAUTO = "1";
		
		$this->writeToDestination($numSerie);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO NUMSERIE (NU_ID, NU_BETEGN, NU_SEKNR1, NU_SEKNR2, NU_AAR, NU_AARAUTO) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->NU_ID . "', ";			
		$sqlInsertStatement .= "'" . $data->NU_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->NU_SEKNR1 . "', ";
		$sqlInsertStatement .= "'" . $data->NU_SEKNR2 . "', ";
		$sqlInsertStatement .= "'" . $data->NU_AAR . "', ";
		$sqlInsertStatement .= "'" . $data->NU_AARAUTO . "'";
		
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM NUMSERIE";
    	$mapping = array ('idColumn' => 'nu_id', 
  				'rootTag' => 'NUMSERIE.TAB',	
			    		'rowTag' => 'NUMSERIE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'NU.ID' => 'nu_id',
							'NU.BETEGN' => 'nu_betegn',
							'NU.AAR' => 'nu_aar',
							'NU.SEKNR1' => 'nu_seknr1',
							'NU.SEKNR2' => 'nu_seknr2',
							'NU.AARAUTO' => 'nu_aarauto'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			