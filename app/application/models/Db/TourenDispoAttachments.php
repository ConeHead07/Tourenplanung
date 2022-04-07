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
class Model_Db_TourenDispoAttachments extends Zend_Db_Table_Abstract 
{
    //put your code here
    // Vars fuer Zend_Db_Table    
    protected $_name    = 'mr_touren_dispo_attachments';
    protected $_primary = 'dokid';
    
    protected $_referenceMap = array(
        'vorgang' => array(
            'columns' => 'tour_id',
            'refTableClass' => 'Model_Db_TourenDispoVorgaenge',
            'refColumns' => 'tour_id',
        )
    );

}

