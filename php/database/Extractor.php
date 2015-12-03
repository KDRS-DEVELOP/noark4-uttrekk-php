<?php

require_once "MySQLDB.php";
require_once "OracleDB.php";

class Extractor {
        
	private $db_handle = null;
	private $databaseType = null;
	protected $xmlFile = null;

    public function __construct ($databaseType, $databaseParameters, $uttrekkDirectory) {

		$this->databaseType = $databaseType;

		if (strcasecmp($databaseType, 'mysql') == 0) {
			$this->db_handle = new MySQLDB($databaseParameters, null);
		}
		else if (strcasecmp($databaseType, 'oracle') == 0) {
			$this->db_handle = new OracleDB($databaseParameters, null);
		}
		else {
			throw new Exception("Unknown database type " . $databaseType);
		}
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
			$this->xmlFile = fopen($xmlFilename, "w");
		}

		if (!$this->xmlFile) {
			echo "Cannot open XMLfile " . $xmlFilename;
		}
		
		fwrite($this->xmlFile, $xmlHeader);
		fwrite($this->xmlFile, $docType);
		fwrite($this->xmlFile, $rootTag);
		
		$this->db_handle->executeStatement($sqlQuery);
		echo "Number of rows in table to be written out to XML file " . $this->db_handle->getNumRows() . "\n";
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
				echo "(" . $result[$tempColName] . ")\n";

				if (isset($result[$tempColName]) == true && $result[$tempColName] != null) {
					$startTag = "    <" . $realColName . ">";
					$value = $this->makeDataXMLSafe($result[$tempColName]);				
					$endTag = "</" . $realColName . ">";
					$row = $startTag . $value . $endTag . Constants::NEWLINE;
  
					fwrite($this->xmlFile, $row);
				}
			} // foreach
			
			fwrite($this->xmlFile, $endRowTag);			
		} // while
		echo "Actual number of rows in table that were written out to XML file " . $currentRow . "\n";
		fwrite($this->xmlFile, $endRootTag);
		flush($this->xmlFile);
		fclose($this->xmlFile);

	} // function extract

	function makeDataXMLSafe($input) {
		// an own function incase I want to add something extra
		return htmlentities($input);
	}
} // class      

