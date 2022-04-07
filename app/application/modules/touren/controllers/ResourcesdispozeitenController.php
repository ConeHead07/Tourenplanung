<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceDispozeitenController
 *
 * @author rybka
 */
class Touren_ResourcesdispozeitenController extends Zend_Controller_Action {

    //put your code here
    protected $_resourceModels = array(
        "FP" => 'fuhrpark',
        "MA" => 'mitarbeiter',
        "WZ" => 'werkzeug'
    );

    protected $_resourceDispoModels = array(
        "FP" => 'tourenDispoFuhrpark',
        "MA" => 'tourenDispoMitarbeiter',
        "WZ" => 'tourenDispoWerkzeug'
    );
    
    // TourenDispoLogger: kann MyProject_Model_Database::loadModel geladen werden
    protected $_modelName = 'resourcesDispozeiten';
    protected $_modelLoggerName = 'tourenDispoLog';
    protected $rq = null;
    protected $db = null;
    
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }

    public function gridlistAction()
    {
//        die('#' . __METHOD__);
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');
        $ajax     = (int)$rq->getParam('ajax', 0);
        $rsrcData = array();
        $rsrcName = '';

        if (isset($this->_resourceModels[$rsrcType])) {
            $model = MyProject_Model_Database::loadModel($this->_resourceModels[$rsrcType]);
            $row = $model->fetchEntry($rsrcID);

            if ($row) {
                switch($rsrcType) {
                    case 'MA':
                        $rsrcName.= $row['vorname'] . ' ' . $row['name'] . ' / ' . $row['eingestellt_als'];
                        break;

                    case 'FP':
                        $rsrcName.= $row['kennzeichen'] . ' ' . ' / ' . $row['fahrzeugart'];
                        break;

                    case 'WZ':
                        $rsrcName.= $row['bezeichnung'] . ' ' . ' / ' . $row['erforderliche_qualifikation'];
                        break;
                }
            } else {
                $rsrcName.= '| ERROR: Ressource mit der ID ' . $rsrcID . ' wurde nicht gefunden!';
            }
        }
        
        $this->view->disableLayout = $ajax;
        $this->view->rsrcType = $rsrcType;
        $this->view->rsrcID   = $rsrcID;
        $this->view->rsrcName = $rsrcName;
        $this->view->rsrcData = $rsrcData;
    }


    public function gridtourlistAction()
    {
//        die('#' . __METHOD__);
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');
        $ajax     = (int)$rq->getParam('ajax', 0);
        $rsrcData = array();
        $rsrcName = '';

        if (isset($this->_resourceModels[$rsrcType])) {
            $model = MyProject_Model_Database::loadModel($this->_resourceModels[$rsrcType]);
            $row = $model->fetchEntry($rsrcID);

            if ($row) {
                switch($rsrcType) {
                    case 'MA':
                        $rsrcName.= $row['vorname'] . ' ' . $row['name'] . ' / ' . $row['eingestellt_als'];
                        break;

                    case 'FP':
                        $rsrcName.= $row['kennzeichen'] . ' ' . ' / ' . $row['fahrzeugart'];
                        break;

                    case 'WZ':
                        $rsrcName.= $row['bezeichnung'] . ' ' . ' / ' . $row['erforderliche_qualifikation'];
                        break;
                }
            } else {
                $rsrcName.= '| ERROR: Ressource mit der ID ' . $rsrcID . ' wurde nicht gefunden!';
            }
        }

        $this->view->disableLayout = $ajax;
        $this->view->rsrcType = $rsrcType;
        $this->view->rsrcID   = $rsrcID;
        $this->view->rsrcName = $rsrcName;
        $this->view->rsrcData = $rsrcData;
    }


    public function gridlistdataAction()
    {
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');
        
        $page  = (int) $this->_request->getParam('page', 1);
        $limit = (int) $this->_request->getParam('rows', 100);
        $sidx  = $this->_request->getParam('sidx', null);
        $sord  = $this->_request->getParam('sord', 'ASC');
        
        
        
        $listOpts = array(
            'count'  => $limit,
            'offset' => ($page-1)*$limit,
            'sidx'   => $sidx,
            'sord'   => $sord,
        );
//        die(print_r($rq->getParams(), 1));
        
        $model = MyProject_Model_Database::loadModel($this->_modelName);
        $model = new Model_ResourcesDispozeiten();
        
        $TblCnf = $model->infoToTblConf();
        $opt = array("additionalFields" => array());
        $listOpts['where'] = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        
        $list = $model->fetchList($rsrcType, $rsrcID, $listOpts);
        
        $out = new stdClass();
        $out->total = $list->total_pages;
        $out->records = count($list->rows);
        $out->rows = $list->rows;
        $out->page = $list->page;
        $this->_helper->json($out);
        
    }


    public function gridtourlistdataAction()
    {
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');

        $page  = (int) $rq->getParam('page', 1);
        $limit = (int) $rq->getParam('rows', 100);
        $sidx  = $rq->getParam('sidx', 'DatumVon');
        $sord  =strtoupper($rq->getParam('sord', 'ASC'));

        $fieldMapping = array(
            'id' => 'r.id',
            'ressourcen_id' => 'ressourcen_id',
            'tour_id' => 't.tour_id',
            'tagesnr' => 'p.tagesnr',
            'Mandant' => 'ak.Mandant',
            'Auftragsnummer' => 'ak.Auftragsnummer',
            'ressourcen_typ' => 'ressourcen_typ',
            'LieferungName' => 'ak.LieferungName',
            'Vorgangstitel' => 'ak.Vorgangstitel',
            'DatumVon' => 't.DatumVon',
            'ZeitVon' => 't.ZeitVon',
            'ZeitBis' => 't.ZeitBis',
            'DatumBis' => 't.DatumBis'
        );
        if (!in_array($sord, ['ASC','DESC'])) {
            $sord = 'ASC';
        }
        if (isset($fieldMapping[$sidx])) {
            $sidx = $fieldMapping[$sidx];
        } else {
            $sidx = null;
        }

        /** @var Model_TourenDispoResourceAbstract $model */
        $model = MyProject_Model_Database::loadModel($this->_resourceDispoModels[$rsrcType]);

        $filters = $rq->getParam('filters', null );
        if ($filters) {
            $changed = false;
            $filtersObj = json_decode( $filters );
            for($i = 0; $i < count($filtersObj->rules); $i++) {
                switch($filtersObj->rules[$i]->field) {
                    case 'DatumVon':
                        if (preg_match('#^[0-9]+#', $filtersObj->rules[$i]->data)) {
                            $filtersObj->rules[$i]->data = '>=' . $filtersObj->rules[$i]->data;
                            $changed = true;
                        }
                        break;

                    case 'DatumBis':
                        if (preg_match('#^[0-9]+#', $filtersObj->rules[$i]->data)) {
                            $filtersObj->rules[$i]->data = '<=' . $filtersObj->rules[$i]->data;
                            $changed = true;
                        }
                        break;
                }
            }
            if ($changed) {
                $rq->setParam( 'filters', json_encode($filtersObj));
            }
        }

        $listOpts = array(
            'count'  => $limit,
            'offset' => ($page-1)*$limit,
            'sidx'   => $sidx,
            'sord'   => $sord,
        );

        $TblCnf = $model->infoToTblConf();

        $opt = array("additionalFields" => $fieldMapping);
        $listOpts['where'] = JqGridSearch::getSqlBySearch($TblCnf, $opt);

        $list = $model->getTourlistByResourceId($rsrcID, $listOpts);

        $out = new stdClass();
        $out->total = $list->total_pages;
        $out->records = count($list->rows);
        $out->rows = $list->rows;
        $out->page = $list->page;
        $this->_helper->json($out);

    }

    public function tourlistAction()
    {
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');
        $datumVon   = $rq->getParam('DatumVon', '');
        $datumBis   = $rq->getParam('DatumBis', '');

        /** @var Model_TourenDispoResourceAbstract $model */
        $model = MyProject_Model_Database::loadModel($this->_resourceDispoModels[$rsrcType]);

        $listOpts = array(
            'count'  => 100,
            'offset' => 0,
            'sidx'   => 'DatumVon',
            'sord'   => 'ASC',
        );

        $db = $model->getStorage()->getAdapter();

        $listOpts['where'] = 't.DatumVon >= ' . $db->quote($datumVon). ' AND t.DatumVon <= ' . $db->quote($datumBis);

        $list = $model->getTourlistByResourceId($rsrcID, $listOpts);

        $this->view->disableLayout = true;
        $this->view->rsrcType = $rsrcType;
        $this->view->rsrcID = $rsrcID;

        $this->view->out = new stdClass();
        $this->view->out->success = true;
        $this->view->out->total = $list->total_pages;
        $this->view->out->total_records = $list->total_records;
        $this->view->out->records = count($list->rows);
        $this->view->out->rows = $list->rows;
        $this->view->out->page = $list->page;

        $this->_helper->json($this->view->out);
    }
    
    public function grideditdataAction()
    {
        $return = new stdClass();
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');
        
        $data = $rq->getParams();
        if (empty($data['ressourcen_typ'])) $data['ressourcen_typ'] = $rsrcType;
        if (empty($data['ressourcen_id']))  $data['ressourcen_id']  = $rsrcID;
//        echo '#'.__LINE__ . ' ' . __METHOD__ . ' data: ' . print_r($data,1).PHP_EOL;
        
        $model = MyProject_Model_Database::loadModel($this->_modelName);
        $model = new Model_ResourcesDispozeiten();
        
        $id = $rq->getParam('id', 0);
        $op = $rq->getParam('oper', '');
        
        try {
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


    public function gridtoureditdataAction()
    {
        $return = new stdClass();
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');

        $data = $rq->getParams();
        if (empty($data['ressourcen_typ'])) $data['ressourcen_typ'] = $rsrcType;
        if (empty($data['ressourcen_id']))  $data['ressourcen_id']  = $rsrcID;
//        echo '#'.__LINE__ . ' ' . __METHOD__ . ' data: ' . print_r($data,1).PHP_EOL;

        $model = MyProject_Model_Database::loadModel($this->_modelName);
        $model = new Model_ResourcesDispozeiten();

        $id = $rq->getParam('id', 0);
        $op = $rq->getParam('oper', '');

        try {
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
}

