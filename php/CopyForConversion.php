<?php

require_once "database/uttrekkMySQLBase.php";

$options = getopt("d::f::");

// -d database, name of kommune database
// -p stopAt start at numeric directory number
// -t startAt,
// -s simulate, just print what you would do to screen
// -i directory, input directory to start processing from
// -o directory, output directory where to copy/mv files to


$processingDir = "/home/oracle/processing/to";
$kommune = "";
$fileType = "";


if (isset($options["d"]) && isset($options["f"])) {
	$kommune = $options["d"];
	$fileType = $options["f"];
}
else {
	echo "Usage script -d=kommune -ffiletype\n";
	exit;	
} 

echo "Running script with following options : \n";
print_r($options);


$uttrekk_db_ini_array = parse_ini_file("ini/destination_db.ini");
$uttrekk_db_host = $uttrekk_db_ini_array['uttrekk_db_host'];
$uttrekk_db_user = $uttrekk_db_ini_array['uttrekk_db_user'];
$uttrekk_db_pswd = $uttrekk_db_ini_array['uttrekk_db_pswd'];
$uttrekk_db_database = $uttrekk_db_ini_array['uttrekk_db_database'];


// Check list of known SID
switch ($options["d"]) {
	case "fla":
	case "aal":
	case "nes":
	case "gol":
	case "hem":
	case "hol":
	case "holhist":
	case "hemhist":
	case "orcl":
		break;
	default:
		echo "Not a valid kommune name (" . $options["d"] . ")\n";
		die;
		break;
}

$kommune = $options["d"];
$uttrekkMySQLBase = null;

try {
	$uttrekkMySQLBase = new UtrekkMySQLBase($uttrekk_db_host, $uttrekk_db_user, $uttrekk_db_pswd, $uttrekk_db_database);
}
catch (Exception $e) {
	echo $e->getMessage();
	die;
}

$sqlStatement = " select fileLocatedInFolder, fileName, fileDetectedExtension from ConvertProcessInfo where kommuneBase = '". $kommune .  "' AND  fileDetectedExtension = '" . $fileType . "' AND fileConvertedHashValue IS NULL";
$result = $uttrekkMySQLBase->executeQueryNoProcess($sqlStatement); 

$count = 0;
while ($row = mysql_fetch_array($result)) {
	
	$fileNameFrom = $row['fileLocatedInFolder'] . "/" .  $row['fileName'] . "." . $row['fileDetectedExtension'];
	$fileNameTo = $processingDir . "/" . $row['fileName'] . "." . $row['fileDetectedExtension'];
	$count++; 
	//echo "copy (" . $fileNameFrom. ", ". $fileNameTo . ") \n";
	if (copy ($fileNameFrom, $fileNameTo) == true) {
		echo "(" . $count . ") Copied " . $fileNameFrom . " to " . $fileNameTo . "\n";	
	}
	else {
		echo "(" . $count . ")Error Copying " . $fileName . " to " . $processingDir . "\n";
	}

	if ($count%500 == 0)
		sleep(2);
}

echo $count . " files copied to " .  $processingDir . "\n";













