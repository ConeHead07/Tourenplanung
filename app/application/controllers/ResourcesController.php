<?php

/**
 * Description of userController
 * @author rybka
 */
class ResourcesController extends Zend_Controller_Action 
{
    
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }
    
    //put your code here
    public function indexAction() 
    {
        $db = Zend_Registry::get('db');
        $user = MyProject_Model_Database::loadModel('user');
        $this->view->userlist = $user->fetchEntries();
    }

    public function listAction() 
    {
        
    }

    public function datalistAction() 
    {
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        $this->view->datalist = $modelUser->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function grideditdataAction()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        
        switch($_REQUEST['oper']) {
            case 'edit':
                $modelUser->update($_REQUEST, $_REQUEST['id']);
                break;
            
            case 'add':
                $modelUser->insert($_REQUEST);
                break;
            
            case 'del':
                $modelUser->delete($_REQUEST['id']);
                break;
        }
        
        /* @var $storageUser Model_Db_User */
        $storageUser = $modelUser->getStorage();
        
        print_r($_REQUEST);
        Zend_Layout::getMvcInstance()->disableLayout();
        exit;
    }

    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        
        /* @var $storageUser Model_Db_User */
        $storageUser = $modelUser->getStorage();
        
        $TblCnf = include APPLICATION_PATH . '/configs/dbtables/user.inc.php';
        
        
        $response = new stdClass();
        
        $TblCnfParser = MyProject_Parser_TableConf::getInstance();
        $TblCnfParser->parse_conf($TblCnf);

        $mitarbeiter = $this->getRequest()->getParam('username');
        
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
        $select = $storageUser->select($withFromPart = false);
        $select->from($storageUser->info(Zend_Db_Table::NAME), new Zend_Db_Expr('COUNT(*) AS count'));
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
        $select = $storageUser->select($withFromPart = true);
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

