<?php

/**
 * Description of userController
 * @author rybka
 */
class TeamsController extends Zend_Controller_Action
{

    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }

    //put your code here
    public function indexAction()
    {
        /** @var $model Model_Teams */
        $model = new Model_Teams();
        $this->view->datalist = $model->fetchEntries();
    }

    public function listAction()
    {
    }

    public function datalistAction()
    {
        /** @var $model Model_Teams */
        $model = new Model_Teams();
        $this->view->datalist = $model->fetchEntries();
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
            /** @var $model Model_Teams */
            $model = new Model_Teams();

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

        /** @var $model Model_Teams */
        $model = new Model_Teams();

        /* @var $storage Model_Db_Leistung*/
        $storage = Model_Db_Teams::obj();

        $TblCnf = $model->infoToTblConf();

        $response = new stdClass();

        $TblCnfParser = MyProject_Parser_TableConf::getInstance();
        $TblCnfParser->parse_conf($TblCnf);

        $page  = (int) $this->getRequest()->getParam('page', 1);
        $limit = (int) $this->getRequest()->getParam('rows', 100);
        $sidx  = $this->getRequest()->getParam('sidx', null);
        $sord  = $this->getRequest()->getParam('sord', 'ASC');

        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) $sord = 'ASC';

        $opt = array("additionalFields" => array());
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        header('X-Debug-GetWherePartBySearch: '.json_encode($sqlWhere));
        header('X-Debug-SearchRequest: '.json_encode($_REQUEST));

        /* @var $select Zend_Db_Table_Select */
        $select = $storage->select($withFromPart = false);
        $select->from($storage->info(Zend_Db_Table::NAME), new Zend_Db_Expr('COUNT(*) AS count'));
        if ($sqlWhere) $select->where ($sqlWhere);
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

        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);

        $this->view->gridresponsedata = $response;

    }

}
