<?php

require_once "database/uttrekkMySQLBase.php";

$options = getopt("d::");

// -d database, name of kommune database
// -p stopAt start at numeric directory number
// -t startAt,
// -s simulate, just print what you would do to screen
// -i directory, input directory to start processing from
// -o directory, output directory where to copy/mv files to

$directoryWithFiles = "/home/oracle/processing/from"; 
 
$kommune = "";
$fileType = "";


if (isset($options["d"])) {
	$kommune = $options["d"];
}
else {
	echo "Usage script -d=kommune \n";
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

$count = 0;

while (1) {
	
	// Potentially 100 000+ files in directory, don't want to process everything in one go as it probably will
	// be dog slow
	$command = "ls -U " . escapeshellarg($directoryWithFiles) . " | head -n 1000";
	exec ($command, $output);

// I need to identify subXXX 
	
	print_r($output);

	if ($output != null) {
		foreach ($output as $fileNameFrom) {

			if (strpos($fileNameFrom, "PeTmpDir") == false) {

				$path_parts = pathinfo($fileNameFrom);
	
				$fileBaseName = $path_parts['basename'];
				// The following 3 statements result in fileID, which is the filename without an extension
				$fileConvertedExtension = $path_parts['extension'];
				$position = strpos($fileNameFrom, "." . $path_parts['extension']);
				$fileID =  substr($fileNameFrom, 0, $position);
		
		
				$directoryNumber = "";
				$sqlStatement = " select fileLocatedInFolder from ConvertProcessInfo where kommuneBase = '". $kommune .  "' AND  fileName = '" . $fileID . "'";
				$result = $uttrekkMySQLBase->executeQuery($sqlStatement);
				
				if ($result != null) {
					$fileLocatedInFolder = $result['fileLocatedInFolder'];
					$pos = strrpos($fileLocatedInFolder, "sub");
					
					$directoryNumber = substr($fileLocatedInFolder, $pos+3);
				}
				
		
				$fileToCopyFrom =  $directoryWithFiles . "/" .  $fileNameFrom;
				$fileToCopyTo = $arkivDir . "/sub" . $directoryNumber . "/" . $fileNameFrom;
		
	
				echo "(" . $fileToCopyFrom . ")(" . $fileToCopyTo .")\n";
		
				// The rename function is used in php to mv a file
				
				if (copy ($fileToCopyFrom, $fileToCopyTo ) == true) {
					//echo  $fileNameFrom . " " . $fileID . "\n";
					echo "(" . $count++ . ") copied " . $fileToCopyFrom . " to " . $fileToCopyTo . "\n";
					
		
					$hashValue =  hash_file('md5', $fileToCopyTo);							
					$fileConvertedSize = @filesize($fileToCopyTo);
		
					$sqlStatement = "UPDATE ConvertProcessInfo SET fileConvertedExtension='" . $fileConvertedExtension."',";
					$sqlStatement .= " fileConvertedSize = '".$fileConvertedSize ."',";	
					$sqlStatement .= " fileConvertedHashValue = '". $hashValue ."'";
					$sqlStatement .= " WHERE kommuneBase = '" . $kommune . "' AND ";
					$sqlStatement .= " fileName = '" . $fileID. "';";
		
					//echo $sqlStatement . "\n";
		
					if ($uttrekkMySQLBase->executeStatement($sqlStatement) == false) {
						echo "Error Executing " . $sqlStatement . "\n";
					}
					if (unlink($fileToCopyFrom) == false) {
						echo "Error Cannot delete" . $fileToCopyFrom . "\n";
					}
				}
				else {
					echo "(" . $count . ")Error Copying " . $fileNameFrom . " to " . $arkivDir . "\n";
				}
			} // if isdir
		} // for each
	}
	sleep(1);
	$output = null; 

}

echo "\n" . $count . " files copied to " .  $arkivDir . "\n";













