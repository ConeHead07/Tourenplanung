<?php

class Model_Db_WwsLieferscheindruckkopf extends Zend_Db_Table_Abstract 
{
    //put your code here
    // Vars fuer Zend_Db_Table
    protected $_name    = 'wws_lieferscheindruckkopf';
    protected $_primary = array('Mandant', 'Lieferscheinnummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }


}

//$x = new Model_Db_Vorgaenge();
//$x->setDefaultAdapter();

