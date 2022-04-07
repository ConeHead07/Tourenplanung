<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceSperrzeitenController
 *
 * @author rybka
 */
class Touren_ResourcessperrzeitenController extends Zend_Controller_Action {
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
    protected $_modelName = 'resourcesSperrzeiten';
    protected $_modelLoggerName = 'tourenDispoLog';
    protected $rq = null;
    protected $db = null;
    
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }

    public function tourlistAction()
    {
        $rq = $this->getRequest();
        $sperrzeiten_id   = $rq->getParam('id', '');

        $szModel = new Model_ResourcesSperrzeiten();
        $szData = $szModel->getStorage()->find($sperrzeiten_id)->current();

        $rsrcType = $szData->ressourcen_typ;
        $rsrcID = $szData->ressourcen_id;

        if (!$szData || !$rsrcID) {
            die(print_r($szData,1));
            return;
        }

        $listOpts = array(
            'count'  => 100,
            'offset' => 0,
            'sidx'   => 'DatumVon',
            'sord'   => 'ASC',
        );

        $list = $szModel->getRemovedTourlist($sperrzeiten_id, $rsrcType, $rsrcID, $listOpts);

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


    public function gridlistAction() 
    {
//        die('#' . __METHOD__);
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID   = $rq->getParam('rsrcID', '');
        $ajax     = (int)$rq->getParam('ajax', 0);
        $rsrcName = '';
        $rsrcData = array();
        
        if (isset($this->_resourceModels[$rsrcType])) {
            $model = MyProject_Model_Database::loadModel($this->_resourceModels[$rsrcType]);
            $row = $model->fetchEntry($rsrcID);
            
            switch($rsrcType) {
                case 'MA':
                    $rsrcName = $row['vorname'] . ' ' . $row['name'] . ' / ' . $row['eingestellt_als'];
                    break;
                
                case 'FP':
                    $rsrcName = $row['kennzeichen'] . ' ' . ' / ' . $row['fahrzeugart'];
                    break;
                
                case 'WZ':
                    $rsrcName = $row['bezeichnung'] . ' ' . ' / ' . $row['erforderliche_qualifikation'];
                    break;
            }
        }
        
        $this->view->disableLayout = $ajax;
        $this->view->rsrcType = $rsrcType;
        $this->view->rsrcID   = $rsrcID;
        $this->view->rsrcName = $rsrcName;
        $this->view->rsrcData = $rsrcData;
        
//        die(__METHOD__);
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
        $model = new Model_ResourcesSperrzeiten();
        
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
    
    public function grideditdataAction()
    {
        $return = new stdClass();
        $rq = $this->getRequest();
        $rsrcType = $rq->getParam('rsrcType', '');
        $rsrcID = $rq->getParam('rsrcID', '');
        $dtVon  = $rq->getParam('gesperrt_von', '');
        $dtBis  = $rq->getParam('gesperrt_bis', '');
        
        $data = $rq->getParams();
        if (empty($data['ressourcen_typ'])) $data['ressourcen_typ'] = $rsrcType;
        if (empty($data['ressourcen_id']))  $data['ressourcen_id']  = $rsrcID;

        $error = '';

        $model = new Model_ResourcesSperrzeiten();
        
        $id = $rq->getParam('id', 0);
        $op = $rq->getParam('oper', '');

        /** @var Model_TourenDispoResourceAbstract $rsrcDispoModel */
        $rsrcDispoModel = MyProject_Model_Database::loadModel($this->_resourceDispoModels[$rsrcType]);
        $numRemovedItems = 0;

        if (in_array($op, ['add', 'edit'])) {
            $dateValidator = new MyProject_Validate_Date();
            if (!$dateValidator->isValid($dtVon)) {
                $error.= 'Gesperrt-Von: ' . implode('. ', $dateValidator->getMessages());
            }
            if (!$dateValidator->isValid($dtBis)) {
                $error.= 'Gesperrt-Bis: ' . implode('. ', $dateValidator->getMessages());
            }

            if (!$error && $dtVon > $dtBis) {
                $error.= 'Startdatum darf nicht hinter dem Enddatum liegen!';
            }

            if ($error) {
                $return->type = 'error';
                $return->err = $error;
                $this->_helper->json($return);
                return;
            }

            $aTouren = $rsrcDispoModel->getTourlistByIdAndDaterange($rsrcID, $dtVon, $dtBis);
            $aTourIDs = array_map(function($v) {
                return $v['tour_id'];
            }, $aTouren);
        }
        
        try {
            switch($op) {
                case 'edit':
                    if ($model->update($data, $id)) {
                        $return->type = 'success';

                        $numRemovedItems = $rsrcDispoModel->removeRessourceFromTourlist($rsrcID, $aTourIDs, $id);
                        if ($numRemovedItems) {
                            $model->addRemovedItems($numRemovedItems, $id);
                        }
                    } else {
                        $return->err = 'Datensatz konnte nicht aktualisiert werden!';
                    }
                    break;

                case 'add':
                    $return->id = $model->insert($data);
                    if ($return->id) {
                        $return->type = 'success';

                        $numRemovedItems = $rsrcDispoModel->removeRessourceFromTourlist($rsrcID, $aTourIDs, $return->id);

                        if ($numRemovedItems) {
                            $model->setRemovedItems($numRemovedItems, $return->id);
                        }
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

        if (in_array($op, ['add', 'edit'])) {
            $return->msg = "Die Ressource wurde aus $numRemovedItems Touren wieder entfernt!";
            $return->message = "Die Ressource wurde aus $numRemovedItems Touren wieder entfernt!";
        }
        
        $this->_helper->json($return);
    }
}

