<?php


require_once 'models/UtvBehDo.php';
require_once 'models/Noark4Base.php';


class UtvBehDoDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('UTVBEHDO'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select * from " . $SRC_TABLE_NAME . "";		
	} 
	
	function processUtvBehDo($utvBehDo) {
		$this->writeToDestination($utvBehDo);
	}

	function processTable () {
	
		echo "I should not be called. I am handled in UtvSakDAO. find me in UtvBehDoDAO->processTable";
		die;


		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {

				$utvBehDo = new UtvBehDo();
				$utvBehDo->BD_JPID = $result[''];
				$utvBehDo->BD_BEHID = $result[''];
				$utvBehDo->BD_DOKID = $result[''];
				$utvBehDo->BD_DOKTYPE = $result[''];
				$utvBehDo->BD_STATUS= $result[''];
				$utvBehDo->BD_NDOKTYPE = $result[''];
				$this->writeToDestination($utvBehDo);
		}
		$this->srcBase->endQuery($this->selectQuery);

	}
	
	function writeToDestination($data) {
		
		$sqlInsertStatement = "INSERT INTO UTVBEHDO (BD_JPID, BD_BEHID, BD_DOKID, BD_DOKTYPE, BD_STATUS, BD_NDOKTYPE) VALUES (";

		$sqlInsertStatement .= "'" . $data->BD_JPID . "',";
		$sqlInsertStatement .= "'" . $data->BD_BEHID . "',";
		$sqlInsertStatement .= "'" . $data->BD_DOKID . "',";
		$sqlInsertStatement .= "'" . $data->BD_DOKTYPE . "',";
		$sqlInsertStatement .= "'" . $data->BD_STATUS . "',";
		$sqlInsertStatement .= "'" . $data->BD_NDOKTYPE. "'";

		$sqlInsertStatement.= ");";
		
		$this->uttrekksBase->executeStatement($sqlInsertStatement);

        }
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM UTVBEHDO";
    	$mapping = array ('idColumn' => 'bd_behid', 
  				'rootTag' => 'UTVBEHDOK.TAB',	
			    		'rowTag' => 'UTVBEHDOK',
  						'encoder' => 'utf8_decode',
  						'elements' => array(
							'BD.BEHID' => 'bd_behid',
							'BD.DOKID' => 'bd_dokid',
							'BD.NDOKTYPE' => 'bd_ndoktype',
							'BD.STATUS' => 'bd_status',
							'BD.JPID' => 'bd_jpid',
							'BD.DOKTYPE' => 'bd_doktype'
  							) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			