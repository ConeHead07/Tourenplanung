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
class Model_TourenDispoWerkzeug extends Model_TourenDispoResourceAbstract
{
    protected $_storageName   = 'tourenDispoWerkzeug';
    protected $_resourceName  = 'Werkzeug';
    protected $_resourceModel = 'Model_Werkzeug';
    protected $_resourceType  = 'WZ';
    protected $_prmRsrcKey    = 'wid';
    protected $_rsrcLnkKey    = 'werkzeug_id';
    
    protected $_rsrcTitleField = 'bezeichnung';

    protected $_tblCtgName = 'mr_werkzeug_categories';

    protected $_tblCtgLnkName = 'mr_werkzeug_categories_lnk';
    protected $_tblCtgLnkRsrcKey = 'werkzeug_id';


    public function getSqlSelectExprAsLabel(): string {
        return 'bezeichnung';
    }
    
    /**
     * 
     * @param DateTimeImmutable $von
     * @param DateTimeImmutable $bis
     * @param type $filter
     * @param array $listOptions
     * @return $resultOptions
     */
    public function listFreeResources(
            DateTime $von, 
            DateTime $bis,
            $filter,
            array $listOptions = array(),
            $categoryTerm = ''
            )
    {
        
        // Get gesperrte Resourcen
        $db  = $this->getStorage()->getAdapter();
        $key = $this->_prmRsrcKey;
        $modelR = MyProject_Model_Database::loadModel('werkzeug');
        $tblR = $modelR->getStorage()->info( Zend_Db_Table::NAME );
        $tblSZ = MyProject_Model_Database::loadModel('resourcesSperrzeiten')->getStorage()->info(Zend_Db_Table::NAME);
        $tblDZ = MyProject_Model_Database::loadModel('resourcesDispozeiten')->getStorage()->info(Zend_Db_Table::NAME);
        $tblTR = MyProject_Model_Database::loadModel('tourenDispoWerkzeug')->getStorage()->info(Zend_Db_Table::NAME);
        $tblDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge')->getStorage()->info(Zend_Db_Table::NAME);
        
        $tblLK = MyProject_Model_Database::loadModel('leistung')->getStorage()->info(Zend_Db_Table::NAME);
        $tblEx = MyProject_Model_Database::loadModel('extern')->getStorage()->info(Zend_Db_Table::NAME);
        $tblCL = MyProject_Model_Database::loadModel('werkzeugCategoriesLnk')->getStorage()->info(Zend_Db_Table::NAME);
        $tblC  = MyProject_Model_Database::loadModel('werkzeugCategories')->getStorage()->info(Zend_Db_Table::NAME);
        
        $sqlDZ = 'SELECT ressourcen_id FROM ' . $tblDZ . PHP_EOL
             . ' WHERE ressourcen_typ = :typ ' . PHP_EOL
             . ' AND gebucht_von <= :dateBis'
             . ' AND gebucht_bis >= :dateVon';
        
        $sqlSZ = 'SELECT ressourcen_id FROM ' . $tblSZ . '  '
             . ' WHERE ressourcen_typ = :typ ' . PHP_EOL
             . ' AND gesperrt_von <= :dateBis'
             . ' AND gesperrt_bis >= :dateVon';
        
        $sqlDM = 'SELECT werkzeug_id FROM ' . $tblDV . ' dv '  . PHP_EOL
             . ' LEFT JOIN ' . $tblTR . ' mt ON(dv.tour_id = mt.tour_id) ' . PHP_EOL
             . ' WHERE ' . PHP_EOL
             . ' werkzeug_id IS NOT NULL ' . PHP_EOL 
             . ' AND dv.DatumVon = :dateBis AND dv.DatumBis = :dateVon ' . PHP_EOL 
             . ' AND dv.ZeitVon <= :timeBis AND dv.ZeitBis >= :timeVon ' . PHP_EOL 
             . ' GROUP BY werkzeug_id ' . PHP_EOL
                ;  
        
        $sqlSelectCount = 'SELECT COUNT(DISTINCT(' . $key . ')) ' . PHP_EOL;
        $sqlSelect   = 'SELECT r.*, lk.leistungs_name, lk.kosten_pro_einheit, xt.extern_firma, '
             . ' GROUP_CONCAT(DISTINCT c.name ORDER BY c.category_id SEPARATOR \';\') categories, '
             . ' GROUP_CONCAT(DISTINCT c.category_id ORDER BY c.category_id SEPARATOR \';\') category_ids ' . PHP_EOL;
        $sql = ' FROM ' . $tblR . ' r ' . PHP_EOL
             . ' LEFT JOIN ' . $tblLK . ' lk ON(r.leistungs_id = lk.leistungs_id) ' . PHP_EOL
             . ' LEFT JOIN ' . $tblEx . ' xt ON(r.extern_id = xt.extern_id) ' . PHP_EOL
             . ' LEFT JOIN ' . $tblCL . ' cl ON(r.' . $key . ' = cl.werkzeug_id) ' . PHP_EOL
             . ' LEFT JOIN ' . $tblC  . ' c ON(cl.category_id = c.category_id) ' . PHP_EOL
             . ' WHERE ' . PHP_EOL
             . ' (r.extern_id IS NULL OR r.extern_id = 0 OR ' . $key . ' IN(' . $sqlDZ . ')) ' . PHP_EOL 
             . ' AND ' . $key . ' NOT IN(' . $sqlSZ . ')  ' . PHP_EOL 
             . ' AND ' . $key . ' NOT IN(' . $sqlDM . ') ' . PHP_EOL
             ;
        
        if ($categoryTerm) {
            /* @var $ctgLink Model_FuhrparkCategoriesLnk */
            $ctgLink = MyProject_Model_Database::loadModel('werkzeugCategoriesLnk');
            $categorieSubSql = $ctgLink->getCategorySubSql($categoryTerm);
            $sql.= ' AND ' . $key . ' IN (' . $categorieSubSql . ')';
        }
        
        $whereFilter = $modelR->getWhereByJGridFilter($filter, 'r');
        // die(print_r($whereFilter,1));
        if ($whereFilter) {
            $sql.= ' AND ' . $whereFilter->where;        
        }
        
        $sqlGroupBy = ' GROUP BY ' . $key;
        
        $page = (!empty($listOptions['page']) ? (int)$listOptions['page'] : 1);
        $limit = (!empty($listOptions['rows']) ? min((int)$listOptions['rows'],100) : 50);
        $offset = (!empty($listOptions['page']) ? ($page-1)*$limit : 0);
        $sqlLimit = ' LIMIT ' . $offset . ', ' . $limit;
        
        if (!empty($listOptions['sidx'])) {
            $sord = (empty($listOptions['sord']) 
                     || !in_array(strtoupper($listOptions['sord']),array('ASC','DESC')))
                     ? 'ASC'
                     : $listOptions['sord'];
            $sqlOrderBy = ' ORDER BY ' . $listOptions['sidx'] . ' ' . $sord;
        } else {
            $sqlOrderBy = '';
        }
        
        $params = array(
            ':typ' => $this->_resourceType,
            ':dateVon' => $von->format('Y-m-d'),
            ':dateBis' => $bis->format('Y-m-d'),
            ':timeVon' => $von->format('H:i:s'),
            ':timeBis' => $bis->format('H:i:s'),
        );
        
        if (0) {
            $qParams = array();
            foreach($params as $k => $v) $qParams[$k] = $db->quote($v);
            echo strtr($sqlSelect . $sql . $sqlGroupBy . $sqlOrderBy . $sqlLimit, $qParams) . PHP_EOL;
        }
        
        $re = new stdClass();
        $re->total = $db->fetchOne($sqlSelectCount . $sql, $params );
        $re->rows = $db->fetchAll($sqlSelect . $sql . $sqlGroupBy . $sqlOrderBy . $sqlLimit, $params );
        $re->page = $page;
        $re->records = count($re->rows);
        return $re;
    }
    
    /**
     *@todo Abfrage freier Resourcen in Model verlagern, statt kompletter
     * Logik im Controller zu erstellen. Too wet !!!
     */
    public function getFreeResources($prmFilter, $prmPager = array() ) 
    {
        $return = new stdClass;
        
        $tblLinkModelName = 'werkzeugCategoriesLnk';
        $tblRsrcModelName = 'werkzeug';
        
        $filterDefaults = array(
            'tour_id'  => '',
            'DatumVon' => '',
            'DatumBis' => '',
            'ZeitVon'  => '',
            'ZeitBis'  => '',
            'categoryTerm' => '', 
            'rsrcSqlWhere' => '',
        );
        
        $pagerDefaults = array(
            'page' => 1,
            'rows' => 100,
            'sidx'  => null,
            'sord'  => 'ASC',            
        );
        
        $filter = array_merge($filterDefaults, $prmFilter);
        $pager  = array_merge($pagerDefaults,  $prmPager);
        
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        $storage = MyProject_Model_Database::loadStorage($tblRsrcModelName);
        
        $mainTbl = $storage->info(Zend_Db_Table::NAME);
        $mainKey = current($storage->info(Zend_Db_Table::PRIMARY));
        
        $page  = (int) $pager['page'];
        $limit = (int) $pager['rows'];
        $sidx  = $pager['sidx'];
        $sord  = $pager['sord'];
        
        // Get Category-Term and create Sub-Sql
        $categoryTerm = $filter['categoryTerm'];
        $categorieSubSql = '';
        
        if ($categoryTerm) {
            /* @var $ctgLink Model_FuhrparkCategoriesLnk */
            $ctgLink = MyProject_Model_Database::loadModel($tblLinkModelName);
            $categorieSubSql = $ctgLink->getCategorySubSql($categoryTerm);
        }  
        
        $rsrcModel = new Model_TourenDispoMitarbeiter();
        $subSql = $rsrcModel->getTourResourceFilterSql($filter);
        $return->subSqlNew = $subSql;
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) 
            $sord = 'ASC';
        
        $sqlWhere = $filter['rsrcSqlWhere']; // JqGridSearch::getSqlBySearch($TblCnf, $opt);
        if ($categorieSubSql) {
            $sqlWhere.= ($sqlWhere?' AND ':'') . ' ' . $this->_prmRsrcKey . ' IN(' . $categorieSubSql . ') ';
        }
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl, new Zend_Db_Expr('COUNT(*) AS count'));
        if ($sqlWhere) $select->where ($sqlWhere);
        if ($subSql) $select->where($mainKey. ' NOT IN('.$subSql.')');
//        die($select->assemble());
        $count = $db->fetchOne($select);

        $total_pages = ($count > 0) ? ceil($count / $limit) : 0;
        if ($page > $total_pages) $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        /* @var $select Zend_Db_Table_Select */
        $select = $storage->select($withFromPart = true);
        if ($sqlWhere)  $select->where ($sqlWhere);
        if ($subSql)    $select->where($mainKey. ' NOT IN('.$subSql.')');
        if ($sidx)      $select->order( $sidx . ' ' . $sord );
        $select->limit($limit, $start);        
//        echo '#' . __LINE__ . ' ' . $select->assemble() . '<br/>' . PHP_EOL;
        
        /* @var $result Zend_Db_Statement */
        $result = $db->query($select);
        
        $return->subSqlOld = $subSql;
        $return->page = $page;
        $return->total = $total_pages;
        $return->records = $count;
        $return->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);
        $aRsrcIds = array_column($return->rows, $this->_tblRsrcKey);
        // die('<pre>'. $return->sql . '<br/>'.PHP_EOL . print_r([$this->rsrcKey, $aRsrcIds, $return->rows],1) . '</pre>');

        /** @var Model_Fuhrpark $modelRsrc */
        $modelRsrc = Model_Fuhrpark::getSingleton();
        $aCategoriesByRsrcId = $modelRsrc->fetchCategoriesByRsrcIds($aRsrcIds);
        foreach($return->rows as $i => $row ) {
            // $return->rows[$i]['categories'] = $modelRsrc->fetchCategoriesByRow( $row )->toArray();
            $_rsrcId = $row[ $this->_tblRsrcKey ];
            $return->rows[$i]['categories'] = $aCategoriesByRsrcId[$_rsrcId] ?? [];
        }

        return $return;
    }
}
