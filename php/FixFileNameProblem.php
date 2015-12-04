<?php

require_once "database/uttrekkMySQLBase.php";

$options = getopt("d::");

// -d database, name of kommune database

$directoryWithFiles = "/home/oracle/processing/"; 
$kommune = "";

if (isset($options["d"])) {
	$kommune = $options["d"];
}
else {
	echo "Usage script -d=kommune \n";
	exit;	
} 

echo "Running script with following options : \n";
print_r($options);


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
$directoryWithFiles = $directoryWithFiles . $kommune . "/";



$count = 0;

	$command = "ls " . escapeshellarg($directoryWithFiles);
	exec ($command, $output);




	//print_r($output);

	if ($output != null) {
		foreach ($output as $fileNameFrom) {

				$path_parts = pathinfo($fileNameFrom);
	
				$fileBaseName = $path_parts['basename'];
				// The following 3 statements result in fileID, which is the filename without an extension
				$fileExtension = $path_parts['extension'];
				$position = strpos($fileNameFrom, "_0001");

				if ($fileExtension == "jpg") {
					$filenamefixed =  substr($fileNameFrom, 0, $position);
 					if (rename ($directoryWithFiles.$fileNameFrom, $directoryWithFiles .$filenamefixed . ".JPG") == true) {
						echo "mv " . $fileNameFrom . " to ". $filenamefixed . ".JPG\n";
					}
					else
						echo "ERROR mv " . $fileNameFrom . " to ". $filenamefixed . ".JPG\n";
				}
				else if ($fileExtension == "pdf") {
					$filenamefixed = $fileBaseName . ".PDF";
					echo "filename " . $filenamefixed;  
// 					if (rename ($directoryWithFiles.$fileNameFrom, $directoryWithFiles .$filenamefixed ) == true) {
//						echo "mv " . $fileNameFrom . " to ". $filenamefixed . ".PDF\n";
//					}
//					else
//						echo "ERROR mv " . $fileNameFrom . " to ". $filenamefixed . ".PDF\n";
				}

	
		}
	}	


/*
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



	*/










