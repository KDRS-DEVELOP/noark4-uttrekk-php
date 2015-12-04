<?php

require_once 'models/Tillegg.php';
require_once 'models/Noark4Base.php';

class TilleggDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('TILLEGG'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select ID, JOURAARNR, REFAARNR, DOKID, FILNR, VARIANT, ITYPE, TIDSPKT, PNID, INFO FROM " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

				$tillegg = new Tillegg();
				$tillegg->TI_ID = $result['ID'];
				$tillegg->TI_SAID = $result['JOURAARNR'];
				$tillegg->TI_JPID = $result['REFAARNR'];
				$tillegg->TI_DOKID = $result['DOKID'];
				$tillegg->TI_DOKVER = $result['FILNR'];
				$tillegg->TI_VARIANT = $result['VARIANT'];
				$tillegg->TI_ITYPE = $result['ITYPE'];
				$tillegg->TI_REGDATO = $result['TIDSPKT'];
				$tillegg->TI_REGAV = $result['PNID'];
				$tillegg->TI_TEKST = mysql_real_escape_string($result['INFO']);
				$this->writeToDestination($tillegg);
		}
		$this->srcBase->endQuery($this->selectQuery);


		$dgtilleggsinfoSQL = "select ID, JOURAARNR, REFAARNR, DOKID, FILNR, VARIANT, RNR, ITYPE, UNNTOFF, GRUPPEID, REGDATO, REGAV, PVGAV, TEKST FROM  DGTILLEGGSINFO";
		// There is a a table called "DGTILLEGGSINFO" that might also contain information to b eextracted 
		$this->srcBase->createAndExecuteQuery ($dgtilleggsinfoSQL);
	
		while (($result = $this->srcBase->getQueryResult ($dgtilleggsinfoSQL))) {

				$tillegg = new Tillegg();
				$tillegg->TI_ID = $result['ID'];
				$tillegg->TI_SAID = $result['JOURAARNR'];
				$tillegg->TI_JPID = $result['REFAARNR'];
				$tillegg->TI_DOKID = $result['DOKID'];
				$tillegg->TI_DOKVER = $result['FILNR'];
				$tillegg->TI_VARIANT = $result['VARIANT'];
				$tillegg->TI_RNR = $result['RNR'];
				$tillegg->TI_ITYPE = $result['ITYPE'];
				$tillegg->TI_TGKODE = $result['UNNTOFF'];
				$tillegg->TI_TGGRUPPE = $result['GRUPPEID'];
				$tillegg->TI_REGDATO = Utility::fixDateFormat($result['REGDATO']);
				$tillegg->TI_REGAV = $result['REGAV'];
				$tillegg->TI_PVGAV = $result['PVGAV'];
				$tillegg->TI_TEKST = $result['TEKST'];

				$this->writeToDestination($tillegg);
		}
		$this->srcBase->endQuery($dgtilleggsinfoSQL);
	}


	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO TILLEGG (TI_ID, TI_SAID, TI_JPID, TI_DOKID, TI_DOKVER, TI_VARIANT, TI_RNR, TI_ITYPE, TI_TGKODE, TI_TGGRUPPE, TI_REGDATO, 
TI_REGAV, TI_PVGAV, TI_TEKST) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->TI_ID . "',";
		$sqlInsertStatement .= "'" . $data->TI_SAID . "',";
		$sqlInsertStatement .= "'" . $data->TI_JPID . "',";
		$sqlInsertStatement .= "'" . $data->TI_DOKID . "',";
		$sqlInsertStatement .= "'" . $data->TI_DOKVER . "',";
		$sqlInsertStatement .= "'" . $data->TI_VARIANT . "',";
		$sqlInsertStatement .= "'" . $data->TI_RNR . "',";
		$sqlInsertStatement .= "'" . $data->TI_ITYPE . "',";
		$sqlInsertStatement .= "'" . $data->TI_TGKODE . "',";
		$sqlInsertStatement .= "'" . $data->TI_TGGRUPPE . "',";
		$sqlInsertStatement .= "'" . $data->TI_REGDATO . "',";
		$sqlInsertStatement .= "'" . $data->TI_REGAV . "',";
		$sqlInsertStatement .= "'" . $data->TI_PVGAV . "',";
		$sqlInsertStatement .= "'" . $data->TI_TEKST . "'";

		
		$sqlInsertStatement.= ");";
  		$this->uttrekksBase->printErrorIfDuplicateFail = false;	
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Duplicate values on PK detected. This is probably not an error in ESA but more a problem with the extraction code not being able to convert løpenummer to journalpostnummer  TI_ID(" . $data->TI_ID ."), TI_TEKST(" . $data->TI_TEKST . ")", Constants::LOG_ERROR);
			}
		}
  		$this->uttrekksBase->printErrorIfDuplicateFail = true;
		
    }

    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM TILLEGG";
    	$mapping = array ('idColumn' => 'ti_id', 
  				'rootTag' => 'TILLEGG.TAB',	
			    		'rowTag' => 'TILLEGG',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'TI.ID' => 'ti_id',
							'TI.SAID' => 'ti_said',
							'TI.JPID' => 'ti_jpid',
							'TI.DOKID' => 'ti_dokid',
							'TI.DOKVER' => 'ti_dokver',
							'TI.VARIANT' => 'ti_variant',
							'TI.RNR' => 'ti_rnr',
							'TI.ITYPE' => 'ti_itype',
							'TI.TGKODE' => 'ti_tgkode',
							'TI.TGGRUPPE' => 'ti_tggruppe',
							'TI.REGDATO' => 'ti_regdato',
							'TI.REGAV' => 'ti_regav',
							'TI.PVGAV' => 'ti_pvgav',
							'TI.TEKST' => 'ti_tekst'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
