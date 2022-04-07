<?php

/**
 * Description of bestellkoepfeController
 * @author rybka
 */
class BestellkoepfeController extends Zend_Controller_Action {
    protected $modelBKKey = 'bestellkoepfe';
    
    /* @var $modelBK Model_bestellkoepfe */
    protected $modelBK = null;
    
    public function init() {
        $this->modelBKKey = 'bestellkoepfe';
        
        /* @var $this->modelBK Model_bestellkoepfe */
        $this->modelBK = MyProject_Model_Database::loadModel($this->modelBKKey);
        
        $this->_request = $this->getRequest();
   }
    
    //put your code here
    public function indexAction() {
        $db = Zend_Registry::get('db');
        /* @var $model Model_bestellkoepfe */
        $model = $this->modelBK; // MyProject_Model_Database::loadModel('bestellkoepfe');
        $this->view->bestellkoepfelist = $model->fetchEntries(array('count'=>10, 'offset'=>15));
        
        /* @var $storage Model_Db_bestellkoepfe */
        $storage = $model->getStorage();
        $this->view->tblCols = $storage->info(); //'cols');
    }

    public function listAction() {
    }

    public function datalistAction() {
        /* @var $model Model_bestellkoepfe */
        $model = $this->modelBK; //MyProject_Model_Database::loadModel('bestellkoepfe');
        $this->view->datalist = $model->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function grideditdataAction()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_bestellkoepfe */
        $model = $this->modelBK; //MyProject_Model_Database::loadModel('bestellkoepfe');
        
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
        
        /* @var $storage Model_Db_bestellkoepfe */
        $storage = $model->getStorage();
        
        print_r($_REQUEST);
        Zend_Layout::getMvcInstance()->disableLayout();
        exit;
    }

    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_bestellkoepfe */
        $model = $this->modelBK; // MyProject_Model_Database::loadModel('bestellkoepfe');
//        $db = $model->getStorage()->getAdapter();
        $tblAP = MyProject_Model_Database::getStorageByClass('auftragspositionen')->info(Zend_Db_Table::NAME);
        $tblBP = MyProject_Model_Database::getStorageByClass('bestellpositionen')->info(Zend_Db_Table::NAME);
        /* @var $storage Model_Db_bestellkoepfe */
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
        $select->joinLeft(array('m'=>'mr_bestellkoepfe'), "$mainTbl.Mandant = m.Mandant AND $mainTbl.Bestellnummer = m.Bestellnummer", '');
        if ($parentid)  $select->where( $mainTbl . '.' . $db->quoteIdentifier ('Auftragsnummer') .' = ' . $db->quote ($parentid));
        if ($mandantid) $select->where( $mainTbl . '.' . $db->quoteIdentifier ('Mandant') .' = ' . $db->quote ($mandantid));
        if ($sqlWhere)  $select->where ($sqlWhere);
        
        $count = $db->fetchOne($select);
        
        $sqlAPServices = 
                  'SELECT BK.* FROM ' . $tblAP . ' AP ' . PHP_EOL
                . ' LEFT JOIN ' . $tblBP . ' BP ON (AP.Mandant = BP.Mandant AND AP.Auftragsnummer = BP.Auftragsnummer AND AP.Positionsnummer = BP.AuftragsPositionsnummer)' . PHP_EOL 
                . ' LEFT JOIN ' . $mainTbl . ' BK ON (BP.Mandant = BK.Mandant AND BP.Bestellnummer = BK.Bestellnummer)'
                . ' WHERE ' . PHP_EOL
                . ' AP.' . $db->quoteIdentifier ('Auftragsnummer') .' = ' . $db->quote ($parentid) . PHP_EOL
                . ' AND AP.' . $db->quoteIdentifier ('Mandant') .' = ' . $db->quote ($mandantid) . PHP_EOL
                . ' AND BP.Mandant IS NULL LIMIT 1';
        
        $rowAPS = $db->fetchRow($sqlAPServices, array(), Zend_Db::FETCH_ASSOC);
        if ($rowAPS) {
            $rowAPS['Mandant'] = $mandantid;
            $rowAPS['Auftragsnummer'] = $parentid;
            ++$count;
        }
        //echo $sqlAPServices . ' rowAPS:' . print_r($rowAPS,1) . PHP_EOL;
        
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
        $select->joinLeft(array('m'=>'mr_bestellkoepfe'), "$mainTbl.Mandant = m.Mandant AND $mainTbl.Bestellnummer = m.Bestellnummer", 'Stellplatz');
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
        $response->rows = array_merge( ($rowAPS ? array($rowAPS) : array() ), $result->fetchAll(Zend_Db::FETCH_ASSOC));
        
        $this->view->gridresponsedata = $response;
        
    }

}

