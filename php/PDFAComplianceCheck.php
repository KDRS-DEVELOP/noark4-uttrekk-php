<?php

require_once "database/uttrekkMySQLBase.php";


$options = getopt("d::");
$kommune = "";
$fileType = "PDF";


if (isset($options["d"])) {
	$kommune = $options["d"];
}
else {
	echo "Usage script -d=kommune\n";
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

$sqlStatement = " select fileLocatedInFolder, fileName, fileDetectedExtension from ConvertProcessInfo where kommuneBase = '". $kommune .  "' AND  fileDetectedExtension = '" . $fileType . "'";

$result = $uttrekkMySQLBase->executeQueryNoProcess($sqlStatement); 

$count = 0;
while ($row = mysql_fetch_array($result)) {
	
	$fileNameFrom = $row['fileLocatedInFolder'] . "/" . $row['fileName'] . "." . $row['fileDetectedExtension'];
	$fileName =  $row['fileName'];
	$count++; 
	$output = null;
	exec ("/home/oracle/utils/jhove/jhove -m pdf-hul " . $fileNameFrom . "| grep \"PDF/A\"", $output);

	$sqlUpdateStatement = "UPDATE ConvertProcessInfo SET IN_PDF_A=";

	if ($output == null) {
		echo $fileNameFrom .  " is not a pdf/a file\n";
		$sqlUpdateStatement .= "false";
	} 
	else {
		echo $fileNameFrom .  " is a pdf/a file\n";
		$sqlUpdateStatement .= "true";
	}
	$sqlUpdateStatement .= " WHERE kommuneBase = '" . $kommune . "' AND ";
	$sqlUpdateStatement .= " fileName = '" . $fileName. "';";

	if ($uttrekkMySQLBase->executeStatement($sqlUpdateStatement) == false) {
		echo "Error Executing " . $sqlUpdateStatement;
		exit;
	}
}

echo $count . " files checked\n";













