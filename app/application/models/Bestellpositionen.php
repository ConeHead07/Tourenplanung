<?php

class Model_Bestellpositionen extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'bestellpositionen';
    
    /**
     * 
     * @param int $mandant
     * @param int $auftragsnr
     * @param int $apnr auftragsPositionsnummer
     * @return Zend_Db_Table_Row
     */
    public function getRowByAuftragsposition($mandant, $auftragsnr, $apnr) 
    {
        /* @var $db Zend_Db_Adapter_Abstract */        
        $db = Zend_Registry::get('db');
        $m = MyProject_Model_Database::loadModel($this->_storageName);        
        $s = $m->getStorage();
        
        return $s->fetchRow( 
             $db->quoteInto('Mandant = ?', $mandant, Zend_Db::INT_TYPE)
           . $db->quoteInto(' AND Auftragsnummer = ?', $auftragsnr, Zend_Db::INT_TYPE)
           . $db->quoteInto(' AND AuftragsPositionsnummer = ?', $apnr, Zend_Db::INT_TYPE)                
        );
    }
}
