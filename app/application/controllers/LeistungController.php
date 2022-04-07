<?php

/**
 * Description of userController
 * @author rybka
 */
class LeistungController extends Zend_Controller_Action 
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
        
        $whereType = ($typeField) ? $db->quoteInto('ressourcen_typ = ?', $typeField) : '';
        
//      if ($nodeId > 0) $level+= 1;
        /* @var $treeview Model_FuhrparkCategories */
        $model = MyProject_Model_Database::loadModel('leistung');
        $tbl = $model->getStorage()->info(Zend_Db_Table::NAME);
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                ->from($tbl)
                ->where('leistungs_name LIKE ?', $term)        
                ->limit($limit, $offset)
                ->order('leistungs_name');
        if ($whereType) $sql->where($whereType);
        
        $this->view->autocomplete = $db->fetchAll($sql);;     
    }
    
    //put your code here
    public function indexAction() 
    {
        $db = Zend_Registry::get('db');
        $model = MyProject_Model_Database::loadModel('leistung');
        $this->view->datalist = $model->fetchEntries();
    }

    public function listAction() 
    {        
    }

    public function datalistAction() 
    {
        /* @var $model Model_Leistung */
        $model = MyProject_Model_Database::loadModel('leistung');
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
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        try {
            /* @var $model Model_Leistung */
            $model = MyProject_Model_Database::loadModel('leistung');

            switch($op) {
                
                case 'edit':
                    if ($model->update($data, $id)) {
                        $return->type = 'success';
                    } else {
                        $return->err = 'Datensatz konnte nicht aktualisiert werden!';
                    }
                    break;

                case 'add':
                    $return->id = $model->insert($data);
                    if ($return->id) {
                        $return->type = 'success';
                    } else {
                        $return->err = 'Datensatz konnte nicht angelegt werden!';
                    }
                    break;

                case 'del':
                    if ($model->delete($id)) {
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

    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_Leistung */
        $model = MyProject_Model_Database::loadModel('leistung');
        
        /* @var $storage Model_Db_Leistung*/
        $storage = $model->getStorage();
        
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

