<?php

class Model_Db_Warenbewegungen extends Model_Db_Abstract
{
    //put your code here
    // Vars fuer Zend_Db_Table    
    protected $_name    = 'mr_warenbewegungen_dispofilter';
    protected $_primary = array('Mandant', 'LaufendeNummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }


}

//$x = new Model_Db_Vorgaenge();
//$x->setDefaultAdapter();

