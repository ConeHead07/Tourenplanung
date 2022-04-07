<?php

/**
 * Description of user
 *
 * @author rybka
 */
class Model_Db_TourenDispoVorgaenge extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_touren_dispo_vorgaenge';
    protected $_primary = 'tour_id';
    
    protected $_referenceMap = array(
        'touren_timelines' => array(
            'columns' => 'timeline_id',
            'refTableClass' => 'Model_Db_TourenTimelines',
            'refColumns' => 'timeline_id'
        ),
        'touren_vorgaenge' => array(
            'columns' => array('Mandant','Auftragsnummer'),
            'refTableClass' => 'Model_Db_Vorgaenge',
            'refColumns' => array('Mandant','Auftragsnummer'),
        )
    );
}


