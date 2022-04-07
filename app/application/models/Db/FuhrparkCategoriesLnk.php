<?php 

/**
 * Description of user
 * @author rybka
 */
class Model_Db_FuhrparkCategoriesLnk extends Model_Db_Abstract
{
    //put your code here
    protected $_name = 'mr_fuhrpark_categories_lnk';
    protected $_primary = array('fuhrpark_id', 'category_id');
    
    protected $_referenceMap = array(
        'Fuhrpark' => array(
            'columns' => 'fuhrpark_id',
            'refTableClass' => 'Model_Db_Fuhrpark',
            'refColumns' => 'fid'
        ),
        'Categories' => array(
            'columns' => 'category_id',
            'refTableClass' => 'Model_Db_FuhrparkCategories',
            'refColumns' => 'category_id'
        )
    );
}


