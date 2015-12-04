<?php
	/*
		create database noark4Uttrekk;
		GRANT ALL PRIVILEGES ON noark4Uttrekk.* TO 'uttrekkBruker'@'localhost' IDENTIFIED BY 'noark4uttrekk' WITH GRANT OPTION;
	*/
	
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

	
	$options = getopt("d:k:");
	
	$srcBase = null;
	
	// if the file doesn't exsist stop!!!
	$uttrekk_db_ini_array = parse_ini_file("ini/destination_db.ini");
		
	$uttrekk_db_host = $uttrekk_db_ini_array['uttrekk_db_host'];
	$uttrekk_db_user = $uttrekk_db_ini_array['uttrekk_db_user'];
	$uttrekk_db_pswd = $uttrekk_db_ini_array['uttrekk_db_pswd'];
	$uttrekk_db_database = $uttrekk_db_ini_array['uttrekk_db_database'];
	
	print_r($uttrekk_db_ini_array);
	
	echo "\n";
	
	$uttrekkMySQLBase = null;
	
	$extractor = null;
	$uttrekkDirectory = "./uttrekksfiler";
	$noarkIHoutputDir = "./";
	
	// Main program starts here
	
	$uttrekkMySQLBase = null;
	
	try {

		$uttrekkMySQLBase = new UtrekkMySQLBase($uttrekk_db_host, $uttrekk_db_user, $uttrekk_db_pswd, $uttrekk_db_database);
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
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
	$uttrekkMySQLBase->executeStatement($noark4DatabaseStruktur->createEMNEORD());	
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



	echo "Database ser ut til å fungere!\n";

?>
