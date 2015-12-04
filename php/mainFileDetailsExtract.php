<?php

require_once 'Database.php';

/**
 * Documentation, License etc.
 *
 * @package PDFAFileConvert
 
 MySQL Table used:


Create table ConvertProcessInfo (
kommuneBase char (7),
fileName varchar (20),
fileOriginalExtension char (4),
fileDetectedExtension char (4),
fileOriginalSize varchar (12),
fileConvertedExtension char (4),
fileConvertedSize varchar (12),
md5HashValue char (32),
fileConvertedHashValue char (32),
fileLocatedInFolder varchar (255),
PRIMARY KEY(kommuneBase, fileName, md5HashValue )
) engine =InnoDB;
ALTER TABLE ConvertProcessInfo ADD COLUMN IN_PDF_A boolean


Create table FilesNotInDatabase (
kommuneBase char (7),
fileName varchar (20),
fileOriginalExtension char (4),
fileOriginalSize varchar (12),
fileLocatedInFolder varchar (255),
PRIMARY KEY(kommuneBase, fileName, fileOriginalExtension)
) engine =InnoDB;


|| 
		     strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 || 
		     strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 ||  strcasecmp($fileOriginalExtension, "") == 0 || 
		     strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 || 
		     strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 ||  strcasecmp($fileOriginalExtension, "") == 0 || 
		     strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 || 
		     strcasecmp($fileOriginalExtension, "") == 0 || strcasecmp($fileOriginalExtension, "") == 0 ||  strcasecmp($fileOriginalExtension, "") == 0 || 

*/

  if (count($argv) != 6 ) {
    echo "You must specify the directory to process and this  must be run from the commandline - kommune and folder\n"; 
    exit;
  }
 

  $db_host = 'localhost';
  $db_user = 'uttrekkBruker';
  $db_pswd = 'noark4uttrekk';
  $db_database = 'Noark4'; 
  
  try {
    $database = new Database($db_host, $db_user, $db_pswd, $db_database);
  }
  catch (Exception $e) {
    echo $e->getMessage();
  }
  
  $kommune =  $argv[1];
  $inputDir = $argv[2];
  $startAt = $argv[3];
  $stopAt = $argv[4]; // can be a large number of dirs labeled subXXX. Can say when to stop. Quick and dirty method!
  $folderType = $argv[5]; // postmottakk etc
  $outputDir = "/media/tsodring/My Passport/pdfA/" . $kommune . "/";
  $processingDir = "/home/tsodring/pdfaConvert/in/";
  
   if (is_dir ($outputDir) == false) {
    mkdir($outputDir, 0700);
    echo "Made " . $outputDir ."\n";
    }

   $outputDir .=  $folderType . "/";

   if (is_dir ($outputDir) == false) {
    mkdir($outputDir, 0700);
    echo "Made " . $outputDir ."\n";
    }
   
   //make output directory/subdirectory
 
 
  echo "Processing script with following values :";
  
  echo "kommune (" . $kommune . ")\n";
  echo "inputDir (" . $inputDir . ")\n";
  echo "startAt (" . $startAt . ")\n";
  echo "stopAt (" . $stopAt . ")\n";
  echo "folderType (" . $folderType . ")\n"; 
  echo "outputDir (" . $outputDir . ")\n";
  echo "processingDir (" . $processingDir . ")\n";
 
  // /media/tsodring/My Passport/filer/Ål_dokumenter/AL_dok_postkasse/d12
  // /media/tsodring/My Passport/filer/Ål_dokumenter/AL_dok_postkasse/d12/sub260
 
  // pick up all the subdirs and process eachone at a time
 
  
  if (is_dir ($outputDir) == false)
    mkdir($outputDir, 0700);
    
  if ($handle = opendir( $inputDir )) {
    while (false !== ($entry = readdir($handle))) {
	if ($entry != "." && $entry != ".." && is_dir($entry) == false) {

	  
	  $folderNumber = substr($entry, 3, 3);
	  
	  if (strcmp ($folderNumber, $startAt) >= 0 && strcmp ($folderNumber, $stopAt) <= 0) {

	    if (is_dir ($outputDir . "/sub" . $folderNumber) == false) {
		mkdir($outputDir . "/sub" . $folderNumber, 0700);
		echo "Made " . $outputDir ."\n";
	    }
	    echo $folderNumber  . " " . $entry . " ". "\n";
	    processDirectory($kommune, $inputDir."/".$entry, $database, $outputDir . "/sub" . $folderNumber , $processingDir);  	 
	  }
      } // if
  } // while
 closedir($handle);
 } // if  
  
 
  function processDirectory($kommune, $inputDir, $database, $outputDir, $processingDir) {
  
  if ($handle = opendir( $inputDir )) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && is_dir($entry) == false) {

	    $path_parts = pathinfo($entry);
	    $fileBaseName = $path_parts['basename'];
	    $fileOriginalExtension = $path_parts['extension']; 
	    $fileName =  $path_parts['filename'];        
	    $fileOriginalSize = @filesize($inputDir . "/" . $fileBaseName);
	    
	    
	    
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
	  echo "UNKNOWN FILE TYPE when copy " . $inputDir . "/" . $fileBaseName . " to " . $processingDir . "\n";
	} //if 
    } // while
    closedir($handle);
  }
}
 
 
 
 
 
 
 
