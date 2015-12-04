<?php
/* Name is a little misleading. It also copies all PDF/A files to arkiv folder */

require_once "database/uttrekkMySQLBase.php";

$options = getopt("d::");

// -d database, name of kommune database
// -p stopAt start at numeric directory number
// -t startAt,
// -s simulate, just print what you would do to screen
// -i directory, input directory to start processing from
// -o directory, output directory where to copy/mv files to


$kommune = "";
$fileType = "PDF";


if (isset($options["d"]) ) {
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
$arkivDir = "/home/oracle/arkivfiler/" . $kommune . "/";
$uttrekkMySQLBase = null;



try {
	$uttrekkMySQLBase = new UtrekkMySQLBase($uttrekk_db_host, $uttrekk_db_user, $uttrekk_db_pswd, $uttrekk_db_database);
}
catch (Exception $e) {
	echo $e->getMessage();
	die;
}

// kommuneBase | fileName | fileOriginalExtension | fileDetectedExtension | fileOriginalSize | fileConvertedExtension 
// fileConvertedSize | md5HashValue | fileConvertedHashValue  | fileLocatedInFolder | IN_PDF_A 

$sqlStatement = "select fileLocatedInFolder, fileName, fileDetectedExtension, IN_PDF_A from ConvertProcessInfo where kommuneBase = '". $kommune .  "' AND  fileDetectedExtension = '" . $fileType . "'";
$result = $uttrekkMySQLBase->executeQueryNoProcess($sqlStatement); 

$count = 0;
while ($row = mysql_fetch_array($result)) {
	
	$fileNameFrom = $row['fileLocatedInFolder'] . "/" .  $row['fileName'] . "." . $row['fileDetectedExtension'];
	
	$count++;
	 

	$directoryNumber = "";

	// identify the sub number 	
	$fileLocatedInFolder = $row['fileLocatedInFolder'];

	$pos = strrpos($fileLocatedInFolder, "sub");
	//echo "pos=" . $pos . " (" . $fileLocatedInFolder . ")\n"; 
	$directoryNumber = substr($fileLocatedInFolder, $pos+3);

	$fileNameTo = $arkivDir . "/sub" . $directoryNumber . "/" . $row['fileName'] . "." . $row['fileDetectedExtension'];

	$pdfAConvertCommand = "gs  -dPDFA -dBATCH -dNOPAUSE -dUseCIEColor -sProcessColorModel=DeviceCMYK -sDEVICE=pdfwrite -sPDFACompatibilityPolicy=2  -sOutputFile=". $fileNameTo . " " .  $fileNameFrom ;

	if ($row['IN_PDF_A'] == true) {
		
		if (rename ($fileNameFrom, $fileNameTo) == true) {
			echo "(" . $count . ") Moved " . $fileNameFrom . " to " . $fileNameTo . "\n";	
		}
		else {
			echo "(" . $count . ") Error moving " . $fileName . " to " . $fileNameTo . "\n";
		}
	
	}
	else {
		$output = null;	
		exec ($pdfAConvertCommand, $output);
		echo $fileNameTo . " " . $output . "\n";

	}

	
	if ($count%10 == 0) {
		sleep(5);
	}
}

echo $count . " files copied \n";











