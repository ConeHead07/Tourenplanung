<?php

class Model_BestellpositionenMeta extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'bestellpositionenMeta';
    
    public function groupEditStellplatz($mandant, $bestellnr, $stellplatz, $user)
    {
        /** @var $db Zend_Db_Adapter_Abstract **/
        $db = Zend_Registry::get('db');
        $storage = $this->getStorage();
        $storageWws = $this->loadStorage('bestellpositionen');
        
        /** @var $rowset Zend_Db_Table_Rowset */
        $select = $db->select();
        $select->from(array('bp' => $storageWws->info(Zend_Db_Table::NAME)), 'Positionsnummer');
        $select->where('Mandant = ' . $mandant . ' AND Bestellnummer = ' . $bestellnr );
        $posnrs = $db->fetchCol($select);
        
        foreach($posnrs as $pnr) {
            $row = $storage->find($mandant, $bestellnr, $pnr)->current();
            if (!$row) {
                $row = $storage->createRow();
                $row->Mandant = $mandant;
                $row->Bestellnummer = $bestellnr;
                $row->Positionsnummer = $pnr;
            } else {
                if ($row->Stellplatz == $stellplatz) continue;
            }
            $row->Stellplatz = $stellplatz;
            $row->StellplatzHistorie.= $stellplatz . ';' . $user . ';' . date('Y-m-d H:i:s') . PHP_EOL;
            $row->save();
        }
        
    }
    
    public function editStellplatz($mandant, $bestellnr, $pnr, $stellplatz, $user)
    {
        /** @var $db Zend_Db_Adapter_Abstract **/
        $db = Zend_Registry::get('db');
        $storage = $this->getStorage();
        $storageWws = $this->loadStorage('bestellpositionen');
        
        /** @var $rowset Zend_Db_Table_Rowset */
        $select = $db->select();
        $select->from(array('bp' => $storageWws->info(Zend_Db_Table::NAME)), 'Positionsnummer');
        $select->where('Mandant = ' . $mandant . ' AND Bestellnummer = ' . $bestellnr . ' AND Positionsnummer = ' . $pnr );
        $posnrs = $db->fetchCol($select);
        $doSave = false;
        
        $row = $storage->find($mandant, $bestellnr, $pnr)->current();
        if (!$row) {
            $row = $storage->createRow();
            $row->Mandant = $mandant;
            $row->Bestellnummer = $bestellnr;
            $row->Positionsnummer = $pnr;
            $doSave = true;
        } else {
            if ($row->Stellplatz != $stellplatz) $doSave = true;
        }
        
        if ($doSave) {
            $row->Stellplatz = $stellplatz;
            $row->StellplatzHistorie = $stellplatz . ';' . $user . ';' . date('Y-m-d H:i:s') . PHP_EOL . $row->StellplatzHistorie;
            
            $row->save();
//            $before = $row->toArray();
//            die('#'. __LINE__ . ' saveStellplatz row before: ' . print_r($before,1) . PHP_EOL . ' after save: ' .  print_r($row->toArray(), 1) );
            if ($row->save()) return true;
        }
        return false;
        
    }
}
