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
class Model_Db_TourenTimelines extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_touren_timelines';
    protected $_primary = 'timeline_id';
    
    
    protected $_referenceMap = array(
        'touren_portlets' => array(
            'columns' => 'portlet_id',
            'refTableClass' => 'Model_Db_TourenPortlets',
            'refColumns' => 'portlet_id'
            )
        );
}


