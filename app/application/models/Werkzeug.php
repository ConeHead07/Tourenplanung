<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author rybka
 */
class Model_Werkzeug extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'werkzeug';
    
    protected $_storage = null;
    protected $_db = null;
    protected $_tbl = null;
    
    
    public function __construct() {
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);
        $this->_key = current($this->_storage->info(Zend_Db_Table::PRIMARY));
    }

    public function getName(int $id)
    {
        return $this->_db->fetchOne("SELECT bezeichnung name FROM {$this->_tbl} WHERE {$this->_key} = $id");
    }
    
    public function update(array $data, $id) {
        if (!parent::update($data, $id)) return false;
        
        $oldCategoryIds = $this->fetchCategoryIds($id);
        $newCategoryIds = (trim($data['categories'])) ? explode(',', $data['categories']) : array();
        
        $deleteCategoryIds = array_diff($oldCategoryIds, $newCategoryIds);
        $insertCategoryIds = array_diff($newCategoryIds, $oldCategoryIds);
        if (count($deleteCategoryIds)===0 && count($insertCategoryIds)===0)
            return true;
        
        $lnk = new Model_Db_WerkzeugCategoriesLnk();
        $lnkMap  = $lnk->info('referenceMap');
        $lnkFpId = $lnkMap['Werkzeug']['columns'];
        $refFpId = $lnkMap['Werkzeug']['refColumns'];
        $lnkCgId = $lnkMap['Categories']['columns'];
        $refCgId = $lnkMap['Categories']['refColumns'];
        
        foreach($insertCategoryIds as $categoryId) {
            $lnk->insert(array(
                $lnkFpId => $id,
                $lnkCgId => $categoryId
            ));
        }
        
        foreach($deleteCategoryIds as $categoryId) {
            $row = $lnk->find($id, $categoryId)->current();
            $row->delete();
        }
        return true;
    }
    
    public function insert(array $data) {
        $id = parent::insert($data);
        
        $insertCategoryIds = (trim($data['categories'])) ? explode(',', $data['categories']) : array();
        
        if ($id && count($insertCategoryIds)===0)
            return $id;
        
        //die('insertCategoryIds: ' . print_r($insertCategoryIds,1));
        
        $lnk = new Model_Db_WerkzeugCategoriesLnk();
        $lnkMap  = $lnk->info('referenceMap');
        $lnkFpId = $lnkMap['Werkzeug']['columns'];
        $refFpId = $lnkMap['Werkzeug']['refColumns'];
        $lnkCgId = $lnkMap['Categories']['columns'];
        $refCgId = $lnkMap['Categories']['refColumns'];
        
        foreach($insertCategoryIds as $categoryId) {
            $lnk->insert(array(
                $lnkFpId => $id,
                $lnkCgId => $categoryId
            ));
        }
        return $id;
    }
    
    public function fetchEntries($options = array()) {
        $entries = parent::fetchEntries($options);
        
        for($i = 0; $i < count($entries); ++$i) {
            $entries[$i]['categories'] = $this->fetchCategoriesByRow( $entries[$i] )->toArray();
        }
        return $entries;
    }
    
    public function fetchEntry($id) 
    {
        /* @var $row Zend_Db_Table_Row_Abstract */
        $rslt = $this->getStorage()->find($id);
        if ($rslt) {
            $row  = $rslt->current();
            if ($row) {
                $data = $row->toArray();
                $cg = $this->fetchCategoriesByRow($row);
                $data['categories'] = ($cg) ? $cg->toArray() : array();
                return $data;
            }
        }
        return null;
    }
    
    public function delete($id) {
        parent::delete($id);
        
        $deleteCategoryIds = $this->fetchCategoryIds($id);
        
        if ( count($deleteCategoryIds)===0 )
            return;
        
        $lnk = new Model_Db_WerkzeugCategoriesLnk();
        $lnkMap  = $lnk->info('referenceMap');
        $lnkFpId = $lnkMap['Werkzeug']['columns'];
        $refFpId = $lnkMap['Werkzeug']['refColumns'];
        $lnkCgId = $lnkMap['Categories']['columns'];
        $refCgId = $lnkMap['Categories']['refColumns'];
        
        foreach($deleteCategoryIds as $categoryId) {
            $row = $lnk->find($id, $categoryId)->current();
            $row->delete();
        }
    }
    
    public function fetchCategoryIds($id) {
        $lnk = new Model_WerkzeugCategoriesLnk();
        return $lnk->fetchCategoryIdsByWerkzeugId($id);
    }
    
    /**
     * @return Zend_Db_Table
     * @param array|Zend_Db_Table_Row $row 
     */
    public function fetchCategoriesByRow($row)
    {
        if (gettype($row)!=='array' && (gettype($row) !=='object' || false==($row instanceof Zend_Db_Table_Row) ))
            throw new Exception('Invalid Parameter. Expect array or Zend_Db_Table_Row! Given: ' . print_r($row,1));
        
        if (! ($row instanceof Zend_Db_Table_Row))
            $row = $this->getStorage()->createRow( $row );
        
        return $row->findManyToManyRowset('Model_Db_WerkzeugCategories', 'Model_Db_WerkzeugCategoriesLnk');
        
    }
    
}
