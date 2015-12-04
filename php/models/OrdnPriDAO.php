<?php

require_once 'models/OrdnPri.php';
require_once 'utility/Utility.php';
require_once 'utility/Constants.php';
require_once 'models/Noark4Base.php';

class OrdnPriDAO extends Noark4Base {
	
	public function __construct ($srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger) {
                parent::__construct (Constants::getXMLFilename('ORDNPRI'), $srcBase, $uttrekksBase, $SRC_TABLE_NAME, $logger);
		$this->selectQuery = "select OTYPE, OBJKTEXT, EVOK, EVAUTO, FRADATO, TILDATO, KLFLAGG, OVBESK, SEKFLAGG, LTEKST, OPTYPE, UNNTOFF from " . $SRC_TABLE_NAME . "";
	} 

	function processTable () {

		$ordnpri = new OrdnPri();
 
		$ordnpri->OP_ORDNPRI = 'EM';
		$ordnpri->OP_BETEGN = 'Elevmappe';
		$ordnpri->OP_LTEKST = 'Added at extraction';
		$ordnpri->OP_TYPE = 'UO';
		$ordnpri->OP_OVBESK = '0';
		$ordnpri->OP_KLFLAGG = '1';

		$ordnpri->OP_SIFLAGG = '1';
		$ordnpri->OP_EVOK = '1';
		$ordnpri->OP_EVAUTO = '0';
		$ordnpri->OP_SEKFLAGG = '1';

		
		$this->logger->log($this->XMLfilename, "Mising EM in ORDNPRO. Added here" , Constants::LOG_WARNING);
		$this->warningIssued = true;
	
		$this->writeToDestination($ordnpri);


		$this->srcBase->createAndExecuteQuery ($this->selectQuery);
		while (($result = $this->srcBase->getQueryResult ($this->selectQuery))) {
				$ordnpri = new OrdnPri();
				$ordnpri->OP_ORDNPRI = $result['OTYPE'];
				$ordnpri->OP_BETEGN = $result['OBJKTEXT'];
				$ordnpri->OP_LTEKST = $result['LTEKST'];
				$ordnpri->OP_TYPE = $result['OPTYPE'];
				$ordnpri->OP_OVBESK = $result['OVBESK'];
				$ordnpri->OP_KLFLAGG = $result['KLFLAGG'];
				// Note this is being set to 1 even though I am not sure it should be
				$ordnpri->OP_SIFLAGG = '1';
				$ordnpri->OP_EVOK = $result['EVOK'];
				$ordnpri->OP_EVAUTO = $result['EVAUTO'];
				$ordnpri->OP_SEKFLAGG = $result['SEKFLAGG'];
//				$ordnpri->OP_FRADATO = Utility::fixDateFormat($result['FRADATO']);
				$ordnpri->OP_TILDATO = Utility::fixDateFormat($result['TILDATO']);
				$ordnpri->OP_TGKODE = $result['UNNTOFF'];

				$this->logger->log($this->XMLfilename, "SI_FLAGG has no value for " . $ordnpri->OP_ORDNPRI . " setting it to '1'" , Constants::LOG_WARNING);	
				$this->warningIssued = true;

				$this->writeToDestination($ordnpri);
		}
		$this->srcBase->endQuery($this->selectQuery);
	}
	
	function writeToDestination($data) {
		
//		$sqlInsertStatement = "INSERT INTO ORDNPRI (OP_ORDNPRI, OP_BETEGN, OP_LTEKST, OP_TYPE, OP_OVBESK, OP_KLFLAGG, OP_SIFLAGG, OP_EVOK, OP_EVAUTO, OP_SEKFLAGG, OP_FRADATO, OP_TILDATO, OP_TGKODE) VALUES (";
		$sqlInsertStatement = "INSERT INTO ORDNPRI (OP_ORDNPRI, OP_BETEGN, OP_LTEKST, OP_TYPE, OP_OVBESK, OP_KLFLAGG, OP_SIFLAGG, OP_EVOK, OP_EVAUTO, OP_SEKFLAGG,  OP_TILDATO, OP_TGKODE) VALUES (";
	
		$sqlInsertStatement .= "'" . $data->OP_ORDNPRI . "', ";			
		$sqlInsertStatement .= "'" . $data->OP_BETEGN . "', ";			
		$sqlInsertStatement .= "'" . $data->OP_LTEKST . "', ";			
		$sqlInsertStatement .= "'" . $data->OP_TYPE . "', ";			
		$sqlInsertStatement .= "'" . $data->OP_OVBESK . "', ";			
		$sqlInsertStatement .= "'" . $data->OP_KLFLAGG . "', ";			
		$sqlInsertStatement .= "'" . $data->OP_SIFLAGG . "', ";			
		$sqlInsertStatement .= "'" . $data->OP_EVOK . "', ";
		$sqlInsertStatement .= "'" . $data->OP_EVAUTO . "', ";
		$sqlInsertStatement .= "'" . $data->OP_SEKFLAGG . "', ";
//		$sqlInsertStatement .= "'" . $data->OP_FRADATO . "', ";
		$sqlInsertStatement .= "'" . $data->OP_TILDATO . "', ";
		$sqlInsertStatement .= "'" . $data->OP_TGKODE . "'";
	
		$sqlInsertStatement.= ");";
		
		if ($this->uttrekksBase->executeStatement($sqlInsertStatement) == false) {
			// 1062 == duplicate key. Scary to hardcode, but can't find mysql constants somewhere
			if (mysql_errno() == Constants::MY_SQL_DUPLICATE) {
				// This table is know to contain duplicates. We just log and continue
				$this->logger->log($this->XMLfilename, "Known duplicate value detected. Value is " . $data->OP_ORDNPRI, Constants::LOG_WARNING);
			}
		}

    }
    
    
    
  function createXML($extractor) {    
    	$sqlQuery = "SELECT * FROM ORDNPRI";
    	$mapping = array ('idColumn' => 'op_ordnpri', 
  				'rootTag' => 'ORDNPRINS.TAB',	
			    		'rowTag' => 'ORDNPRINS',
  						'encoder' => 'utf8_decode',
							'elements' => array(
								'OP.ORDNPRI' => 'op_ordnpri',
								'OP.BETEGN' => 'op_betegn',
								'OP.LTEKST' => 'op_ltekst',
								'OP.TYPE' => 'op_type',
								'OP.OVBESK' => 'op_ovbesk',
								'OP.KLFLAGG' => 'op_klflagg',
								'OP.SIFLAGG' => 'op_siflagg',
								'OP.EVOK' => 'op_evok',
								'OP.EVAUTO' => 'op_evauto',
								'OP.SEKFLAGG' => 'op_sekflagg',
//								'OP.FRADATO' => 'op_fradato',
								'OP.TILDATO' => 'op_tildato',
								'OP.TGKODE' => 'op_tgkode'
								) 
						) ;
		
    	$extractor->extract($sqlQuery, $mapping, $this->XMLfilename, "file");
    	
    }    
 }
			