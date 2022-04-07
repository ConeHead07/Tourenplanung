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
class Model_Db_MitarbeiterCategoriesLnk extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_mitarbeiter_categories_lnk';
    protected $_primary = array('mitarbeiter_id', 'category_id');
    
    protected $_referenceMap = array(
        'Mitarbeiter' => array(
            'columns' => 'mitarbeiter_id',
            'refTableClass' => 'Model_Db_Mitarbeiter',
            'refColumns' => 'mid'
        ),
        'Categories' => array(
            'columns' => 'category_id',
            'refTableClass' => 'Model_Db_MitarbeiterCategories',
            'refColumns' => 'category_id'
        )
    );
}


