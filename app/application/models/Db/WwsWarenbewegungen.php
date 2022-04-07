<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author rybka
 */
class Model_Db_WwsWarenbewegungen extends Model_Db_Abstract
{
    //put your code here
    // Vars fuer Zend_Db_Table    
    protected $_name    = 'wws_warenbewegungen';
    protected $_primary = array('Mandant', 'LaufendeNummer');
    
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db');
    }


}

//$x = new Model_Db_Vorgaenge();
//$x->setDefaultAdapter();

