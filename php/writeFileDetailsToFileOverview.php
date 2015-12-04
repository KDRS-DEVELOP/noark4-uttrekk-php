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


if (isset($options["d"])) {
	$kommune = $options["d"];
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
$uttrekk_db_database = $uttrekk_db_ini_array['file_overview_db_database'];


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

$baseDir = "/home/oracle/arkivfiler/";
$inputDir = $baseDir . $kommune . "/"; 

if ($handle = opendir( $inputDir )) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != ".." && is_dir($entry) == false) {
			$folderNumber = intval(substr($entry, 3, 3));
				processDirectory($inputDir.$entry. "/", $uttrekkMySQLBase, $kommune);
		} // if
	} // while
	closedir($handle);
} // if



function processDirectory($inputDir, $fileInfoDB, $kommune) {

	if ($handle = opendir( $inputDir )) {
		echo "Processing" . $inputDir . "\n";
		// go through entry in the directory
		while (false !== ($entry = readdir($handle))) {
			// if it's not a directory
			if ($entry != "." && $entry != ".." && is_dir($entry) == false) {


				// Find the various parts of the file
				$path_parts = pathinfo($entry);
				$detectedFileType = null;
				$fileBaseName = $path_parts['basename'];

				// The following 3 statements result in filename without an extension
				$fileExtension = $path_parts['extension'];
				$position = strpos($entry, "." . $path_parts['extension']);
				$fileName =  substr($entry, 0, $position);

				// get the file size
				$fileSize = @filesize($inputDir . "/" . $fileBaseName);
				$hashValue =  hash_file('md5', $inputDir ."/" . $fileBaseName);


				$sqlStatement = "INSERT INTO fileOverview (kommune, filename, filetype, fullfileName, fileSize, location, hashValue) VALUES ('" . $kommune . "', '"  . $fileName . "', '" . $fileExtension . "', '" . $fileBaseName . "', '" . $fileSize . "', '" . $inputDir .  "', '"  . $hashValue  . "')"; 

				$fileInfoDB->executeStatement($sqlStatement);

				echo $fileName . " ";

				if ($fileExtension == null) {
					print_r($path_parts);   
					echo "No extension . Non gracious exit!";
					die;
				} // if
			} // if
		} // while
	} // if
	closedir($handle);
} // function



/*


CREATE DATABASE FileOverview; 

USE FileOverview;

CREATE TABLE fileOverview 
(
kommune VARCHAR (10),
filename VARCHAR (100), 
filetype VARCHAR (40), 
fullfileName VARCHAR (400), 
fileSize INT, 
location VARCHAR (400),
hashValue CHAR(16),
PRIMARY KEY (kommune, filename)
);
/*



