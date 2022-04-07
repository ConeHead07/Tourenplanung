<?php

require_once('MyProject/Db/NestedTree/Controller.php');
require_once(APPLICATION_PATH . '/models/Db/MitarbeiterCategories.php');

class Model_MitarbeiterCategoriesLnk extends MyProject_Model_Database {
    
    protected $_storageName = 'mitarbeiterCategoriesLnk';
    
    /** @var $_db PDO **/
    protected $_storage = null;
    protected $_db = null;
    protected $_tbl = null;
    
    public function __construct() {
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);
        $this->_cnf = $this->_storage->info();
        $this->_map = $this->_storage->info('referenceMap');
    }
    
    /**
     *
     * @param int $id 
     * @return array ids (integers)
     */
    public function fetchCategoryIdsByMitarbeiterId($id) 
    {   
        /** @var $_s Model_Db_MitarbeiterCategoriesLnk */
        $_s = $this->_storage;
        $fpKey = $this->_map['Mitarbeiter']['columns'];
        $cgKey = $this->_map['Categories']['columns'];
        
        /* @var $select Zend_Db_Select */
        $select = $this->_db->select()->from($this->_tbl, $cgKey)->where($fpKey.'=?', $id, Zend_Db::INT_TYPE);
//        echo (string) $select;
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        return $db->fetchCol($select);
    }
    
    public function getCategorySubSql($term)
    {
        $main = 'Mitarbeiter';
        $ctg  = 'Categories';
        $map = $this->_storage->info(Zend_Db_Table::REFERENCE_MAP);
        
        /* @var $ctgStorage Model_Db_FuhrparkCategories */
        $ctgStorage = new $map[$ctg]['refTableClass'];
        $ctgTbl = $ctgStorage->info(Zend_Db_Table::NAME);
        
        if ($term) {
            
            if (is_numeric($term))
                $sqlFromSub = 'SELECT * FROM ' . $ctgTbl . ' WHERE ' . $map[$ctg]['refColumns'] . ' = '.(int)$term;
            else
                $sqlFromSub = 'SELECT * FROM ' . $ctgTbl . ' WHERE name LIKE "'.$term.'%"';
                        
            return 
                     'SELECT lnk.' . $map[$main]['columns'] . ' FROM ' . $this->_tbl  . ' AS lnk' . PHP_EOL
                    .'WHERE ' . $map[$ctg]['columns'] . ' IN (' . PHP_EOL
                    .'SELECT node.' . $map[$ctg]['refColumns'] . ' ' . PHP_EOL
                    .'FROM ' . $ctgTbl . ' AS node, ' . PHP_EOL
                    .'(' . $sqlFromSub. ') AS parent' . PHP_EOL
                    .'WHERE node.lft BETWEEN parent.lft AND parent.rgt ' . PHP_EOL
                    .')';
        }
        return '';
    }
}

