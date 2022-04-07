<?php

class MyProject_Model_WwsFilterImport
{
    protected $dbInfoName = 0;
    
    public function __construct() {
        $this->dbInfoName = Zend_Db_Table::NAME;
    }
    
    public function import_wws(
            Zend_Db_Table_Abstract $wws, 
            Zend_Db_Table_Abstract $df, 
            $wwsFields, $dispoFields = '')
    {
        set_time_limit(240);
        $db = $df->getAdapter();
        
        $tblWws = $wws->info($this->dbInfoName);
        $tblDF  = $df->info($this->dbInfoName);
        
        $sql = 'SELECT MAX(mr_modified) FROM ' . $tblDF;
        $lastImport = $db->fetchOne($sql);
		echo '#' . __LINE__ . ' ' . __FILE__ . ' lastImport: ' . print_r($lastImport,1) . '<br>' . PHP_EOL;
        
        $sql = 'REPLACE ' . $tblDF . '(' . ($dispoFields?:$wwsFields) . ', mr_modified) '
              .'SELECT ' . $wwsFields . ', IFNULL(GeaendertAm, AngelegtAm) FROM ' . $tblWws;
        if ( preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $lastImport) ) //&& substr($lastImport,0,4) > '0000'  )
            $sql.= ' WHERE GeaendertAm > ' . $db->quote($lastImport) . ' OR AngelegtAm > ' . $db->quote($lastImport);
        
        if (!empty($where)) {
            $sql.= ' AND (' . $where . ')';
        }
		
        echo '#' . __LINE__ . $sql . '<br/>' . PHP_EOL;
        $stmt = $db->query($sql);
        
    }
    
    public function import_auftragskoepfe() 
    {
        // ALT
        $fields = 'Mandant,Auftragsnummer,Bearbeitungsstatus,Lieferwoche,Lieferjahr,'
            .'Liefertermin,LieferterminFix,Auftragswert,AuftragswertListe,Gruppierungsnummer,'
            .'Vorgangstitel,LieferungName,Kundennummer,LieferungOrt,LieferungLand,'
            .'AngebotName,DirektLieferInfo,RechnungName,'
            .'LieferungStrassePostfach,LieferungPostleitzahl,AnsprechpartnerNachnameLief,'
            .'Geschaeftsbereich,AngelegtAm,BestaetigtAm,GeaendertAm';

        // NEU
        $fields = 'Mandant,Auftragsnummer,Bearbeitungsstatus,zusatzvorgangsartnr,Lieferwoche,Lieferjahr,'
            .'Liefertermin,LieferterminFix,Auftragswert,AuftragswertListe,Gruppierungsnummer,'
            .'Vorgangstitel,LieferungName,Kundennummer,LieferungOrt,LieferungLand,'
            .'LieferungStrassePostfach,LieferungPostleitzahl,AnsprechpartnerNachnameLief,'
            .'Geschaeftsbereich,AngelegtAm,BestaetigtAm,GeaendertAm';
        echo '#' . __LINE__ . ' ' . __FILE__ . ' Call ' . __CLASS__ . '->import_wws<br>' . PHP_EOL;
        $this->import_wws(
                new Model_Db_WwsVorgaenge,
                new Model_Db_Vorgaenge,
                $fields,
                '',
                'Versandbedingung not LIKE "nicht ins Dispotool"');
    }
    
    public function import_bestellkoepfe() 
    {
        $fields = 'Mandant,Bestellnummer,Bestellungstyp,Bestellart,Bearbeitungsstatus,'
                 .'Kundennummer,BestellName,Lieferwoche,Lieferjahr,Liefertermin,'
                 .'LieferterminFix,Auftragsnummer,Lagerkennung,Bestellwert,Lieferbedingung,'
                 .'ErwarteterEingang,ErwarteterEingangWoche,ErwarteterEingangJahr,'
                 .'ErwarteterEingangterminFix,AngelegtAm,GeaendertAm';
        
        $this->import_wws(
                new Model_Db_WwsBestellkoepfe,
                new Model_Db_Bestellkoepfe,
                $fields);        
    }
    
    public function import_auftragspositionen() 
    {
        $fields = 'Mandant,Auftragsnummer,Positionsnummer,Positionsart,Positionstext,StruPosNr,'
                 .'Artikelnummer,Bezeichnung,Bestellmenge,Bestellmengeneinheit,'
                 .'Liefermenge,Preis,Lieferwoche,Lieferjahr,Liefertermin,LieferterminFix,'
				 .'InternePos, AlternativPos,'
                 .'AvisierterTermin,AvisierteWoche,AvisiertesJahr,AvisierterTerminFix,AvisierterTerminDauer,'
                 .'Lagerkennung,Stellplatz,AngelegtAm,GeaendertDurch,GeaendertAm';
        
        $this->import_wws(
                new Model_Db_WwsAuftragspositionen,
                new Model_Db_Auftragspositionen,
                $fields);        
    }
    
    public function import_bestellpositionen() 
    {
        $fields = 'Mandant,Bestellnummer,Positionsnummer,StruPosnr,Positionsart,'
                 .'Artikelnummer,Bezeichnung,Bestellmenge,Liefermenge,Lieferanschrift,Lieferwoche,Lieferjahr,'
                 .'Liefertermin,LieferterminFix,Lagerkennung,Auftragsnummer,'
                 .'AuftragsPositionsnummer,ErwarteterEingang,ErwarteterEingangWoche,'
                 .'ErwarteterEingangJahr,ErwarteterEingangterminFix,HerstellerKuerzel,'
                 .'AngelegtAm,GeaendertDurch,GeaendertAm';
        
        $this->import_wws(
                new Model_Db_WwsBestellpositionen,
                new Model_Db_Bestellpositionen,
                $fields);        
    }
}
?>
