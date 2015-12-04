<?php



// This file does have a lot of hardcoded stuff, but the standard is from 1999 and
// will not change!!

require_once 'utility/Constants.php';

class SAKDOKCreator {
	
	var $outputDir = ".";	
	var $XMLfilename = 'SAKDOK.XML';	
	var $uttrekksBase = null;

	var $exportInfo = null;

 
	public function SAKDOKCreator  ($uttrekksBase, $outputDir, $exportInfo) {		
		$this->uttrekksBase = $uttrekksBase;	
		$this->outputDir = $outputDir;
		$this->exportInfo = $exportInfo;
	} 

	public function  generateJournal () {		

		$outputFileName = $this->outputDir . DIRECTORY_SEPARATOR . $this->XMLfilename;
		//echo "Opening file " . $outputFileName . " filename " . $this->XMLfilename . " dir(" . $this->outputDir . ")\n"; 
		if (($fh = fopen($outputFileName, "w")) != false) {
	
			fwrite($fh, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<!DOCTYPE SAKDOKOVERSIKT SYSTEM \"SAKDOK.DTD\">\n<SAKDOKOVERSIKT VERSJON=\"1.0\">\n");
	
			fwrite($fh, "\t<PRODINFO>\n");
			
			$arkskaper = "\t\t<PI.ARKSKAPER>" . $this->exportInfo->arkskaper .  "</PI.ARKSKAPER>\n";
			fwrite($fh, $arkskaper);
	
//			$systemName = "\t\t<EI.SYSTEMNAVN>" . $this->exportInfo->systemName . "</EI.SYSTEMNAVN>\n";
//			fwrite($fh, $systemName);
	
			$kommune = "\t\t<PI.KOMMUNE>" .  $this->exportInfo->kommune . "</PI.KOMMUNE>\n";
			fwrite($fh, $kommune);
	
			$fraDato = "\t\t<PI.FRADATO>" . $this->exportInfo->fraDato . "</PI.FRADATO>\n";
			fwrite($fh, $fraDato);
	
			$tilDato = "\t\t<PI.TILDATO>" .  $this->exportInfo->tilDato . "</PI.TILDATO>\n";
			fwrite($fh, $tilDato);
			
			$prodDato = "\t\t<PI.PRODDATO>" .  $this->exportInfo->prodDato . "</PI.PRODDATO>\n";
			fwrite($fh, $prodDato);

			$prodAntallFiler = "\t\t<PI.ANTFILER>1</PI.ANTFILER>\n";
			fwrite($fh, $prodAntallFiler);

			$prodFilnavn = "\t\t<FIL>\n\t\t\t<PI.FILNAVN>SAKDOK.XML</PI.FILNAVN>\n\t\t</FIL>\n";
			fwrite($fh, $prodFilnavn);

			fwrite($fh, "\t</PRODINFO>\n");
	
			fwrite($fh, "\t<RAPPORT>\n");

			$sakQuery = "select * from NOARKSAK";
			$sakerHandle =  $this->uttrekksBase->executeQueryFetchResultHandle ($sakQuery);
		
			while ($sak = mysql_fetch_assoc($sakerHandle)) {
				fwrite($fh, "\t<NOARKSAK.SD>\n");						
				$sakXML = $this->sakXML ($sak, '\t\t');
				fwrite($fh, $sakXML);

				$klassQuery = "select * from KLASS WHERE KL_SAID = '" . $sak['SA_ID'] . "'";
				$klassHandle =  $this->uttrekksBase->executeQueryFetchResultHandle ($klassQuery);

				while ($klass = mysql_fetch_assoc($klassHandle)) {
					fwrite($fh, "\t<KLASSERING.SD>\n");						
					$klassXML = $this->klassXML ($klass, '\t\t\t');
					fwrite($fh, $klassXML);
					fwrite($fh, "\t</KLASSERING.SD>\n");
				}
				mysql_free_result($klassHandle);

				$jpQuery = "select * from JOURNPST WHERE JP_SAID = '" . $sak['SA_ID'] . "'";
				$jpHandle =  $this->uttrekksBase->executeQueryFetchResultHandle ($jpQuery);

				while ($jp = mysql_fetch_assoc($jpHandle)) {
					fwrite($fh, "\t<JOURNPOST.SD>\n");						
					$jpXML = $this->jpXML ($jp, '\t\t\t');
					fwrite($fh, $jpXML);

					$avsmotQuery = "select * from AVSMOT WHERE AM_JPID = '" . $jp['JP_ID'] . "'";
					$avsmotHandle =  $this->uttrekksBase->executeQueryFetchResultHandle ($avsmotQuery);
	
					while ($avsmot = mysql_fetch_assoc($avsmotHandle)) {
						fwrite($fh, "\t<AVSMOT.SD>\n");						
						$avsmotXML = $this->avsmotXML ($avsmot, '\t\t\t\t');
						fwrite($fh, $avsmotXML);
						fwrite($fh, "\t</AVSMOT.SD>\n");
					}
					mysql_free_result($avsmotHandle);
					fwrite($fh, "\t</JOURNPOST.SD>\n");
				}
				mysql_free_result($jpHandle);
				fwrite($fh, "\t</NOARKSAK.SD>\n");
			}
				
			mysql_free_result($sakerHandle);

			fwrite($fh, "\t</RAPPORT>\n");
		}
		else
			echo "Error opening file " . $outputFileName . "\n";
	} // function



	function sakXML ($sak, $tabs) {
	
		$xmlString  = $tabs . "<SA.SAAR>" . $sak['SA_SAAR'] . "</SA.SAAR>\n";
		$xmlString .= $tabs . "<SA.SEKNR>" . $sak['SA_SEKNR'] . "</SA.SEKNR>\n";
		$xmlString .= $tabs . "<SA.PAPIR>". $sak['SA_PAPIR'] ."</SA.PAPIR>\n";
		$xmlString .= $tabs . "<SA.DATO>". $sak['SA_DATO'] ."</SA.DATO>\n";
		$xmlString .= $tabs . "<SA.TITTEL>". $sak['SA_TITTEL'] ."</SA.TITTEL>\n";
//		$xmlString .= $tabs . "<SA.OFFTITTEL>". $sak[''] ."</SA.OFFTITTEL>\n";
		$xmlString .= $tabs . "<SA.U1>". $sak['SA_U1'] ."</SA.U1>\n";
		$xmlString .= $tabs . "<SA.ARKDEL>". $sak['SA_ARKDEL'] ."</SA.ARKDEL>\n";
		$xmlString .= $tabs . "<SA.ASTATUS>". $sak['SA_STATUS'] ."</SA.ASTATUS>\n";
//		$xmlString .= $tabs . "<SA.ADMKORT>". $sak[''] ."</SA.ADMKORT>\n";
//		$xmlString .= $tabs . "<SA.ANSVINIT>". $sak[''] ."</SA.ANSVINIT>\n";
		$xmlString .= $tabs . "<SA.TGKODE>". $sak['SA_TGKODE'] ."</SA.TGKODE>\n";
		$xmlString .= $tabs . "<SA.UOFF>". $sak['SA_UOFF'] ."</SA.UOFF>\n";
	
		return $xmlString;
	}
	
	function klassXML ($jp, $tabs) {

		$xmlString  = $tabs . "<KL.ORDNVERDI>" . $jp['KL_ORDNVERDI'] . "</KL.ORDNVERDI>\n";
		$xmlString .= $tabs . "<KL.OVBESK>" . $jp['KL.OVBESK'] . "</KL.OVBESK>\n";
	
		return $xmlString;
	}

	function jpXML ($jp, $tabs) {
	
		$xmlString  = $tabs . "<JP.JAAR>" . $jp['JP_JAAR'] . "</JP.JAAR>\n";
		$xmlString .= $tabs . "<JP.SEKNR>" . $jp['JP_SEKNR'] . "</JP.SEKNR>\n";
		$xmlString .= $tabs . "<JP.POSTNR>". $jp['JP_JPOSTNR'] ."</JP.POSTNR>\n";
		$xmlString .= $tabs . "<JP.JDATO>". $jp['JP_JDATO'] ."</JP.JDATO>\n";
		$xmlString .= $tabs . "<JP.NDOKTYPE>". $jp['JP_NDOKTYPE'] ."</JP.NDOKTYPE>\n";
		$xmlString .= $tabs . "<JP.DOKDATO>". $jp['JP_DOKDATO'] ."</JP.DOKDATO>\n";
		$xmlString .= $tabs . "<JP.INNHOLD>". $jp['JP_INNHOLD'] ."</JP.INNHOLD>\n";
		$xmlString .= $tabs . "<JP.U1>". $jp['JP_U1'] ."</JP.U1>\n";
	
		return $xmlString;
	}
	
	function avsmotXML ($avsmot, $tabs) {
	
		$xmlString  = $tabs . "<AM.NAVN>" . $avsmot['AM_NAVN'] . "</AM.NAVN>\n";
		$xmlString .= $tabs . "<AM.IHTYPE>" . $avsmot['AM_IHTYPE'] . "</AM.IHTYPE>\n";
		$xmlString .= $tabs . "<AM.U1>". $avsmot['AM_U1'] ."</AM.U1>\n";
//		$xmlString .= $tabs . "<AM.ADMKORT>". $avsmot['AM_ADMKORT'] ."</AM.ADMKORT>\n";
//		$xmlString .= $tabs . "<AM.SBHINIT>". $avsmot['AM_SBHINIT'] ."</AM.SBHINIT>\n";
	
		return $xmlString;
	}
}
?>