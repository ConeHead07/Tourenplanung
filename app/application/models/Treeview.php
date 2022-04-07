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
require_once('MyProject/Model/TreeviewAbstract.php');

class Model_Treeview extends MyProject_Model_TreeviewAbstract {
    //put your code here
    
    protected $_storageName = 'treeview';
    
    protected $_nameFld     = 'name';
    protected $_lftFld      = 'lft';
    protected $_rgtFld      = 'rgt';
    
    /* @var $_storage Model_Db_Treeview */
    protected $_storage;
    protected $_idFld;
    
    public function __construct(array $fld_config = null) {
        $this->_storage = $this->getStorage();
           
        $this->_storageName = $this->_storage->info(Zend_Db_Table_Abstract::NAME);
        $this->_idFld = current($this->_storage->info(Zend_Db_Table_Abstract::PRIMARY));
        
        parent::__construct(array('tableName'=>$this->_storageName));
    }
    
}

