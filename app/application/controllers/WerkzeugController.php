<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of userController
 * @author rybka
 */
class WerkzeugController extends MyProject_Controller_RestAbstract
{
    /** @var $_model Model_Werkzeug */
    protected $_model = null;
    
    // Initialize ActionController
    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_model = MyProject_Model_Database::loadModel('werkzeug');
        $this->_storage = $this->_model->getStorage();
        
        /* @var $this->_tourModel Model_TourenDispoVorgaenge */
        $this->_tourModel = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        /* @var $this->_tourStorage Model_Db_TourenDispoVorgaenge */
        $this->_tourStorage = $this->_tourModel->getStorage();

        // response-Objekt f�r den View
        $this->_rsp = new stdClass();

        /* @var $request Zend_Controller_Request_Abstract */
        $this->_request = $this->getRequest();
        
        $this->_request = $this->getRequest();
    }
    
    public function indexAction() {
        $db = Zend_Registry::get('db');
        $user = MyProject_Model_Database::loadModel('werkzeug');
        $this->view->vorgaengelist = $user->fetchEntries();
    }

    public function listAction() {
        $modelLg = new Model_Lager();
        $this->view->lagerOptionsList = $modelLg->getAssocLagerNames();
    }

    public function datalistAction() {
        /* @var $model Model_Fuhraprk */
        $model = MyProject_Model_Database::loadModel('werkzeug');
        $this->view->datalist = $model->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function grideditdataAction()
    {
        $return = new stdClass();
        $return->type = 'error';
        
        $rq = $this->getRequest();
        $data = $rq->getParams();
        $id = $rq->getParam('id', 0);
        $op = $rq->getParam('oper', '');
        
        try {
            switch($op) {
                case 'edit':
                    if ($this->_model->update($data, $id)) {
                        $return->type = 'success';
                    } else {
                        $return->err = 'Datensatz konnte nicht aktualisiert werden!';
                    }
                    break;

                case 'add':
                    $return->id = $this->_model->insert($data);
                    if ($return->id) {
                        $return->type = 'success';
                    } else {
                        $return->err = 'Datensatz konnte nicht angelegt werden!';
                    }
                    break;

                case 'del':
                    if ($this->_model->delete($id)) {
                        $return->type = 'success';
                    } else {
                        $return->err = 'Datensatz konnte nicht geloescht werden!';
                    }
                    break;
                    
                default:
                    $return->err = 'Ungueltiger Operation ' . $op . '! Erwartet: edit, add, del!';
            }
        } catch(Zend_Db_Exception $e) {
            $return->err = $e->getMessage();
        } catch(Exception $e) {
            $return->err = $e->getMessage();
        }
        
        $this->_helper->json($return);
    }
    
    public function listavaiablesAction() 
    {
        /* @var $request Zend_Controller_Request_Abstract */
        $rq = $this->getRequest();
        $oQueryOpts = new MyProject_Model_QueryBuilder();

        $toDateOrNull = function(string $date) {
            $t = strtotime($date);
            return ($t === false || !preg_match('#^\d{4}-\d\d-\d\d$#', $date)) ? null : new DateTime( $date );
        };

        $listPage = (int) $rq->getParam('page', 1);
        $listSize = (int) $rq->getParam('rows', 100);

        $iTourId = $rq->getParam('tour_id', 0);
        if ($iTourId) {
            $aDateTimeRangeByTour = Model_Db_TourenDispoVorgaenge::get($iTourId);
            $this->_require(!empy($aDateTimeRangeByTour), 'No Tour-Record found for given TourId ' . $iTourId, 'json');

            $aDateTimeRange = $aDateTimeRangeByTour;

        } else {

            $datumVon = $rq->getParam('DatumVon', '');
            if (!$datumVon) {
                $datumVon = $rq->getParam('date', '');
            }
            $dateVon = $toDateOrNull($datumVon);

            $this->_require(
                !is_null($dateVon),
                'Missing valid Filter-Value [DatumVon]. Expected Format YYYY-DD-MM, Given: ' . $datumVon,
                'json');

            $aDateTimeRange = [
                'DatumVon' => $dateVon,
                'DatumBis' => $toDateOrNull($rq->getParam('DatumBis', '')),
                'ZeitVon' => $rq->getParam('ZeitVon', ''),
                'ZeitBis' => $rq->getParam('ZeitBis', ''),
            ];
        }

        $aFilter = [
            'extFilter' => $rq->getParam('extFilter', 'all'),
            'tour_id' => $rq->getParam('tour_id', 0),
        ];

        $oQueryOpts
            ->setOrder($rq->getParam('sidx', 'name'))
            ->setOrderDir($rq->getParam('sord', 'ASC'))
            ->setOffset(max(0,$listPage-1) * $listSize)
            ->setLimit($listSize);

        $filters = $rq->getParam('filters', '');
        $objFilters = null;

        $TblCnf = $this->_model->infoToTblConf();

        $mainTbl = Model_Db_Werkzeug::obj()->tableName();

        if ( $filters ) {
            $objFilters = json_decode($filters);
            foreach($objFilters->rules as $_i => &$_r) {
                switch($_r->field) {
                    case 'extern_id':
                        $_r->field = (is_numeric($_r->data) ? $mainTbl .'.' . $_r->field  : "extern_firma");
                        break;

                    case 'leistungs_id':
                        $_r->field = (is_numeric($_r->data) ? $mainTbl .'.' . $_r->field  : "leistungs_name");
                        break;

                    case 'categories':
                    case 'kategorie':
                        $aFilter['categoryTerm'] = $_r->data;
                        break;

                }
            }
            $rq->setParam('filters', $objFilters );
        }

        $opt = array("additionalFields" => array($mainTbl.'.extern_id', 'extern_firma',$mainTbl.'.leistungs_id', 'leistungs_name') ); //

        $oQueryOpts->setWhere(
            JqGridSearch::getSqlBySearch($TblCnf, $opt)
        );

        $oResultOptions = (new Model_TourenDispoWerkzeug())->getListOfAvailableItems(
            $aDateTimeRange, $aFilter, $oQueryOpts
        );

        $this->sendRawJson([
            'page' => $oResultOptions->getPage(),
            'total' => $oResultOptions->getTotalPages(),
            'rows' => $oResultOptions->getRows(),
            'records' => $oResultOptions->getTotal(),
            'sql' => $oResultOptions->getSql(),
            'logs' => $oResultOptions->getLogs(),
        ]);
    }


    public function gridresponsedata_NEU_ONLY_AVAIABLES_Action()
    {
        /* @var $request Zend_Controller_Request_Abstract */
        $rq = $this->getRequest();
        $oQueryOpts = new MyProject_Model_QueryBuilder();

        $toDateOrNull = function(string $date) {
            $t = strtotime($date);
            return ($t === false || !preg_match('#^\d{4}-\d\d-\d\d$#', $date)) ? null : new DateTime( $date );
        };

        $listPage = (int) $rq->getParam('page', 1);
        $listSize = (int) $rq->getParam('rows', 100);

        $iTourId = $rq->getParam('tour_id', 0);
        if ($iTourId) {
            $aDateTimeRangeByTour = Model_Db_TourenDispoVorgaenge::get($iTourId);
            $this->_require(!empy($aDateTimeRangeByTour), 'No Tour-Record found for given TourId ' . $iTourId, 'json');

            $aDateTimeRange = $aDateTimeRangeByTour;

        } else {

            $datumVon = $rq->getParam('DatumVon', '');
            if (!$datumVon) {
                $datumVon = $rq->getParam('date', '');
            }
            $dateVon = $toDateOrNull($datumVon);

            $this->_require(
                !is_null($dateVon),
                'Missing valid Filter-Value [DatumVon]. Expected Format YYYY-DD-MM, Given: ' . $datumVon,
                'json');

            $aDateTimeRange = [
                'DatumVon' => $dateVon,
                'DatumBis' => $toDateOrNull($rq->getParam('DatumBis', '')),
                'ZeitVon' => $rq->getParam('ZeitVon', ''),
                'ZeitBis' => $rq->getParam('ZeitBis', ''),
            ];
        }

        $aFilter = [
            'extFilter' => $rq->getParam('extFilter', 'all'),
            'tour_id' => $rq->getParam('tour_id', 0),
        ];

        $oQueryOpts
            ->setOrder($rq->getParam('sidx', 'name'))
            ->setOrderDir($rq->getParam('sord', 'ASC'))
            ->setOffset(max(0,$listPage-1) * $listSize)
            ->setLimit($listSize);

        $filters = $rq->getParam('filters', '');
        $objFilters = null;

        $TblCnf = $this->_model->infoToTblConf();

        $mainTbl = Model_Db_Werkzeug::obj()->tableName();

        if ( $filters ) {
            $objFilters = json_decode($filters);
            foreach($objFilters->rules as $_i => &$_r) {
                switch($_r->field) {
                    case 'extern_id':
                        $_r->field = (is_numeric($_r->data) ? $mainTbl .'.' . $_r->field  : "extern_firma");
                        break;

                    case 'leistungs_id':
                        $_r->field = (is_numeric($_r->data) ? $mainTbl .'.' . $_r->field  : "leistungs_name");
                        break;

                    case 'categories':
                    case 'kategorie':
                        $aFilter['categoryTerm'] = $_r->data;
                        break;

                }
            }
            $rq->setParam('filters', $objFilters );
        }

        $opt = array("additionalFields" => array($mainTbl.'.extern_id', 'extern_firma',$mainTbl.'.leistungs_id', 'leistungs_name') ); //

        $oQueryOpts->setWhere(
            JqGridSearch::getSqlBySearch($TblCnf, $opt)
        );

        $oResultOptions = (new Model_TourenDispoWerkzeug())->getListOfAvailableItems(
            $aDateTimeRange, $aFilter, $oQueryOpts
        );

        $this->sendRawJson([
            'page' => $oResultOptions->getPage(),
            'total' => $oResultOptions->getTotalPages(),
            'rows' => $oResultOptions->getRows(),
            'records' => $oResultOptions->getTotal(),
            'sql' => $oResultOptions->getSql(),
            'logs' => $oResultOptions->getLogs(),
        ]);
    }

    public function gridresponsedataAction()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        /* @var $request Zend_Controller_Request_Abstract */
        $this->_request = $this->getRequest();
        
        $mainTbl = $this->_storage->info(Zend_Db_Table::NAME);
        $mainKey = current($this->_storage->info(Zend_Db_Table::PRIMARY));

        $TblCnf = $this->_model->infoToTblConf();
        
        $page  = (int) $this->_request->getParam('page', 1);
        $limit = (int) $this->_request->getParam('rows', 100);
        $sidx  = $this->_request->getParam('sidx', null);
        $sord  = $this->_request->getParam('sord', 'ASC');
        $pid   = $this->_request->getParam('pid', '');
        $filters = $this->_request->getParam('filters', '');
        $extFilter = $this->_request->getParam('extFilter', 'all');
        $objFilters = null;
        
        if ( $filters ) {
            $objFilters = json_decode($filters);
            foreach($objFilters->rules as $_i => $_r) {
                if ($_r->field == "extern_id") {
                    $_r->field = (is_numeric($_r->data) ? $mainTbl .'.' . $_r->field  : "extern_firma");
                }
            }            
            $this->_request->setParam('filters', $objFilters );
        }
        
        $tourId = $this->_request->getParam('tour', '');
        $filter = $this->_request->getParams();
        if ($tourId) $filter['tour_id'] = $tourId;
        if (!$this->_request->getParam('DatumVon') && $this->_request->getParam('date')) {
            $filter['DatumVon'] = $this->_request->getParam('date');
        }        
        
        // Get Category-Term and create Sub-Sql
        $categoryTerm = '';
        $categorieSubSql = '';
        $gridFilter = $objFilters;
//        die( print_r($gridFilter,1));
        if (is_object($gridFilter) && property_exists($gridFilter, 'rules')) {            
            foreach($gridFilter->rules as $_k => $_v) {
                if ($_v->field == 'categories') { $categoryTerm = $_v->data; break; }
                if ($_v->field == 'kategorie')  { $categoryTerm = $_v->data; break; }
            }
        }
        
        if ($categoryTerm) {
            /* @var $ctgLink Model_FuhrparkCategoriesLnk */
            $ctgLink = MyProject_Model_Database::loadModel('werkzeugCategoriesLnk');
            $categorieSubSql = $ctgLink->getCategorySubSql($categoryTerm);
        }  
        
        $rsrcModel = new Model_TourenDispoWerkzeug();
        //$subSql = $rsrcModel->getTourResourceFilterSql($filter);
        $subSql = $rsrcModel->getTourResourceFilterSql0($filter);
        $this->_rsp->subSqlNew = $subSql;
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) 
            $sord = 'ASC';
        
        $opt = array("additionalFields" => array($mainTbl.'.extern_id', 'extern_firma') ); //
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        if ($categorieSubSql) {
            $sqlWhere.= ($sqlWhere?' AND ':'') . ' wid IN(' . $categorieSubSql . ') ';
        }
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl, new Zend_Db_Expr('COUNT(*) AS count'));
        $select->joinLeft(array('e'=>'mr_extern'), "e.extern_id = $mainTbl.extern_id", '');
        if ($sqlWhere) $select->where ($sqlWhere);
		
        
        if ($extFilter == 'int') {
			if ($subSql) $select->where($mainKey. ' NOT IN('.$subSql.')');
			$select->where(
				'('.$mainTbl. '.extern_id IS NULL OR ' . $mainTbl. '.extern_id = 0)' );
		}
        if ($extFilter == 'ext') $select->where( $mainTbl. '.extern_id > 0' );
//        die($select->assemble());
        $count = $db->fetchOne($select);

        $total_pages = ($count > 0) ? ceil($count / $limit) : 0;
        if ($page > $total_pages) $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl );
        $select->joinLeft(array('e'=>'mr_extern'), "e.extern_id = $mainTbl.extern_id", 'extern_firma');
        if ($sqlWhere)  $select->where ($sqlWhere);
        if ($subSql)    $select->where($mainKey. ' NOT IN('.$subSql.')');
        if ($subSql) $select->where($mainKey. ' NOT IN('.$subSql.')');
        if ($extFilter == 'int') $select->where(
            '('.$mainTbl. '.extern_id IS NULL OR ' . $mainTbl. '.extern_id = 0)' );
        if ($extFilter == 'ext') $select->where( $mainTbl. '.extern_id > 0' );
        if ($sidx)      $select->order( $sidx . ' ' . $sord );
        $select->limit($limit, $start);
        
        header('X-Debug-GetWherePartBySearch: '.json_encode($sqlWhere));
        header('X-Debug-SearchRequest: '.json_encode($_REQUEST));
        header('X-Debug-SQL: ' . json_encode($select->assemble()) );
        
        /* @var $result Zend_Db_Statement */
        $result = $db->query($select);
        $num_fields = $result->columnCount();
        
        $this->_rsp->subSqlOld = $subSql;
        $this->_rsp->page = $page;
        $this->_rsp->total = $total_pages;
        $this->_rsp->records = $count;
        $this->_rsp->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);
        
        foreach($this->_rsp->rows as $i => $row ) {
            $this->_rsp->rows[$i]['categories'] = $this->_model->fetchCategoriesByRow( $row )->toArray();
        }
        
        $this->view->gridresponsedata = $this->_rsp;        
    }

}


        

