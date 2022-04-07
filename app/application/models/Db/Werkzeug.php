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
class Model_Db_Werkzeug extends Zend_Db_Table_Abstract 
{  
    protected $_name    = 'mr_werkzeug';
    protected $_primary = 'wid';
    protected $_dependentTables = array('Model_Db_WerkzeugCategoriesLnk');
}

