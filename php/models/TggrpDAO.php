<?php

require_once 'models/Tggrp.php';
require_once 'utility/Utility.php';
require_once 'utility/Constants.php';
require_once 'models/Noark4Base.php';

class TggrpDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
		parent::__construct (Constants::getXMLFilename('TGGRP'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select FULLTNAVN, GRUPPEID, OPPRAVID, FRADATO, TILDATO, CTYPE from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$tggrp = new Tggrp();
				$tggrp->TG_GRUPPNAVN = $result['FULLTNAVN'];
				$tggrp->TG_GRUPPEID = $result['GRUPPEID'];

 
				if (is_null($result['OPPRAVID'])) {
					$tggrp->TG_OPPRAV =  Constants::INGENBRUKER_ID;
					$this->logger->log($this->XMLfilename, "TG_OPPRAV is null for TG_GRUPPNAVN (" . $tggrp->TG_GRUPPNAVN . ") setting it to unkown user " . Constants::INGENBRUKER_ID, Constants::LOG_WARNING);
				}
				else { 
					$tggrp->TG_OPPRAV = $result['OPPRAVID'];
				}

				if (strcmp($result['CTYPE'], "T") == 0)
					$tggrp->TG_GENERELL = '1';
				else 
					$tggrp->TG_GENERELL = '0';

				$this->logger->log($this->XMLfilename, "TG_GENERELL is missing for TG_GRUPPNAVN (" . $tggrp->TG_GRUPPNAVN . ") assumed a value from CTYPE field (" . $result['CTYPE'].  ") setting TG_GENERELL  to " . $tggrp->TG_GENERELL , Constants::LOG_WARNING);


				if (is_null($result['FRADATO'])){
					$this->logger->log($this->XMLfilename, "TG_FRADATO is null for TG_GRUPPNAVN (" . $tggrp->TG_GRUPPNAVN . ") setting it to unkown date " . Constants::DATE_AUTO_START, Constants::LOG_WARNING);
					$tggrp->TG_FRADATO = Utility::fixDateFormat(Constants::DATE_AUTO_START);
				}
				else {
					$tggrp->TG_FRADATO = Utility::fixDateFormat($result['FRADATO']);
				}						
				
				if (is_null($result['TILDATO'])){
					$this->logger->log($this->XMLfilename, "TG_TILDATO is null for TG_GRUPPNAVN (" . $tggrp->TG_GRUPPNAVN . ") setting it to unkown date " . Constants::DATE_AUTO_END, Constants::LOG_WARNING);
					$tggrp->TG_TILDATO = Utility::fixDateFormat(Constants::DATE_AUTO_END);
				}
				else {
					$tggrp->TG_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				}		
				
				$this->writeToDestination($tggrp);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
		
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO TGGRP (TG_GRUPPNAVN, TG_GENERELL, TG_GRUPPEID, TG_OPPRAV, TG_FRADATO, TG_TILDATO) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->TG_GRUPPNAVN . "', ";
		$sqlInsertStatement .= "'" . $data->TG_GENERELL  . "', ";
		$sqlInsertStatement .= "'" . $data->TG_GRUPPEID . "', ";
		$sqlInsertStatement .= "'" . $data->TG_OPPRAV . "', ";
		$sqlInsertStatement .= "'" . $data->TG_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->TG_TILDATO . "'";		
		
		$sqlInsertStatement.= ");";
	
			if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Duplicate value detected. Value is " . $data->OP_ORDNPRI, Constants::LOG_ERROR);
			}
		}


    }  

  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM TGGRP";
    	$mapping = array ('idColumn' => 'tg_gruppnavn', 
  				'rootTag' => 'TGGRUPPE.TAB',	
			    		'rowTag' => 'TGGRUPPE',
  						'encoder' => 'utf8_decode',
							'elements' => array(
								'TG.GRUPPEID' => 'tg_gruppeid',
								'TG.GRUPPNAVN' => 'tg_gruppnavn',
								'TG.GENERELL' => 'tg_generell',
								'TG.OPPRAV' => 'tg_opprav',
								'TG.FRADATO' => 'tg_fradato',
								'TG.TILDATO' => 'tg_tildato'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			