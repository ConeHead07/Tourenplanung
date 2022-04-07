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
class Model_Db_TourenDispoResources extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_touren_dispo_resources';
    protected $_primary = array('resource_id','timeline_id');
    
    protected $_referenceMap = array(
        'resources' => array(
            'columns' => 'resource_id',
            'refTableClass' => 'Model_Db_Resources',
            'refColumns' => 'rid'
        ),
        'timelines' => array(
            'columns' => array('Mandant', 'Auftragsnummer'),
            'refTableClass' => 'Model_Db_TourenDispoVorgaenge',
            'refColumns' => array('Mandant', 'Auftragsnummer')
        )
    );
}


