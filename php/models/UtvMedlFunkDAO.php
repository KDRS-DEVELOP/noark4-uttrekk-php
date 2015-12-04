<?php

require_once 'models/UtvMedlFunk.php';
require_once 'models/Noark4Base.php';

class UtvMedlFunkDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVMEDLF'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select FUNKSJON, BESKRIVELSE, TALERETT, STEMMERETT, SEKR, INNKALLES FROM " . $SRC_TABLE_NAME . "";		
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$utvMedlFunk = new UtvMedlFunk();
				$utvMedlFunk->MK_KODE = $result['FUNKSJON'];
				$utvMedlFunk->MK_BETEGN = $result['BESKRIVELSE'];
				$utvMedlFunk->MK_TALE = $result['TALERETT'];
				$utvMedlFunk->MK_MEDLEM = $result['STEMMERETT'];				
				$utvMedlFunk->MK_SEKR = $result['SEKR'];
				$utvMedlFunk->MK_FMKODE = $result['INNKALLES'];

				$this->logger->log($this->XMLfilename, "Unsure if MK_FMKODE maps from ESA field INNKALLES for " . $utvMedlFunk->MK_KODE, Constants::LOG_INFO);				
				$this->infoIssued = true;
				$this->writeToDestination($utvMedlFunk);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO UTVMEDLF (MK_KODE, MK_BETEGN, MK_TALE, MK_MEDLEM, MK_SEKR, MK_FMKODE) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->MK_KODE . "', ";						
		$sqlInsertStatement .= "'" . $data->MK_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->MK_TALE . "', ";
		$sqlInsertStatement .= "'" . $data->MK_MEDLEM . "', ";
		$sqlInsertStatement .= "'" . $data->MK_SEKR . "', ";
		$sqlInsertStatement .= "'" . $data->MK_FMKODE . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
 
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM UTVMEDLF";
    	$mapping = array ('idColumn' => 'mk_kode', 
  				'rootTag' => 'UTVMEDLFUNK.TAB',	
			    		'rowTag' => 'UTVMEDLFUNK',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'MK.KODE' => 'mk_kode',
							'MK.BETEGN' => 'mk_betegn',
							'MK.TALE' => 'mk_tale',
							'MK.MEDLEM' => 'mk_medlem',
							'MK.SEKR' => 'mk_sekr',
							'MK.FMKODE' => 'mk_fmkode'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			