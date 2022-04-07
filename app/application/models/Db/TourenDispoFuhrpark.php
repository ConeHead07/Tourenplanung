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
class Model_Db_TourenDispoFuhrpark extends Model_Db_Abstract
{
    //put your code here
    protected $_name = 'mr_touren_dispo_fuhrpark';
    protected $_primary = 'id';
    
    protected $_referenceMap = array(
        'resource' => array(
            'columns' => 'fuhrpark_id',
            'refTableClass' => 'Model_Db_Fuhrpark',
            'refColumns' => 'fid'
        ),
        'vorgang' => array(
            'columns' => 'tour_id',
            'refTableClass' => 'Model_Db_TourenDispoVorgaenge',
            'refColumns' => 'tour_id'
        )
    );
}


