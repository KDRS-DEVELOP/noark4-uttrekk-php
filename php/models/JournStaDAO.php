<?php

require_once 'models/JournSta.php';
require_once 'models/Noark4Base.php';

class JournStaDAO  extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('JOURNSTA'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select STATUS, BESKRIVELSE, ANSVAR, DOKKONTR, EKSPEDERT, FORARKIV, FORLEDER, FORSAKSBEH, FOREKST, FORINT from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {	
		
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);

		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$journSta = new JournSta();
				$journSta->JS_STATUS = $result['STATUS'];
				$journSta->JS_BETEGN = $result['BESKRIVELSE'];
				$journSta->JS_DOKKONTR = $result['DOKKONTR'];
				$journSta->JS_EKSPEDERT = $result['EKSPEDERT'];
				$journSta->JS_FORARKIV = $result['FORARKIV'];
				$journSta->JS_FORLEDER = $result['FORLEDER'];
				$journSta->JS_FORSAKSBEH = $result['FORSAKSBEH'];
				$journSta->JS_FOREKST= $result['FOREKST'];
				$journSta->JS_FORINT = $result['FORINT'];
				$journSta->JS_ANSVAR = $result['ANSVAR'];
				
				$this->writeToDestination($journSta);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO JOURNSTA (JS_STATUS, JS_BETEGN, JS_DOKKONTR, JS_EKSPEDERT, JS_FORARKIV, JS_FORLEDER, JS_FORSAKSBEH, JS_FOREKST, JS_FORINT, JS_ANSVAR) VALUES (";

		$sqlInsertStatement .= "'" . $data->JS_STATUS . "', ";
		$sqlInsertStatement .= "'" . $data->JS_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->JS_DOKKONTR . "', ";
		$sqlInsertStatement .= "'" . $data->JS_EKSPEDERT . "', ";
		$sqlInsertStatement .= "'" . $data->JS_FORARKIV . "', ";
		$sqlInsertStatement .= "'" . $data->JS_FORLEDER . "', ";
		$sqlInsertStatement .= "'" . $data->JS_FORSAKSBEH . "', ";
		$sqlInsertStatement .= "'" . $data->JS_FOREKST . "', ";
		$sqlInsertStatement .= "'" . $data->JS_FORINT . "', ";
		$sqlInsertStatement .= "'" . $data->JS_ANSVAR . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

	}

 	function createXML($extractor) {    
		$sqlQuery = "SELECT * FROM JOURNSTA";
		$mapping = array ('idColumn' => 'js_status', 
					'rootTag' => 'JOURNSTATUS.TAB',	
						'rowTag' => 'JOURNSTATUS',
							'encoder' => 'utf8_decode',
								'elements' => array(
									'JS.STATUS' => 'js_status',
									'JS.BETEGN' => 'js_betegn',
									'JS.ANSVAR' => 'js_ansvar',
									'JS.DOKKONTR' => 'js_dokkontr',
									'JS.EKSPEDERT' => 'js_ekspedert',
									'JS.FORARKIV' => 'js_forarkiv',
									'JS.FORLEDER' => 'js_forleder',
									'JS.FORSAKSBEH' => 'js_forsaksbeh',
									'JS.FOREKST' => 'js_forekst',
									'JS.FORINT' => 'js_forint'
									) 
							) ;
			
		$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
	}
 }
