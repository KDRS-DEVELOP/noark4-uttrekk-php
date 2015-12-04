<?php



// This file does have a lot of hardcoded stuff, but the standard is from 1999 and
// will not change!!

require_once 'utility/Constants.php';

class NoarkIHCreator {
	
	var $outputDir = ".";	
	var $XMLfilename = 'NOARKIH.XML';	
	var $uttrekksBase = null;
	var $selectTablesQuery = "Show tables;";
	var $selectColsQuery = "Show columns in " ;
	var $selectRowCountQuery = "Select count(*) AS TOTAL from ";
	var $exportInfo = null;

	var $colsToIgnore = array("SA_BEVTID", "SA_KASSKODE", "SA_KASSDATO", "SA_PROSJEKT", "SA_PRES", "SA_FRARKDEL","MD_AGDATO", "MD_AGKODE", "MD_BEVTID", "MD_KASSDATO", "MD_KASSKODE", "AI_ADMKORT", "AI_IDFAR", "TK_FRADATO");	

   

 
	public function NoarkIHCreator  ($uttrekksBase, $outputDir, $exportInfo) {		
		$this->uttrekksBase = $uttrekksBase;	
		$this->outputDir = $outputDir;
		$this->exportInfo = $exportInfo;
	} 

	public function  generateNoarkIH () {		

		$outputFileName = $this->outputDir . DIRECTORY_SEPARATOR . $this->XMLfilename;
		//echo "Opening file " . $outputFileName . " filename " . $this->XMLfilename . " dir(" . $this->outputDir . ")\n"; 
		if (($fh = fopen($outputFileName, "w")) != false) {
	
			fwrite($fh, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<!DOCTYPE NOARK.IH SYSTEM \"NOARKIH.DTD\">\n<NOARK.IH VERSJON=\"1.0\">\n");
	
			fwrite($fh, "\t<EKSPORTINFO>\n");
			
			$arkskaper = "\t\t<EI.ARKSKAPER>" . $this->exportInfo->arkskaper .  "</EI.ARKSKAPER>\n";
			fwrite($fh, $arkskaper);
	
			$systemName = "\t\t<EI.SYSTEMNAVN>" . $this->exportInfo->systemName . "</EI.SYSTEMNAVN>\n";
			fwrite($fh, $systemName);
	
			$kommune = "\t\t<EI.KOMMUNE>" .  $this->exportInfo->kommune . "</EI.KOMMUNE>\n";
			fwrite($fh, $kommune);
	
			$fraDato = "\t\t<EI.FRADATO>" . $this->exportInfo->fraDato . "</EI.FRADATO>\n";
			fwrite($fh, $fraDato);
	
			$tilDato = "\t\t<EI.TILDATO>" .  $this->exportInfo->tilDato . "</EI.TILDATO>\n";
			fwrite($fh, $tilDato);
			
			$prodDato = "\t\t<EI.PRODDATO>" .  $this->exportInfo->prodDato . "</EI.PRODDATO>\n";
			fwrite($fh, $prodDato);
	
			fwrite($fh, "\t</EKSPORTINFO>\n");
	
			$resultHandleForTables =  $this->uttrekksBase->executeQueryFetchResultHandle ($this->selectTablesQuery);
	
			
			while ($tableDetails = mysql_fetch_assoc($resultHandleForTables)) {
				
			
				fwrite($fh, "\t<TABELLINFO>\n");

				$tablenamePre = array_pop($tableDetails);
				//echo PHP_EOL . " **--** " . $tablename . " " . $tablenamePre . "\n";
				$tablename = Constants::mapTablenamesForNOARKIH(strtoupper($tablenamePre)); 
				//echo PHP_EOL . " **--** " . $tablename . " " . $tablenamePre . "\n";
				// Argh!! Why must the standard be so akward and require me to do this!!!
				if (is_null($tablename) == true) {
					$tablename = $tablenamePre;
				}

				$columnQuery = $this->selectColsQuery . " " . $tablenamePre;
	
	
				$tablenameLine = "\t\t<TI.TABELL>" . $tablename . "</TI.TABELL>\n";
				//echo $tablenameLine . PHP_EOL;
				
				

				fwrite($fh, $tablenameLine );
				fwrite($fh, "\t\t<ATTRIBUTTER>\n");
	
				$resultHandleForColumns = $this->uttrekksBase->executeQueryFetchResultHandle ($columnQuery);
				while ($columnDetails = mysql_fetch_assoc($resultHandleForColumns)) {
					$columnName= str_replace("_", ".", $columnDetails["Field"]);
					$attributeLine = "\t\t\t<TI.ATTR>" . $columnName . "</TI.ATTR>\n";

					if (isset($colsToIgnore[$columnDetails["Field"]]) == false) {
						fwrite($fh, $attributeLine);
					}
				}
				fwrite($fh, "\t\t</ATTRIBUTTER>\n");
				// This codebase only loads everything in one file. I guess this requirement
				// is from a time when filesystems supported smaller filesizes
				fwrite($fh, "\t\t<TI.ANTFILER>1</TI.ANTFILER>\n");
				mysql_free_result($resultHandleForColumns);
	

				$noarkXmlFileName = Constants::getXMLFilename(strtoupper($tablenamePre));
				fwrite($fh, "\t\t<FIL>\n");
				$noark4fileNameLine = "\t\t\t<TI.FILNAVN>". $noarkXmlFileName. "</TI.FILNAVN>\n";
				fwrite($fh, $noark4fileNameLine);

				$rowCountQuery = $this->selectRowCountQuery . $tablenamePre;				
				$resultHandleForRowCount = $this->uttrekksBase->executeQueryFetchResultHandle ($rowCountQuery);
				$rowCountDetails = mysql_fetch_assoc($resultHandleForRowCount);
				$numberOfRecords = $rowCountDetails ["TOTAL"];			
 				mysql_free_result($resultHandleForRowCount );
					
				$noark4NumberRecords = "\t\t\t<TI.ANTPOSTER>". $numberOfRecords . "</TI.ANTPOSTER>\n";
				fwrite($fh, $noark4NumberRecords);
				fwrite($fh, "\t\t</FIL>\n");
				fwrite($fh, "\t</TABELLINFO>\n");
				fflush($fh);
			}
	
			mysql_free_result($resultHandleForTables);
			fwrite($fh, "</NOARK.IH>\n");
			fclose($fh);	
		}
		else
			echo "Error opening file " . $outputFileName . "\n";
	} // function
}



?>