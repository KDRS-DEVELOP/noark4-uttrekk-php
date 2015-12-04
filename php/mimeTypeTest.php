<?php

if (count($argv) != 2 ) {
    echo "You must specify the filename\n"; 
    exit;
}

// Office files start with D0CF11E (HEX values) offset 0 from begining of file
// After 512 bytes, a new header chunk starts and this contains PPT/DOC/XLS specificaiton
// The big problem with this ocde is that a word file could contin an excel file withing
// I don't expect this to happen but this is a best guess approach

$wordProID = "576F726450726F";
$wordPerfectID = "81CDAB";
$msOfficeID = "D0CF11E0A1B11AE1";
$msWordFileSubID = "576F72642E446F63756D656E742E";
$msPptFileSubID = "50006F0077006500720050006F0069006E007400200044006F00630075006D0065006E00";
$msExcelFileSubID = "4D6963726F736F667420457863656C00";
$jpgFileID = "FFD8FFE0";
$gifFileID = "47494638"; // GIF also include either 3761 or 3961
$pdfFileID = "25504446";
$pngFileID = "89504E470D0A1A0A";
$msOfficeXversions = "504B030414000600"; // No sub identification as with older office
$rtfFileID = "7B5C72746631";
$tiffFileID = "49492A00";
$htmlFileID = "3C21444F43545950452048544D4C"; // No guarantee it starts like this, but for this it's OK
$html2FileID = "3C68746D6C"; // No guarantee it starts like this, but for this it's OK
$fileName = $argv[1];
// some magic numbers use lowercase instead of uppercase!! therefor strtoupper
// Do I want to limit file type size?
$dataAll = strtoupper(bin2hex(file_get_contents($fileName)));
$fileType = null;

 // Check if it's an office document
if (strpos (substr($dataAll, 0, strlen($msOfficeID)), $msOfficeID) !== FALSE) {
	echo strlen($dataAll) . " " . strlen($msWordFileSubID) .  " MS Office file\n" ;
	// Check if office file is doc
	if (strpos ($dataAll,  $msWordFileSubID) !== FALSE) {
		$fileType = "DOC";
	}
	else if (strpos ($dataAll,  $msExcelFileSubID) !== FALSE) {
		$fileType = "XLS";
	}
	else if (strpos ($dataAll,  $msPptFileSubID) !== FALSE) {
		$fileType = "PPT";
	}
}
else if (strpos (substr($dataAll, 0, strlen($wordProID)),  $wordProID) !== FALSE) {
	$fileType = "LWP";
}
else if (strpos (substr($dataAll, 0, strlen($jpgFileID)),  $jpgFileID) !== FALSE) {
	$fileType = "JPG";
}
else if (strpos (substr($dataAll, 0, strlen($gifFileID)),  $gifFileID) !== FALSE) {
	$fileType = "GIF";
}
else if (strpos (substr($dataAll, 0, strlen($pngFileID)),  $pngFileID) !== FALSE) {
	$fileType = "PNG";
}
else if (strpos (substr($dataAll, 0, strlen($pdfFileID)),  $pdfFileID) !== FALSE) {
	$fileType = "PDF";
}
else if (strpos (substr($dataAll, 0, strlen($msOfficeXversions)),  $msOfficeXversions) !== FALSE) {
	$fileType = "MSOFFICEX";
}
else if (strpos (substr($dataAll, 0, strlen($rtfFileID)),  $rtfFileID) !== FALSE) {
	$fileType = "RTF";
}
else if (strpos (substr($dataAll, 0, strlen($tiffFileID)),  $tiffFileID) !== FALSE) {
	$fileType = "TIF";
}
else if (strpos (substr($dataAll, 0, strlen($htmlFileID)),  $htmlFileID) !== FALSE) {
	$fileType = "HTML";
}
else if (strpos (substr($dataAll, 0, strlen($html2FileID)),  $html2FileID) !== FALSE) {
	$fileType = "HTML";
}
else if (strpos (substr($dataAll, 0, strlen($wordPerfectID)),  $wordPerfectID) !== FALSE) {
	$fileType = "WPF";
}
  



echo $fileName . " is a " . $fileType  . " file \n";

















?>
