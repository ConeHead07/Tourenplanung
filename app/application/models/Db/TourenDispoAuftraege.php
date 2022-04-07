<?php
/**
 * Description of user
 *
 * @author rybka
 */
class Model_Db_TourenDispoAuftraege extends Zend_Db_Table_Abstract 
{
    //put your code here
    // Vars fuer Zend_Db_Table    
    protected $_name    = 'mr_touren_dispo_auftraege';
    protected $_primary = array('Mandant', 'Auftragsnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}

