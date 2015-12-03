<?php

require_once 'models/UtvSak.php';
require_once 'models/Noark4Base.php';

class UtvSakDAO extends Noark4Base {
	
	protected $utvBehDoDAO;
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger, $utvBehDoDAO) {
                parent::__construct (Constants::getXMLFilename('UTVSAK'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->utvBehDoDAO = $utvBehDoDAO;
	} 
	
	function processUtvSak ($utvSak, $utvBeh) {


		$this->writeToDestination($utvSak);

		$jpQuery = "SELECT REFAARNR, DOKKAT, DOKSTATUS, TYPE FROM DGJMJO WHERE JOURAARNR = '" . $utvSak->US_SAID . "'";

		$this->srcBase->createAndExecuteQuery ($jpQuery);		
		while (($jpResult = $this->srcBase->getQueryResult ($jpQuery))) {

			$jpID = $jpResult['REFAARNR']; 
			$dokKat = $jpResult['DOKKAT'];
			$dokStatus = $jpResult['DOKSTATUS'];
			$dokType = $jpResult['TYPE'];

			$dokumentQuery = "SELECT DOKID FROM FILER WHERE REFAARNR = '" . $jpID . "'";

			// You are going to have to set values where no value is set!!

			$this->srcBase->createAndExecuteQuery ($dokumentQuery);		
			while (($dokResult = $this->srcBase->getQueryResult ($dokumentQuery))) {
	
				$utvBehDo = new UtvBehDo();
				$utvBehDo->BD_BEHID = $utvBeh->UB_ID;
				$utvBehDo->BD_DOKID = $dokResult['DOKID'];
				$utvBehDo->BD_NDOKTYPE = $dokType;
				$utvBehDo->BD_STATUS= $dokStatus;
				$utvBehDo->BD_JPID = $jpID;
				$utvBehDo->BD_DOKTYPE = $dokKat;

				$this->utvBehDoDAO->processUtvBehDo($utvBehDo);

			}
	
			$this->srcBase->endQuery($dokumentQuery);

		}

		$this->srcBase->endQuery($jpQuery);
/*
		// Here we want to handle UtvBehDo
		$sakFromDGSMASQL = "SELECT INNH1, U1, BEHTYPE, UNNTOFF, GRUPPEID, LHJEMMEL, SBHID, ADMID  FROM DGSMSA WHERE JOURAARNR = '" . $result['JOURAARNR'] . "'" ;
				
		$this->srcBase->createAndExecuteQuery ($sakFromDGSMASQL );	
 		$sakResult = $this->srcBase->getQueryResult ($sakFromDGSMASQL );

		if (isset($sakResult) == false) {
			echo " UTVALG SAK missing SAK in DGSMSA " . $sakFromDGSMASQL; 
			die;
		}			

		//$utvBeh->UB_ADMID = ;
		$utvBeh->UB_SBHID = $sakResult['SBHID'];
		$utvBeh->UB_PROTOKOLL = $result['PROTOKOLL'];
		
		$u1 =  $sakResult['U1'];
		$tittel =  $sakResult['INNH1'];
		$saktype =  $sakResult['BEHTYPE'];
		$tgKode =  $sakResult['UNNTOFF'];
		$uOff =  $sakResult['LHJEMMEL'];
		$tgGrupe =  $sakResult['GRUPPEID'];
		//$ =  $sakResult[''];$ =  $sakResult[''];				

		$this->srcBase->endQuery($sakFromDGSMASQL);

*/
		// pick up pall journalposter connected to this sak and identify all documents connected to the journalpost


	}


	function processTable () {

	// This does not get called


/*
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
	
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

				// Some values don't exist in the table, but we can to go to the DGSMSA table to get the values

				print_r($result);

				$utvSak = new UtvSak();
				//$utvSak->US_UTVID  = $result['UTVID'];

				$utvSak->US_ID = $result['SBHID'];
				$utvSak->US_SAKSTYPE = $result['BEHTYPE'];


// There IS A INNH1 in DGMABH!!!!!!!!!

				$sakFromDGSMASQL = "SELECT INNH1, U1, BEHTYPE, UNNTOFF, GRUPPEID, LHJEMMEL FROM DGSMSA WHERE JOURAARNR = '" . $result['JOURAARNR'] . "'" ;
				
				$this->srcBase->createAndExecuteQuery ($sakFromDGSMASQL );	
 				$sakResult = $this->srcBase->getQueryResult ($sakFromDGSMASQL );

				if (isset($sakResult) == false) {
					echo " UTVALG SAK missing SAK in DGSMSA " . $sakFromDGSMASQL; 
					die;
				}			
		
				
				$u1 =  $sakResult['U1'];
				$tittel =  $sakResult['INNH1'];
				$saktype =  $sakResult['BEHTYPE'];
				$tgKode =  $sakResult['UNNTOFF'];
				$uOff =  $sakResult['LHJEMMEL'];
				$tgGrupe =  $sakResult['GRUPPEID'];
				//$ =  $sakResult[''];$ =  $sakResult[''];				

				$this->srcBase->endQuery($sakFromDGSMASQL);



				if (is_null($result['INNH1'])) {
					// Pick it up from DGSMSA					
					if (is_null($tittel)) {
						$this->logger->log($this->XMLfilename, "No title available for NOARKSAK/UTVALGSAK_SAID (" . $result['JOURAARNR'] . ")", Constants::LOG_WARNING);
						$this->warningIssued = true;
					}
					else {
						$utvSak->US_TITTEL = $tittel;
					}
				}
				// MAke sure it's the same as the one from DGSMSA 
				else {
					$utvSak->US_TITTEL = $result['INNH1'];
					if (strcmp($tittel, $result['INNH1']) != 0) {
						$this->logger->log($this->XMLfilename, "Tittel field in NOARKSAK and UTVSAK  are not the same for US.ID (". $utvSak->US_ID . ") Bruker UTVSAK.TITTEL( " . $result['INNH1'] .  ") but NOARKSAK value is (". $tittel ." )", Constants::LOG_INFO);
						$this->infoIssued = true;
					} 

				}	

				if (strcmp($result['LUKKET'], "Å") == 0 ){
					$utvSak->US_LUKKET = '0';
					$this->logger->log($this->XMLfilename, "LUKKET has value 'Å' assuming '0' for UTVSAK.ID(". $utvSak->US_ID . ")", Constants::LOG_INFO);
					$this->infoIssued = true;
				}
				else if (strcmp($result['LUKKET'], "L") == 0 ){
					$utvSak->US_LUKKET = '1';
					$this->logger->log($this->XMLfilename, "LUKKET has value 'L' assuming '1' for UTVSAK.ID(". $utvSak->US_ID . ")", Constants::LOG_INFO);
					$this->infoIssued = true;
				}
				else {
					$this->logger->log($this->XMLfilename, "LUKKET has no value or unknown value for UTVSAK.ID(". $utvSak->US_ID . ") Setting it to 0", Constants::LOG_INFO);
					$this->infoIssued = true;
					$utvSak->US_LUKKET = '0';
				}


				$utvSak->US_TGKODE = $tgKode;
				$utvSak->US_TGGRUPPE = $tgGrupe;
				$utvSak->US_UOFF = $uOff;
				$utvSak->US_SAID = $result['JOURAARNR'];
				
//				$utvSak->US_POLSGID = $result[''];
//				$utvSak->US_JPID = $result[''];
//				$utvSak->US_SAMMENR = $result[''];
				$this->writeToDestination($utvSak);
		}
		$this->srcBase->endQuery($this->selectQuery);
*/
	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO  UTVSAK (US_UTVID, US_ID, US_SAKSTYPE, US_TITTEL, US_LUKKET, US_TGKODE, US_TGGRUPPE, US_UOFF, US_SAMMENR, US_SAID, US_POLSGID,  US_JPID) VALUES (";
		$sqlInsertStatement .= "'" . $data->US_UTVID . "', ";						
		$sqlInsertStatement .= "'" . $data->US_ID . "', ";
		$sqlInsertStatement .= "'" . $data->US_SAKSTYPE . "', ";
		$sqlInsertStatement .= "'" . mysql_real_escape_string($data->US_TITTEL) . "', ";
		$sqlInsertStatement .= "'" . $data->US_LUKKET . "', ";
		$sqlInsertStatement .= "'" . $data->US_TGKODE . "', ";
		$sqlInsertStatement .= "'" . $data->US_TGGRUPPE . "', ";
		$sqlInsertStatement .= "'" . $data->US_UOFF . "', ";
		$sqlInsertStatement .= "'" . $data->US_SAMMENR . "', ";
		$sqlInsertStatement .= "'" . $data->US_SAID . "', ";
		$sqlInsertStatement .= "'" . $data->US_POLSGID. "', ";
		$sqlInsertStatement .= "'" . $data->US_JPID. "' ";
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);
    }
 
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM UTVSAK";
    	$mapping = array ('idColumn' => 'us_utvid', 
  				'rootTag' => 'UTVALGSAK.TAB',	
			    		'rowTag' => 'UTVALGSAK',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'US.UTVID' => 'us_utvid',
							'US.ID' => 'us_id',
							'US.SAKSTYPE' => 'us_sakstype',
							'US.TITTEL' => 'us_tittel',
							'US.LUKKET' => 'us_lukket',
							'US.TGKODE' => 'us_tgkode',
							'US.TGGRUPPE' => 'us_tggruppe',
							'US.UOFF' => 'us_uoff',
							'US.SAID' => 'us_said',
							'US.POLSGID' => 'us_polsgid',
							'US.JPID' => 'us_jpid'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    }    
 }
