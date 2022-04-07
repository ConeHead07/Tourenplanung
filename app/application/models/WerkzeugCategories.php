<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Treeview
 *
 * @author Administrator
 */
require_once('MyProject/Db/NestedTree/Controller.php');
require_once(APPLICATION_PATH . '/models/Db/WerkzeugCategories.php');

class Model_WerkzeugCategories extends MyProject_Db_NestedTree_Controller {
    //put your code here
    
    protected $_storageName = 'werkzeugCategories';
    
    /** @var $_db PDO **/
    protected $_db = null;
    
    // Default Identifiers
    protected $_name = 'mr_werkzeug_categories';
    protected $_primary = 'category_id';
    protected $_caption = 'name';
    protected $_left = 'lft';
    protected $_right = 'rgt';
    
    // Default Quote Identifiers (Without quoting)
    protected $_qname = 'mr_werkzeug_categories';
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
        return new Model_Db_WerkzeugCategories();
//        return MyProject_Model_Database::loadModel($this->_storageName);
    }
    
    public function getNodesByQuery($term) {
        
    }
    
}

