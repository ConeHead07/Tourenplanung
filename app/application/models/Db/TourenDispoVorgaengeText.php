<?php

/**
 * Description of user
 *
 * @author rybka
 */
class Model_Db_TourenDispoVorgaengeText extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_touren_dispo_vorgaenge_txt';
    protected $_primary = 'tour_id';
    
    protected $_referenceMap = array(
        'touren_vorgaenge' => array(
            'columns' => array('tour_id'),
            'refTableClass' => 'Model_Db_Vorgaenge',
            'refColumns' => array('tour_id'),
        )
    );
}


