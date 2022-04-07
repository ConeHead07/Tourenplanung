<?php

class Model_Mitarbeiter extends MyProject_Model_Database implements Model_ResourceInterface
{
    protected $_storageName = 'mitarbeiter';
    
    protected $_storage = null;
    protected $_db = null;
    protected $_tbl = null;
    
    
    public function __construct() {
        parent::__construct();
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);
        $this->_key = current($this->_storage->info(Zend_Db_Table::PRIMARY));
    }

    public function getName(int $id)
    {
        return $this->_db->fetchOne("SELECT concat(SUBSTR(vorname,1,1),'. ', name) name FROM {$this->_tbl} WHERE {$this->_key} = $id");
    }
    
    public function update(array $data, $id) {
        if (!parent::update($data, $id)) return false;
        
        $oldCategoryIds = $this->fetchCategoryIds($id);
        $newCategoryIds = (trim($data['categories'])) ? explode(',', $data['categories']) : array();
        
        $deleteCategoryIds = array_diff($oldCategoryIds, $newCategoryIds);
        $insertCategoryIds = array_diff($newCategoryIds, $oldCategoryIds);
        if (count($deleteCategoryIds)===0 && count($insertCategoryIds)===0)
            return true;
        
//        echo '<pre>';
//        echo 'oldC: '; var_dump($oldCategoryIds); echo '<br/>' . PHP_EOL;
//        echo 'newC: '; var_dump($newCategoryIds); echo '<br/>' . PHP_EOL;
//        echo 'delC: '; var_dump($deleteCategoryIds); echo '<br/>' . PHP_EOL;
//        echo 'addC: '; var_dump($insertCategoryIds); echo '<br/>' . PHP_EOL;
//        exit;
        
        $lnk = new Model_Db_MitarbeiterCategoriesLnk();
        $lnkMap  = $lnk->info('referenceMap');
        $lnkFpId = $lnkMap['Mitarbeiter']['columns'];
        $refFpId = $lnkMap['Mitarbeiter']['refColumns'];
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
        
        $lnk = new Model_Db_MitarbeiterCategoriesLnk();
        $lnkMap  = $lnk->info('referenceMap');
        $lnkFpId = $lnkMap['Mitarbeiter']['columns'];
        $refFpId = $lnkMap['Mitarbeiter']['refColumns'];
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
            $cg = $this->fetchCategoriesByRow( $entries[$i] );
            $entries[$i]['categories'] = ($cg) ? $cg->toArray() : array();
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
        
        $lnk = new Model_Db_MitarbeiterCategoriesLnk();
        $lnkMap  = $lnk->info('referenceMap');
        $lnkFpId = $lnkMap['Mitarbeiter']['columns'];
        $refFpId = $lnkMap['Mitarbeiter']['refColumns'];
        $lnkCgId = $lnkMap['Categories']['columns'];
        $refCgId = $lnkMap['Categories']['refColumns'];
        
        foreach($deleteCategoryIds as $categoryId) {
            $row = $lnk->find($id, $categoryId)->current();
            $row->delete();
        }
    }
    
    public function fetchCategoryIds($id) {
        $lnk = new Model_MitarbeiterCategoriesLnk();
        return $lnk->fetchCategoryIdsByMitarbeiterId($id);
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
        
        return $row->findManyToManyRowset('Model_Db_MitarbeiterCategories', 'Model_Db_MitarbeiterCategoriesLnk');
        
    }

    public function fetchCategoriesByRsrcIds(array $aRsrcIds): array {

        $rsrcKey = Model_Db_MitarbeiterCategoriesLnk::obj()->key();
        $rsrcCtgLnkTbl = Model_Db_MitarbeiterCategoriesLnk::obj()->tableName();
        $rsrcCtgTbl = Model_Db_MitarbeiterCategories::obj()->tableName();

        $aRsrcIntIds = array_map(function($itm) { return (int)$itm; }, $aRsrcIds);
        $sql = "SELECT l.$rsrcKey, c.* 
                FROM $rsrcCtgLnkTbl l
                JOIN $rsrcCtgTbl c ON (l.category_id = c.category_id)
                WHERE l.$rsrcKey IN (" . implode(',', $aRsrcIntIds) . ") ORDER BY l.$rsrcKey";

        $rows = $this->_db->fetchAll($sql, Zend_Db::FETCH_ASSOC);
        $aRowsByRsrcId = [];
        $lastRid = '';
        $lastRsrc = null;

        foreach($rows as $_row) {
            $_rid = $_row[ $rsrcKey ];
            if ($lastRid != $_rid) {
                $lastRid = $_rid;
                $aRowsByRsrcId[$lastRid] = [];
                $lastRsrc = &$aRowsByRsrcId[$lastRid];
            }
            $lastRsrc[] = $_row;
        }

        return $aRowsByRsrcId;
    }

    public function getSqlSelectExprAsLabel(): string {
        return "CONCAT( SUBSTR(vorname, 1, 1), '.', name, IF(eingestellt_als NOT BETWEEN '0' AND '999999',CONCAT(',', eingestellt_als), '') )";
    }
    
}
