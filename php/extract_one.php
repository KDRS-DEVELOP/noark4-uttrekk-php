	
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

	
	
	// if the file doesn't exsist stop!!!
	$uttrekk_db_ini_array = parse_ini_file("ini/destination_db.ini");
		
	$uttrekk_db_host = $uttrekk_db_ini_array['uttrekk_db_host'];
	$uttrekk_db_user = $uttrekk_db_ini_array['uttrekk_db_user'];
	$uttrekk_db_pswd = $uttrekk_db_ini_array['uttrekk_db_pswd'];
	$uttrekk_db_database = $uttrekk_db_ini_array['uttrekk_db_database'];
	
	
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
	$extractor = new Extractor("mysql", $databaseParameters, "uttrekksfiler");


	$earkKodeDAO = new EarkKodeDAO(null, $uttrekkMySQLBase, "EARKODE_TABLE", null);		
	$earkKodeDAO->createXML($extractor);


?>