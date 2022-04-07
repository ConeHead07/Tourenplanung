<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bestellpositionenController
 * @author rybka
 */
class BestellpositionenmetaController extends Zend_Controller_Action 
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
        $rq = $this->getRequest();
        $oper = $rq->getParam('oper', 'edit');
        $mandant = $rq->getParam('Mandant', '');
        $bestellnr = $rq->getParam('Bestellnummer', '');
        $pnr = $rq->getParam('Positionsnummer', '');
        $stellplatz = $rq->getParam('Stellplatz', '');
        $err = '';
        
        $user = MyProject_Auth_Adapter::getUserName();
        
        foreach(array('Mandant','Bestellnummer','Positionsnummer') as $_prm) {
            if (!trim($rq->getParam($_prm, ''))) {
                $err.= 'Vermisse Angabe fuer das Feld ' . $_prm . '!' . PHP_EOL;
            }
        }
        
        /* @var $model Model_Bestellpositionen */
        $model = MyProject_Model_Database::loadModel('bestellpositionenMeta');
        
        if (!$err) switch($oper) {
            case 'edit':
                if (!$model->editStellplatz($mandant, $bestellnr, $pnr, $stellplatz, $user)) {
                    $err.= 'Fehler beim Aktualisieren der Daten!' . PHP_EOL;
                }
                break;
                
            default:
                    $err.= 'Ungueltige Operation ' . $oper . '!' . PHP_EOL;
            
//            case 'add':
//                $model->insert($_REQUEST);
//                break;
//            
//            case 'del':
//                $model->delete($_REQUEST['id']);
//                break;
        }
        
        if ($err) $this->getResponse()->setHttpResponseCode(404);
        $this->_helper->json(array(
            'type' => ($err) ? 'error' : 'success',
            'error' => $err,
            'msg' => '',
        ));
        exit;
    }

    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_Bestellpositionen */
        $model = MyProject_Model_Database::loadModel('bestellpositionen');
        $modelWB = new Model_Warenbewegungen();
        $tblWB = $modelWB->getStorage()->info(Zend_Db_Table::NAME);
//        $db = $model->getStorage()->getAdapter();
        
        /* @var $storage Model_Db_bestellpositionen */
        $storage = $model->getStorage();
        $TblCnf = $model->infoToTblConf();
                
        $response = new stdClass();
        
        $parentid   = (int) $this->getRequest()->getParam('parentid', 0);
        $mandantid  = (int) $this->getRequest()->getParam('mandant', 10);
        $page  = (int) $this->getRequest()->getParam('page', 1);
        $limit = (int) $this->getRequest()->getParam('rows', 100);
        $sidx  = $this->getRequest()->getParam('sidx', null);
        $sord  = $this->getRequest()->getParam('sord', 'ASC');
        $pid   = $this->getRequest()->getParam('pid', '');
        
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
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select(); //$withFromPart = false);
//        die('#' . __LINE__ );
        $select->from(
                array('BP' => $model->getStorage()->info(Zend_Db_Table::NAME) )
        );
        
        $select->joinLeft(
                array('WB' => $tblWB ),
                'BP.Mandant = WB.Mandant AND BP.Auftragsnummer = WB.Auftragsnummer AND BP.Positionsnummer = WB.Positionsnummer',
                array('WB_Lagerkennung' => 'Lagerkennung', 'WB_Stellplatz' => 'Stellplatz')
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

