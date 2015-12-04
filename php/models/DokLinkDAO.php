<?php

require_once 'models/DokLink.php';
require_once 'utility/Constants.php';

class DokLinkDAO extends Noark4Base {

	var $SRC_TABLE_NAME = null;
	var $selectFromFilerQuery = null;
	var $selectFromVedleggQuery = null;
	var $kommuneName = null;
	var $dokVersDAO = null;
	var $dokBeskDAO = null;

	public function __construct  ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $kommuneName, $logger) {		
		parent::__construct (Constants::getXMLFilename('DOKLINK'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectFromFilerQuery = "select REFAARNR, DOKID, FILNOKKEL, OPPRETTETDATO, OPPRETTETAVID, FILNR, AKTIV from FILER WHERE REFAARNR = '";
		$this->selectFromVedleggQuery =  "select REFAARNR, VEDLNR, BESKRIVELSE, DOKID, DOKNAVN, DOKODE, TKDATO, TKAV, PAPIR, LOKPAPIR, DOKSTATUS, DOKKAT, PNID, GRUPPE, GRUPPEID, UNNTOFF, HJEMMEL, AVGRADER, AGDATO from DGJMVEDLEGG WHERE REFAARNR = '";
		$this->kommuneName = $kommuneName;

		$this->dokVersDAO = new DokVersDAO($this->srcBase, $this->uttrekksBase, "", $kommuneName, $logger);
		$this->dokBeskDAO = new DokBeskDAO($this->srcBase, $this->uttrekksBase, "", $kommuneName, $logger);
	} 


	function processTable(){
		echo "DokLinkDAO::processTable should not be called!!! I am handled from JournPstDAO\n";
		die;
	}


	// Consider using DGJM_VEDLEGG to verify that files actually are VEDLEGG
	// NOTE I AM USING *INSERT IGNORE* so I won't add a vedlegg twice
	// I'll pick them up from VEDLEGG first

	// First identify all tupples in FILER that correspond to this JP
	// Before 06 there will be no VEDLEGG attached to incoming documents but there could be some on outgoing documents, however they won't be in the DGJMVEDLEGG table
	// From 06 incoming and outgoing attachments (VEDLEGG) are scanned and you can find details of them in DGJMVEDLEGG
	// The FILER table often has the SCAN user as FILER.OPPRETTETAVID but in DGJMVEDLEGG it is correct so the logic is to pick up the user identified with the file from FILEr
	// and if a corresponding record exists in VEDLEGG then use the person identified there.
	// All we really need to do is get confirmation of a person ID from DGJMVEDLEGG.TKAV
	// We are also assuming the first record associated with JP is the Hoveddokument and anything after that is Vedlegg, so sortby FILID ASC
	// I also need a TITTEL for DOKBESRKIV.XML but pre 06 that does not exist for VEDLEGG and the only TITTEL I can find is DGJMJO.INNH1 and INNH2

	// Should I do a count check to make sure DGJMVEDLEGG has 1 less row than FILER for all vedlegg??
	function processDokLinks ($journPst, $sbhId, $dokBeskDokKat, $dokBeskPapir, $dokBeskLokPapir, $dokBeskStatus) {

		$length = 8;
		
		$oldDokId = null;	
		$currentDokId =  null;
		
		$oldVersion = "ZZZ";
		$currentVersion = null;
		$newVersion = false;
		$detailsSortedByNokkel = array();
		$count=0;


		$dokBeskAGDato = $journPst->JP_AGDATO;
		$dokBeskAGKode = $journPst->JP_AGKODE;
		$dokBeskTittel = $journPst->JP_INNHOLD;
		$dokBeskTGKODE = $journPst->JP_TGKODE;
		$dokBeskUOFF = $journPst->JP_UOFF;
		$dokBeskTGGruppe = $journPst->JP_TGGRUPPE;
 

		$fileListQuery = $this->selectFromFilerQuery . $journPst->JP_ID . "' ORDER BY FILNOKKEL ASC";
		$this->srcBase->createAndExecuteQuery ($fileListQuery);


		while (($result = $this->srcBase->getQueryResult ($fileListQuery))) {

			$currentDokId =  $result['DOKID'];
		
			if (strcmp($currentDokId, $oldDokId) != 0) {			
				$oldDokId = $currentDokId;
				$count++;
				$detailsSortedByNokkel[$count] = array();
			}
			$nokkel = $result['FILNOKKEL'];
			$detailsSortedByNokkel[$count][$nokkel] = $result; 

		}


	
		$dokCount= 1;
		$dlType = 'H';

		foreach ($detailsSortedByNokkel as $row) {
			
//			echo "\nNumber of documents " . count($row) . "\n";
		
			if ($dokCount > 1) {
				$dlType = 'V';
			}
	
			foreach ($row as $versionDetails) {

				$currentVersion = substr($versionDetails['FILNOKKEL'], 0, $length+2);					
			
				if (strcmp($currentVersion, $oldVersion) != 0) {			
					$oldVersion = $currentVersion;			
					//print_r($versionDetails);
					$dokId = $versionDetails['DOKID'];
					$aktiv = $versionDetails['AKTIV'];

					if ($versionDetails['FILNR'] == 1) {
						$this->createDokLink($journPst, $dokCount, $dlType, $versionDetails);
						$this->createDokBesk($dokId, $sbhId, $dokBeskAGDato, $dokBeskAGKode, $dokBeskTittel, $dokBeskTGKODE, $dokBeskUOFF, $dokBeskTGGruppe, $dokBeskDokKat, $dokBeskPapir, $dokBeskLokPapir, $dokBeskStatus);
						$this->createDokVers($dokId, $journPst->JP_TGKODE, $sbhId, $versionDetails['FILNR'], $aktiv);

					} else if ($versionDetails['FILNR'] > 1) {
						$this->createDokVers($dokId, $journPst->JP_TGKODE, $sbhId, $versionDetails['FILNR'], $aktiv);
					}

				}		
			} // foreach ($row as $versionDetails)	
			$dokCount++;
		} // foreach ($detailsSortedByNokkel as $row) 


		$this->srcBase->endQuery($fileListQuery);

	return;

		// $dokInfoArray.Processing of filer table is OK. All required attributes are there. 
		// We assume there is one to one mapping from DOKLINK to DOKBESK although that might not be correct
		// There is a one to many mapping from DOKLINK/DOKBESK to DOKVERS. This is also evident in the FILER table
		$dokInfoArray = array();
		$JP_ID = $journPst->JP_ID;

		$vedleggArray = array();

		// Might have to do a numeric / null check. Seems to be 00 and 02 in some cases. Make sure code handles that	 
		if ($journPst->VEDLEGG > 0) {
			$this->srcBase->createAndExecuteQuery ($this->selectFromVedleggQuery . $JP_ID . "' ORDER BY VEDLNR ASC");
			
			while (($result = $this->srcBase->getQueryResult ($this->selectFromVedleggQuery . $JP_ID . "' ORDER BY VEDLNR ASC"))) {

				$vedleggArray[$result['dokid']] = array ( 'REFAARNR' => $result['REFAARNR'], 
										'VEDLNR' => $result['VEDLNR'], 
										'BESKRIVELSE' => $result['BESKRIVELSE'], 
										'DOKID' => $result['DOKID'], 
										'DOKNAVN' => $result['DOKNAVN'], 
										'DOKODE' => $result['DOKODE'], 
										'TKDATO' => $result['TKDATO'], 
										'TKAV' => $result['TKAV'], 
										'PAPIR' => $result['PAPIR'], 
										'LOKPAPIR' => $result['LOKPAPIR'], 
										'DOKSTATUS' => $result['DOKSTATUS'], 
										'DOKKAT' => $result['DOKKAT'], 
										'PNID' => $result['PNID'], 
										'GRUPPE' => $result['GRUPPE'], 
										'GRUPPEID' => $result['GRUPPEID'], 
										'UNNTOFF' => $result['UNNTOFF'], 
										'HJEMMEL' => $result['HJEMMEL'], 
										'AVGRADER' => $result['AVGRADER'], 
										'AGDATO'  => $result['AGDATO']); 

			}

			$this->srcBase->endQuery($this->selectFromVedleggQuery . $JP_ID . "' ORDER BY VEDLNR ASC");

		} // endif
		// This loop should go through all files connected to a JP


		$this->srcBase->createAndExecuteQuery ($this->selectFromFilerQuery . $JP_ID . "' ORDER BY FILNOKKEL ASC");
		$RNR_COUNT = 1;

		$dokVerskDetails = array();


		$dokLink= new DokLink();
		$dokLink->DL_JPID = $JP_ID;
		$dokLink->DL_RNR = $RNR_COUNT;
		$dokLink->DL_DOKID = $result['DOKID'];


		$oldDokId = null;
		$currentDokId = null;



		//  $dokDetails array ('FILNOKKEL' => FILNOKKEL, 'NUMELEMENTS' => 1|2|3, array ('DOKVERSVALUES' => $dokValValues)

		while (($result = $this->srcBase->getQueryResult ($this->selectFromFilerQuery))) {

			$currentDokId =  $result['DOKID'];

			if ($currentDokId != $oldDokId) {			
				$oldDokId = $currentDokId;
				$filNokkel = substr($filNokkel, 0, Constants::JP_STRING_LENGTH+2);;
			}

			$dokLink->DL_DOKID = $result['DOKID'];


			$versjon = substr($filNokkel, Constants::JP_STRING_LENGTH, 2); 

			$variant = 'P'; // Assuming it's P unless something else tells us it's 'A' 
			if (strlen($filNokkel) ==  Constants::JP_STRING_LENGTH+3) {
				$variant = substr($filNokkel, Constants::JP_STRING_LENGTH+2);
			}


			$dokLink= new DokLink();
			$dokLink->DL_JPID = $JP_ID;
			$dokLink->DL_RNR = $RNR_COUNT;
			$dokLink->DL_DOKID = $result['DOKID'];

//			$dokDetails[$filNokkel] = array  ($versjon => ); 

		}
		$dokLink= new DokLink();
		$dokLink->DL_JPID = $JP_ID;
		$dokLink->DL_RNR = $RNR_COUNT;
		$dokLink->DL_DOKID = $result['DOKID'];

		// Set values used in DokBesk to values from JP
		$dokBeskAGDato = $journPst->AGDATO;
		$dokBeskAGKode = $journPst->AVGRADER;
		$dokBeskTittel = $journPst->INNH1 . " " . $journPst->INNH2;
		$dokBeskUntOff = $journPst->UNTOFF;
		$dokBeskHjemmel = $journPst->HJEMMEL;
		$dokBeskTGGruppe = $journPst->GRUPPEID;
		$dokBeskDokKat = $journPst->DOKKAT; 
		$dokBeskPapir = $journPst->PAPIR;
		$dokBeskLokPapir = $journPst->LOKPAPIR;
		$dokBeskStatus = $journPst->STATUS; 


		// Assume it is a Hoveddokument TYPE of a document			
		$dokLink->DL_TYPE = "H";
		$dokLink->DL_TKDATO = $result['OPPRETTETDATO'];
		$dokLink->DL_TKAV = $result['OPPRETTETAVID'];


		// If there is known vedlegg from DGJMVEDLEGG, then overwrite some values
		if (isset($vedleggArray[$result['DOKID']])) {
			
			$vedleggDetails = $vedleggArray[$result['DOKID']];
			$dokLink->DL_TYPE = "V";
			$dokLink->DL_TKDATO = $vedleggDetails['TKDATO'];
			$dokLink->DL_TKAV = $vedleggDetails['TKAV'];

			// Overwrite values from JP with values from DGJMVEDLEGG
			if ($dokBeskTittel != NULL) {
				$dokBeskTittel = $vedleggDetails['BESKRIVELSE'];
			}
			if ($dokBeskAGDato  != NULL) {
				$dokBeskAGDato = $vedleggDetails['AGDATO'];
			}	
			if ($dokBeskAGKode != NULL) {
				$dokBeskAGKode = $vedleggDetails['AVGRADER'];
			}
			if ($dokBeskTittel != NULL) {
				$dokBeskTittel = $vedleggDetails['BESKRIVELSE'];
			}
			if ($dokBeskUntOff != NULL) {
				$dokBeskUntOff = $vedleggDetails['UNNTOFF'];
			}
			if ($dokBeskHjemmel != NULL) {
				$dokBeskHjemmel = $vedleggDetails['HJEMMEL'];
			}
			if ($dokBeskTGGruppe != NULL) {
				$dokBeskTGGruppe = $vedleggDetails['GRUPPEID']; 
			}
			if ($dokBeskDokKat != NULL) {
				$dokBeskDokKat = $vedleggDetails['DOKKAT'];
			}
			if ($dokBeskPapir != NULL) {
				$dokBeskPapir = $vedleggDetails['PAPIR'];
			}
			if ($dokBeskLokPapir != NULL) {
				$dokBeskLokPapir = $vedleggDetails['LOKPAPIR'];
			}
			if ($dokBeskStatus != NULL) {
				$dokBeskStatus = $vedleggDetails['DOKSTATUS'];
			}
		} // endif

		$RNR_COUNT++;


		// you need to see how to handle versions 

	
		// I need to be able to know if case is not ended and that I have to convert to archive format,
		// It's nearly a subprocess/ subquery again, just to pick up these values.

		// Do you want to check number of answers in result set. If this JP has a single document n P or A format
		// pick up the document from the doc database


		$filNokkel = $result['FILNOEKKEL'];
		// Databases can have varying Constants::JP_STRING_LENGTH, 8 or 10 I have
		// seen so far. So I need to be able to handle both cases. This is something 
		// that should be known in advance and set/update Constants::JP_STRING_LENGTH / Constants::SAK_STRING_LENGTH
		// accordingly

		// iT CAN NEVER BE THIS LENGTH!!!!!
		if (strlen($filNokkel) ==  Constants::JP_STRING_LENGTH) {
			// 07005293 (8 characters). Asuming this is a P variant
			$filVerId = substr($filNokkel, 0, Constants::JP_STRING_LENGTH+2);
			$dokVerskDetails[$RNR_COUNT] =  array ( 'VARIANT' => 'P', 
								'DOKID' => $result['DOKID'],
								'VERSJON' => $result['VEDLNR'],
								'AKTIV' =>  '1',
								'FILVERID' => $filVerId
								); 

		} else if (strlen($filNokkel) ==  Constants::JP_STRING_LENGTH+2) {
			// 0700529301 (10 characters) Asuming this is a P variant
			$filVerId = substr($filNokkel, 0, Constants::JP_STRING_LENGTH+2);
			$dokVerskDetails[$RNR_COUNT] =  array ( 'VARIANT' => 'P', 
								'DOKID' => $result['DOKID'],
								'VERSJON' => $result['VEDLNR'],
								'AKTIV' =>  '1',
								'DOKFORMAT' =>  null,
								'REGAV' => null,
								'TGKODE' => null,
								'FILREF' => null,
								'FILVERID' => $filVerId
								); 	

		} else if (strlen($filNokkel) ==  Constants::JP_STRING_LENGTH+3) {
			// 0700529301A (11 characters), here P or A is given
			$variant = substr($filNokkel, Constants::JP_STRING_LENGTH+2);
			$filVerId = substr($filNokkel, 0, Constants::JP_STRING_LENGTH+2);

			$dokVerskDetails[$RNR_COUNT] =  array ( 'VARIANT' => $variant, 
								'DOKID' => $result['DOKID'],
								'VERSJON' => $result['VEDLNR'],
								'AKTIV' =>  '1',
								'FILVERID' => $filVerId 
								); 

		}

		
		$RNR_COUNT++;


			// Once I have gone through all the versions of a document, I need to check what I can do with it
			// If there are only two, one P, one A, use A
			// If there is only one, P then use converted P
			// If there are two or three, Find each A 
		
		$this->processDokVers($dokVerskDetails);

		writeToDestination($dokLink->DOK_LINK);


		$this->srcBase->endQuery($this->selectFromFilerQuery . $JP_ID . "' ORDER BY FILID ASC");


	}

	function createDokLink($journPst, $rnr, $dlType, $dokDetails) {

		//print_r($dokDetails);
		$dokLink= new DokLink();
		$dokLink->DL_JPID = $journPst->JP_ID;
		$dokLink->DL_RNR = $rnr;
		$dokLink->DL_DOKID = $dokDetails['DOKID'];
		$dokLink->DL_TYPE = $dlType;
		$dokLink->DL_TKDATO = Utility::fixDateFormat($dokDetails['OPPRETTETDATO']); 

		if (is_null($dokDetails['OPPRETTETAVID'])) {
			$dokLink->DL_TKAV = Constants::INGENBRUKER_ID;
			$this->logger->log($this->XMLfilename, "DOKLINK DL_JPID(" . $dokLink->DL_JPID. "), DL_DOKID (". $dokLink->DL_DOKID . ") has no DL_TKAV value. Set to nouser (" . Constants::INGENBRUKER_ID . " however it is likely this is probably the same as NOARKSAK(SA.ANSVID) ", Constants::LOG_WARNING);
			$this->warningIssued = true;
		}
		else {
			$dokLink->DL_TKAV = $dokDetails['OPPRETTETAVID'];
		}

		$this->writeToDestination($dokLink);
	}

	function createDokBesk($dokId, $sbhId, $dokBeskAGDato, $dokBeskAGKode, $dokBeskTittel, $dokBeskTGKODE, $dokBeskUOFF, $dokBeskTGGruppe, $dokBeskDokKat, $dokBeskPapir, $dokBeskLokPapir, $dokBeskStatus) {

	// Handle DokBesk using current values
		$dokBesk = new DokBesk();
		$dokBesk->DB_DOKID = $dokId; 
		$dokBesk->DB_KATEGORI = $dokBeskDokKat; 
		$dokBesk->DB_TITTEL = $dokBeskTittel;
		$dokBesk->DB_PAPIR = $dokBeskPapir;
		$dokBesk->DB_STATUS = $dokBeskStatus; 
		$dokBesk->DB_UTARBAV = $sbhId;
		$dokBesk->DB_UOFF = $dokBeskUOFF;
		$dokBesk->DB_TGKODE = $dokBeskTGKODE;
		$dokBesk->DB_TGGRUPPE = $dokBeskTGGruppe;
		$dokBesk->DB_AGDATO = $dokBeskAGDato;
		$dokBesk->DB_AGKODE = $dokBeskAGKode;

		$this->dokBeskDAO->processDokBeskrivelse($dokBesk);
	}


	function createDokVers($dokId, $tgKode, $regAv, $versjon, $aktiv) {

		$dokVers = new DokVers();
		
		$dokVers->VE_DOKID = $dokId;
		$dokVers->VE_VERSJON = $versjon;
		$dokVers->VE_VARIANT = 'A';
		$dokVers->VE_DOKFORMAT = $this->getDetailsForFileConvertedInfo($this->kommuneName);
		$dokVers->VE_AKTIV = $aktiv;
		$dokVers->VE_REGAV = $regAv;
		$dokVers->VE_TGKODE = $tgKode;
		$dokVers->VE_LAGRENH = Constants::LAGRENHET;
		$dokVers->VE_FILREF = "test";
 //TODO .... FIX!!!!!!!!!!!!!!!!!!!!!!!
//$this->lookUpFileFromDatabase($dokId, $this->kommuneName);

		$this->dokVersDAO->processDokVers($dokVers);
	}
			

	function lookUpFileFromDatabase($dokId, $kommune) {

		$fullFileName = "";
		$fileLocation = "";

		$fileFindQuery = "select * from FileOverview.fileOverview where filename LIKE '" . $dokId  . "%' AND kommune = '" . $kommune . "'  ORDER BY fullfileName DESC";

		$result = $this->uttrekksBase->executeQueryFetchResultHandle ($fileFindQuery);
		$numRows = mysql_num_rows($result);
			
		if ($numRows > 1) {

			// Pick up the last one, but we're gonna have to check this a bit
			while ($row = mysql_fetch_array($result)) {
//				print_r($row);
				$fullFileName = $row['fullfileName'];
				$fileLocation = $row['location'];
			}
		}
		else {
			$row = mysql_fetch_array($result);
			$fullFileName = $row['fullfileName'];
			$fileLocation = $row['location'];
		}

		$this->uttrekksBase->freeHandle($result);
		
		return $fileLocation . $fullFileName;
	}
	
	function getDetailsForFileConvertedInfo($dokId) {

		return "PDF";

		/*$selSelectQuery = "SELECT ArkivFormat, fileConvertedExtension from ConvertProcessInfo where kommuneBase='" . $kommuneBase . "' AND fileName ='" . $dokId . "';"; 
		return $this->uttrekksBase->executeQuery ($selSelectQuery);*/ 
	}


	// 07005293 01 A

	// If 10 characters, no letter then its production
	// Maybe find the file and double check in archive format with tool Dimitar mentioned

	// If 11 characters and Letter, A or P, 
	// If A then and 01, look for 01P. Think serial procesing, can't jump
	//   do a file confirm (maybe do filedonfirm in advance and add to db)
	//
	// 
	//

		



	function processDokVers($dokVerskDetails) {

		$dokVersDAO->processDokVers($dokVerskDetails);
	}

	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO DOKLINK (DL_JPID, DL_RNR, DL_DOKID, DL_TYPE, DL_TKDATO, DL_TKAV) VALUES (";
	
//if (substr($dokLink->DL_JPID, 0, 2) > 30 )
//    		$sqlInsertDokLink .= "'19" . $dokLink->DL_JPID . "', ";
//    	else 
//    		$sqlInsertDokLink .= "'20" . $dokLink->DL_JPID . "', ";
    	    	

		$sqlInsertStatement .= "'" . $data->DL_JPID . "', ";						
		$sqlInsertStatement .= "'" . $data->DL_RNR . "', ";
		$sqlInsertStatement .= "'" . $data->DL_DOKID . "', ";
		$sqlInsertStatement .= "'" . $data->DL_TYPE . "', ";
		$sqlInsertStatement .= "'" . $data->DL_TKDATO . "', ";
		$sqlInsertStatement .= "'" . $data->DL_TKAV . "'";			
	
		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

	}

    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM DOKLINK";
    	$mapping = array ('idColumn' => 'dl_jpid', 
				'rootTag' => 'DOKLINK.TAB',	
			    		'rowTag' => 'DOKLINK',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'DL.JPID' => 'dl_jpid',
							'DL.RNR' => 'dl_rnr',
							'DL.DOKID' => 'dl_dokid',
							'DL.TYPE' => 'dl_type',
							'DL.TKDATO' => 'dl_tkdato',
							'DL.TKAV' => 'dl_tkav'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			
/*




		$vedleggArray  = null;
		while (($result = $this->srcBase->getQueryResult ($this->selectFromFilerQuery))) {

				$dokLink= new DokLink();
				$dokLink->DL_JPID = $JP_ID;
				$dokLink->DL_RNR = $RNR_COUNT;
				$dokLink->DL_DOKID = $result['DOKID'];

 				if ($RNR_COUNT == 1) {
					$dokLink->DL_TYPE = "H";
					$this->logger->log($this->XMLfilename, "Assuming first file encountered in FILER for DOKID = " . $dokLink->DL_DOKID . " has DL.TYPE H" , Constants::LOG_INFO);
				}
				else {
					$dokLink->DL_TYPE = "V";
					$this->logger->log($this->XMLfilename, "Assuming subsequent files encountered in FILER for DOKID = " . $dokLink->DL_DOKID . " has DL.TYPE V. current RNR is " . $RNR_COUNT, Constants::LOG_INFO);
				}

				$dokLink->DL_TKDATO = $result['OPPRETTETDATO'];
				$dokLink->DL_TKAV = $result['OPPRETTETAVID'];


				$dokLinkArray[$RNR_COUNT]->

				$dokInfoArray[$RNR_COUNT]->HACK_TITTEL =   $journPst->INNH1 . " " . $journPst->INNH2;
 				$dokInfoArray[$RNR_COUNT]->HACK_AGDATO =   $journPst->AGDATO; 
				$dokInfoArray[$RNR_COUNT]->HACK_AGKODE =   $journPst->AVGRADER;
				$dokInfoArray[$RNR_COUNT]->HACK_UNTOFF =   $journPst->UNTOFF;
				$dokInfoArray[$RNR_COUNT]->HACK_PAPIR =    $journPst->HJEMMEL;
				$dokInfoArray[$RNR_COUNT]->HACK_TGGRUPPE = $journPst->GRUPPEID;
				$dokInfoArray[$RNR_COUNT]->DOK_LINK = $dokLink;
		
				$RNR_COUNT++;
		}
		


		// Overwrite the TKAV/TKDATO if it's in the DGJMVEDLEGG table
		if ($journPst->JP_ANTVED > 0) {
			$this->srcBase->createAndExecuteQuery ($this->selectFromVedleggQuery . $JP_ID . "' ORDER BY VEDLNR ASC");
			// We ignore the first document which is a HOVEDDOKUMENT and from 2 onwards is VEDLEGG
			$RNR_COUNT = 2;
			while (($result = $this->srcBase->getQueryResult ($this->selectFromVedleggQuery))) {

					if ($dokLinkArray[$RNR_COUNT]->DL_DOKID != $result['DOKID']) {
						echo "JP (" . $result['REFAARNR'] . ") RNR (" . $RNR_COUNT . ") DOKID (" . $dokLinkArray[$RNR_COUNT]->DL_DOKID . ") NOT EQUAL DOKID (" . $result['DOKID'] . ")\n" ;
						die;
					}

					$dokLinkArray[$RNR_COUNT]->DL_TKDATO =     $result['TKAV'];
					//$dokLinkArray[$RNR_COUNT]->DL_TKAV =       $result['TKDATO'];
					//$dokLinkArray[$RNR_COUNT]->HACK_TITTEL =   $result['BESKRIVELSE'];
				//TODO:	$dokLinkArray[$RNR_COUNT]->HACK_AGDATO =   $result['AGDATO']; 
				//	$dokLinkArray[$RNR_COUNT]->HACK_AGKODE =   $result['AVGRADER'];
				//	$dokLinkArray[$RNR_COUNT]->HACK_UNNTOFF =  $result['UNNTOFF'];
				//	$dokLinkArray[$RNR_COUNT]->HACK_PAPIR =    $result['PAPIR'];
				//	$dokLinkArray[$RNR_COUNT]->HACK_TGGRUPPE = $result['GRUPPEID'];

					$RNR_COUNT++;	
			}
			// Assuming the person who added the VEDLEGG also added the HOVEDDOKUMENT
// TODO			$dokLinkArray[1]->DL_TKAV = $dokLinkArray[$RNR_COUNT]->DL_TKAV;
			$this->srcBase->endQuery($this->selectFromVedleggQuery . $JP_ID . "' ORDER BY VEDLNR ASC");
		} 

		foreach ($dokInfoArray as $dokLink) {
			// Is there a $dokLinkArray[0] ?
			if ($dokLink != null) {
				processDokBeskriv($journPst, $filerInfo, $dokLink);
 				processDokVers($journPst, $filerInfo);
				writeToDestination($dokLink->DOK_LINK);
			}
		}



*/
