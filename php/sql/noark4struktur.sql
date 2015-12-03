# This file has been made from Noark 4 DTDs with the intent to create Noark 4 extractions
# This sql code has been developed for mysql and comes with no warranties
# Free to be used in proejcts as you like, no citation required

# Need to check how dates are stored in the FROM database DATE/DATETIME

CREATE DATABASE Noark4;

CREATE TABLE NOARKSAK (
	SA_ID CHAR(10), # Should be numeric, can enforce using regep
	SA_SAAR CHAR(4) NOT NULL,  # numeric
	SA_SEKNR CHAR(6) NOT NULL, # numeric
	SA_PAPIR CHAR(1),  # 0 or 1
	SA_DATO DATE NOT NULL, 
	SA_TITTEL VARCHAR(255) NOT NULL, 
	SA_U1 CHAR(1) NOT NULL, 
	SA_STATUS CHAR(2) NOT NULL, # Oppslag mot tabellen Saksstatus
	SA_ARKDEL CHAR(10) NOT NULL, # Oppslag mot tabellen
	SA_TYPE CHAR(10), # Oppslag mot tabellen Sakstype
	SA_JENHET CHAR(10), # Oppslag mot tabellen Journalenhet.         
	SA_OPSAKSDEL CHAR(10), #Oppslag mot tabellen Ordningsprinsipp
	SA_ADMID CHAR(10) NOT NULL,  # numeric Oppslag mot tabellen Administrativ inndeling
	SA_ANSVID CHAR(10) NOT NULL,  # numeric Oppslag mot tabellen Personnavn
	SA_TGKODE CHAR(2), # Oppslag mot tabellen Hjemmel for tilgangskode
	SA_UOFF VARCHAR(70), 
	SA_TGGRUPPE CHAR(10),  # numeric
	SA_SISTEJP DATE, 
	SA_ANTJP CHAR(2) NOT NULL,  # numeric
	SA_BEVTID CHAR(2),  # numeric
	SA_KASSKODE CHAR(2), # Oppslag mot tabellen Kassasjonskode
	SA_KASSDATO DATE, 
	SA_PROSJEKT VARCHAR(70), 
	SA_PRES VARCHAR(70), 
	SA_OBS DATE, 
	SA_FRARKDEL VARCHAR(10), 
	SA_UTLDATO DATE, 
	SA_UTLTIL VARCHAR(70), # numeric Oppslag mot tabellen Personnavn
	PRIMARY KEY(SA_ID))
engine = innodb;


CREATE TABLE JOURNPOST (
	JP_ID CHAR(10) NOT NULL, # numeric
	JP_JAAR CHAR(4) NOT NULL, # numeric
	JP_SEKNR CHAR(7) NOT NULL, # numeric
	JP_SAID CHAR(10) NOT NULL, # numeric (ref til NOARKSAK.SA_ID??)
	JP_JPOSTNR CHAR(4) NOT NULL, # numeric 
	JP_JDATO DATE NOT NULL, 
	JP_NDOKTYPE CHAR(1) NOT NULL, # Oppslag mot tabellen Noark dokumenttype
	JP_DOKDATO DATE, 
	JP_UDATERT CHAR(1) NOT NULL, 
	JP_STATUS CHAR(2) NOT NULL, # Oppslag mot tabellen Journalstatus
	JP_INNHOLD VARCHAR(255) NOT NULL, 
	JP_U1 CHAR(1) NOT NULL, 
	JP_AVSKDATO DATE, 
	JP_EKSPDATO DATE, 
	JP_FORFDATO DATE, 
	JP_TGKODE CHAR(2), 
	JP_UOFF CHAR(70), 
	JP_OVDATO DATE, 
	JP_AGDATO DATE,  
	JP_AGKODE CHAR(2), # Oppslag mot tabellen Avgraderingskode 
	JP_TGGRUPPE CHAR(10),  # numeric
	JP_SAKSDEL VARCHAR(70), # Oppslag på grunnlag av innholdet av attributtet Ordningsprinsipp saksdeler i saken mot tabellen Ordningsverdi
	JP_U2 CHAR(1), 
	JP_ARKDEL CHAR(10), 
	JP_PAPIR CHAR(1), 
	JP_TLKODE CHAR(10), 
	JP_ANTVED CHAR(2), # numeric 
	JP_UTLDATO DATE, 
	JP_UTLTIL CHAR(10), # numeric Oppslag mot tabellen Personnavn
	PRIMARY KEY(JP_ID),
	FOREIGN KEY (JP_SAID) REFERENCES NOARKSAK (SA_ID))
engine = innodb;


CREATE TABLE SAKPART (
	SP_SAID CHAR(10) NOT NULL, # numeric 
	SP_U1 CHAR(1) NOT NULL, 
	SP_KORTNAVN CHAR(10), # Oppslag mot tabellen Adresseregister (numeric??)
	SP_NAVN VARCHAR(70) NOT NULL, 
	SP_ADRESSE VARCHAR(120), 
	SP_POSTNR CHAR(5),
	SP_POSTSTED VARCHAR(70), 
	SP_UTLAND VARCHAR(120), 
	SP_EPOSTADR VARCHAR(120), 
	SP_KONTAKT VARCHAR(70), 
	SP_ROLLE VARCHAR(70), 
	SP_FAKS VARCHAR(20), 
	SP_TLF VARCHAR(70), 
	SP_MERKNAD VARCHAR(255),
	FOREIGN KEY (SP_SAID) REFERENCES NOARKSAK (SA_ID),
	PRIMARY KEY(SP_SAID, SP_NAVN))
engine = innodb;



CREATE TABLE DOKLINK (
	DL_JPID CHAR(10) NOT NULL,
	DL_RNR ,
	DL_TYPE ,
	DL_DOKID ,
	DL_TKDATO , # Tilknyttet Dato
	DL_TKAV, # Tilknyttet av
	FOREIGN KEY (DP_JPID) REFERENCES JOURNPOST (JP_ID),
	PRIMARY KEY(DP_JPIP)
)engine = innodb;
