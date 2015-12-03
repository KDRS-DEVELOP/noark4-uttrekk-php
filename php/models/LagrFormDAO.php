<?php

require_once 'models/LagrForm.php';
require_once 'models/Noark4Base.php';

class LagrFormDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('LAGRFORM'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
	} 
	
	function processTable () {	
		// Here you can add new fileformats
		$lagrForm = new LagrForm();
		$lagrForm->LF_KODE = 'RA-PDF'; 
		$lagrForm->LF_BESKRIV = 'Portable Document Format';
		$lagrForm->LF_ARKIV = '1'; 
		$lagrForm->LF_FILTYPE = 'PDF';
		$this->writeToDestination($lagrForm);
 		$this->logger->log($this->XMLfilename, "Adding (LF_KODE, LF_BESKRIV) value (RA-PDF, Portable Document Format)", Constants::LOG_INFO);

		$lagrForm = new LagrForm();
		$lagrForm->LF_KODE = 'RA-SGML'; 
		$lagrForm->LF_BESKRIV = 'SGML med tilhørende DTD';
		$lagrForm->LF_ARKIV = '1'; 
		$lagrForm->LF_FILTYPE = 'SGML'; 		
		$this->writeToDestination($lagrForm);
 		$this->logger->log($this->XMLfilename, "Adding (LF_KODE, LF_BESKRIV) value (RA-SGML, SGML med tilhørende DTD)", Constants::LOG_INFO);

		$lagrForm = new LagrForm();
		$lagrForm->LF_KODE = 'RA-TEKST'; 
		$lagrForm->LF_BESKRIV = 'ISO 8859-1';
		$lagrForm->LF_ARKIV = '1';
		$lagrForm->LF_FILTYPE = 'TXT';
		$this->writeToDestination($lagrForm);
		$this->logger->log($this->XMLfilename, "Adding (LF_KODE, LF_BESKRIV) value (RA-TEKST, ISO 8859-1)", Constants::LOG_INFO);

		$lagrForm = new LagrForm();
		$lagrForm->LF_KODE = 'RA-TIFF6'; 
		$lagrForm->LF_BESKRIV = 'TIFF Versjon 6';
		$lagrForm->LF_ARKIV = '1';
		$lagrForm->LF_FILTYPE = 'TIF';
		$this->writeToDestination($lagrForm);
		$this->logger->log($this->XMLfilename, "Adding (LF_KODE, LF_BESKRIV) value (RA-TIFF6, TIFF Versjon 6)", Constants::LOG_INFO);

		$lagrForm = new LagrForm();
		$lagrForm->LF_KODE = 'XML'; 
		$lagrForm->LF_BESKRIV = 'XML format';
		$lagrForm->LF_ARKIV = '1'; 
		$lagrForm->LF_FILTYPE = 'XML';
		$this->writeToDestination($lagrForm);
		$this->logger->log($this->XMLfilename, "Adding (LF_KODE, LF_BESKRIV) value (XML, XML format)", Constants::LOG_INFO);

		$lagrForm = new LagrForm();
		$lagrForm->LF_KODE = 'JPEG'; 
		$lagrForm->LF_BESKRIV = 'JPEG (ISO 10918-1:1994)';
		$lagrForm->LF_ARKIV = '1';
		$lagrForm->LF_FILTYPE = 'JPG';
		$this->writeToDestination($lagrForm);
		$this->logger->log($this->XMLfilename, "Adding (LF_KODE, LF_BESKRIV) value (JPEG, JPEG (ISO 10918-1:1994))", Constants::LOG_INFO);

 		$this->infoIssued = true;
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO LAGRFORM (LF_KODE, LF_BESKRIV, LF_ARKIV, LF_FILTYPE) VALUES (";

		$sqlInsertStatement .= "'" . $data->LF_KODE . "', ";
		$sqlInsertStatement .= "'" . $data->LF_BESKRIV . "', ";
		$sqlInsertStatement .= "'" . $data->LF_ARKIV . "', ";
		$sqlInsertStatement .= "'" . $data->LF_FILTYPE. "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
	}

	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM LAGRFORM";
		$mapping = array ('idColumn' => 'lf_kode', 
					'rootTag' => 'LAGRFORMAT.TAB',	
						'rowTag' => 'LAGRFORMAT',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'LF.KODE' => 'lf_kode',
									'LF.BESKRIV' => 'lf_beskriv',
									'LF.ARKIV' => 'lf_arkiv',
									'LF.FILTYPE' => 'lf_filtype'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");	
	}
 }
