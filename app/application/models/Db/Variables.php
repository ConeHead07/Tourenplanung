<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 06.09.2018
 * Time: 10:25
 */

class Model_Db_Variables extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'mr_variables';
    protected $_primary = 'name';
}