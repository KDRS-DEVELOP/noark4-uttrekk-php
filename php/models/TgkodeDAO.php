<?php

require_once 'models/Tgkode.php';
require_once 'utility/Utility.php';
require_once 'models/Noark4Base.php';

class TgkodeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
		parent::__construct (Constants::getXMLFilename('TGKODE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select UNNTOFF, BESKRIVELSE, SERIE, EPOSTNIV, FRADATO, TILDATO from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {

		$tgKode = new Tgkode();
		$tgKode->TK_TGKODE = 'F';
		$tgKode->TK_BETEGN = 'Ofl. §5a, Beskyttelsesinstruksen';
		$tgKode->TK_EPOSTNIV = '4';		
		$this->writeToDestination($tgKode);
		$this->logger->log($this->XMLfilename, "Added missing TGKODE TK_TGKODE(" . $tgKode->TK_TGKODE . ")", Constants::LOG_INFO);
		$this->infoIssued = true;

		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$tgKode = new Tgkode();
				if (strcmp($result['UNNTOFF'], ' ') != 0) {
					$tgKode->TK_TGKODE = $result['UNNTOFF'];
					$tgKode->TK_BETEGN = $result['BESKRIVELSE'];
					$tgKode->TK_SERIE = $result['SERIE'];
					$tgKode->TK_EPOSTNIV = $result['EPOSTNIV'];
					$tgKode->TK_FRADATO = Utility::fixDateFormat($result['FRADATO']);
					$tgKode->TK_TILDATO = Utility::fixDateFormat($result['TILDATO']);
					
					$this->writeToDestination($tgKode);
				}			
				else {
					$this->logger->log($this->XMLfilename, "TK.TGKODE value is null. null is used where TGKODE is not set. This value has been ignored.", Constants::LOG_WARNING);
 					$this->warningIssued = true;

				}
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO TGKODE (TK_TGKODE, TK_BETEGN, TK_SERIE, TK_EPOSTNIV, TK_FRADATO, TK_TILDATO) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->TK_TGKODE . "', ";						
		$sqlInsertStatement .= "'" . $data->TK_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->TK_SERIE . "', ";
		$sqlInsertStatement .= "'" . $data->TK_EPOSTNIV . "', ";
		$sqlInsertStatement .= "'" . $data->TK_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->TK_TILDATO . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Known duplicate value detected. Value is " . $data->TK_TGKODE, Constants::LOG_WARNING);
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail = false;
    }
	
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM TGKODE";
    	$mapping = array ('idColumn' => 'tk_tgkode', 
  				'rootTag' => 'TGKODE.TAB',	
			    		'rowTag' => 'TGKODE',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'TK.TGKODE' => 'tk_tgkode',
							'TK.BETEGN' => 'tk_betegn',
							'TK.SERIE' => 'tk_serie',
							'TK.FRADATO' => 'tk_fradato',
							'TK.TILDATO' => 'tk_tildato',
							'TK.EPOSTNIV' => 'tk_epostniv'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			