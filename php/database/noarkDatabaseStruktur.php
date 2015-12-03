<?php

// these will create the statements you need to create the database structure for MYSQL
// You probably don't need to use INDEX like I have done. You only need INDEX if your foreign key is pointing to something that's not a primary key
 
class Noark4DatabaseStruktur  { 
	
	function Noark4DatabaseStruktur() {
		
	}
	
	function deleteDatabaseStatement($dbName) {
		return "DROP DATABASE IF EXISTS " . $dbName;	
	}
	
	function createDatabaseStatement($dbName) {
		return "CREATE DATABASE " . $dbName;
	}

	// POSTNUMMER TABELL som skal brukes i ADRESSEK???

	function createADMINDEL () {
		$createStatement = " CREATE TABLE ADMINDEL (";
		
		$createStatement .= " AI_ID CHAR(10) PRIMARY KEY,";
		$createStatement .= " AI_IDFAR CHAR(10),";
		$createStatement .= " AI_FORKDN CHAR(10),";
		$createStatement .= " AI_ADMKORT CHAR(30),";
		$createStatement .= " AI_ADMBET CHAR(70),";
		$createStatement .= " AI_TYPE CHAR(10),";		
		$createStatement .= " AI_FRADATO DATE,";
		$createStatement .= " AI_TILDATO DATE";
		
		$createStatement .= ") engine = innodb; ";
		
		return $createStatement;
	}

	// adressen til administrativ enheter
	
	function createADRADMENH() {
		$createStatement = " CREATE TABLE ADRADMENH ( ";

		$createStatement .= " AA_ADMID CHAR(10),";
		$createStatement .= " AA_ADRID CHAR(10),";
		$createStatement .= " PRIMARY KEY (AA_ADMID, AA_ADRID),";
		$createStatement .= " FOREIGN KEY (AA_ADMID) REFERENCES ADMINDEL (AI_ID),";
		$createStatement .= " FOREIGN KEY (AA_ADRID) REFERENCES ADRESSEK (AK_ADRID)";
				
		$createStatement .= ") engine = innodb; ";
		
		return $createStatement;
	}


	function createADRESSEK() {
	// *Dette er er tabellen som inneholder adresser. 
	// $createStatement .= " FOREIGN KEY (AM_KORTNAVN) REFERENCES ADRESSEK (AK_KORTNAVN)";			
		$createStatement = " CREATE TABLE ADRESSEK ( ";
		
		$createStatement .= " AK_ADRID CHAR(10) PRIMARY KEY,";
		$createStatement .= " AK_TYPE CHAR(2),";
		$createStatement .= " AK_ADRGRUPPE CHAR(1),";
		$createStatement .= " AK_KORTNAVN CHAR(10),";
		$createStatement .= " AK_NAVN CHAR(70),";
		$createStatement .= " AK_POSTADR CHAR(120),";
		$createStatement .= " AK_POSTNR CHAR(5),";
		$createStatement .= " AK_POSTSTED CHAR(60),";
		$createStatement .= " AK_EPOST CHAR(120),";
		$createStatement .= " AK_FAKS CHAR(20),";
		$createStatement .= " AK_TLF CHAR(20),";
		$createStatement .= " AK_ORGNR CHAR(20),";
		$createStatement .= " INDEX (AK_KORTNAVN)";
//		$createStatement .= " FOREIGN KEY (AK_TYPE) REFERENCES ADRTYPE (AT_KODE)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}


	function createADRPERS() {
	// *Dette kobler en adresse ID til en Person ID
	
		$createStatement = " CREATE TABLE ADRPERS ( ";
		
		$createStatement .= " PA_ADRID CHAR(10),";
		$createStatement .= " PA_PEID CHAR(10),";
		$createStatement .= " PRIMARY KEY (PA_ADRID, PA_PEID),";
		$createStatement .= " FOREIGN KEY (PA_PEID) REFERENCES PERSON (PE_ID),";
		$createStatement .= " FOREIGN KEY (PA_ADRID) REFERENCES ADRESSEK (AK_ADRID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createADRTYPE() {
		$createStatement = " CREATE TABLE ADRTYPE ( ";
		
		$createStatement .= " AT_KODE CHAR (2) PRIMARY KEY,";
		$createStatement .= " AT_BETEGN CHAR (70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createALIASADM() {

		$createStatement = " CREATE TABLE ALIASADM ( ";
		
		$createStatement .= " AL_ADMIDFRA CHAR (10),";
		$createStatement .= " AL_ADMIDTIL CHAR (10),";
		$createStatement .= " PRIMARY KEY (AL_ADMIDFRA, AL_ADMIDTIL),";		
		$createStatement .= " AL_MERKNAD CHAR (255)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}


	// $createStatement .= " PRIMARY KEY (,),";

	function createARKIV () {
		$createStatement = " CREATE TABLE ARKIV ( ";
		
		$createStatement .= " AR_ARKIV CHAR (10) PRIMARY KEY,";
		$createStatement .= " AR_BETEGN CHAR (70),";
		$createStatement .= " AR_NUMSER CHAR (10),";
		$createStatement .= " AR_FRADATO DATE,";
		$createStatement .= " AR_TILDATO DATE,";
		$createStatement .= " AR_MERKNAD TEXT,";
		
		$createStatement .= " FOREIGN KEY (AR_NUMSER) REFERENCES NUMSERIE (NU_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createARKIVDEL() {
		$createStatement = " CREATE TABLE ARKIVDEL ( ";
		
		$createStatement .= " AD_ARKDEL CHAR (15) PRIMARY KEY,";
		$createStatement .= " AD_BETEGN CHAR (70),";
		$createStatement .= " AD_ARKIV CHAR (10),";
		$createStatement .= " AD_ASTATUS CHAR (2),";
		$createStatement .= " AD_PERIODE CHAR (2),";
		$createStatement .= " AD_PRIMNOK CHAR (10),";
		$createStatement .= " AD_BSKODE CHAR (2),";
		$createStatement .= " AD_FORTS CHAR (10),";
		$createStatement .= " AD_PAPIR CHAR (1),";
		$createStatement .= " AD_ELDOK CHAR (1),";
		$createStatement .= " AD_NUMSER CHAR (10),";
		$createStatement .= " AD_FRADATO DATE,";
		$createStatement .= " AD_TILDATO DATE,";
		$createStatement .= " AD_MERKNAD TEXT,";
		$createStatement .= " AD_KONTRAV CHAR (10)";
/*		$createStatement .= " FOREIGN KEY (AD_ASTATUS) REFERENCES ARSTATUS (AS_STATUS),";
		$createStatement .= " FOREIGN KEY (AD_PRIMNOK) REFERENCES ORDNPRI (OP_ORDNPRI),";
		$createStatement .= " FOREIGN KEY (AD_BSKODE) REFERENCES BSKODE (BK_KODE),";
		$createStatement .= " FOREIGN KEY (AD_ARKIV) REFERENCES ARKIV (AR_ARKIV),";
		$createStatement .= " FOREIGN KEY (AD_NUMSER) REFERENCES NUMSERIE (NU_ID)";
*/		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
	
	function createARKIVPER() {
		$createStatement = " CREATE TABLE ARKIVPER ( ";

		$createStatement .= " AP_ARKIV CHAR (10),";
		$createStatement .= " AP_PERIODE CHAR (5) PRIMARY KEY,";
		$createStatement .= " AP_STATUS CHAR (2),";
		$createStatement .= " AP_FRADATO DATE,";
		$createStatement .= " AP_TILDATO DATE,";
		$createStatement .= " AP_MERKNAD TEXT,";
		
		$createStatement .= " FOREIGN KEY (AP_STATUS) REFERENCES ARSTATUS (AS_STATUS)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
	

	function createARSTATUS() {
		$createStatement = " CREATE TABLE ARSTATUS ( ";
		
		$createStatement .= " AS_STATUS CHAR (2) PRIMARY KEY,";
		$createStatement .= " AS_BETEGN CHAR (70),";
		$createStatement .= " AS_SPEFSAK CHAR (1),";
		$createStatement .= " AS_SPEFDOK CHAR (1),";
		$createStatement .= " AS_LUKKET CHAR (1)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createAVGRKODE() {
		$createStatement = " CREATE TABLE AVGRKODE ( ";
		
		$createStatement .= " AG_KODE CHAR(2) PRIMARY KEY,";
		$createStatement .= " AG_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createAVSKRM() {
		$createStatement = " CREATE TABLE AVSKRM ( ";
		
		$createStatement .= " AV_KODE CHAR(10) PRIMARY KEY,";
		$createStatement .= " AV_BETEGN CHAR(70),";
		$createStatement .= " AV_MIDLERTID CHAR(1),";
		$createStatement .= " AV_BESVART CHAR(1)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createAVSMOT() {
		$createStatement = " CREATE TABLE AVSMOT ( ";
		
		$createStatement .= " AM_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " AM_JPID CHAR (10),";
		$createStatement .= " AM_IHTYPE CHAR (1),"; // 0 hvis avsender, 1 hvis motaker
		$createStatement .= " AM_KOPIMOT CHAR (1),";
		$createStatement .= " AM_BEHANSV CHAR (1),";
		$createStatement .= " AM_NAVN CHAR (70),";		
		$createStatement .= " AM_GRUPPEMOT CHAR (1),";
		$createStatement .= " AM_U1 CHAR (1),";
		$createStatement .= " AM_KORTNAVN CHAR (10),";
		$createStatement .= " AM_ADRESSE CHAR (120),";
		$createStatement .= " AM_POSTNR CHAR (5),";
		$createStatement .= " AM_POSTSTED CHAR (60),";
		$createStatement .= " AM_UTLAND CHAR (120),";
		$createStatement .= " AM_EPOSTADR CHAR (120),";		
		$createStatement .= " AM_REF CHAR (70),";
		$createStatement .= " AM_ADMID CHAR (10),";				
		$createStatement .= " AM_SBHID CHAR (10),";
		$createStatement .= " AM_AVSKM CHAR (10),";
		$createStatement .= " AM_AVSKAV CHAR (10),";		
		$createStatement .= " AM_AVSKDATO DATE,";
		$createStatement .= " AM_BESVAR CHAR (10),";
		$createStatement .= " AM_FRIST DATE,";
		$createStatement .= " AM_FORSEND CHAR (10),";
		// $createStatement .= " AM_AVSKMET CHAR(),"; USIKKER OM DENNE SKAL VÆRE MED, IKKE i DTD eller Noark 4 - Del 2
		$createStatement .= " AM_FSSTATUS CHAR(2)";
/*
		$createStatement .= " FOREIGN KEY (AM_JPID) REFERENCES JOURNPST (JP_ID),";
		$createStatement .= " FOREIGN KEY (AM_KORTNAVN) REFERENCES ADRESSEK (AK_KORTNAVN),";			
		$createStatement .= " FOREIGN KEY (AM_ADMID) REFERENCES ADMINDEL (AI_ID),";		
		$createStatement .= " FOREIGN KEY (AM_SBHID) REFERENCES PERSON (PE_ID),";		
		$createStatement .= " FOREIGN KEY (AM_AVSKM) REFERENCES AVSKRM (AV_KODE),";
		$createStatement .= " FOREIGN KEY (AM_AVSKAV) REFERENCES JOURNPST (JP_ID),";
		$createStatement .= " FOREIGN KEY (AM_BESVAR) REFERENCES JOURNPST (JP_ID),"; // TODO -- check!!! JOURNPOST table!!
		$createStatement .= " FOREIGN KEY (AM_FORSEND) REFERENCES FORSMATE (FM_KODE),";
		$createStatement .= " FOREIGN KEY (AM_FSSTATUS) REFERENCES FSTATUS (FS_STATUS)";
*/ 
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	
	// Bortsettingskode
	function createBSKODE () {
		$createStatement = " CREATE TABLE BSKODE ( ";
		
		$createStatement .= " BK_KODE CHAR (2) PRIMARY KEY,";
		$createStatement .= " BK_BETEGN CHAR (70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createDOKBESK() {
		$createStatement = " CREATE TABLE DOKBESK ( ";
		
		$createStatement .= " DB_DOKID CHAR (10),";
		$createStatement .= " DB_KATEGORI CHAR (10),";
		$createStatement .= " DB_TITTEL CHAR (255),";
		$createStatement .= " DB_PAPIR CHAR (1),";
		$createStatement .= " DB_LOKALISERING CHAR (255),";
		$createStatement .= " DB_STATUS CHAR (2),";
		$createStatement .= " DB_UTARBAV CHAR (10),";
		$createStatement .= " DB_TGKODE CHAR (2),";		
		$createStatement .= " DB_AGDATO DATE,";
		$createStatement .= " DB_UOFF CHAR (16),";
		$createStatement .= " DB_TGGRUPPE CHAR (10),";
		$createStatement .= " DB_AGKODE CHAR (2),";
		$createStatement .= " PRIMARY KEY (DB_DOKID),";
		$createStatement .= " FOREIGN KEY (DB_KATEGORI) REFERENCES DOKKAT (DK_KODE),";
		$createStatement .= " FOREIGN KEY (DB_STATUS) REFERENCES DOKSTAT (DS_STATUS),";
		$createStatement .= " FOREIGN KEY (DB_UTARBAV) REFERENCES PERSON (PE_ID)";
		//$createStatement .= " FOREIGN KEY (DB_TGKODE) REFERENCES TGHJEM (TH_TGKODE)";

		// Skal dette være med????? Uklart i standarden
		//		$createStatement .= " FOREIGN KEY (DB_TGGRUPPE) REFERENCES TGGRP (TG_GRUPPEID)";
		
//		$createStatement .= " FOREIGN KEY (DB_AGKODE) REFERENCES AVGRKODE (AG_KODE)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createDOKKAT() {
		$createStatement = " CREATE TABLE DOKKAT ( ";
		
		$createStatement .= " DK_KODE CHAR (10) PRIMARY KEY,";
		$createStatement .= " DK_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createDOKLINK() {
		$createStatement = " CREATE TABLE DOKLINK ( ";
		
		$createStatement .= " DL_JPID CHAR(10),";
		$createStatement .= " DL_RNR CHAR(4),";
		$createStatement .= " DL_DOKID CHAR(10),";
		$createStatement .= " DL_TYPE CHAR(2),";
		$createStatement .= " DL_TKDATO DATE,";		
		$createStatement .= " DL_TKAV CHAR(10),";
		$createStatement .= " PRIMARY KEY (DL_JPID, DL_RNR),";		
		$createStatement .= " FOREIGN KEY (DL_JPID) REFERENCES JOURNPST (JP_ID),";
		$createStatement .= " FOREIGN KEY (DL_TKAV) REFERENCES PERSON (PE_ID)";
				
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createDOKSTAT() {
		$createStatement = " CREATE TABLE DOKSTAT ( ";
		
		$createStatement .= " DS_STATUS CHAR(2) PRIMARY KEY,";
		$createStatement .= " DS_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createDOKTILKN() {
		$createStatement = " CREATE TABLE DOKTILKN ( ";
		
		$createStatement .= " DT_KODE CHAR(2) PRIMARY KEY,";
		$createStatement .= " DT_BETEGN CHAR(70),";
		$createStatement .= " DT_JOURNAL CHAR(1),";
		$createStatement .= " DT_MOTEDOK CHAR(1)";
        
		$createStatement .= ") engine = innodb; ";
		
		return $createStatement;
	}

	function createDOKTYPE() {
		$createStatement = " CREATE TABLE DOKTYPE ( ";
		
		$createStatement .= " ND_DOKTYPE CHAR(3) PRIMARY KEY,";
		$createStatement .= " ND_BETEGN CHAR(70),";
		$createStatement .= " ND_EKSTPROD CHAR(1),";
		$createStatement .= " ND_INTMOT CHAR(1),";
		$createStatement .= " ND_EKSTMOT CHAR(1),";
		$createStatement .= " ND_OPPF CHAR(1)";
		
		$createStatement .= ") engine = innodb; ";
		
		return $createStatement;
	}


	function createDOKVERS() {
		$createStatement = " CREATE TABLE DOKVERS ( ";
		
		$createStatement .= " VE_DOKID CHAR(10),";
		$createStatement .= " VE_VERSJON CHAR(5),";
		$createStatement .= " VE_VARIANT CHAR(2),";
		$createStatement .= " VE_AKTIV CHAR(1),";
		$createStatement .= " VE_DOKFORMAT CHAR(10),";
		$createStatement .= " VE_REGAV CHAR(19),";
		$createStatement .= " VE_TGKODE CHAR(2),";
		$createStatement .= " VE_LAGRENH CHAR(10),";
		$createStatement .= " VE_FILREF CHAR(255),";
		$createStatement .= " PRIMARY KEY (VE_DOKID, VE_VERSJON, VE_VARIANT),";
		$createStatement .= " FOREIGN KEY (VE_VARIANT) REFERENCES VARFORM  (VF_KODE),";
		$createStatement .= " FOREIGN KEY (VE_REGAV) REFERENCES PERSON (PE_ID)";
				
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createEARKKODE() {
		$createStatement = " CREATE TABLE EARKKODE ( ";
		
		$createStatement .= " EA_ORDNPRI CHAR(10),";
		$createStatement .= " EA_ORDNVER CHAR(70),";
		$createStatement .= " EA_SORDFLAGG CHAR(1),";
		$createStatement .= " EA_ORD CHAR(70),";
		$createStatement .= " PRIMARY KEY (EA_ORDNPRI, EA_ORDNVER, EA_ORD)";
		$createStatement .= ") engine = innodb; ";
		
		return $createStatement;
	}

	function createENHTYPE() {
		$createStatement = " CREATE TABLE ENHTYPE ( ";
		
		$createStatement .= " ET_KODE CHAR(10) PRIMARY KEY,";
		$createStatement .= " ET_BETEGN CHAR(70),";
		$createStatement .= " ET_UNDEREN CHAR(10)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	// Forsendelsesstatus	
	function createFORSMATE() {
		$createStatement = " CREATE TABLE FORSMATE ( ";
		
		$createStatement .= " FM_KODE CHAR(10) PRIMARY KEY,";
		$createStatement .= " FM_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createFSTATUS() {
		$createStatement = " CREATE TABLE FSTATUS ( ";
		
		$createStatement .= " FS_STATUS CHAR(2) PRIMARY KEY,";
		$createStatement .= " FS_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;

	}

	function createINFOTYPE() {
		$createStatement = " CREATE TABLE INFOTYPE ( ";
		
		$createStatement .= " IT_KODE CHAR(10) PRIMARY KEY,";
		$createStatement .= " IT_BETEGN CHAR(70),";
		$createStatement .= " IT_LTEKST1 CHAR(30),";
		$createStatement .= " IT_MERKNAD CHAR(1),";
		$createStatement .= " IT_AUTOLOG CHAR(1),";
		$createStatement .= " IT_OPPBETID CHAR(3)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createJENARKDEL() {
		$createStatement = " CREATE TABLE JENARKDEL ( ";
		
		$createStatement .= " JA_JENHET CHAR(10),";
		$createStatement .= " JA_ARKDEL CHAR(70),";
		$createStatement .= " PRIMARY KEY(JA_JENHET, JA_ARKDEL),";
		$createStatement .= " FOREIGN KEY (JA_JENHET) REFERENCES JOURNENH (JE_JENHET),";
		$createStatement .= " FOREIGN KEY (JA_ARKDEL) REFERENCES ARKIVDEL (AD_ARKDEL)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
	
	
	function createJOURNENH() {
		$createStatement = " CREATE TABLE JOURNENH ( ";
		
		$createStatement .= " JE_JENHET CHAR(10) PRIMARY KEY,";
		$createStatement .= " JE_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}


	
	
	
	function createJOURNPST() {
		$createStatement = "CREATE TABLE JOURNPST ("; 
		
		$createStatement .= " JP_ID CHAR(10) NOT NULL,";
		$createStatement .= " JP_JAAR CHAR(4) NOT NULL,";
		$createStatement .= " JP_SEKNR CHAR(7) NOT NULL,";
		$createStatement .= " JP_SAID CHAR(10) NOT NULL,";
		$createStatement .= " JP_JPOSTNR CHAR(4) NOT NULL,"; 
		$createStatement .= " JP_JDATO DATE NOT NULL,";
		$createStatement .= " JP_NDOKTYPE CHAR(1) NOT NULL,";
		$createStatement .= " JP_DOKDATO DATE,";
		$createStatement .= " JP_UDATERT CHAR(1) NOT NULL,"; 
		$createStatement .= " JP_STATUS CHAR(2) NOT NULL,";
		$createStatement .= " JP_INNHOLD VARCHAR(255) NOT NULL,"; 
		$createStatement .= " JP_U1 CHAR(1) NOT NULL,";
		$createStatement .= " JP_AVSKDATO DATE,";
		$createStatement .= " JP_EKSPDATO DATE,";
		$createStatement .= " JP_FORFDATO DATE,";
		$createStatement .= " JP_TGKODE CHAR(2),";
		$createStatement .= " JP_UOFF CHAR(70),";
		$createStatement .= " JP_OVDATO DATE,";
		$createStatement .= " JP_AGDATO DATE,";
		$createStatement .= " JP_AGKODE CHAR(2),"; 
		$createStatement .= " JP_TGGRUPPE CHAR(10),";
		$createStatement .= " JP_SAKSDEL CHAR(70),";
		$createStatement .= " JP_U2 CHAR(1),";
		$createStatement .= " JP_ARKDEL CHAR(10),"; 
		$createStatement .= " JP_PAPIR CHAR(1),";
		$createStatement .= " JP_TLKODE CHAR(10),";
		$createStatement .= " JP_ANTVED CHAR(2),"; 
		$createStatement .= " PRIMARY KEY(JP_ID),";
		$createStatement .= " FOREIGN KEY (JP_SAID) REFERENCES NOARKSAK (SA_ID),";
//		$createStatement .= " FOREIGN KEY (JP_NDOKTYPE) REFERENCES DOKTYPE (ND_DOKTYPE),";
		$createStatement .= " FOREIGN KEY (JP_STATUS) REFERENCES JOURNSTA (JS_STATUS),";
		$createStatement .= " FOREIGN KEY (JP_TGKODE) REFERENCES TGKODE (TK_TGKODE),";
		$createStatement .= " FOREIGN KEY (JP_AGKODE) REFERENCES AVGRKODE (AG_KODE),";
		// TODO: Check this!!! Should it be to TGGRP.TG_GRUPPEID
		$createStatement .= " FOREIGN KEY (JP_TGGRUPPE) REFERENCES TGGRP (TG_GRUPPEID),";
		$createStatement .= " FOREIGN KEY (JP_SAKSDEL) REFERENCES ORDNVERD (OV_ORDNVER),";		
		$createStatement .= " FOREIGN KEY (JP_TLKODE) REFERENCES TLKODE (TL_KODE)";
		//$createStatement .= "FOREIGN KEY () REFERENCES ()";
		
		$createStatement .= " ) engine = innodb;";
		
		return $createStatement;
	}	
	

	function createJOURNSTA() {
		$createStatement = " CREATE TABLE JOURNSTA ( ";
		
		$createStatement .= " JS_STATUS CHAR (2) PRIMARY KEY,";
		$createStatement .= " JS_BETEGN CHAR (70),";
		$createStatement .= " JS_ANSVAR CHAR (1),";
		$createStatement .= " JS_DOKKONTR CHAR (1),";
		$createStatement .= " JS_EKSPEDERT CHAR (2),";
		$createStatement .= " JS_FORARKIV CHAR (1),";
		$createStatement .= " JS_FORLEDER CHAR (1),";
		$createStatement .= " JS_FORSAKSBEH CHAR (1),";
		$createStatement .= " JS_FOREKST CHAR (1),";
		$createStatement .= " JS_FORINT CHAR (1)";
		
		$createStatement .= " ) engine = innodb; ";
		return $createStatement;
	}

	function createKASSKODE() {
		$createStatement = " CREATE TABLE KASSKODE ( ";
		
		$createStatement .= " KK_KODE CHAR(2) PRIMARY KEY,";
		$createStatement .= " KK_BETEGN CHAR(70)";
		
		$createStatement .= " ) engine = innodb; ";
		
		return $createStatement;
	}

	function createKLASS() {

		$createStatement = " CREATE TABLE KLASS ( ";
		
		$createStatement .= " KL_SAID CHAR (10),";
		$createStatement .= " KL_SORT CHAR (1),";
		$createStatement .= " KL_ORDNPRI CHAR (10),";
		$createStatement .= " KL_ORDNVER CHAR (70),";
		$createStatement .= " KL_U1 CHAR (1),";
		$createStatement .= " PRIMARY KEY (KL_SAID, KL_ORDNPRI, KL_ORDNVER),";
		$createStatement .= " FOREIGN KEY (KL_SAID) REFERENCES NOARKSAK (SA_ID),";
		$createStatement .= " FOREIGN KEY (KL_ORDNPRI) REFERENCES ORDNPRI(OP_ORDNPRI),";
		$createStatement .= " FOREIGN KEY (KL_ORDNVER) REFERENCES ORDNVERD(OV_ORDNVER) ";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createLAGRENH() {
		$createStatement = " CREATE TABLE LAGRENH ( ";
		
		$createStatement .= " LA_KODE CHAR (10) PRIMARY KEY,";
		$createStatement .= " LA_BESKRIV CHAR(70) ";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createLAGRFORM() {
		$createStatement = " CREATE TABLE LAGRFORM ( ";
		
		$createStatement .= " LF_KODE CHAR (10) PRIMARY KEY, ";
		$createStatement .= " LF_BESKRIV CHAR (70), ";
		$createStatement .= " LF_ARKIV CHAR (1), ";
		$createStatement .= " LF_FILTYPE CHAR (10)";
				
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createMEDADRGR() {
		$createStatement = " CREATE TABLE MEDADRGR ( ";
		
		$createStatement .= " MG_GRID CHAR(10),";
		$createStatement .= " MG_MEDLID CHAR(10),";
		$createStatement .= " PRIMARY KEY (MG_GRID, MG_MEDLID)";
// Foreign key 	(MG_GRID ) ADRESSEKP.AK_ADRGRUPPE
// Foreign key 	(MG_MEDLID) ADRESSEKP.AK_ADRGRUPPE	
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createMERKNAD() {
		$createStatement = " CREATE TABLE MERKNAD ( ";
		
		$createStatement .= " ME_ID CHAR (10) PRIMARY KEY,";	
		$createStatement .= " ME_SAID CHAR (10),";
		$createStatement .= " ME_JPID CHAR (10),";
		$createStatement .= " ME_RNR CHAR (6),";
		$createStatement .= " ME_ITYPE CHAR (10),";
		$createStatement .= " ME_TGKODE CHAR (2),";
		$createStatement .= " ME_TGGRUPPE CHAR (10),";
		$createStatement .= " ME_REGKL DATETIME,";
		$createStatement .= " ME_REGAV CHAR (10),";
		$createStatement .= " ME_PVGAV CHAR (10),";
		$createStatement .= " ME_REGDATO DATE,";
		$createStatement .= " ME_TEKST LONGTEXT";		
/*		$createStatement .= " FOREIGN KEY (ME_SAID) REFERENCES NOARKSAK (SA_ID),";
		$createStatement .= " FOREIGN KEY (ME_JPID) REFERENCES JOURNPST (JP_ID),";
		$createStatement .= " FOREIGN KEY (ME_TGKODE) REFERENCES TGKODE (TK_TGKODE),";
		// TODO: Check this!!! Should it be to TGGRP.TG_GRUPPEID
		$createStatement .= " FOREIGN KEY (ME_TGGRUPPE) REFERENCES TGGRP (TG_GRUPPEID),";
		$createStatement .= " FOREIGN KEY (ME_REGAV) REFERENCES PERSON (PE_ID),";
		$createStatement .= " FOREIGN KEY (ME_PVGAV) REFERENCES PERSON (PE_ID) ";
*/				
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createNOARKSAK() {		
		$createStatement = " CREATE TABLE NOARKSAK ( ";
		
		$createStatement .= " SA_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " SA_SAAR CHAR (4),";
		$createStatement .= " SA_PAPIR CHAR (1),";
		$createStatement .= " SA_DATO DATE,"; 
		$createStatement .= " SA_TITTEL VARCHAR (255),"; 
		$createStatement .= " SA_U1 CHAR (1),";
		$createStatement .= " SA_STATUS CHAR (2),";
		$createStatement .= " SA_ARKDEL CHAR (10),";
		$createStatement .= " SA_TYPE CHAR (10),";
		$createStatement .= " SA_JENHET CHAR (10),";
		$createStatement .= " SA_ADMID CHAR (10),";
		$createStatement .= " SA_ANSVID CHAR (10),";
		$createStatement .= " SA_TGKODE CHAR (2),";
		$createStatement .= " SA_UOFF VARCHAR (70),";
		$createStatement .= " SA_TGGRUPPE CHAR (10),";
		$createStatement .= " SA_ANTJP CHAR (2),";
		$createStatement .= " SA_BEVTID CHAR (2),";
		$createStatement .= " SA_KASSKODE CHAR (2),";
		$createStatement .= " SA_KASSDATO DATE,"; 
		$createStatement .= " SA_PROSJEKT VARCHAR (70),";
		$createStatement .= " SA_PRES VARCHAR (70),";
		$createStatement .= " SA_FRARKDEL VARCHAR (10),";
		$createStatement .= " SA_UTLDATO DATE,"; 
		$createStatement .= " SA_UTLTIL CHAR (2),";
		$createStatement .= " FOREIGN KEY (SA_TYPE) REFERENCES  SAKTYPE (ST_TYPE)";
/*		$createStatement .= " FOREIGN KEY (SA_STATUS) REFERENCES SAKSTAT (SS_STATUS),";
		$createStatement .= " FOREIGN KEY (SA_ARKDEL) REFERENCES ARKIVDEL (AD_ARKDEL),";

		$createStatement .= " FOREIGN KEY (SA_ADMID) REFERENCES ADMINDEL (AI_ID),";
		$createStatement .= " FOREIGN KEY (SA_ANSVID) REFERENCES PERSON (PE_ID),";
		$createStatement .= " FOREIGN KEY (SA_TGKODE) REFERENCES TGKODE (TK_TGKODE),";
		//TODO : Check if SA_TGGRUPPE REFERENCES TGGRP TG_GRUPPNAVN or TG_GRUPPEID 
		$createStatement .= " FOREIGN KEY (SA_TGGRUPPE) REFERENCES TGGRP (TG_GRUPPEID),";
		$createStatement .= " FOREIGN KEY (SA_KASSKODE) REFERENCES KASSKODE (KK_KODE),";		
		$createStatement .= " FOREIGN KEY (SA_FRARKDEL) REFERENCES ARKIVDEL (AD_ARKDEL)";		
*/				
		$createStatement .= ") engine = innodb; ";		
		return $createStatement;
	}	

	function createNUMSERIE() {
		$createStatement = " CREATE TABLE NUMSERIE  ( ";
		
		$createStatement .= " NU_ID CHAR(10),";
		$createStatement .= " NU_BETEGN CHAR(70),";
		$createStatement .= " NU_AAR CHAR(4),";
		$createStatement .= " NU_SEKNR1 CHAR(10),";
		$createStatement .= " NU_SEKNR2 CHAR(10),";
		$createStatement .= " NU_AARAUTO CHAR(1),";
		$createStatement .= " PRIMARY KEY (NU_ID, NU_AAR)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createOPRITYP() {
		$createStatement = " CREATE TABLE OPRITYP ( ";
		
		$createStatement .= " OT_KODE CHAR (10) PRIMARY KEY,";
		$createStatement .= " OT_BETEGN CHAR (70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createORDNPRI() {
		$createStatement = " CREATE TABLE ORDNPRI ( ";
		
		$createStatement .= " OP_ORDNPRI CHAR (10) PRIMARY KEY,";
		$createStatement .= " OP_BETEGN CHAR (70),";
		$createStatement .= " OP_LTEKST CHAR (20),";
		$createStatement .= " OP_TYPE CHAR (10),";
		$createStatement .= " OP_OVBESK CHAR (1),";
		$createStatement .= " OP_KLFLAGG CHAR (1),";
		$createStatement .= " OP_SIFLAGG CHAR (1),";
		$createStatement .= " OP_EVOK CHAR (1),";
		$createStatement .= " OP_EVAUTO CHAR (1),";
		$createStatement .= " OP_SEKFLAGG CHAR (1),";
		$createStatement .= " OP_FRADATO DATE,";
		$createStatement .= " OP_TILDATO DATE,";
		$createStatement .= " OP_TGKODE CHAR(2),";
		$createStatement .= " FOREIGN KEY (OP_TYPE) REFERENCES OPRITYP (OT_KODE)";		
				
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	
		
	function createORDNVERD() {
		$createStatement = " CREATE TABLE ORDNVERD ( ";
		
		$createStatement .= " OV_ORDNPRI CHAR (10),";
		$createStatement .= " OV_ORDNVER CHAR (70),";
		$createStatement .= " OV_FAR CHAR (70),";		
		$createStatement .= " OV_BESKR CHAR (255),";
		$createStatement .= " OV_REGFLAGG CHAR (1),";
		$createStatement .= " OV_SEKFLAGG CHAR (1),";
		$createStatement .= " INDEX (OV_ORDNVER),";
		$createStatement .= " PRIMARY KEY (OV_ORDNPRI, OV_ORDNVER)";
//		$createStatement .= " FOREIGN KEY (OV_ORDNPRI) REFERENCES ORDNPRI (OP_ORDNPRI)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createPERKLAR() {
		$createStatement = " CREATE TABLE PERKLAR ( ";
		
		$createStatement .= " KT_PEID CHAR(10),";
		$createStatement .= " KT_TGKODE CHAR(2),";
		$createStatement .= " KT_AUTHELE CHAR(1),";
		$createStatement .= " KT_KLAV CHAR(10),";
		$createStatement .= " KT_FRADATO DATE,";
		$createStatement .= " PRIMARY KEY (KT_PEID, KT_TGKODE),";
		$createStatement .= " FOREIGN KEY (KT_PEID) REFERENCES PERSON (PE_ID),";
		$createStatement .= " FOREIGN KEY (KT_TGKODE) REFERENCES TGKODE (TK_TGKODE),";
		$createStatement .= " FOREIGN KEY (KT_KLAV) REFERENCES PERSON (PE_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createPERNAVN() {
	// *Her er alle navn beskrevet
		$createStatement = " CREATE TABLE PERNAVN ( ";
		
		$createStatement .= " PN_ID CHAR(10) PRIMARY KEY,";
		$createStatement .= " PN_PEID CHAR(10),";
		// Om navnet er et nåværende navn
		$createStatement .= " PN_AKTIV CHAR(1),";
		$createStatement .= " PN_INIT CHAR(10),";
		$createStatement .= " PN_NAVN CHAR(70),";
		$createStatement .= " PN_ENAVN CHAR(30),";
		$createStatement .= " PN_FORNAVN CHAR(30),";
		$createStatement .= " PN_FRADATO DATE,";
		$createStatement .= " PN_TILDATO DATE,";
		$createStatement .= " FOREIGN KEY (PN_PEID) REFERENCES PERSON(PE_ID) ";

		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createPERROLLE() {
	// *
		$createStatement = " CREATE TABLE PERROLLE ( ";
		
		$createStatement .= " PR_ID CHAR(10) PRIMARY KEY,";
		$createStatement .= " PR_PEID CHAR(10),";
		$createStatement .= " PR_STDROLLE CHAR(1),";
		$createStatement .= " PR_TITTEL CHAR(30),";
		$createStatement .= " PR_ADMID CHAR(10),";
		$createStatement .= " PR_FRADATO DATE,";
		$createStatement .= " PR_TILDATO DATE,";
		$createStatement .= " PR_JENHET CHAR(10),";
		$createStatement .= " PR_ARKDEL CHAR(10),";
		$createStatement .= " INDEX (PR_PEID),";
		$createStatement .= " INDEX (PR_ADMID),";
		$createStatement .= " INDEX (PR_ARKDEL),";
		$createStatement .= " FOREIGN KEY (PR_PEID) REFERENCES PERSON(PE_ID),"; 
		$createStatement .= " FOREIGN KEY (PR_ADMID) REFERENCES ADMINDEL (AI_ID)";
		// Can't enforce referential integrity with null values, unless we allow a null value ARKIVDEL. Do I want to do that?
		
		//$createStatement .= " FOREIGN KEY (PR_ARKDEL) REFERENCES ARKIVDEL (AD_ARKDEL)";
		  
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createPERSON() {
	// *Dette er Person objektet med en ID og 2 datoer. Første er nå de kom i systemet og 
	// det er andre datoen systemet skal slutte å støtte søk for denne personen
		$createStatement = " CREATE TABLE PERSON ( ";
		
		$createStatement .= " PE_ID CHAR(10) PRIMARY KEY,";
		$createStatement .= " PE_BRUKERID CHAR(30),";
		$createStatement .= " PE_FRADATO DATE,";
		$createStatement .= " PE_TILDATO DATE,";
		$createStatement .= " INDEX (PE_BRUKERID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createPOSTNR() {
	// *Dette er Person objektet med en ID og 2 datoer. Første er nå de kom i systemet og 
	// det er andre datoen systemet skal slutte å støtte søk for denne personen
		$createStatement = " CREATE TABLE POSTNR ( ";
		
		$createStatement .= " PO_POSTNR CHAR(5) PRIMARY KEY,";
		$createStatement .= " PO_POSTSTED CHAR(70),";
		$createStatement .= " PO_KOMNR CHAR(4),";
		$createStatement .= " PO_KOMMUNE CHAR(70)";
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
	
	
	
	function createPOLSAKG() {

		$createStatement = " CREATE TABLE POLSAKG ( ";
		
		$createStatement .= " SG_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " SG_SAID CHAR (10),";		
		$createStatement .= " SG_SAKSTYPE CHAR (10),";
		$createStatement .= " SG_KLADGANG CHAR (1),";
		$createStatement .= " SG_LUKKET CHAR (1),";
		$createStatement .= " SG_UOFF CHAR (1),";
		$createStatement .= " SG_STARTDATO DATE,";
		$createStatement .= " SG_VEDTDATO DATE,";
		$createStatement .= " SG_SISTEVEDT CHAR(10),";
		$createStatement .= " SG_MERKNAD CHAR (255)";
//		$createStatement .= " FOREIGN KEY (SG_SAID) REFERENCES NOARKSAK (SA_ID),";
//		$createStatement .= " FOREIGN KEY (SG_SAKSTYPE) REFERENCES UTVSAKTY (SU_KODE)";
		// 
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createSAKPART() {
		$createStatement = " CREATE TABLE SAKPART ( ";
		
		$createStatement .= " SP_SAID CHAR (10),";
		$createStatement .= " SP_U1 CHAR (1),";
		$createStatement .= " SP_KORTNAVN CHAR (10),";
		$createStatement .= " SP_NAVN CHAR (70),";
		$createStatement .= " SP_ADRESSE CHAR (120),";
		$createStatement .= " SP_POSTNR CHAR (5),";
		$createStatement .= " SP_POSTSTED CHAR (60),";
		$createStatement .= " SP_EPOSTADR CHAR (120),";
		$createStatement .= " SP_UTLAND CHAR (120),";
		$createStatement .= " SP_KONTAKT CHAR (70),";
		$createStatement .= " SP_ROLLE CHAR (70),";
		$createStatement .= " SP_FAKS CHAR (20),";
		$createStatement .= " SP_TLF CHAR (20)";
//		$createStatement .= " PRIMARY KEY (SP_SAID, SP_NAVN),";
//		$createStatement .= " FOREIGN KEY (SP_SAID) REFERENCES NOARKSAK (SA_ID),";
		//$createStatement .= " FOREIGN KEY (SP_KORTNAVN) REFERENCES ADRESSEK (AK_KORTNAVN)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createSAKSTAT() {
		$createStatement = " CREATE TABLE SAKSTAT ( ";
		
		$createStatement .= " SS_STATUS CHAR (2) PRIMARY KEY,";
		$createStatement .= " SS_BETEGN CHAR (70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createSAKTYPE() 	{
		$createStatement = " CREATE TABLE SAKTYPE ( ";
		
		$createStatement .= " ST_TYPE CHAR (10) PRIMARY KEY,";
		$createStatement .= " ST_BETEGN CHAR (70),";
		$createStatement .= " ST_UOFF CHAR (1),";
		$createStatement .= " ST_KLAGEADG CHAR (1)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createSTATMDOK () {
		$createStatement = " CREATE TABLE STATMDOK ( ";
		
		$createStatement .= " MS_STATUS CHAR(2) PRIMARY KEY,";
		$createStatement .= " MS_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createTGGRP() {
		$createStatement = " CREATE TABLE TGGRP ( ";
		
		$createStatement .= " TG_GRUPPEID CHAR (10) PRIMARY KEY,";
		$createStatement .= " TG_GRUPPNAVN CHAR (70),";
		$createStatement .= " TG_GENERELL CHAR (1),";		
		$createStatement .= " TG_OPPRAV CHAR (10),";
		$createStatement .= " TG_FRADATO DATE,";
		$createStatement .= " TG_TILDATO DATE,";		
		$createStatement .= " FOREIGN KEY (TG_OPPRAV) REFERENCES PERSON (PE_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createTGHJEM() {
		$createStatement = " CREATE TABLE TGHJEM ( ";
		
		$createStatement .= " TH_TGKODE CHAR (2),";
		$createStatement .= " TH_UOFF CHAR (70),";
		$createStatement .= " TH_AGKODE CHAR (2),";
		$createStatement .= " TH_AGAAR CHAR (2),";
		$createStatement .= " TH_AGDAGER CHAR (3),";
		$createStatement .= " TH_ANVEND LONGTEXT,";
		$createStatement .= " INDEX (TH_TGKODE),";
		$createStatement .= " PRIMARY KEY (TH_TGKODE, TH_UOFF),";
		$createStatement .= " FOREIGN KEY (TH_TGKODE) REFERENCES TGKODE (TK_TGKODE)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
//TJ_FRADATO, TJ_FRADATO, TJ_AUTOPPAV
	function createTGINFO() {
		$createStatement = " CREATE TABLE TGINFO ( ";
		
		$createStatement .= " TJ_PEID CHAR (10),";
		$createStatement .= " TJ_JENHET CHAR (10),";
		$createStatement .= " TJ_ADMID CHAR (10),";
		$createStatement .= " TJ_AUTAV CHAR (10),";
		$createStatement .= " TJ_AUTOPPAV CHAR (10),";
		$createStatement .= " TJ_FRADATO DATE,";
		$createStatement .= " TJ_TILDATO DATE,";
		$createStatement .= " PRIMARY KEY (TJ_PEID, TJ_JENHET, TJ_ADMID)";		
//		$createStatement .= " FOREIGN KEY (TJ_PEID) REFERENCES PERSON (PE_ID),";
		// can contain null values
		//$createStatement .= " FOREIGN KEY (TJ_JENHET) REFERENCES JOURNENH (JE_JENHET),";
//		$createStatement .= " FOREIGN KEY (TJ_ADMID) REFERENCES ADMINDEL (AI_ID),";
//		$createStatement .= " FOREIGN KEY (TJ_AUTAV) REFERENCES PERSON (PE_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createTGKODE() {
		$createStatement = " CREATE TABLE TGKODE ( ";
		
		$createStatement .= " TK_TGKODE CHAR (2) PRIMARY KEY,";
		$createStatement .= " TK_BETEGN CHAR (70),";
		$createStatement .= " TK_SERIE CHAR (4),";		
		$createStatement .= " TK_EPOSTNIV CHAR (1),";
		$createStatement .= " TK_FRADATO DATE,";
		$createStatement .= " TK_TILDATO DATE";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}


 // TODO: CHECK THIS . STANDARD SAYS PG.PERROLID IS PRIMARY KEY, BUT IS MISSING
 
	function createTGMEDLEM() {
		$createStatement = " CREATE TABLE TGMEDLEM ( ";
		
		$createStatement .= " PG_PEID CHAR (10),";
		$createStatement .= " PG_GRUPPEID CHAR (10),";
		$createStatement .= " PG_INNMAV CHAR (10),";
		$createStatement .= " PRIMARY KEY (PG_PEID, PG_GRUPPEID),";
		$createStatement .= " FOREIGN KEY (PG_PEID) REFERENCES PERSON (PE_ID),";
		$createStatement .= " FOREIGN KEY (PG_GRUPPEID) REFERENCES TGGRP (TG_GRUPPEID),";
		$createStatement .= " FOREIGN KEY (PG_INNMAV) REFERENCES PERSON (PE_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createTILLEGG() {
		$createStatement = " CREATE TABLE TILLEGG ( ";
		
		$createStatement .= " TI_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " TI_SAID CHAR (10),";
		$createStatement .= " TI_JPID CHAR (10),";
		$createStatement .= " TI_DOKID CHAR (10),";
		$createStatement .= " TI_DOKVER CHAR (5),";
		$createStatement .= " TI_VARIANT CHAR(2),";
		$createStatement .= " TI_RNR CHAR(6),";
		$createStatement .= " TI_ITYPE CHAR(10),";
		$createStatement .= " TI_TGKODE CHAR(2),";
		$createStatement .= " TI_TGGRUPPE CHAR(10),";
		$createStatement .= " TI_REGDATO DATE,";
		$createStatement .= " TI_REGKL TIME,";
		$createStatement .= " TI_REGAV CHAR (10),";
		$createStatement .= " TI_PVGAV CHAR (10),";
		$createStatement .= " TI_TEKST CHAR (255)";
		//$createStatement .= " FOREIGN KEY (TI_SAID) REFERENCES NOARKSAK (SA_ID),";
		//$createStatement .= " FOREIGN KEY (TI_JPID) REFERENCES JOURNPST (JP_ID),";
		//$createStatement .= " FOREIGN KEY (TI_DOKID) REFERENCES DOKBESK (DB_DOKID),";
		//$createStatement .= " FOREIGN KEY (TI_ITYPE) REFERENCES INFOTYPE (IT_KODE),";
		//$createStatement .= " FOREIGN KEY (TI_REGAV) REFERENCES PERSON (PE_ID),";
		//$createStatement .= " FOREIGN KEY (TI_PVGAV) REFERENCES PERSON (PE_ID)";
				
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createTLKODE() {
		$createStatement = " CREATE TABLE TLKODE ( ";
		
		$createStatement .= " TL_KODE CHAR (10) PRIMARY KEY,";
		$createStatement .= " TL_BETEGN CHAR (70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createUTDOKTYP() {
		$createStatement = " CREATE TABLE UTDOKTYP ( ";
		
		$createStatement .= " DU_KODE CHAR(2) PRIMARY KEY,";
		$createStatement .= " DU_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createUTVALG() {

		$createStatement = " CREATE TABLE UTVALG ( ";
		
		$createStatement .= " UT_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " UT_KODE CHAR (10),";
		$createStatement .= " UT_NAVN CHAR (70),";
		$createStatement .= " UT_ADMID CHAR (10),";
		$createStatement .= " UT_ARKDEL CHAR (10),";
		$createStatement .= " UT_MONUMSER CHAR (10),";		
		$createStatement .= " UT_NEDLAGT DATE";
		//$createStatement .= " FOREIGN KEY (UT_ARKDEL) REFERENCES ARKIVDEL (AD_ARKDEL),";
		//$createStatement .= " FOREIGN KEY (UT_ADMID) REFERENCES ADMINDEL (AI_ID)";
//		$createStatement .= " FOREIGN KEY (UT_MONUMSER) REFERENCES NUMSERIE (NU_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
	
	function createUTVBEH() {
		$createStatement = " CREATE TABLE UTVBEH ( ";
		
		$createStatement .= " UB_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " UB_UTSAKID CHAR (10),";
		$createStatement .= " UB_RFOLGE CHAR (3),";
		$createStatement .= " UB_MOID CHAR (10),";
		$createStatement .= " UB_USEKNR CHAR (5),";
		$createStatement .= " UB_AAR CHAR (4),";
		$createStatement .= " UB_BEHSTATUS CHAR (2),";
		$createStatement .= " UB_ADMID CHAR (10),";
		$createStatement .= " UB_SBHID CHAR (10),";
		$createStatement .= " UB_PROTOKOLL CHAR (1),";
		$createStatement .= " FOREIGN KEY (UB_UTSAKID) REFERENCES NOARKSAK (SA_ID),";
		//$createStatement .= " FOREIGN KEY (UB_MOID) REFERENCES UTVMOTE (MO_ID),";
		$createStatement .= " FOREIGN KEY (UB_BEHSTATUS) REFERENCES UTVBEHSTAT (BS_STATUS),";
		$createStatement .= " FOREIGN KEY (UB_ADMID) REFERENCES ADMINDEL (AI_ID),";
		$createStatement .= " FOREIGN KEY (UB_SBHID) REFERENCES PERSON (PE_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createUTVBEHDO() {

		$createStatement = " CREATE TABLE UTVBEHDO ( ";
		
		$createStatement .= " BD_BEHID CHAR (10),";
		$createStatement .= " BD_DOKID CHAR (10),";
		$createStatement .= " BD_NDOKTYPE CHAR (1),";
		$createStatement .= " BD_JPID CHAR (10),";
		$createStatement .= " BD_DOKTYPE CHAR (2),";
		$createStatement .= " BD_STATUS CHAR (2),";
		$createStatement .= " PRIMARY KEY (BD_BEHID, BD_DOKID),";
//		$createStatement .= " FOREIGN KEY (BD_DOKID) REFERENCES DOKBESK (DB_DOKID),";
		$createStatement .= " FOREIGN KEY (BD_NDOKTYPE) REFERENCES DOKTYPE (ND_DOKTYPE),";
		$createStatement .= " FOREIGN KEY (BD_JPID) REFERENCES JOURNPST (JP_ID),";
		$createStatement .= " FOREIGN KEY (BD_DOKTYPE) REFERENCES UTDOKTYP (DU_KODE),";
		$createStatement .= " FOREIGN KEY (BD_STATUS) REFERENCES STATMDOK (MS_STATUS)";
				
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createUTVBEHSTAT() {

		$createStatement = " CREATE TABLE UTVBEHSTAT ( ";
		
		$createStatement .= " BS_STATUS CHAR(2) PRIMARY KEY,";
		$createStatement .= " BS_BETEGN CHAR(70),";
		$createStatement .= " BS_KOLISTE CHAR(1),";
		$createStatement .= " BS_KANSKART CHAR(1),";
		$createStatement .= " BS_SAKSKART CHAR(1),";
		$createStatement .= " BS_BEHANDLET CHAR(1),";
		$createStatement .= " BS_SORT1 CHAR(3)";
	
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
		
	
	
	function createUTVMEDL() {
		$createStatement = " CREATE TABLE UTVMEDL ( ";
		
		$createStatement .= " UM_UTVID CHAR (10),";
		$createStatement .= " UM_PNID CHAR (10),";
		$createStatement .= " UM_RANGERING CHAR (3),";
		$createStatement .= " UM_VARAFOR CHAR (10),";
		$createStatement .= " UM_FUNK CHAR (10),";		
		$createStatement .= " UM_FRADATO DATE,";
		$createStatement .= " UM_TILDATO DATE,";
		$createStatement .= " UM_SORT CHAR (3),";
		$createStatement .= " UM_REPRES CHAR (10),";
		$createStatement .= " UM_SEKR CHAR (1),";
		$createStatement .= " UM_MEDLEM CHAR (1),";
		$createStatement .= " UM_MERKNAD VARCHAR(255),";
		$createStatement .= " PRIMARY KEY(UM_UTVID, UM_PNID, UM_TILDATO),"; 
		$createStatement .= " FOREIGN KEY (UM_PNID) REFERENCES PERSON (PE_ID),";
		// THis one needs to be active
		//$createStatement .= " FOREIGN KEY (UM_VARAFOR) REFERENCES PERSON (PE_ID),";
		$createStatement .= " FOREIGN KEY (UM_FUNK) REFERENCES UTVMEDLF (MK_KODE)";
		//$createStatement .= " FOREIGN KEY (UM_REPRES) REFERENCES ADRESSEK (AK_ADRID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createUTVMEDLF() {
		$createStatement = " CREATE TABLE UTVMEDLF ( ";
		
		$createStatement .= " MK_KODE CHAR (10) PRIMARY KEY,";
		$createStatement .= " MK_BETEGN CHAR (70),";
		$createStatement .= " MK_TALE CHAR (1),";
		$createStatement .= " MK_MEDLEM CHAR (1),";				
		$createStatement .= " MK_SEKR CHAR (1),";
		$createStatement .= " MK_FMKODE CHAR (1)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	
	function createUTVMOTE() {

		$createStatement = " CREATE TABLE UTVMOTE ( ";
		
		$createStatement .= " MO_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " MO_NR CHAR (5),";
		$createStatement .= " MO_UTVID CHAR (10),";
		$createStatement .= " MO_LUKKET CHAR (1),";
		$createStatement .= " MO_DATO DATE,";
		$createStatement .= " MO_START TIME,";
		$createStatement .= " MO_STED CHAR (70),";
		$createStatement .= " MO_FRIST CHAR (70),";
		$createStatement .= " MO_SAKSKART CHAR (1),";
		$createStatement .= " MO_PROTOKOLL CHAR (1)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}
	
	
	function createUTVSAK() {
		$createStatement = " CREATE TABLE UTVSAK ( ";
		
		$createStatement .= " US_UTVID CHAR (10),";
		$createStatement .= " US_ID CHAR (10) PRIMARY KEY,";
		$createStatement .= " US_SAKSTYPE CHAR (10),";
		$createStatement .= " US_TITTEL CHAR (255),";
		$createStatement .= " US_U1 CHAR (1),";
		$createStatement .= " US_LUKKET CHAR (12),";
		$createStatement .= " US_TGKODE CHAR (2),";
		$createStatement .= " US_TGGRUPPE CHAR (10),";
		$createStatement .= " US_UOFF CHAR (70),";
		$createStatement .= " US_SAMMENR CHAR (1),";
		$createStatement .= " US_MERKNAD CHAR (255),";
		$createStatement .= " US_SAID CHAR (10),";
		$createStatement .= " US_POLSGID CHAR (10),";
		$createStatement .= " US_JPID CHAR (10)";
		// TODO : Check this $createStatement .= " FOREIGN KEY (US_UTVID) REFERENCES  ),";		
//		$createStatement .= " FOREIGN KEY (US_SAKSTYPE) REFERENCES UTVSAKTY (SU_KODE), ";
//		$createStatement .= " FOREIGN KEY (US_TGKODE) REFERENCES TGKODE (TK_TGKODE),";
//		$createStatement .= " FOREIGN KEY (US_TGGRUPPE) REFERENCES TGGRP (TG_GRUPPEID),";
//		$createStatement .= " FOREIGN KEY (US_SAID) REFERENCES NOARKSAK (SA_ID),";
//		$createStatement .= " FOREIGN KEY (US_POLSGID) REFERENCES POLSAKG (SG_ID)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createUTVSAKTY() {
		$createStatement = " CREATE TABLE UTVSAKTY ( ";
		
		$createStatement .= " SU_KODE CHAR(10) PRIMARY KEY,";
		$createStatement .= " SU_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}

	function createVARFORM() {
		$createStatement = " CREATE TABLE VARFORM ( ";
		
		$createStatement .= " VF_KODE CHAR(2) PRIMARY KEY,";
		$createStatement .= " VF_BETEGN CHAR(70)";
		
		$createStatement .= ") engine = innodb; ";
		return $createStatement;
	}


	function createNoarkDB() {
		
		//  Delete this method!!! It confueses
	// The ordering here is important as it takes into consideration 
	// the foreign key relations
		/*
		createADMINDEL();
		$this->createALIASADM();
		$this->createADRTYPE();
		$this->createADRESSEK();		
		$this->createADRADMENH();		
		$this->createALIASADM();		
		$this->createPERSON();		
		$this->createADRPERS();
		$this->createNUMSERIE();
		$this->createARKIV();		
		$this->createBSKODE();
		$this->createOPRITYP();
		$this->createORDNPRI();
		$this->createARSTATUS();
		createARKIVDEL();
		createAVGRKODE();
		createAVSKRM();
		createKASSKODE();
		createTGKODE();
		createTGGRP();
		createSAKSTAT();
		createSAKTYPE();
		createNOARKSAK();
		createDOKTYPE();
		createJOURNSTA();
		createORDNVERD();
		createTLKODE();
		createJOURNPST();
		createLAGRENH();
		createLAGRFORM();	
		createMEDADRGR();
		createFORSMATE();
		createFSTATUS();
		createAVSMOT();
		createDOKKAT();
		createDOKSTAT();
		createTGKODE();
		createTGHJEM();
		createDOKBESK();
		createDOKLINK();
		createDOKTILKN();
		createDOKTYPE();
		createVARFORM();
		createDOKVERS();
		createEARKKODE();
		createENHTYPE();
		createINFOTYPE();
		createJOURNENH();
		createKLASS();
		createPERKLAR();
		createPERNAVN();		
		createMERKNAD();
		createPERROLLE();
		createUTVSAKTY();
		createPOLSAKG();
		createSAKPART();
		createSTATMDOK();
		createTGMEDLEM();
		createTILLEGG();
		createUTDOKTYP();
		createUTVALG();
		createUTVMOTE();
		createUTVBEHSTAT();
		createUTVBEH();
		createUTVBEHDO();
		createUTVBEHSTAT();
		createUTVMEDLF();
		createUTVSAK();*/
	}
	

	
	function testData () {	
		$insertStatement = " INSERT INTO `NOARKSAK` (`SA_ID`, `SA_SAAR`, `SA_SEKNR`, `SA_PAPIR`, `SA_DATO`, `SA_TITTEL`, `SA_U1`, `SA_STATUS`, `SA_ARKDEL`, `SA_TYPE`, `SA_JENHET`, `SA_OPSAKSDEL`, `SA_ADMID`, `SA_ANSVID`, `SA_TGKODE`, `SA_UOFF`, `SA_TGGRUPPE`, `SA_SISTEJP`, `SA_ANTJP`, `SA_BEVTID`, `SA_KASSKODE`, `SA_KASSDATO`, `SA_PROSJEKT`, `SA_PRES`, `SA_OBS`, `SA_FRARKDEL`, `SA_UTLDATO`, `SA_UTLTIL`) VALUES (";	
		$insertStatement.= "'1', ";
		$insertStatement.= "'00', ";
		$insertStatement.= "'01', ";
		$insertStatement.= "'J', ";
		$insertStatement.= "'2012-1-1', "; 
		$insertStatement.= "'TEST DATA', "; 
		$insertStatement.= "'O', ";
		$insertStatement.= "'UU', ";
		$insertStatement.= "'arkivdel', ";
		$insertStatement.= "'typen', ";
		$insertStatement.= "'jourenhet', ";         
		$insertStatement.= "'qweree',";
		$insertStatement.= "'ewewewew',";
		$insertStatement.= "'ansvid', ";
		$insertStatement.= "'tg', ";
		$insertStatement.= "'uoffff', ";
		$insertStatement.= "'tggrp',";
		$insertStatement.= "'2012-1-2',";
		$insertStatement.= "'4', ";
		$insertStatement.= "'Bt', ";
		$insertStatement.= "'KK', ";
		$insertStatement.= "'2012-1-3',";
		$insertStatement.= "'prosjekt',"; 
		$insertStatement.= "'pres', ";
		$insertStatement.= "'obs', ";
		$insertStatement.= "'fraarkivdel', "; 
		$insertStatement.= "'2012-1-4', ";
		$insertStatement.= "'utlaantil'	";
		$insertStatement.= ")";
		
		return $insertStatement;
	}

		
	
}
