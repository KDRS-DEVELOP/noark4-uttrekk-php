<?php

require_once 'models/SakType.php';
require_once 'models/Noark4Base.php';

class SakTypeDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('SAKTYPE'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select SAKTYPE, SAKART, BESKRIVELSE, KLAGEADG from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		$sakType = new SakType();
		$sakType->ST_TYPE = "ENK-ETTBYG";
		$sakType->ST_BETEGN = "Enkel ett-trinnsbygesak";
		$sakType->ST_KLAGEADG= "0";				
		$sakType->ST_UOFF = "0";
		$this->logger->log($this->XMLfilename, "Known SAKTYPE missing from database. Adding ST_TYPE, ST_BETEGN, ST_KLAGEADG, ST_UOFF values (" . $sakType->ST_TYPE . ", " . $sakType->ST_BETEGN . ", " . $sakType->ST_KLAGEADG . ", " . $sakType->ST_UOFF . "). Originally SAKTYPE/SAKART(ENK-ETT BYG)", Constants::LOG_WARNING);
		$this->writeToDestination($sakType);
			
		$sakType = new SakType();
		$sakType->ST_TYPE = "GRJDEL";
		$sakType->ST_BETEGN = "Grensejustering delingssak";
		$sakType->ST_KLAGEADG= "0";				
		$sakType->ST_UOFF = "0";
		$this->logger->log($this->XMLfilename, "KnowN SAKTYPE missing from database. Adding ST_TYPE, ST_BETEGN, ST_KLAGEADG, ST_UOFF values (" . $sakType->ST_TYPE . ", " . $sakType->ST_BETEGN . ", " . $sakType->ST_KLAGEADG . ", " . $sakType->ST_UOFF . "). Originally SAKTYPE/SAKART (GRJ DEL)", Constants::LOG_WARNING);
		$this->writeToDestination($sakType);

		$sakType = new SakType();
		$sakType->ST_TYPE = "KRTDEL";
		$sakType->ST_BETEGN = "Grensejustering delingssak";
		$sakType->ST_KLAGEADG= "0";				
		$sakType->ST_UOFF = "0";
		$this->logger->log($this->XMLfilename, "KnowN SAKTYPE missing from database. Adding ST_TYPE, ST_BETEGN, ST_KLAGEADG, ST_UOFF values (" . $sakType->ST_TYPE . ", " . $sakType->ST_BETEGN . ", " . $sakType->ST_KLAGEADG . ", " . $sakType->ST_UOFF . ") Originally SAKTYPE/SAKART (KRT DEL)", Constants::LOG_WARNING);
		$this->writeToDestination($sakType);

		$sakType = new SakType();
		$sakType->ST_TYPE = "MELDINGBYG";
		$sakType->ST_BETEGN = "Meldingssak bygg";
		$sakType->ST_KLAGEADG= "0";				
		$sakType->ST_UOFF = "0";
		$this->logger->log($this->XMLfilename, "KnowN SAKTYPE missing from database. Adding ST_TYPE, ST_BETEGN, ST_KLAGEADG, ST_UOFF values (" . $sakType->ST_TYPE . ", " . $sakType->ST_BETEGN . ", " . $sakType->ST_KLAGEADG . ", " . $sakType->ST_UOFF . ") Originally SAKTYPE/SAKART (MELDING BYG)", Constants::LOG_WARNING);
		$this->writeToDestination($sakType);

		$sakType = new SakType();
		$sakType->ST_TYPE = "TOTRINNBYG";
		$sakType->ST_BETEGN = "To-trinnsbygesak";
		$sakType->ST_KLAGEADG= "0";				
		$sakType->ST_UOFF = "0";
		$this->logger->log($this->XMLfilename, "KnowN SAKTYPE missing from database. Adding ST_TYPE, ST_BETEGN, ST_KLAGEADG, ST_UOFF values (" . $sakType->ST_TYPE . ", " . $sakType->ST_BETEGN . ", " . $sakType->ST_KLAGEADG . ", " . $sakType->ST_UOFF . ") Originally SAKTYPE/SAKART (TOTRINN  BYG)", Constants::LOG_WARNING);
		$this->writeToDestination($sakType);


		$this->warningIssued = true;

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$sakType = new SakType();
				$sakType->ST_TYPE = substr($result['SAKTYPE'] . $result['SAKART'], 0, 10);
				$sakType->ST_BETEGN = $result['BESKRIVELSE'];

				$sakType->ST_KLAGEADG= $result['KLAGEADG'];				
				if (is_null($result['KLAGEADG'])) {
					$sakType->ST_KLAGEADG = '0';
					$this->logger->log($this->XMLfilename, "Assuming NULL value for ST_KLAGEADG represents 0 for SAKTYPE (" . $sakType->ST_TYPE . ")", Constants::LOG_WARNING);
				}	

				$sakType->ST_UOFF = "0";
				$this->logger->log($this->XMLfilename, "Assigning 0 to ST_UTOFF as field is not in src database" . $sakType->ST_TYPE, Constants::LOG_WARNING);
				$this->warningIssued = true;

				$this->writeToDestination($sakType);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO SAKTYPE (ST_TYPE, ST_BETEGN, ST_KLAGEADG, ST_UOFF) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->ST_TYPE . "', ";
		$sqlInsertStatement .= "'" . $data->ST_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->ST_KLAGEADG . "', ";
		$sqlInsertStatement .= "'" . $data->ST_UOFF . "'";		
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->printErrorIfDuplicateFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table i	s know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Known duplicate value detected. Value is " . $data->ST_TYPE, Constants::LOG_WARNING);
			}
		}
		$this->uttrekksBase->printErrorIfDuplicateFail = true;
    }  

	
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM SAKTYPE";
    	$mapping = array ('idColumn' => 'st_type', 
  				'rootTag' => 'SAKSTYPE.TAB',	
			    		'rowTag' => 'SAKSTYPE',
  						'encoder' => 'utf8_decode',
							'elements' => array(
									'ST.TYPE' => 'st_type',
									'ST.BETEGN' => 'st_betegn',
									'ST.UOFF' => 'st_uoff',
									'ST.KLAGEADG' => 'st_klageadg'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			