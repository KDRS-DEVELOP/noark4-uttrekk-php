<?php

require_once 'models/UtvMote.php';
require_once 'models/UtvMoteDok.php';
require_once 'utility/Utility.php';
require_once 'models/Noark4Base.php';

class UtvMoteDokDAO extends Noark4Base {
	
	protected $docCounter;
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVMOTEDOK'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->docCounter = 0;
	} 
	
	function processTable () {
		echo "DO NOT CALL ME!!!!!!! But if you do call me, at least call me Al!";
	}

	function processUtvMoteDok($utvID, $moID, $sakID) {

		$selectJPQuery = "SELECT DOKKAT, DOKODE, REGDATO, STATUS, EKODE1, PAPIR, INNH1, U1, ADMID, SBHID, UNNTOFF, GRUPPEID, HJEMMEL FROM DGJMJO WHERE JOURAARNR = '" . $sakID . "'"; ;
		$this->srcBase->createAndExecuteQuery ($selectJPQuery);

		while (($result = $this->srcBase->getQueryResult ($selectJPQuery))) {
			$utvMoteDok = new UtvMoteDok();
			$this->docCounter++;
		
			$utvMoteDok->MD_ID = $this->docCounter; // auto-increment, starts at 1
			$utvMoteDok->MD_UTVID = $utvID; // dgmamo.utvid
			$utvMoteDok->MD_MOID = $moID;  // dgmamo.id
			$utvMoteDok->MD_DOKTYPE = $result['DOKODE']; // dgjmjo.dokkat
			$utvMoteDok->MD_REGDATO = Utility::fixDateFormat($result['REGDATO']); // dgjmjo.regdato
			$utvMoteDok->MD_STATUS = $result['STATUS']; // dgjmjo.status
			$utvMoteDok->MD_ARKKODE = $result['EKODE1']; // dgjmjo.ekode1 / AVGRADER???
			$utvMoteDok->MD_PAPIRDOK = $result['PAPIR']; // dgjmjo.papir checklogic
			$utvMoteDok->MD_INNHOLD = $result['INNH1'];  //dgjmjo.innh1
			$utvMoteDok->MD_U1 = $result['U1']; // //dgjmjo.innh1
			$utvMoteDok->MD_ADMID = $result['ADMID']; //dgjmjo.admid
			$utvMoteDok->MD_SBHID = $result['SBHID']; // dgjmjo.sbhid
			$utvMoteDok->MD_TGKODE = $result['UNNTOFF']; // dgjmjo
			$utvMoteDok->MD_TGGRUPPE = $result['GRUPPEID']; // dgjmjo
			$utvMoteDok->MD_UOFF = $result['HJEMMEL']; //
			// The follwing could be implemented but will need a call to SAK/DGSMSA
			// From what I can tell from inspection, these fields have no value / are null

			//$utvMoteDok->MD_AGDATO = $result['']; //
			//$utvMoteDok->MD_AGKODE = $result['']; //
			//$utvMoteDok->MD_KASSDATO = $result['']; //
			//$utvMoteDok->MD_KASSKODE = $result['']; //

			// This seems to be missing from the database, but not sure
			//$utvMoteDok->MD_BEVTID = $result['']; //

			$this->writeToDestination($utvMoteDok);
		}

		$this->srcBase->endQuery($selectJPQuery);
	}
	

	function writeToDestination($data) {

		$sqlInsertStatement = "INSERT INTO  UTVMOTEDOK (MD_ID, MD_UTVID, MD_MOID, MD_DOKTYPE, MD_REGDATO, MD_STATUS, MD_ARKKODE, MD_PAPIRDOK, MD_INNHOLD, MD_U1, MD_ADMID, MD_SBHID, MD_TGKODE, MD_TGGRUPPE, MD_UOFF) VALUES (";

//$sqlInsertStatement = "INSERT INTO  UTVMOTEDOK (MD_ID, MD_UTVID, MD_MOID, MD_DOKTYPE, MD_REGDATO, MD_STATUS, MD_ARKKODE, MD_PAPIRDOK, MD_INNHOLD, MD_U1, MD_ADMID,
//MD_SBHID, MD_TGKODE, MD_TGGRUPPE, MD_UOFF, MD_AGDATO, MD_AGKODE, MD_BEVTID, MD_KASSDATO, MD_KASSKODE) VALUES (";


		$sqlInsertStatement .= "'" . $data->MD_ID . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_UTVID . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_MOID . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_DOKTYPE . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_REGDATO . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_STATUS . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_ARKKODE . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_PAPIRDOK . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_INNHOLD . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_U1 . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_ADMID . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_SBHID . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_TGKODE . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_TGGRUPPE . "', ";;
		$sqlInsertStatement .= "'" . $data->MD_UOFF . "'";;
//		$sqlInsertStatement .= "'" . $data->MD_AGDATO . "', ";;
//		$sqlInsertStatement .= "'" . $data->MD_AGKODE . "', ";;
//		$sqlInsertStatement .= "'" . $data->MD_BEVTID . "', ";;
		//$sqlInsertStatement .= "'" . $data->MD_KASSDATO . "', ";;
		//$sqlInsertStatement .= "'" . $data->MD_KASSKODE . "' ";;
		
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }
 
	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM UTVMOTEDOK";
		$mapping = array ('idColumn' => 'MD.ID', 
					'rootTag' => 'UTVMOTEDOK.TAB',	
						'rowTag' => 'UTVMOTEDOK',
						'fileName' => 'UTVMDOK',
							'encoder' => 'utf8_decode',
							'elements' => array(
										'MD.ID' => 'md_id',
										'MD.UTVID' => 'md_utvid',
										'MD.MOID' => 'md_moid',
										'MD.DOKTYPE' => 'md_doktype',
										'MD.REGDATO' => 'md_regdato',
										'MD.STATUS' => 'md_status',
										'MD.ARKKODE' => 'md_arkkode',
										'MD.PAPIRDOK' => 'md_papirdok',
										'MD.INNHOLD' => 'md_innhold',
										'MD.U1' => 'md_u1',
										'MD.ADMID' => 'md_admid',
										'MD.SBHID' => 'md_sbhid',
										'MD.TGKODE' => 'md_tgkode',
										'MD.TGGRUPPE' => 'md_tggruppe',
										'MD.UOFF' => 'md_uoff'
								//		'MD.AGDATO' => 'md_agdato',
								//		'MD.AGKODE' => 'md_agkode',
								//		'MD.BEVTID' => 'md_bevtid',
								//		'MD.KASSDATO' => 'md_kassdato',
								//		'MD.KASSKODE' => 'md_kaskode'
								) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }
}