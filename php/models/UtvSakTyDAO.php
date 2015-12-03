<?php

require_once 'models/UtvSakTy.php';
require_once 'models/Noark4Base.php';

class UtvSakTyDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVSAKTY'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select KVEDTAK, BESKRIVELSE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$utvSakTy = new UtvSakTy();
				$utvSakTy->SU_KODE = $result['KVEDTAK'];
				$utvSakTy->SU_BETEGN = $result['BESKRIVELSE'];

				if (is_null ($result['KVEDTAK'])) { 
					$utvSakTy->SU_KODE = "IF";;
					$this->logger->log($this->XMLfilename, "null value for primary key SU.KODE ESA(DGJHBVEDTAK.SU_KODE)" . $utvSakTy->SU_BETEGN, Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
				
				$this->writeToDestination($utvSakTy);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO UTVSAKTY (SU_KODE, SU_BETEGN) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->SU_KODE . "', ";						
		$sqlInsertStatement .= "'" . $data->SU_BETEGN . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }

  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM UTVSAKTY";
    	$mapping = array ('idColumn' => 'su_kode', 
  				'rootTag' => 'UTVSAKTY.TAB',	
			    		'rowTag' => 'UTVSAKTY',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'SU_KODE' => 'su_kode',
							'SU_BETEGN' => 'su_betegn'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
