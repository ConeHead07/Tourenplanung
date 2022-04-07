<?php

class Model_Db_Vorgaenge extends Zend_Db_Table_Abstract 
{   
    protected $_name    = 'mr_auftragskoepfe_dispofilter';
    protected $_primary = array('Mandant', 'Auftragsnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}
