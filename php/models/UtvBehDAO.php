<?php

require_once 'models/UtvBeh.php';
require_once 'models/Noark4Base.php';

class UtvBehDAO extends Noark4Base {

	protected $utvSakDAO;
	protected $utvBehDoDAO;

	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger, $utvSakDAO, $utvBehDoDAO) {
                parent::__construct (Constants::getXMLFilename('UTVBEH'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select UTVID, BEHID, JOURAARNR, BEHNR, MOTEID, SAKSNR, SAKSAAR, STATUS, PROTOKOLL from " . $SRC_TABLE_NAME . "";
		$this->utvSakDAO = $utvSakDAO;
		$this->utvBehDoDAO = $utvBehDoDAO;		
	} 
	
	
	function processTable () {
	
		// Missing US.ID value, it's basically a counter 1,2,3 etc but ties two tables together
		// Problem we se is that a US.ID is 1:M from another table to this table
		// So I have to generate it here. Everytime I resee a UTVID/SAKNR i use the already stored value
		// in $utvalg2uvalgsakPK
		$usIdCounter = 1;
		$utvalg2uvalgsakPK = array();

		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

				$utvBeh = new UtvBeh();
				$utvBeh->UB_ID = $result['BEHID'];
				$utvBeh->UB_UTSAKID = $result['JOURAARNR'];
				$utvBeh->UB_RFOLGE = $result['BEHNR'];

				if (strcmp($result['MOTEID'], '0') == 0) {
					$this->logger->log($this->XMLfilename, "UB_MOID is 0 for UB_ID (" . $result['BEHID'] . "). Value ignored,  assumed to be null", Constants::LOG_WARNING);
					$this->warningIssued = true;
				} else { 
					$utvBeh->UB_MOID = $result['MOTEID'];
				}

				$utvBeh->UB_USEKNR = $result['SAKSNR'];
				$utvBeh->UB_AAR = $result['SAKSAAR'];
				$utvBeh->UB_BEHSTATUS = $result['STATUS'];

				$sakFromDGSMASQL = "SELECT INNH1, U1, BEHTYPE, UNNTOFF, GRUPPEID, LHJEMMEL, SBHID, ADMID  FROM DGSMSA WHERE JOURAARNR = '" . $result['JOURAARNR'] . "'" ;
				
				$this->srcBase->createAndExecuteQuery ($sakFromDGSMASQL );	
 				$sakResult = $this->srcBase->getQueryResult ($sakFromDGSMASQL );

				if (isset($sakResult) == false) {
					echo " UTVALG SAK missing SAK in DGSMSA ". $sakFromDGSMASQL; 
					die;
				}			

				

				if (is_null($sakResult['ADMID']) == true) {
					$utvBeh->UB_ADMID = Constants::ADMININDEL_TOPNIVA;
					$this->logger->log($this->XMLfilename, "UB_ADMID is null for UB_ID (" . $result['BEHID'] . "), SAK ID (" . $result['JOURAARNR'] . ") Value mandatory, set to ADMININDEL_TOPNIVA VALUE (" . Constants::ADMININDEL_TOPNIVA  . ")", Constants::LOG_ERROR);
					$this->errorIssued = true;

				} else {
					$utvBeh->UB_ADMID = $sakResult['ADMID'];
				}



				if (is_null($sakResult['SBHID']) == true) {
					$utvBeh->UB_SBHID = Constants::INGENBRUKER_ID;
					$this->logger->log($this->XMLfilename, "UB_SBHID is null for UB_ID (" . $result['BEHID'] . "), SAK ID (" . $result['JOURAARNR'] . ") Value mandatory,  set to PERSON NOUSER Value (" . Constants::INGENBRUKER_ID  . ")", Constants::LOG_ERROR);
					$this->errorIssued = true;

				} else {
					$utvBeh->UB_SBHID = $sakResult['SBHID'];
				}

				$key =  $result['UTVID'] . '_' . $result['JOURAARNR'];
				$US_ID  = null;

				$duplicateSakDoNotProcess = false;
				if (isset($utvalg2uvalgsakPK[$key]) == true) {
					$US_ID = $utvalg2uvalgsakPK[$key];
					$duplicateSakDoNotProcess = true; 
				}
				else {
					$utvalg2uvalgsakPK[$key] = $usIdCounter;
					$US_ID = $usIdCounter;
					$usIdCounter++;	
				}

				$utvBeh->UB_PROTOKOLL = $result['PROTOKOLL'];

				
				$u1 = $sakResult['U1'];
				$tittel = $sakResult['INNH1'];
				$saktype = $sakResult['BEHTYPE'];
				$tgKode = $sakResult['UNNTOFF'];
				$uOff = $sakResult['LHJEMMEL'];
				$tgGrupe = $sakResult['GRUPPEID'];
				//$ =  $sakResult[''];$ =  $sakResult[''];				


				$utvSak = new UtvSak();
				$utvSak->US_UTVID = $result['UTVID'];
				$utvSak->US_ID = $US_ID;
				$utvSak->US_SAKSTYPE = $sakResult['BEHTYPE'];
				$utvSak->US_LUKKET = '0';
		
				$utvSak->US_TGKODE = $tgKode;
				$utvSak->US_TGGRUPPE = $tgGrupe;
				$utvSak->US_UOFF = $uOff;
				$utvSak->US_SAID = $result['JOURAARNR'];
				
//				$utvSak->US_POLSGID = $result[''];
//				$utvSak->US_JPID = $result[''];
//				$utvSak->US_SAMMENR = $result[''];

				if ($duplicateSakDoNotProcess == false) {
					$this->utvSakDAO->processUtvSak ($utvSak, $utvBeh);
				}

				$this->srcBase->endQuery($sakFromDGSMASQL);



				$this->writeToDestination($utvBeh);
		}
		$this->srcBase->endQuery($this->selectQuery);
		$utvalg2uvalgsakPK = null;
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO UTVBEH (UB_ID, UB_UTSAKID, UB_RFOLGE, UB_MOID, UB_USEKNR, UB_AAR, UB_BEHSTATUS, UB_ADMID, UB_SBHID, UB_PROTOKOLL) VALUES (";

		$sqlInsertStatement .= "'" . $data->UB_ID . "',";
		$sqlInsertStatement .= "'" . $data->UB_UTSAKID . "',";
		$sqlInsertStatement .= "'" . $data->UB_RFOLGE . "',";
		$sqlInsertStatement .= "'" . $data->UB_MOID . "',";
		$sqlInsertStatement .= "'" . $data->UB_USEKNR . "',";
		$sqlInsertStatement .= "'" . $data->UB_AAR . "',";
		$sqlInsertStatement .= "'" . $data->UB_BEHSTATUS . "',";
		$sqlInsertStatement .= "'" . $data->UB_ADMID . "',";
		$sqlInsertStatement .= "'" . $data->UB_SBHID . "',";
		$sqlInsertStatement .= "'" . $data->UB_PROTOKOLL . "'";

		$sqlInsertStatement.= ");";

		$this->uttrekksBase->printErrorIfFKFail = false;
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {

			if (mysql_errno() == Constants::MY_SQL_MISSING_FK_VALUE) {

				$errorString = mysql_error();

				// Missing a Person in PERSON table
				if (strpos($errorString, "PERSON") !== FALSE) {
	
					$missingPersonId = $data->UB_SBHID;
					
					$this->logger->log($this->XMLfilename, "UB_SBHID VALUE (" . $data->UB_SBHID . ") for UB_ID (" . $data->UB_ID . "), SAK ID (" . $data->UB_UTSAKID . ") Does not exist in PERSON table. UB_SBHID is set to (" . Constants::INGENBRUKER_ID  . ")", Constants::LOG_ERROR);
					$data->UB_SBHID = Constants::INGENBRUKER_ID;
					$this->errorIssued = true;
 					$this->writeToDestination($data);
				}			
				$this->logger->log($this->XMLfilename, "Error with FK" , Constants::LOG_WARNING);
				$this->warningIssued = true;
			}
		}
		$this->uttrekksBase->printErrorIfFKFail = true;


        }
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM UTVBEH";
    	$mapping = array ('idColumn' => 'ub_moid', 
  				'rootTag' => 'UTVBEH.TAB',	
			    		'rowTag' => 'UTVBEH',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'UB.MOID' => 'ub_moid',
							'UB.ID' => 'ub_id',
							'UB.RFOLGE' => 'ub_rfolge',
							'UB.UTSAKID' => 'ub_utsakid',
							'UB.BEHSTATUS' => 'ub_behstatus',
							'UB.PROTOKOLL' => 'ub_protokoll',
							'UB.AAR' => 'ub_aar',
							'UB.USEKNR' => 'ub_useknr',
							'UB.ADMID' => 'ub_admid',
							'UB.SBHID' => 'ub_sbhid'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			