<?php

require_once 'models/Klass.php';
require_once 'models/Noark4Base.php';

class KlassDAO  extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('KLASS'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select JOURAARNR, U1, EKODE1, EKODE2, EKODE3, FELLESK, FAGK, TILLEGSK, OTYPE, OKODE1 from " . $SRC_TABLE_NAME . "";
	} 
	
	
	// In this code base we set ORDNPRI to A1, A2 or FA. Maybe also TILLEGSKODE should be used
	// This should really be done as part of the processNoark table. Here because I need to see the values.	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
	
				$klass = new Klass();
			if (isset($result['U1']) == false) {
				$klass->KL_U1 = '0';
				$this->logger->log($this->XMLfilename, "For KL_SAID (" . $klass->KL_SAID  . "), KL_U1 is null. Seeting it to (" . $klass->KL_U1. ")", Constants::LOG_WARNING);

			} else {
				$klass->KL_U1 = $result['U1'];
			}		

			if (isset($result['OKODE1']) == true) {

				$klass->KL_SAID = $result['JOURAARNR'];
				$klass->KL_SORT = '1';
				$klass->KL_ORDNPRI = $result['OTYPE'];;
				$klass->KL_ORDNVER = $result['OKODE1'];
//				$klass->KL_U1 = $result['U1'];				
				$this->writeToDestination($klass);
			}
			if (isset($result['EKODE1']) == true) {
//				$klass = new Klass();
				$klass->KL_SAID = $result['JOURAARNR'];
				$klass->KL_SORT = '1';
				$klass->KL_ORDNPRI = 'A1';
				$klass->KL_ORDNVER = $result['EKODE1'];
				//$klass->KL_U1 = $result['U1'];
				
				$this->writeToDestination($klass);
			}
			if (isset($result['EKODE2']) == true) {
//				$klass = new Klass();
				$klass->KL_SAID = $result['JOURAARNR'];
				$klass->KL_SORT = '1';
				$klass->KL_ORDNPRI = 'A2';
				$klass->KL_ORDNVER = $result['EKODE2'];
				//$klass->KL_U1 = $result['U1'];
				
				$this->writeToDestination($klass);
			}
			if (isset($result['EKODE3']) == true) {
//				$klass = new Klass();
				$klass->KL_SAID = $result['JOURAARNR'];
				$klass->KL_SORT = '1';
				$klass->KL_ORDNPRI = 'FA';
				$klass->KL_ORDNVER = $result['EKODE2'];
				//$klass->KL_U1 = $result['U1'];
				
				$this->writeToDestination($klass);
			}
			

	
			
		}
		$this->srcBase->endQuery($this->selectQuery);	
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO KLASS (KL_SAID, KL_SORT, KL_ORDNPRI, KL_ORDNVER, KL_U1) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->KL_SAID . "', ";
		$sqlInsertStatement .= "'" . $data->KL_SORT . "', ";
		$sqlInsertStatement .= "'" . $data->KL_ORDNPRI . "', ";
		$sqlInsertStatement .= "'" . mysql_real_escape_string($data->KL_ORDNVER) . "', ";
		$sqlInsertStatement .= "'" . $data->KL_U1 . "'";
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	} 
	
	function createXML($extractor) {
		$sqlQuery = "SELECT * FROM KLASS";
		$mapping = array ('idColumn' => 'kl_said',
  				'rootTag' => 'KLASSERING.TAB',	
			    		'rowTag' => 'KLASSERING',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'KL.SAID' => 'kl_said',
							'KL.SORT' => 'kl_sort',
							'KL.ORDNPRI' => 'kl_ordnpri',
							'KL.ORDNVER' => 'kl_ordnver',
							'KL.U1' => 'kl_u1'
							)
						) ;

		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
							 
	}
}
	