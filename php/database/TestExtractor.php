<?php

 require_once "MySQLDBParameters.php";
 require_once "Extractor.php";
 require_once "Constants.php";
 
 
 $dbParams = new MySQLDBParameters("localhost", 3306, "utmo_noark5", "root", "1234haha");
 $extractor = new Extractor('mysql', $dbParams);
 
  $sqlQuery = "SELECT * FROM mappe";
  
  $mapping = array ('idColumn' => 'tj_peid', 
									'rootTag' => 'ARKIV.TAB',      
                                        'rowTag' => 'ARKIV',
                                                'encoder' => 'utf8_decode',
                                                'elements' => array(
                                                        'SYSTEM' => 'systemId',
                                                        'TITTEL' => 'tittel',
                                                        'OFFTITT' => 'offentligTittel',
                                                        'MEDIUM' => 'dokumentmedium',
                                                        'OPPDAT' => 'opprettetDato',
                                                        'AVDATO' => 'avsluttetDato'
                                                        ) 
                                                ) ;
                
  $extractor->extract($sqlQuery, $mapping, "arkiv.xml", "file");




?>
