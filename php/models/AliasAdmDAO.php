<?php

require_once 'models/AliasAdm.php';
require_once 'models/Noark4Base.php';

class AliasAdmDAO extends Noark4Base {
	
	public function  __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ALIASADM'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select FRAORGENHET, TILORGENHET, MERKNAD FROM " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

				$aliasAdm = new AliasAdm();
				$aliasAdm->AL_ADMIDFRA = $result['FRAORGENHET'];
				$aliasAdm->AL_ADMIDTIL = $result['TILORGENHET'];
				$aliasAdm->AL_MERKNAD = $result['MERKNAD'];

				$this->writeToDestination($aliasAdm);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO ALIASADM (AL_ADMIDFRA, AL_ADMIDTIL, AL_MERKNAD) VALUES (";

		$sqlInsertStatement .= "'" . $data->AL_ADMIDFRA . "',";
		$sqlInsertStatement .= "'" . $data->AL_ADMIDTIL . "',";
		$sqlInsertStatement .= "'" . $data->AL_MERKNAD . "'";

		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }

  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ALIASADM";
    	$mapping = array ('idColumn' => 'al_admidfra', 
  				'rootTag' => 'ALIASADMENH.TAB',	
			    		'rowTag' => 'ALIASADMENH',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'AL.ADMIDFRA' => 'al_admidfra',
							'AL.ADMIDTIL' => 'al_admidtil',
							'AL.MERKNAD' => 'al_merknad'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	
    } 
 }