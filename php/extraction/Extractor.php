<?php

require_once "database/MySQLDB.php";
require_once "database/OracleDB.php";

class Extractor {


	protected $loggDir;
	private $db_handle = null;
	private $databaseType = null;
	protected $xmlFile = null;
	protected $uttrekkDirectory = null;


    public function __construct ($databaseType, $databaseParameters, $uttrekkDirectory) {

		$this->databaseType = $databaseType;
		$this->uttrekkDirectory = $uttrekkDirectory;
 
		if (strcasecmp($databaseType, 'mysql') == 0) {
			$this->db_handle = new MySQLDB($databaseParameters, null);
		}
		else if (strcasecmp($databaseType, 'oracle') == 0) {
			$this->db_handle = new OracleDB($databaseParameters, null);
		}
		else {
			throw new Exception("Unknown database type " . $databaseType);
		}
		$this->loggDir = $uttrekkDirectory . DIRECTORY_SEPARATOR . "logg";
	} 

    	public function __destruct () {
		$this->close();
	}

	public function close() {
		if (isset($xmlFile)) {
			flush($xmlFile);
			fclose($xmlFile);
		}		
	}



	public function getLogDir() {
		return $this->loggDir;
	}

	public function cleanDirectory () {
		$this->deleteDirectoryAndContents();
		$this->createDirectory ();		
	}

	
	function createDirectory() {
		if (is_dir($this->uttrekkDirectory) == false) {
			mkdir($this->uttrekkDirectory);
		}
		if (is_dir($this->uttrekkDirectory . DIRECTORY_SEPARATOR . "logg") == false) {
			mkdir($this->uttrekkDirectory . DIRECTORY_SEPARATOR . "logg");
		}

	}
	
	function deleteDirectoryAndContents() {
		// code taken from php manual
		if (is_dir($this->uttrekkDirectory) == true)
			foreach (scandir($this->uttrekkDirectory) as $item) {
				if ($item == '.' || $item = '..')
					continue;
				unlink($this->uttrekkDirectory.DIRECTORY_SEPARATOR.$item);									
			}
	}
	
	// I was using the xml2Query library, but wanted to make my code standalone
	// and dumping a database table in XML is trivial. xml2Query allowed me to map
	// db attribute (column) names to xml element names. However in this app
	// the columns names are proper, but I need to make sure they are uppercase 
	// and the "_" is replaced with a "."

	public function extract($sqlQuery, $mapping, $xmlFilename, $outputTo) {
		// Ignoring outputTo, but easily implementable
		// Using the query, filename
		
		$colMapping = $mapping['elements'];
		$xmlHeader = "<?xml version=\"1.0\" encoding=\"" . Constants::XML_ENCODING . "\"?>" . Constants::NEWLINE;
		$tabInfo = $mapping['rootTag'];
		$dtdInfo = $mapping['rowTag'] . ".DTD";
		$docType = "<!DOCTYPE " . $tabInfo . " SYSTEM \"" . $dtdInfo . "\">" . Constants::NEWLINE;

		$rootTag = "<" . $mapping['rootTag'] . " VERSJON=\"1.0\"" . ">" . Constants::NEWLINE; 
		$endRootTag = "</" . $mapping['rootTag'] . ">" . Constants::NEWLINE; 

		$startRowTag = "  <" . $mapping['rowTag'] . ">" . Constants::NEWLINE;
		$endRowTag = "  </" . $mapping['rowTag'] . ">" . Constants::NEWLINE; 
		
		if (isset($xmlFilename) == true) {
			$this->xmlFile = fopen($this->uttrekkDirectory . DIRECTORY_SEPARATOR . $xmlFilename, "w");
		}

		if (!$this->xmlFile) {
			echo "Cannot open XMLfile " . $xmlFilename;
		}
		
		fwrite($this->xmlFile, $xmlHeader);
		fwrite($this->xmlFile, $docType);
		fwrite($this->xmlFile, $rootTag);
		
		$this->db_handle->executeStatement($sqlQuery);
		echo "\t Number of rows in table to be written out to XML file " . $mapping['rowTag'] . " is (" .$this->db_handle->getNumRows() . ") ... (";
		$currentRow = 0;
		while ($this->db_handle->hasResult() == true) {			
			$currentRow++;
			$result = $this->db_handle->nextResult();	
			
			// This could potentially print a row tag 
			// with no subelements in it. The only time that 
			// could occur is when Primary Key is null
			// Include in SQL statement "where Primary Key != NULL"
			fwrite($this->xmlFile, $startRowTag);			
	
			foreach ($colMapping as $realColName => $tempColName) {
				
				// Do not print out empty tags
				// Are dates with 0000-00-00 values empty??
				// strtoupper because xml2query required column ids'
				// be lowercase even though the column ids are in uppercase
				// check to see if i need to use strtoupper

				$colNameUpper = strtoupper($tempColName);


				if (isset($result[$colNameUpper]) == true && $result[$colNameUpper] != null) {
					$startTag = "    <" . $realColName . ">";
					$value = $this->makeDataXMLSafe($result[$colNameUpper]);				
					$endTag = "</" . $realColName . ">";
					$row = $startTag . $value . $endTag . Constants::NEWLINE;
  
					fwrite($this->xmlFile, $row);
				}
			} // foreach
			
			fwrite($this->xmlFile, $endRowTag);			
		} // while
		echo $currentRow . ") written \n";
		fwrite($this->xmlFile, $endRootTag);
		flush($this->xmlFile);
		fclose($this->xmlFile);

	} // function extract

	function makeDataXMLSafe($input) {
		// an own function incase I want to add something extra
		return htmlentities($input);
	}
} // class      











?>