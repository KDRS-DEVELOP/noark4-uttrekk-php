<?php

require_once 'models/UtvBehStat.php';
require_once 'models/Noark4Base.php';

class UtvBehStatDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVBEHSTAT'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select STATUS, BESKRIVELSE, KOLISTE, SAKSKART, BSTATUS from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		$this->logger->log($this->XMLfilename, "Required information missing from ESA when processing table", Constants::LOG_INFO);
 		$this->logger->log($this->XMLfilename, "KANSKART har verdi 1 dersom saker med denn status kan settes på sakskartet, 0 ellers. Default everything to 0" ,Constants::LOG_INFO);
 		$this->logger->log($this->XMLfilename, "BS.BEHANDLET har verdi 1 dersom denne behandlingsststatusen at saken har vært oppe til behandling ...see N4 documentation for info. Default everything to 0" ,Constants::LOG_INFO);	
 		$this->logger->log($this->XMLfilename, "BS.SORT1  rekkefølge saksframlegg skal sorteres. Set default to 123", Constants::LOG_INFO);		
 		$this->infoIssued = true;

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$utvBehStat = new UtvBehStat();
				$utvBehStat->BS_STATUS = $result['STATUS'];
				$utvBehStat->BS_BETEGN = $result['BESKRIVELSE'];
				$utvBehStat->BS_KOLISTE = $result['KOLISTE'];
				$utvBehStat->BS_SAKSKART = $result['SAKSKART'];
				$utvBehStat->BS_BEHANDLET = '0';
				$this->logger->log($this->XMLfilename, "Required information missing BS.BEHANDLET set to 0" , Constants::LOG_WARNING);
				
				$utvBehStat->BS_SORT1 = '123';
				$this->logger->log($this->XMLfilename, "Required information missing BS.SORT1 set to 0" , Constants::LOG_WARNING);

				$utvBehStat->BS_KANSKART = '0';
				$this->logger->log($this->XMLfilename, "Required information missing BS.KANSKART set to 0" , Constants::LOG_WARNING);
				$this->warningIssued = true;
				$this->writeToDestination($utvBehStat);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
	  $sqlInsertStatement = "INSERT INTO UTVBEHSTAT (BS_STATUS, BS_BETEGN, BS_KOLISTE, BS_SAKSKART , BS_KANSKART, BS_BEHANDLET, BS_SORT1) VALUES (";
		
		$sqlInsertStatement .= "'" . $data->BS_STATUS . "', ";						
		$sqlInsertStatement .= "'" . $data->BS_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->BS_KOLISTE . "', ";
		$sqlInsertStatement .= "'" . $data->BS_SAKSKART . "', ";						
		$sqlInsertStatement .= "'" . $data->BS_KANSKART . "', ";
		$sqlInsertStatement .= "'" . $data->BS_BEHANDLET . "', ";
		$sqlInsertStatement .= "'" . $data->BS_SORT1 . "' ";						
		
		$sqlInsertStatement .= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }


  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM UTVBEHSTAT";
    	$mapping = array ('idColumn' => 'bs_status', 
  				'rootTag' => 'UTVBEHSTAT.TAB',	
			    		'rowTag' => 'UTVBEHSTAT',
					'fileName' => 'UTVBEHST',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'BS.STATUS' => 'bs_status',
							'BS.BETEGN' => 'bs_betegn',
							'BS.KOLISTE' => 'bs_koliste',
							'BS.KANSKART' => 'bs_kanskart',
							'BS.SAKSKART' => 'bs_sakskart',
							'BS.BEHANDLET' => 'bs_behandlet',
							'BS.SORT1' => 'bs_sort1'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }