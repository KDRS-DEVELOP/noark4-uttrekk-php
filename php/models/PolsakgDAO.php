<?php

require_once 'models/Polsakg.php';
require_once 'models/Noark4Base.php';

class PolsakgDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('POLSAKG'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select JOURAARNR, BEHTYPE, KLAGE, LUKKET, HJEMMEL, AAPNET, AVSLUTTET, MERKNAD FROM " . $SRC_TABLE_NAME . " WHERE BEHTYPE = 'PS'";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

				$polSakG = new Polsakg();
				$polSakG->SG_ID = $result['JOURAARNR'];
				$polSakG->SG_SAID = $result['JOURAARNR'];
				$polSakG->SG_SAKSTYPE = $result['BEHTYPE'];

				if (is_null($result['KLAGE'])) {
					$polSakG->SG_KLADGANG = '0';
					$this->logger->log($this->XMLfilename, "SG_KLADGANG is null, assuming 0 (åpen) for POLSAKG with SAID " . $polSakG->SG_SAID, Constants::LOG_INFO);
				}
				else if (strcmp($result['KLAGE'], 'J')) {
					$polSakG->SG_KLADGANG = '1';
				}
				else if (strcmp($result['KLAGE'], 'N')) {
					$polSakG->SG_KLADGANG = '0';
				}
				else {
					$polSakG->SG_KLADGANG = '0';
					$this->logger->log($this->XMLfilename, "SG_KLADGANG has unknown value, assuming 0 (åpen) for POLSAKG with SAID " . $polSakG->SG_SAID . " Unknown value is " . $result['KLAGE'], Constants::LOG_ERROR );

				}

				$polSakG->SG_LUKKET = $result['LUKKET'];

				if (is_null($result['LUKKET'])) {
					$polSakG->SG_LUKKET = '0';
					$this->logger->log($this->XMLfilename, "SG_LUKKET is null, assuming 0 (åpen) for POLSAKG with SAID " . $polSakG->SG_SAID, Constants::LOG_INFO);

					if (is_null($result['HJEMMEL'] == false)) {
						$polSakG->SG_LUKKET = '1';
						$this->logger->log($this->XMLfilename, "SG_LUKKET is null, but has UOFF value, assigning to 1 for POLSAKG with SAID " . $polSakG->SG_SAID, Constants::LOG_INFO);
					}
				}
				else if (strcmp($result['LUKKET'], 'Å')) {
					$polSakG->SG_LUKKET = '0';
				}
				else if (strcmp($result['LUKKET'], 'L')) {
					$polSakG->SG_LUKKET = '1';
				}
				else {
					$polSakG->SG_LUKKET = '0';
					$this->logger->log($this->XMLfilename, "SG_LUKKET has unknown value, assuming 0 (åpen) for POLSAKG with SAID " . $polSakG->SG_SAID . " Unknown value is " . $result['LUKKET'], Constants::LOG_ERROR );

				}

				$polSakG->SG_UOFF = $result['HJEMMEL'];
				$polSakG->SG_STARTDATO = Utility::fixDateFormat($result['AAPNET']);
				$polSakG->SG_VEDTDATO = Utility::fixDateFormat($result['AVSLUTTET']);
				//$polSakG->SG_SISTEVEDT = Utility::fixDateFormat($result['AVSLUTTET']);
				$polSakG->SG_MERKNAD = $result['MERKNAD'];

				$this->writeToDestination($polSakG);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	


	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO  POLSAKG (SG_ID, SG_SAID, SG_SAKSTYPE, SG_KLADGANG, SG_LUKKET, SG_UOFF, SG_STARTDATO, SG_VEDTDATO, SG_SISTEVEDT, SG_MERKNAD) VALUES (";
		$sqlInsertStatement .= "'" . $data->SG_ID . "', ";
		$sqlInsertStatement .= "'" . $data->SG_SAID . "', ";
		$sqlInsertStatement .= "'" . $data->SG_SAKSTYPE . "', ";
		$sqlInsertStatement .= "'" . $data->SG_KLADGANG . "', ";
		$sqlInsertStatement .= "'" . $data->SG_LUKKET . "', ";
		$sqlInsertStatement .= "'" . $data->SG_UOFF . "', ";
		$sqlInsertStatement .= "'" . $data->SG_STARTDATO . "', ";
		$sqlInsertStatement .= "'" . $data->SG_VEDTDATO . "', ";
		$sqlInsertStatement .= "'" . $data->SG_SISTEVEDT . "', ";
		$sqlInsertStatement .= "'" . mysql_real_escape_string($data->SG_MERKNAD) . "' ";
		
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }

 
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM POLSAKG";
    	$mapping = array ('idColumn' => 'sg_id', 
  				'rootTag' => 'POLSAKSGANG.TAB',	
			    		'rowTag' => 'POLSAKSGANG',
  						'encoder' => 'utf8_decode',
  							'elements' => array(
								'SG.ID' => 'sg_id',
								'SG.SAID' => 'sg_said',
								'SG.SAKSTYPE' => 'sg_sakstype',
								'SG.KLADGANG' => 'sg_kladgang',
								'SG.LUKKET' => 'sg_lukket',
								'SG.UOFF' => 'sg_uoff',
								'SG.STARTDATO' => 'sg_startdato',
								'SG.VEDTDATO' => 'sg_vedtdato',
								'SG.SISTEVEDT' => 'sg_sistevedt',
								'SG.MERKNAD' => 'sg_merknad'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }    
 }
			