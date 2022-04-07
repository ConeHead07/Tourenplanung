<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of userController
 * @author rybka
 */
class MitarbeiterController extends Zend_Controller_Action 
{

    // Initialize ActionController
    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_model = MyProject_Model_Database::loadModel('mitarbeiter');
        $this->_storage = $this->_model->getStorage();
        
        /* @var $this->_tourModel Model_TourenDispoVorgaenge */
        $this->_tourModel = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        /* @var $this->_tourStorage Model_Db_TourenDispoVorgaenge */
        $this->_tourStorage = $this->_tourModel->getStorage();

        // response-Objekt f�r den View
        $this->_rsp = new stdClass();

        /* @var $request Zend_Controller_Request_Abstract */
        $rq = $this->getRequest();
    }
    //put your code here
    public function indexAction() {
        $db = Zend_Registry::get('db');
        $user = MyProject_Model_Database::loadModel('mitarbeiter');
        $this->view->vorgaengelist = $user->fetchEntries();
    }

    public function listAction() {
        $modelLg = new Model_Lager();
        $this->view->lagerOptionsList = $modelLg->getAssocLagerNames();
    }

    public function datalistAction() {
        /* @var $model Model_Fuhraprk */
        $model = MyProject_Model_Database::loadModel('mitarbeiter');
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
        $return->data = $data;
        
        try {
            switch($op) {
                case 'edit':
                    if (isset($data['extern_id'])) {
                        $data['extern_id'] = (int)$data['extern_id'];
                    }
                    if (isset($data['menge'])) {
                        $data['menge'] = (int)$data['menge'];
                    }
                    if ($this->_model->update($data, $id)) {
                        $return->type = 'success';
                    } else {
                        $return->err = 'Datensatz konnte nicht aktualisiert werden!';
                    }
                    break;

                case 'add':
                    $return->post = $_POST;
                    if (empty($data['extern_id'])) {
                        $data['extern_id'] = 0;
                    }
                    if (empty($data['extern_id'])) {
                        $data['leistungs_id'] = 0;
                    }
                    if (empty($data['menge'])) {
                        $data['menge'] = 1;
                    }
                    if (empty($data['urlaubsanspruch'])) {
                        $data['urlaubsanspruch'] = 0;
                    }
                    $return->id = $this->_model->insert($data);
                    $return->data = $data;
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

        $this->view->return = $return;
        
        // $this->_helper->json($return);
    }
    
    public function gridresponsedata2Action() 
    {
        
        $TblCnf = include APPLICATION_PATH . '/configs/dbtables/mitarbeiter.inc.php';        
        $TblCnfParser = MyProject_Parser_TableConf::getInstance();
        $TblCnfParser->parse_conf($TblCnf);

        $rq = $this->getRequest();
        
        $pager['page']  = (int) $rq->getParam('page', 1);
        $pager['limit'] = (int) $rq->getParam('rows', 100);
        $pager['sidx']  = $rq->getParam('sidx', null);
        $pager['sord']  = $rq->getParam('sord', 'ASC');
        
        $filter = $rq->getParams();
        
        $filter['tour'] = $rq->getParam('tour', '');
        if ($filter['tour']) $filter['tour_id'] = $filter['tour'];
        if (!$rq->getParam('DatumVon') && $rq->getParam('date')) {
            $filter['DatumVon'] = $rq->getParam('date');
        }        
        
        // Get Category-Term and create Sub-Sql
        $filter['categoryTerm'] = '';
        $gridFilter = json_decode($rq->getParam('filters', null));
//        die( print_r($gridFilter,1));
        if (is_object($gridFilter) && property_exists($gridFilter, 'rules')) {            
            foreach($gridFilter->rules as $_k => $_v) {
                if ($_v->field == 'categories') { $filter['categoryTerm'] = $_v->data; break; }
                if ($_v->field == 'kategorie')  { $filter['categoryTerm'] = $_v->data; break; }
            }
        }
        
        if (!in_array(strtoupper($pager['sord']), array('ASC', 'DESC'))) 
            $pager['sord'] = 'ASC';
        
        $opt = array("additionalFields" => array());
        $filter['rsrcSqlWhere'] = JqGridSearch::getSqlBySearch($TblCnf, $opt);
                
        $this->_rsp = MyProject_Model_Database::loadModel('tourenDispoMitarbeiter')->getFreeResources($filter, $pager );
        
        $this->_helper->json($this->_rsp);
    }
    
    public function listavaiablesAction() 
    {        
        $rq = $this->getRequest();
        $sord    = $rq->getParam('sord', 'ASC');
        $filters = $rq->getParam('filters', '');
        
        $extFilter = $rq->getParam('extFilter', 'all');
        $tourId  = (int) $rq->getParam('tour', '');
        
        if ($tourId) {
            $modelTDV = new Model_TourenDispoVorgaenge();
            $tourData = (object)$modelTDV->fetchEntry($tourId);
            if ($tourData && $tourData->DatumVon) {
                $dVon = $tourData->DatumVon;
                $dBis = $tourData->DatumBis;
                $zVon = $tourData->ZeitVon;
                $zBis = $tourData->ZeitBis;
            }
        } else {
            $dVon    = $rq->getParam('DatumVon') || $rq->getParam('date');
            $dBis    = $rq->getParam('DatumBis');
            $zVon    = $rq->getParam('ZeitVon');
            $zBis    = $rq->getParam('ZeitBis');
        }
                
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) {
            $sord = 'ASC';
        }
        
        $listOptions = array(
            'page' => (int) $rq->getParam('page', 1),
            'rows' => (int) $rq->getParam('rows', 100),
            'sidx' => $rq->getParam('sidx', null),
            'sord' => $sord,
        );
        
        
        $categoryTerm = '';
        if ( $filters ) {
            $objFilters = json_decode($filters);
            foreach($objFilters->rules as $_i => &$_r) {
                if ($_r->field == "extern_id") {
                    $_r->field = (is_numeric($_r->data) ? $_r->field  : "extern_firma");
                }
                if ($_r->field == "leistungs_id") {
                    $_r->field = (is_numeric($_r->data) ? $_r->field  : "leistungs_name");
                }
                
                if (preg_match('/^(categories|kategorie)$/', $_r->field)) { $categoryTerm = $_r->data; }
            }            
            $rq->setParam('filters', $objFilters );
        } else {
            $objFilters = (object)array('groupOp'=>'AND', 'rules'=>array());
        }
        
        if ($extFilter && preg_match('/^(int|ext)$/', $extFilter) ) {
            $objFilters->rules[] = (object)array(
                'field' => 'extern_id',
                'op' =>  $extFilter == 'int' ? 'nn' : 'nu',
                'data' => '',
            );
        }
//        echo '<pre>' . print_r(array('dVon'=>$dVon, 'zVon' => $zVon, 'dBis' => $dBis, 'zBis' => $zBis),1) . '</pre>' . PHP_EOL;
//        echo strtotime($dVon.' '.$zVon) . ' => ' . date('Y-m-d H:i:s', strtotime($dVon.' '.$zVon)) . '<br>' . PHP_EOL;
        $modelTDM = new Model_TourenDispoMitarbeiter();
        
        $re = $modelTDM->listFreeResources(
                new DateTime(date('Y-m-d H:i:s', strtotime($dVon.' '.$zVon))),
                new DateTime(date('Y-m-d H:i:s', strtotime($dBis.' '.$zBis))),
                $objFilters, 
                $listOptions);
        
        $this->_helper->json($re);
    }
    
    
    
    /**
     *@todo Abfrage freier Resourcen in Model verlagern, statt kompletter
     * Logik im Controller zu erstellen. Too wet !!!
     */
    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        /* @var $request Zend_Controller_Request_Abstract */
        $rq = $this->getRequest();
        
        $modelLstg = new Model_Leistung();
        $tblLstg   = $modelLstg->getStorage()->info(Zend_Db_Table::NAME);
        
        $modelDz = new Model_ResourcesDispozeiten();
        $tblDz   = $modelDz->getStorage()->info(Zend_Db_Table::NAME);
        
        $modelSz = new Model_ResourcesSperrzeiten();
        $tblSz   = $modelSz->getStorage()->info(Zend_Db_Table::NAME);
        
        $mainTbl = $this->_storage->info(Zend_Db_Table::NAME);
        $mainKey = current($this->_storage->info(Zend_Db_Table::PRIMARY));
        
        $TblCnf = include APPLICATION_PATH . '/configs/dbtables/mitarbeiter.inc.php';        
        $TblCnfParser = MyProject_Parser_TableConf::getInstance();
        $TblCnfParser->parse_conf($TblCnf);
        
        $page    = (int) $rq->getParam('page', 1);
        $limit   = (int) $rq->getParam('rows', 100);
        $sidx    = $rq->getParam('sidx', null);
        $sord    = $rq->getParam('sord', 'ASC');
        $pid     = $rq->getParam('pid', '');
        $filters = $rq->getParam('filters', '');
        $extFilter = $rq->getParam('extFilter', 'all');
        $objFilters = null;
        
        if ( $filters ) {
            $objFilters = json_decode($filters);
            foreach($objFilters->rules as $_i => &$_r) {
                if ($_r->field == "extern_id") {
                    $_r->field = (is_numeric($_r->data) ? $mainTbl .'.' . $_r->field  : "extern_firma");
                }
                if ($_r->field == "leistungs_id") {
                    $_r->field = (is_numeric($_r->data) ? $mainTbl .'.' . $_r->field  : "leistungs_name");
                }
            }            
            $rq->setParam('filters', $objFilters );
        }
        
        $dVon = $rq->getParam('DatumVon');
        $dBis = $rq->getParam('DatumBis');
        //die(print_r($objFilters, 1));
        //die(print_r($rq->getParam('filters'), 1));
        
        $tourId = $rq->getParam('tour', '');
        $filter = $rq->getParams();
        if ($tourId) $filter['tour_id'] = $tourId;
        if (!$rq->getParam('DatumVon') && $rq->getParam('date')) {
            $filter['DatumVon'] = $rq->getParam('date');
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
            $ctgLink = MyProject_Model_Database::loadModel('mitarbeiterCategoriesLnk');
            $categorieSubSql = $ctgLink->getCategorySubSql($categoryTerm);
        }  
        
        $rsrcModel = new Model_TourenDispoMitarbeiter();
        $subSql = $rsrcModel->getTourResourceFilterSql($filter);
        $this->_rsp->subSqlNew = $subSql;
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) 
            $sord = 'ASC';
        
        $opt = array("additionalFields" => array($mainTbl.'.extern_id', 'extern_firma',$mainTbl.'.leistungs_id', 'leistungs_name') ); //
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        if ($categorieSubSql) {
            $sqlWhere.= ($sqlWhere?' AND ':'') . ' mid IN(' . $categorieSubSql . ') ';
        }
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl, new Zend_Db_Expr('COUNT(*) AS count'));
        $select->joinLeft(array('e'=>'mr_extern'), "e.extern_id = $mainTbl.extern_id", '');
        $select->joinLeft(array('l'=> $tblLstg), "l.leistungs_id = $mainTbl.leistungs_id", '');
        if ($sqlWhere) $select->where ($sqlWhere);
		
        if ($extFilter == 'int') {
            if ($subSql) $select->where($mainKey. ' NOT IN('.$subSql.')');
            $select->where(
                '('.$mainTbl. '.extern_id IS NULL OR ' . $mainTbl. '.extern_id = 0)' );
        }
        if ($extFilter == 'ext') {
            $select->where( $mainTbl. '.extern_id > 0' );
            if ($dVon || $dBis) {
                $select->joinLeft(array('dz' => $tblDz), " dz.ressourcen_typ = 'MA' AND mid = dz.ressourcen_id");
                if ($dVon) $select->where($db->quoteInto(
                    ' gebucht_von <= ? AND gebucht_bis >= ? ', $dVon));

                if ($dBis) $select->where($db->quoteInto(
                    ' gebucht_von <= ? AND gebucht_bis >= ? ', $dBis ));
            }
        }
		
        $count = $db->fetchOne($select);

        $total_pages = ($count > 0) ? ceil($count / $limit) : 0;
        if ($page > $total_pages) $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl );
        $select->joinLeft(array('e'=>'mr_extern'), "e.extern_id = $mainTbl.extern_id", 'extern_firma');
        $select->joinLeft(array('l'=> $tblLstg),   "l.leistungs_id = $mainTbl.leistungs_id", 'leistungs_name');
        if ($sqlWhere)  $select->where ($sqlWhere);
        
        if ($extFilter == 'int') {
            if ($subSql)    $select->where($mainKey. ' NOT IN('.$subSql.')');
            $select->where(
                '('.$mainTbl. '.extern_id IS NULL OR ' . $mainTbl. '.extern_id = 0)' );
        }
        
        if ($extFilter == 'ext') {
            $select->where( $mainTbl. '.extern_id > 0' );
            if ($dVon || $dBis) {
                $select->joinLeft(array('dz' => $tblDz), " dz.ressourcen_typ = 'MA' AND mid = dz.ressourcen_id");
                if ($dVon) $select->where($db->quoteInto(
                    ' gebucht_von <= ? AND gebucht_bis >= ? ', $dVon));

                if ($dBis) $select->where($db->quoteInto(
                    ' gebucht_von <= ? AND gebucht_bis >= ? ', $dBis ));
            }
        }
        if ($sidx)      $select->order( $sidx . ' ' . $sord );
        $select->limit($limit, $start);
        //echo '#' . __LINE__ . '<pre>' . $select->assemble() . '</pre>' . PHP_EOL;
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

