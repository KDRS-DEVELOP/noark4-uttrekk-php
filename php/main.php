	
<?php
	
	require_once "database/SrcDB.php";
	require_once "database/uttrekkMySQLBase.php";
	require_once "database/MySQLDBParameters.php";
	require_once "database/noarkDatabaseStruktur.php";

	require_once 'extraction/Extractor.php';
	require_once 'extraction/NoarkIHCreator.php'; 	
  	require_once "models/ExportInfo.php";

	require_once 'utility/Constants.php';
	require_once 'utility/Logger.php';

	require_once 'models/AdminDelDAO.php';;
	require_once 'models/AdrAdmEnhDAO.php';	
	require_once 'models/AdressekpDAO.php';
	require_once 'models/AdrPersonDAO.php';
	require_once 'models/AdrTypeDAO.php';	
	require_once 'models/AliasAdmDAO.php';
	require_once 'models/ArkivDAO.php';
	require_once 'models/ArkivdelDAO.php';
	require_once 'models/ArkivPerDAO.php';
	require_once 'models/ArStatusDAO.php';
	require_once 'models/AvgrKodeDAO.php';
	require_once 'models/AvskrmDAO.php';	
	require_once 'models/AvsmotDAO.php';	
	require_once 'models/BskodeDAO.php';	
	require_once 'models/DokKatDAO.php';
	require_once 'models/EarkKodeDAO.php';
	require_once 'models/DokBeskDAO.php';
	require_once 'models/DokLinkDAO.php';
	require_once 'models/DokKatDAO.php';
	require_once 'models/DokStatDAO.php';	
	require_once 'models/DokTilknDAO.php';
	require_once 'models/DokTypeDAO.php';
	require_once 'models/DokVersDAO.php';
	require_once 'models/EarkKodeDAO.php';  
	require_once 'models/EnhTypeDAO.php';
//	require_once 'models/FilerDAO.php';
	require_once 'models/FStatusDAO.php';
//	require_once 'models/Filer.php';
	require_once 'models/ForsmateDAO.php';
	require_once 'models/FStatusDAO.php';   
	require_once 'models/InfoTypeDAO.php';
	require_once 'models/JenArkdDAO.php';
	require_once 'models/JournEnhDAO.php';
	require_once 'models/JournPstDAO.php';	
	require_once 'models/JournStaDAO.php';
	require_once 'models/KassKodeDAO.php';
	require_once 'models/KlassDAO.php';	
	require_once 'models/LagrEnhDAO.php';
	require_once 'models/LagrFormDAO.php';
	require_once 'models/MedadrgrDAO.php'; 
	require_once 'models/MerknadDAO.php';
	require_once 'models/NoarkSakDAO.php';
	require_once 'models/NumserieDAO.php';
	require_once 'models/OpriTypDAO.php';
	require_once 'models/OrdnPriDAO.php';
	require_once 'models/OrdnVerdDAO.php';
	require_once 'models/PersonDAO.php';
	require_once 'models/PerNavnDAO.php';
	require_once 'models/PerRolleDAO.php';
	require_once 'models/PerklarDAO.php';
	require_once 'models/PolsakgDAO.php'; 
	require_once 'models/PostnrDAO.php';
	require_once 'models/SakStatDAO.php';
	require_once 'models/SakPartDAO.php';
	require_once 'models/SakTypeDAO.php';
	require_once 'models/StatMDokDAO.php';
	require_once 'models/TggrpDAO.php';
	require_once 'models/TghjemDAO.php';
	require_once 'models/TginfoDAO.php';
	require_once 'models/TgkodeDAO.php';
	require_once 'models/TgmedlemDAO.php';
	require_once 'models/TilleggDAO.php';
	require_once 'models/TlKodeDAO.php';
	require_once 'models/UtDokTypDAO.php';
	require_once 'models/UtvBehDAO.php';
	require_once 'models/UtvBehDoDAO.php';
	require_once 'models/UtvBehStatDAO.php';
	require_once 'models/UtvalgDAO.php';
	require_once 'models/UtvBehStatDAO.php';
	require_once 'models/UtDokTypDAO.php';
	require_once 'models/UtvMedlDAO.php';
	require_once 'models/UtvMedlFunkDAO.php';
	require_once 'models/UtvMoteDAO.php';
	require_once 'models/UtvSakDAO.php';
	require_once 'models/UtvSakTyDAO.php';
	require_once 'models/VarFormDAO.php';
	//require_once 'extraction/NoarkIHCreator.php';

	
	$options = getopt("d::");

	if (isset($options["d"]) == false) {
			echo "ORACLE SID (til kildebasen) ikke angitt \n";
//			return;
	}

	if (isset($options["k"]) == false) {
			echo "Kommune navn ikke angitt \n";
			return;
	}
	
	$ORACLE_SID = "ORACLE_SID=" . $options["d"];
	putenv ($ORACLE_SID);
	$src_db_sid  = $options["d"];
	$kommuneName = $options["d"];


	$table_names = parse_ini_file("ini/table_names.ini");
	
	//	if the file doesn't exsist stop!!!
	$src_db_ini_array = parse_ini_file("ini/src_db.ini");
	
	$src_db_host = $src_db_ini_array['src_db_host'];
	$src_db_port = $src_db_ini_array['src_db_port'];
	$src_db_name = $src_db_ini_array['src_db_name'];
	$src_db_user = $src_db_ini_array['src_db_user'];
	$src_db_pswd = $src_db_ini_array['src_db_pswd'];
	$src_db_sid =  $src_db_ini_array['src_db_sid'];
	
	$srcBase = null;
	
	
	// if the file doesn't exsist stop!!!
	$uttrekk_db_ini_array = parse_ini_file("ini/destination_db.ini");
		
	$uttrekk_db_host = $uttrekk_db_ini_array['uttrekk_db_host'];
	$uttrekk_db_user = $uttrekk_db_ini_array['uttrekk_db_user'];
	$uttrekk_db_pswd = $uttrekk_db_ini_array['uttrekk_db_pswd'];
	$uttrekk_db_database = $uttrekk_db_ini_array['uttrekk_db_database'];
	
	print_r($uttrekk_db_ini_array);
	print_r($src_db_ini_array);
	print_r($table_names);
	
	echo "\n";
	
	$uttrekkMySQLBase = null;
	
	$extractor = null;
	$uttrekkDirectory = "./uttrekksfiler";
	$noarkIHoutputDir = "./";
	
	// Main program starts here
	
	$uttrekkMySQLBase = null;
	
	try {
		$uttrekkMySQLBase = new UtrekkMySQLBase($uttrekk_db_host, $uttrekk_db_user, $uttrekk_db_pswd, $uttrekk_db_database);

	$srcBase = new SrcBase($src_db_host, $src_db_port, $src_db_name, $src_db_user, $src_db_pswd, $src_db_sid);
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
	}
	
	if ($srcBase == null) {
		echo "Problem med kobling til kildebasen\n";
		return;
	}
	
	if ($uttrekkMySQLBase == null) {
		echo "Problem med kobling til Uttrekksbasen\n";
		return;
	}
	
	$databaseParameters = new MySQLDBParameters($uttrekk_db_host, 3306, $uttrekk_db_database, $uttrekk_db_user, $uttrekk_db_pswd);
	$extractor = new Extractor("mysql", $databaseParameters, $uttrekkDirectory);

	$extractor->deleteDirectoryAndContents();
	$extractor->createDirectory ();
	
	$logDir = $extractor->getLogDir();
	$logger = new Logger($logDir, false, false, true);
 

	
	echo "\nSlettet gamle filer (hvis de eksisterte) og oppretter mappe for uttrekk ($uttrekkDirectory) \n";
	
	$noark4DatabaseStruktur = new Noark4DatabaseStruktur();

	// Temporary commented out as it takes to long to rebuild everything
			
	// Noe administrativt arbeid først, slett databasen om den eksisterer, lag en ny tom en og lag alle tabellene
	echo "Sletter MySQL midlertidig Noark 4 base. Resultatet er (";
	$val = $uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->deleteDatabaseStatement($uttrekk_db_database));
	echo ($val == true  ? 'OK' : 'Feil' ) . ");\n";
	echo "Oppretter MySQL midlertidig Noark 4 base. Resultatet er (";
	$val = $uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDatabaseStatement($uttrekk_db_database));
	echo ($val == true  ? 'OK' : 'Feil' ) . ");\n";
	
	$uttrekkMySQLBase->setDefaultDatabase();
	
	
	$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->deleteDatabaseStatement($uttrekk_db_database));
	$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDatabaseStatement($uttrekk_db_database));

	$uttrekkMySQLBase->setDefaultDatabase();
	
	createNoark4DBStructure ($uttrekkMySQLBase, $noark4DatabaseStruktur);
	

	
	
	
	echo "Starter overføring fra Kildebasen til MySQL\n";
	
	// Å lage mysql.NOARKSAK er et to-stegs prosess. I kilde.NOARKSAK (DGSMSA) så mangler vi
	// SA.ANTJP (Antall Journalposter) og SA.SISTEJP (Siste JournalDato)
	// Steg 1 er å lage mysql.NOARKSAK utifra kilde.NOARKSAK
	// Steg 2 er å hente SA.ANTJP og SA.SISTEJP mens vi prossesere kilde.NOARKSAK
	
	//Start with the easy tables to create, the ones with a one-to-one mapping from the src database
	//Tables are processed in an order that ensures Relational dependancy is not broken  



//	$tablesToTruncate = array(/*'DOKBESK', 'DOKLINK', 'DOKVERS', 'JOURNPST',*/ 'POLSAKG', 'UTVALG', 'UTVMEDL', 'UTVMOTEDOK', 'UTVMOTE', 'UTVSAK', 'UTVBEH', 'UTVBEHDO');


	//foreach ($tablesToTruncate as $table) {
	//	$SQLStatement = "TRUNCATE " . $table . "; ";
	//	$uttrekkMySQLBase->executeStatement($SQLStatement);
	//	echo $SQLStatement . "\n";
	//}


	

	
	$postnrDAO = handlePOSTNR($srcBase, $uttrekkMySQLBase, $table_names['POSTNR_TABLE'], $logger);
	
	$earkKode = handleEARKKODE($srcBase, $uttrekkMySQLBase, $table_names['EARKKODE_TABLE'], $logger);

	$adrTypeDAO = handleADRTYPE($srcBase, $uttrekkMySQLBase, $table_names['ADRTYPE_TABLE'], $logger);
	$adressekpDAO = handleADRESSEKP($srcBase, $uttrekkMySQLBase, $table_names['ADRESSEKP_TABLE'], $logger);
		
	$adminDelDAO = handleADMINDEL($srcBase, $uttrekkMySQLBase, $table_names['ADMINDEL_TABLE'], $logger);
	$avgrKodeDAO = handleAVGRKODE($srcBase, $uttrekkMySQLBase, $table_names['AVGRKODE_TABLE'], $logger);
	$avskrmDAO = handleAVSKRM($srcBase, $uttrekkMySQLBase, $table_names['AVSKRM_TABLE'], $logger);
	$dokkatDAO = handleDOKKAT($srcBase, $uttrekkMySQLBase, $table_names['DOKKAT_TABLE'], $logger);
	$dokstatDAO = handleDOKSTAT($srcBase, $uttrekkMySQLBase, $table_names['DOKSTAT_TABLE'], $logger);
	$dokTypeDAO = handleDOKTYPE($srcBase, $uttrekkMySQLBase, $table_names['DOKTYPE_TABLE'], $logger);
	$kassKodeDAO = handleKASSKODE($srcBase, $uttrekkMySQLBase, $table_names['KASSKODE_TABLE'], $logger);
	$infoTypeDAO = handleINFOTYPE($srcBase, $uttrekkMySQLBase, $table_names['INFOTYPE_TABLE'], $logger);
	$lagrFormDAO = handleLAGRFORM($srcBase, $uttrekkMySQLBase, $table_names['LAGRFORM_TABLE'], $logger);
	$lagrEnhDAO = handleLAGRENH($srcBase, $uttrekkMySQLBase, $table_names['LAGRENH_TABLE'], $logger);
	$personDAO = handlePERSON($srcBase, $uttrekkMySQLBase, $table_names['PERSON_TABLE'], $logger);
	$perNavnDAO = handlePERNAVN($srcBase, $uttrekkMySQLBase, $table_names['PERNAVN_TABLE'], $logger);
	
	$fstatusDAO = handleFSTATUS($srcBase, $uttrekkMySQLBase, $table_names['FSTATUS_TABLE'], $logger);
	$forsmateDAO = handleFORSMATE($srcBase, $uttrekkMySQLBase, $table_names['FORSMATE_TABLE'], $logger);
	$sakStatDAO = handleSAKSTAT($srcBase, $uttrekkMySQLBase, $table_names['SAKSTAT_TABLE'], $logger);
	$sakTypeDAO = handleSAKTYPE($srcBase, $uttrekkMySQLBase, $table_names['SAKTYPE_TABLE'], $logger);	
	$varFromDAO = handleVARFORM($srcBase, $uttrekkMySQLBase, $table_names['VARFORM_TABLE'], $logger);
	$opriTypDAO = handleOPRITYP($srcBase, $uttrekkMySQLBase, $table_names['OPRITYP_TABLE'], $logger);
	$ordnpriDAO = handleORDNPRI($srcBase, $uttrekkMySQLBase, $table_names['ORDNPRI_TABLE'], $logger);
	$ordVerdDAO = handleORDNVERD($srcBase, $uttrekkMySQLBase, $table_names['ORDNVERD_TABLE'], $logger);	
	$journEnhDAO = handleJOURNENH($srcBase, $uttrekkMySQLBase, $table_names['JOURNENH_TABLE'], $logger);
	$journStaDAO = handleJOURNSTA($srcBase, $uttrekkMySQLBase, $table_names['JOURNSTA_TABLE'], $logger);
	$bskodeDAO = handleBSKODE($srcBase, $uttrekkMySQLBase, $table_names['BSKODE_TABLE'], $logger);
	$numSerieDAO = handleNUMSERIE($srcBase, $uttrekkMySQLBase, $table_names['NUMSERIE_TABLE'], $logger);
	$arkivDAO = handleARKIV($srcBase, $uttrekkMySQLBase, $table_names['ARKIV_TABLE'], $logger);
	$arstatusDAO = handleARSTATUS($srcBase, $uttrekkMySQLBase, $table_names['ARSTATUS_TABLE'], $logger);
	$arkivdelDAO = handleARKIVDEL($srcBase, $uttrekkMySQLBase, $table_names['ARKIVDEL_TABLE'], $logger);
	$arkivperiodeDAO = handleARKIVPERIODE($srcBase, $uttrekkMySQLBase, $table_names['ARKIVPER_TABLE'], $logger);
	$jenArkdDAO = handleJENARKD($srcBase, $uttrekkMySQLBase, $table_names['JENARKD_TABLE'], $logger);
	$perRolleDAO = handlePERROLLE($srcBase, $uttrekkMySQLBase, $table_names['PERROLLE_TABLE'], $logger);


	$noarkSakDAO = handleNOARKSAK($srcBase, $uttrekkMySQLBase, $table_names['NOARKSAK_TABLE'], $logger, $ordVerdDAO, 
					new MerknadDAO($srcBase, $uttrekkMySQLBase, $table_names['MERKNAD_TABLE'], $logger));
	
 
	$merknadDAO = handleMERKNAD($srcBase, $uttrekkMySQLBase, $table_names['MERKNAD_TABLE'], $logger);

	$journPstDAO = handleJOURNPST($srcBase, $uttrekkMySQLBase, $table_names['JOURNPOST_TABLE'], $logger, $merknadDAO, $kommuneName);

	
	
	// after sak and JP do the MERKNAD
//	$merknadDAO = handleMERKNAD($srcBase, $uttrekkMySQLBase, $table_names['MERKNAD_TABLE']);
	$tgInfoDAO = handleTGINFO($srcBase, $uttrekkMySQLBase, $table_names['TGINFO_TABLE'], $logger);
	$tgKodeDAO = handleTGKODE($srcBase, $uttrekkMySQLBase, $table_names['TGKODE_TABLE'], $logger);
	$tgHjemDAO = handleTGHJEM($srcBase, $uttrekkMySQLBase, $table_names['TGHJEM_TABLE'], $logger);
	$tggrpDAO = handleTGGRP($srcBase, $uttrekkMySQLBase, $table_names['TGGRP_TABLE'], $logger);
	$tgMedlemDAO = handleTGMEDLEM($srcBase, $uttrekkMySQLBase, $table_names['TGMEDLEM_TABLE'], $logger);

	$utvSakTyDAO = handleUTVSAKTY($srcBase, $uttrekkMySQLBase, $table_names['UTVSAKTY_TABLE'], $logger);
	$utvBehStatDAO = handleUTVBEHSTAT($srcBase, $uttrekkMySQLBase, $table_names['UTVBEHSTAT_TABLE'], $logger);
	$utvMedlFunkDAO = handleUTVMEDLF($srcBase, $uttrekkMySQLBase, $table_names['UTVMEDLFUNK_TABLE'], $logger);


	$utvalgDAO = handleUTVALG($srcBase, $uttrekkMySQLBase, $table_names['UTVALG_TABLE'], $logger);

	$utvMedlDAO = handleUTVMEDL($srcBase, $uttrekkMySQLBase, $table_names['UTVMEDL_TABLE'], $logger);
	//$klassDAO = handleKLASS($srcBase, $uttrekkMySQLBase, $table_names['KLASS_TABLE']);


	$adradmenhDAO = handleADRADMENH($srcBase, $uttrekkMySQLBase, $table_names['ADRADMENH_TABLE'], $logger);

	$adrPersonDAO = handleADRPERSON($srcBase, $uttrekkMySQLBase, $table_names['ADRPERSON_TABLE'], $logger);
	$aliasAdmDAO = handleALIASADM($srcBase, $uttrekkMySQLBase, $table_names['ALIASADM_TABLE'], $logger);
	$avsmotDAO = handleAVSMOT($srcBase, $uttrekkMySQLBase, $table_names['AVSMOT_TABLE'], $logger);
	$dokTilknDAO = handleDOKTILKN($srcBase, $uttrekkMySQLBase, $table_names['DOKTILKN_TABLE'], $logger);
	$enhTypeDAO = handleENHTYPE($srcBase, $uttrekkMySQLBase, $table_names['ENHTYPE_TABLE'], $logger);

	$medadrgrDAO = handleMEDADADRGR($srcBase, $uttrekkMySQLBase, $table_names['MEDADRGR_TABLE'], $logger);
	//$perklarDAO = handlePERKLAR($srcBase, $uttrekkMySQLBase, $table_names['PERKLAR_TABLE'], $logger);
	$tilleggDAO =  handleTILLEGG($srcBase, $uttrekkMySQLBase, $table_names['TILLEGG_TABLE'], $logger);

	$sakPartDAO = handleSAKPART($srcBase, $uttrekkMySQLBase, $table_names['SAKPART_TABLE'], $logger);

	$statMDokDAO = handleSTATMDOK($srcBase, $uttrekkMySQLBase, $table_names['SAKPART_TABLE'], $logger);
	$polsakgDAO = handlePOLSAKG($srcBase, $uttrekkMySQLBase, $table_names['POLSAKG_TABLE'], $logger);
	$tlKodeDAO =  handleTLKODE($srcBase, $uttrekkMySQLBase, $table_names['TLKODE_TABLE'], $logger);
	// dokLink / dokVers / dokBesk are handled inside JOURPST, Creating DAO objects
	// only to create extraction to XML	
	$dokVersDAO = new DokLinkDAO($srcBase, $uttrekkMySQLBase, $table_names['DOKLINK_TABLE'], $kommuneName, $logger);
	$dokLinkDAO = new DokVersDAO($srcBase, $uttrekkMySQLBase, $table_names['DOKVERS_TABLE'], $kommuneName, $logger);
	$dokBeskDAO = new DokBeskDAO($srcBase, $uttrekkMySQLBase, $table_names['DOKBESK_TABLE'], $logger);

	$utDokTypDAO = handleUTDOKTYPE($srcBase, $uttrekkMySQLBase, $table_names['UTVDOKTYPE_TABLE'], $logger);
	$utvMoteDAO = handleUTVMOTE($srcBase, $uttrekkMySQLBase, $table_names['UTVMOTE_TABLE'], $logger);

	$utvMoteDokDAO = new UtvMoteDokDAO($srcBase, $uttrekkMySQLBase, $table_names['UTVMOTEDOK_TABLE'], $logger);
	$utvBehDoDAO = new UtvBehDoDAO($srcBase, $uttrekkMySQLBase, $table_names['UTVBEHDO_TABLE'], $logger);
	$utvSakDAO = new UtvSakDAO($srcBase, $uttrekkMySQLBase, $table_names['UTVSAK_TABLE'], $logger, $utvBehDoDAO);

	$utvBehDAO = handleUTVBEH($srcBase, $uttrekkMySQLBase, $table_names['UTVBEH_TABLE'], $logger, $utvSakDAO, $utvBehDoDAO);

	

	
//PERKLAR STATMDOK UTVDOKTYPE


	$adminDelDAO->createXML($extractor);
 	$adradmenhDAO->createXML($extractor);
 	$adressekpDAO->createXML($extractor);
 	$adrPersonDAO->createXML($extractor);
	$adrTypeDAO->createXML($extractor); 
	$aliasAdmDAO->createXML($extractor);
	$arkivDAO->createXML($extractor);
	$arkivdelDAO->createXML($extractor);
	$arkivperiodeDAO->createXML($extractor);
	$arstatusDAO->createXML($extractor);
	$avgrKodeDAO->createXML($extractor);
	$avskrmDAO->createXML($extractor);
 	$avsmotDAO->createXML($extractor);
	$bskodeDAO->createXML($extractor);	
	$dokBeskDAO->createXML($extractor);
	$dokkatDAO->createXML($extractor);
	$dokLinkDAO->createXML($extractor);
	$dokstatDAO->createXML($extractor); 
	$dokTilknDAO->createXML($extractor);
	$dokTypeDAO->createXML($extractor);
	$dokVersDAO->createXML($extractor);
	$earkKode->createXML($extractor);
	$enhTypeDAO->createXML($extractor);
	$fstatusDAO->createXML($extractor);
	$forsmateDAO->createXML($extractor);
	$infoTypeDAO->createXML($extractor);
	$jenArkdDAO->createXML($extractor);
	$journEnhDAO->createXML($extractor);
	$journPstDAO->createXML($extractor);
	$journStaDAO->createXML($extractor);
	$kassKodeDAO->createXML($extractor);
//	$klassDAO->createXML($extractor);
	$lagrFormDAO->createXML($extractor);
	$lagrEnhDAO->createXML($extractor);
	$medadrgrDAO->createXML($extractor);
	$merknadDAO->createXML($extractor);
	$noarkSakDAO->createXML($extractor);	
	$numSerieDAO->createXML($extractor);
	$ordnpriDAO->createXML($extractor);
	$opriTypDAO->createXML($extractor);
	$ordVerdDAO->createXML($extractor);
	$personDAO->createXML($extractor);
//	$perklarDAO->createXML($extractor);
	$perNavnDAO->createXML($extractor);
	$perRolleDAO->createXML($extractor);
	$polsakgDAO->createXML($extractor);
	$postnrDAO->createXML($extractor);
	$sakPartDAO->createXML($extractor);
	$sakStatDAO->createXML($extractor);
	$sakTypeDAO->createXML($extractor);		
	$statMDokDAO->createXML($extractor);
	$tggrpDAO->createXML($extractor);
	$tgHjemDAO->createXML($extractor); 
	$tgInfoDAO->createXML($extractor);
	$tgKodeDAO->createXML($extractor);
	$tgMedlemDAO->createXML($extractor);	
	$tilleggDAO->createXML($extractor);
	$tlKodeDAO->createXML($extractor);
	//$utDokTypDAO->createXML($extractor);
	$utvalgDAO->createXML($extractor);
	$utvBehDAO->createXML($extractor);
	$utvBehDoDAO->createXML($extractor);
	$utvBehStatDAO->createXML($extractor);
	$utvMedlFunkDAO->createXML($extractor);
	$utvMedlDAO->createXML($extractor);
	$utvMoteDAO->createXML($extractor);
	$utvSakDAO->createXML($extractor);
	$utvSakTyDAO->createXML($extractor);
	$varFromDAO->createXML($extractor);
	

	
	echo "Lager NOARKIH.XML\n";

        $exportInfo = new ExportInfo();

        $exportInfo->arkskaper = "test";
        $exportInfo->systemName = "test";
        $exportInfo->kommune = "kommune Name";
        $exportInfo->fraDato = "22002200";
        $exportInfo->tilDato = "22002200";
        $exportInfo->prodDato = "22002200";

        $ihCreator = new NoarkIHCreator($uttrekkMySQLBase, $uttrekkDirectory, $exportInfo);
        $ihCreator->generateNoarkIH();
 
	echo "Ferdig med XML filer\n";
	echo "Lukker koblinger til databaser\n";

 	$logger->close();
	
	echo "\tLukker MySQL basen. Resultatet er (";
	$val = $uttrekkMySQLBase->close();
	echo ($val == true  ? 'OK' : 'Feil' ) . ");\n";
	
	echo "\tLukker Oracle basen. Resultatet er (";
	$val = $srcBase->close();
	echo ($val == true  ? 'OK' : 'Feil' ) . ");\n";
	
	echo "Uttrekksprossesen er ferdig. Alle filer er i mappen  ($uttrekkDirectory)  \n";


	
	function createNoark4DBStructure ($uttrekkMySQLBase, $noark4DatabaseStruktur) {
	
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createPOSTNR());		
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createADMINDEL());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createALIASADM());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createADRTYPE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createADRESSEK());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createADRADMENH());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createPERSON());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createADRPERS());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createNUMSERIE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createARKIV ());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createBSKODE ());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createOPRITYP());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createORDNPRI());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createARSTATUS());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createARKIVPER());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createARKIVDEL());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createAVGRKODE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createAVSKRM());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createKASSKODE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createTGKODE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createTGGRP());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createSAKSTAT());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createSAKTYPE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createNOARKSAK());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDOKTYPE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createJOURNSTA());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createORDNVERD());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createTLKODE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createJOURNPST());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createLAGRENH());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createLAGRFORM());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createMEDADRGR());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createFORSMATE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createFSTATUS());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createAVSMOT());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDOKKAT());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDOKSTAT());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createTGHJEM());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDOKBESK());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDOKLINK());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDOKTILKN());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createVARFORM());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createDOKVERS());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createEARKKODE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createENHTYPE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createINFOTYPE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createJOURNENH());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createJENARKDEL());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createKLASS());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createPERKLAR());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createPERNAVN());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createMERKNAD());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createPERROLLE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVSAKTY());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createPOLSAKG());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createSAKPART());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createSTATMDOK ());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createTGMEDLEM());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createTILLEGG());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTDOKTYP());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createTGINFO());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVALG());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVMOTE());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVMOTEDOK());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVBEHSTAT());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVBEH());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVBEHDO());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVMEDLF());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVMEDL());
		$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createUTVSAK());
	}
	
/*

	function handle($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {
		echo "\t handling  ";
		$DAO = new DAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$issues = $DAO->processTable();
		echo $issues . "... processed \n";		
		return $;	
	}

	function handle($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {
		echo "\t handling  ";
		$DAO = new DAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$issues = $DAO->processTable();
		echo $issues . "... processed \n";		
		return $;	
	}

*/
	function handleADMINDEL($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ADMINDEL ... ";
		$logger->log("ADMINDEL.XML", "Started processing ADMINDEL(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$admindelDAO = new AdminDelDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$admindelDAO->processTable();		
		$issues = $admindelDAO->getIssues();

		if ($issues == "") 		
			$logger->log("ADMINDEL.XML", "Finished processing ADMINDEL. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ADMINDEL.XML", "Finished processing ADMINDEL. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed ADMINDEL " . $admindelDAO->countRowsInTableAfter() . " rows handled\n";
		return $admindelDAO;
	}

	function handleADRADMENH($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {
		echo "\t handling ADRADMENH ... ";
		$logger->log("ADRADMENH.XML", "Started processing ADRADMENH(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		$adradmenhDAO = new AdrAdmEnhDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$adradmenhDAO->processTable();
		$issues = $adradmenhDAO->getIssues();

		if ($issues == "") 		
			$logger->log("ADRADMENH.XML", "Finished processing ADRADMENH. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ADRADMENH.XML", "Finished processing ADRADMENH. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed ADRADMENH " . $adradmenhDAO->countRowsInTableAfter() . " rows handled\n" ;
		return $adradmenhDAO;	
	}

	function handleADRESSEKP($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {

		$adressekpDAO = new AdressekpDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		

		$numRowsBefore = $adressekpDAO->countRowsInTableBefore(); 
		preTableProcess($logger, "ADRESSEKP", $numRowsBefore, $tableName);

		$adressekpDAO->processTable();
		$issues = $adressekpDAO->getIssues();

		$numRowsAfter = $adressekpDAO->countRowsInTableAfter();
		postTableProcess($logger, $issues, "ADRESSEKP", $numRowsAfter, $tableName);
		
		return $adressekpDAO;	
	}


	function handleADRPERSON($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {
		echo "\t handling ADRPERS ... ";
		$logger->log("ADRPERS.XML", "Started processing ADRPERS(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$adrPersonDAO = new AdrPersonDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$adrPersonDAO->processTable();
		$issues = $adrPersonDAO->getIssues();

		if ($issues == "") 		
			$logger->log("ADRPERS.XML", "Finished processing ADRPERSON. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ADRPERS.XML", "Finished processing ADRPERSON. " . $issues,  Constants::LOG_PROCESSINGINFO);
		echo $issues . " ...  processed ADRPERS " . $adrPersonDAO->countRowsInTableAfter() . " rows handled\n" ;;		
		return $adrPersonDAO;	
	}

	function handleADRTYPE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ADRTYPE ... ";
		$logger->log("ADRTYPE.XML", "Started processing ADRTYPE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$adrTypeDAO = new AdrTypeDAO($srcBase, $uttrekkMySQLBase, $tableName,  $logger);		
		$adrTypeDAO->processTable();
		$issues = $adrTypeDAO->getIssues();

		if ($issues == "") 		
			$logger->log("ADRTYPE.XML", "Finished processing ADRTYPE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ADRTYPE.XML", "Finished processing ADRTYPE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues  . " ... processed ADRTYPE " . $adrTypeDAO->countRowsInTableAfter() . " rows handled\n" ;;
		return $adrTypeDAO;	
	}

	function handleALIASADM($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {
		echo "\t handling ALIASADM ... ";
		$logger->log("ALIASADM.XML", "Started processing ALIASADM(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$aliasAdmDAO = new AliasAdmDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$aliasAdmDAO->processTable();
		$issues = $aliasAdmDAO->getIssues();

		if ($issues == "") 		
			$logger->log("ALIASADM.XML", "Finished processing ALIASADM. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ALIASADM.XML", "Finished processing ALIASADM. " . $issues,  Constants::LOG_PROCESSINGINFO);
		echo $issues . " ... processed ALIASADM " . $aliasAdmDAO->countRowsInTableAfter() . " rows handled\n" ;;		
		return $aliasAdmDAO;	
	}
	
	function handleARKIV($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ARKIV ...";
		$logger->log("ARKIV.XML", "Started processing ARKIV(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$arkivDAO = new ArkivDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$arkivDAO->processTable();
		$issues = $arkivDAO->getIssues();

		if ($issues == "")	
			$logger->log("ARKIV.XML", "Finished processing ARKIV. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ARKIV.XML", "Finished processing ARKIV. " . $issues,  Constants::LOG_PROCESSINGINFO);
		echo $issues  . "... processed ARKIV " . $arkivDAO->countRowsInTableAfter() . " rows handled\n";
		return $arkivDAO;
	}
	
	function handleARKIVDEL($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ARKIVDEL ...";
		$logger->log("ARKIVDEL.XML", "Started processing ARKIVDEL(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$arkivdelDAO = new ArkivDelDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$arkivdelDAO->processTable();
		$issues = $arkivdelDAO->getIssues();

		if ($issues == "")	
			$logger->log("ARKIVDEL.XML", "Finished processing ARKIVDEL. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ARKIVDEL.XML", "Finished processing ARKIVDEL. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed ARKIVDEL " . $arkivdelDAO->countRowsInTableAfter() . " rows handled\n";;
		return $arkivdelDAO;
	}
	
	function handleARKIVPERIODE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ARKIVPERIODE ... ";
		$logger->log("ARKIVPER.XML", "Started processing ARKIVPER(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$arkivperiodeDAO = new ArkivPeriodeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$arkivperiodeDAO->processTable();
		$issues = $arkivperiodeDAO->getIssues();

		if ($issues == "")	
			$logger->log("ARKIVPER.XML", "Finished processing ARKIVPER. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ARKIVPER.XML", "Finished processing ARKIVPER. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues  . " ... processed ARKIVPERIODE " . $arkivperiodeDAO->countRowsInTableAfter() . " rows handled\n";;
		return $arkivperiodeDAO;
	}

	function handleARSTATUS($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ARSTATUS ...";
		$logger->log("ARSTATUS.XML", "Started processing ARSTATUS(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$arstatusDAO = new ArStatusDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$arstatusDAO->processTable();
		$issues = $arstatusDAO->getIssues();

		if ($issues == "")	
			$logger->log("ARSTATUS.XML", "Finished processing ARSTATUS. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ARSTATUS.XML", "Finished processing ARSTATUS. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues = "... processed ARSTATUS " . $arstatusDAO->countRowsInTableAfter() . " rows handled\n";;
		return $arstatusDAO;
	}

	function handleAVGRKODE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling AVGRKODE ... ";
		$logger->log("AVGRKODE.XML", "Started processing AVGRKODE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$avgrkodeDAO = new AvgrKodeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$avgrkodeDAO->processTable();
		$issues = $avgrkodeDAO->getIssues();

		if ($issues == "")	
			$logger->log("AVGRKODE.XML", "Finished processing AVGRKODE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("AVGRKODE.XML", "Finished processing AVGRKODE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed AVGRKODE " . $avgrkodeDAO->countRowsInTableAfter() . " rows handled\n";;
		return $avgrkodeDAO;
	}


	function handleAVSKRM($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling AVSKRM ... ";
		$logger->log("AVSKRM.XML", "Started processing AVSKRM(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$avskrmDAO = new AvskrmDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$avskrmDAO->processTable();
		$issues = $avskrmDAO->getIssues();

		if ($issues == "")	
			$logger->log("AVSKRM.XML", "Finished processing AVSKRM. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("AVSKRM.XML", "Finished processing AVSKRM. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues  . " ... processed AVSKRM " . $avskrmDAO->countRowsInTableAfter() . " rows handled\n";;
		return $avskrmDAO;
	}
	

	function handleAVSMOT($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {
		echo "\t handling AVSMOT ... ";
		$logger->log("AVSMOT.XML", "Started processing AVSKRM(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$avsmotDAO = new AvsmotDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		 		
		$issues = $avsmotDAO->processTable();
		$issues = $avsmotDAO ->getIssues();

		if ($issues == "")	
			$logger->log("AVSMOT.XML", "Finished processing AVSMOT. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("AVSMOT.XML", "Finished processing AVSMOT. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo  $issues  . " ... processed AVSMOT " . $avsmotDAO->countRowsInTableAfter() . " rows handled\n";
		return $avsmotDAO;	
	}

	
	function handleBSKODE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling BSKODE ... ";
		$logger->log("BSKODE.XML", "Started processing BSKODE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$bskodeDAO = new BskodeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$bskodeDAO->processTable();
		$issues = $bskodeDAO->getIssues();

		if ($issues == "")	
			$logger->log("BSKODE.XML", "Finished processing BSKODE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("BSKODE.XML", "Finished processing BSKODE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . "... processed BSKODE " . $bskodeDAO->countRowsInTableAfter() . " rows handled\n";
		return $bskodeDAO;
	}

	function handleDOKKAT($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling DOKKAT ... ";
		$logger->log("DOKKAT.XML", "Started processing DOKKAT(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$dokkatDAO = new DokKatDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$dokkatDAO->processTable();
		$issues = $dokkatDAO->getIssues();

		if ($issues == "")	
			$logger->log("DOKKAT.XML", "Finished processing DOKKAT. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("DOKKAT.XML", "Finished processing DOKKAT. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed DOKKAT " . $dokkatDAO->countRowsInTableAfter() . " rows handled\n";	
		return $dokkatDAO;	
	}

	function handleDOKSTAT($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling DOKSTAT ... ";
		$logger->log("DOKSTAT.XML", "Started processing DOKSTAT(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$dokstatDAO = new DokStatDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$dokstatDAO->processTable();
		$issues = $dokstatDAO->getIssues();

		if ($issues == "")	
			$logger->log("DOKSTAT.XML", "Finished processing DOKSTAT. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("DOKSTAT.XML", "Finished processing DOKSTAT. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed DOKSTAT " . $dokstatDAO->countRowsInTableAfter() . " rows handled\n";
		return $dokstatDAO;
	}

	function handleDOKTILKN($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling DOKTILKN ... ";
		$logger->log("DOKTILKN.XML", "Started processing DOKTILKN(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$dokTilknDAO = new DokTilknDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$dokTilknDAO->processTable();
		$issues = $dokTilknDAO->getIssues();

		if ($issues == "")	
			$logger->log("DOKTILKN.XML", "Finished processing DOKTILKN. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("DOKTILKN.XML", "Finished processing DOKTILKN. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed DOKTILKN " .  $dokTilknDAO->countRowsInTableAfter() . " rows handled\n";	
		return $dokTilknDAO;
	}

	function handleDOKTYPE ($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling DOKTYPE ... ";
		$logger->log("DOKTYPE.XML", "Started processing DOKTYPE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$doktypeDAO = new DokTypeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);				
		$doktypeDAO->processTable();
		$issues = $doktypeDAO->getIssues();

		if ($issues == "")	
			$logger->log("DOKTYPE.XML", "Finished processing DOKTYPE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("DOKTYPE.XML", "Finished processing DOKTYPE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed DOKTYPE " . $doktypeDAO->countRowsInTableAfter() . " rows handled\n";
		return $doktypeDAO;		
	}

	function handleEARKKODE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		
		echo "\t handling EARKKODE ... ";
		$logger->log("EARKKODE.XML", "Started processing EARKKODE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$earkKodeDAO = new EarkKodeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$earkKodeDAO->processTable();
		$issues = $earkKodeDAO->getIssues();

		if ($issues == "")	
			$logger->log("EARKKODE.XML", "Finished processing EARKKODE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("EARKKODE.XML", "Finished processing EARKKODE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed EARKKODE " . $earkKodeDAO->countRowsInTableAfter() . " rows handled\n";
		return $earkKodeDAO;
	}
	
	function handleENHTYPE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		
		echo "\t handling ENHTYPE ... ";
		$logger->log("ENHTYPE.XML", "Started processing ENHTYPE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		$enhTypeDAO  = new EnhTypeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$enhTypeDAO ->processTable();
		$issues = $enhTypeDAO->getIssues();

		if ($issues == "")	
			$logger->log("ENHTYPE.XML", "Finished processing ENHTYPE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ENHTYPE.XML", "Finished processing ENHTYPE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed ENHTYPE " . $enhTypeDAO->countRowsInTableAfter() . " rows handled\n";
		return $enhTypeDAO ;
	}

	function handleFORSMATE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling FORSMATE ... ";
		$logger->log("FORSMATE.XML", "Started processing FORSMATE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		
		$forsmateDAO = new ForsMateDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$forsmateDAO->processTable();
		$issues = $forsmateDAO->getIssues();

		if ($issues == "")	
			$logger->log("FORSMATE.XML", "Finished processing FORSMATE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("FORSMATE.XML", "Finished processing FORSMATE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed FORSMATE " . $forsmateDAO->countRowsInTableAfter() . " rows handled\n";
		return $forsmateDAO;		
	}	 

	
	function handleFSTATUS($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling FSTATUS ... ";
		$logger->log("FSTATUS.XML", "Started processing FSTATUS(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$fstatusDAO = new FStatusDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$fstatusDAO->processTable();
		$issues = $fstatusDAO->getIssues();

		if ($issues == "")	
			$logger->log("FSTATUS.XML", "Finished processing FSTATUS. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("FSTATUS.XML", "Finished processing FSTATUS. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed FSTATUS " . $fstatusDAO->countRowsInTableAfter() . " rows handled\n";
		return $fstatusDAO;		
	}	 


	function handleINFOTYPE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling INFOTYPE ... ";
		$logger->log("INFOTYPE.XML", "Started processing INFOTYPE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$infoTypeDAO = new InfoTypeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$infoTypeDAO->processTable();
		$issues = $infoTypeDAO->getIssues();

		if ($issues == "")	
			$logger->log("INFOTYPE.XML", "Finished processing INFOTYPE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("INFOTYPE.XML", "Finished processing INFOTYPE. " . $issues,  Constants::LOG_PROCESSINGINFO);


		echo $issues . " ... processed INFOTYPE " . $infoTypeDAO->countRowsInTableAfter() . " rows handled\n";
		return $infoTypeDAO;	
	}	 
	
	function handleJENARKD($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling JENARKD ... ";
		$logger->log("JENARKD.XML", "Started processing JENARKD(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$jenArkdDAO = new JenArkdDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$jenArkdDAO->processTable();
		$issues = $jenArkdDAO->getIssues();

		if ($issues == "")	
			$logger->log("JENARKD.XML", "Finished processing JENARKD. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("JENARKD.XML", "Finished processing JENARKD. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed JENARKD " . $jenArkdDAO->countRowsInTableAfter() . " rows handled\n";		
		return $jenArkdDAO;
	}	
	
	function handleJOURNENH($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling JOURNENH ... ";
		$logger->log("JOURNENH.XML", "Started processing JOURNENH(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$journEnhDAO = new JournEnhDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$journEnhDAO->processTable();
		$issues = $journEnhDAO->getIssues();

		if ($issues == "")	
			$logger->log("JOURNENH.XML", "Finished processing JOURNENH. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("JOURNENH.XML", "Finished processing JOURNENH. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed JOURNENHET " . $journEnhDAO->countRowsInTableAfter() . " rows handled\n";
		return $journEnhDAO;
	}
	
	function handleJOURNPST($srcBase, $uttrekkMySQLBase, $tableName, $logger, $merknadDAO, $kommuneName) {
		echo "\t handling JOURNPST ... ";
		$logger->log("JOURNPST.XML", "Started processing JOURNPST(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$journPstDAO = new JournPstDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger, $merknadDAO, $kommuneName);		
		$journPstDAO->processTable();
		$issues = $journPstDAO->getIssues();

		if ($issues == "")	
			$logger->log("JOURNPST.XML", "Finished processing JOURNPST. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("JOURNPST.XML", "Finished processing JOURNPST. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed JOURNPST " . $journPstDAO->countRowsInTableAfter() . " rows handled\n";
		return $journPstDAO;		
	}


	function handleJOURNSTA($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling JOURNSTA ... ";
		$logger->log("JOURNSTA.XML", "Started processing JOURNSTA(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$journStaDAO = new JournStaDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$journStaDAO->processTable();
		$issues = $journStaDAO->getIssues();

		if ($issues == "")	
			$logger->log("JOURNSTA.XML", "Finished processing JOURNSTA. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("JOURNSTA.XML", "Finished processing JOURNSTA. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed JOURNSTA " . $journStaDAO->countRowsInTableAfter() . " rows handled\n";
		return $journStaDAO;		
	}

	
	function handleKASSKODE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling KASSKODE ... ";
		$logger->log("KASSKODE.XML", "Started processing KASSKODE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		
		$kassKodeDAO = new KassKodeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$kassKodeDAO->processTable();
		$issues = $kassKodeDAO->getIssues();

		if ($issues == "")	
			$logger->log("KASSKODE.XML", "Finished processing KASSKODE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("KASSKODE.XML", "Finished processing KASSKODE. " . $issues,  Constants::LOG_PROCESSINGINFO);
		
		echo $issues . " ... processed KASSKODE " . $kassKodeDAO->countRowsInTableAfter() . " rows handled\n";
		return $kassKodeDAO;
	}

	function handleKLASS($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling KLASS ... ";
		$logger->log("KLASS.XML", "Started processing KLASS(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$klassDAO = new KlassDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$klassDAO->processTable();
		$issues = $klassDAO->getIssues();

		if ($issues == "")	
			$logger->log("KLASS.XML", "Finished processing KLASS. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("KLASS.XML", "Finished processing KLASS. " . $issues,  Constants::LOG_PROCESSINGINFO);
		

		echo $issues . " ... processed KLASS " . $klassDAO->countRowsInTableAfter() . " rows handled\n";
		return $klassDAO;
	}

	function handleLAGRENH($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling LAGRENH ... ";
		$logger->log("LAGRENH.XML", "Started processing LAGRENH(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$lagrEnhDAO = new LagrEnhDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$lagrEnhDAO->processTable();
		$issues = $lagrEnhDAO->getIssues();

		if ($issues == "")	
			$logger->log("LAGRENH.XML", "Finished processing LAGRENH. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("LAGRENH.XML", "Finished processing LAGRENH. " . $issues,  Constants::LOG_PROCESSINGINFO);
		
		echo $issues . " ... processed LAGRENH " . $lagrEnhDAO->countRowsInTableAfter() . " rows handled\n";		
		return $lagrEnhDAO;
	}	
	
	function handleLAGRFORM($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling LAGRFORM ... ";
		$logger->log("LAGRFORM.XML", "Started processing LAGRFORM(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$lagrFormDAO = new LagrFormDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$lagrFormDAO->processTable();
		$issues = $lagrFormDAO->getIssues();

		if ($issues == "")	
			$logger->log("LAGRFORM.XML", "Finished processing LAGRFORM. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("LAGRFORM.XML", "Finished processing LAGRFORM. " . $issues,  Constants::LOG_PROCESSINGINFO);
		

		echo $issues . " ... processed LAGRFORM " . $lagrFormDAO->countRowsInTableAfter() . " rows handled\n";		
		return $lagrFormDAO;
	}
	
	function handleMEDADADRGR($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling MEDADADRGR ... ";
		$logger->log("MEDADADRGR.XML", "Started processing MEDADADRGR(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$medadrgrDAO = new MedadrgrDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$medadrgrDAO->processTable();
		$issues = $medadrgrDAO->getIssues();

		if ($issues == "")	
			$logger->log("MEDADADRGR.XML", "Finished processing MEDADADRGR. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("MEDADADRGR.XML", "Finished processing MEDADADRGR. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed MEDADADRGR " .  $medadrgrDAO->countRowsInTableAfter() . " rows handled\n";	
		return $medadrgrDAO; 
	}

	function handleMERKNAD($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling MERKNAD ... ";
		$logger->log("MERKNAD.XML", "Started processing MERKNAD(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$merknadDAO = new MerknadDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$merknadDAO->processTable();
		$issues = $merknadDAO->getIssues();

		if ($issues == "")	
			$logger->log("MERKNAD.XML", "Finished processing MERKNAD. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("MERKNAD.XML", "Finished processing MERKNAD. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed MERKNAD " . $merknadDAO->countRowsInTableAfter() . " rows handled\n";	
		return $merknadDAO; 
	}

	function handleNOARKSAK($srcBase, $uttrekkMySQLBase, $tableName, $logger, $ordVerdDAO, $merknadDAO) {
		echo "\t handling NOARKSAK ... ";
		$logger->log("NOARKSAK.XML", "Started processing NOARKSAK(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$noarkSakDAO = new NoarkSakDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger, $ordVerdDAO, $merknadDAO);
		$noarkSakDAO->processTable();

		$issues = $merknadDAO->getIssues();

		if ($issues == "")	
			$logger->log("NOARKSAK.XML", "Finished processing NOARKSAK. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("NOARKSAK.XML", "Finished processing NOARKSAK. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed NOARKSAK " .  $noarkSakDAO->countRowsInTableAfter() . " rows handled\n";	
		return $noarkSakDAO;
	}			
		
	function handleNUMSERIE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling NUMSERIE ... WARNING!! DUMMY CODE ";
		$logger->log("NUMSERIE.XML", "Started processing NUMSERIE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$numSerieDAO = new NumSerieDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$numSerieDAO->processTable();
		$issues = $numSerieDAO->getIssues();

		if ($issues == "")	
			$logger->log("NUMSERIE.XML", "Finished processing NUMSERIE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("NUMSERIE.XML", "Finished processing NUMSERIE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed NUMSERIE " . $numSerieDAO->countRowsInTableAfter() . " rows handled\n";
		return $numSerieDAO;
	}
	
	function handleOPRITYP($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling OPRITYP ... ";
		$logger->log("OPRITYP.XML", "Started processing OPRITYP(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$opriTypDAO = new OpriTypDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$opriTypDAO->processTable();
		$issues = $opriTypDAO->getIssues();

		if ($issues == "")	
			$logger->log("OPRITYP.XML", "Finished processing OPRITYP. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("OPRITYP.XML", "Finished processing OPRITYP. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed OPRITYP " . $opriTypDAO->countRowsInTableAfter() . " rows handled\n";
		return $opriTypDAO;
	}
	
	function handleORDNPRI($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ORDNPR ... ";
		$logger->log("ORDNPR.XML", "Started processing ORDNPR(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$ordnpriDAO = new OrdnPriDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$ordnpriDAO->processTable();
		$issues = $ordnpriDAO->getIssues();

		if ($issues == "")	
			$logger->log("ORDNPR.XML", "Finished processing ORDNPR. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ORDNPR.XML", "Finished processing ORDNPR. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed ORDNPR " .  $ordnpriDAO->countRowsInTableAfter() . " rows handled\n";
		return $ordnpriDAO;
	} 	

	function handleORDNVERD($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling ORDNVERD ... ";
		$logger->log("ORDNVERD.XML", "Started processing ORDNVERD(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$ordVerdDAO = new OrdnVerdDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
//		echo "\t          Creating known codes ";
		//$ordVerdDAO->createKnownORDNVERD();
		$ordVerdDAO->processTable();
		$issues = $ordVerdDAO->getIssues();

		if ($issues == "")	
			$logger->log("ORDNVERD.XML", "Finished processing ORDNVERD. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("ORDNVERD.XML", "Finished processing ORDNVERD. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed ORDNVERD " .  $ordVerdDAO->countRowsInTableAfter() . " rows handled\n";
		return $ordVerdDAO;
	}
	
	function handlePERKLAR($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling PERKLAR ... ";
		$logger->log("PERKLAR.XML", "Started processing PERKLAR(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$perKlarDAO = new PerKlarDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$perKlarDAO->processTable();
		$issues = $perKlarDAO->getIssues();

		if ($issues == "")	
			$logger->log("PERKLAR.XML", "Finished processing PERKLAR. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("PERKLAR.XML", "Finished processing PERKLAR. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed PERKLAR " .  $perKlarDAO->countRowsInTableAfter() . " rows handled\n";
		return $perKlarDAO;
	}
	
	function handlePERNAVN($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling PERNAVN ... ";
		$logger->log("PERNAVN.XML", "Started processing PERNAVN(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		
		$perNavnDAO = new PerNavnDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$perNavnDAO->processTable();
		$issues = $perNavnDAO->getIssues();

		if ($issues == "")	
			$logger->log("PERNAVN.XML", "Finished processing PERNAVN. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("PERNAVN.XML", "Finished processing PERNAVN. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed PERNAVN " .  $perNavnDAO->countRowsInTableAfter() . " rows handled\n";
		return $perNavnDAO;
	}	

	function handlePERROLLE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling PERROLLE ";
		$logger->log("PERROLLE.XML", "Started processing PERROLLE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$perRolleDAO = new PerRolleDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$perRolleDAO->processTable();
		$issues = $perRolleDAO->getIssues();

		if ($issues == "")	
			$logger->log("PERROLLE.XML", "Finished processing PERROLLE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("PERROLLE.XML", "Finished processing PERROLLE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed PERROLLE " .  $perRolleDAO->countRowsInTableAfter() . " rows handled\n";
		return $perRolleDAO;
	}


	function handlePERSON($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling PERSON ... ";
		$logger->log("PERSON.XML", "Started processing PERSON(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$personDAO = new PersonDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$personDAO->processTable();
		$issues = $personDAO->getIssues();

		if ($issues == "")	
			$logger->log("PERSON.XML", "Finished processing PERSON. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("PERSON.XML", "Finished processing PERSON. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed PERSON " .  $personDAO->countRowsInTableAfter() . " rows handled\n";
		return $personDAO;
	}

	

	function handlePOLSAKG($srcBase, $uttrekkMySQLBase, $tableName, $logger)  {
		echo "\t handling POLSAKG ... ";
		$logger->log("POLSAKG.XML", "Started processing POLSAKG(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$polSakgDAO = new PolsakgDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$polSakgDAO->processTable();
		$issues = $polSakgDAO->getIssues();

		if ($issues == "")	
			$logger->log("POLSAKG.XML", "Finished processing POLSAKG. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("POLSAKG.XML", "Finished processing POLSAKG. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues .  " ... processed " .  $polSakgDAO->countRowsInTableAfter() . " rows handled\n";		
		return $polSakgDAO;
	}

	
	function preTableProcess($logger, $noarkTableName, $numRows, $sourceTableName ) {
		echo "\t handling " . $noarkTableName . " expect " . $numRows . " rows ... ";
		$logger->log($noarkTableName . ".XML", "Started processing " . $noarkTableName . "(" . $sourceTableName . ") expect " . $numRows . " rows",  Constants::LOG_PROCESSINGINFO);
	}

	function postTableProcess($logger, $issues, $noarkTableName, $numRows) {
		if ($issues == "")	
			$logger->log($noarkTableName . ".XML", "Finished processing " . $noarkTableName . ". No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log($noarkTableName . ".XML", "Finished processing " . $noarkTableName . "." . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed " . $noarkTableName . ". " . $numRows . " rows handled \n" ;
	}

	
	function handlePOSTNR($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		$postnrDAO = new PostnrDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger, $logger);

		$numRowsBefore = $postnrDAO->countRowsInTableBefore(); 
		preTableProcess($logger, "POSTNR", $numRowsBefore, $tableName);
		
		$postnrDAO->processTable();
		$issues = $postnrDAO->getIssues();

		$numRowsAfter = $postnrDAO->countRowsInTableAfter();
		postTableProcess($logger, $issues, "POSTNR", $numRowsAfter, $tableName);

		return $postnrDAO;
	}
	
	function handleSAKPART($srcBase, $uttrekkMySQLBase, $tableName, $logger) {	
		$sakPartDAO = new SakPartDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		

		$numRowsBefore = $sakPartDAO->countRowsInTableBefore(); 
		preTableProcess($logger, "SAKPART", $numRowsBefore, $tableName);

		$sakPartDAO->processTable();
		$issues = $sakPartDAO->getIssues();

		$numRowsAfter = $sakPartDAO->countRowsInTableAfter();
		postTableProcess($logger, $issues, "SAKPART", $numRowsAfter, $tableName);

		return $sakPartDAO;
	}

	function handleSAKSTAT($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		$sakStatDAO = new SakStatDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);

		$numRowsBefore = $sakStatDAO->countRowsInTableBefore(); 
		preTableProcess($logger, "SAKSTAT", $numRowsBefore, $tableName);

		$sakStatDAO->processTable();
		$issues = $sakStatDAO->getIssues();

		$numRowsAfter = $sakStatDAO->countRowsInTableAfter();
		postTableProcess($logger, $issues, "SAKSTAT", $numRowsAfter, $tableName);

		return $sakStatDAO;
	}
	
	function handleSAKTYPE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling SAKTYPE ... ";
		$logger->log("SAKTYPE.XML", "Started processing SAKTYPE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$sakTypeDAO = new SakTypeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$sakTypeDAO->processTable();
		$issues = $sakTypeDAO->getIssues();

		if ($issues == "")	
			$logger->log("SAKTYPE.XML", "Finished processing SAKTYPE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("SAKTYPE.XML", "Finished processing SAKTYPE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed SAKTYPE " .  $sakTypeDAO->countRowsInTableAfter() . " rows handled\n";
		return $sakTypeDAO;
	}

	function handleSTATMDOK($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling STATMDOK ... ";
		$logger->log("STATMDOK.XML", "Started processing STATMDOK(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$statMDokDAO = new StatMDokDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$statMDokDAO->processTable();
		$issues = $statMDokDAO->getIssues();

		if ($issues == "")	
			$logger->log("STATMDOK.XML", "Finished processing STATMDOK. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("STATMDOK.XML", "Finished processing STATMDOK. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues .  " ... processed STATMDOK " .  $statMDokDAO->countRowsInTableAfter() . " rows handled\n";
		return $statMDokDAO;
	}
 

	function handleTGGRP($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling TGGRP ... ";
		$logger->log("TGGRP.XML", "Started processing TGGRP(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$tggrpDAO = new TggrpDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$tggrpDAO->processTable();
		$issues = $tggrpDAO->getIssues();

		if ($issues == "")	
			$logger->log("TGGRP.XML", "Finished processing TGGRP. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("TGGRP.XML", "Finished processing TGGRP. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues .  " ... processed TGGRP " .  $tggrpDAO->countRowsInTableAfter() . " rows handled\n";				
		return $tggrpDAO; 
	}
	
	function handleTGHJEM($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling TGHJEM ... ";
		$logger->log("TGGRP.XML", "Started processing TGGRP(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$tgHjemDAO = new TghjemDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$tgHjemDAO->processTable();
		$issues = $tgHjemDAO->getIssues();

		if ($issues == "")	
			$logger->log("TGGRP.XML", "Finished processing TGGRP. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("TGGRP.XML", "Finished processing TGGRP. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed TGHJEM " .  $tgHjemDAO->countRowsInTableAfter() . " rows handled\n";				
		return $tgHjemDAO;
	}
	
	function handleTGINFO($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling TGINFO ... ";
		$logger->log("TGINFO.XML", "Started processing TGINFO(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$tgInfoDAO = new TginfoDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$tgInfoDAO->processTable();
		$issues = $tgInfoDAO->getIssues();

		if ($issues == "")	
			$logger->log("TGINFO.XML", "Finished processing TGINFO. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("TGINFO.XML", "Finished processing TGINFO. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed TGINFO " . $tgInfoDAO->countRowsInTableAfter() . " rows handled\n";		
		return $tgInfoDAO;	
	}
	
	
	function handleTGKODE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {		
		echo "\t handling TGKODE ... ";
		$logger->log("TGKODE.XML", "Started processing TGKODE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$tgKodeDAO = new TgkodeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$tgKodeDAO->processTable();
		$issues = $tgKodeDAO->getIssues();

		if ($issues == "")	
			$logger->log("TGINFO.XML", "Finished processing TGINFO. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("TGINFO.XML", "Finished processing TGINFO. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed TGKODE " . $tgKodeDAO->countRowsInTableAfter() . " rows handled\n";	
		return $tgKodeDAO;	
	}
	
	function handleTGMEDLEM($srcBase, $uttrekkMySQLBase, $tableName, $logger){
		echo "\t handling TGMEDLEM ... ";
		$logger->log("TGMEDLEM.XML", "Started processing TGMEDLEM(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$tgMedlemDAO = new TgmedlemDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$tgMedlemDAO->processTable();
		$issues = $tgMedlemDAO->getIssues();

		if ($issues == "")	
			$logger->log("TGMEDLEM.XML", "Finished processing TGMEDLEM. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("TGMEDLEM.XML", "Finished processing TGMEDLEM. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed TGMEDLEM " . $tgMedlemDAO->countRowsInTableAfter() . " rows handled\n";

		return $tgMedlemDAO;
	}
	
 	function handleTILLEGG($srcBase, $uttrekkMySQLBase, $tableName, $logger){
		echo "\t handling TILLEGG ... ";
		$logger->log("TILLEGG.XML", "Started processing TILLEGG(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$tilleggDAO = new TilleggDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		//$tilleggDAO->processTable();
		//echo $issues  . " ...processed TGMEDLEM\n";
		$issues = $tilleggDAO->getIssues();

		if ($issues == "")	
			$logger->log("TILLEGG.XML", "Finished processing TILLEGG. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("TILLEGG.XML", "Finished processing TILLEGG. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed TILLEGG " .  $tilleggDAO->countRowsInTableAfter() . " rows handled\n";
		return $tilleggDAO;
	}

	function handleTLKODE($srcBase, $uttrekkMySQLBase, $tableName, $logger){
		echo "\t handling TLKODE ... ";
		$logger->log("TGMEDLEM.XML", "Started processing TGMEDLEM(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		$tlKodeDAO = new TlKodeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$tlKodeDAO->processTable();
		$issues = $tlKodeDAO->getIssues();

		if ($issues == "")	
			$logger->log("TILLEGG.XML", "Finished processing TILLEGG. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("TILLEGG.XML", "Finished processing TILLEGG. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ...processed TLKODE "  . $tlKodeDAO->countRowsInTableAfter() . " rows handled\n";
		return $tlKodeDAO; 
	}

	function handleUTDOKTYP($srcBase, $uttrekkMySQLBase, $tableName, $logger){
		echo "\t handling UTDOKTYP ... ";
		$logger->log("UTDOKTYP.XML", "Started processing UTDOKTYP(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		$utDokTypeDAO = new UtDokTypeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utDokTypeDAO->processTable();
		$issues = $utDokTypeDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTDOKTYP.XML", "Finished processing UTDOKTYP. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTDOKTYP.XML", "Finished processing UTDOKTYP. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ...processed UTDOKTYP " . $utDokTypeDAO->countRowsInTableAfter() . " rows handled\n";
		return $utDokTypeDAO; 
	}

	function handleUTVALG($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling UTVALG ... ";
		$logger->log("UTVALG.XML", "Started processing UTVALG(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		
		$utvalgDAO = new UtvalgDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvalgDAO->processTable();
		$issues = $utvalgDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVALG.XML", "Finished processing UTVALG. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVALG.XML", "Finished processing UTVALG. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVALG " .  $utvalgDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvalgDAO ;
	}


	function handleUTVBEH($srcBase, $uttrekkMySQLBase, $tableName, $logger, $utvSakDAO, $utvBehDoDAO) {		
		echo "\t handling UTVBEH ... ";
		$logger->log("UTVBEH.XML", "Started processing UTVBEH(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvBehDAO = new UtvBehDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger, $utvSakDAO, $utvBehDoDAO);
		$utvBehDAO->processTable();
		$issues = $utvBehDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVBEH.XML", "Finished processing UTVBEH. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVBEH.XML", "Finished processing UTVBEH. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVBUTVBEHEH " . $utvBehDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvBehDAO;
	}

	function handleUTVBEHDO($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling UTVBEHDO ... ";
		$logger->log("UTVBEHDO.XML", "Started processing UTVBEHDO(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvBehDoDAO = new UtvBehDoDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvBehDoDAO->processTable();
		$issues = $utvBehDoDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVBEHDO.XML", "Finished processing UTVBEHDO. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVBEHDO.XML", "Finished processing UTVBEHDO. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " processed UTVBEHDO " .  $utvBehDoDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvBehDoDAO;
	}

	function handleUTVBEHSTAT($srcBase, $uttrekkMySQLBase, $tableName, $logger) {		
		echo "\t handling UTVBEHSTAT ... ";
		$logger->log("UTVBEHSTAT.XML", "Started processing UTVBEHSTAT(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvBehStatDAO = new UtvBehStatDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvBehStatDAO->processTable();
		$issues = $utvBehStatDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVBEHSTAT.XML", "Finished processing UTVBEHSTAT. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVBEHSTAT.XML", "Finished processing UTVBEHSTAT. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVBEHSTAT " . $utvBehStatDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvBehStatDAO;
	}

	function handleUTDOKTYPE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {		
		echo "\t handling UTDOKTYPE ... ";
		$logger->log("UTDOKTYPE.XML", "Started processing UTDOKTYPE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvDokTypeDAO = new UtvDokTypeDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvDokTypeDAO->processTable();
		$issues = $utvDokTypeDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTDOKTYPE.XML", "Finished processing UTDOKTYPE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTDOKTYPE.XML", "Finished processing UTDOKTYPE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTDOKTYPE " . $utvDokTypeDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvDokTypeDAO;


	}

	function handleUTVMEDL($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling UTVMEDL ... ";
		$logger->log("UTVMEDL.XML", "Started processing UTVMEDL(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		$utvMedlDAO = new UtvMedlDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvMedlDAO->processTable();
		$issues = $utvMedlDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVMEDL.XML", "Finished processing UTVMEDL. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVMEDL.XML", "Finished processing UTVMEDL. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVMEDL " .  $utvMedlDAO->countRowsInTableAfter() . " rows handled\n";	
		return $utvMedlDAO;
	}

	function handleUTVMEDLF($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling UTVMEDLF ... ";
		$logger->log("UTVMEDLF.XML", "Started processing UTVMEDLF(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvMedlFunkDAO = new UtvMedlFunkDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvMedlFunkDAO->processTable();
		$issues = $utvMedlFunkDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVMEDLF.XML", "Finished processing UTVMEDLF. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVMEDLF.XML", "Finished processing UTVMEDLF. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVMEDLF " . $utvMedlFunkDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvMedlFunkDAO ;
	}

	function handleUTVMOTE($srcBase, $uttrekkMySQLBase, $tableName, $logger) {		
		echo "\t handling UTVMOTE ... ";
		$logger->log("UTVMOTE.XML", "Started processing UTVMOTE(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvMoteDAO = new UtvMoteDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvMoteDAO->processTable();
		$issues = $utvMoteDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVMOTE.XML", "Finished processing UTVMOTE. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVMOTE.XML", "Finished processing UTVMOTE. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVMOTE " .  $utvMoteDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvMoteDAO;
	}

	function handleUTVMOTEDOK($srcBase, $uttrekkMySQLBase, $tableName, $logger) {		
		echo "\t handling UTVMOTEDOK ... ";
		$logger->log("UTVMOTEDOK.XML", "Started processing UTVMOTEDOK(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvMoteDokDAO = new UtvMoteDokDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvMoteDokDAO->processTable();
		$issues = $utvMoteDokDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVMOTEDOK.XML", "Finished processing UTVMOTEDOK. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVMOTEDOK.XML", "Finished processing UTVMOTEDOK. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVMOTEDOK " .  $utvMoteDokDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvMoteDokDAO;
	}


	function handleUTVSAK($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling UTVSAK ... ";
		$logger->log("UTVSAK.XML", "Started processing UTVSAK(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$utvSakDAO = new UtvSakDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvSakDAO->processTable();
		$issues = $utvSakDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVSAK.XML", "Finished processing UTVSAK. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVSAK.XML", "Finished processing UTVSAK. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVSAK " .  $utvSakDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvSakDAO; 
	}

	function handleUTVSAKTY($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling UTVSAKTY ... ";
		$logger->log("UTVSAKTY.XML", "Started processing UTVSAKTY(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);
		$utvSakTyDAO = new UtvSakTyDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);
		$utvSakTyDAO->processTable();
		$issues = $utvSakTyDAO->getIssues();

		if ($issues == "")	
			$logger->log("UTVSAKTY.XML", "Finished processing UTVSAKTY. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("UTVSAKTY.XML", "Finished processing UTVSAKTY. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed UTVSAKTY "  .  $utvSakTyDAO->countRowsInTableAfter() . " rows handled\n";
		return $utvSakTyDAO; 
	}
		
	function handleVARFORM($srcBase, $uttrekkMySQLBase, $tableName, $logger) {
		echo "\t handling VARFROM ... ";
		$logger->log("VARFROM.XML", "Started processing VARFROM(" . $tableName . ")",  Constants::LOG_PROCESSINGINFO);

		$varFromDAO = new VarFormDAO($srcBase, $uttrekkMySQLBase, $tableName, $logger);		
		$varFromDAO->processTable();
		$issues = $varFromDAO->getIssues();

		if ($issues == "")	
			$logger->log("VARFROM.XML", "Finished processing VARFROM. No issues reported",  Constants::LOG_PROCESSINGINFO);
		else
			$logger->log("VARFROM.XML", "Finished processing VARFROM. " . $issues,  Constants::LOG_PROCESSINGINFO);

		echo $issues . " ... processed VARFROM " .  $varFromDAO->countRowsInTableAfter() . " rows handled\n";	
		return $varFromDAO;	
	}
		
	
?>
