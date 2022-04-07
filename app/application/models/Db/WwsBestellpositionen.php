<?php

class Model_Db_WwsBestellpositionen extends Model_Db_Abstract
{  
    protected $_name    = 'wws_bestellpositionen';
    protected $_primary = array('Mandant','Bestellnummer','Positionsnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}
