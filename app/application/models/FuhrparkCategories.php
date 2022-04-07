<?php

require_once('MyProject/Db/NestedTree/Controller.php');
require_once(APPLICATION_PATH . '/models/Db/FuhrparkCategories.php');

class Model_FuhrparkCategories extends MyProject_Db_NestedTree_Controller {
    
    protected $_storageName = 'fuhrparkCategories';
    
    /** @var $_db PDO **/
    protected $_db = null;
    
    // Default Identifiers
    protected $_name = 'mr_fuhrpark_categories';
    protected $_primary = 'category_id';
    protected $_caption = 'name';
    protected $_left = 'lft';
    protected $_right = 'rgt';
    
    // Default Quote Identifiers (Without quoting)
    protected $_qname = 'mr_fuhrpark_categories';
    protected $_qprimary = 'category_id';
    protected $_qcaption = 'name';
    protected $_qleft = 'lft';
    protected $_qright = 'rgt';
    
    public function __construct() {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_storage = $this->getStorage();
           
        $this->_storageName = $this->_storage->info(Zend_Db_Table_Abstract::NAME);
        $this->_idFld = current($this->_storage->info(Zend_Db_Table_Abstract::PRIMARY));
        
        parent::__construct(array('tableName'=>$this->_storageName));
    }
    
    public function getStorage()
    {
        return new Model_Db_FuhrparkCategories();
//        return MyProject_Model_Database::loadModel($this->_storageName);
    }
    
    public function getNodesByQuery($term) 
    {
        
    }
    
}

