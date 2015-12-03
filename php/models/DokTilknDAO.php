<?php

require_once 'models/DokTilkn.php';
require_once 'models/Noark4Base.php';
require_once 'utility/Constants.php';

class DokTilknDAO extends Noark4Base {

	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('DOKTILKN'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {	

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'A'; 
		$dokTilkn->DT_BETEGN = 'Andre tilleggsdokument';
		$dokTilkn->DT_JOURNAL = '1'; 
		$dokTilkn->DT_MOTEDOK = '1';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (A, Andre tilleggsdokument) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'F'; 
		$dokTilkn->DT_BETEGN = 'Følgeskriv';
		$dokTilkn->DT_JOURNAL = '1'; 
		$dokTilkn->DT_MOTEDOK = '1';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (F, Følgeskriv) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'FH'; 
		$dokTilkn->DT_BETEGN = 'Forside av Hoveddokument';
		$dokTilkn->DT_JOURNAL = '1'; 
		$dokTilkn->DT_MOTEDOK = '0';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (FH, Forside av Hoveddokument) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'H'; 
		$dokTilkn->DT_BETEGN = 'Hoveddokument';
		$dokTilkn->DT_JOURNAL = '1'; 
		$dokTilkn->DT_MOTEDOK = '0';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (H, Hoveddokument) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'PH'; 
		$dokTilkn->DT_BETEGN = 'Hoveddokument møteprotokoll';
		$dokTilkn->DT_JOURNAL = '0'; 
		$dokTilkn->DT_MOTEDOK = '1';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (PH, Hoveddokument møteprotokoll) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'SF'; 
		$dokTilkn->DT_BETEGN = 'Hoveddokument for saksframlegg';
		$dokTilkn->DT_JOURNAL = '0'; 
		$dokTilkn->DT_MOTEDOK = '1';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (SF, Hoveddokument for saksframlegg) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'SP'; 
		$dokTilkn->DT_BETEGN = 'Saksprotokoll';
		$dokTilkn->DT_JOURNAL = '0'; 
		$dokTilkn->DT_MOTEDOK = '1';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (SP, Saksprotokoll) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;

		$dokTilkn = new DokTilkn();
		$dokTilkn->DT_KODE = 'V'; 
		$dokTilkn->DT_BETEGN = 'Vedlegg';
		$dokTilkn->DT_JOURNAL = '1'; 
		$dokTilkn->DT_MOTEDOK = '1';
		$this->writeToDestination($dokTilkn);
		$this->logger->log($this->XMLfilename, "(DT.KODE, DT.BETEGN) value (V, Vedlegg) assumed missing in database. Added", Constants::LOG_INFO);
		$this->infoIssued = true;
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO DOKTILKN (DT_KODE, DT_BETEGN, DT_JOURNAL, DT_MOTEDOK) VALUES (";

		$sqlInsertStatement .= "'" . $data->DT_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->DT_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->DT_JOURNAL . "', ";
		$sqlInsertStatement .= "'" . $data->DT_MOTEDOK. "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    	}

	function createXML($extractor) {    
    		$sqlQuery = "SELECT * FROM DOKTILKN";
    		$mapping = array ('idColumn' => 'dt_kode', 
  				'rootTag' => 'DOKTILKN.TAB',	
			    		'rowTag' => 'DOKTILKN',
  						'encoder' => 'utf8_decode',
							'elements' => array(
								'DT.KODE' => 'dt_kode',
								'DT.BETEGN' => 'dt_betegn',
								'DT.JOURNAL' => 'dt_journal',
								'DT.MOTEDOK' => 'dt_motedok'
								) 
							) ;
		
    		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
