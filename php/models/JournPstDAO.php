<?php

require_once 'models/JournPst.php';
require_once 'models/Noark4Base.php';

class JournPstDAO extends Noark4Base {

	protected $merknadDao;
	protected $jpQueryInitialised = false;
	protected $kommuneName;

	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger, $merknadDao, $kommuneName) {
		parent::__construct (Constants::getXMLFilename('JOURNPST'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);		
		$this->merknadDao = $merknadDao; //10003633  10004929
		$this->selectQuery = "select * from " . $SRC_TABLE_NAME . ""; //" where refaarnr = '10004929'";
		$this->kommuneName = $kommuneName;
	} 
	
	function processTable () {
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		$counter = 1;

		$dokLinkDAO = new DokLinkDAO($this->srcBase, $this->uttrekksBase, Constants::getXMLFilename('JOURNPST'), $this->kommuneName, $this->logger);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$person = new JournPst();

// 				TODO : Do I ned this??
//				if (substr($noarkJP->JP_ID, 0 , 2) > 30)
//  					$journPst->JP_ID = "19" . $result['REFAARNR'];
//    				else 
//					$journPst->JP_ID = "20" . $result['REFAARNR'];
    	    	
	
				$journPst->JP_ID = $result['REFAARNR'];
				$journPst->JP_JAAR = $result['JOURAAR'];
				$journPst->JP_SEKNR = $result['REFNR'];
				$journPst->JP_SAID = $result['JOURAARNR'];
// 				TODO : Do I ned this??
//				if (substr($result['JOURAARNR'], 0, 2) > 30)
//					$journPst->JP_SAID = "19" . $result['JOURAARNR'];
//				else
//					$journPst->JP_SAID = "20" . $result['JOURAARNR'];

				$journPst->JP_JDATO = Utility::fixDateFormat($result['REGDATO']);
				$journPst->JP_NDOKTYPE = $result['TYPE'];
				$journPst->JP_DOKDATO = $result['DATERT'];
				$journPst->JP_UDATERT = 0; // $result['']; // TODO, why 0

				if (strcmp($result['STATUS'], '?') == 0) {
					$this->logger->log($this->XMLfilename, "JOURNALPOST JP_ID (" . $journPst->JP_ID . ") has status (" . $result['STATUS'] . ") Setting status to AVSLUTTET ", Constants::LOG_WARNING);
					$this->warningIssued = true;
				}
				else {
					$journPst->JP_STATUS = $result['STATUS'];
				}
				$journPst->JP_INNHOLD = mysql_real_escape_string($result['INNH1']) . mysql_real_escape_string($result['INNH2']);
				$journPst->JP_U1 = $result['U1'];
				$journPst->JP_AVSKDATO = Utility::fixDateFormat($result['AVSKRDATO']);
				$journPst->JP_FORFDATO = Utility::fixDateFormat($result['SVARFRIST']);
				$journPst->JP_UOFF = $result['HJEMMEL'];
				$journPst->JP_OVDATO = null;
				$journPst->JP_AGDATO = null;
				$journPst->JP_AGKODE = "";
				$journPst->JP_JPOSTNR ='1';
				$journPst->JP_TGKODE = $result['UNNTOFF'];
				$journPst->JP_TGGRUPPE = "";
//				$journPst->JP_OVDATO = Utility::fixDateFormat($result['']);
//				$journPst->JP_AGDATO = Utility::fixDateFormat($result['']);
				$journPst->JP_PAPIR = $result['PAPIR'];
				if ($result['VEDLEGG'] != null)
					$journPst->JP_ANTVED = $result['VEDLEGG'];
				else 
					$journPst->JP_ANTVED = 0;
	 
				$this->writeToDestination($journPst);

				$dokLinkDAO->processDokLinks($journPst, $result['SBHID'],  $result['DOKKAT'], $result['PAPIR'], $result['LOKPAPIR'], $result['DOKSTATUS']);

//				processDokBeskriv($journPst, $filerInfo);
			//	processDokVers($journPst, $filerInfo);

				$register = "S";
/*				$nokkel = $result->SA_ID;
				$untoff = $result->SA_TGKODE;
				$gruppeId = $result->SA_TGGRUPPE;
				$merknad = $result['MERKNAD'];
				$sbhId = $noarkSak->SA_ANSVID;
				//$this->merknad->processMerknadFromSakOrJP($register, $nokkel, $untoff, $gruppeId, $merknad, $sbhId);
*/
				if ($counter++ %Constants::DOT_MARKER_COUNT == 0)
					echo ".";

				}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO JOURNPST (JP_ID, JP_JAAR, JP_SEKNR, JP_SAID, JP_JPOSTNR,  JP_JDATO, JP_NDOKTYPE, JP_DOKDATO, JP_STATUS, JP_INNHOLD, JP_U1, JP_AVSKDATO, JP_FORFDATO, JP_UOFF, JP_OVDATO, JP_AGDATO, JP_PAPIR, JP_ANTVED) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->JP_ID . "', ";
		$sqlInsertStatement .= "'" . $data->JP_JAAR . "', ";
		$sqlInsertStatement .= "'" . $data->JP_SEKNR . "', ";
		$sqlInsertStatement .= "'" . $data->JP_SAID . "', ";
		$sqlInsertStatement .= "'" . $data->JP_JPOSTNR . "', ";
		$sqlInsertStatement .= "'" . $data->JP_JDATO . "', ";
		$sqlInsertStatement .= "'" . $data->JP_NDOKTYPE . "', ";
		$sqlInsertStatement .= "'" . $data->JP_DOKDATO . "', ";
		$sqlInsertStatement .= "'" . $data->JP_STATUS . "', ";
		$sqlInsertStatement .= "'" . $data->JP_INNHOLD . "', ";
		$sqlInsertStatement .= "'" . $data->JP_U1 . "', ";
		$sqlInsertStatement .= "'" . $data->JP_AVSKDATO . "', ";
		$sqlInsertStatement .= "'" . $data->JP_FORFDATO . "', ";
		$sqlInsertStatement .= "'" . $data->JP_UOFF . "', ";
		$sqlInsertStatement .= "'" . $data->JP_OVDATO . "', ";
		$sqlInsertStatement .= "'" . $data->JP_AGDATO. "', ";
		$sqlInsertStatement .= "'" . $data->JP_PAPIR . "', ";
		$sqlInsertStatement .= "'" . $data->JP_ANTVED . "'";
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->printErrorIfFKFail = false;

		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_MISSING_FK_VALUE) {

				$errorString = mysql_error();
				// Missing refernece to NOARKSAK. Probably UTGÅR - ingnored and logged as ERROR
				if (strpos($errorString, "NOARKSAK") !== FALSE) {
					$this->logger->log($this->XMLfilename, "JP_ID VALUE (" . $data->JP_ID . ") has no reference to a NOARKSAK. JP_INNHOLD is (" . $data->JP_INNHOLD . ")", Constants::LOG_ERROR);
					$this->errorIssued = true;

				}			
			}
		}
		$this->uttrekksBase->printErrorIfFKFail = true;


    }


  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM JOURNPST";
    	$mapping = array ('idColumn' => 'jp_id', 
  				'rootTag' => 'JOURNPOST.TAB',	
			    		'rowTag' => 'JOURNPOST',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'JP.ID' => 'jp_id',
							'JP.JAAR' => 'jp_jaar',
							'JP.SEKNR' => 'jp_seknr',
							'JP.SAID' => 'jp_said',
							'JP.POSTNR' => 'jp_jpostnr',
							'JP.JDATO' => 'jp_jdato',
							'JP.NDOKTYPE' => 'jp_ndoktype',
							'JP.UDATERT' => 'jp_udatert',
							'JP.STATUS' => 'jp_status',
							'JP.INNHOLD' => 'jp_innhold',
							'JP.U1' => 'jp_u1'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			