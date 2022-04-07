<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bestellpositionenController
 * @author rybka
 */
class BestellpositionenController extends Zend_Controller_Action 
{
   public function init() {
       parent::init();
       $this->_request = $this->getRequest();
   }
   
    //put your code here
    public function indexAction() {
        $db = Zend_Registry::get('db');
        $model = MyProject_Model_Database::loadModel('bestellpositionen');
        $this->view->bestellpositionenlist = $model->fetchEntries(array('count'=>10, 'offset'=>15));
        
        /* @var $storage Model_Db_bestellpositionen */
        $storage = $model->getStorage();
        $this->view->tblCols = $storage->info(); //'cols');
    }

    public function listAction() {
    }

    public function datalistAction() {
        /* @var $model Model_bestellpositionen */
        $model = MyProject_Model_Database::loadModel('bestellpositionen');
        $this->view->datalist = $model->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function grideditdataAction()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_Bestellpositionen */
        $model = MyProject_Model_Database::loadModel('bestellpositionen');
        
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
        
        /* @var $storage Model_Db_bestellpositionen */
        $storage = $model->getStorage();
        
        print_r($_REQUEST);
        Zend_Layout::getMvcInstance()->disableLayout();
        exit;
    }
    
    public function gridresponseapdataAction()
    {
        //die('#'.__LINE__ . ' ' . __METHOD__);
        $db = Zend_Registry::get('db');
        $model = MyProject_Model_Database::loadModel('auftragspositionen');
        $store = $model->getStorage();
        $TblCnf = $model->infoToTblConf();
        
        $modelBP = MyProject_Model_Database::loadModel('bestellpositionen');
        $storeBP = $modelBP->getStorage();
        
        $response = new stdClass();
        
        $rq = $this->getRequest();
        $mandant = $rq->getParam('mandant');
        $auftrag = $rq->getParam('auftrag');
        
        $page  = (int) $rq->getParam('page', 1);
        $limit = (int) $rq->getParam('rows', 100);
        $sidx  = $rq->getParam('sidx', null);
        $sord  = $rq->getParam('sord', 'ASC');
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) $sord = 'ASC';
        
        $opt = array("additionalFields" => array());
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        header('X-Debug-GetWherePartBySearch: '.json_encode($sqlWhere));
        header('X-Debug-SearchRequest: '.json_encode($_REQUEST));
        header('X-Debug-Bestellpositionen-Tbl: '.json_encode($TblCnf['Table']));
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from(
            array('AP' => $store->info(Zend_Db_Table::NAME)),
            array(new Zend_Db_Expr('count(1)')) 
        )->joinLeft(
            array('BP' => $storeBP->info(Zend_Db_Table::NAME)),
            'AP.Mandant = BP.Mandant AND AP.Auftragsnummer = BP.Auftragsnummer AND AP.Positionsnummer = BP.AuftragsPositionsnummer',
            null
        );
        if ($auftrag)  $select->where( $db->quoteIdentifier ('AP.Auftragsnummer') .' = ' . $db->quote ($auftrag));
        if ($mandant)  $select->where( $db->quoteIdentifier ('AP.Mandant') .' = ' . $db->quote ($mandant));
        $select->where( 'BP.Mandant IS NULL');
        if ($sqlWhere) $select->where( $sqlWhere);
//        die($select->assemble());
        $count = $db->fetchOne($select);

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit);
        $select = $db->select();
        $select->from(
            array( 'AP' => $store->info(Zend_Db_Table::NAME)),
            array('*')
        )->joinLeft(
            array('BP' => $storeBP->info(Zend_Db_Table::NAME)),
            'AP.Mandant = BP.Mandant AND AP.Auftragsnummer = BP.Auftragsnummer AND AP.Positionsnummer = BP.AuftragsPositionsnummer',
            array('') 
        );
        
        if ($auftrag)  $select->where( $db->quoteIdentifier ('AP.Auftragsnummer') .' = ' . $db->quote ($auftrag));
        if ($mandant)  $select->where( $db->quoteIdentifier ('AP.Mandant') .' = ' . $db->quote ($mandant));
        $select->where( 'BP.Mandant IS NULL');
        if ($sqlWhere) $select->where( $sqlWhere);
        if ($sidx) $select->order( $sidx . ' ' . $sord );
        
        $select->limit($limit, $start);
//        die($select->assemble());
        
        /* @var $result Zend_Db_Statement */
        header('X-Debug-SQL: ' . json_encode($select->assemble()) );
        $result = $db->query($select);
        
        
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);
        
//        $this->view->gridresponsedata = $response;
        $this->_helper->json($response);
    }

    public function gridresponsedataAction() 
    {        
        $rq = $this->getRequest();
        $parentid   = (int) $rq->getParam('parentid', 0);
        if (!$parentid || !is_numeric($parentid)) {
            return $this->_forward('gridresponseapdata');
        }
		//die('parent_id: ' . $parentid);
        $mandantid  = (int) $rq->getParam('mandant', 10);
        $page  = (int) $rq->getParam('page', 1);
        $limit = (int) $rq->getParam('rows', 100);
        $sidx  = $rq->getParam('sidx', null);
        $sord  = $rq->getParam('sord', 'ASC');
        $pid   = $rq->getParam('pid', '');
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_Bestellpositionen */
        $model = MyProject_Model_Database::loadModel('bestellpositionen');
        $modelWB = new Model_Warenbewegungen();
        $tblWB = $modelWB->getStorage()->info(Zend_Db_Table::NAME);
        $modelPBM = new Model_BestellpositionenMeta();
        $tblPBM = $modelPBM->getStorage()->info(Zend_Db_Table::NAME);
//        die(print_r($modelPBM->getStorage()->info(), 1));
//        $db = $model->getStorage()->getAdapter();
        
        /* @var $storage Model_Db_bestellpositionen */
        $storage = $model->getStorage();
        $TblCnf = $model->infoToTblConf();
                
        $response = new stdClass();
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) $sord = 'ASC';
        
        $opt = array("additionalFields" => array());
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        header('X-Debug-GetWherePartBySearch: '.json_encode($sqlWhere));
        header('X-Debug-SearchRequest: '.json_encode($_REQUEST));
        header('X-Debug-Bestellpositionen-Tbl: '.json_encode($TblCnf['Table']));
        
        /* @var $select Zend_Db_Table_Select */
        $select = $storage->select($withFromPart = false);
        $select->from($TblCnf['Table'], new Zend_Db_Expr('COUNT(*) AS count'));
        if ($parentid)  $select->where( $db->quoteIdentifier ('Bestellnummer') .' = ' . $db->quote ($parentid));
        if ($mandantid) $select->where( $db->quoteIdentifier ('Mandant') .' = ' . $db->quote ($mandantid));
        if ($sqlWhere)  $select->where ($sqlWhere);
//        die($select->assemble());
        $count = $db->fetchOne($select);

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
//        die('#' . __LINE__ . ' ' . __FILE__);
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select(); //$withFromPart = false);
//        die('#' . __LINE__ );
        $select->from(
                array('BP' => $model->getStorage()->info(Zend_Db_Table::NAME),
                array('*') )
        );
        
        $select->joinLeft(
                array('WB' => $tblWB ),
                'BP.Mandant = WB.Mandant AND BP.Auftragsnummer = WB.Auftragsnummer AND BP.Positionsnummer = WB.Positionsnummer',
                array('WB_Lagerkennung' => 'Lagerkennung', 'WB_Stellplatz' => 'Stellplatz')
        );
        $select->joinLeft(
                array('BPM' => $tblPBM ),
                'BP.Mandant = BPM.Mandant AND BP.Bestellnummer = BPM.Bestellnummer AND BP.Positionsnummer = BPM.Positionsnummer',
                array('PBM_Stellplatz' => 'Stellplatz', 'StellplatzHistorie', new Zend_Db_Expr('IFNULL(BPM.Stellplatz, WB.Stellplatz) Stellplatz'))
        );
        
        if ($parentid)  $select->where( $db->quoteIdentifier ('BP.Bestellnummer') .' = ' . $db->quote ($parentid));
        if ($mandantid) $select->where( $db->quoteIdentifier ('BP.Mandant') .' = ' . $db->quote ($mandantid));
        if ($sqlWhere)  $select->where ($sqlWhere);
        if ($sidx) $select->order( $sidx . ' ' . $sord );
        
        $select->limit($limit, $start);
//        die('#' . __LINE__ . ' ' . $select->assemble() );
        
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

