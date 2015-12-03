<?php

require_once 'models/Tginfo.php';
require_once 'utility/Constants.php';
require_once 'models/Noark4Base.php';

class TginfoDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
		parent::__construct (Constants::getXMLFilename('TGINFO'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select PEID, JOURENHET, ADMID, AUTAV, DATO, TILDATO, AUTOPPAV from " . $SRC_TABLE_NAME . "";
	} 
	

	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$tgInfo = new Tginfo();
				$tgInfo->TJ_PEID = $result['PEID'];
				$tgInfo->TJ_JENHET = $result['JOURENHET'];

				
				if (is_null($result['ADMID'])) {
					$tgInfo->TJ_ADMID = '0';
					$this->logger->log($this->XMLfilename, "Assuming NULL value for ADMID represents 0 ", Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
				else {
					$tgInfo->TJ_ADMID = $result['ADMID'];
				}
					
				$tgInfo->TJ_AUTAV = $result['AUTAV'];
				$tgInfo->TJ_FRADATO = $result['DATO'];
				$tgInfo->TJ_TILDATO = $result['TILDATO'];
				$tgInfo->TJ_AUTOPPAV = $result['AUTOPPAV'];

				$this->writeToDestination($tgInfo);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO TGINFO (TJ_PEID, TJ_JENHET, TJ_ADMID, TJ_AUTAV, TJ_FRADATO, TJ_TILDATO, TJ_AUTOPPAV) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->TJ_PEID . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_JENHET  . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_ADMID . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_AUTAV . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_FRADATO . "', ";		
		$sqlInsertStatement .= "'" . $data->TJ_TILDATO . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_AUTOPPAV . "' ";		


		$sqlInsertStatement.= ");";
	
   		$this->uttrekksBase->printErrorIfDuplicateFail = false;
                if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

                        if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
                                $this->logger->log($this->XMLfilename, "Duplicate TJ.PEID, TJ.JENHET, TJ.ADMID values (" . $data->TJ_PEID . "," . $data->TJ_JENHET . "," . $data->TJ_ADMID . ")", Constants::LOG_WARNING);
                                $this->warningIssued = true;
                        }
                }
                $this->uttrekksBase->printErrorIfDuplicateFail  = true;

    }  
	
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM TGINFO";
    	$mapping = array ('idColumn' => 'tj_peid', 
  				'rootTag' => 'TGINFO.TAB',	
			    		'rowTag' => 'TGINFO',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'TJ.PEID' => 'tj_peid',
							'TJ.JENHET' => 'tj_jenhet',
							'TJ.ADMID' => 'tj_admid',
							'TJ.AUTAV' => 'tj_autav',
							'TJ.FRADATO' => 'tj_fradato',
							'TJ.TILDATO' => 'tj_tildato',
							'TJ.AUTOPPAV' => 'tj_autoppav'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			