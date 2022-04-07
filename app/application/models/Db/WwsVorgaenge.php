<?php

class Model_Db_WwsVorgaenge extends Model_Db_Abstract
{   
    protected $_name    = 'wws_auftragskoepfe';
    protected $_primary = array('Mandant', 'Auftragsnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}

