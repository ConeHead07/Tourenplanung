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
class Model_Db_TourenDispoMitarbeiter extends Model_Db_Abstract
{
    //put your code here
    protected $_name = 'mr_touren_dispo_mitarbeiter';
    protected $_primary = 'id';
    
    protected $_referenceMap = array(
        'resource' => array(
            'columns' => 'mitarbeiter_id',
            'refTableClass' => 'Model_Db_Mitarbeiter',
            'refColumns' => 'mid'
        ),
        'vorgang' => array(
            'columns' => 'tour_id',
            'refTableClass' => 'Model_Db_TourenDispoVorgaenge',
            'refColumns' => 'tour_id'
        )
    );
}


