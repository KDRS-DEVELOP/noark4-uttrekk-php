<?php

// TODO: You're gonna process ANTJP as a select count(*) from DGJMJO where JOURAARNR = "SAKID
// That way you can do a single process of the table

require "NoarkSak.php";
require_once 'utility/Utility.php';


 // When processing this file, you come across data that is used in other tables. 
 // each case file should result in an update og ORDNVERD using OKODE1 and OTYPE as new 


class NoarkSakDAO extends Noark4Base {

	protected $ordVerdDAO;
	protected $merknadDAO;
	protected $logger;


	public function __construct  ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger, $ordVerdDAO, $merknadDAO) {
		parent::__construct (Constants::getXMLFilename('NOARKSAK'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);		
		$this->ordVerdDAO = $ordVerdDAO;
		$this->merknadDAO = $merknadDAO;
		$this->selectQuery = "select JOURAARNR, JOURAAR, JOURNR, PAPIR, AAPNET, INNH1, INNH2, MERKNAD, U1, STATUS, FRAARKDEL, SAKTYPE, SAKART, JOURENHET, OTYPE, OKODE1, UNAVN, ADMID, HJEMMEL from " . $SRC_TABLE_NAME . "";
	} 
		

	// Handle INNH2?????
	function processTable() {
			
			// TODO: ER INNH1 = Tittel og INNH2 = offentlig Tittel?
			// U1=1 for 5 6 7, U1 = null for 8 9 10 11
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		$counter = 1;
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
						
	            		$noarkSak = new  NoarkSak();
	          
				$noarkSak->SA_ID = $result['JOURAARNR'];
				$noarkSak->SA_SAAR = $result['JOURAAR'];
				
				
				$noarkSak->SA_SEKNR = $result['JOURNR'];
				$noarkSak->SA_PAPIR = $result['PAPIR'];
				$noarkSak->SA_DATO = Utility::fixDateFormat($result['AAPNET']);



				// Here update ORDNVRD table

				$noarkSak->SA_TITTEL = addslashes($result['INNH1']);
				
				if ($result['U1'] == null)
					// TODO: srcBasen bruker null der Noark bruker 0			
					$noarkSak->SA_U1 = 0;
				else
					$noarkSak->SA_U1 = $result['U1'];
	
				// TODO: 'A' og 'B' verdier, hvilke verdier kan det være
				$noarkSak->SA_STATUS = $result['STATUS'];
				
				// TODO: FRAARKDEL er altid null men skal avleveres, er dette noe som må settes manuelt
				// dvs at det er kun en arkivdel i denne basen?
				// Tabellen arkivdel og ARKIVDEL.XML DTD må da også lages  
				//$noarkSak->SA_ARKDEL = $result['FRAARKDEL'];
				
				// TODO: Alle SAKTYPE er == STD, 
				// SAKART har STD, MOT
				// Tabellen Sakstype og SAKTYPE.XML/DTD må lages 
				$noarkSak->SA_TYPE = substr($result['SAKTYPE'] . $result['SAKART'], 0, 10);
				
				// TODO: Alle JOURENHET == null
				// Tabellen Jourenhet og JOURNENH.XML/DTD må lages
				$noarkSak->SA_JENHET = "DUMMY";//$result[''];
				
				// Tror dette er OTYPE
				// EM == Emnebasert??
				// GB == Grunnbok?
				// Navn
				// FD 
				// null ?? Det er 20507 som er == null, 37064 totalt			
		//		$noarkSak->SA_OPSAKSDEL = $result['OTYPE'];
				
				
				// Verdier 116, 120, 115, 107, 110, 105, 114, 111, 121, 113
				// Jeg er sikker på at det er en tabell som matcher disse			
				// Er dette AVD? Verdier her er KL, NN, FT			
				$noarkSak->SA_ADMID = $result['ADMID'];
				
				// SBHID, 19 forskjellige saksbehandlere men også 194 tupler med null 
				$noarkSak->SA_ANSVID = "DUMMY";//$result[''];
				
				// TODO: usikker hvor den får verdien, men er heller ikke obligatorisk, spørs om vi har dette i basen. Se også $noarkSak->SA_TGGRUPPE
				$noarkSak->SA_TGKODE = "DUMMY";//$result[''];
				
				$noarkSak->SA_UOFF = $result['HJEMMEL'];
				
				// TODO: Dette er noe som ikke er i basen tror jeg, se også $noarkSak->SA_TGKODE
				//$noarkSak->SA_TGGRUPPE = $result[''];
	
				// Tror vi blir nødt til å slå opp JP tabellen for å hente disse verdiene
				// TODO: Er dette det samme som SAK siste endret dato eller er jeg nødt til å sjekke JPOST tabellen?
				// VIKTIG å sjekke dette og ikke anta det!!! SISTEDOK, AGDATO,  OVDATO
				//$noarkSak->SA_SISTEJP = $result[''];
				$noarkSak->SA_ANTJP = 0;//$result[''];
				
				// Usikker på denne men det er Antall år saken skal oppbeprotectedes før kassasjon eller annen aksjon i henhold til kassasjonskoden skal foretas.
				// Basen har en BEVTID men de er alle null 			
				// TODO: Se her
				$noarkSak->SA_BEVTID = 0;//$result[''];
				
				// KASSAJON er lik null for all tupler
				// TODO: Se her
				$noarkSak->SA_KASSKODE = "DUMMY";//$result[''];
				// Alle verdiene i basen er null
				// TODO: Se her
				$noarkSak->SA_KASSDATO = ""; //$result[''];
				
				// Skal bevtid og kassdato kasskode tilegnes en verid i det uttrekket lages?
				// Referanse til et ekstern system hvis det eksisterer. Eksisterer det.
				$noarkSak->SA_PROSJEKT = "DUMMY";//$result[''];
				
				
				$noarkSak->SA_PRES = "DUMMY";//$result[''];
				
				// Hvis dette er en arkivdel så skal denne ikke være med
				$noarkSak->SA_FRARKDEL = "DUMMY";//$result[''];
				
				// Ingen saker i basen er utlånt 
				$noarkSak->SA_UTLDATO = null; //$result[''];
				$noarkSak->SA_UTLTIL = null; //$result[''];

				// It appears that ESA has some merknad as part of a sak. These have to copied to MERKNAD 

				$nokkel = $noarkSak->SA_ID;
				$untoff = $noarkSak->SA_TGKODE;
				$gruppeId = $noarkSak->SA_TGGRUPPE;
				$merknad = $result['MERKNAD'];
				$sbhId = $noarkSak->SA_ANSVID;


// This is where you are. fixing this->ordVerdDAO->addOrdnVerdi($ordPrinsipp, $ordVerdi, $ordBeskrivelse);
// yo ucn pick up some values OTYPE e.g GB/
// Not all OTYPE are registered. EM/SA e.g so yo will need to manually add these to ORDPRINSIPP

/*				$ordPrinsipp = "DUMMY";
				$ordVerdi = "DUMMY";
				$ordBeskrivelse = "DUMMY";
				$this->ordVerdDAO->addOrdnVerdi($ordPrinsipp, $ordVerdi, $ordBeskrivelse);
*/
				$this->writeToDestination($noarkSak);

				$register = "S";

//				echo "NOARKSAK ID is " . $noarkSak->SA_ID . "\n";
 
				if (isset($merknad) == true) {
					$this->merknadDAO->processMerknadFromSakOrJP($register, $nokkel, $untoff, $gruppeId, $merknad, $sbhId);
				}

				if ($counter++ %Constants::DOT_MARKER_COUNT == 0)
					echo ".";
			}

		$this->srcBase->endQuery($this->selectQuery);

    } // function getSak($SA_ID)
    
    
    function writeToDestination($data) {

		$sqlInsertSak = "INSERT INTO NOARKSAK (SA_ID, SA_SAAR, ". /* SA_SEKNR,*/ " SA_PAPIR, SA_DATO, SA_TITTEL, SA_U1, SA_STATUS, SA_ARKDEL, SA_TYPE, SA_JENHET,  SA_ADMID, SA_ANSVID, SA_TGKODE, SA_UOFF, SA_ANTJP, SA_BEVTID, SA_KASSKODE, SA_KASSDATO, SA_PROSJEKT, SA_PRES,  SA_FRARKDEL, SA_UTLDATO, SA_UTLTIL) VALUES (";

		
		// We are asking if the year is greater than 2030, hopefully no kommune will use ESA in 2030!		
		//if (substr($data->SA_ID, 0, 2)  > 30)
		//	$sqlInsertSak .= "'19" . $data->SA_ID . "', ";	
	//	else
	//		$sqlInsertSak .= "'20" . $data->SA_ID . "', ";
		    	
		$sqlInsertSak .= "'" . $data->SA_ID . "', ";
		$sqlInsertSak .= "'" . $data->SA_SAAR . "', ";
		// TODO : Check required $sqlInsertSak .= "'" . $noarkSak->SA_SEKNR . "', ";
		$sqlInsertSak .= "'" . $data->SA_PAPIR . "', ";
		$sqlInsertSak .= "'" . $data->SA_DATO . "', "; 
		$sqlInsertSak .= "'" . $data->SA_TITTEL . "', ";			
		$sqlInsertSak .= "'" . $data->SA_U1 . "', ";
		$sqlInsertSak .= "'" . $data->SA_STATUS . "', ";
		$sqlInsertSak .= "'" . $data->SA_ARKDEL . "', ";
		$sqlInsertSak .= "'" . $data->SA_TYPE . "', ";
		$sqlInsertSak .= "'" . $data->SA_JENHET . "', ";
		$sqlInsertSak .= "'" . $data->SA_ADMID . "', ";
		$sqlInsertSak .= "'" . $data->SA_ANSVID . "', ";
		$sqlInsertSak .= "'" . $data->SA_TGKODE . "', ";
		$sqlInsertSak .= "'" . $data->SA_UOFF . "', ";
		$sqlInsertSak .= "'" . $data->SA_ANTJP . "', ";
		$sqlInsertSak .= "'" . $data->SA_BEVTID . "', ";
		$sqlInsertSak .= "'" . $data->SA_KASSKODE . "', ";
		$sqlInsertSak .= "'" . Utility::fixDateFormat($data->SA_KASSDATO) . "', "; 
		$sqlInsertSak .= "'" . $data->SA_PROSJEKT . "', ";
		$sqlInsertSak .= "'" . $data->SA_PRES . "', ";
		$sqlInsertSak .= "'" . $data->SA_FRARKDEL . "', "; 
		$sqlInsertSak .= "'" . Utility::fixDateFormat($data->SA_UTLDATO) . "', "; 
		$sqlInsertSak .= "'" . $data->SA_UTLTIL . "'";

		$sqlInsertSak .= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertSak);
    }
    
    function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM NOARKSAK";
    	$mapping = array ('idColumn' => 'sa_id', 
				'rootTag' => 'NOARKSAK.TAB',	
			    		'rowTag' => 'NOARKSAK',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'SA.ID' => 'sa_id',
							'SA.SAAR' => 'sa_saar',
							'SA.SEKNR' => 'sa_seknr',
							'SA.PAPIR' => 'sa_papir',
							'SA.DATO' => 'sa_dato',
							'SA.TITTEL' => 'sa_tittel',
							'SA.U1' => 'sa_u1',
							'SA.STATUS' => 'sa_status',
							'SA.ARKDEL' => 'sa_arkdel',
							'SA.TYPE' => 'sa_type',
							'SA.JENHET' => 'sa_jenhet',
							'SA.OPSAKSDEL' => 'sa_opsaksdel',
							'SA.ADMID' => 'sa_admid',
							'SA.ANSVID' => 'sa_ansvid',
							'SA.TGKODE' => 'sa_tgkode',
							'SA.UOFF' => 'sa_uoff',
							'SA.TGGRUPPE' => 'sa_tggruppe',
							'SA.SISTEJP' => 'sa_sistejp',
							'SA.ANTJP' => 'sa_antjp',
							'SA.BEVTID' => 'sa_bevtid',
							'SA.KASSKODE' => 'sa_kasskode',
							'SA.KASSDATO' => 'sa_kassdato',
							'SA.PROSJEKT' => 'sa_prosjekt',
							'SA.PRES' => 'sa_pres',
							'SA.FRARKDEL' => 'sa_frarkdel',
							'SA.UTLDATO' => 'sa_utldato',
							'SA.UTLTIL' => 'sa_utltil'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }

    function updateSakAntallJP($sakID, $antallJPForSak) {
  		return $this->uttrekksBase->executeStatement("UPDATE NOARKSAK SET SA_ANTJP = " . $antallJPForSak . " WHERE SA_ID = '". $sakID . "'");  	
    } 
 
/*   
    function endQuery() {
		$this->srcBase->endQuery ($this->noarkQuery);
		$this->sakInitialised = false;		    	    	
 	   }
 
	function antallSaker() {
		$sqlQueryAntallSaker = "select COUNT (JOURAARNR) FROM DGSMSA";
		$row = $this->srcBase->executeQueryAndGetResult($sqlQueryAntallSaker);		
		return  $row['COUNT(JOURAARNR)'];
	} 
*/		
}
//class $noarkSakDAO 
?>