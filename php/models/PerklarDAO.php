<?php

require_once 'models/Perklar.php';
require_once 'models/Noark4Base.php';

class PerklarDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('PERKLAR'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {

		echo "ERROR!! this table is not working. Not sure where the data is!";
		return;
 
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$perKlar = new Perklar();
				$perKlar->KT_PEID = $result['']; 
				$perKlar->KT_TGKODE = $result[''];
				$perKlar->KT_AUTHELE = $result[''];
				$perKlar->KT_KLAV = $result[''];
				$perKlar->KT_FRADATO = $result[''];
				$this->writeToDestination($perKlar);
		}

	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO PERKLAR (KT_PEID, KT_TGKODE, KT_AUTHELE, KT_KLAV, KT_FRADATO) VALUES (";

		$sqlInsertStatement .= "'" . $data->KT_PEID . "', ";			
		$sqlInsertStatement .= "'" . $data->KT_TGKODE . "', ";
		$sqlInsertStatement .= "'" . $data->KT_AUTHELE . "', ";
		$sqlInsertStatement .= "'" . $data->KT_KLAV . "', ";
		$sqlInsertStatement .= "'" . $data->KT_FRADATO . "'";
		
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

   	}

	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM PERKLAR";
		$mapping = array ('idColumn' => 'kt_peid', 
					'rootTag' => 'PERKLARER.TAB',	
						'rowTag' => 'PERKLARER',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'KT.PEID' => 'kt_peid',
									'KT.TGKODE' => 'kt_tgkode',
									'KT.AUTHELE' => 'kt_authele',
									'KT.KLAV' => 'kt_klav',
									'KT.FRADATO' => 'kt_fradato'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
		
	}    
 }
