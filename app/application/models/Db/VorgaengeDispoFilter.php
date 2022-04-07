<?php

class Model_Db_VorgaengeDispoFilter extends Model_Db_Abstract
{   
    protected $_name    = 'mr_auftragskoepfe_dispofilter';
    protected $_primary = array('Mandant', 'Auftragsnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }
}
