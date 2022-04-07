<?php

class Model_Db_WwsAuftragspositionen extends Model_Db_Abstract
{  
    protected $_name    = 'wws_auftragspositionen';
    protected $_primary = array('Mandant','Auftragsnummer','Positionsnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}
