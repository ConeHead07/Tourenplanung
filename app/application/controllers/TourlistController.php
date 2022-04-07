<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TourlistController
 *
 * @author rybka
 */
class TourlistController extends Zend_Controller_Action 
{
    /** @var $_model MyProject_Model_Database */
    protected $_storage = null;
    protected $_model = null;
    protected $_db = null;
    protected $_rsp = null;
    protected $_request =  null;
    
    // Initialize ActionController
    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        
        /* @var $this->_model Model_TourenDispoVorgaenge */
        $this->_model = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        /* @var $this->_storage Model_Db_TourenDispoVorgaenge */
        $this->_storage = $this->_model->getStorage();

        // response-Objekt für den View
        $this->_rsp = new stdClass();

        /* @var $request Zend_Controller_Request_Abstract */
        $this->_request = $this->getRequest();
    }
    
    public function indexAction()
    {
        include 'JqGrid/JqGridHelper.php';
        
        $jq = new JqGrid_Zend_GridModelCreator();
        
        $storage = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge');
        $this->view->tourlist = new stdClass();
        $this->view->tourlist->tblInfo = $storage->info();
        $this->view->tourlist->cols = $storage->info(Zend_Db_Table::METADATA);
        $this->view->tourlist->grid = $jq->createGridModel('tourenDispoVorgaenge');     
        $this->view->tourlist->grid['url'] = $this->view->baseUrl() . '/tourlist/gridresponsedata';
        
    }
    
    //put your code here
    public function gridresponsedataAction() 
    {
/*
SELECT
DT.tour_id, timeline_id, DatumVon, DatumBis, ZeitVon, ZeitBis,
AK.Vorgangstitel,
AK.LieferungName,
AK.Kundennummer,
COUNT(DISTINCT(AP.Positionsnummer)) NumAP,
COUNT(DISTINCT(DP.Positionsnummer)) NumDP,
COUNT(DISTINCT(DF.fuhrpark_id    )) NumDF,
COUNT(DISTINCT(DM.mitarbeiter_id )) NumDM,
MA.name
FROM 
mr_touren_dispo_vorgaenge DT
LEFT JOIN wws_auftragskoepfe AK USING(Mandant, Auftragsnummer)
LEFT JOIN wws_auftragspositionen AP USING(Mandant, Auftragsnummer)
LEFT JOIN mr_touren_dispo_auftragspositionen DP ON(DT.tour_id = DP.tour_id)
LEFT JOIN mr_touren_dispo_fuhrpark DF ON(DT.tour_id = DP.tour_id)
LEFT JOIN mr_touren_dispo_mitarbeiter DM ON(DM.tour_id = DT.tour_id)
LEFT JOIN mr_mitarbeiter MA ON(MA.mid = DM.mitarbeiter_id)

WHERE
(DatumVon >= "2012-04-16" AND DatumVon <= "2012-04-22")
OR
(DatumBis >= "2012-04-16" AND DatumBis <= "2012-04-22")

GROUP BY DT.tour_id
ORDER BY DT.DatumVon, ZeitVon, DatumBis, ZeitBis
*/
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        $mainTbl = $this->_storage->info(Zend_Db_Table::NAME);
        $mainKey = current($this->_storage->info(Zend_Db_Table::PRIMARY));
        
        $TblCnf = $this->_model->infoToTblConf();
        
        $page  = (int) $this->_request->getParam('page', 1);
        $limit = (int) $this->_request->getParam('rows', 100);
        $sidx  = $this->_request->getParam('sidx', null);
        $sord  = $this->_request->getParam('sord', 'ASC');
        $pid   = $this->_request->getParam('pid', '');
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) 
        $sord = 'ASC';
        
        try {
        $opt = array("additionalFields" => array());
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl, new Zend_Db_Expr('COUNT(*) AS count'));
        if ($sqlWhere) $select->where ($sqlWhere);
//        die($select->assemble());
        $count = $db->fetchOne($select);

        $total_pages = ($count > 0) ? ceil($count / $limit) : 0;
        if ($page > $total_pages) $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        /* @var $select Zend_Db_Table_Select */
        $select = $this->_storage->select($withFromPart = true);
        if ($sqlWhere)  $select->where ($sqlWhere);
        if ($sidx)      $select->order( $sidx . ' ' . $sord );
        $select->limit($limit, $start);
        
        header('X-Debug-GetWherePartBySearch: '.json_encode($sqlWhere));
        header('X-Debug-SearchRequest: '.json_encode($_REQUEST));
        header('X-Debug-SQL: ' . json_encode($select->assemble()) );
        
        /* @var $result Zend_Db_Statement */
        $result = $db->query($select);
        $num_fields = $result->columnCount();
        
        $this->_rsp->page = $page;
        $this->_rsp->total = $total_pages;
        $this->_rsp->records = $count;
        $this->_rsp->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);
        
        $this->view->gridresponsedata = $this->_rsp;
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function indexstatAction()
    {
        include 'JqGrid/JqGridHelper.php';
        
        $jq = new JqGrid_Zend_GridModelCreator();
        
        $tourModel = new Model_TourenDispoVorgaenge();
        
        $rq      = $this->_request;
        $kw      = $rq->getParam('kw', '');
        $DVon    = $rq->getParam('DatumVon', '');
        $DBis    = $rq->getParam('DatumBis', '');
        $lagerId = $rq->getParam('lager_id', '1');
        $avisiert = $rq->getParam('avisiert', '*');
        
        /** @var $modelLg Model_lager **/
        //$modelLG = MyProject_Model_Database::loadModel('lager');
        $modelLg = new Model_Lager();
        $this->view->lagerData = $modelLg->getList();
        $this->view->lagerHtmlOptions = $modelLg->getHtmlOptions($lagerId);
        
        if ($DVon) $DVon = date("Y-m-d", strtotime($DVon));
        if ($DBis) $DBis = date("Y-m-d", strtotime($DBis));
        
        if (!$DVon && !$kw) $kw = date('Y-W');
        if (preg_match('/2[0-9]{3}-[0-5]?[0-9]/', $kw)) {
//                    1  2  3  4  5  6  0
//                    Mo Di Mi Do Fr Sa So
//          NeuesJahr x  x  x  x
//          AltesJahr             x  x  x
//          Diff      0  -1 -2 -3 3  2  1
            // Get the Date-Diff of the first Monday of the first Kw of this Year
            list($y,$W) = explode('-', $kw);
            $neujahr = mktime(0, 0, 0, 1, 1, $y); 
            list($w, $_W) = explode(' ', date('w W', $neujahr )); 
            $diff =  ($_W > 1) ? ( ($w) ? (8-$w) : 1 ) : ( -($w-1) );
            $firstMoTime = $neujahr + ($diff * (24*60*60) );
            $moTimeOfKW = $firstMoTime + ( ($W-1) * 7*24*60*60) ;            
            $DVon = date('Y-m-d', $moTimeOfKW);
            $DBis = ''; //date('Y-m-d', $moTimeOfKW + (6 * 24 * 60 * 60));
        }
        
        try {
            $db = Zend_Registry::get('db');
            $storage = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge');
            $this->view->tourlist = new stdClass();
            $this->view->tourlist->ajax = $this->_request->getParam('ajax', '0');
            $this->view->tourlist->tblInfo = $storage->info();
            $this->view->tourlist->baseFilter = array(
                'DVon' => $DVon,
                'DBis' => $DBis,
                'lager_id' => $lagerId
            );
            if ( in_array($avisiert, array('0', '1') )) {
                $this->view->tourlist->baseFilter['avisiert'] = (int)$avisiert;
            }
            $this->view->tourlist->cols = $storage->info(Zend_Db_Table::METADATA);
            $this->view->tourlist->grid = $jq->createGridModelByColNames($tourModel->tourlist2ColNames());
            $this->view->tourlist->grid['url'] = $this->view->baseUrl() . '/tourlist/gridresponsedatastat?kw='.$kw.'&lager_id='.(int)$lagerId;
            
        } catch(Exception $e) {
            die( '<pre>#' . __LINE__ . ' ' . __METHOD__ . ' ' . print_r($e) );
        }
    }
    
    public function gridresponsedatastatAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        /* @var $request Zend_Controller_Request_Abstract */
        $this->_request = $this->getRequest();
        
        $mainTbl = $this->_storage->info(Zend_Db_Table::NAME);
        $mainKey = current($this->_storage->info(Zend_Db_Table::PRIMARY));
        
        $page    = (int) $this->_request->getParam('page', 1);
        $limit   = (int) $this->_request->getParam('rows', 20);
        $sidx    = $this->_request->getParam('sidx', null);
        $sord    = $this->_request->getParam('sord', 'ASC');
        $pid     = $this->_request->getParam('pid', '');
        $kw      = $this->_request->getParam('kw', '');
        $DVon    = $this->_request->getParam('DatumVon', '');
        $DBis    = $this->_request->getParam('DatumBis', '');
        $lagerId = $this->_request->getParam('lager_id', '');
        $avisiert = $this->_request->getParam('avisiert', '*');
        
        
        // ?kw=2012-25&lager_id=1&_search=false&nd=1340038014576&rows=10&page=1&sidx=&sord=asc
        $TblCnf = $this->_model->infoToTblConf();
        $cols   = $this->_model->tourlistColNames();
		
		try {
            $opt    = array("additionalFields" => array_diff($cols, array_keys($TblCnf['Fields'])) );
            $sqlUsrWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
            //die( '#' . __LINE__ . ' sqlUsrWhere: ' . $sqlUsrWhere);

            if (!$DVon && !$kw) $kw = date('Y-W');
            if (!$DVon && preg_match('/2[0-9]{3}-[0-5]?[0-9]/', $kw)) {
    //                    1  2  3  4  5  6  0
    //                    Mo Di Mi Do Fr Sa So
    //          NeuesJahr x  x  x  x
    //          AltesJahr             x  x  x
    //          Diff      0  -1 -2 -3 3  2  1
                // Get the Date-Diff of the first Monday of the first Kw of this Year
                list($y,$W) = explode('-', $kw);
                $neujahr = mktime(0, 0, 0, 1, 1, $y);
                list($w, $_W) = explode(' ', date('w W', $neujahr ));
                $diff =  ($_W > 1) ? ( ($w) ? (8-$w) : 1 ) : ( -($w-1) );
                $firstMoTime = $neujahr + ($diff * (24*60*60) );
                $moTimeOfKW = $firstMoTime + ( ($W-1) * 7*24*60*60) ;
                $DVon = date('Y-m-d', $moTimeOfKW);
                $DBis = ''; //date('Y-m-d', $moTimeOfKW + (6 * 24 * 60 * 60));
            }


            $baseFilter = array(
                'DVon' => $DVon,
                'DBis' => $DBis,
                'lager_id' => $lagerId
            );
            if ($avisiert != '*') $baseFilter['avisiert'] = (int)$avisiert;

            $offset = ($page-1)*$limit;

            $tourModel = new Model_TourenDispoVorgaenge();
            //die('#' . __LINE__ . ' ' . __METHOD__);
            $result = $tourModel->tourlist2($baseFilter, $sqlUsrWhere, $limit, $offset, $sidx, $sord);

            $this->_rsp->page      = ($offset && $limit !== 0 ? ceil(($offset+$limit)/$limit) : 1);
            $this->_rsp->total     = ($result->numAll>=$limit) ? ceil($result->numAll/$limit) : 1 ;
            $this->_rsp->records   = count($result->rows);
            $this->_rsp->rows      = $result->rows;
            $this->_rsp->x_numall  = $result->numAll;
            $this->_rsp->x_offset  = $offset;
            $this->_rsp->x_page    = $page;
            $this->_rsp->x_limit   = $limit;

            //header('X-SQL-COUNT: ' . $result->sqlCount);
            //header('X-SQL-LIST: ' . $result->sqlList);

            $this->view->gridresponsedata = $this->_rsp;
		} catch(Exception $e) {
			die($e->getMessage() . PHP_EOL . $e->getTraceAsString());
		}
    }
}

