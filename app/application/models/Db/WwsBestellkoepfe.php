<?php

class Model_Db_WwsBestellkoepfe extends Model_Db_Abstract
{
    protected $_name    = 'wws_bestellkoepfe';
    protected $_primary = array('Mandant','Bestellnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}

