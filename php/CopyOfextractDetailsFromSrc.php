<?php

// Do a check for VARIANT to see if txt, jpg etc are in ARKIVFORMAT 

require_once "database/SrcDB.php";
require_once "database/uttrekkMySQLBase.php";

$options = getopt("d::t:p:s:i::o:");

// -d database, name of kommune database
// -p stopAt start at numeric directory number
// -t startAt,
// -s simulate, just print what you would do to screen
// -i directory, input directory to start processing from
// -o directory, output directory where to copy/mv files to 

$processSubset = false; // prcoess all files
$simulateConversion = true;  //Don't copy / mv anything, just tell what you will do 
$outputDir = "";
$startAt = "";
$stopAt = "";
 

echo "Running script with following options : \n"; 
print_r($options);

$src_db_ini_array = parse_ini_file("ini/src_db.ini");
// For this script all these are known and won't change
$src_db_host = $src_db_ini_array['src_db_host'];
$src_db_port = $src_db_ini_array['src_db_port'];
$src_db_name = $src_db_ini_array['src_db_name'];
$src_db_user = $src_db_ini_array['src_db_user'];
$src_db_pswd = $src_db_ini_array['src_db_pswd'];
	
$srcBase = null;
	
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

$ORACLE_SID = "ORACLE_SID=" . $options["d"]; 
putenv ($ORACLE_SID);
$src_db_sid  = $options["d"];
$kommune = $options["d"];
$srcBase = null;
$uttrekkMySQLBase = null;
	
try {
	$srcBase = new SrcBase($src_db_host, $src_db_port, $src_db_name, $src_db_user, $src_db_pswd, $src_db_sid);
	$uttrekkMySQLBase = new UtrekkMySQLBase($uttrekk_db_host, $uttrekk_db_user, $uttrekk_db_pswd, $uttrekk_db_database);
}
catch (Exception $e) {
	echo $e->getMessage();
	die;
}


if (isset($options["t"]) && is_numeric($options["t"])) 
	$startAt = intval($options["t"]);
if (isset($options["p"]) && is_numeric($options["p"]))
	$stopAt = intval($options["p"]);

if 	($startAt >= 0 && $stopAt >= 0) {
	$processSubset = true;
}
  
	
//if (is_numeric($options["t"]) && !is_numeric($options["p"])) {
	//echo "start set but not stop\n";
	//die;	
//}
//else if (!is_numeric($options["t"]) && is_numeric($options["p"])) {
//	echo "stop set but not start\n";
//	die;
//}
if (!isset($options["i"]) || !isset($options["o"])){
	echo "\n Missing either input/output directory to process!!";
	die;		
}	
else {
	$inputDir = $options["i"]; //"/home/oracle/produksjonfiler/gol/GO_dok_9x til 2007/d12/";
	$outputDir =  $options["o"];
} 

if (isset($options["s"]) && ($options["s"] == "N" || $options["s"] == "n")){
	$simulateConversion = false;

}
 
if ($handle = opendir( $inputDir )) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != ".." && is_dir($entry) == false) {
			$folderNumber = intval(substr($entry, 3, 3));
			
			
			
			if ($folderNumber >= $startAt  && $folderNumber <= $stopAt) {
				if ($simulateConversion == false) { 
		  			//if (is_dir ($outputDir . "/sub" . $folderNumber) == false) {
		  			//	mkdir($outputDir . "/sub" . $folderNumber, 0700);
	  				//	//echo "Made " . $outputDir ."\n";
	  				//}
				}
	  			processDirectory($inputDir."/".$entry, $outputDir. "/sub" . $folderNumber, $srcBase, $uttrekkMySQLBase, $simulateConversion,  $kommune);
	  		} // if
		} // if
	} // while
	closedir($handle);
} // if


function determineFileType($detailsFromDB) {
			
		if ($detailsFromDB == null)
			return null;
	
		$fileFormatDescription = $detailsFromDB['VERKTOY'];
	 	$pcfiltype = $detailsFromDB['PCFILTYPE'];
	 	
		// The idea behind this code is that the db doesn't always identify file type e.g a doc file can be .br2 etc
		// There seems to be about 20 different unknown extentions. VERKTOY often tells the progam e.g Word 97 but 
		// sometimes it has a null value. So the idea is to try first and use VERKTOY and if it is empty try and pick up the
		// extension from PCFILTYPE. If that fails use externl file identification tools and hope conversion software can handle it.
		// I think this approach captures over 99% of the files
		
		$detectedFileType = "";
	
		if (strcasecmp($fileFormatDescription, "RA-TIFF6") == 0) {
			$detectedFileType = "TIF";
		}
		else if (strcasecmp($fileFormatDescription, "Excel 5.0") == 0) {
			$detectedFileType = "XLSX";
		}
		else if (strcasecmp($fileFormatDescription, "Word 2007") == 0) {
			$detectedFileType = "DOCX";
		}
		else if (strcasecmp($fileFormatDescription, "RA-TEKST") == 0) {
			$detectedFileType = "TXT";
		}
		else if (strcasecmp($fileFormatDescription, "RA-JPEG") == 0) {
			$detectedFileType = "JPG";
		}
		else if (strcasecmp($fileFormatDescription, "Excel 7.0") == 0) {
			$detectedFileType = "XLS";
		}
		else if (strcasecmp($fileFormatDescription, "K2000 PDF-Scan") == 0) {
			$detectedFileType = "PDF";
		}
		else if (strcasecmp($fileFormatDescription, "Excel 97") == 0) {
			$detectedFileType = "XLS";
		}
		else if (strcasecmp($fileFormatDescription, "RA-XML") == 0) {
			$detectedFileType = "XML";
		}
		else if (strcasecmp($fileFormatDescription, "HTM") == 0) {
			$detectedFileType = "HTM";
		}
		else if (strcasecmp($fileFormatDescription, "HTML") == 0) {
			$detectedFileType = "HTML";
		}
		else if (strcasecmp($fileFormatDescription, "Word 97") == 0) {
			$detectedFileType = "DOC";
		}
		else if (strcasecmp($fileFormatDescription, "RA-PDF") == 0) {
			$detectedFileType = "PDF";
		}
		else // An unregistered file type
		{
			if ($pcfiltype != null)
				$detectedFileType = $pcfiltype; 
			else {
				echo "Unknown file format description  - dumping information \n";
				echo " " . $detectedFileType  ." " .$pcfiltype;
				echo "Unable to handle - must be fixed - exiting non graciously!!\n";
				exit;
			}
		}
	return $detectedFileType;
}


function getFileDetails($srcDB, $fileName) {	
	$result = getFileDetailsFromDatabase($srcDB, $fileName);	
	return determineFileType($result);	
}

function getFileDetailsFromDatabase($srcDB, $fileName) {
	$sqlStatement = " SELECT REDIGERTAV, PCFILTYPE, VERKTOY, VARIANT FROM FILER WHERE PCFIL = '" . $fileName . "'";
	return $srcDB->executeQueryAndGetResult($sqlStatement);		
}



function processDirectory($inputDir, $outputDir,  $srcDB, $fileInfoDB, $simulateConversion, $kommune) {
	
	// open a directory
	if ($handle = opendir( $inputDir )) {
		echo "Processing" . $inputDir . "\n";
		// go through entry in the directory
		while (false !== ($entry = readdir($handle))) {
			// if it's not a directory
			if ($entry != "." && $entry != ".." && is_dir($entry) == false) {
				
				$variant = "P";
				
				
				// Find the various parts of the file
				$path_parts = pathinfo($entry);
				$detectedFileType = "";
				$fileBaseName = $path_parts['basename'];
				
				// The following 3 statements result in filename without an extension
				$fileOriginalExtension = $path_parts['extension'];
				$position = strpos($entry, "." . $path_parts['extension']);				
				$fileName =  substr($entry, 0, $position);
				
				// get the file size
				$fileOriginalSize = @filesize($inputDir . "/" . $fileBaseName);

				
				if ($fileOriginalExtension == null) {
					print_r($path_parts);
					echo "No extension . Non gracious exit!";
					die;					
				}
				
				
				// First see if we know about this file
				$detectedFileType = getFileDetails($srcDB, $fileBaseName);

				if ($detectedFileType == null) {

					
					
					$detectedFileType = getFileDetails($srcDB, $fileName);
				}
				
				
				
				// If we know this file has information
				if ($result != null) {
					$detectedFileType = determineFileType($fileFormatDescription, $pcfiltye);
				}
				else {
					
					
					// This file is not registered in the database, can because the filename is wrong and a change is needed.
					//echo  "file type check file (" . $fileOriginalExtension . ") detected file type (" . $detectedFileType . ")\n";
					
					//print_r($path_parts); 
					
					
					
					if (strcasecmp($fileOriginalExtension, "PDF") == 0) {
						$pos = strpos ($fileBaseName, "-A1"); 
						if ($pos != false) {
							$variant = "A";
							$detectedFileType = "PDF";
						}						 
					}
					else if (strcasecmp($fileOriginalExtension,"doc") == 0) {
						$detectedFileType = $fileOriginalExtension; 						
					}
					else {
						// There are a few different cases to check for
						// Case 1:						
						// Sometimes a filename on disk starts with '0', but the entry in the database is missing this '0'
						// Case 2:
						// PDF files created by (REDIGERTAV) with value 'OPP'. The PCFIL  values are always missing the file extension!!
						// and the filename starts with 0  
						
						echo "File not found in database. k(" . $kommune . "), file(" . $inputDir . "/". $fileName  . "." . $fileOriginalExtension . "), size(" .  $fileOriginalSize . ")\n";
						if (strncmp($fileName, "0", 1) == 0) {
							
							echo "File starts with a 0 so checking for missing 0 problem (" . substr($fileBaseName, 1) . ") \n"; 
							
				
							
							
							if ($result != null) {		
							
								// 	If we know this file has information
							
								// IS it Produksjon or ARKIV format
								$variant = $result['VARIANT'];
								//What tool is used to access the file
								$fileFormatDescription = $result['VERKTOY'];
								// What is the registered file extension (a doc file may not neceiserily have a .doc extension)
								$pcfiltye =  $result['PCFILTYPE'];
								// Identify the actual filetype from either the tool to access it or fall back on registered extension 
								$detectedFileType = determineFileType($fileFormatDescription, $pcfiltye);
								echo "Updated not found in database. k(" . $kommune . "), file(" . $inputDir . "/". $fileName  . "." . $fileOriginalExtension . "), size(" .  $fileOriginalSize . ")\n";
							}
							else {
								
								
								// PDF files created by (REDIGERTAV) with value 'OPP'. The PCFIL  values are always missing the file extension!!
								// and the filename starts with 0  
								echo "Checking if this is a file missing a 0, PDF file with missing extension in database (" . substr($fileBaseName, 1) . ".) \n";
								$pos = strpos ($fileName, "-A1"); 
								if ($pos != false) {											
									$fileNameToCheck = substr($fileName, 1, $pos-1) . ".";
									
									echo "Checking (" . $fileNameToCheck . ") \n";
									
									$result = getFileDetails($srcDB, $fileNameToCheck);
								}						 
								
								if ($result != null && $result['REDIGERTAV'] == "OPP") {
									// IS it Produksjon or ARKIV format
									$variant = $result['VARIANT'];
									//What tool is used to access the file
									$fileFormatDescription = $result['VERKTOY'];
									// What is the registered file extension (a doc file may not neceiserily have a .doc extension)
									$pcfiltye =  $result['PCFILTYPE'];
									// Identify the actual filetype from either the tool to access it or fall back on registered extension 
									$detectedFileType = determineFileType($fileFormatDescription, $pcfiltye);
								}	
								else {
									echo "File still not found in database \n";
									$detectedFileType = $fileOriginalExtension;
									$sqlStatementBase = "INSERT IGNORE INTO FilesNotInDatabase (kommuneBase, fileName, fileOriginalExtension, fileOriginalSize, fileLocatedInFolder) VALUES (";
									$sqlStatement = $sqlStatementBase . "'" . $kommune . "', '" . $fileName  . "', '" . $fileOriginalExtension . "', '" . $fileOriginalSize . "', '" . $inputDir . "');";
									$fileInfoDB->executeStatement ($sqlStatement);
								}
							}									
						} else {
							
								echo "Checking if this is a , PDF file with missing extension in database (" . $fileBaseName . ".) \n";

								$pos = strpos ($fileName, "-A1"); 
								if ($pos != false) {											
									$fileNameToCheck = substr($fileName, 0, $pos-1) . ".";
									
									echo "Checking (" . $fileNameToCheck . ") \n";
									
									$result = getFileDetails($srcDB, $fileNameToCheck);
								}						 
																
								if ($result != null && $result[REDIGERTAV] == "OPP") {
									// IS it Produksjon or ARKIV format
									$variant = $result['VARIANT'];
									//What tool is used to access the file
									$fileFormatDescription = $result['VERKTOY'];
									// What is the registered file extension (a doc file may not neceiserily have a .doc extension)
									$pcfiltye =  $result['PCFILTYPE'];
									// Identify the actual filetype from either the tool to access it or fall back on registered extension 
									$detectedFileType = determineFileType($fileFormatDescription, $pcfiltye);
								}
								else {	
									echo "File not found in database. k(" . $kommune . "), file(" . $inputDir . "/". $fileName  . "." . $fileOriginalExtension . "), size(" .  $fileOriginalSize . ")\n";
									$sqlStatementBase = "INSERT IGNORE INTO FilesNotInDatabase (kommuneBase, fileName, fileOriginalExtension, fileOriginalSize, fileLocatedInFolder) VALUES (";
									$sqlStatement = $sqlStatementBase . "'" . $kommune . "', '" . $fileName  . "', '" . $fileOriginalExtension . "', '" . $fileOriginalSize . "', '" . $inputDir . "');";
								  	//	echo $sqlStatement . "\n"; 
									$fileInfoDB->executeStatement ($sqlStatement);
									$detectedFileType = $fileOriginalExtension;
								}
							}
						
					}
				}

				// ALready in arkivformat, just mv file to output directory
					if ($simulateConversion == true) {
					
						$hashValue =  hash_file('md5', $inputDir ."/" . $fileBaseName);
						echo "s: mv $inputDir/$fileBaseName, $inputDir/$fileName.$detectedFileType hv (" . $hashValue . ")\n";	
					}
					else
						// rename the file with the correct file extension
						if (rename ( $inputDir . "/".  $fileBaseName,   $inputDir . "/" . $fileName . ".". $detectedFileType) == true) {
							// do a md5 checksum for authenticity purposes
							$hashValue =  hash_file('md5', $inputDir . "/" . $fileName . ".". $detectedFileType);
							//echo "rename $inputDir/$fileBaseName, $inputDir/$fileName.$detectedFileType\n";
							
							
							$sqlStatementBase = "INSERT IGNORE INTO ConvertProcessInfo (kommuneBase, fileName, fileOriginalExtension, fileDetectedExtension, fileOriginalSize, md5HashValue, fileLocatedInFolder) VALUES (";
						  	$sqlStatement = $sqlStatementBase . "'" . $kommune . "', '" . $fileName  . "', '" . $fileOriginalExtension . "', '" . $detectedFileType  . "', '" . $fileOriginalSize . "', '" . $hashValue  . "', '" . $inputDir . "');";
						  //	echo $sqlStatement . "\n"; 
						 	$fileInfoDB->executeStatement ($sqlStatement);
					} // if
				 
				} //if
			} // while
			closedir($handle);
		}
}



/*
 * 
 * 
 * 
				$arkivFormat = false;

				if ($arkivFormat == true) {

				

				}

				if (strcasecmp($fileOriginalExtension, "txt") == 0 || strcasecmp($fileOriginalExtension, "jpg") == 0 || strcasecmp($fileOriginalExtension, "xml") == 0) {


					$hashValue =  hash_file('md5', $inputDir . "/" . $fileBaseName);
					//echo "k(". $kommune . ") d(" .  $inputDir . ") fn(" .  $fileName . ") fbn(" . $fileBaseName  . ") fE(" .  $fileOriginalExtension . ") fS(" .  $fileOriginalSize .")" . " h (" . $hashValue  . ")\n";

					// waste of time converting txt files. pixedit also has charcheter set problems with norwegian letters
					if (strcasecmp($fileOriginalExtension, "txt") == 0 || strcasecmp($fileOriginalExtension, "jpg") == 0 || strcasecmp($fileOriginalExtension, "xml") == 0) {
						$database->addEntryConverted($kommune, $inputDir, $fileName, $fileOriginalExtension, $fileOriginalSize, "txt", $fileOriginalSize, $hashValue);

						if (copy ($inputDir . "/".  $fileBaseName,  $outputDir . "/" . $fileBaseName) == true) {
							echo "copied " . $inputDir . "/" . $fileBaseName . " to " . $outputDir . "/" . $fileBaseName . "\n";
						}
						else {
							echo "ERROR when trying to copy " . $inputDir . "/" . $fileBaseName . " to " . $outputDir . "\n";
						}
					}
					else if (strcasecmp($fileOriginalExtension, "pdf") == 0 ) {
						//copy file to InTS
						$database->addEntry($kommune, $inputDir, $fileName, $fileOriginalExtension, "pdf", $fileOriginalSize, $hashValue);
						if (copy ($inputDir . "/" . $fileBaseName,  $processingDir) == true) {
							echo "copied " . $inputDir . "/" . $fileBaseName . " to " . $processingDir;
						}
						else {
							echo "ERROR when trying to copy " . $inputDir . "/" . $fileBaseName . " to " . $processingDir . "\n";
						}
					}
					else if (strcasecmp($fileOriginalExtension, "rhl") == 0 || strcasecmp($fileOriginalExtension, "rss") == 0 || strcasecmp($fileOriginalExtension, "rsp") == 0 ||
					strcasecmp($fileOriginalExtension, "rsl") == 0 || strcasecmp($fileOriginalExtension, "rhu") == 0 ||  strcasecmp($fileOriginalExtension, "rmb") == 0 ||
					strcasecmp($fileOriginalExtension, "not") == 0 || strcasecmp($fileOriginalExtension, "s") == 0 || strcasecmp($fileOriginalExtension, "ink") == 0 ||
					strcasecmp($fileOriginalExtension, "for") == 0 || strcasecmp($fileOriginalExtension, "b1") == 0 ||  strcasecmp($fileOriginalExtension, "ref") == 0 ||
					strcasecmp($fileOriginalExtension, "rsr") == 0 || strcasecmp($fileOriginalExtension, "B1T") == 0 || strcasecmp($fileOriginalExtension, "b2") == 0 ||
					strcasecmp($fileOriginalExtension, "bft") == 0 || strcasecmp($fileOriginalExtension, "lb1") == 0 ||  strcasecmp($fileOriginalExtension, "b2t") == 0 ||
					strcasecmp($fileOriginalExtension, "bfs") == 0 || strcasecmp($fileOriginalExtension, "rso") == 0){
						//copy file to InTS
						$database->addEntry($kommune, $inputDir, $fileName, $fileOriginalExtension, "pdf", $fileOriginalSize, $hashValue);
						if (copy ($inputDir . "/" . $fileBaseName,  $processingDir. "/" . $fileName . ".doc") == true) {
							echo "copied " . $inputDir . "/" . $fileBaseName . " to " . $processingDir. "/" . $fileName . ".doc";
						}
						else {
							echo "ERROR when trying to copy " . $inputDir . "/" . $fileBaseName . " to " . $processingDir . "\n";
						}

					}
					else
					echo "UNKNOWN FILE TYPE when copy " . $inputDir . "/" . $fileBaseName . " to " . $processingDir . "\n"; */
