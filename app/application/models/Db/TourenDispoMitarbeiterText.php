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
class Model_Db_TourenDispoMitarbeiterText extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_touren_dispo_mitarbeiter_txt';
    protected $_primary = 'id';
    
    protected $_referenceMap = array(
        'disporesource' => array(
            'columns' => 'id',
            'refTableClass' => 'Model_Db_TourenDispoMitarbeiter',
            'refColumns' => 'id'
        )
    );
}


