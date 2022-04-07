<?php

/**
 * Description of userController
 * @author rybka
 */
class ExternController extends Zend_Controller_Action 
{
    
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }
   
    public function autocompleteAction()
    {
        /** @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        $term = $rq->getParam('term', '');
        $typeField = $rq->getParam('type', '');
        $offset = $rq->getParam('offset', 0);
        $limit  = $rq->getParam('limit', 15);
        $trunc = $rq->getParam('trunc', 'both');
        
        switch($trunc) {
            case 'both':    $term = '%' . $term . '%'; break;
            case 'left':    $term = '%' . $term;       break;
            default:        $term.= '%';
        }
        
        $whereType = ($typeField) ? $db->quoteIdentifier('extern_' . strtolower($typeField)) . ' = 1' : '';
        
//      if ($nodeId > 0) $level+= 1;
        /* @var $treeview Model_FuhrparkCategories */
        $model = MyProject_Model_Database::loadModel('extern');
        $tbl = $model->getStorage()->info(Zend_Db_Table::NAME);
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                ->from($tbl)
                ->where('extern_firma LIKE ?', $term)
                ->where('extern_disponierbar = 1')                
                ->limit($limit, $offset)
                ->order('extern_firma');
        if ($whereType) $sql->where($whereType);
        
        $this->view->autocomplete = $db->fetchAll($sql);
                
        if (0) $model->fetchEntries(array(
            'where' => $db->quote('extern_firma LIKE ?', $term)
                      . ' AND extern_disponierbar = 1 '
                      . $whereType,
            'count' => (int)$limit,
            'offset'=> (int)$offset,
            'order' => 'extern_firma'
        ));     
    }
    
    //put your code here
    public function indexAction() 
    {
        $db = Zend_Registry::get('db');
        $model = MyProject_Model_Database::loadModel('extern');
        $this->view->datalist = $model->fetchEntries();
    }

    public function listAction() 
    {
        
    }

    public function datalistAction() 
    {
        /* @var $model Model_Extern */
        $model = MyProject_Model_Database::loadModel('extern');
        $this->view->datalist = $model->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function grideditdataAction()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_Extern */
        $model = MyProject_Model_Database::loadModel('extern');
        
        switch($_REQUEST['oper']) {
            case 'edit':
                $model->update($_REQUEST, $_REQUEST['id']);
                break;
            
            case 'add':
                $model->insert($_REQUEST);
                break;
            
            case 'del':
                $model->delete($_REQUEST['id']);
                break;
        }
        
        /* @var $storage Model_Db_Extern */
        $storage = $model->getStorage();
        
        print_r($_REQUEST);
        Zend_Layout::getMvcInstance()->disableLayout();
        exit;
    }

    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_Extern */
        $model = MyProject_Model_Database::loadModel('extern');
        
        /* @var $storage Model_Db_Extern */
        $storage = $model->getStorage();
        
        //$TblCnf = include APPLICATION_PATH . '/configs/dbtables/extern.inc.php';
        $TblCnf = $model->infoToTblConf();
        
        $response = new stdClass();
        
        $TblCnfParser = MyProject_Parser_TableConf::getInstance();
        $TblCnfParser->parse_conf($TblCnf);
        
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
        
        /* @var $select Zend_Db_Table_Select */
        $select = $storage->select($withFromPart = false);
        $select->from($storage->info(Zend_Db_Table::NAME), new Zend_Db_Expr('COUNT(*) AS count'));
        if ($sqlWhere) $select->where ($sqlWhere);
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
        $select = $storage->select($withFromPart = true);
        if ($sqlWhere) $select->where ($sqlWhere);
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

