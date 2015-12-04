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

				if (isset($result['JOURENHET']) == false) {
					$tgInfo->TJ_JENHET = Constants::JOURNENHET_MISSING;
					$this->logger->log($this->XMLfilename, "For TJ_PEID (" . $result['PEID'] . ") TJ_JENHET is null. Required value. Setting it to " . Constants::JOURNENHET_MISSING , Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
		
				
				if (isset($result['ADMID']) == false) {
					$tgInfo->TJ_ADMID = '0';
					$this->logger->log($this->XMLfilename, "For TJ_PEID (" . $result['PEID'] . ") assuming NULL value for ADMID represents 0 ", Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
				else {
					$tgInfo->TJ_ADMID = $result['ADMID'];
				}
					
				$tgInfo->TJ_AUTAV = $result['AUTAV'];

				if (isset($result['DATO']) == true) {
					$tgInfo->TJ_FRADATO = Utility::fixDateFormat($result['DATO']);
				} else {

					$tgInfo->TJ_FRADATO = "19981201";
					$this->logger->log($this->XMLfilename, "For TJ_PEID (" . $result['PEID'] . ") assuming missing value TJ_FRADATO is 19981201", Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
				$tgInfo->TJ_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				$tgInfo->TJ_AUTOPPAV = $result['AUTOPPAV'];

				$this->writeToDestination($tgInfo);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO TGINFO (TJ_PEID, TJ_JENHET, TJ_ADMID, TJ_AUTAV, TJ_FRADATO) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->TJ_PEID . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_JENHET  . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_ADMID . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_AUTAV . "', ";
		$sqlInsertStatement .= "'" . $data->TJ_FRADATO . "' ";		
//		$sqlInsertStatement .= "'" . $data->TJ_TILDATO . "', ";
//		$sqlInsertStatement .= "'" . $data->TJ_AUTOPPAV . "' ";		


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
							'TJ.FRADATO' => 'tj_fradato'
//							'TJ.TILDATO' => 'tj_tildato',
//							'TJ.AUTOPPAV' => 'tj_autoppav'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			