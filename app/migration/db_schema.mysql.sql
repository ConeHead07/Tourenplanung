-- --------------------------------------------------------
-- Host:                         10.30.2.132
-- Server Version:               10.3.7-MariaDB-1:10.3.7+maria~jessie-log - mariadb.org binary distribution
-- Server Betriebssystem:        debian-linux-gnu
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Exportiere Struktur von Funktion mt_rm.ExtractNumber
DELIMITER //
CREATE FUNCTION `ExtractNumber`(
	`in_string` VARCHAR(250)
) RETURNS int(11)
    NO SQL
BEGIN
    DECLARE ctrNumber VARCHAR(250);
    DECLARE finNumber VARCHAR(250) DEFAULT '';
    DECLARE sChar VARCHAR(1);
    DECLARE inti INTEGER DEFAULT 1;

    IF LENGTH(in_string) > 0 THEN
        WHILE(inti <= LENGTH(in_string)) DO
            SET sChar = SUBSTRING(in_string, inti, 1);
            SET ctrNumber = FIND_IN_SET(sChar, '0,1,2,3,4,5,6,7,8,9');
            IF ctrNumber > 0 THEN
                SET finNumber = CONCAT(finNumber, sChar);
            END IF;
            SET inti = inti + 1;
        END WHILE;
        IF (LENGTH(finNumber) > 0) THEN
        		RETURN CAST(finNumber AS UNSIGNED);
        ELSE
        		RETURN 1;
        	END IF;
    ELSE
        RETURN 1;
    END IF;
END//
DELIMITER ;

-- Exportiere Struktur von Tabelle mt_rm.mr_auftragskoepfe_dispofilter
CREATE TABLE IF NOT EXISTS `mr_auftragskoepfe_dispofilter` (
  `Mandant` tinyint(4) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `Auftragsart` tinyint(3) DEFAULT NULL,
  `zusatzvorgangsartnr` smallint(5) DEFAULT NULL,
  `ZusatzVorgangsArtBezeichnung` varchar(150) DEFAULT NULL,
  `Bearbeitungsstatus` tinyint(4) NOT NULL,
  `Lieferwoche` tinyint(4) DEFAULT NULL,
  `Lieferjahr` tinyint(4) DEFAULT NULL,
  `Liefertermin` date DEFAULT NULL,
  `LieferterminFix` tinyint(4) NOT NULL,
  `LieferterminHinweisText` text DEFAULT NULL,
  `Auftragswert` decimal(10,2) DEFAULT NULL,
  `AuftragswertListe` decimal(10,2) DEFAULT NULL,
  `Gruppierungsnummer` bigint(20) DEFAULT NULL,
  `Vorgangstitel` varchar(100) DEFAULT NULL,
  `LieferungName` varchar(255) DEFAULT NULL,
  `Kundennummer` bigint(20) DEFAULT NULL,
  `LieferungOrt` varchar(50) NOT NULL,
  `LieferungLand` varchar(10) NOT NULL,
  `LieferungStrassePostfach` varchar(50) NOT NULL,
  `LieferungPostleitzahl` varchar(10) NOT NULL,
  `AnsprechpartnerNachnameLief` varchar(50) NOT NULL,
  `Geschaeftsbereich` varchar(20) DEFAULT NULL,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  `BestaetigtAm` datetime DEFAULT NULL,
  `mr_modified` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Mandant`,`Auftragsnummer`),
  KEY `Lieferwoche` (`Lieferwoche`),
  KEY `Lieferjahr` (`Lieferjahr`),
  KEY `Liefertermin` (`Liefertermin`),
  KEY `Bearbeitungsstatus` (`Bearbeitungsstatus`),
  KEY `AngelegtAm` (`AngelegtAm`),
  KEY `GeaendertAm` (`GeaendertAm`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_auftragskoepfe_dispofilter_archiv_20180611
CREATE TABLE IF NOT EXISTS `mr_auftragskoepfe_dispofilter_archiv_20180611` (
  `Mandant` tinyint(4) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `Bearbeitungsstatus` tinyint(4) NOT NULL,
  `UnterBearbeitungsstatus` tinyint(4) NOT NULL,
  `Lieferwoche` tinyint(4) DEFAULT NULL,
  `Lieferjahr` tinyint(4) DEFAULT NULL,
  `Liefertermin` date DEFAULT NULL,
  `LieferterminFix` tinyint(4) NOT NULL,
  `LieferterminHinweisText` varchar(255) DEFAULT NULL,
  `Auftragswert` decimal(10,2) DEFAULT NULL,
  `AuftragswertListe` decimal(10,2) DEFAULT NULL,
  `Gruppierungsnummer` int(11) NOT NULL,
  `Vorgangstitel` varchar(100) DEFAULT NULL,
  `LieferungName` varchar(255) DEFAULT NULL,
  `Kundennummer` int(11) NOT NULL,
  `LieferungOrt` varchar(50) NOT NULL,
  `LieferungLand` varchar(10) NOT NULL,
  `LieferungStrassePostfach` varchar(50) NOT NULL,
  `LieferungPostleitzahl` varchar(10) NOT NULL,
  `AnsprechpartnerNachnameLief` varchar(50) NOT NULL,
  `Geschaeftsbereich` varchar(20) NOT NULL,
  `RechnungName` varchar(255) DEFAULT NULL,
  `AngebotName` varchar(255) DEFAULT NULL,
  `DirektLieferInfo` varchar(255) DEFAULT NULL,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  `BestaetigtAm` datetime DEFAULT NULL,
  `mr_modified` datetime NOT NULL,
  PRIMARY KEY (`Mandant`,`Auftragsnummer`),
  KEY `Lieferwoche` (`Lieferwoche`),
  KEY `Lieferjahr` (`Lieferjahr`),
  KEY `Liefertermin` (`Liefertermin`),
  KEY `Bearbeitungsstatus` (`Bearbeitungsstatus`),
  KEY `Schlüssel 6` (`AngelegtAm`),
  KEY `Schlüssel 7` (`GeaendertAm`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_auftragskoepfe_refs
CREATE TABLE IF NOT EXISTS `mr_auftragskoepfe_refs` (
  `Mandant` tinyint(4) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `Mandant_ref` tinyint(4) NOT NULL,
  `Auftragsnummer_ref` bigint(20) NOT NULL,
  KEY `Mandant` (`Mandant`,`Auftragsnummer`),
  KEY `Mandant_ref` (`Mandant_ref`,`Auftragsnummer_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_auftragspositionen_dispofilter_archiv_20180611
CREATE TABLE IF NOT EXISTS `mr_auftragspositionen_dispofilter_archiv_20180611` (
  `Mandant` tinyint(4) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  `Positionsart` tinyint(4) NOT NULL,
  `StruPosNr` varchar(255) NOT NULL,
  `Artikelnummer` varchar(30) NOT NULL,
  `Bezeichnung` varchar(255) NOT NULL,
  `Bestellmenge` decimal(10,2) NOT NULL,
  `Bestellmengeneinheit` varchar(20) NOT NULL,
  `Liefermenge` decimal(10,2) NOT NULL,
  `Preis` decimal(10,2) NOT NULL,
  `Lieferwoche` tinyint(4) DEFAULT NULL,
  `Lieferjahr` tinyint(4) DEFAULT NULL,
  `Liefertermin` datetime DEFAULT NULL,
  `LieferterminFix` tinyint(4) NOT NULL,
  `AvisierterTermin` datetime DEFAULT NULL,
  `AvisierteWoche` tinyint(4) DEFAULT NULL,
  `AvisiertesJahr` tinyint(4) DEFAULT NULL,
  `AvisierterTerminFix` tinyint(4) NOT NULL,
  `AvisierterTerminDauer` smallint(6) DEFAULT NULL,
  `Lagerkennung` varchar(15) NOT NULL,
  `Stellplatz` varchar(50) DEFAULT NULL,
  `Positionstext` text DEFAULT NULL,
  `InternePos` tinyint(4) NOT NULL DEFAULT 0,
  `AlternativPos` tinyint(4) NOT NULL DEFAULT 0,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertDurch` varchar(30) DEFAULT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  `mr_modified` datetime NOT NULL,
  PRIMARY KEY (`Mandant`,`Auftragsnummer`,`Positionsnummer`),
  KEY `Lieferwoche` (`Lieferwoche`),
  KEY `Lieferjahr` (`Lieferjahr`),
  KEY `Liefertermin` (`Liefertermin`),
  KEY `Positionsart` (`Positionsart`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_bestellkoepfe
CREATE TABLE IF NOT EXISTS `mr_bestellkoepfe` (
  `Mandant` tinyint(4) NOT NULL,
  `Bestellnummer` int(11) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `Stellplatz` varchar(50) NOT NULL,
  PRIMARY KEY (`Mandant`,`Bestellnummer`),
  KEY `Auftragsnummer` (`Auftragsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_bestellkoepfe_dispofilter_archiv_20180611
CREATE TABLE IF NOT EXISTS `mr_bestellkoepfe_dispofilter_archiv_20180611` (
  `Mandant` tinyint(4) NOT NULL,
  `Bestellnummer` int(11) NOT NULL,
  `Bestellungstyp` tinyint(4) NOT NULL,
  `Bestellart` tinyint(4) DEFAULT NULL,
  `Bearbeitungsstatus` tinyint(4) NOT NULL,
  `Kundennummer` varchar(20) NOT NULL,
  `BestellName` varchar(255) NOT NULL,
  `Lieferwoche` tinyint(4) DEFAULT NULL,
  `Lieferjahr` tinyint(4) DEFAULT NULL,
  `Liefertermin` datetime DEFAULT NULL,
  `LieferterminFix` tinyint(4) NOT NULL,
  `Auftragsnummer` bigint(20) DEFAULT NULL,
  `Lagerkennung` varchar(15) NOT NULL,
  `Bestellwert` decimal(10,2) DEFAULT NULL,
  `Lieferbedingung` varchar(50) DEFAULT NULL,
  `ErwarteterEingang` datetime DEFAULT NULL,
  `ErwarteterEingangWoche` smallint(6) DEFAULT NULL,
  `ErwarteterEingangJahr` smallint(6) DEFAULT NULL,
  `ErwarteterEingangterminFix` tinyint(4) NOT NULL,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  `mr_modified` datetime NOT NULL,
  KEY `Lieferwoche` (`Lieferwoche`),
  KEY `Lieferjahr` (`Lieferjahr`),
  KEY `Liefertermin` (`Liefertermin`),
  KEY `Auftragsnummer` (`Auftragsnummer`),
  KEY `Mandant` (`Mandant`,`Bestellnummer`),
  KEY `mr_modified` (`mr_modified`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_bestellpositionen
CREATE TABLE IF NOT EXISTS `mr_bestellpositionen` (
  `Mandant` tinyint(3) NOT NULL,
  `Bestellnummer` int(11) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `AuftragsPositionsnummer` int(11) NOT NULL,
  `Stellplatz` varchar(50) NOT NULL,
  `StellplatzHistorie` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`Mandant`,`Bestellnummer`,`Positionsnummer`),
  KEY `Auftragsnummer` (`Auftragsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_bestellpositionen_dispofilter_archiv_20180611
CREATE TABLE IF NOT EXISTS `mr_bestellpositionen_dispofilter_archiv_20180611` (
  `Mandant` tinyint(4) NOT NULL,
  `Bestellnummer` int(11) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  `StruPosnr` varchar(255) NOT NULL,
  `Positionsart` tinyint(4) NOT NULL,
  `Artikelnummer` varchar(30) NOT NULL,
  `Bezeichnung` varchar(255) NOT NULL,
  `Bestellmenge` decimal(10,2) NOT NULL,
  `Liefermenge` decimal(10,2) NOT NULL,
  `Lieferanschrift` int(11) NOT NULL,
  `Lieferwoche` tinyint(4) NOT NULL,
  `Lieferjahr` tinyint(4) NOT NULL,
  `Liefertermin` datetime DEFAULT NULL,
  `LieferterminFix` tinyint(4) NOT NULL,
  `Lagerkennung` varchar(15) DEFAULT NULL,
  `Auftragsnummer` bigint(20) DEFAULT NULL,
  `AuftragsPositionsnummer` int(11) DEFAULT NULL,
  `ErwarteterEingang` datetime DEFAULT NULL,
  `ErwarteterEingangWoche` smallint(6) DEFAULT NULL,
  `ErwarteterEingangJahr` smallint(6) DEFAULT NULL,
  `ErwarteterEingangterminFix` tinyint(4) NOT NULL,
  `HerstellerKuerzel` varchar(50) DEFAULT NULL,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertDurch` varchar(30) DEFAULT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  `mr_modified` datetime NOT NULL,
  PRIMARY KEY (`Mandant`,`Bestellnummer`,`Positionsnummer`),
  KEY `Auftragsnummer` (`Auftragsnummer`),
  KEY `mr_modified` (`mr_modified`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_extern
CREATE TABLE IF NOT EXISTS `mr_extern` (
  `extern_id` int(11) NOT NULL AUTO_INCREMENT,
  `extern_firma` varchar(100) NOT NULL,
  `extern_disponierbar` tinyint(1) NOT NULL,
  `extern_ma` tinyint(1) NOT NULL DEFAULT 0,
  `extern_fp` tinyint(1) NOT NULL DEFAULT 0,
  `extern_wz` tinyint(1) NOT NULL DEFAULT 0,
  `extern_ansprechpartner` varchar(80) NOT NULL,
  `extern_strasse` varchar(80) NOT NULL,
  `extern_plz` varchar(10) NOT NULL,
  `extern_ort` varchar(80) NOT NULL,
  `extern_fon` varchar(30) NOT NULL,
  `extern_email` varchar(80) NOT NULL,
  `extern_bemerkung` text NOT NULL,
  `extern_created` datetime NOT NULL DEFAULT current_timestamp(),
  `extern_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`extern_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_fuhrpark
CREATE TABLE IF NOT EXISTS `mr_fuhrpark` (
  `fid` int(11) NOT NULL AUTO_INCREMENT,
  `extern_id` int(11) DEFAULT 0,
  `menge` int(11) NOT NULL DEFAULT 1,
  `leistungs_id` int(11) DEFAULT 0,
  `standort` varchar(40) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT 'Neuss',
  `kennzeichen` char(15) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `hersteller` char(50) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `modell` char(30) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `fahrzeugart` char(30) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `FKL` varchar(20) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `Erstzulassung` date DEFAULT NULL,
  `Anschaffung` date DEFAULT NULL,
  `NaechsteInspektion` date DEFAULT NULL,
  `Kmst` int(11) DEFAULT 0,
  `laderaum_laenge` float DEFAULT 0,
  `laderaum_breite` float DEFAULT 0,
  `laderaum_hoehe` float DEFAULT 0,
  `ladevolumen` int(11) DEFAULT 0,
  `nutzlast` int(11) DEFAULT 0,
  `kw` int(11) DEFAULT 0,
  `sitze` int(11) DEFAULT 0,
  `created_uid` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified_uid` int(11) DEFAULT 0,
  `modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB AUTO_INCREMENT=376 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_fuhrpark_categories
CREATE TABLE IF NOT EXISTS `mr_fuhrpark_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_fuhrpark_categories_lnk
CREATE TABLE IF NOT EXISTS `mr_fuhrpark_categories_lnk` (
  `fuhrpark_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`fuhrpark_id`,`category_id`),
  UNIQUE KEY `fuhrpark_id` (`fuhrpark_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_lager
CREATE TABLE IF NOT EXISTS `mr_lager` (
  `lager_id` int(11) NOT NULL AUTO_INCREMENT,
  `lager_name` varchar(50) NOT NULL,
  `strasse` varchar(50) DEFAULT NULL,
  `hausnr` varchar(15) DEFAULT NULL,
  `plz` int(10) DEFAULT NULL,
  `ort` varchar(50) NOT NULL,
  `land` varchar(50) DEFAULT NULL,
  `geo_lat` varchar(20) DEFAULT NULL,
  `geo_lng` varchar(20) DEFAULT NULL,
  `ordnungszahl` int(11) DEFAULT NULL,
  PRIMARY KEY (`lager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_lieferscheindruckkopf_dispofilter
CREATE TABLE IF NOT EXISTS `mr_lieferscheindruckkopf_dispofilter` (
  `Mandant` tinyint(4) NOT NULL,
  `Lieferscheinnummer` int(11) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `SelektionID` int(11) DEFAULT NULL,
  `Lieferscheindatum` datetime NOT NULL,
  `Auftragsart` tinyint(4) DEFAULT NULL,
  `Vorgangstitel` varchar(100) DEFAULT NULL,
  `Kundennummer` int(11) DEFAULT NULL,
  `Rechnungsanschriftkennung` tinyint(4) DEFAULT NULL,
  `RechnungName` varchar(255) DEFAULT NULL,
  `Rechnungsanschrift` int(11) DEFAULT NULL,
  `RechnungStrassePostfach` varchar(50) DEFAULT NULL,
  `RechnungPostleitzahl` varchar(10) DEFAULT NULL,
  `RechnungOrt` varchar(50) DEFAULT NULL,
  `RechnungLand` varchar(10) DEFAULT NULL,
  `Lieferanschrift` int(11) DEFAULT NULL,
  `LieferungStrassePostfach` varchar(50) DEFAULT NULL,
  `LieferungName` varchar(255) DEFAULT NULL,
  `LieferungOrt` varchar(50) DEFAULT NULL,
  `LieferungPostleitzahl` varchar(10) DEFAULT NULL,
  `LieferungLand` varchar(10) DEFAULT NULL,
  `Lieferwoche` tinyint(4) DEFAULT NULL,
  `Lieferjahr` tinyint(4) DEFAULT NULL,
  `Liefertermin` datetime DEFAULT NULL,
  `LieferterminFix` tinyint(4) NOT NULL,
  `Bestellnummer` varchar(255) DEFAULT NULL,
  `Bestellinfo` text DEFAULT NULL,
  `Kostenstelle` varchar(40) DEFAULT NULL,
  `Waehrung` varchar(5) NOT NULL,
  `Versandbedingung` varchar(255) DEFAULT NULL,
  `Lieferbedingung` varchar(255) DEFAULT NULL,
  `Handlieferschein` tinyint(4) NOT NULL,
  `Kopftext` text DEFAULT NULL,
  `KopftextFormat` tinyint(4) DEFAULT NULL,
  `Fusstext` text DEFAULT NULL,
  `FusstextFormat` tinyint(4) DEFAULT NULL,
  `Druckdatum` datetime DEFAULT NULL,
  `Nettobetrag` decimal(10,2) NOT NULL,
  `Bruttobetrag` decimal(10,2) NOT NULL,
  `MwstSatz` decimal(10,2) NOT NULL,
  `Bemerkung` text DEFAULT NULL,
  `Verkaufsteam` varchar(50) DEFAULT NULL,
  `Geschaeftsbereich` varchar(50) DEFAULT NULL,
  `AnzahlPakete` int(11) DEFAULT NULL,
  `AngelegtDurch` varchar(30) NOT NULL,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertDurch` varchar(30) DEFAULT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  PRIMARY KEY (`Mandant`,`Lieferscheinnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_lieferscheindruck_dispofilter
CREATE TABLE IF NOT EXISTS `mr_lieferscheindruck_dispofilter` (
  `Mandant` tinyint(4) NOT NULL,
  `Lieferscheinnummer` int(11) NOT NULL,
  `Auftragsnummer` bigint(20) NOT NULL,
  `Abschnittsnummer` tinyint(4) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  `StruPosnr` varchar(255) NOT NULL,
  `Rechnungsanschriftkennung` tinyint(4) DEFAULT NULL,
  `RechnungName` varchar(255) DEFAULT NULL,
  `Rechnungsanschrift` int(11) DEFAULT NULL,
  `RechnungStrassePostfach` varchar(50) DEFAULT NULL,
  `RechnungPostleitzahl` varchar(10) DEFAULT NULL,
  `RechnungOrt` varchar(50) DEFAULT NULL,
  `RechnungLand` varchar(10) DEFAULT NULL,
  `Positionsart` tinyint(4) DEFAULT NULL,
  `Artikelnummer` varchar(30) DEFAULT NULL,
  `Bestellmenge` decimal(10,2) DEFAULT NULL,
  `Liefermenge` decimal(19,4) DEFAULT NULL,
  `Bezeichnung` varchar(255) DEFAULT NULL,
  `Bestellmengeneinheit` varchar(20) DEFAULT NULL,
  `Nachkommastellen` tinyint(4) DEFAULT NULL,
  `Preis` decimal(10,2) DEFAULT NULL,
  `Rabatt` decimal(10,2) DEFAULT NULL,
  `Positionstext` text DEFAULT NULL,
  `Kostenstelle` varchar(40) DEFAULT NULL,
  `KundenArtikelnummer` varchar(20) DEFAULT NULL,
  `KundenArtikelbezeichnung` varchar(255) DEFAULT NULL,
  `Seriennummern` text DEFAULT NULL,
  `BezugsRechnungsnummer` int(11) DEFAULT NULL,
  `RestMenge` float DEFAULT NULL,
  `Druckdatum` datetime DEFAULT NULL,
  `LieferwochePos` smallint(6) DEFAULT NULL,
  `LieferjahrPos` smallint(6) DEFAULT NULL,
  `LieferterminPos` datetime DEFAULT NULL,
  `LieferterminFixPos` tinyint(4) NOT NULL,
  `Langtext` text DEFAULT NULL,
  `Gewicht` decimal(10,2) DEFAULT NULL,
  `Volumen` decimal(10,2) DEFAULT NULL,
  `AufmassText` text DEFAULT NULL,
  `Vertragsleistung` tinyint(4) NOT NULL,
  `Kulanz` tinyint(4) NOT NULL,
  `Garantie` tinyint(4) NOT NULL,
  `Austausch` tinyint(4) NOT NULL,
  `Reklamation` tinyint(4) NOT NULL,
  `StruPosnrAnz` varchar(50) NOT NULL,
  `SummeZuschlaege` decimal(10,2) DEFAULT NULL,
  `Bestellnummer` varchar(255) DEFAULT NULL,
  `ArtikelBestellnummer` varchar(500) DEFAULT NULL,
  `Lagerkennung` varchar(15) DEFAULT NULL,
  `Stellplatz` varchar(50) DEFAULT NULL,
  `IstInternepos` tinyint(4) NOT NULL,
  `KundenBestellposition` varchar(50) DEFAULT NULL,
  `AngelegtDurch` varchar(30) NOT NULL,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertDurch` varchar(30) DEFAULT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  PRIMARY KEY (`Mandant`,`Lieferscheinnummer`,`Auftragsnummer`,`Abschnittsnummer`,`Positionsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_mitarbeiter
CREATE TABLE IF NOT EXISTS `mr_mitarbeiter` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `extern_id` int(11) DEFAULT 0,
  `menge` int(11) NOT NULL DEFAULT 1,
  `leistungs_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `team_id` int(11) NOT NULL DEFAULT 0,
  `standort` varchar(40) COLLATE latin1_german1_ci NOT NULL,
  `anrede` enum('Frau','Herr') COLLATE latin1_german1_ci NOT NULL,
  `titel` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  `name` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `vorname` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `email` varchar(80) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `abteilung` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `eingestellt_als` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `fuehrerscheinklassen` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `urlaubsanspruch` float DEFAULT 0,
  `created_uid` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified_uid` int(11) NOT NULL DEFAULT 0,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`mid`),
  KEY `Schlüssel 2` (`name`,`vorname`)
) ENGINE=InnoDB AUTO_INCREMENT=464 DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_mitarbeiter_categories
CREATE TABLE IF NOT EXISTS `mr_mitarbeiter_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_mitarbeiter_categories_lnk
CREATE TABLE IF NOT EXISTS `mr_mitarbeiter_categories_lnk` (
  `mitarbeiter_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`mitarbeiter_id`,`category_id`),
  KEY `mr_mitarbeiter_categories_lnk_ibfk_2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_ressourcen_dispozeiten
CREATE TABLE IF NOT EXISTS `mr_ressourcen_dispozeiten` (
  `dispozeiten_id` int(11) NOT NULL AUTO_INCREMENT,
  `ressourcen_id` int(11) NOT NULL,
  `ressourcen_typ` enum('MA','FP','WZ') NOT NULL,
  `gebucht_von` date NOT NULL,
  `gebucht_bis` date NOT NULL,
  `gebucht_zeit_von` time DEFAULT NULL,
  `gebucht_zeit_bis` time DEFAULT NULL,
  `kosten` float DEFAULT NULL,
  `bemerkung` text DEFAULT NULL,
  PRIMARY KEY (`dispozeiten_id`),
  KEY `ressourcen_id` (`ressourcen_id`,`ressourcen_typ`),
  KEY `gebucht_von` (`gebucht_von`),
  KEY `gebucht_bis` (`gebucht_bis`)
) ENGINE=InnoDB AUTO_INCREMENT=1376 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_ressourcen_leistungskatalog
CREATE TABLE IF NOT EXISTS `mr_ressourcen_leistungskatalog` (
  `leistungs_id` int(11) NOT NULL AUTO_INCREMENT,
  `ressourcen_typ` char(2) NOT NULL,
  `leistungs_name` varchar(80) NOT NULL,
  `kosten_pro_einheit` float NOT NULL,
  PRIMARY KEY (`leistungs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_ressourcen_sperrzeiten
CREATE TABLE IF NOT EXISTS `mr_ressourcen_sperrzeiten` (
  `sperrzeiten_id` int(11) NOT NULL AUTO_INCREMENT,
  `ressourcen_typ` enum('FP','MA','WZ') NOT NULL,
  `ressourcen_id` int(11) NOT NULL,
  `gesperrt_von` date NOT NULL,
  `gesperrt_bis` date NOT NULL,
  `bemerkung` varchar(60) DEFAULT NULL,
  `anzahl_entfernt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sperrzeiten_id`),
  KEY `ressourcen_typ` (`ressourcen_typ`,`ressourcen_id`),
  KEY `gesperrt_von` (`gesperrt_von`),
  KEY `gesperrt_bis` (`gesperrt_bis`)
) ENGINE=InnoDB AUTO_INCREMENT=6823 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_teams
CREATE TABLE IF NOT EXISTS `mr_teams` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `team` char(50) NOT NULL,
  KEY `team_id` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_aktivitaet
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_aktivitaet` (
  `tour_id` int(11) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `portlet_id` int(11) NOT NULL,
  `lager_id` int(11) NOT NULL,
  `DatumVon` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `zugriffszeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `aktion` varchar(20) DEFAULT NULL,
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  KEY `tour_id` (`tour_id`,`timeline_id`,`portlet_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_attachments
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_attachments` (
  `dokid` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `oeffentlich` enum('Ja','Nein') COLLATE latin1_german1_ci NOT NULL,
  `typ` enum('Datei','Text','Url') COLLATE latin1_german1_ci NOT NULL,
  `titel` char(100) COLLATE latin1_german1_ci NOT NULL,
  `dok_datei` char(250) COLLATE latin1_german1_ci DEFAULT NULL,
  `dok_text` text COLLATE latin1_german1_ci DEFAULT NULL,
  `dok_url` char(250) COLLATE latin1_german1_ci DEFAULT NULL,
  `dok_groesse` int(11) DEFAULT NULL,
  `dok_type` char(30) COLLATE latin1_german1_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` int(5) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modifiedby` int(55) DEFAULT NULL,
  PRIMARY KEY (`dokid`)
) ENGINE=MyISAM AUTO_INCREMENT=525 DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_auftraege
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_auftraege` (
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  `auftrag_disponiert_user` char(30) DEFAULT NULL,
  `auftrag_disponiert_am` datetime DEFAULT NULL,
  `auftrag_abgeschlossen_user` char(30) DEFAULT NULL,
  `auftrag_abgeschlossen_am` datetime DEFAULT NULL,
  `auftrag_wiedervorlage_am` date DEFAULT NULL,
  `auftrag_abschluss_summe` float(9,2) DEFAULT NULL,
  `auftrag_abschluss_prozent` float(5,2) DEFAULT NULL,
  `tour_dispo_count` int(11) NOT NULL DEFAULT 0,
  `tour_abschluss_count` int(11) NOT NULL DEFAULT 0,
  `tour_neulieferungen_count` int(11) NOT NULL DEFAULT 0,
  `wws_last_geaendertam` datetime NOT NULL,
  PRIMARY KEY (`Mandant`,`Auftragsnummer`),
  KEY `Schlüssel 2` (`auftrag_abgeschlossen_am`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_auftraege_backup_201805231450
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_auftraege_backup_201805231450` (
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  `auftrag_disponiert_user` char(30) DEFAULT NULL,
  `auftrag_disponiert_am` datetime DEFAULT NULL,
  `auftrag_abgeschlossen_user` char(30) DEFAULT NULL,
  `auftrag_abgeschlossen_am` datetime DEFAULT NULL,
  `auftrag_wiedervorlage_am` date DEFAULT NULL,
  `auftrag_abschluss_summe` float(9,2) DEFAULT NULL,
  `auftrag_abschluss_prozent` float(5,2) DEFAULT NULL,
  `tour_dispo_count` int(11) NOT NULL DEFAULT 0,
  `tour_abschluss_count` int(11) NOT NULL DEFAULT 0,
  `tour_neulieferungen_count` int(11) NOT NULL DEFAULT 0,
  `wws_last_geaendertam` datetime NOT NULL,
  PRIMARY KEY (`Mandant`,`Auftragsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_auftraege_backup_201806040025
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_auftraege_backup_201806040025` (
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  `auftrag_disponiert_user` char(30) DEFAULT NULL,
  `auftrag_disponiert_am` datetime DEFAULT NULL,
  `auftrag_abgeschlossen_user` char(30) DEFAULT NULL,
  `auftrag_abgeschlossen_am` datetime DEFAULT NULL,
  `auftrag_wiedervorlage_am` date DEFAULT NULL,
  `auftrag_abschluss_summe` float(9,2) DEFAULT NULL,
  `auftrag_abschluss_prozent` float(5,2) DEFAULT NULL,
  `tour_dispo_count` int(11) NOT NULL DEFAULT 0,
  `tour_abschluss_count` int(11) NOT NULL DEFAULT 0,
  `tour_neulieferungen_count` int(11) NOT NULL DEFAULT 0,
  `wws_last_geaendertam` datetime NOT NULL,
  PRIMARY KEY (`Mandant`,`Auftragsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_auftraege_clean_neu
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_auftraege_clean_neu` (
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  `auftrag_disponiert_user` char(30) DEFAULT NULL,
  `auftrag_disponiert_am` datetime DEFAULT NULL,
  `auftrag_abgeschlossen_user` char(30) DEFAULT NULL,
  `auftrag_abgeschlossen_am` datetime DEFAULT NULL,
  `auftrag_wiedervorlage_am` date DEFAULT NULL,
  `auftrag_abschluss_summe` float(9,2) DEFAULT NULL,
  `auftrag_abschluss_prozent` float(5,2) DEFAULT NULL,
  `tour_dispo_count` int(11) NOT NULL DEFAULT 0,
  `tour_abschluss_count` int(11) NOT NULL DEFAULT 0,
  `tour_neulieferungen_count` int(11) NOT NULL DEFAULT 0,
  `wws_last_geaendertam` datetime NOT NULL,
  PRIMARY KEY (`Mandant`,`Auftragsnummer`),
  KEY `Schlüssel 2` (`auftrag_abgeschlossen_am`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_auftragspositionen
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_auftragspositionen` (
  `tour_id` int(11) NOT NULL,
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  `DisponierteMenge` int(11) NOT NULL,
  `AbschlussMenge` int(11) NOT NULL DEFAULT 0,
  `AbschlussReklaMenge` int(11) NOT NULL DEFAULT 0,
  `AbschlussReklaGrund` enum('','Kunde','Mertens') NOT NULL DEFAULT '',
  `AbschlussNLMenge` int(11) NOT NULL DEFAULT 0,
  `AbschlussNLGrund` enum('','Kunde','Mertens') NOT NULL DEFAULT '',
  `Stellplatz` char(30) DEFAULT NULL,
  PRIMARY KEY (`tour_id`,`Mandant`,`Auftragsnummer`,`Positionsnummer`),
  KEY `Mandant` (`Mandant`),
  KEY `Auftragsnummer` (`Auftragsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_auftragspositionen_txt
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_auftragspositionen_txt` (
  `tour_id` int(11) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  `AbschlussBemerkung` text NOT NULL,
  `StellplatzHistorie` text DEFAULT NULL,
  PRIMARY KEY (`tour_id`,`Positionsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_fuhrpark
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_fuhrpark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fuhrpark_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `einsatzdauer` time DEFAULT NULL,
  `km` int(11) DEFAULT NULL,
  `kosten` float(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fuhrpark_tour` (`fuhrpark_id`,`tour_id`),
  KEY `tour_id` (`tour_id`),
  KEY `fuhrpark_id` (`fuhrpark_id`)
) ENGINE=InnoDB AUTO_INCREMENT=263059 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_log
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lager_id` int(11) NOT NULL DEFAULT 0,
  `portlet_id` int(11) NOT NULL DEFAULT 0,
  `timeline_id` int(11) NOT NULL DEFAULT 0,
  `tour_id` int(11) NOT NULL DEFAULT 0,
  `object_type` char(4) NOT NULL,
  `object_id` int(11) NOT NULL,
  `action` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `sperrzeiten_id` int(11) DEFAULT NULL,
  `tour_anr` int(11) DEFAULT NULL,
  `dispo_datum` datetime DEFAULT NULL,
  `dispo_zeit_von` time DEFAULT NULL,
  `dispo_zeit_bis` time DEFAULT NULL,
  `bemerkung` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_time` (`action_time`),
  KEY `tour_id` (`tour_id`),
  KEY `sperrzeiten_id` (`sperrzeiten_id`),
  KEY `dispo_datum` (`dispo_datum`)
) ENGINE=InnoDB AUTO_INCREMENT=1617401 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_mitarbeiter
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_mitarbeiter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mitarbeiter_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `einsatzdauer` time DEFAULT NULL,
  `kosten` float(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mitarbeiter_tour` (`mitarbeiter_id`,`tour_id`),
  KEY `tour_id` (`tour_id`),
  KEY `mitarbeiter_id` (`mitarbeiter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=688285 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_mitarbeiter_txt
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_mitarbeiter_txt` (
  `id` int(11) NOT NULL,
  `einsatz_ab` char(40) NOT NULL,
  `bemerkung` text NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_vorgaenge
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_vorgaenge` (
  `tour_id` int(11) NOT NULL AUTO_INCREMENT,
  `Mandant` int(11) NOT NULL DEFAULT 0,
  `Auftragsnummer` int(11) NOT NULL DEFAULT 0,
  `timeline_id` int(11) NOT NULL,
  `DatumVon` date NOT NULL,
  `ZeitVon` time NOT NULL,
  `DatumBis` date NOT NULL,
  `ZeitBis` time NOT NULL,
  `IsDefault` tinyint(1) NOT NULL DEFAULT 0,
  `info` text NOT NULL DEFAULT '',
  `info_link` text NOT NULL DEFAULT '',
  `count_actions` int(11) NOT NULL DEFAULT 0,
  `tour_disponiert_am` datetime DEFAULT NULL,
  `tour_disponiert_user` varchar(30) DEFAULT NULL,
  `zeiten_erfasst_am` datetime DEFAULT NULL,
  `zeiten_erfasst_user` char(30) DEFAULT NULL,
  `tour_abgeschlossen_am` datetime DEFAULT NULL,
  `tour_abgeschlossen_user` char(30) DEFAULT NULL,
  `neulieferung` tinyint(1) NOT NULL DEFAULT 0,
  `avisiert` tinyint(1) NOT NULL DEFAULT 0,
  `avisiertZeitgenau` tinyint(1) NOT NULL DEFAULT 0,
  `avisiertDatum` date DEFAULT NULL,
  `avisiertZeitVon` time DEFAULT NULL,
  `avisiertZeitBis` time DEFAULT NULL,
  `attachments` tinyint(4) NOT NULL DEFAULT 0,
  `farbklasse` varchar(50) NOT NULL DEFAULT '',
  `created_uid` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified_uid` int(11) NOT NULL DEFAULT 0,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`tour_id`),
  KEY `timeline_id` (`timeline_id`),
  KEY `Mandant` (`Mandant`,`Auftragsnummer`),
  KEY `DatumVon` (`DatumVon`),
  KEY `DatumBis` (`DatumBis`),
  KEY `ZeitVon` (`ZeitVon`),
  KEY `ZeitBis` (`ZeitBis`)
) ENGINE=InnoDB AUTO_INCREMENT=223687 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_vorgaenge_neu
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_vorgaenge_neu` (
  `tour_id` int(11) NOT NULL AUTO_INCREMENT,
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `DatumVon` date NOT NULL,
  `ZeitVon` time NOT NULL,
  `DatumBis` date NOT NULL,
  `ZeitBis` time NOT NULL,
  `IsDefault` tinyint(1) NOT NULL DEFAULT 0,
  `count_actions` int(11) NOT NULL,
  `tour_disponiert_am` datetime DEFAULT NULL,
  `tour_disponiert_user` varchar(30) DEFAULT NULL,
  `zeiten_erfasst_am` datetime DEFAULT NULL,
  `zeiten_erfasst_user` char(30) DEFAULT NULL,
  `tour_abgeschlossen_am` datetime DEFAULT NULL,
  `tour_abgeschlossen_user` char(30) DEFAULT NULL,
  `neulieferung` tinyint(1) NOT NULL DEFAULT 0,
  `avisiert` tinyint(1) NOT NULL DEFAULT 0,
  `attachments` tinyint(4) NOT NULL DEFAULT 0,
  `created_uid` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified_uid` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`tour_id`),
  KEY `timeline_id` (`timeline_id`),
  KEY `Mandant` (`Mandant`,`Auftragsnummer`),
  KEY `DatumVon` (`DatumVon`),
  KEY `DatumBis` (`DatumBis`),
  KEY `ZeitVon` (`ZeitVon`),
  KEY `ZeitBis` (`ZeitBis`)
) ENGINE=InnoDB AUTO_INCREMENT=106200 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_vorgaenge_txt
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_vorgaenge_txt` (
  `tour_id` int(11) NOT NULL,
  `bemerkung` text NOT NULL,
  `bemerkung_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_dispo_werkzeug
CREATE TABLE IF NOT EXISTS `mr_touren_dispo_werkzeug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `werkzeug_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `einsatzdauer` time DEFAULT NULL,
  `kosten` float(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `werkzeug_tour` (`werkzeug_id`,`tour_id`),
  KEY `werkzeug_id` (`werkzeug_id`),
  KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_portlets
CREATE TABLE IF NOT EXISTS `mr_touren_portlets` (
  `portlet_id` int(11) NOT NULL AUTO_INCREMENT,
  `lager_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `tagesnr` int(11) NOT NULL,
  `topcustom` varchar(60) DEFAULT NULL,
  `title` char(250) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL,
  `modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`portlet_id`),
  KEY `datum` (`datum`),
  KEY `lager_id` (`lager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69120 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_touren_timelines
CREATE TABLE IF NOT EXISTS `mr_touren_timelines` (
  `timeline_id` int(11) NOT NULL AUTO_INCREMENT,
  `portlet_id` int(11) NOT NULL,
  `group_key` varchar(20) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `interval` time NOT NULL DEFAULT '00:30:00',
  `title` char(250) DEFAULT NULL,
  `locked_uid` int(11) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`timeline_id`),
  KEY `portlet_id` (`portlet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=70962 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_user
CREATE TABLE IF NOT EXISTS `mr_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` char(30) NOT NULL,
  `ldap_user` char(30) DEFAULT NULL,
  `user_pw` char(50) NOT NULL,
  `user_role` char(30) DEFAULT NULL,
  `email` char(100) DEFAULT NULL,
  `vorname` char(50) DEFAULT NULL,
  `nachname` char(60) DEFAULT NULL,
  `login_status` enum('init','logged-in','logged-out') NOT NULL DEFAULT 'init',
  `login_counter` int(11) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `freigegeben` enum('init','Ja','Nein') DEFAULT 'Ja',
  `deleted` tinyint(4) DEFAULT 0,
  `created` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=339 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_user_log
CREATE TABLE IF NOT EXISTS `mr_user_log` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `phpsessid` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `ip` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
  `user_agent` varchar(150) COLLATE utf8_german2_ci DEFAULT NULL,
  `user_agent_details` varchar(500) COLLATE utf8_german2_ci DEFAULT NULL,
  `login_date` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_date` datetime DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22599 DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_user_profile
CREATE TABLE IF NOT EXISTS `mr_user_profile` (
  `user_id` int(11) NOT NULL,
  `standorte` varchar(100) DEFAULT NULL,
  `profile_json` text NOT NULL DEFAULT '',
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_variables
CREATE TABLE IF NOT EXISTS `mr_variables` (
  `name` varchar(80) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_warenbewegungen_dispofilter_archiv_20180611
CREATE TABLE IF NOT EXISTS `mr_warenbewegungen_dispofilter_archiv_20180611` (
  `Mandant` tinyint(4) NOT NULL,
  `LaufendeNummer` int(11) NOT NULL,
  `Bewegungsart` tinyint(4) NOT NULL,
  `Lagerkennung` varchar(15) DEFAULT NULL,
  `Stellplatz` varchar(50) DEFAULT NULL,
  `Artikelnummer` varchar(30) NOT NULL,
  `Menge` decimal(10,2) NOT NULL,
  `MengenEinheit` varchar(20) DEFAULT NULL,
  `Preis` decimal(10,2) DEFAULT NULL,
  `Auftragsnummer` bigint(20) DEFAULT NULL,
  `Positionsnummer` int(11) DEFAULT NULL,
  `Bestellnummer` int(11) DEFAULT NULL,
  `Bestellposition` int(11) DEFAULT NULL,
  `AngelegtAm` datetime NOT NULL,
  `GeaendertAm` datetime DEFAULT NULL,
  PRIMARY KEY (`Mandant`,`LaufendeNummer`),
  KEY `Auftragsnummer` (`Auftragsnummer`),
  KEY `Positionsnummer` (`Positionsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_werkzeug
CREATE TABLE IF NOT EXISTS `mr_werkzeug` (
  `wid` int(11) NOT NULL AUTO_INCREMENT,
  `extern_id` int(11) DEFAULT NULL,
  `menge` int(11) NOT NULL DEFAULT 1,
  `leistungs_id` int(11) DEFAULT NULL,
  `standort` varchar(20) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `bezeichnung` varchar(60) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `erforderliche_qualifikation` varchar(60) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `created_uid` int(11) NOT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified_uid` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`wid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_werkzeug_categories
CREATE TABLE IF NOT EXISTS `mr_werkzeug_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_werkzeug_categories_lnk
CREATE TABLE IF NOT EXISTS `mr_werkzeug_categories_lnk` (
  `werkzeug_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`werkzeug_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_wws_ak_keys
CREATE TABLE IF NOT EXISTS `mr_wws_ak_keys` (
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  KEY `Mandant` (`Mandant`,`Auftragsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_wws_ap_keys
CREATE TABLE IF NOT EXISTS `mr_wws_ap_keys` (
  `Mandant` int(11) NOT NULL,
  `Auftragsnummer` int(11) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  KEY `Mandant` (`Mandant`,`Auftragsnummer`,`Positionsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_wws_bk_keys
CREATE TABLE IF NOT EXISTS `mr_wws_bk_keys` (
  `Mandant` int(11) NOT NULL,
  `Bestellnummer` int(11) NOT NULL,
  KEY `Mandant` (`Mandant`,`Bestellnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_wws_bp_keys
CREATE TABLE IF NOT EXISTS `mr_wws_bp_keys` (
  `Mandant` int(11) NOT NULL,
  `Bestellnummer` int(11) NOT NULL,
  `Positionsnummer` int(11) NOT NULL,
  KEY `Mandant` (`Mandant`,`Bestellnummer`,`Positionsnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle mt_rm.mr_wws_wb_keys
CREATE TABLE IF NOT EXISTS `mr_wws_wb_keys` (
  `Mandant` tinyint(4) NOT NULL,
  `LaufendeNummer` int(11) NOT NULL,
  PRIMARY KEY (`Mandant`,`LaufendeNummer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Funktion mt_rm.replaceEncoding
DELIMITER //
CREATE FUNCTION `replaceEncoding`(
	`srcString` text
) RETURNS text CHARSET latin1
    DETERMINISTIC
BEGIN
    DECLARE re text;

 	 SET re = srcString;

 	 SET re = Replace(re, 'Ã„', 'Ä');
 	 SET re = Replace(re, 'Ã–', 'Ö');
 	 SET re = Replace(re, 'Ãœ', 'Ü');
 	 SET re = Replace(re, 'Ã¤', 'ä');
 	 SET re = Replace(re, 'Ã¶', 'ö');
 	 SET re = Replace(re, 'Ã¼', 'ü');
 	 SET re = Replace(re, 'ÃŸ', 'ß');
 	 SET re = Replace(re, 'Ã‰', 'É');


 RETURN (re);
END//
DELIMITER ;

-- Exportiere Struktur von Tabelle mt_rm.test
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von View mt_rm.view_verbrauch
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `view_verbrauch` (
	`tour_id` INT(11) NOT NULL,
	`Mandant` INT(11) NOT NULL,
	`Auftragsnummer` INT(11) NOT NULL,
	`lager_name` VARCHAR(50) NULL COLLATE 'latin1_swedish_ci',
	`datum` DATETIME NULL,
	`typ` VARCHAR(15) NOT NULL COLLATE 'utf8_general_ci',
	`name` VARCHAR(154) NULL COLLATE 'latin1_german1_ci',
	`verbrauch` VARCHAR(11) NULL COLLATE 'utf8mb4_general_ci',
	`einheit` VARCHAR(3) NULL COLLATE 'utf8_general_ci',
	`kosten` DOUBLE(23,6) NULL,
	`resource_id` INT(11) NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View mt_rm.view_verbrauch_20141110
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `view_verbrauch_20141110` (
	`tour_id` INT(11) NOT NULL,
	`Mandant` INT(11) NOT NULL,
	`Auftragsnummer` INT(11) NOT NULL,
	`lager_name` VARCHAR(50) NULL COLLATE 'latin1_swedish_ci',
	`datum` DATE NULL,
	`typ` VARCHAR(11) NOT NULL COLLATE 'utf8_general_ci',
	`name` VARCHAR(154) NULL COLLATE 'latin1_german1_ci',
	`verbrauch` VARCHAR(11) NULL COLLATE 'utf8mb4_general_ci',
	`einheit` VARCHAR(3) NOT NULL COLLATE 'utf8_general_ci',
	`kosten` FLOAT(11,2) NULL,
	`resource_id` INT(11) NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View mt_rm.view_verbrauch_alt
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `view_verbrauch_alt` (
	`tour_id` INT(11) NOT NULL,
	`Mandant` INT(11) NOT NULL,
	`Auftragsnummer` INT(11) NOT NULL,
	`lager_name` VARCHAR(50) NULL COLLATE 'latin1_swedish_ci',
	`datum` DATE NULL,
	`typ` VARCHAR(11) NOT NULL COLLATE 'utf8_general_ci',
	`name` VARCHAR(154) NULL COLLATE 'latin1_german1_ci',
	`verbrauch` VARCHAR(11) NULL COLLATE 'utf8mb4_general_ci',
	`einheit` VARCHAR(3) NOT NULL COLLATE 'utf8_general_ci',
	`kosten` FLOAT(11,2) NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View mt_rm.view_verbrauch_alt_20140630
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `view_verbrauch_alt_20140630` (
	`tour_id` INT(11) NOT NULL,
	`Mandant` INT(11) NOT NULL,
	`Auftragsnummer` INT(11) NOT NULL,
	`lager_name` VARCHAR(50) NULL COLLATE 'latin1_swedish_ci',
	`datum` DATE NULL,
	`typ` VARCHAR(11) NOT NULL COLLATE 'utf8_general_ci',
	`name` VARCHAR(154) NULL COLLATE 'latin1_german1_ci',
	`verbrauch` DECIMAL(19,2) NULL,
	`einheit` VARCHAR(3) NOT NULL COLLATE 'utf8_general_ci',
	`kosten` FLOAT(11,2) NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View mt_rm.view_verbrauch_test4
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `view_verbrauch_test4` (
	`tour_id` INT(11) NOT NULL,
	`Mandant` INT(11) NOT NULL,
	`Auftragsnummer` INT(11) NOT NULL,
	`lager_name` VARCHAR(50) NULL COLLATE 'latin1_swedish_ci',
	`datum` DATE NULL,
	`typ` VARCHAR(11) NOT NULL COLLATE 'utf8_general_ci',
	`resource_id` INT(11) NULL,
	`name` VARCHAR(154) NULL COLLATE 'latin1_german1_ci',
	`verbrauch` VARCHAR(11) NULL COLLATE 'utf8mb4_general_ci',
	`einheit` VARCHAR(3) NOT NULL COLLATE 'utf8_general_ci',
	`kosten` FLOAT(11,2) NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View mt_rm.view_verbrauch
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `view_verbrauch`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_verbrauch` AS select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Fuhrpark' AS `typ`,concat(ifnull(`f`.`kennzeichen`,''),' ',ifnull(`f`.`fahrzeugart`,'')) AS `name`,`df`.`km` AS `verbrauch`,'km' AS `einheit`,`df`.`kosten` AS `kosten`,`df`.`fuhrpark_id` AS `resource_id` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_fuhrpark` `df` on(`t`.`tour_id` = `df`.`tour_id`)) left join `mr_fuhrpark` `f` on(`df`.`fuhrpark_id` = `f`.`fid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `df`.`fuhrpark_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Mitarbeiter' AS `typ`,concat(ifnull(`m`.`vorname`,''),' ',ifnull(`m`.`name`,''),' [',ifnull(`m`.`eingestellt_als`,''),']') AS `name`,`dm`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dm`.`kosten` AS `kosten`,`dm`.`mitarbeiter_id` AS `resource_id` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_mitarbeiter` `dm` on(`t`.`tour_id` = `dm`.`tour_id`)) left join `mr_mitarbeiter` `m` on(`dm`.`mitarbeiter_id` = `m`.`mid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dm`.`mitarbeiter_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Werkzeug' AS `typ`,`w`.`bezeichnung` AS `name`,`dw`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dw`.`kosten` AS `kosten`,`dw`.`werkzeug_id` AS `resource_id` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_werkzeug` `dw` on(`t`.`tour_id` = `dw`.`tour_id`)) left join `mr_werkzeug` `w` on(`dw`.`werkzeug_id` = `w`.`wid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dw`.`werkzeug_id` is not null union select 0 AS `tour_id`,`a`.`Mandant` AS `Mandant`,`a`.`Auftragsnummer` AS `Auftragsnummer`,'' AS `lager_name`,`a`.`auftrag_abgeschlossen_am` AS `datum`,'Direktabschluss' AS `typ`,concat('AW: ',ifnull(`ak`.`Auftragswert`,'NULL')) AS `name`,if(`a`.`auftrag_abschluss_summe` is not null and `a`.`auftrag_abschluss_summe` > 0,`a`.`auftrag_abschluss_summe`,`a`.`auftrag_abschluss_prozent`) AS `verbrauch`,if(`a`.`auftrag_abschluss_summe` is not null and `a`.`auftrag_abschluss_summe` > 0,'EUR','%') AS `einheit`,case when (`a`.`auftrag_abschluss_summe` is not null and `a`.`auftrag_abschluss_summe` > 1) then `a`.`auftrag_abschluss_summe` when (`a`.`auftrag_abschluss_prozent` is not null and `a`.`auftrag_abschluss_prozent` > 1) then `a`.`auftrag_abschluss_prozent` * `ak`.`Auftragswert` / 100 else 0.00 end AS `kosten`,0 AS `resource_id` from (`mr_touren_dispo_auftraege` `a` left join `mr_auftragskoepfe_dispofilter` `ak` on(`a`.`Mandant` = `ak`.`Mandant` and `a`.`Auftragsnummer` = `ak`.`Auftragsnummer`)) where `a`.`auftrag_abgeschlossen_am` is not null and (`a`.`auftrag_abschluss_summe` > 1 or `a`.`auftrag_abschluss_prozent` > 1);

-- Exportiere Struktur von View mt_rm.view_verbrauch_20141110
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `view_verbrauch_20141110`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_verbrauch_20141110` AS select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Fuhrpark' AS `typ`,concat(ifnull(`f`.`kennzeichen`,''),' ',ifnull(`f`.`fahrzeugart`,'')) AS `name`,`df`.`km` AS `verbrauch`,'km' AS `einheit`,`df`.`kosten` AS `kosten`,`df`.`fuhrpark_id` AS `resource_id` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_fuhrpark` `df` on(`t`.`tour_id` = `df`.`tour_id`)) left join `mr_fuhrpark` `f` on(`df`.`fuhrpark_id` = `f`.`fid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `df`.`fuhrpark_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Mitarbeiter' AS `typ`,concat(ifnull(`m`.`vorname`,''),' ',ifnull(`m`.`name`,''),' [',ifnull(`m`.`eingestellt_als`,''),']') AS `name`,`dm`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dm`.`kosten` AS `kosten`,`dm`.`mitarbeiter_id` AS `resource_id` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_mitarbeiter` `dm` on(`t`.`tour_id` = `dm`.`tour_id`)) left join `mr_mitarbeiter` `m` on(`dm`.`mitarbeiter_id` = `m`.`mid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dm`.`mitarbeiter_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Werkzeug' AS `typ`,`w`.`bezeichnung` AS `name`,`dw`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dw`.`kosten` AS `kosten`,`dw`.`werkzeug_id` AS `resource_id` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_werkzeug` `dw` on(`t`.`tour_id` = `dw`.`tour_id`)) left join `mr_werkzeug` `w` on(`dw`.`werkzeug_id` = `w`.`wid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dw`.`werkzeug_id` is not null;

-- Exportiere Struktur von View mt_rm.view_verbrauch_alt
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `view_verbrauch_alt`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_verbrauch_alt` AS select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Fuhrpark' AS `typ`,concat(ifnull(`f`.`kennzeichen`,''),' ',ifnull(`f`.`fahrzeugart`,'')) AS `name`,`df`.`km` AS `verbrauch`,'km' AS `einheit`,`df`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_fuhrpark` `df` on(`t`.`tour_id` = `df`.`tour_id`)) left join `mr_fuhrpark` `f` on(`df`.`fuhrpark_id` = `f`.`fid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `df`.`fuhrpark_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Mitarbeiter' AS `typ`,concat(ifnull(`m`.`vorname`,''),' ',ifnull(`m`.`name`,''),' [',ifnull(`m`.`eingestellt_als`,''),']') AS `name`,`dm`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dm`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_mitarbeiter` `dm` on(`t`.`tour_id` = `dm`.`tour_id`)) left join `mr_mitarbeiter` `m` on(`dm`.`mitarbeiter_id` = `m`.`mid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dm`.`mitarbeiter_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Werkzeug' AS `typ`,`w`.`bezeichnung` AS `name`,`dw`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dw`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_werkzeug` `dw` on(`t`.`tour_id` = `dw`.`tour_id`)) left join `mr_werkzeug` `w` on(`dw`.`werkzeug_id` = `w`.`wid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dw`.`werkzeug_id` is not null;

-- Exportiere Struktur von View mt_rm.view_verbrauch_alt_20140630
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `view_verbrauch_alt_20140630`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_verbrauch_alt_20140630` AS select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Fuhrpark' AS `typ`,concat(ifnull(`f`.`kennzeichen`,''),' ',ifnull(`f`.`fahrzeugart`,'')) AS `name`,`df`.`km` AS `verbrauch`,'km' AS `einheit`,`df`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_fuhrpark` `df` on(`t`.`tour_id` = `df`.`tour_id`)) left join `mr_fuhrpark` `f` on(`df`.`fuhrpark_id` = `f`.`fid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `df`.`fuhrpark_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Mitarbeiter' AS `typ`,concat(ifnull(`m`.`vorname`,''),' ',ifnull(`m`.`name`,''),' [',ifnull(`m`.`eingestellt_als`,''),']') AS `name`,round(time_to_sec(`dm`.`einsatzdauer`) / 3600,2) AS `verbrauch`,'std' AS `einheit`,`dm`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_mitarbeiter` `dm` on(`t`.`tour_id` = `dm`.`tour_id`)) left join `mr_mitarbeiter` `m` on(`dm`.`mitarbeiter_id` = `m`.`mid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dm`.`mitarbeiter_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Werkzeug' AS `typ`,`w`.`bezeichnung` AS `name`,round(time_to_sec(`dw`.`einsatzdauer`) / 3600,2) AS `verbrauch`,'std' AS `einheit`,`dw`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_werkzeug` `dw` on(`t`.`tour_id` = `dw`.`tour_id`)) left join `mr_werkzeug` `w` on(`dw`.`werkzeug_id` = `w`.`wid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dw`.`werkzeug_id` is not null;

-- Exportiere Struktur von View mt_rm.view_verbrauch_test4
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `view_verbrauch_test4`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_verbrauch_test4` AS select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Fuhrpark' AS `typ`,`df`.`fuhrpark_id` AS `resource_id`,concat(ifnull(`f`.`kennzeichen`,''),' ',ifnull(`f`.`fahrzeugart`,'')) AS `name`,`df`.`km` AS `verbrauch`,'km' AS `einheit`,`df`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_fuhrpark` `df` on(`t`.`tour_id` = `df`.`tour_id`)) left join `mr_fuhrpark` `f` on(`df`.`fuhrpark_id` = `f`.`fid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `df`.`fuhrpark_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Mitarbeiter' AS `typ`,`dm`.`mitarbeiter_id` AS `resource_id`,concat(ifnull(`m`.`vorname`,''),' ',ifnull(`m`.`name`,''),' [',ifnull(`m`.`eingestellt_als`,''),']') AS `name`,`dm`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dm`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_mitarbeiter` `dm` on(`t`.`tour_id` = `dm`.`tour_id`)) left join `mr_mitarbeiter` `m` on(`dm`.`mitarbeiter_id` = `m`.`mid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dm`.`mitarbeiter_id` is not null union select `t`.`tour_id` AS `tour_id`,`t`.`Mandant` AS `Mandant`,`t`.`Auftragsnummer` AS `Auftragsnummer`,`lg`.`lager_name` AS `lager_name`,`p`.`datum` AS `datum`,'Werkzeug' AS `typ`,`dw`.`werkzeug_id` AS `resource_id`,`w`.`bezeichnung` AS `name`,`dw`.`einsatzdauer` AS `verbrauch`,'std' AS `einheit`,`dw`.`kosten` AS `kosten` from (((((`mr_touren_dispo_vorgaenge` `t` left join `mr_touren_dispo_werkzeug` `dw` on(`t`.`tour_id` = `dw`.`tour_id`)) left join `mr_werkzeug` `w` on(`dw`.`werkzeug_id` = `w`.`wid`)) left join `mr_touren_timelines` `tl` on(`t`.`timeline_id` = `tl`.`timeline_id`)) left join `mr_touren_portlets` `p` on(`tl`.`portlet_id` = `p`.`portlet_id`)) left join `mr_lager` `lg` on(`p`.`lager_id` = `lg`.`lager_id`)) where `t`.`tour_abgeschlossen_am` is not null and `dw`.`werkzeug_id` is not null;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
