<?php

/**
 *
 * @author rybka
 */
class Model_Db_TourenDispoWerkzeug extends Model_Db_Abstract
{
    //put your code here
    protected $_name = 'mr_touren_dispo_werkzeug';
    protected $_primary = 'id';
    
    protected $_referenceMap = array(
        'resource' => array(
            'columns' => 'werkzeug_id',
            'refTableClass' => 'Model_Db_Werkzeug',
            'refColumns' => 'wid'
        ),
        'vorgang' => array(
            'columns' => 'tour_id',
            'refTableClass' => 'Model_Db_TourenDispoVorgaenge',
            'refColumns' => 'tour_id'
        )
    );
}


