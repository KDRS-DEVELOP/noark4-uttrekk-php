<?php

require_once 'models/Tghjem.php';
require_once 'models/Noark4Base.php';

class TghjemDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
		parent::__construct (Constants::getXMLFilename('TGHJEM'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select UNNTOFF, HJEMMEL, AVGRADER, AGDAGER, BESKRIVELSE, AGAAR from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {

//		$tgHjem = new Tghjem();
//		$tgHjem->TH_TGKODE = '4'; // UNNTOFF
//		$tgHjem->TH_UOFF = 'Ofl §4'; //HJEMMEL
//		$tgHjem->TH_AGDAGER = 'U';
//		$tgHjem->TH_ANVEND = 'Unntatt offentlig ofl §4'; // BESKRIVELSE
		
//		$this->writeToDestination($tgHjem);
//		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);
		$this->infoIssued = true;

		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = '5'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Ofl §5'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Unntatt offentlig ofl §5'; // BESKRIVELSE		
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);

		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = '5a'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Ofl §5a'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Unntatt offentlig ofl §5a'; // BESKRIVELSE
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);

		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = '6'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Ofl §6'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Unntatt offentlig ofl §6'; // BESKRIVELSE		
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);
/*
		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = 'EI'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Off.l. §13-15'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Må sjekkes'; // BESKRIVELSE		
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);
*/
		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = '11'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Ofl §11'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Unntatt offentlig ofl §11'; // BESKRIVELSE
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);
/*
		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = 'B'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Ofl §6.1 sikk.l §11d.'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Begrenset etter sikkerhetsinstruksen'; // BESKRIVELSE
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);
*/
		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = 'F'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Ofl §6.1 sikk.l §11d.'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Fortrolig etter sikkerhetsinstruksen'; // BESKRIVELSE
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);
/*
		$tgHjem = new Tghjem();
		$tgHjem->TH_TGKODE = 'U'; // UNNTOFF
		$tgHjem->TH_UOFF = 'Utgar'; //HJEMMEL
		$tgHjem->TH_AGDAGER = 'U';
		$tgHjem->TH_ANVEND = 'Utgår'; // BESKRIVELSE
		$this->writeToDestination($tgHjem);
		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);
*/
//		$tgHjem = new Tghjem();
//		$tgHjem->TH_TGKODE = ''; // UNNTOFF
//		$tgHjem->TH_UOFF = ''; //HJEMMEL
//		$tgHjem->TH_AGDAGER = 'U';
//		$tgHjem->TH_ANVEND = ''; // BESKRIVELSE
//		
//		$this->writeToDestination($tgHjem);
//		$this->logger->log($this->XMLfilename, "Added missing TGHJEMMEL TGKODE(" . $tgHjem->TH_TGKODE . ") TH_UOFF (" . $tgHjem->TH_UOFF . ")", Constants::LOG_INFO);

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
					'rootTag' => 'TGHJEMMEL.TAB',	
						'rowTag' => 'TGHJEMMEL',
							'encoder' => 'utf8_decode',
							'elements' => array(
								'TH.TGKODE' => 'th_tgkode',
								'TH.UOFF' => 'th_uoff',
								'TH.AGKODE' => 'th_agkode',
								'TH.AGAAR' => 'th_agaar',
								'TH.AGDAGER' => 'th_agdager',
								'TH.ANVEND' => 'th_anvend',
								) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
 }
	