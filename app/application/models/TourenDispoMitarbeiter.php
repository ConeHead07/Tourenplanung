<?php

class Model_TourenDispoMitarbeiter extends Model_TourenDispoResourceAbstract
{
    protected $_storageName   = 'tourenDispoMitarbeiter';
    protected $_resourceName  = 'Mitarbeiter';
    protected $_resourceModel = 'Model_Mitarbeiter';
    protected $_resourceType  = 'MA';
    protected $_prmRsrcKey    = 'mid';
    protected $_rsrcLnkKey    = 'mitarbeiter_id';
    
    protected $_rsrcTitleField = 'CONCAT(vorname, " ", name)';
        
    public function updateAufwand($rows)
    {        
        $storage = $this->getStorage();
        
        foreach($rows as $_id => $_row) {
            $record = $storage->find($_id)->current();
            if (!$record) { return false; }
            if (strpos($_row['einsatzdauer'], ',')) {
                $_row['einsatzdauer'] = (float)str_replace(',', '.', $_row['einsatzdauer']);
                $_row['einsatzdauer'] = intval($_row['einsatzdauer']).':'.((intval($_row['einsatzdauer'] * 60)) % 60);
            }
            $record->setFromArray(array(
                'einsatzdauer' => implode(':', array_slice(explode(':',$_row['einsatzdauer'].':00:00'),0,3)),
                'kosten' => str_replace(",", ".", $_row['kosten'])
            ))->save();
        }
    }    
        
    /**
     *@todo Abfrage freier Resourcen in Model verlagern, statt kompletter
     * Logik im Controller zu erstellen. Too wet !!!
     */
    public function getFreeResources($prmFilter, $prmPager = array() ) 
    {
        $return = new stdClass;
        
        $tblLinkModelName = 'mitarbeiterCategoriesLnk';
        $tblRsrcModelName = 'mitarbeiter';
        
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
            'sidx'  => 'name, vorname',
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
        if ($sidx)      $select->order( new Zend_Db_Expr($sidx . ' ' . $sord) );
        $select->limit($limit, $start);        
//        echo '#' . __LINE__ . ' ' . $select->assemble() . '<br/>' . PHP_EOL;
        
        /* @var $result Zend_Db_Statement */
        $result = $db->query($select);
        
        $return->sql = $select->assemble();
        $return->subSqlOld = $subSql;
        $return->page = $page;
        $return->total = $total_pages;
        $return->records = $count;
        $return->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);
        
//        die('<pre>'. $return->sql . '<br/>'.PHP_EOL . print_r($return->rows,1) . '</pre>');
        
        $modelRsrc = MyProject_Model_Database::loadModel($tblRsrcModelName);
        foreach($return->rows as $i => $row ) {
            $return->rows[$i]['categories'] = $modelRsrc->fetchCategoriesByRow( $row )->toArray();
        }
        
        return $return;
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
        $modelMA = MyProject_Model_Database::loadModel('mitarbeiter');
        $tblMA = $modelMA->getStorage()->info( Zend_Db_Table::NAME );
        $tblSZ = MyProject_Model_Database::loadModel('resourcesSperrzeiten')->getStorage()->info(Zend_Db_Table::NAME);
        $tblDZ = MyProject_Model_Database::loadModel('resourcesDispozeiten')->getStorage()->info(Zend_Db_Table::NAME);
        $tblTM = MyProject_Model_Database::loadModel('tourenDispoMitarbeiter')->getStorage()->info(Zend_Db_Table::NAME);
        $tblDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge')->getStorage()->info(Zend_Db_Table::NAME);
        
        $tblLK = MyProject_Model_Database::loadModel('leistung')->getStorage()->info(Zend_Db_Table::NAME);
        $tblEx = MyProject_Model_Database::loadModel('extern')->getStorage()->info(Zend_Db_Table::NAME);
        $tblCL = MyProject_Model_Database::loadModel('mitarbeiterCategoriesLnk')->getStorage()->info(Zend_Db_Table::NAME);
        $tblC  = MyProject_Model_Database::loadModel('mitarbeiterCategories')->getStorage()->info(Zend_Db_Table::NAME);
        
        $sqlDZ = 'SELECT ressourcen_id FROM ' . $tblDZ . PHP_EOL
             . ' WHERE ressourcen_typ = :typ ' . PHP_EOL
             . ' AND gebucht_von <= :dateBis'
             . ' AND gebucht_bis >= :dateVon';
        
        $sqlSZ = 'SELECT ressourcen_id FROM ' . $tblSZ . '  '
             . ' WHERE ressourcen_typ = :typ ' . PHP_EOL
             . ' AND gesperrt_von <= :dateBis'
             . ' AND gesperrt_bis >= :dateVon';
        
        $sqlDM = 'SELECT mitarbeiter_id FROM ' . $tblDV . ' dv '  . PHP_EOL
             . ' LEFT JOIN ' . $tblTM . ' mt ON(dv.tour_id = mt.tour_id) ' . PHP_EOL
             . ' WHERE ' . PHP_EOL
             . ' mitarbeiter_id IS NOT NULL ' . PHP_EOL 
             . ' AND dv.DatumVon = :dateBis AND dv.DatumBis = :dateVon ' . PHP_EOL 
             . ' AND dv.ZeitVon <= :timeBis AND dv.ZeitBis >= :timeVon ' . PHP_EOL 
             . ' GROUP BY mitarbeiter_id ' . PHP_EOL
                ;  
        
        $sqlSelectCount = 'SELECT COUNT(DISTINCT(' . $key . ')) ' . PHP_EOL;
        $sqlSelect = 'SELECT r.*, lk.leistungs_name, lk.kosten_pro_einheit, xt.extern_firma, ' . PHP_EOL
             . ' GROUP_CONCAT(DISTINCT c.name ORDER BY c.category_id SEPARATOR \';\') categories, ' . PHP_EOL
             . ' GROUP_CONCAT(DISTINCT c.category_id ORDER BY c.category_id SEPARATOR \';\') category_ids ' . PHP_EOL;
        $sql = ' FROM ' . $tblMA . ' r ' . PHP_EOL
             . ' LEFT JOIN ' . $tblLK . ' lk ON(r.leistungs_id = lk.leistungs_id) ' . PHP_EOL
             . ' LEFT JOIN ' . $tblEx . ' xt ON(r.extern_id = xt.extern_id) ' . PHP_EOL
             . ' LEFT JOIN ' . $tblCL . ' cl ON(r.mid = cl.mitarbeiter_id) ' . PHP_EOL
             . ' LEFT JOIN ' . $tblC  . ' c ON(cl.category_id = c.category_id) ' . PHP_EOL
             . ' WHERE ' . PHP_EOL
             . ' (r.extern_id IS NULL OR r.extern_id = 0 OR ' . $key . ' IN(' . $sqlDZ . ')) ' . PHP_EOL 
             . ' AND ' . $key . ' NOT IN(' . $sqlSZ . ')  ' . PHP_EOL 
             . ' AND ' . $key . ' NOT IN(' . $sqlDM . ') ' . PHP_EOL
             ;
        
        if ($categoryTerm) {
            /* @var $ctgLink Model_FuhrparkCategoriesLnk */
            $ctgLink = MyProject_Model_Database::loadModel('mitarbeiterCategoriesLnk');
            $categorieSubSql = $ctgLink->getCategorySubSql($categoryTerm);
            $sql.= ' AND ' . $key . ' IN (' . $categorieSubSql . ')';
        }
        
        $whereFilter = $modelMA->getWhereByJGridFilter($filter, 'r');
        // die(print_r($whereFilter,1));
        if ($whereFilter && !empty($whereFilter->where) ) {
            $sql.= ' AND ' . $whereFilter->where;        
        }
        
        $sqlGroupBy = ' GROUP BY ' . $key . '';
        
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
}

$SQL=<<<EOT
SELECT CONCAT(m.name, "; extern_id: ", m.extern_id) ma 
FROM mr_mitarbeiter m 
LEFT JOIN mr_ressourcen_leistungskatalog lk ON(m.leistungs_id = lk.leistungs_id) 
LEFT JOIN mr_extern x ON(m.extern_id = x.extern_id) 
WHERE 
    ( m.extern_id IS NULL 
      OR m.extern_id = 0 
      OR mid IN(
        SELECT ressourcen_id FROM mr_ressourcen_dispozeiten 
        WHERE ressourcen_typ = 'MA' AND gebucht_von <= '2014-09-22' AND gebucht_bis >= '2014-09-22')
    )
    
    AND mid NOT IN(
        SELECT ressourcen_id FROM mr_ressourcen_sperrzeiten 
        WHERE ressourcen_typ = 'MA' AND gesperrt_von <= '2014-09-22' AND gesperrt_bis >= '2014-09-22'
    ) 
    
   AND mid NOT IN(
       SELECT mitarbeiter_id FROM mr_touren_dispo_vorgaenge dv 
       LEFT JOIN mr_touren_dispo_mitarbeiter mt ON(dv.tour_id = mt.tour_id) 
       WHERE dv.DatumVon = '2014-09-22' AND dv.DatumBis = '2014-09-22' 
             AND dv.ZeitVon <= '13:00:00' AND dv.ZeitBis >= '11:30:00'
             GROUP BY mitarbeiter_id 
    ) 
        
   LIMIT 0, 100 

EOT;
