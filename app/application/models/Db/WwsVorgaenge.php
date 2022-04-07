<?php

class Model_Db_WwsVorgaenge extends Zend_Db_Table_Abstract 
{   
    protected $_name    = 'wws_auftragskoepfe';
    protected $_primary = array('Mandant', 'Auftragsnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}

