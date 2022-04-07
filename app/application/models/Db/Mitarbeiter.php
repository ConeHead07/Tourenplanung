<?php

/**
 * Description of user
 * @author rybka
 */
class Model_Db_Mitarbeiter extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_mitarbeiter';
    protected $_primary = 'mid';
    protected $_dependentTables = array('Model_Db_MitarbeiterCategoriesLnk');
}


