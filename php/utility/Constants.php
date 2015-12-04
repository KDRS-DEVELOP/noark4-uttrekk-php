<?php

class Constants {

	

	public static $xmlFileNames = array (
		"ADMINDEL" => "ADMINDEL.XML",
		"ADRADMENH" => "ADRADMEN.XML",
		"ADRESSEK" => "ADRESSEK.XML",
		"ADRPERS" => "ADRPERS.XML",
		"ADRTYPE" => "ADRTYPE.XML",
		"ALIASADM" => "ALIASADM.XML",
		"ARKIV" => "ARKIV.XML",
		"ARKIVDEL" => "ARKIVDEL.XML",
		"ARKIVPER" => "ARKIVPER.XML",
		"ARSTATUS" => "ARSTATUS.XML",
		"AVGRKODE" => "AVGRKODE.XML",
		"AVSKRM" => "AVSKRM.XML",
		"AVSMOT" => "AVSMOT.XML",
		"BSKODE" => "BSKODE.XML",
		"DOKBESK" => "DOKBESK.XML",
		"DOKKAT" => "DOKKAT.XML",
		"DOKLINK" => "DOKLINK.XML",
		"DOKSTAT" => "DOKSTAT.XML",
		"DOKTILKN" => "DOKTILKN.XML",
		"DOKTYPE" => "DOKTYPE.XML",
		"DOKVERS" => "DOKVERS.XML",
		"EARKKODE" => "EARKKODE.XML",
		"ENHTYPE" => "ENHTYPE.XML",
		"EMNEORD" => "EMNEORD.XML",
		"FORSMATE" => "FORSMATE.XML",
		"FSTATUS" => "FSTATUS.XML",
		"INFOTYPE" => "INFOTYPE.XML",
		"JENARKD" => "JENARKD.XML",
		"JOURNENH" => "JOURNENH.XML",
		"JOURNPST" => "JOURNPST.XML",
		"JOURNSTA" => "JOURNSTA.XML",
		"KASSKODE" => "KASSKODE.XML",
		"KLASS" => "KLASS.XML",
		"LAGRENH" => "LAGRENH.XML",
		"LAGRFORM" => "LAGRFORM.XML",
		"MEDADRGR" => "MEDADRGR.XML",
		"MERKNAD" => "MERKNAD.XML",
		"NOARKSAK" => "NOARKSAK.XML",
		"NUMSERIE" => "NUMSERIE.XML",
		"OPRITYP" => "OPRITYP.XML",
		"ORDNPRI" => "ORDNPRI.XML",
		"ORDNVERD" => "ORDNVERD.XML",
		"PERKLAR" => "PERKLAR.XML",
		"PERNAVN" => "PERNAVN.XML",
		"PERROLLE" => "PERROLLE.XML",
		"PERSON" => "PERSON.XML",
		"POLSAKG" => "POLSAKG.XML",
		"POSTNR" => "POSTNR.XML",
		"SAKPART" => "SAKPART.XML",
		"SAKSTAT" => "SAKSTAT.XML",
		"SAKTYPE" => "SAKTYPE.XML",
		"STATMDOK" => "STATMDOK.XML",
		"TGGRP" => "TGGRP.XML",
		"TGHJEM" => "TGHJEM.XML",
		"TGINFO" => "TGINFO.XML",
		"TGKODE" => "TGKODE.XML",
		"TGMEDLEM" => "TGMEDLEM.XML",
		"TILLEGG" => "TILLEGG.XML",
		"TLKODE" => "TLKODE.XML",
		"UTDOKTYP" => "UTDOKTYP.XML",
		"UTVALG" => "UTVALG.XML",
		"UTVBEH" => "UTVBEH.XML",
		"UTVBEHDO" => "UTVBEHDO.XML",
		"UTVBEHSTAT" => "UTVBEHST.XML",
		"UTVMEDL" => "UTVMEDL.XML",
		"UTVMEDLF" => "UTVMEDLF.XML",
		"UTVMOTE" => "UTVMOTE.XML",
		"UTVMOTEDOK" => "UTVMDOK.XML",
		"UTVSAK" => "UTVSAK.XML",
		"UTVSAKTY" => "UTVSAKTY.XML",
		"VARFORM" => "VARFORM.XML"

	);

	public static function getXMLFilename($table) {
		return self::$xmlFileNames[$table];
	}


	public static function convertUtvDokType($input) {
		
		return str_replace(
				array(
					"PROTOKOLL",
					"FREMLEGG",
					"RAPPORT",
					"VEDTAK",
					"BREV",
					"DOKUMENT",
					"NOTAT",
					"I"
			),
				array(
					"SP",
					"SF",
					"RP",
					"VE",
					"BR",
					"DO",
					"NT",
					"I"
			),
			$input
		);
	}


	public static function mapTablenamesForNOARKIH($input) {

	// Yes this wil lcost perfromance wise, but I need to finish!!!

	$mapNames = array("UTVBEHDO" => "UTVBEHDOK",
				"UTVMEDL" => "UTVMEDLEM",
				"UTVMEDLF" => "UTVMEDLFUNK",
				"UTVSAK" => "UTVALGSAK", 
				"UTVSAKTY" => "UTVSAKTYP",
				"VARFORM" => "VARIANTFORMAT",
				"ARKIVPER" => "ARKIVPERIODE",
				"AVGRKODE" => "AVGRADKODE",
				"AVSKRM" => "AVSKRMAATE",
				"DOKBESK" => "DOKBESKRIV",
				"DOKKAT" => "DOKKATEGORI",
				"DOKSTAT" => "DOKSTATUS",
				"DOKTYPE" => "NOARKDOKTYPE",
				"DOKVERS" => "DOKVERSJON",
				"ENHTYPE" => "ENHETSTYPE",
				"FORSMATE" => "FORSMAATE",
				"ADRESSEK" => "ADRESSEKP",
				"JENARKD" => "JENARKDEL",
				"JOURNENH" => "JOURNENHET",
				"JOURNPST" => "JOURNPOST",
				"JOURNSTA" => "JOURNSTATUS",
				"KLASS" => "KLASSERING",
				"LAGRENH" => "LAGRENHET",
				"LAGRFORM" => "LAGRFORMAT",
				"MEDADRGR" => "MEDLADRGR",
				"ADRPERS" => "ADRPERSON",
				"OPRITYP" => "ORDNPRINSTYPE",
				"ORDNPRI" => "ORDNPRINS",
				"ORDNVERD" => "ORDNVERDI",
				"PERKLAR" => "PERKLARER",
				"POLSAKG" => "POLSAKSGANG",
				"SAKPART" => "SAKSPART",
				"SAKSTAT" => "SAKSTATUS",
				"SAKTYPE" => "SAKSTYPE",
				"STATMDOK" => "STATUSMDOK",
				"TGGRP" => "TGGRUPPE",
				"TGHJEM" => "TGHJEMMEL",
				"ALIASADM" => "ALIASADMENH",
				"TILLEGG" => "TILLEGGSINFO",
				"UTVBEH" => "UTVBEHANDLING",
				"UTVBEHDO" => "UTVBEHDOK",
				"EMNEORD" => "EMNEORD",
				"JENARKDEL" => "JENARKDEL"
			);

		
		if (isset($mapNames[$input]) == true) {
			return $mapNames[$input];
		}
		else 
			return $input;
	
	}


	const JOURNENHET_MISSING = 'ING';
	const ADMININDEL_TOPNIVA = '0';
	const NEWLINE = "\n";
	const XML_ENCODING = "UTF-8";
	const REFNR_JP_STRING_LENGTH = 6; //6
	const REFNR_SAK_STRING_LENGTH = 5; //5
	const JP_STRING_LENGTH = 8; //8
	const SAK_STRING_LENGTH = 8; 
	const INGENBRUKER_ID = '0';
	const UTTREKSBRUKER_ID = '1';
	const MY_SQL_DUPLICATE = 1062;
	const MY_SQL_MISSING_FK_VALUE = 1452;
	const LAGRENHET = 'ENHET1';
	const DOT_MARKER_COUNT = 1000;
	const LOG_INFO = "INFO";
	const LOG_TODO = "TODO";
	const LOG_PROCESSINGINFO = "PROCINFO"; 
	const LOG_WARNING = "WARNING";
	const LOG_ERROR = "ERROR";
	const UNKNOWN_DATE = "1000-01-01"; 
	const DATE_AUTO_END = "12-JAN-13"; // Things that aren't finished need a date that it's finished. Use this value to set a date 
	const DATE_AUTO_START = "01-DEC-98";
	const DOKTYPE_IKKE_ANNGITT = "Q";
 

}
?>
