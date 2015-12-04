<?php
	require_once "database/uttrekkMySQLBase.php";
	require_once "extraction/NoarkIHCreator.php";
	require_once "models/ExportInfo.php";

	$uttrekk_db_ini_array = parse_ini_file("ini/destination_db.ini");
		
	$uttrekk_db_host = $uttrekk_db_ini_array['uttrekk_db_host'];
	$uttrekk_db_user = $uttrekk_db_ini_array['uttrekk_db_user'];
	$uttrekk_db_pswd = $uttrekk_db_ini_array['uttrekk_db_pswd'];
	$uttrekk_db_database = $uttrekk_db_ini_array['uttrekk_db_database'];
	
	print_r($uttrekk_db_ini_array);
	
	echo "\n";
	
	$extractor = null;
	
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
	
	$outputDir = ".";
	$exportInfo = new ExportInfo();

	$exportInfo->arkskaper = "test";
	$exportInfo->systemName = "test";
	$exportInfo->kommune = "kommune Name";
	$exportInfo->fraDato = "22002200";
	$exportInfo->tilDato = "22002200";
	$exportInfo->prodDato = "22002200";

	$ihCreator = new NoarkIHCreator($uttrekkMySQLBase, $outputDir, $exportInfo);
	$ihCreator->generateNoarkIH(); 
?>