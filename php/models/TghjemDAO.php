<?php

require_once 'models/Tghjem.php';
require_once 'models/Noark4Base.php';

class TghjemDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
		parent::__construct (Constants::getXMLFilename('TGHJEM'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select UNNTOFF, HJEMMEL, AVGRADER, AGDAGER, BESKRIVELSE, AGAAR from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$tgHjem = new Tghjem();
				$tgHjem->TH_TGKODE = $result['UNNTOFF'];
				$tgHjem->TH_UOFF = $result['HJEMMEL'];
				$tgHjem->TH_AGKODE = $result['AVGRADER'];
				$tgHjem->TH_AGDAGER = $result['AGDAGER'];
				$tgHjem->TH_ANVEND = $result['BESKRIVELSE'];
				$tgHjem->TH_AGAAR = $result['AGAAR'];
				
				$this->writeToDestination($tgHjem);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO TGHJEM (TH_TGKODE, TH_UOFF, TH_AGKODE, TH_AGDAGER, TH_ANVEND, TH_AGAAR) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->TH_TGKODE . "', ";						
		$sqlInsertStatement .= "'" . $data->TH_UOFF . "', ";
		$sqlInsertStatement .= "'" . $data->TH_AGKODE . "', ";
		$sqlInsertStatement .= "'" . $data->TH_AGDAGER . "', ";
		$sqlInsertStatement .= "'" . $data->TH_ANVEND . "', ";
		$sqlInsertStatement .= "'" . $data->TH_AGAAR . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
	

	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM TGHJEM";
		$mapping = array ('idColumn' => 'th_tgkode', 
					'rootTag' => 'TGHJEM.TAB',	
						'rowTag' => 'TGHJEM',
							'encoder' => 'utf8_decode',
							'elements' => array(
								'TH.TGKODE' => 'th_tgkode',
								'TH.UOFF' => 'th_uoff',
								'TH.AGKODE' => 'th_agkode',
								'TH.AGDAGER' => 'th_agdager',
								'TH.ANVEND' => 'th_anvend',
								'TH.AGAAR' => 'th_agaar'
								) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }
	