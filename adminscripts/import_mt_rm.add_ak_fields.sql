ALTER TABLE `mr_auftragskoepfe_dispofilter`
   ADD COLUMN `UnterBearbeitungsstatus` TINYINT(4) NULL AFTER `Bearbeitungsstatus`,
   ADD COLUMN  `RechnungName` varchar(255) DEFAULT NULL AFTER `Geschaeftsbereich`,
   ADD COLUMN `AngebotName` varchar(255) DEFAULT NULL AFTER `RechnungName`,
   ADD COLUMN `DirektLieferInfo` varchar(255) DEFAULT NULL AFTER `AngebotName`;