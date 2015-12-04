<?php

require_once 'models/Avskrm.php';
require_once 'models/Noark4Base.php';

class AvskrmDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('AVSKRM'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select AVSKRMET, BESKRIVELSE, AVSKRTYPE, BESVART from " . $SRC_TABLE_NAME . "";
		$this->logger=$logger;
	} 
	
	function processTable () {

		$avskrm = new Avskrm();
		$avskrm->AV_KODE = 'SA';
		$avskrm->AV_BETEGN = 'Saken Avsluttet';
		$avskrm->AV_MIDLERTID = "0";
		$avskrm->AV_BESVART = '0';

		$this->logger->log($this->XMLfilename, "'SA/Saken Avsluttet' pair missing. Added here", Constants::LOG_INFO);
		$this->infoIssued = true;
		$this->writeToDestination($avskrm);

		$avskrm  = null;

		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$avskrm = new Avskrm();
				$avskrm->AV_KODE = $result['AVSKRMET'];
				$avskrm->AV_BETEGN = $result['BESKRIVELSE'];


				if (strcmp($result['AVSKRTYPE'], "A") == 0) {
					$avskrm->AV_MIDLERTID = "0";
					$this->logger->log($this->XMLfilename, "Assuming AV_MIDLERTID (AVSKRTYPE) value A is 0", Constants::LOG_INFO);
					$this->infoIssued = true; 
				}
				else if (strcmp($result['AVSKRTYPE'], "F") == 0) {
					$avskrm->AV_MIDLERTID = "1";
					$this->logger->log($this->XMLfilename, "Assuming AV_MIDLERTID (AVSKRTYPE) value F is 1", Constants::LOG_INFO);
					$this->infoIssued = true; 
				}
				else {
					$avskrm->AV_MIDLERTID = "0";
					$this->logger->log($this->XMLfilename, "One of AV_KODE (" . $avskrm->AV_KODE. ") has null value for AV_MIDLERTID. Setting it to 0", Constants::LOG_WARNING);
					$this->warningIssued = true;

				}

				if (isset($result['BESVART']) == false) {	
					$avskrm->AV_BESVART = '0';
					$this->logger->log($this->XMLfilename, "Assuming AV_BESVART (BESVART) null value is 0", Constants::LOG_INFO);
					$this->infoIssued = true;
				}
				else {
					$avskrm->AV_BESVART = $result['BESVART'];
				}

				$this->writeToDestination($avskrm);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
			
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO AVSKRM (AV_KODE, AV_BETEGN, AV_MIDLERTID, AV_BESVART) VALUES (";

		$sqlInsertStatement .= "'" . $data->AV_KODE . "', ";
		$sqlInsertStatement .= "'" . $data-> AV_BETEGN. "', ";			
		$sqlInsertStatement .= "'" . $data->AV_MIDLERTID . "', ";
		$sqlInsertStatement .= "'" . $data->AV_BESVART . "'";			
	
		$sqlInsertStatement.= ");";

		$this->uttrekksBase->printErrorIfDuplicateFail = false;	
		
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Duplicate value detected. Value is AV_KODE (" . $data->AV_KODE . "), AV_BETEGN (" . $data->AV_BETEGN . ")", Constants::LOG_WARNING);
			}
		}
  		$this->uttrekksBase->printErrorIfDuplicateFail = true;
		
		
    }
	
  	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM AVSKRM";
		$mapping = array ('idColumn' => 'av_kode', 
					'rootTag' => 'AVSKRMAATE.TAB',	
						'rowTag' => 'AVSKRMAATE',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'AV.KODE' => 'av_kode',
									'AV.BETEGN' => 'av_betegn',
									'AV.MIDLERTID' => 'av_midlertid',
									'AV.BESVART' => 'av_besvart'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			