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
class Model_Db_Fuhrpark extends Zend_Db_Table_Abstract 
{
    //put your code here
    // Vars fuer Zend_Db_Table    
    protected $_name    = 'mr_fuhrpark';
    protected $_primary = 'fid';
    protected $_dependentTables = array('Model_Db_FuhrparkCategoriesLnk');
}

