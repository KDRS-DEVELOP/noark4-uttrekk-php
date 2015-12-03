<?php

require_once 'models/ArkivDel.php';
require_once "utility/Utility.php";
require_once 'models/Noark4Base.php';

class ArkivDelDAO extends Noark4Base {
		
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ARKIVDEL'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);

		$this->selectQuery = "select FYSARK, BESKRIVELSE, ARKIV, PERIODE, ASTATUS, PRIMNOK, BSKODE, FORTS, PAPIR, ELDOK, FRADATO, TILDATO, MERKNAD from " . $SRC_TABLE_NAME . "";
	} 
	
	function processTable () {
	
		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$arkivDel = new ArkivDel();
				$arkivDel->AD_ARKDEL = $result['FYSARK'];
				$arkivDel->AD_BETEGN = $result['BESKRIVELSE'];				
				$arkivDel->AD_ARKIV = $result['ARKIV'];
				$arkivDel->AD_PERIODE = $result['PERIODE'];
				$arkivDel->AD_ASTATUS = $result['ASTATUS'];
				// TODO: Confirm this
				$arkivDel->AD_PRIMNOK = $result['PRIMNOK'];
				// TODO : NOT IN FINAL VERSION!!!
				if  ($result['PRIMNOK'] == null)
					$arkivDel->AD_PRIMNOK = 'INGEN';
				
				$arkivDel->AD_BSKODE = $result['BSKODE'];
				$arkivDel->AD_FORTS = $result['FORTS'];
				$arkivDel->AD_PAPIR = $result['PAPIR'];
				$arkivDel->AD_ELDOK = $result['ELDOK'];
				$arkivDel->AD_FRADATO = Utility::fixDateFormat($result['FRADATO']);
				$arkivDel->AD_TILDATO = Utility::fixDateFormat($result['TILDATO']);					
				$arkivDel->AD_MERKNAD = $result['MERKNAD'];
				$this->writeToDestination($arkivDel);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {		
		
		$sqlInsertStatement = "INSERT INTO ARKIVDEL (AD_ARKDEL, AD_BETEGN, AD_ARKIV, AD_ASTATUS, AD_PERIODE, AD_PRIMNOK, AD_BSKODE, AD_FORTS, AD_PAPIR, AD_ELDOK, AD_FRADATO, AD_TILDATO, AD_MERKNAD) VALUES (";
    	    	
		$sqlInsertStatement .= "'" . $data->AD_ARKDEL . "', ";
		$sqlInsertStatement .= "'" . $data->AD_BETEGN . "', ";
		$sqlInsertStatement .= "'" . $data->AD_ARKIV . "', ";
		$sqlInsertStatement .= "'" . $data->AD_ASTATUS . "', ";
		$sqlInsertStatement .= "'" . $data->AD_PERIODE . "', ";
		$sqlInsertStatement .= "'" . $data->AD_PRIMNOK . "', ";
		$sqlInsertStatement .= "'" . $data->AD_BSKODE . "', ";
		$sqlInsertStatement .= "'" . $data->AD_FORTS . "', ";
		$sqlInsertStatement .= "'" . $data->AD_PAPIR . "', ";
		$sqlInsertStatement .= "'" . $data->AD_ELDOK . "', ";		
		$sqlInsertStatement .= "'" . $data->AD_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->AD_TILDATO . "', ";
		$sqlInsertStatement .= "'" . $data->AD_MERKNAD . "'";			
		
		$sqlInsertStatement.= ");";
	
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

    }
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ARKIVDEL";
    	$mapping = array ('idColumn' => 'ad_arkdel', 
  				'rootTag' => 'ARKIVDEL.TAB',	
			    		'rowTag' => 'ARKIVDEL',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'AD.ARKDEL' => 'ad_arkdel',
							'AD.BETEGN' => 'ad_betegn',
							'AD.ARKIV' => 'ad_arkiv',
							'AD.ASTATUS' => 'ad_astatus',
							'AD.PERIODE' => 'ad_periode',
							'AD.PRIMNOK' => 'ad_primnok',
							'AD.BSKODE' => 'ad_bskode',
							'AD.FORTS' => 'ad_forts',
    						    	'AD.PAPIR' => 'ad_papir',
    							'AD.ELDOK' => 'ad_eldok',
    							'AD.FRADATO' => 'ad_fradato',
    							'AD.TILDATO' => 'ad_tildato',
							'AD.MERKNAD' => 'ad_merknad'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			