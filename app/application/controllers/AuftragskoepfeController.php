<?php

/**
 * Description of auftragskoepfeController
 * @author rybka
 */
class AuftragskoepfeController extends Zend_Controller_Action {
    protected $modelBKKey = 'auftragskoepfe';
    
    /* @var $modelBK Model_auftragskoepfe */
    protected $modelBK = null;
    
    public function init() {
        $this->modelBKKey = 'auftragskoepfe';
        
        /* @var $this->modelBK Model_auftragskoepfe */
        $this->modelBK = MyProject_Model_Database::loadModel($this->modelBKKey);
        
        $this->_request = $this->getRequest();
   }
    
    //put your code here
    public function indexAction() {
        $db = Zend_Registry::get('db');
        /* @var $model Model_auftragskoepfe */
        $model = $this->modelBK; // MyProject_Model_Database::loadModel('auftragskoepfe');
        $this->view->auftragskoepfelist = $model->fetchEntries(array('count'=>10, 'offset'=>15));
        
        /* @var $storage Model_Db_auftragskoepfe */
        $storage = $model->getStorage();
        $this->view->tblCols = $storage->info(); //'cols');
    }

    public function listAction() {
    }

    public function datalistAction() {
        /* @var $model Model_auftragskoepfe */
        $model = $this->modelBK; //MyProject_Model_Database::loadModel('auftragskoepfe');
        $this->view->datalist = $model->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function grideditdataAction()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_auftragskoepfe */
        $model = $this->modelBK; //MyProject_Model_Database::loadModel('auftragskoepfe');
        
        switch($_REQUEST['oper']) {
            case 'edit':
//                $model->update($_REQUEST, $_REQUEST['id']);
                break;
            
            case 'add':
//                $model->insert($_REQUEST);
                break;
            
            case 'del':
//                $model->delete($_REQUEST['id']);
                break;
        }
        
        /* @var $storage Model_Db_auftragskoepfe */
        $storage = $model->getStorage();
        
        print_r($_REQUEST);
        Zend_Layout::getMvcInstance()->disableLayout();
        exit;
    }

    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_auftragskoepfe */
        $model = $this->modelBK; // MyProject_Model_Database::loadModel('auftragskoepfe');
//        $db = $model->getStorage()->getAdapter();
        
        /* @var $storage Model_Db_auftragskoepfe */
        $storage = $model->getStorage();
        $TblCnf = $model->infoToTblConf();
        
        $response = new stdClass();
        
        $mainTbl    = $storage->info(Zend_Db_Table::NAME);
        $parentid   = (int) $this->getRequest()->getParam('parentid', 0);
        $mandantid  = (int) $this->getRequest()->getParam('mandant', 10);
        $page  = (int) $this->getRequest()->getParam('page', 1);
        $limit = (int) $this->getRequest()->getParam('rows', 100);
        $sidx  = $this->getRequest()->getParam('sidx', null);
        $sord  = $this->getRequest()->getParam('sord', 'ASC');
        $pid   = $this->getRequest()->getParam('pid', '');
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) $sord = 'ASC';
        
        $opt = array("additionalFields" => array(), 'tableNamespace' => $mainTbl);
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        header('X-Debug-GetWherePartBySearch: '.json_encode($sqlWhere));
        header('X-Debug-SearchRequest: '.json_encode($_REQUEST));
        header('X-Debug-Bestellkoepfe-Tbl: '.json_encode($TblCnf['Table']));
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl, new Zend_Db_Expr('COUNT(*) AS count'));        
        $select->joinLeft(array('m'=>'mr_auftragskoepfe'), "$mainTbl.Mandant = m.Mandant AND $mainTbl.Bestellnummer = m.Bestellnummer", '');
        if ($parentid)  $select->where( $mainTbl . '.' . $db->quoteIdentifier ('Auftragsnummer') .' = ' . $db->quote ($parentid));
        if ($mandantid) $select->where( $mainTbl . '.' . $db->quoteIdentifier ('Mandant') .' = ' . $db->quote ($mandantid));
        if ($sqlWhere)  $select->where ($sqlWhere);
        
        $sqlAPServices = 
                  'SELECT ap.Positionsnummer FROM wws_auftragspositionen_dispofilter ' . PHP_EOL
                . ''
                .''
                .'';
        
        // die('<html><head></head><body>#' . __LINE__ . ' ' . $select->assemble() . '</body></html>');

        $count = $db->fetchOne($select);

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from( $mainTbl );        
        $select->joinLeft(array('m'=>'mr_auftragskoepfe'), "$mainTbl.Mandant = m.Mandant AND $mainTbl.Bestellnummer = m.Bestellnummer", 'Stellplatz');
        if ($parentid)  $select->where( $mainTbl . '.' . $db->quoteIdentifier ('Auftragsnummer') .' = ' . $db->quote ($parentid));
        if ($mandantid) $select->where( $mainTbl . '.' . $db->quoteIdentifier ('Mandant') .' = ' . $db->quote ($mandantid));
        if ($sqlWhere)  $select->where ($sqlWhere);
        if ($sidx) $select->order( $sidx . ' ' . $sord );
        
        $select->limit($limit, $start);
        
        /* @var $result Zend_Db_Statement */
        header('X-Debug-SQL: ' . json_encode($select->assemble()) );
        $result = $db->query($select);
        $num_fields = $result->columnCount();

        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);
        
        $this->view->gridresponsedata = $response;
        
    }

}
