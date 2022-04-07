<?php

class Model_VorgaengeDispoFilter extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'vorgaengeDispoFilter';
    
    public function reloadData()
    {
        $db = $this->getStorage()->getAdapter();
        
        $tblVDF = $this->getStorage()->info(Zend_Db_Table::NAME);
        $tblV = MyProject_Model_Database::loadStorage('vorgaenge')->info(Zend_Db_Table::NAME);
        
        $this->getStorage()->delete("1 > 0");
        
        $fields = array(
            'Mandant', 'Auftragsnummer', 'Bearbeitungsstatus', 'Gruppierungsnummer', 
            'Vorgangstitel', 'Kundennummer', 'LieferungName', 'Lieferwoche', 'Lieferjahr', 
            'Liefertermin', 'LieferterminFix', 'Auftragswert',
            'LieferungOrt', 'LieferungStrassePostfach', 'LieferungPostleitzahl', 'AnsprechpartnerNachnameLief', 'Geschaeftsbereich'
            );
        
        $sql = 'INSERT INTO  ' . $tblVDF . ' ( ' . implode(',', $fields) . ' ) ';
        $sql.= 'SELECT  ' . implode(',', $fields) . ' FROM ' . $tblV . ' WHERE Bearbeitungsstatus > 1 AND Bearbeitungsstatus < 10';
        
        $db->query($sql);

    }
    
}