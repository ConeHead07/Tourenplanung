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
class Model_Db_WerkzeugCategoriesLnk extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_werkzeug_categories_lnk';
    protected $_primary = array('werkzeug_id', 'category_id');
    
    protected $_referenceMap = array(
        'Werkzeug' => array(
            'columns' => 'werkzeug_id',
            'refTableClass' => 'Model_Db_Werkzeug',
            'refColumns' => 'wid'
        ),
        'Categories' => array(
            'columns' => 'category_id',
            'refTableClass' => 'Model_Db_WerkzeugCategories',
            'refColumns' => 'category_id'
        )
    );
}


