<?php

/**
 * Description of user
 * @author rybka
 */
class Model_Db_Mitarbeiter extends Model_Db_Abstract
{
    //put your code here
    protected $_name = 'mr_mitarbeiter';
    protected $_primary = 'mid';
    protected $_dependentTables = array('Model_Db_MitarbeiterCategoriesLnk');
}


