<?php 

class Touren_AjaxController extends Zend_Controller_Action
{
    protected $_resourceModels = array(
        "FP" => 'tourenDispoFuhrpark',
        "MA" => 'tourenDispoMitarbeiter',
        "WZ" => 'tourenDispoWerkzeug'
    );

    protected $_resourceDataModels = array(
        "FP" => 'fuhrpark',
        "MA" => 'mitarbeiter',
        "WZ" => 'werkzeug',
    );
    
    // TourenDispoLogger: kann MyProject_Model_Database::loadModel geladen werden
    protected $_modelLoggerName = 'tourenDispoLog';
    protected $rq = null;
    protected $db = null;

    /** @var MyProject_Helper_JsonResponse  */
    protected $json = null;
    
    public function init() {
        $this->json = $this->getHelper('jsonResponse');

        $this->view->debug = false;
        // Rückgabe-Objekt initialisieren
        $this->view->ajax_response = new stdClass();
        // TODO Refacto ajax response type => string success|true
        $this->view->ajax_response->type = true;
        $this->view->ajax_response->msg = '';
        $this->view->ajax_response->error = '';
        $this->view->ajax_response->test = array(__LINE__);
        
        // Warning:
        // Never Use Underscores in your viewscript-Filename: e.g. json_response doesn't work!
        $this->_helper->viewRenderer->setRender('jsonresponse');
        
        // TourenDispoLogger
        $this->_modelLoggerName = 'tourenDispoLog';
        
        $this->rq = $this->getRequest();
        $this->db = Zend_Db_Table::getDefaultAdapter();
        
        $this->_request = $this->getRequest();

        $this->getResponse()->setHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    protected function sendJsonSuccess(string $msg, array $data = []) {
        return $this->_helper->json([
            'type' => 'success',
            'success' => true,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    protected function sendJsonSuccessID($id, string $msg = '', array $data = [])
    {
        return $this->_helper->json([
            'type' => 'success',
            'success' => true,
            'msg' => $msg,
            'id' => $id,
            'data' => $data,
        ]);
    }

    protected function sendJsonError(string $error, array $data = []) {
        return $this->_helper->json([
            'type' => 'error',
            'success' => false,
            'error' => $error,
            'data' => $data,
        ]);
    }

    protected function _require( $mixedExpression, string $messageOnFalse) {
        $jsonError = '';
        if (is_callable($mixedExpression)) {
            if (!$mixedExpression()) {
                $jsonError = $this->sendJsonError( $messageOnFalse );
            }
        }

        if (empty($mixedExpression)) {
            $jsonError = $this->sendJsonError( $messageOnFalse );
        }

        if ($jsonError) {
            ob_end_clean();
            echo $jsonError;
            flush();
            exit;
        }
    }

    /**
     * Method is using the Error-Message stored in this->_lastDisposableError
     * @return mixed
     */
    protected function sendJsonVorlaufError() {
        return $this->sendJsonError( $this->_lastDisposableError );
    }
    
    public function checkconcurrentactivitiesAction()
    {   
        $rq = $this->getRequest();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $checkTour      = (int)$rq->getParam('tour_id', '');
        $checkTimeline  = (int)$rq->getParam('timeline_id', '');
        $checkPortlet   = (int)$rq->getParam('portlet_id', '');
        $checkVorgang   = (int)$rq->getParam('Auftragsnummer', '');
        $checkMandant   = (int)$rq->getParam('Mandant', ($checkVorgang ? 10 : '') );
        $checkLager     = (int)$rq->getParam('lager_id', '');
        $checkMaxAge    = (int)$rq->getParam('maxage', 5);
        
        $checkUserId    = MyProject_Auth_Adapter::getUserId();
        
        $select = 'COUNT(1) ';
        $from   = 'mr_touren_dispo_aktivitaet A ' . PHP_EOL;
        $where  = 'user_id <> ' . $db->quote($checkUserId) . PHP_EOL
                  .'AND zugriffszeit >= DATE_ADD(NOW(), INTERVAL -'.$checkMaxAge.' MINUTE)' . PHP_EOL;
                  ;
        
        if ($checkVorgang) {
            $from.= ' LEFT JOIN mr_touren_dispo_vorgaenge T ON (A.tour_id = T.tour_id)' . PHP_EOL;
            $where.= 'AND T.Auftragsnummer = ' . $db->quote($checkVorgang)
                    .'AND T.Mandant = ' . $db->quote($checkMandant) . PHP_EOL;
        }
        
        $sql = 'SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where;

        if ($checkTour)        $sql.= 'AND A.tour_id = ' . $db->quote($checkTour) . PHP_EOL;
        elseif ($checkTimeline)$sql.= 'AND timeline_id = :timeline_id ' . $db->quote($checkTimeline) . PHP_EOL;
        elseif ($checkPortlet) $sql.= 'AND portlet_id = :portlet_id ' . $db->quote($checkPortlet) . PHP_EOL;
        elseif ($checkLager)   $sql.= 'AND lager_id = :lager_id ' . $db->quote($checkLager) . PHP_EOL;
        
        $CountActivities = (int)$db->fetchOne($sql);
        
        //die($CountActivities . PHP_EOL . $sql);
        
        // Send Result direct as Json without use of MVC
        $this->_helper->json($CountActivities);
    }
    
    public function getabschlussprozenteAction() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/abschlussmengen.ini');
        $re = array();
        foreach($config->abschlussmengen as $k => $v) $re[$k] = $v;
        $this->_helper->json( $re );
    }
    
    public function showconcurrentactivities() 
    {
        $rq = $this->getRequest();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $checkTour      = (int)$rq->getParam('tour_id', '');
        $checkTimeline  = (int)$rq->getParam('timeline_id', '');
        $checkPortlet   = (int)$rq->getParam('portlet_id', '');
        $checkVorgang   = (int)$rq->getParam('Auftragsnummer', '');
        $checkMandant   = (int)$rq->getParam('Mandant', ($checkVorgang ? 10 : '') );
        $checkLager     = (int)$rq->getParam('lager_id', '');
        $checkMaxAge    = (int)$rq->getParam('maxage', 5);
        
        $checkUserId    = MyProject_Auth_Adapter::getUserId();
        
        $select = 'user_name, A.*,';
        $from   = 'mr_touren_dispo_aktivitaet A ' . PHP_EOL
                 .'LEFT JOIN mr_user U ON(A.user_id = U.user_id) ' . PHP_EOL;
        
        $where  = 'user_id <> ' . $db->quote($checkUserId) . PHP_EOL
                  .'AND zugriffszeit >= DATE_ADD(NOW(), INTERVAL -'.$checkMaxAge.' MINUTE)' . PHP_EOL;
                  ;
        
        if ($checkVorgang) {
            $from.= ' LEFT JOIN mr_touren_dispo_vorgaenge T ON (A.tour_id = T.tour_id)' . PHP_EOL;
            $where.= 'AND T.Auftragsnummer = ' . $db->quote($checkVorgang)
                    .'AND T.Mandant = ' . $db->quote($checkMandant) . PHP_EOL;
        }
        
        $sql = 'SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where;

        if ($checkTour)        $sql.= 'AND A.tour_id = ' . $db->quote($checkTour) . PHP_EOL;
        elseif ($checkTimeline)$sql.= 'AND timeline_id = :timeline_id ' . $db->quote($checkTimeline) . PHP_EOL;
        elseif ($checkPortlet) $sql.= 'AND portlet_id = :portlet_id ' . $db->quote($checkPortlet) . PHP_EOL;
        elseif ($checkLager)   $sql.= 'AND lager_id = :lager_id ' . $db->quote($checkLager) . PHP_EOL;
        
        $CountActivities = (int)$db->fetchOne($sql);
        
        // die($CountActivities . PHP_EOL . $sql);
        
        // Send Result direct as Json without use of MVC
        $this->_helper->json($CountActivities);
    }
    
    public function vorgangstimetableAction()
    {
        $timeIn = time();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $role = MyProject_Auth_Adapter::getUserRole();

        /** @var MyProject_Acl $acl */
        $acl = Zend_Registry::get('acl');
        $this->view->ajax_response->allowed = new stdClass();
        $this->view->ajax_response->allowed->removeresource = 
            $acl->isAllowed($role, 'touren_ajax', 'removeresource');
        $this->view->ajax_response->allowed->updatetimetable = 
            $acl->isAllowed($role, 'touren_ajax', 'updatetimetable');        
        $this->view->ajax_response->allowed->addportletandrouteserie = 
            $acl->isAllowed($role, 'touren_ajax', 'addportletandrouteserie');
        $this->view->ajax_response->allowed->addportletandroute = 
            $acl->isAllowed($role, 'touren_ajax', 'addportletandroute');
        
        /* @var $TV Model_TourenDispoVorgaenge */
        $TV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        /** @var $TV Model_TourenDispoAuftraege */      
        $TA = new Model_TourenDispoAuftraege();
//        $TA = MyProject_Model_Database::loadModel('tourenDispoAuftraege');  
        
        $rq = $this->getRequest();
        $format = $rq->getParam('format', 'html');
        $tour_id = $rq->getParam('tour_id', 0);
//        $tldata  = $rq->getParam('data', array());
        
        $mandant = 0;
        $auftragsnr = 0;        
        
        if ($tour_id) {
            
            $this->view->ajax_response->data = $TV->fetchEntry($tour_id);            
            
            $mandant    = $this->view->ajax_response->data['Mandant'];
            $auftragsnummer = $this->view->ajax_response->data['Auftragsnummer'];  
            
            $auftragsstatus = $TA->fetchEntry($mandant, $auftragsnummer);

            if (!$auftragsstatus) {
                $auftragsstatus = [
                    "Mandant" => $mandant,
                    "Auftragsnummer" => $auftragsnummer,
                    "auftrag_disponiert_user" => 0,
                    "auftrag_disponiert_am" => "",
                    "auftrag_abgeschlossen_user" => "",
                    "auftrag_abgeschlossen_am" => "",
                    "auftrag_wiedervorlage_am" => "",
                    "auftrag_abschluss_summe" => "",
                    "auftrag_abschluss_prozent" => "",
                    "tour_dispo_count" => 0,
                    "tour_abschluss_count" => 0,
                    "tour_neulieferungen_count" => 0,
                    "wws_last_geaendertam" => "",
                ];
            }


            if (is_array($auftragsstatus)) {
                foreach ($auftragsstatus as $k => $v) {
                    if (!isset($this->view->ajax_response->data[$k]))
                        $this->view->ajax_response->data[$k] = $v;
                }
            }
            
            $this->view->ajax_response->list = $TV->tourlistByANR(
                $mandant, $auftragsnummer, array('DVon' => '1')
            );
            
        }
        
        if ($format == 'json'){
            $this->_helper->viewRenderer->setRender('jsonresponse');
        }
        else{
            $this->_helper->viewRenderer->setRender( $rq->getActionName() );
        }
        
    }
    
    public function vorgangspositionenAction() 
    {        
        $this->view->ajax_response = new stdClass();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $role = MyProject_Auth_Adapter::getUserRole();
        
        $acl = Zend_Registry::get('acl');
        $this->view->ajax_response->allowed = new stdClass();
        $this->view->ajax_response->allowed->updatepositionen = 
            $acl->isAllowed($role, 'touren_ajax', 'updatepositionen');
        $this->view->ajax_response->allowed->finishtourdispo = 
            $acl->isAllowed($role, 'touren_ajax', 'finishtourdispo');
        $this->view->ajax_response->allowed->opentourdispo = 
            $acl->isAllowed($role, 'touren_ajax', 'opentourdispo');
        $this->view->ajax_response->allowed->finishauftragsdispo = 
            $acl->isAllowed($role, 'touren_ajax', 'finishauftragsdispo');
        $this->view->ajax_response->allowed->openauftragsdispo = 
            $acl->isAllowed($role, 'touren_ajax', 'openauftragsdispo');
        
        $NAME = Zend_Db_Table::NAME;
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblDA = MyProject_Model_Database::loadStorage('tourenDispoAuftraege')->info($NAME);
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format = $rq->getParam('format', 'json');
        $posBelongTo = $rq->getParam( 'belong', 'all');
        
        $sqlVorgangsIdByTourId =
             'SELECT dv.*, da.auftrag_abgeschlossen_am, da.auftrag_abgeschlossen_user, da.auftrag_disponiert_am, da.auftrag_disponiert_user  '
            .'FROM ' . $tblDV .  ' dv '
            .'LEFT JOIN ' . $tblDA . ' da  USING(Mandant, Auftragsnummer) '
            .'WHERE dv.tour_id = :tour_id LIMIT 1';

        $row = $db->fetchRow($sqlVorgangsIdByTourId, array(':tour_id' => $tour_id), Zend_Db::FETCH_ASSOC);

        $mandant = $row['Mandant'];
        $auftragsnr = $row['Auftragsnummer'];
        $this->view->ajax_response->data = new stdClass();
        $this->view->ajax_response->data->tour_id = $tour_id;
        $this->view->ajax_response->data->mandant = $mandant;
        $this->view->ajax_response->data->auftragsnummer = $auftragsnr;
        $this->view->ajax_response->data->auftrag_disponiert_am      = $row['auftrag_disponiert_am'];
        $this->view->ajax_response->data->auftrag_disponiert_user    = $row['auftrag_disponiert_user'];
        $this->view->ajax_response->data->auftrag_abgeschlossen_am   = $row['auftrag_abgeschlossen_am'];
        $this->view->ajax_response->data->auftrag_abgeschlossen_user = $row['auftrag_abgeschlossen_user'];
        $this->view->ajax_response->data->dispovorgang = $row;
        $this->view->ajax_response->msg = ''; //$sqlAPositionen;

        if ($format == 'json')
            $this->_helper->viewRenderer->setRender('jsonresponse');
        else
            $this->_helper->viewRenderer->setRender('vorgangspositionen');
        
        try {
            /* @var $modelDP Model_TourenDispoPositionen */
            $modelDP = MyProject_Model_Database::loadModel('tourenDispoPositionen');
            $this->view->ajax_response->data->positionen = $modelDP->getPositionen($tour_id, $posBelongTo);
        } catch(Exception $e) {
            die( $e->getTraceAsString() );
            $this->view->ajax_response->error = $e->getMessage() . PHP_EOL .$e->getTraceAsString();
        }
    }
    
    
    public function vorgangsgruppierungAction() 
    {        
        $this->view->ajax_response = new stdClass();
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format  = $rq->getParam('format');
        
        $storageDV = new Model_Db_TourenDispoVorgaenge();
        $modelDA   = new Model_TourenDispoAuftraege();
        
        $this->view->ajax_response->msg = ''; //$sqlAPositionen;

        if ($format == 'json')
            $this->_helper->viewRenderer->setRender('jsonresponse');
        else
            $this->_helper->viewRenderer->setRender('vorgangsgruppierung');
        
        try 
        {
            $tourRow = $storageDV->find($tour_id)->current();     
//            die( print_r($tourRow, 1));
            $this->view->ajax_response->data = $modelDA->getGruppierteVorgaenge($tourRow->Mandant, $tourRow->Auftragsnummer);
            
            foreach($this->view->ajax_response->data as $i => $v) {
                $this->view->ajax_response->data[$i]['touren'] = array();
                if ($v['Auftragsnummer']) {
                    $rowset = $storageDV->fetchAll('Mandant = '.$v['Mandant'] . ' AND Auftragsnummer = ' . $v['Auftragsnummer']);
                    if ($rowset) $this->view->ajax_response->data[$i]['touren'] = $rowset->toArray();
                }
            }
        } catch(Exception $e) {
            die( $e->getTraceAsString() );
            $this->view->ajax_response->error = $e->getMessage() . PHP_EOL .$e->getTraceAsString();
        }
    }
    
    public function tourlinksAction()
    {
        $rq = $this->getRequest();
        $mandant = (int) $rq->getParam('mandant', 0);
        $auftrag = (int) $rq->getParam('auftrag', 0);
        $this->_helper->viewRenderer->setRender('tourlinks');
        
        $modelDT = new Model_TourenDispoVorgaenge;        
        $modelTA = new Model_TourenDispoAuftraege();
        
        $this->view->data = new stdClass();
        $this->view->data->mandant  = $mandant;
        $this->view->data->auftrag  = $auftrag;
        $this->view->data->status   = $modelTA->fetchEntry($mandant, $auftrag);
        
        $this->view->data->tourlist = $modelDT->tourlistByANR(
                $mandant, $auftrag, array(), '', 100, 0, 'DatumVon');
    }
    
    public function vorgangsresourcenAction() 
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->data = new stdClass();
        
        $role = MyProject_Auth_Adapter::getUserRole();
        
        $acl = Zend_Registry::get('acl');
        $this->view->ajax_response->allowResourceRemove = 
            $acl->isAllowed($role, 'touren_ajax', 'removeresource');
        $this->view->ajax_response->allowResourceUpdate = 
            $acl->isAllowed($role, 'touren_ajax', 'updateresources');
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format  = $rq->getParam('format', 'html');
        
        if ($format == 'json')
            $this->_helper->viewRenderer->setRender('jsonresponse');
        else
            $this->_helper->viewRenderer->setRender('vorgangsresourcen');
        
        $NAME = Zend_Db_Table::NAME;
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblDF = MyProject_Model_Database::loadStorage('tourenDispoFuhrpark')->info($NAME);
        $tblDM = MyProject_Model_Database::loadStorage('tourenDispoMitarbeiter')->info($NAME);
        $tblDW = MyProject_Model_Database::loadStorage('tourenDispoWerkzeug')->info($NAME);
        
        $tblDMB = MyProject_Model_Database::loadStorage('tourenDispoMitarbeiterText')->info($NAME);

        $tblFP = MyProject_Model_Database::loadStorage('fuhrpark')->info($NAME);
        $tblMA = MyProject_Model_Database::loadStorage('mitarbeiter')->info($NAME);
        $tblWZ = MyProject_Model_Database::loadStorage('werkzeug')->info($NAME);
        
        $sqlVorgangsIdByTourId =
             'SELECT Mandant, Auftragsnummer '
            .' FROM ' . $tblDV . "\n"
            .' WHERE tour_id = :tour_id';
        $rows = $db->fetchAll($sqlVorgangsIdByTourId, array(':tour_id' => $tour_id));
        
        if ($rows && count($rows)) {

            $mandant = $rows[0]['Mandant'];
            $auftragsnr = $rows[0]['Auftragsnummer'];
            $this->view->ajax_response->data = new stdClass();
            $this->view->ajax_response->data->tour_id = $tour_id;
            $this->view->ajax_response->data->mandant = $mandant;
            $this->view->ajax_response->data->auftragsnummer = $auftragsnr;

            $sqlRsrc['FP'] = 
                "SELECT tr.id, 'FP' resourceType, r.*, CONCAT(r.hersteller,' ',r.modell) name "
                ."FROM $tblDF tr "
                ."LEFT JOIN $tblFP r ON tr.fuhrpark_id = r.fid "
                ."WHERE tr.tour_id = :tour_id";
            //die( $sqlRsrc['FP']);

            $sqlRsrc['MA'] = 
                "SELECT tr.id, 'MA' resourceType, r.*, CONCAT(r.vorname,' ',r.name,' [',r.eingestellt_als,']') name, "
                ."b.einsatz_ab, b.bemerkung "
                ."FROM $tblDM tr "
                ."LEFT JOIN $tblDMB b on tr.id = b.id "
                ."LEFT JOIN $tblMA r ON tr.mitarbeiter_id = r.mid "
                ."WHERE tr.tour_id = :tour_id";

            $sqlRsrc['WZ'] = 
                "SELECT tr.id, 'WZ' resourceType, r.*, bezeichnung name "
                ."FROM $tblDW tr "
                ."LEFT JOIN $tblWZ r ON tr.werkzeug_id = r.wid "
                ."WHERE tr.tour_id = :tour_id";

            $result = array();
            foreach($sqlRsrc as $key => $_sql) {
                $result[$key] = $db->fetchAll(
                    $_sql, 
                    array(':tour_id'=>$tour_id), 
                    Zend_Db::FETCH_ASSOC 
                );
            }

            $this->view->ajax_response->data->result = &$result;
        } else {
            $this->view->ajax_response->data->result = array();
        }
        // Send Result direct as Json without use of MVC
        //$this->_helper->json($result);
    }
    
    public function vorgangsresourcendefaultsAction() 
    {
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format  = $rq->getParam('format', 'html');

        $role = MyProject_Auth_Adapter::getUserRole();

        $acl = Zend_Registry::get('acl');
        $this->view->allowed = new stdClass();
        $this->view->data = new stdClass();

        $this->view->allowed->removeresourcedefault =
            $acl->isAllowed($role, 'touren_ajax', 'removeresourcedefault');

        
        /* @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $this->view->data->default = $modelDV->fetchEntry($tour_id);
        $this->view->data->defaultResources = $modelDV->getResources($tour_id);
        $data = &$this->view->data->default;
        
        /* @var $modelTL Model_TourenTimelines */
        $modelTL = $tblTL = MyProject_Model_Database::loadModel('tourenTimelines');
        $this->view->data->timeline = $modelTL->fetchEntry($data['timeline_id']);
        $this->view->data->vorgaenge = $modelTL->getDispoVorgaenge($data['timeline_id']);
        $vorgaenge = &$this->view->data->vorgaenge;
        
        /* @var $_vg Model_TourenDispoVorgaenge */
        foreach($vorgaenge as $_vgIdx => $_vg) {
            $vorgaenge[$_vgIdx]['resources'] = $modelDV->getResources($_vg['tour_id']);
        }
                
        $this->_helper->viewRenderer->setRender(
            $format == 'json' ? 'jsonresponse' : 'vorgangsresourcendefaults');
        
    }
    
    public function timelinedataAction() 
    {
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $NAME = Zend_Db_Table::NAME;
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $TL = MyProject_Model_Database::loadModel('tourenTimelines');
        $TP = MyProject_Model_Database::loadModel('tourenPortlets');
        $TV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $rq = $this->getRequest();
        $format = $rq->getParam('format', 'html');
        $tlid = $rq->getParam('id', 0);
        $tour_id = $rq->getParam('tour_id', 0);
        $tldata = $rq->getParam('data', array());
        
        $this->view->ajax_response->portlet     = null;
        $this->view->ajax_response->defaultTour = null;
        $this->view->ajax_response->rows        = null;
        
        if (!$tlid && $tour_id) {
            $tourData = $TV->fetchEntry($tour_id);
            $tlid = $tourData['timeline_id'];
        } elseif ($tlid) {
            $tourData = $TV->getStorage()->find('IsDefault = 1 AND timeline_id = '.$tlid)->current();
            if ($tourData) $this->view->ajax_response->defaultTour = $tourData->toArray();
        } else {
            $this->view->ajax_response->defaultTour = $tourData;
        }
        
        if ($tlid) {
            $data = $TL->fetchEntry($tlid);
//            die(print_r($data,1));
            $this->view->ajax_response->data = $data;
            if ($data && $data['portlet_id'])
                $this->view->ajax_response->portlet = $TP->fetchEntry($data['portlet_id']);
            
            if ($data && $data['group_key']) {
                $select = $db->select(false);
                $select->from(array('TL'=>$TL->getStorage()->info($NAME)));
                $select->joinLeft(
                        array('TP'=>$TP->getStorage()->info($NAME)),
                        'TL.portlet_id = TP.portlet_id');
                $select->joinLeft(
                        array('TV'=>$TV->getStorage()->info($NAME)),
                        'TL.timeline_id = TV.timeline_id',
                        array('tour_ID', 'Mandant', 'Auftragsnummer', 'ZeitVon')
                        );
                $select->where('group_key = :group_key' );
                $select->where('TV.IsDefault = 0' );
                $select->order(array('TL.timeline_id', 'TV.ZeitVon'));
                //$select->group('timeline_id');
                
                $rowset = $db->fetchAll($select, array('group_key'=>$data['group_key']), Zend_Db::FETCH_ASSOC);
                $this->view->ajax_response->rows = $rowset;
//                echo '<pre>#'.__LINE__.' '.strtr($select->assemble(),array(':group_key'=>$db->quote($data['group_key'])))  .'</pre>'; // . '; rowset:'.print_r($rowset,1);
//                die('<pre>#' . __LINE__ . ' ' . __METHOD__ . PHP_EOL . print_r($rowset, 1) . '</pre>');
            }
        }
        
        if ($format == 'json')
        $this->_helper->viewRenderer->setRender('jsonresponse');
        else
        $this->_helper->viewRenderer->setRender('timelinedata');
    }

    /**
     *
     */
    public function updateresourcesAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->type = 'success';
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;

        $DM  = new Model_TourenDispoMitarbeiter();
        $DMB = new Model_TourenDispoMitarbeiterText();
        
        $rq = $this->getRequest();
        $data = $rq->getParam('rsrc', array());

        $aMaIDs = [];
        foreach($data as $k => $_data) {
            list($_rsrcType, $_id) = explode(':', $k);
            $aMaIDs[$_id] = $_data;

            if (!empty($_rsrcType) && $_rsrcType != 'MA') {
                return $this->sendJsonError('Mitarbeiter-Resource erwartet, ' . $_rsrcType . ' erhalten');
            }

            if (!$this->dayIsDisposable($DM->getDatum($_id))) {
                return $this->sendJsonVorlaufError();
            }
        };

        foreach($aMaIDs as $_id => $_data) {

            try {
                if (!$DMB->replace( array_merge($_data, array('id'=>$_id)))) {
                    return $this->sendJsonError('Mitarbeiter-Resource mit ID ' . $_id . ' konnte nicht aktualisiert werden!');
                }
            } catch(\Exception $e) {
                return $this->sendJsonError($e->getMessage());
            }
            //echo 'update record with id ' . $_id . '<br>' . PHP_EOL;
        }

        return $this->sendJsonSuccess('Ressourcen wurden aktualisiert!');
    }
    
    public function updatetimelineAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $NAME = Zend_Db_Table::NAME;
        $TL = MyProject_Model_Database::loadModel('tourenTimelines');
        $TV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $rq = $this->getRequest();
        $tlid = $rq->getParam('timeline_id', 0);
        $tour_id = $rq->getParam('tour_id', 0);
        $tldata = $rq->getParam('data', array());
        
        if (!$tlid && $tour_id) {
            $tourData = $TV->fetchEntry($tour_id);
            $tlid = $tourData['timeline_id'];
        }
        
        try {
        if ($tlid) {
            $TL = new Model_TourenTimelines();
            $re = $TL->update($tldata, $tlid);
            if (!$re->success) throw new Exception(print_r($re, 1));
        }    
        } catch(Exception $e) {
            $this->view->ajax_response->error   = $e->getMessage();
            $this->view->ajax_response->success = false;
            $this->view->ajax_response->type    = 'error';           
        }
    }
    
    public function updatetimetableAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;

        /** @var Model_TourenDispoVorgaenge $TV */
        $TV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', 0);
        $data = (array)$rq->getParam('data', []);

        $this->_require(!empty($tour_id), 'Fehlende Angabe zur tour_id!');
        $this->_require(!empty($data['DatumVon']), 'Fehlende Datumgsangabe für Aktualisierung!');
        $this->_require(!empty($data['ZeitVon']), 'Fehlende Angabe ZeitVon für Aktualisierung!');
        $this->_require(!empty($data['ZeitBis']), 'Fehlende Angabe ZeitBis für Aktualisierung!');

        $datum = $TV->getDatum( (int) $tour_id );
        if (!empty( $datum) && !$this->dayIsDisposable($datum)) {
            return $this->sendJsonVorlaufError();
        }
        
        if ($tour_id) {
            if (!$TV->update($data, $tour_id)) {
                $this->view->ajax_response->error = $TV->error();
                return $this->sendJsonError($TV->error());
            } else {
                return $this->sendJsonSuccess('Zeitangaben der Tour wurden aktualisiert!');
            }
        }
    }
    
    public function finishtourdispoAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        
        /** @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $this->view->ajax_response->msg.= PHP_EOL . $tour_id . ', ' . MyProject_Auth_Adapter::getUserName();
        $modelDV->finishdispositionen($tour_id, MyProject_Auth_Adapter::getUserName());
        $this->view->ajax_response->msg.= '#'.__LINE__ . ' ' . __METHOD__;
    }
    
    public function opentourdispoAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        
        /** @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $this->view->ajax_response->msg.= PHP_EOL . $tour_id . ', ' . MyProject_Auth_Adapter::getUserName();
        $modelDV->opendispositionen($tour_id);
        $this->view->ajax_response->msg.= '#'.__LINE__ . ' ' . __METHOD__;
    }
    
    public function finishauftragsdispoAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        
        /** @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $row = $modelDV->getStorage()->find($tour_id)->current();
        if ($row) {
            $modelDA = MyProject_Model_Database::loadModel('tourenDispoAuftraege');
            $modelDA->finishdispo($row->Mandant, $row->Auftragsnummer, MyProject_Auth_Adapter::getUserName());
        }
        $this->view->ajax_response->msg.= '';
        
        $this->view->ajax_response->msg.= '#'.__LINE__ . ' ' . __METHOD__;
    }
    public function openauftragsdispoAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        
        /** @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $row = $modelDV->getStorage()->find($tour_id)->current();
        if ($row) {
            $modelDA = MyProject_Model_Database::loadModel('tourenDispoAuftraege');
            $modelDA->opendispo($row->Mandant, $row->Auftragsnummer, MyProject_Auth_Adapter::getUserName());
        }
        $this->view->ajax_response->msg.= '';
        
        $this->view->ajax_response->msg.= '#'.__LINE__ . ' ' . __METHOD__;
    }
    
    protected function fitDeFloatNumbers($fitFloatNumber) {
        $floatNumber = trim($fitFloatNumber, ',.');
        if ( is_numeric($floatNumber) || preg_match('/^\d+(\.\d+)?$/', $floatNumber) ) {
            return (float) $floatNumber;
        }
        
        if ( preg_match('/^[0-9.]+,[0-9]+$/', $floatNumber)) {
            return (float) strtr($floatNumber, array('.' => '', ',' => '.'));
        }        
        return $floatNumber;
    }
    
    public function finishauftragsabschlussAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        $mandant = (int) $rq->getParam('Mandant');
        $auftragsnr = (int) $rq->getParam('Auftragsnummer');
        $summe = $rq->getParam('abschluss_summe');
        $prznt = $rq->getParam('abschluss_prozent');
        $error = '';
        
        if ($summe !== '' && !empty($summe)) {
            if (!preg_match('/^\d+,?\d*$/', $summe)) $error.= 'Die Angabe Summe ist kein gültige Zahlenformat!' . PHP_EOL;
        }
        if ($prznt !== '' && !empty($prznt)) {
            if (!preg_match('/^\d+,?\d*$/', $prznt)) $error.= 'Die Angabe Prozent ist kein gültige Zahlenformat!' . PHP_EOL;
        }
        
        $data = array(
            'auftrag_abschluss_summe'   => $this->fitDeFloatNumbers( $summe ),
            'auftrag_abschluss_prozent' => $this->fitDeFloatNumbers( $prznt ),
        );
        
        if ($tour_id) {
            /** @var $modelDV Model_TourenDispoVorgaenge */
            $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
            $row = $modelDV->getStorage()->find($tour_id)->current();
            if ($row) {
                $mandant = $row->Mandant;
                $auftragsnr = $row->Auftragsnummer;
            }
        }
        
        //die('#'.__METHOD__ . ' data: '. print_r($data, 1));
        if ($mandant && $auftragsnr) {
            $modelDA = MyProject_Model_Database::loadModel('tourenDispoAuftraege');
            $modelDA->finishauftrag($mandant, $auftragsnr, $data, MyProject_Auth_Adapter::getUserName());
        }
        $this->view->ajax_response->error.= $error;
    }
    
    public function openauftragsabschlussAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        
        /** @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $row = $modelDV->getStorage()->find($tour_id)->current();
        if ($row) {
            $modelDA = MyProject_Model_Database::loadModel('tourenDispoAuftraege');
            $modelDA->openauftrag($row->Mandant, $row->Auftragsnummer, MyProject_Auth_Adapter::getUserName());
        }
        $this->view->ajax_response->msg.= '';
        
        $this->view->ajax_response->msg.= '#'.__LINE__ . ' ' . __METHOD__;
    }
    
    public function finishtourabschlussAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        
        /** @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $this->view->ajax_response->msg.= PHP_EOL . $tour_id . ', ' . MyProject_Auth_Adapter::getUserName();
        $modelDV->finishtourabschluss($tour_id, MyProject_Auth_Adapter::getUserName());
        $this->view->ajax_response->msg.= '#'.__LINE__ . ' ' . __METHOD__;
    }
    
    public function opentourabschlussAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $rq = $this->getRequest();
        $tour_id = (int) $rq->getParam('tour_id');
        
        /** @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $this->view->ajax_response->msg.= PHP_EOL . $tour_id . ', ' . MyProject_Auth_Adapter::getUserName();
        $modelDV->opentourabschluss($tour_id);
        $this->view->ajax_response->msg.= '#'.__LINE__ . ' ' . __METHOD__;
    }

    public function setwiedervorlageAction()
    {
        $r = $this->getRequest();
        $datum = $r->getParam('date', '');
        $mandant = $r->getParam('Mandant', '');
        $auftragsnr = $r->getParam('Auftragsnummer', '');

        $modelDA = new Model_TourenDispoAuftraege();
        $result = $modelDA->setWiedervorlage($mandant, $auftragsnr, $datum);

        if ($result->success) {
            if ($datum) {
                return $this->sendJsonSuccess('Vorgang wurde zur Wiedervorlage am ' . $datum . ' vorgemerkt!');
            } else {
                return $this->sendJsonSuccess('Rückstellung wurde aufgehoben!');
            }
        } else {
            $this->sendJsonError('Fehler beim Speichern!');
        }
    }

    public function setTourFarbklasseAction()
    {
        $userIdentity = MyProject_Auth_Adapter::getIdentity();
        $r = $this->getRequest();
        $tour_id = $r->getParam('tour_id', '');
        $sFarbklasse = $r->getParam('fk', '');

        $this->_require(
            (!empty($tour_id) && is_numeric($tour_id)),
            'Fehlender oder ungültiger Parameter tour_id: ' . $tour_id);

        $this->_require(
            (isset($sFarbklasse)),
            'Fehlender oder ungültiger Parameter fk (Farbklasse): ' . $sFarbklasse);

        $tour_id = (int)$tour_id;
        $modelDV = new Model_TourenDispoVorgaenge();

        if ($userIdentity->user_role === 'innendienst') {
            $savedFK = $modelDV->getFarbklasse($tour_id);

            if ( $sFarbklasse === 'Gruen' ) {
                return $this->sendJsonError(
                    'Die Tourfarbe kann vom Innendienst nicht auf Grün gesetzt werden!');
            }

            if ($savedFK === 'Gruen') {
                return $this->sendJsonError(
                    'Die Tourfarbe kann vom Innendienst nicht mehr geändert werden, '
                        . ' wenn sie einmal auf grün gesetzt wurde!');
            }
        }

        try {
            $modelDV->setFarbklasse($tour_id, $sFarbklasse);
            return $this->sendJsonSuccess( 'Farbklasse "' . $sFarbklasse . '" wurde übernommen!');

        } catch(Exception $e) {
            return $this->sendJsonError('Farbklasse "' . $sFarbklasse . '" konnte nicht übernommen werden!');
        }
    }
    
    public function getwiedervorlageAction()
    {
        $r = $this->getRequest();
        $mandant    = $r->getParam('Mandant', '');
        $auftragsnr = $r->getParam('Auftragsnummer', '');
        
        $modelDA = new Model_TourenDispoAuftraege();
        $modelAK = new Model_VorgaengeDispoFilter();
        $row = $modelDA->fetchEntry($mandant, $auftragsnr);
        if (!$row) {
            $modelDA->importAuftrag($mandant, $auftragsnr, false);
            $row = $modelDA->fetchEntry($mandant, $auftragsnr);
        }
        $ak = $modelAK->fetchEntry($mandant, $auftragsnr);
        
        if ($row) {
            $row['Auftragswert'] = $ak['Auftragswert'];
            $this->_helper->json(array(
                'type' => 'success',
                'success' => true,
                'date' => $row['auftrag_wiedervorlage_am'],
                'data' => $row,
            ));
        } else {
            $this->_helper->json(array(
                'type' => 'error',
                'success' => false,
                'error' => 'Eintrag wurde nicht gefunden!',
                'date'  => ''
            ));
        }
    }
        
    public function updateabschlusspositionenAction() 
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        $this->view->ajax_response->error = '';
        
        $rq = $this->getRequest();
        
        $model = new Model_TourenDispoPositionen();
        $tour_id = $rq->getParam('tour_id', 0);
        $PKeys = (array) $rq->getParam('PositionKeys', array());
        
        $LMengen     = (array) $rq->getParam('LMengen',   array());
        $RKLMengen   = (array) $rq->getParam('RKLMengen', array());
        $NLMengen    = (array) $rq->getParam('NLMengen',  array()); // NeuLieferungen
        $RKLGrund    = (array) $rq->getParam('RKLGrund',  array());
        $NLGrund     = (array) $rq->getParam('NLGrund',   array());
        $Bemerkungen = (array) $rq->getParam('Bemerkungen', array());
        
        $RKLGrundOptions = array('', 'Kunde', 'Mertens');
        $NLGrundOptions  = array('', 'Kunde', 'Mertens');
        $posErrors  = array();
        $updateRowsData = array();
        foreach($PKeys as $_pnr) {
            
            // Auslesen
            $_lm = (isset($LMengen[$_pnr])     ? $LMengen[$_pnr]     : null);
            $_rm = (isset($RKLMengen[$_pnr])   ? $RKLMengen[$_pnr]   : 0);
            $_nm = (isset($NLMengen[$_pnr])    ? $NLMengen[$_pnr]    : 0);
            
            $_rg = (isset($RKLGrund[$_pnr])    ? $RKLGrund[$_pnr]    : '');
            $_ng = (isset($NLGrund[$_pnr])     ? $NLGrund[$_pnr]     : '');
            $_bm = (isset($Bemerkungen[$_pnr]) ? $Bemerkungen[$_pnr] : '');
            
            // Validieren
            if (!is_numeric($_lm)) $posErrors[$_pnr]['LMengen']   = 'Liefermenge ist keine natuerliche Zahl!';
            if (!is_numeric($_rm)) $posErrors[$_pnr]['RKLMengen'] = 'ReklaMenge ist keine natuerliche Zahl!';
            if (!is_numeric($_lm)) $posErrors[$_pnr]['NLMengen']  = 'Neulieferungsmenge ist keine natuerliche Zahl!';
            
            if (!in_array($_rg, $RKLGrundOptions)) 
                $posErrors[$_pnr]['RKLGrund']   = 'Grund fuer Rekla ist kein gueltiger Auswahlwert!';
            
            if (!in_array($_ng, $NLGrundOptions)) 
                $posErrors[$_pnr]['NLGrund']    = 'Grund fuer Rekla ist kein gueltiger Auswahlwert!';
            
            // Record-Update-Daten zusammenstellen.
            // Nur so lange keine Validierungsfehler auftreten
            if (!count($posErrors)) {
                $updateRowsData[] = array(
                    'tour_id'             => $tour_id,
                    'Positionsnummer'     => $_pnr,
                    'AbschlussMenge'      => (int) $_lm,
                    'AbschlussReklaMenge' => (int) $_rm,
                    'AbschlussReklaGrund' => $_rg,
                    'AbschlussNLMenge'    => (int) $_nm,
                    'AbschlussNLGrund'    => $_ng,
                    'AbschlussBemerkung'  => $_bm,                    
                );
            }            
        }
        
        if (!count($posErrors))  {
            foreach($updateRowsData as $_data) 
                $model->updateAbschlussPosition ($_data);
        } else {
            foreach($posErrors as $_pnr => $_flds) {
                foreach($_flds as $_fld => $_err)
                $this->view->ajax_response->error.= 'Pos ' . $_pnr. ': ' . $_err . PHP_EOL;
            }
        }
        
    }
    
    /**
     * @todo Zu viel Logik im Controller, die in den Model gehört
     * @throws Exception 
     */
    public function updatepositionenAction()
    {          
        
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        $this->view->ajax_response->error = '';
        
        $NAME  = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblAP = MyProject_Model_Database::loadStorage('auftragspositionen')->info($NAME);
        $tblBK = MyProject_Model_Database::loadStorage('bestellkoepfe')->info($NAME);
        $tblWB = MyProject_Model_Database::loadStorage('warenbewegungen')->info($NAME);
        
        $modelBP = MyProject_Model_Database::loadModel('bestellpositionen');
        $tblBP   = $modelBP->getStorage()->info($NAME);
        
        $modelDP = MyProject_Model_Database::loadModel('tourenDispoPositionen');
        $tblDP   = $modelDP->getStorage()->info($NAME);
        
        $modelBPM = MyProject_Model_Database::loadModel('bestellpositionenMeta');
        $tblBPM   = $modelBPM->getStorage()->info($NAME);
        
        $user = MyProject_Auth_Adapter::getUserName();
                  
        
//        echo '#'.__LINE__ . ' ' . __FILE__ . '<br/>' . PHP_EOL;
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_ASSOC);

            $rq              = $this->getRequest();
            $tour_id         = (int) $rq->getParam('tour_id');
            $mandant         = (int) $rq->getParam('Mandant');
            $auftragsnr      = (int) $rq->getParam('Auftragsnummer');
            $positionsMengen = $rq->getParam('PositionsMengen');
            $positionsKeys   = $rq->getParam('PositionKeys');
            $positionsCheck  = $rq->getParam('PositionsCheck');
            $positionsStellplatzNeu = $rq->getParam('PositionsStellplatzNeu');
            
                        
            $storageDV = new Model_Db_TourenDispoVorgaenge();
            $rowDV = $storageDV->find($tour_id)->current();
            
            if (!$rowDV ) {
                throw new Exception('Tour mit ID ' . $tour_id . ' wurde nicht gefunden!');
            }
            if ($rowDV->tour_disponiert_am && !preg_match('/^0000/', $rowDV->tour_disponiert_am)) {
                throw new Exception('Tour wurde bereits als fertig disponiert markiert!');
            }
            
            $auftragspositionen = $positionsKeys;
            $selectedpositionen = (is_array($positionsCheck))  ? array_keys($positionsCheck)  : array();
            
//            foreach($selectedpositionen as $_i => $_k) {
//                if (@empty($positionsMengen[$_k])) unset($selectedpositionen[$_i]);
//            }
//            sort($selectedpositionen);
            
            // Hole bereits angelegte Dispostionen
            $sql =
                'SELECT Positionsnummer '
                .'FROM ' . $tblDP . ' '
                .'WHERE tour_id = '.$db->quote($tour_id).' '
                .'AND Mandant = '.$db->quote($mandant).' '
                .'AND Auftragsnummer = '.$db->quote($auftragsnr).' '
                .'AND Positionsnummer '. $db->quoteInto(' IN(?)',$auftragspositionen) ;
//            die('sql: '.$sql);
            
            $existingPositionen = $db->fetchCol($sql); //, array(':auftragspositionen',$auftragspositionen));            
            $unselectPositionen = array_diff($existingPositionen, $selectedpositionen);
            sort($unselectPositionen);
            
            if (0) die(
                    'auftragspositionen: ' . print_r($auftragspositionen,1) . PHP_EOL
                   .'existingPositionen: ' . print_r($existingPositionen,1) . PHP_EOL
                   .'selectedpositionen: ' . print_r($selectedpositionen,1) . PHP_EOL
                   .'unselectpositionen: ' . print_r($unselectPositionen,1) . PHP_EOL
                   .'positionsStellplatzNeu: ' . print_r($positionsStellplatzNeu,1) . PHP_EOL
            );
            $this->view->ajax_response->positionsKeys = print_r($positionsKeys,1);
            $this->view->ajax_response->selectedPositionen = print_r($selectedpositionen, 1);
            $this->view->ajax_response->existingPositionen = print_r($existingPositionen, 1);
            $this->view->ajax_response->unselectPositionen = print_r($unselectPositionen, 1);

            $newPositionen = array_diff($auftragspositionen, $existingPositionen);
            

            $unselectSql = 
                'DELETE FROM ' . $tblDP . ' '
                .'WHERE tour_id = '.$db->quote($tour_id).' '
                .'AND Mandant = '.$db->quote($mandant).' '
                .'AND Auftragsnummer = '.$db->quote($auftragsnr).' '
                .'AND Positionsnummer = :posnr';
            
            foreach($unselectPositionen as $_posnr) {                
                $db->query($unselectSql, array(
                    ':posnr' => $_posnr));
            }
            
            $updateSql = 
                'UPDATE ' . $tblDP . ' '
                .'SET DisponierteMenge = :menge '
                .'WHERE tour_id = '.$db->quote($tour_id).' '
                .'AND Mandant = '.$db->quote($mandant).' '
                .'AND Auftragsnummer = '.$db->quote($auftragsnr).' '
                .'AND Positionsnummer = :posnr';
            
//          echo 'existingPositionen: ' . print_r($existingPositionen, 1) . PHP_EOL;
            if (is_array($existingPositionen)) foreach($existingPositionen as $_posnr) {  
                if (!@isset($positionsMengen[$_posnr])) continue;
                $_pm = str_replace(',', '.',  str_replace('.', '', $positionsMengen[$_posnr]));
                
//                $_sql = strtr($updateSql, array(
//                    ':menge' => $positionsMengen[$_posnr],
//                    ':posnr' => $_posnr));
//                echo '#'. __LINE__." " . __FILE__ . " sql: " . $_sql . PHP_EOL;
//                $db->query($_sql);
                $db->query($updateSql, array(
                    ':menge' => $_pm,
                    ':posnr' => $_posnr));
                //echo '#'.__LINE__ . ' ' . $positionsMengen[$_posnr] . PHP_EOL;
            }
            
            /* @var $modelLogger Model_TourenDispoLog */
            $modelLogger = new Model_TourenDispoLog();
            $modelLogger->logTour($tour_id, 'update positionen');
            
            $existingSpSql =
                'SELECT Positionsnummer, Stellplatz FROM ' . $tblWB 
               .' WHERE '
               .' Mandant = '.$db->quote($mandant).' '
               .' AND Auftragsnummer = '.$db->quote($auftragsnr).' '
               .' AND Positionsnummer IN (' . implode(',', $newPositionen) . ')';
            
            //echo 'sql:' . $existingSpSql . '<br/>' . PHP_EOL;
            if ($newPositionen)
                $pairsPosnrSp = $db->fetchPairs($existingSpSql);
            else
                $pairsPosnrSp = array();
            
            $insertSql = 
                'INSERT ' . $tblDP . ' '
                .'SET DisponierteMenge = :menge, '
                .'tour_id = '.$db->quote($tour_id).', '
                .'Mandant = '.$db->quote($mandant).', '
                .'Auftragsnummer = '.$db->quote($auftragsnr).', '
                .'Positionsnummer = :posnr';
            
            if (is_array($newPositionen)) foreach($newPositionen as $_posnr) {
                if (!@isset($positionsMengen[$_posnr])) continue;
                $_pm = str_replace(',', '.',  str_replace('.', '', $positionsMengen[$_posnr]));
                
                $db->query($insertSql, array(
                    ':menge' => $_pm,
                    ':posnr' => $_posnr,
                ));
            }
            
            if (is_array($positionsStellplatzNeu)) foreach($positionsStellplatzNeu as $_posnr => $_sp) {
                // echo '#' . __LINE__. ' ' . $tour_id . ',' . $_posnr . ',' . $_sp . ',' . $user . '<br/>' . PHP_EOL;
                $row = $modelBP->getRowByAuftragsposition($mandant, $auftragsnr, $_posnr);
                if ($row) {                    
                    $modelBPM->editStellplatz($mandant, $row->Bestellnummer, $row->Positionsnummer, $_sp, $user);
                }
            }
            
        
        } catch(Exception $e) {
            die( $e->getMessage() . '<br/>' . $e->getTraceAsString() );
            $this->view->ajax_response->error = $e->getMessage() . PHP_EOL .$e->getTraceAsString();
        }
    }
    
    public function calendarweekdataAction()
    {
        $this->_helper->viewRenderer->setRender('calendarweekdata');
        $this->view->assign(array(
            'dateRange' => array('DatumVon'=>'x', 'DatumBis' => 'y'),
            'lager_id' => '9',
            'data' => array(1,2,3,4,5,6,7,8,9)
        ));
        
        $rq = $this->rq;
        $date = $rq->getParam('date', '');
        $kw   = $rq->getParam('kw', '');
        $lager_id = $rq->getParam('lager_id', '');
        
        if ($kw && preg_match('/^(?P<YEAR>\d{4})-(?P<KW>\d{1,2}?)$/', $kw, $m)) {
            // Ermittel das Start-Datum (Montag) der ersten KW
            // Kann auch im Vorjahr liegen
            // ISO-Regel, die Woche die den ersten Donnerstag des neuen Jahres enthält gilt als 1. KW
            $m['KW'] = intval($m['KW']);
            $yrStartTime = mktime(4,0,0,1,1,$m['YEAR']);
            $yrStartDay = date('w', $yrStartTime);
            $moDiff = 0;
            $moTime = 0;
            
            switch($yrStartDay) {
                case 1: /* Mo */ $moDiff = 0; break;
                case 2: /* Di */ $moDiff = -1; break;
                case 3: /* Mi */ $moDiff = -2; break;
                case 4: /* Do */ $moDiff = -3; break;
                case 5: /* fr */ $moDiff = 3; break;
                case 6: /* Sa */ $moDiff = 2; break;
                case 0: /* So */ $moDiff = 1; break;
            }
            
            $yrStartMoTime = $yrStartTime + ($moDiff*(24*3600));
            echo '<pre>#'.__LINE__. ' moTime: ' . date('Y-m-d', $yrStartMoTime) . PHP_EOL
                             . ' moDiff: ' . $moDiff . PHP_EOL
                             . 'yrStartDay: ' . $yrStartDay . PHP_EOL . '</pre>';
            $kwTime = $yrStartMoTime + ( ($m['KW']-1) * (7 * 24 * 3600));
        }
        elseif ($date && preg_match('/^(?P<Y>\d{4}-(?P<M>\d\d)-(?P<D>\d{2}$/', $date, $m)) {
            $time = mktime(1,0,0,$m['M'],$m['D'],$m['Y']);
            $wDay = date( 'w', $time);
            $moDiff = 0;
            
            $moDiff = ($wDay > 0) ? $wDay - 1 : -6;
            $kwTime = $time + ($moDiff*(24*3600));
        }
        else {
            $time = time();
            $wDay = date( 'w');
            $moDiff = 0;
            
            $moDiff = ($wDay > 0) ? $wDay - 1 : -6;
            $kwTime = $time + ($moDiff*(24*3600));  
        }
        
        $dateRange = array(
          'Von' => date('Y-m-d', $kwTime),
          'Bis' => date('Y-m-d', $kwTime + (7*24*3600))
        );
        
        $db = $this->db;
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblTL = MyProject_Model_Database::loadStorage('tourenTimelines')->info($NAME);
        $tblPT = MyProject_Model_Database::loadStorage('tourenPortlets')->info($NAME);
        
        $modDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        
        $sqlTouren =
         "SELECT DV.*, PT.*, PT.topcustom PortletTopCustom, PT.title PortletTitle, TL.*, TL.title TimelineTitle, A.* " . PHP_EOL
            ."FROM " . $tblPT . " PT " . PHP_EOL
            ."LEFT JOIN " . $tblTL . " TL ON PT.portlet_id  = TL.portlet_id " . PHP_EOL
            ."LEFT JOIN " . $tblDV . " DV ON TL.timeline_id = DV.timeline_id " . PHP_EOL
            ."LEFT JOIN " . $tblAK . " A  ON (DV.Mandant = A.Mandant AND DV.Auftragsnummer = A.Auftragsnummer) " . PHP_EOL
                
        ." WHERE " . PHP_EOL
        ." PT.lager_id = :lager_id " . PHP_EOL
        ." AND (" . PHP_EOL
        ." (PT.title IS NOT NULL AND PT.datum BETWEEN :DatumVon AND :DatumBis) "  . PHP_EOL
        ." OR (DV.IsDefault = 0  "  . PHP_EOL
        ." AND DV.DatumVon BETWEEN :DatumVon AND :DatumBis) "  . PHP_EOL
        ." )" . PHP_EOL
        ."ORDER BY DV.timeline_id, ZeitVon";
        
        if (0) die('<pre>' . strtr($sqlTouren, array(
            ':lager_id' => (int)$lager_id,
            ':DatumVon' => $dateRange['Von'],
            ':DatumBis' => $dateRange['Bis']
        )) . '</pre>' . PHP_EOL);
        $this->view->dateRange = $dateRange;
        $this->view->lager_id = $lager_id;
        
        
        $this->view->data = $db->fetchAll($sqlTouren, array(
            ':lager_id' => (int)$lager_id,
            ':DatumVon' => $dateRange['Von'],
            ':DatumBis' => $dateRange['Bis']
        ));
        
        $this->view->tourResources = array();
        foreach($this->view->data as $_tour) {
            $this->view->tourResources[$_tour['tour_id']] = $modDV->getResources($_tour['tour_id'], false);
        }
    }
    
    public function calendarmonthdataAction()
    {
        $this->_helper->viewRenderer->setRender('calendarmonthdata');
        
        $this->view->assign(array(
            'dateRange' => array('DatumVon'=>'x', 'DatumBis' => 'y'),
            'lager_id' => '9',
            'data' => array(1,2,3,4,5,6,7,8,9)
        ));
        
        $rq = $this->rq;
        $date = $rq->getParam('date', '');
        $monat   = $rq->getParam('monat', '');
        $lager_id = $rq->getParam('lager_id', '');
        
        $dateRange = array();
        
        if (preg_match('/^(?P<YEAR>\d{4})-0?(?P<MONTH>\d{1,2})$/', $monat, $m)) {
            if (checkdate($m['MONTH'], 1, $m['YEAR'])) {
                
                $year = $m['YEAR'];
                $month = $m['MONTH'];
                
                $mTime = mktime(12,0,0,$month,1, $year);
                $mDays = date("t", $mTime);
                
                $dateRange = array(
                    'Von' => date('Y-m-d', $mTime),
                    'Bis' => date('Y-m-d', mktime(12,0,0, $month, $mDays, $year) )
                );
            }
        }
        
        if (!array_key_exists('Von', $dateRange))
            throw new Exception('Ungueltige Monatsange: ' . $monat);
        
        $db = $this->db;
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblTL = MyProject_Model_Database::loadStorage('tourenTimelines')->info($NAME);
        $tblPT = MyProject_Model_Database::loadStorage('tourenPortlets')->info($NAME);
        
        $sqlTouren =
         "SELECT T.*, PT.*, A.* " . PHP_EOL
            ."FROM " . $tblDV . " T " . PHP_EOL
        ."LEFT JOIN " . $tblAK . " A USING(Mandant,Auftragsnummer) " . PHP_EOL
        ."LEFT JOIN " . $tblTL . " TL ON T.timeline_id = TL.timeline_id " . PHP_EOL
        ."LEFT JOIN " . $tblPT . " PT ON TL.portlet_id = PT.portlet_id " . PHP_EOL
        ." WHERE T.IsDefault = 0 AND PT.lager_id = :lager_id " . PHP_EOL
        ." AND T.DatumVon BETWEEN :DatumVon AND :DatumBis" . PHP_EOL
        ."ORDER BY T.timeline_id, ZeitVon";
        
        
        $sqlTouren =
         "SELECT DV.*, PT.*, PT.title PortletTitle, TL.*, TL.title TimelineTitle, A.* " . PHP_EOL
            ."FROM " . $tblPT . " PT " . PHP_EOL
            ."LEFT JOIN " . $tblTL . " TL ON PT.portlet_id  = TL.portlet_id " . PHP_EOL
            ."LEFT JOIN " . $tblDV . " DV ON TL.timeline_id = DV.timeline_id " . PHP_EOL
            ."LEFT JOIN " . $tblAK . " A  ON (DV.Mandant = A.Mandant AND DV.Auftragsnummer = A.Auftragsnummer) " . PHP_EOL
        ." WHERE " . PHP_EOL
        ." PT.lager_id = :lager_id " . PHP_EOL
        ." AND (" . PHP_EOL
        ." (PT.title IS NOT NULL AND PT.datum BETWEEN :DatumVon AND :DatumBis) "  . PHP_EOL
        ." OR (DV.IsDefault = 0  "  . PHP_EOL
        ." AND DV.DatumVon BETWEEN :DatumVon AND :DatumBis) "  . PHP_EOL
        ." )" . PHP_EOL
        ."ORDER BY DV.timeline_id, ZeitVon";
        
        $this->view->dateRange = $dateRange;
        $this->view->lager_id = $lager_id;        
        
        $this->view->data = $db->fetchAll($sqlTouren, array(
            ':lager_id' => (int)$lager_id,
            ':DatumVon' => $dateRange['Von'],
            ':DatumBis' => $dateRange['Bis']
        ));
        
//        die( print_r($this->view->data, 1));
    }
    
    public function tageseinsatzlisteAction()
    {
        $rq = $this->getRequest();
        $date = $rq->getParam('date', date('Y-m-d'));
        $lager_id = $rq->getParam('lager_id', '1');
        
        $this->_helper->viewRenderer->setRender('tageseinsatzliste');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->date = $date;
        $this->view->ajax_response->lager_id = $lager_id;
        
        $filterFreeRsrc = array(
            'DatumVon' => date('Y-m-d', strtotime($date)),
            'DatumBis' => date('Y-m-d', strtotime($date)),
        );
        
        $modelDV = new Model_TourenDispoVorgaenge();
        $result = $modelDV->getFullDayData($date, $lager_id);
        
        $modelRsrc = array(
            'FP' => MyProject_Model_Database::loadModel('tourenDispoFuhrpark'),
            'MA' => MyProject_Model_Database::loadModel('tourenDispoMitarbeiter')
        );
        
        $this->view->ajax_response->freeResources = array('FP'=>null,'MA'=>null);
        
        
        foreach($modelRsrc as $k => $_model) {
            $this->view->ajax_response->freeResources[$k] = $_model->getFreeResources($filterFreeRsrc, array());
        }
        // die('<pre>'.print_r($this->view->ajax_response->freeResources,1).'</pre>');
        
        if ($result->error === null) {
            $this->view->ajax_response->data = $result->data;
        } else {
            $this->view->ajax_response->data = null;
            $this->view->ajax_response->error = $result->error->message;
            $this->view->ajax_response->errorObject = $result->error;
        }
    }

    public function tourOperationIsAllowedByIdAction()
    {
        $userIdentity = MyProject_Auth_Adapter::getIdentity();
        $tourId = (int)$this->getParam('tourid', 0);

        $modelDV = new Model_TourenDispoVorgaenge();

        $bIsAllowed = $modelDV->tourOperationIsAllowedById($tourId, $userIdentity);

        if ($bIsAllowed) {
            return $this->sendJsonSuccess('True', []);
        }

        return $this->sendJsonError($modelDV->getLastErrorTourOperationNotAllowed(), []);
    }

    protected function closedUntilKW(object $userIdentity)
    {

        if ($userIdentity->user_role !== 'innendienst') {
            return null;
        }

        $today = new \DateTime();
        $weekDay = $today->format('N');
        // Ab Donnerstag wird Dispo
        $lockDaysFromMonday = ($weekDay < 4) ? 13 : 20;

        if ($weekDay > 1) {
            $sGotoMondaySelector = '-' . ($weekDay-1) . ' day';
            $iGotoMondayTime = strtotime( $sGotoMondaySelector );
            $sGotoMondayDate = date(\DateTime::ATOM, $iGotoMondayTime);
            /* print_r(['<pre>', __LINE__, __FILE__, __METHOD__,
                '$sGotoMondaySelector'=>$sGotoMondaySelector,
                '$iGotoMondayTime' => $iGotoMondayTime,
                '$sGotoMondayDate' => $sGotoMondayDate,
                '</pre>',
                ]);
            exit;*/
            $weekMonday = new \DateTime( $sGotoMondayDate );
        } else {
            $weekMonday = new \DateTime( $today->format(\DateTime::ATOM) );
        }

        $closedUntil = new \DateTime( $weekMonday->format( \DateTime::ATOM ) );
        $closedUntil->add(
            new \DateInterval('P' . $lockDaysFromMonday . 'D')
        );

        return $closedUntil;
    }

    protected function closedUntilWorkingDays(string $sDate, string $userRole) {
        $this->_lastDisposableError = '';
        $dateTime = strtotime($sDate);

        /** @var Zend_Application_Bootstrap_Bootstrap $bootstrap */

        $bootstrap = $this->getInvokeArg('bootstrap');
        $dispoSettings = $bootstrap->getOption('dispo');
        $iCheckVorlaufTage = (int)($dispoSettings['roles'][$userRole]['vorlauftage'] ?? 0);
        // $iCheckVorlaufTage = 10;

        if ( $iCheckVorlaufTage ) {

            $dispoDate = new DateTime(date('Y-m-d', $dateTime));
            $nowDate = new Datetime(date('Y-m-d'));
            $oDateDiff = new MyProject_Date_Diff($nowDate, $dispoDate);

            $dayIsDisposable = ($oDateDiff->getDiffTotalDays() > $iCheckVorlaufTage && $oDateDiff->getDiffArbeitstage() > $iCheckVorlaufTage);


            if (0) {
                print_r([
                    '<pre>',
                    'line'=>__LINE__,
                    'method' => __METHOD__,
                    'userRole' => $userRole,
                    'vorlauftage' => $iCheckVorlaufTage,
                    'dispoDate' => $dispoDate->format('Y-m-d'),
                    'nowDate' => $nowDate->format('Y-m-d'),
                    'diffDays' => $oDateDiff->getDiffTotalDays(),
                    'diffArbeitstage' => $oDateDiff->getDiffArbeitstage(),
                    'dayIsDisposable' => $dayIsDisposable,
                ]);
                exit;
            }

            if (!$dayIsDisposable) {
                $this->_lastDisposableError = 'Vorgegebene Vorlaufzeit von '
                    . $iCheckVorlaufTage . ' Tagen für ' . $userRole . ' wurde unterschritten!';
                return false;
            }

            return $dayIsDisposable;
        }
        return true;
    }

    /**
     * @param string $sDate
     * @return bool
     * @return bool
     */
    protected function dayIsDisposable(string $sDate):bool
    {
        $this->_lastDisposableError = '';
        $userIdentity = MyProject_Auth_Adapter::getIdentity();

        $closedUntil = $this->closedUntilKW( $userIdentity );
        $checkDay = new \DateTime( date(\DateTime::ATOM, strtotime($sDate)) );

        if (!is_null( $closedUntil) &&
            $checkDay->format('Y-m-d') <= $closedUntil->format('Y-m-d')) {
            $this->_lastDisposableError = 'Vorgegebene Vorlaufzeit bis zum '
                . $closedUntil->format('d.m.Y') . '  für ' . $userIdentity->user_role . ' wurde unterschritten!';
            return false;
        } else {
            return true;
        }

        // Alte Prüfprozedur anhand Vorlaufzahl an reinen Arbeitstagen
        $userRole = MyProject_Auth_Adapter::getUserRole();
        $this->_lastDisposableError = '';
        $dateTime = strtotime($sDate);

        /** @var Zend_Application_Bootstrap_Bootstrap $bootstrap */

        $bootstrap = $this->getInvokeArg('bootstrap');
        $dispoSettings = $bootstrap->getOption('dispo');
        $iCheckVorlaufTage = (int)($dispoSettings['roles'][$userRole]['vorlauftage'] ?? 0);
        // $iCheckVorlaufTage = 10;

        if ( $iCheckVorlaufTage ) {

            $dispoDate = new DateTime(date('Y-m-d', $dateTime));
            $nowDate = new Datetime(date('Y-m-d'));
            $oDateDiff = new MyProject_Date_Diff($nowDate, $dispoDate);

            $dayIsDisposable = ($oDateDiff->getDiffTotalDays() > $iCheckVorlaufTage && $oDateDiff->getDiffArbeitstage() > $iCheckVorlaufTage);


            if (0) {
                print_r([
                   '<pre>',
                   'line'=>__LINE__,
                   'method' => __METHOD__,
                   'userRole' => $userRole,
                   'vorlauftage' => $iCheckVorlaufTage,
                   'dispoDate' => $dispoDate->format('Y-m-d'),
                   'nowDate' => $nowDate->format('Y-m-d'),
                   'diffDays' => $oDateDiff->getDiffTotalDays(),
                   'diffArbeitstage' => $oDateDiff->getDiffArbeitstage(),
                   'dayIsDisposable' => $dayIsDisposable,
                ]);
                exit;
            }

            if (!$dayIsDisposable) {
                $this->_lastDisposableError = 'Vorgegebene Vorlaufzeit von '
                    . $iCheckVorlaufTage . ' Tagen für ' . $userRole . ' wurde unterschritten!';
                return false;
            }

            return $dayIsDisposable;
        }
        return true;
    }
    
    public function calendardaydataAction() 
    {
        $rq = $this->getRequest();
        $date = $rq->getParam('date', date('Y-m-d'));
        $lager_id = $rq->getParam('lager_id', '1');

        $dateTime = strtotime($date);

        $userIdentity = MyProject_Auth_Adapter::getIdentity();
        $userRole = $userIdentity->user_role;
        
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        
        $modelDV = new Model_TourenDispoVorgaenge();
        
        // Cache frontendoptions
        $frontendOptions = array(
           'lifetime' => 100, // 100 Sekunden 24*60*60*3 // 3 Tage cachen
           'automatic_serialization' => true,
        );
        // Cache backendoptions
        $backendOptions = array(
            // Directory where to put the cache files
            'cache_dir' => APPLICATION_PATH . '/cache' 
        );
        // Cache object
        $cache = Zend_Cache::factory('Output',
                                     'APC',
                                     $frontendOptions,
                                     $backendOptions);
        $cacheID = md5(__METHOD__ .'/' . $date . '/' . $lager_id);
        
//        if (!($result = $cache->load($cacheID))) {
//            $result = $modelDV->getFullDayData($date, $lager_id);
//            $cache->save($result);
//        }
        
        $timeIn = time();
        $result = $modelDV->getFullDayData($date, $lager_id);

        $denyAll = !$this->dayIsDisposable($date);

        if ($denyAll) {
            $portletIsEditable = 0;
            $routeIsEditable = 0;
            $resrcIsDraggable = false;
            $resrcIsRemovable = false;
            $timelineIsEditable = false;
            $timelineIsDroppable = false;
        } else {
            $portletIsEditable = 1;
            $routeIsEditable = 1;
            $resrcIsDraggable = true;
            $resrcIsRemovable = true;
            $timelineIsEditable = true;
            $timelineIsDroppable = true;
        }

        if ( 1 || !$this->dayIsDisposable($date) ) {

            $result->settings = [];
            $result->settings['portlet']['isEditable'] = $portletIsEditable;
            $result->settings['route']['isEditable'] = $routeIsEditable;
            $result->settings['resource']['isDraggable'] = $resrcIsDraggable;
            $result->settings['resource']['isRemovable'] = $resrcIsRemovable;
            $result->settings['timelineDropzone']['isEditable'] = $timelineIsEditable;
            $result->settings['timelineDropzone']['isDroppable'] = $timelineIsDroppable;

            $iNumResultData = count($result->data);

            for ($i = 0; $i < $iNumResultData; $i++) {
                $result->data[$i]['settings']['isEditable'] = !$denyAll;
                $result->data[$i]['settings']['isDroppable'] = !$denyAll;
                $_iNumTimelines = count($result->data[$i]['timelines']);

                for ($i2 = 0; $i2 < $_iNumTimelines; $i2++) {
                    $result->data[$i]['timelines'][$i2]['settings']['isEditable'] = !$denyAll;
                    $result->data[$i]['timelines'][$i2]['settings']['isDroppable'] = !$denyAll;
                    $_iNumTouren = count($result->data[$i]['timelines'][$i2]['touren']);

                    for ($i3 = 0; $i3 < $_iNumTouren; $i3++) {

                        $_aTourData = $result->data[$i]['timelines'][$i2]['touren'][$i3];
                        $_tourIsEditable  = !$denyAll && $modelDV->tourOperationIsAllowedByData($_aTourData, $userIdentity, 'edit');
                        $_tourIsResizable = !$denyAll && $modelDV->tourOperationIsAllowedByData($_aTourData, $userIdentity, 'resize');
                        $_tourIsRemovable = !$denyAll && $modelDV->tourOperationIsAllowedByData($_aTourData, $userIdentity, 'remove');
                        $_tourIsDroppable = !$denyAll && $modelDV->tourOperationIsAllowedByData($_aTourData, $userIdentity, 'drop');
                        $_tourIsDraggable = !$denyAll && $modelDV->tourOperationIsAllowedByData($_aTourData, $userIdentity, 'drag');

                        $result->data[$i]['timelines'][$i2]['touren'][$i3]['settings']['isEditable']  = $_tourIsEditable;
                        $result->data[$i]['timelines'][$i2]['touren'][$i3]['settings']['isResizable'] = $_tourIsResizable;
                        $result->data[$i]['timelines'][$i2]['touren'][$i3]['settings']['isRemovable'] = $_tourIsRemovable;
                        $result->data[$i]['timelines'][$i2]['touren'][$i3]['settings']['isDroppable'] = $_tourIsDroppable;
                        $result->data[$i]['timelines'][$i2]['touren'][$i3]['settings']['isDraggable'] = $_tourIsDraggable;
                    }
                }
            }
        } elseif ($userRole === 'innendienst' ) {



        }

//        echo '<pre>' . json_encode($result->data, JSON_PRETTY_PRINT) . '</pre>';
//        exit;

        // die(print_r($result->data,1));
        $time = time() - $timeIn;
        
        if ($result->error === null) {
            $this->view->ajax_response->time = $time;
            $this->view->ajax_response->settings = $result->settings;
            $this->view->ajax_response->data = $result->data;
        } else {
            $this->view->ajax_response->data = null;
            $this->view->ajax_response->error = $result->error->message;
            $this->view->ajax_response->errorObject = $result->error;
        }
        return;
        
    }
    
    public function resourcedataAction() 
    {
        $tour_id = $this->getRequest()->getParam("id", 0);
//        die('#'.__LINE__ . ' tour_id: ' . $tour_id);
        
        $this->view->ajax_response->data = array();
        
        /* @var $tourModel Model_TourenDispoVorgaenge */
        $tourModel = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $resources = $tourModel->getResources($tour_id);
        
        if (is_array($resources)) foreach($resources as $_resourceType => $_resources) {
            foreach ($_resources as $_rsrc) {
                $_rsrc['resourceType'] = $_resourceType;
                switch ($_resourceType) {
                    case 'FP':
                        $_rsrc['name'] = $_rsrc['kennzeichen'] . ' ' . $_rsrc['fahrzeugart'];
                        break;
                    case 'WZ':
                        $_rsrc['name'] = $_rsrc['bezeichnung'];
                        break;
                    case 'MA':
                        $_rsrc['name'] = substr($_rsrc['vorname'], 0, 1) . '. ' . $_rsrc['name'] . ' [' . $_rsrc['eingestellt_als'] . ']';
                        break;
                }
                $this->view->ajax_response->data[] = $_rsrc;
            }
        }
        $this->view->ajax_response->msg = "Ressourcen wurden geladen";
    }

    public function indexAction()
    {
        // action body
        die('#' . __LINE__ . ' ' . __METHOD__);
    }
    
    public function addportletAction()
    {
        try {
//        die(__METHOD__);
            $rq = $this->getRequest();
            $data = (array)$rq->getParam('data', []);

            $this->_require(!empty($data['datum']), 'Fehlende Datumsangabe!');

            if (!$this->dayIsDisposable($data['datum'])) {
                return $this->sendJsonVorlaufError();
            }
            
            /* @var $model Model_TourenPortlets */
            $model = MyProject_Model_Database::loadModel('tourenPortlets');

            $newID = $model->add($data);
            $this->view->ajax_response->msg = "Methode wurde aufgerufen (Neue ID:$newID): " . __METHOD__ . PHP_EOL . print_r($data,1);
            $this->view->ajax_response->id = $newID;
            $this->view->ajax_response->data = $model->fetchEntry($newID);
            $_hday = MyProject_Date_Holidays::getHolidayByDate($this->view->ajax_response->data['datum']);
            if ( !$_hday ) {
                $this->view->ajax_response->data['holiday'] = '';
                $this->view->ajax_response->data['holiday_frei'] = 0;
                $this->view->ajax_response->data['holiday_halb'] = 0;
                $this->view->ajax_response->data['holiday_only'] = '';
            } else {
                $this->view->ajax_response->data['holiday'] = $_hday['name'];
                $this->view->ajax_response->data['holiday_frei'] = $_hday['frei'];
                $this->view->ajax_response->data['holiday_halb'] = $_hday['halb'];
                $this->view->ajax_response->data['holiday_only'] = $_hday['only'];
            }
        } catch(Exception $e) {
            die($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }    
	
	public function updateportlettitleAction() 
	{
        try {
            $userIdentity = MyProject_Auth_Adapter::getIdentity();
            $rq    = $this->getRequest();
            $id    = $rq->getParam('id', null);
            $title = $rq->getParam('title', null);
            if (!$id)             throw new Exception('Fehlender Paramter id!');
            if ($title === null)  throw new Exception('Fehlender Paramter title!');

            /* @var $modelPt Model_TourenPortlets */
            $modelPt = new Model_TourenPortlets();
            $sDatum = $modelPt->getDatum($id);

            if (!$this->dayIsDisposable($sDatum)) {
                return $this->sendJsonVorlaufError();
            }

            if (!$modelPt->operationIsAllowedById($id, $userIdentity, 'updateportlettitle' )) {
                return $this->sendJsonError('Titeländerung wurde nicht zugelassen!');
            }

            $modelPt->update(array('title' => $title), $id );
            
            /* @var $modelLogger Model_TourenDispoLog */            
            $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
            $modelLogger->logTourenplan($id, 'updatetitle');
        } catch(Exception $e) {
            $this->view->ajax_response->type = false;
            $this->view->ajax_response->error.= $e->getMessage() . PHP_EOL;
            $this->view->ajax_response->syserror = $e->getTraceAsString();
        }
    }    
    
    public function sortportletAction()
    {
//        die(__METHOD__);
        try {
            $rq = $this->getRequest();
            $id = $rq->getParam('id', null);
            $toPos = $rq->getParam('pos', null);
            if (!$id)            throw new Exception('Fehlender Paramter id!');
            if (!$toPos)         throw new Exception('Fehlender Paramter pos!');
            
            /* @var $model Model_TourenDispoVorgaenge */
            $model = MyProject_Model_Database::loadModel('tourenPortlets');
            $model->movePosition($id, $toPos);
    //        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;   
        } catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
            die();
        }
    }
    
    
    public function removeportletAction()
    {
//        die(__METHOD__);
        try {
            $rq = $this->getRequest();
            $id = $rq->getParam('id', null);
            if (!$id)            throw new Exception('Fehlender Paramter id!');

            /* @var $model Model_TourenDispoVorgaenge */
            $model = MyProject_Model_Database::loadModel('tourenPortlets');
    //        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;

            $data = $model->fetchEntry( $id );

            if (!empty($data['datum']) && !$this->dayIsDisposable( $data['datum'] )) {
                return $this->sendJsonVorlaufError();
            }

            $model->delete($id);
            
            /* @var $modelLogger Model_TourenDispoLog */            
            $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
            $modelLogger->logTourenplan($id, 'remove');
        } catch(Exception $e) {
            $this->view->ajax_response->type = false;
            $this->view->ajax_response->error.= $e->getMessage() . PHP_EOL;
            $this->view->ajax_response->syserror = $e->getTraceAsString();
        }
    }
    
    public function addtimelineAction()
    {
        try {
//          die(__METHOD__);
            $rq = $this->getRequest();
            $data = (array)$rq->getParam('data', []);

            $this->_require(!empty($data['portlet_id']), 'Fehlende Portlet-ID der Tourenschiene');

            $modelPortlets = new Model_TourenPortlets();
            $datum = $modelPortlets->getDatum( (int)$data['portlet_id']);

            if (!empty($datum) && !$this->dayIsDisposable( $datum )) {
                return $this->sendJsonVorlaufError();
            }

//          $model = new Model_TourenPortlets();
            /** @var Model_TourenTimelines $model */
            $model = MyProject_Model_Database::loadModel('tourenTimelines');

            $newID = $model->add($data);
            if ($newID) {
                $this->sendJsonSuccessID($newID, "Zeitleiste wurde angelegt");
            } else {
                $this->sendJsonError('Zeitleiste konnte nicht angelegt werden');
            }
        } catch(Exception $e) {
            return $this->sendJsonError( $e->getMessage() );
        }
    }
    
    public function removetimelineAction() 
    {
//        die(__METHOD__);
        try {
            $rq = $this->getRequest();
            $id = $rq->getParam('id', null);
            $confirm = $rq->getParam('confirm', 0);
            if (!$id)            throw new Exception('Fehlender Paramter id!');

            /* @var $model Model_TourenTimelines */
            $model = MyProject_Model_Database::loadModel('tourenTimelines');
    //        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;

            $datum = $model->getDatum(  $id );

            if (!empty($datum) && !$this->dayIsDisposable( $datum )) {
                return $this->sendJsonVorlaufError();
            }

            if ( $model->delete($id, $confirm) ) {
                /* @var $modelLogger Model_TourenDispoLog */
                $modelLogger = new Model_TourenDispoLog();
                $modelLogger->logTimeline($id, 'remove');                
            } else {
                $this->view->ajax_response->type    = "error";
                $this->view->ajax_response->success = false;
                if (!$confirm) {
                    $this->view->ajax_response->confirm = 
                        $model->getError()
                        . "Moechten Sie die Timeline dennoch inkl. Touren loeschen?";

                    $this->view->ajax_response->confirmData = array("confirm"=>1);
                } else {
                    $this->view->ajax_response->error = $model->getError();
                }
            }
        } catch(Exception $e) {
            $this->view->ajax_response->type = false;
            if ( $model->getError() ) $this->view->ajax_response->msg.= $model->getError() . PHP_EOL;
            $this->view->ajax_response->msg.= $e->getMessage();
            $this->view->ajax_response->msg.= $e->getTraceAsString();
        }
    }    
    
    public function sorttimelineAction()
    {
//        die(__METHOD__);
        try {
            $rq = $this->getRequest();
            $id = $rq->getParam('id', null);
            $toPos = $rq->getParam('pos', null);
            if (!$id)            throw new Exception('Fehlender Paramter id!');
            if (!$toPos)         throw new Exception('Fehlender Paramter pos!');
            
            
            
            /* @var $model Model_TourenDispoVorgaenge */
            $model = MyProject_Model_Database::loadModel('tourenTimelines');
            $model->movePosition($id, $toPos);
    //        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;   
        } catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
            die();
        }
    }
    
    public function movetimelineAction()
    {
//        die(__METHOD__);
        try {
            $rq = $this->getRequest();
            $id = (int) $rq->getParam('id', null);
            $toPos = (int)$rq->getParam('pos', null);
            $toPortletId = (int)$rq->getParam('portlet_id', null);
            if (!$id)            throw new Exception('Fehlender Paramter id!');
            if (!$toPos)         throw new Exception('Fehlender Paramter pos!');
            
            /* @var $model Model_TourenDispoVorgaenge */
            $model = MyProject_Model_Database::loadModel('tourenTimelines');
            $result = $model->moveTimeline($id, $toPos, $toPortletId);
            if ($result->success) {
                $this->_helper->json(array(
                    'type' => 'success',
                    'success' => true,
                ));
            } else {
                $this->_helper->json(array(
                    'type' => 'error',
                    'success' => false,
                    'error' => $result->message,
                ));
            }
    //        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;   
        } catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
            $this->_helper->json(array(
                'type' => 'error',
                'success' => false,
                'error' => $e->getMessage(),
            ));
        }
    }
    
    public function addportletandrouteAction() 
    {
        try {  
    //      die(__METHOD__);
            $rq = $this->getRequest();
            $addTourData = (array)$rq->getParam('data', []);

            $ref_tour_id = $rq->getParam('ref_tour_id');
            $lager_id = $rq->getParam('lager_id');

            $model = new Model_TourenDispoVorgaenge();

            $this->_require($addTourData['DatumVon'] ?? '', 'Fehlende Datumsangabe!');

            if (!$this->dayIsDisposable($addTourData['DatumVon'])) {
                return $this->sendJsonVorlaufError();
            }

            if (!$lager_id && $ref_tour_id) {
                $portlet = $model->getPortlet($ref_tour_id);
                if ($portlet) $lager_id = $portlet['lager_id'];
            }

            if (!$lager_id) {
                $this->sendJsonError('Tourenschiene kann ohne Lagerangabe nicht angelegt werden!');
            }
            
            $newIDs = $model->addPortletAndRoute($addTourData, $lager_id);

            if (0 && $newIDs instanceof TourenDispoIds && $newIDs->tour_id) {
                $this->sendJsonSuccessID($newIDs->tour_id, 'Tour wurde hinzugefügt', [ 'TourenDispoIds' => $newIDs]);
            } else {
                $this->sendJsonError('Tour konnte nicht angelegt werden!');
            }
        } catch(Exception $e) {
            //Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            //Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }
    }
    
    public function removetimelinedefaultAction() 
    {
//        die(__METHOD__);
        $rq = $this->getRequest();
        $id = $rq->getParam('id', null);
        $removePortletIfEmpty = $rq->getParam('removePortletIfEmpty', null);
        if (!$id)            throw new Exception('Fehlender Paramter id!');
        $timeline = null;
        
        /* @var $model Model_TourenTimelinese */
        $modelTL = MyProject_Model_Database::loadModel('tourenTimelines');
        
        /* @var $model Model_TourenPortlets */
        $modelP = MyProject_Model_Database::loadModel('tourenPortlets');
//        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;
        
        if ($removePortletIfEmpty) {
            $timeline = $modelTL->fetchEntry($id);
        }
        
        /* @var $modelLogger Model_TourenDispoLog */
        $modelLogger = new Model_TourenDispoLog();
        $modelLogger->logTour($id, 'removeTimeline'); 
        
        if ($removePortletIfEmpty && $timeline) {
            if ($modelTL->countVorgaenge($id, false) == 0) {
                $modelTL->delete( $id );
            }

            if ($modelP->countTimelines($timeline['portlet_id']) == 0) {
                $modelP->delete($timeline['portlet_id']);
            }

        } else {
            $this->view->ajax_response->error = "Die Zeitleiste mit der id " . $id . " konnte nicht geloescht werde!";
        }    
    }
    
    public function addportletanddefaultAction() 
    {
        try {  
    //      die(__METHOD__);
            $db = Zend_Db_Table::getDefaultAdapter();
            $rq          = $this->getRequest();
            $data        = $rq->getParam('data');
            $ref_timeline_id = $rq->getParam('ref_timeline_id');
            $ref_tour_id = $rq->getParam('ref_tour_id');
            $lager_id    = $rq->getParam('lager_id');
                        
            $newIDs      = array();
            $newDays     = array();
            
            /** @var $model Model_TourenDispoVorgange */
            $model = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
            $modelTL = MyProject_Model_Database::loadModel('tourenTimelines');
            $tplData = null;
            
            if ($ref_timeline_id && !$ref_tour_id) {
                $select = $model->getStorage()->select()->where('IsDefault = 1 AND timeline_id = 203' );
                $tplData = $db->fetchRow($select);
                $ref_tour_id = $tplData['tour_id'];
            } elseif ($ref_tour_id) {
                $tplData = $model->fetchEntry($ref_tour_id);
                $ref_timeline_id = $tplData['timeline_id'];
            }
            
            if (!trim($tlData['title'])) {
                throw new Exception('Fehlende Angabe: Arbeitstitel ist für gruppierte Buchung erforderlich!');
            }
            
            $tlData = $modelTL->fetchEntry($ref_timeline_id);
            
            $opts = array(
              'timeline_data' => array('title' => $tlData['title'])
            );
            
            if (!$tplData || !$tplData['IsDefault']) {
                throw new Exception('Invalid Arguments. Zu dieser Zeitleiste existieren keine Default-Resourcen!');
            }
            
            $keysOnly = true;
            $resources = $model->getResources($ref_tour_id, $keysOnly);
            
            if (!$resources || !count($resources)) {
                throw new Exception('Invalid Arguments. Zu dieser Zeitleiste wurden noch keine Default-Resourcen angelegt!');
            }

            if (!$lager_id && $ref_tour_id) {
                $portlet = $model->getPortlet($ref_tour_id);
                if ($portlet) $lager_id = $portlet['lager_id'];
            }

            if (!$lager_id) {
                $this->getResponse()->setHttpResponseCode(400);
                throw new Exception('Tourenschiene kann ohne Lagerangabe nicht angelegt werden!');
            }
            
            $offset = strtotime($data['DatumVon']);
            
            $tourData = $data;
            $tourData['ZeitVon'] = $tplData['ZeitVon'];
            $tourData['ZeitBis'] = $tplData['ZeitBis'];
            $tourData = array_merge($tplData, $tourData);
            
            
            $offset = strtotime('+1 day', $offset);
            /** @var TourenDispoIds */
            $_newIDs = $model->addPortletAndDefaultRoute($tourData, $lager_id, $resources, false, $opts);
            if ($_newIDs->tour_id) {
                $newIDs[]  = $_newIDs->tour_id;
                $newDays[] = $tourData['DatumVon'];
            }

            $this->view->ajax_response->msg = "Methode " . __METHOD__ . " wurde aufgerufen (Neue Tage:".implode(',', $newDays). " " . PHP_EOL . print_r($tourData,1);
            $this->view->ajax_response->ids = $newIDs;
            $this->view->ajax_response->days = $newDays;
        } catch(Exception $e) {
            //Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            //Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }        
    }
    
    public function addportletanddefaultserieAction()
    {
        $transactionStarted = false;
        try {  
            $db = Zend_Db_Table::getDefaultAdapter();
            $rq          = $this->getRequest();
            $data        = $rq->getParam('data');
            $wochentage  = $rq->getParam('Wochentage', null);
            $ref_timeline_id = $rq->getParam('ref_timeline_id');
            $ref_tour_id = $rq->getParam('ref_tour_id');
            $lager_id    = $rq->getParam('lager_id');
            $mitVorgaenge = $rq->getParam('MitTouren', 0);
            // die( print_r($rq->getParams(), 1));
            
            if (!is_array($wochentage) || !count($wochentage)) {
                throw new Exception('Fehlende Auswahl: Wochentage für Serienbuchung!');
            }
            
            $newIDs      = array();
            $newDays     = array();
            $WT = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');
            
            /** @var $model Model_TourenDispoVorgange */
            $model = new Model_TourenDispoVorgaenge(); // MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
            $modelTL = new Model_TourenTimelines(); // MyProject_Model_Database::loadModel('tourenTimelines');
            $modelLogger = new Model_TourenDispoLog(); // MyProject_Model_Database::loadModel('tourenDispoLog');
            
            $tplData = null;
            
            if ($ref_timeline_id && !$ref_tour_id) {
                $select = $model->getStorage()->select()->where('IsDefault = 1 AND timeline_id = ?', $ref_timeline_id );
                $tplData = $db->fetchRow($select);
                $ref_tour_id = $tplData['tour_id'];
            } elseif ($ref_tour_id) {
                $tplData = $model->fetchEntry($ref_tour_id);
                $ref_timeline_id = $tplData['timeline_id'];
            }
            
            $tlData = $modelTL->fetchEntry($ref_timeline_id);
            
            if (!trim($data['DatumBis']) || !strtotime($data['DatumBis'])) {
                throw new Exception('Fehlende oder ungültige Angabe: Bis Datum für zeitliche Begrenzung der Gruppenbuchung erforderlich!');
            }
            
            $group_key = $modelTL->generateGroupKey($ref_timeline_id);
            $opts = array(
              'timeline_data' => array(
                  'title' => $tlData['title'],
                  'group_key' => $group_key,
                )
            );
            
            if (!$tplData || !$tplData['IsDefault']) {
                throw new Exception('Invalid Arguments. Zu dieser Zeitleiste existieren keine Default-Resourcen!');
            }
            
            $keysOnly = true;
            $resources = $model->getResources($ref_tour_id, $keysOnly);
            $tplVorgaenge = ($mitVorgaenge) ? $modelTL->getDispoVorgaenge($ref_timeline_id) : array();
            
            
            for($i = 0; $i < count($tplVorgaenge); ++$i) {
                unset($tplVorgaenge[$i]['tour_id']);
                $tplVorgaenge[$i]['timeline_id'] = 0;
            }
            
            foreach($resources as $k => $v) if (!count($v)) unset($resources[$k]);
            
            if (!$resources || !count($resources)) {
                throw new Exception('Invalid Arguments. Zu dieser Zeitleiste wurden noch keine Default-Resourcen angelegt!');
            } else {
//                die('#'.__LINE__ . ' ref_timeline_id: ' . $ref_timeline_id . '; ref_tour_id: ' . $ref_tour_id . '; resources: '.print_r($resources,1));
            }

            if (!$lager_id && $ref_tour_id) {
                $portlet = $model->getPortlet($ref_tour_id);
                if ($portlet) $lager_id = $portlet['lager_id'];
            }

            if (!$lager_id) {
                $this->getResponse()->setHttpResponseCode(400);
                throw new Exception('Tourenschiene kann ohne Lagerangabe nicht angelegt werden!');
            }
            
            $offset = strtotime($data['DatumVon']);
            $last   = strtotime($data['DatumBis']);
            
            $tourData = $data;
            $tourData['ZeitVon'] = $tplData['ZeitVon'];
            $tourData['ZeitBis'] = $tplData['ZeitBis'];
            $tourData = array_merge($tplData, $tourData);
            
            $db->beginTransaction();
            $transactionStarted = true;
            while($offset <= $last) {
                $_w = date('w', $offset);
                $_wName = $WT[$_w];
                if (!in_array($_wName, $wochentage)) {
                    $offset = strtotime('+1 day', $offset);
                    continue;
                }
                
                
                $tourData['DatumVon'] = date('Y-m-d', $offset);
                $tourData['DatumBis'] = $tourData['DatumVon'];
                $offset = strtotime('+1 day', $offset);
                
                if ($modelTL->groupedTimelineExists($group_key, $tourData['DatumVon'])) {
                    continue;
                }
                
                //echo '#' . __LINE__ . ' ' . print_r($resources, 1) . '<br>' . PHP_EOL;
                /** @var TourenDispoIds */
                $_newIDs = $model->addPortletAndDefaultRoute($tourData, $lager_id, $resources, false, $opts);
                //echo '#' . print_r($_newIDs, 1) . '<br>' . PHP_EOL;
                if ($_newIDs->tour_id) {
                    $newIDs[]  = $_newIDs->tour_id;
                    $newDays[] = $tourData['DatumVon'];
                    
                    if ($mitVorgaenge) {
                        for($i = 0; $i < count($tplVorgaenge); ++$i) {
                            $tplVorgaenge[$i]['timeline_id'] = $_newIDs->timeline_id;
                            $tplVorgaenge[$i]['DatumVon'] = $tourData['DatumVon'];
                            $tplVorgaenge[$i]['DatumBis'] = $tourData['DatumVon'];

                            $newTourId = $model->drop($tplVorgaenge[$i]);
                            /* @var Model_TourenDispoLog */
                            $modelLogger->logTour($_newIDs->tour_id, 'insert');
                            $model->addDefaultResources( $newTourId );
                        }
                    }
                    
                }
            }
            $db->commit();

            $this->view->ajax_response->msg  = "Methode " . __METHOD__ . " wurde aufgerufen (Neue Tage:".implode(',', $newDays). " " . PHP_EOL . print_r($tourData,1);
            $this->view->ajax_response->ids  = $newIDs;
            $this->view->ajax_response->days = $newDays;
        } catch(Exception $e) {
            if ($transactionStarted) $db->rollBack();
            //Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            //Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = $e->getMessage();
        }
        $this->_helper->json($this->view->ajax_response);
    }
    
    public function addportletandrouteserieAction() 
    {
        try {  
    //      die(__METHOD__);
            $rq          = $this->getRequest();
            $data        = (array)$rq->getParam('data', []);
            $wochentage  = $rq->getParam('Wochentage');
            $ref_tour_id = $rq->getParam('ref_tour_id');
            $lager_id    = $rq->getParam('lager_id');
            
            $newIDs      = array();
            $newDays     = array();
            $WT = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');
            
            /** @var $model Model_TourenDispoVorgange */
            $model = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
            $tplData = $model->fetchEntry($ref_tour_id);

            $datumVon = trim($data['DatumVon'] ?? '');
            $datumBis = trim($data['DatumBis'] ?? '');

            if (!$lager_id && $ref_tour_id) {
                $portlet = $model->getPortlet($ref_tour_id);
                if ($portlet) $lager_id = $portlet['lager_id'];
            }

            $this->_require(((int)$lager_id > 0), 'Tourenschiene kann ohne Lagerangabe nicht angelegt werden!');
            $this->_require(!is_null(strtotime($datumVon)), 'Ungültiges Startdatum: ' . $datumVon . '!');
            $this->_require(!is_null(strtotime($datumBis)), 'Ungültiges Enddatum: ' . $datumBis . '!');

            if (!$this->dayIsDisposable($datumVon)) {
                return $this->sendJsonVorlaufError();
            }

            $offset = strtotime($datumVon);
            $last   = strtotime($datumBis);
            
            $tourData = $data;
            $tourData['ZeitVon'] = $tplData['ZeitVon'];
            $tourData['ZeitBis'] = $tplData['ZeitBis'];
            
            while($offset <= $last) {
                $_w = date('w', $offset);
                $_wName = $WT[$_w];
                if (!in_array($_wName, $wochentage)) {
                    $offset = strtotime('+1 day', $offset);
                    continue;
                }
                $tourData['DatumVon'] = date('Y-m-d', $offset);
                $tourData['DatumBis'] = $tourData['DatumVon'];
                $offset = strtotime('+1 day', $offset);
                $_newIDs = $model->addPortletAndRoute($tourData, $lager_id, true);
                // die(var_export($newIDs, 1));

                if ($_newIDs !== false && $_newIDs->tour_id) {
                    $newIDs[]  = $_newIDs->tour_id;
                    $newDays[] = $tourData['DatumVon'];
                }
            }

            if (count($newIDs)) {
                $this->sendJsonSuccess('<div>Es wurden neue ' . count($newIDs) . ' Touren angelegt:</div>'
                    . implode("\n", $newDays), [
                    'ids' => $newIDs,
                    'days' => $newDays,
                ]);
            } else {
                $this->sendJsonError('Es konnten keine Touren für den Vorgabezeitraum angelegt werden!');
            }
        } catch(Exception $e) {
            $this->sendJsonError('Fehler beim Anlegen der Touren in Serie!' . PHP_EOL . $e->getMessage());
        }
    }
    
    
    public function addportletserieAction() 
    {
        try {  
    //      die(__METHOD__);
            $rq          = $this->getRequest();
            $title       = $rq->getParam('title');
            $DatumVon    = $rq->getParam('DatumVon');
            $DatumBis    = $rq->getParam('DatumBis');
            $wochentage  = $rq->getParam('Wochentage');
            $kwFilter    = $rq->getParam('DispoKWs');
            $lager_id    = $rq->getParam('lager_id');
            $topcustom   = $rq->getParam('topcustom');
            
            $newIDs      = array();
            $newDays     = array();
            $WT = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');
            
            /** @var $model Model_TourenDispoVorgange */
            $modelP = new Model_TourenPortlets();

            if (!$lager_id) {
                $this->getResponse()->setHttpResponseCode(400);
                throw new Exception('Tourenschiene kann ohne Lagerangabe nicht angelegt werden!');
            }
            
            $offset = strtotime($DatumVon);
            $last   = strtotime($DatumBis);
            
            $msg = '';
            $data   = array( 'datum' => '', 'lager_id' => $lager_id, 'title' => $title);
            while($offset <= $last) {
                list($_Y, $_W, $_w) = explode(' ', date('Y W w', $offset));
                if (is_array($kwFilter) && count($kwFilter) && !in_array("$_Y-$_W", $kwFilter)) {
                    $offset = strtotime('+1 day', $offset);                    
                    continue;                    
                }
                $_wName = $WT[$_w];
                if (!in_array($_wName, $wochentage)) {
                    $offset = strtotime('+1 day', $offset);
                    continue;
                }
                $data['datum'] = date('Y-m-d', $offset);
                $data['topcustom'] = $topcustom;
                $msg.= "KW $_W $_wName " . $data['datum'].PHP_EOL;
                
                
                $offset = strtotime('+1 day',  $offset);
                
                $_newIDs = $modelP->addPortletAndTimeline($data, array());
                if ($_newIDs) {
                    $newIDs[]  = $_newIDs[0];
                    $newDays[] = $data['datum'];
                }
            }

            $this->view->ajax_response->msg  = "Methode " . __METHOD__ . " wurde aufgerufen (Neue Tage:".implode(',', $newDays). " " . PHP_EOL . print_r($data,1);
            $this->view->ajax_response->ids  = $newIDs;
            $this->view->ajax_response->days = $newDays;
        } catch(Exception $e) {
            //Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            //Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }
    }
    
    public function droprouteAction()
    {
        try {
    //      die(__METHOD__);
            $rq = $this->getRequest();
            $data = $rq->getParam('data');
            if (!is_array($data) || !count($data)) {
                throw new Exception('Fehlende Parameter');
            }
            
            $isDefault  = (isset($data['IsDefault'])      ? $data['IsDefault'] : '');
            $mandant    = (isset($data['Mandant'])        ? $data['Mandant'] : '');
            $auftragsnr = (isset($data['Auftragsnummer']) ? $data['Auftragsnummer'] : '');
            $datumVon   = $data['DatumVon'];
            
            if (!$isDefault && (!$mandant || !$auftragsnr) ) {
                throw new Exception('Fehlende Parameter: Mandant und Auftragsnummer');
            }
            $modelDA = new Model_TourenDispoAuftraege();
            $lockedTxt = $modelDA->isLockedText($mandant, $auftragsnr);

            if ($lockedTxt) {
                throw new Exception( $lockedTxt );
            }

            if (!$this->dayIsDisposable( $datumVon )) {
                return $this->sendJsonVorlaufError();
            }

            // $model = new Model_TourenPortlets();
            $model = new Model_TourenDispoVorgaenge();        
        
            if (empty($data['id'])) {                
                $newID = $model->drop($data);
                
                $model->addDefaultResources( $newID );
                $this->view->ajax_response->msg = "Der Vorgang ($auftragsnr) wurde im Tourenkalender als Tour ($newID) gespeichert!";
                // : " . __METHOD__ . PHP_EOL . print_r($data,1);
                $this->view->ajax_response->id = $newID;
            }
        } catch(Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
//            Zend_Controller_Front::getInstance()->getResponse()->setException($e);
//            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
            $this->view->ajax_response->type = false;
            $this->view->ajax_response->error = $e->getMessage();
        }
    }

    public function removeroutesAction()
    {

        $rq = $this->getRequest();

        $refTourID = $rq->getParam('ref_tour_id', 0);
        $data = $rq->getParam('data', []);
        $mandant = (int)( $data['Mandant'] ?? 0);
        $auftragsnr = (int)( $data['Auftragsnummer'] ?? 0);
        $wochentage  = $rq->getParam('Wochentage');
        $datumVon = ( $data['DatumVon'] ?? '');
        $datumBis = ( $data['DatumBis'] ?? '');
        $removePortletIfEmpty = ( $data['removePortletIfEmpty'] ?? '');

        $datePattern = '#^2\d{3}-[012]\d-[0123]\d$#';
        $validDays = ['Mo','Di','Mi','Do','Fr'];

        $timeDatumVon = strtotime($datumVon);
        $timeDatumBis = strtotime($datumBis);

        $this->_require(preg_match($datePattern, $datumVon) && !is_null($timeDatumVon),
            'Leere oder ungültige Angabe zum Start-Datum: JJJJ-MM-TT!');

        $this->_require(preg_match($datePattern, $datumBis) && !is_null($timeDatumBis),
            'Leere oder ungültige Angabe zum End-Datum: JJJJ-MM-TT!');

        $this->_require($datumVon < $datumBis,
            'End-Datum darf nicht vor Start-Datum liegen!');

        $this->_require(date('Y-m-d') <= $datumVon,
            'Start-Datum darf nicht in der Vergangenheit liegen!');

        $this->_require(!empty($refTourID) || (!empty($mandant) && !empty($auftragsnr)),
            'Zu buchender Vorgang konnte nicht identifiziert werden.' . PHP_EOL
                          .'Erwarte Tour-Ref-ID oder Mandant und ANR!');

        $this->_require(is_array($wochentage) && !count(array_diff($wochentage, $validDays)),
            'Fehlende oder ungültige Angabe der Wochentage. Zulässige Werte: ' . implode(',', $validDays));

        if (!$this->dayIsDisposable($datumVon)) {
            return $this->sendJsonVorlaufError();
        }

        /** @var DateTime $oDatumVon */
        $oDatumVon = null;

        /** @var DateTime $oDatumVon */
        $oDatumBis = null;

        $oDatumVon = DateTime::createFromFormat('U', $timeDatumVon);
        $oDatumBis = DateTime::createFromFormat('U', $timeDatumBis);


        /* @var $model Model_TourenDispoVorgaenge */
        $model = new Model_TourenDispoVorgaenge(); // MyProject_Model_Database::loadModel('tourenDispoVorgaenge');

        /* @var $model Model_TourenTimelinese */
        $modelTL = new Model_TourenTimelines(); // MyProject_Model_Database::loadModel('tourenTimelines');

        /* @var $model Model_TourenPortlets */
        $modelP = new Model_TourenPortlets(); // MyProject_Model_Database::loadModel('tourenPortlets');

        /* @var $modelLogger Model_TourenDispoLog */
        $modelLogger = new Model_TourenDispoLog();

        $aWtShortToNum = array_flip(['So','Mo','Di','Mi','Do','Fr','Sa']);
        $aWNum = [];
        foreach($wochentage as $_wShort ) {
            $aWNum[] = $aWtShortToNum[ $_wShort];
        }

        $lastProcessedData = [];
        try {
            $aTouren = $model->tourIDsByDateRange($mandant, $auftragsnr, $oDatumVon, $oDatumBis, $aWNum);

            $failedTours = [];
            $deletedTours = [];

            foreach( $aTouren as $_tour) {
                $_id = $_tour['tour_id'];
                $lastProcessedData = $_tour;

                $_timeline = ($removePortletIfEmpty) ? $model->getTimeline($_id) : null;

                $modelLogger->logTour($_id, 'remove');
                if ( $model->delete($_tour['tour_id']) ) {
                    $deletedTours[] = $_tour;
                    if ($removePortletIfEmpty && $_timeline) {
                        if ($modelTL->countVorgaenge($_timeline['timeline_id'], false) == 0) {
                            $modelTL->delete( $_timeline['timeline_id']);
                        }

                        if ($modelP->countTimelines($_timeline['portlet_id']) == 0) {
                            $modelP->delete($_timeline['portlet_id']);
                        }
                    }
                } else {
                    $failedTours[] = $_tour;
                }
            }

            $logMsg = 'Es wurden ' . count($deletedTours) . ' Touren gelöscht';
            if (count($deletedTours)) {

                $minDate = array_reduce($deletedTours, function($c, $v){ $d = $v['DatumVon']; return !empty($c) ? min($c, $d) : $d;}, null);
                $maxDate = array_reduce($deletedTours, function($c, $v){ $d = $v['DatumVon']; return !empty($c) ? max($c, $d) : $d;}, null);

                $logMsg.= ' im Zeitraum ' . $minDate . ' bis ' . $maxDate . '.';
            }

            if (count($failedTours)) {
                $error = "FEHLER! \nEs konnten nicht alle Touren gelöscht werden!" . PHP_EOL
                        .$logMsg
                        ."Gelöschte Touren: " . count($deletedTours) . PHP_EOL
                        .'Nicht gelöscht: ' . count($failedTours) . PHP_EOL
                        .'Liste nicht gelöschter Touren: ' . PHP_EOL
                        .implode("\n", array_map(function($v) { return '- ' . $v['DatumVon'] . ' ' . $v['ZeitVon'] . ' - ' . $v['ZeitBis'];}));

                $this->json->error($error, [
                    'deletedTours' => $deletedTours,
                    'failedTours' => $failedTours
                ]);
            }

            $minDate = array_reduce($deletedTours, function($c, $v){ $d = $v['DatumVon']; return !empty($c) ? min($c, $d) : $d;}, null);
            $maxDate = array_reduce($deletedTours, function($c, $v){ $d = $v['DatumVon']; return !empty($c) ? max($c, $d) : $d;}, null);

            $this->json->ok($logMsg, $deletedTours);

            //ref_tour_id: 113195
            //data[Mandant]: 10
            //data[Auftragsnummer]: 1173105
            //data[DatumVon]: 2018-12-17
            //data[DatumBis]: 2018-12-21
            //removePortletIfEmpty: on


            if (1) {
                $this->json->error("Funktion ist noch nicht implementiert. Es wurden keine Touren gelöscht!");
            }
        } catch(Exception $e) {
            $logMsg = 'Es wurden ' . count($deletedTours) . ' Touren gelöscht';
            if (count($deletedTours)) {

                $minDate = array_reduce($deletedTours, function($c, $v){ $d = $v['DatumVon']; return !empty($c) ? min($c, $d) : $d;}, null);
                $maxDate = array_reduce($deletedTours, function($c, $v){ $d = $v['DatumVon']; return !empty($c) ? max($c, $d) : $d;}, null);

                $logMsg.= ' im Zeitraum ' . $minDate . ' bis ' . $maxDate . '.';
            }
            $logMsg.'.';
            $this->json->error( $e->getMessage() . PHP_EOL . $logMsg . PHP_EOL . print_r(['lastProcessedData' => $lastProcessedData], 1) );
        }
    }
    
    public function removerouteAction()
    {
        $userIdentity = MyProject_Auth_Adapter::getIdentity();
        try {
//        die(__METHOD__);
            $rq = $this->getRequest();
            $id = $rq->getParam('id', null);
            $removePortletIfEmpty = $rq->getParam('removePortletIfEmpty', null);
            if (!$id)            throw new Exception('Fehlender Paramter id!');
            $timeline = null;

            /* @var $model Model_TourenDispoVorgaenge */
            $modelDV = new Model_TourenDispoVorgaenge();
            $model = $modelDV; // MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
            // $tourData = $model->fetchEntry( $id );
            $tourData = $model->getTourWithUserRole( $id );

            if (empty($tourData)) {
                return $this->sendJsonError( 'Tour konnte nicht ermittelt werden und wurde evtll. bereits gelöscht!');
            }

            if (!empty($tourData['DatumVon']) && !$this->dayIsDisposable( $tourData['DatumVon'] )) {
                return $this->sendJsonVorlaufError();
            }

            if (!$modelDV->tourOperationIsAllowedByData($tourData, $userIdentity, 'remove')) {
                return $this->sendJsonError( $modelDV->getLastErrorTourOperationNotAllowed() );
            }

            /* @var $model Model_TourenTimelinese */
            $modelTL = MyProject_Model_Database::loadModel('tourenTimelines');

            /* @var $model Model_TourenPortlets */
            $modelP = MyProject_Model_Database::loadModel('tourenPortlets');
    //        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;

            if ($removePortletIfEmpty) {
                $timeline = $model->getTimeline($id);
            }

            /* @var $modelLogger Model_TourenDispoLog */
            $modelLogger = new Model_TourenDispoLog();
            $modelLogger->logTour($id, 'remove');

            if ( $model->delete($id) ) {
                if ($removePortletIfEmpty && $timeline) {
                    if ($modelTL->countVorgaenge($timeline['timeline_id'], false) == 0) {
                        $modelTL->delete( $timeline['timeline_id']);
                    }

                    if ($modelP->countTimelines($timeline['portlet_id']) == 0) {
                        $modelP->delete($timeline['portlet_id']);
                    }
                }
                return $this->sendJsonSuccess('Tour wurde entfernt!');
            } else {
                return $this->sendJsonError( 'Die Tour mit der id ' . $id . ' konnte nicht geloescht werde!' );
            }
        } catch(Exception $e) {
            return $this->sendJsonError( $e->getMessage() );
        }
    }
    
    public function moverouteAction()
    {
        $rq = $this->getRequest();
        $data = $rq->getParam('data');

        if (empty($data) || empty($data['id']) || empty($data['DatumVon']) ) {
            return $this->sendJsonError('Missing data[]-Properties: id, DatumVon!');
        }

        $tourId = $data['id'];
        $tourDatumVon = $data['DatumVon'];

        if (!empty($tourDatumVon) && !$this->dayIsDisposable( $tourDatumVon )) {
            return $this->sendJsonVorlaufError();
        }

        $modelDV = new Model_TourenDispoVorgaenge(); // MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $userIdentity = MyProject_Auth_Adapter::getIdentity();

        if (!$modelDV->tourOperationIsAllowedById($tourId, $userIdentity, 'move')) {
            return $this->sendJsonError($modelDV->getLastErrorTourOperationNotAllowed() );
        }

        
        $tourIsLocked = $modelDV->isLocked($tourId);
        if ($tourIsLocked) {
            $this->_helper->json(array(
                'error' => 'Tour wurde gesperrt! '
            ));
            exit;
        }
        
        try {
            if (!empty($tourId)) {
                $id = $tourId;
                if ($modelDV->move($data)) {
                    $this->view->ajax_response->msg = "Die Tour (ID:$id)  wurde verschoben!";
                    $this->view->ajax_response->data = $data;
                    $this->view->ajax_response->id = $id;
                } else {
                    $this->view->ajax_response->msg = "FEHLER !!! (ID:$id): " . __METHOD__ . PHP_EOL . print_r($data,1);
                    $this->view->ajax_response->msg.= $modelDV->error();
                    $this->view->ajax_response->error = $modelDV->error();
                }
                $this->view->ajax_response->data = $modelDV->fetchEntry($id);
            }
        } catch(Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }

    }
    
    public function resizerouteAction()
    {
        $rq = $this->getRequest();
        $data = $rq->getParam('data');

        if (empty($data) || empty($data['id']) || empty($data['DatumVon']) || empty($data['ZeitVon']) || empty($data['ZeitBis']) ) {
            return $this->sendJsonError('Missing new data[]-Properties: id of tour, DatumVon, ZeitVon, ZeitBis!');
        }

        $tourId = $data['id'];
        $tourDatumVon = $data['DatumVon'];
        $tourZeitVon = $data['ZeitVon'];
        $tourZeitBis = $data['ZeitBis'];

        if (!empty($tourDatumVon) && !$this->dayIsDisposable( $tourDatumVon )) {
            return $this->sendJsonVorlaufError();
        }

        $userIdentity = MyProject_Auth_Adapter::getIdentity();
        $modelDV = new Model_TourenDispoVorgaenge();
        $aTourOld = $modelDV->getTourWithUserRole($tourId);

        if (empty($aTourOld)) {
            return $this->sendJsonError('Lesefehler: Tour mit ID ' . $tourId . ' wurde nicht gefunden!');
        }


        if ($modelDV->isLocked($tourId)) {
            return $this->sendJsonError( 'Tour wurde gesperrt!' );
        }

        if ( !$this->dayIsDisposable( $aTourOld['DatumVon'] )) {
            return $this->sendJsonVorlaufError();
        }

        if (!$modelDV->tourOperationIsAllowedById($tourId, $userIdentity, 'resize')) {
            return $this->sendJsonError('Skalierungszugriff wurde auf diese Tour verweigert! ' . $modelDV->getLastErrorTourOperationNotAllowed() );
        }
        
        try {
            if (!empty($tourId)) {
                $id = $tourId;
                if ($modelDV->resize($data)) {
                    $this->view->ajax_response->msg = "Die neuen Zeiten der Tour (ID:$id) wurden gespeichert ({$tourZeitVon} - {$tourZeitBis}).";
                    $this->view->ajax_response->data = [ 'posted' => $data ];
                    $this->view->ajax_response->success = true;
                    $this->view->ajax_response->id = $id;
                } else {
                    $this->view->ajax_response->type = "error";
                    $this->view->ajax_response->success = false;
                    $this->view->ajax_response->data = [ 'posted' => $data ];
                    $this->view->ajax_response->error = "Fehler beim Speichern neuer Tourzeiten.\n" . $modelDV->error();
                }
                $this->view->ajax_response->data = $modelDV->fetchEntry($id);
            }
        } catch(Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }
    }
    
    public function dropresourceAction() 
    {
        $timeIn = microtime(true);
        $TLOG = [];
        $this->view->ajax_response->type = 'success';
        $this->view->ajax_response->success = true;
        $rq = $this->getRequest();
        $data = $rq->getParam('data');
        
        // Ist nur relevant, wenn Resource auf der Standard-Resource-Leiste abgelegt wurde
        $applyDefaults      = $rq->getParam('applyDefaults', '');
        $resourceType       = (array_key_exists('resourceType', $data) ? $data['resourceType'] : '');
        $resourceModelClass = (array_key_exists($resourceType, $this->_resourceModels)) ? $this->_resourceModels[$resourceType] : '';
        $resourceDataModelClass = (array_key_exists($resourceType, $this->_resourceDataModels))
            ? $this->_resourceDataModels[$resourceType]
            : '';
        /* @var Model_Mitarbeiter $modelDataRsrc  */
        $modelDataRsrc = MyProject_Model_Database::loadModel( $resourceDataModelClass );

        // Parse true/false from string to boolean
        if ($applyDefaults === 'true') {
            $applyDefaults = true;
        } elseif ($applyDefaults === 'false') {
            $applyDefaults = false;
        }
        
        /* @var $modelTour Model_TourenDispoVorgaenge */
        $modelTour    = new Model_TourenDispoVorgaenge();
        $tourData = $modelTour->getVorgangAndStatus( $data['route_id'] );

        if (!empty($tourData['DatumVon']) && !$this->dayIsDisposable( $tourData['DatumVon'] )) {
            return $this->sendJsonVorlaufError();
        }

        $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'getVorgangAndStatu'];
        $tourIsLocked = $modelTour->isLockedByRow( $tourData );
        $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'isLockedByRow'];
        
        if ($tourIsLocked) {
            $this->json->error('Tour wurde bereits gesperrt!');
        }

        if ($data['route_id']) $data['tour_id'] = $data['route_id'];
        $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'test-log'];
        
        $this->view->ajax_response->sent   = print_r($data, 1);
        $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'test-log'];
        // $this->view->debug = true;
        
        // Pre-Condition
        if (!$resourceModelClass) {
            $this->view->ajax_response->test[] = __LINE__;
            $this->view->ajax_response->type = 'error';
            $this->view->ajax_response->success = false;
            $this->view->ajax_response->error = "Ungültiger ResourceTyp `$resourceType`. Erwarteter Wert FP, MA oder WZ!";
            return;
        }
        $this->view->ajax_response->test[] = __LINE__;
        
        /* @var $modelRsrc MyProject_Model_TourenResourceInterface */
        $modelRsrc = MyProject_Model_Database::loadModel($resourceModelClass);
        $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'loadResourceModel'];
        $this->view->ajax_response->resourceModelClass   = $resourceModelClass;
        
        /* @var $modelLogger Model_TourenDispoLog */        
        $modelLogger = MyProject_Model_Database::loadModel($this->_modelLoggerName);
        $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'loadLogger'];
        $this->view->ajax_response->test[] = __LINE__;
        
        try {
//            die('#' . __LINE__ . 'getRsrcKey(): ' . $modelRsrc->getRsrcKey() . PHP_EOL);

            switch($resourceType) {
                case 'MA':
                    $rid = $data['mid'];
                    break;
                case 'FP':
                    $rid = $data['fid'];
                    break;
                case 'WZ':
                    $rid = $data['wid'];
                    break;

                default:
                    $rid = 0;
            }
            $rsrcName = ($rid) ?  $modelDataRsrc->getName($rid) : $resourceType.'?';
            
            $newID = $modelRsrc->drop($data, $tourData);
            $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'drop'];

            if ($tourData['IsDefault'] && $applyDefaults) {
                $aAppliedTourIDs = $modelRsrc->applyDefaults( $modelRsrc->fetchEntry( $newID ), $tourData['timeline_id'] );
                $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'applyDefaults ' . print_r($aAppliedTourIDs,1)];
            } else {
                $aAppliedTourIDs = [];
                // die(print_r([__LINE__, __FILE__, __METHOD__, 'tourData'=>$tourData, 'applyDefaults'=>$applyDefaults],1));
            }

            $modelLogger->logResource($resourceType, $rid, 'Drop', $data['tour_id']);

            $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'logResource'];
            if (is_array($aAppliedTourIDs)) {
                foreach($aAppliedTourIDs as $_ids) {
                    $modelLogger->logResource($resourceType, $rid, 'Drop-Default', $_ids['tourID']);
                }
                $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'logResource on applied Tours: ' . print_r($aAppliedTourIDs,1)];
            }

            $this->view->ajax_response->msg = "$rsrcName wurde zur Tour ({$data['tour_id']}) hinzugefügt.";
            $this->view->ajax_response->data = [ 'new-rsrc-link-id' => $newID, 'rsrc-id' => $rid] + $data;
            $this->view->ajax_response->id = $newID;
            
        } catch (Exception $e) {
            $this->view->ajax_response->type = 'error';
            $this->view->ajax_response->success = false;
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
//            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL;
            $this->view->ajax_response->error = $e->getMessage();
            
            if (0 && APPLICATION_ENV == 'development') {          
                $this->view->ajax_response->error.= print_r(Zend_Registry::get('db')->getProfiler()->getQueryProfiles(),1);
            }
        }
        $this->view->ajax_response->test[] = __LINE__;
        $TLOG[] = [ __LINE__, microtime(true) - $timeIn, 'End-Of-Action'];
        $this->view->ajax_response->tlog = $TLOG;
    }
        
    public function moveresourceAction() 
    {
        $rq = $this->getRequest();
        $data = $rq->getParam('data');
        
        // Ist nur relevant, wenn Resource auf der Standard-Resource-Leiste abgelegt wurde
        $applyDefaults      = (int)$rq->getParam('applyDefaults', '0');
        $resourceType       = (array_key_exists('resourceType', $data) ? $data['resourceType'] : '');
        $resourceModelClass = (array_key_exists($resourceType, $this->_resourceModels)) ? $this->_resourceModels[$resourceType] : '';
        
        /* @var $modelTour Model_TourenDispoVorgaenge */
        //$modelTour    = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');

        /** @var Model_TourenDispoVorgaenge $modelTour */
        $modelTour    = new Model_TourenDispoVorgaenge();
        $tourIsLocked = $modelTour->isLocked($data['route_id']);

        if ($tourIsLocked) {
            return $this->sendJsonError( 'Tour wurde bereits disponiert/abgeschlossen!' );
        }

        $tourData = $modelTour->fetchEntry($data['route_id']);

        if (!empty($tourData['DatumVon']) && !$this->dayIsDisposable( $tourData['DatumVon'] )) {
            return $this->sendJsonVorlaufError();
        }

        if ($data['route_id']) $data['tour_id'] = $data['route_id'];
        
        $this->view->ajax_response->sent   = print_r($data, 1);
        $this->view->ajax_response->test[] = __LINE__;

        // Pre-Condition
        if (!$resourceModelClass) {
            return $this->sendJsonError( "Ungültiger ResourceTyp `$resourceType`. Erwarteter Wert FP, MA oder WZ!" );
        }
        
        /* @var $modelRsrc MyProject_Model_TourenResourceInterface */
        $modelRsrc = MyProject_Model_Database::loadModel($resourceModelClass);
        
        try {

            $result = $modelRsrc->move($data);
            if ($result->success) {
                if ($applyDefaults) $modelRsrc->applyDefaults( $modelRsrc->fetchEntry( $result->dispoRsrcId ) );

                /** @var Model_TourenDispoLog $modelLogger */
                $modelLogger = new Model_TourenDispoLog();
                $modelLogger->logResource($resourceType, $result->dispoRsrcId, 'Move', $data['tour_id']);

                return $this->sendJsonSuccessID($result->dispoRsrcId, 'Resource wurde verschoben');
            } else {
                return $this->sendJsonError($result->message);
            }
        } catch (Exception $e) {
            return $this->sendJsonError("Fehler beim Verschieben der Resource!\n" . $e->getMessage() );
        }
    }
    
    public function removeresourceAction()
    {
        
        $rq = $this->getRequest();
        $id = $rq->getParam('id', null);
        $resourceType = $rq->getParam('resourceType', null);

        $resourceModelClass =
            (array_key_exists($resourceType, $this->_resourceModels))
                ? $this->_resourceModels[$resourceType]
                : '';

        $resourceDataModelClass =
            (array_key_exists($resourceType, $this->_resourceDataModels))
                ? $this->_resourceDataModels[$resourceType]
                : '';

        /* @var Model_Mitarbeiter $modelDataRsrc  */
        $modelDataRsrc = MyProject_Model_Database::loadModel( $resourceDataModelClass );
        $this->view->ajax_response->test[] = __LINE__;
        
        // Pre-Conditions
        if (!$id || !$resourceType) {
            return $this->sendJsonError("Zu wenig Paramerter`. Erforderliche Werte sind id und resourceType: FP, MA oder WZ!");
        }
        if ( !$resourceModelClass ) {
            return $this->sendJsonError( "Ungültiger ResourceTyp `$resourceType`. Erwarteter Wert FP, MA oder WZ!" );
        }
        
        /* @var $modelRsrc MyProject_Model_TourenResourceInterface */
        $modelRsrc = MyProject_Model_Database::loadModel( $resourceModelClass );
        $data = $modelRsrc->fetchEntry($id);

        if ( !$data ) {
            return $this->sendJsonError( "Es wurde kein " . $resourceType . "-Eintrag mit der id " . $id . " gefunden!" );
        }

        $modelTour    = new Model_TourenDispoVorgaenge();
        $tourData = $modelTour->fetchEntry($data['tour_id']);
        if (!empty($tourData['DatumVon']) && !$this->dayIsDisposable( $tourData['DatumVon'] )) {
            return $this->sendJsonVorlaufError();
        }

        $tourData = $modelTour->fetchEntry($data['tour_id']);

        if (!empty($tourData['DatumVon']) && !$this->dayIsDisposable( $tourData['DatumVon'] )) {
            return $this->sendJsonVorlaufError();
        }

        switch($resourceType) {
            case 'MA':
                $rid = $data['mitarbeiter_id'];
                break;
            case 'FP':
                $rid = $data['fuhrpark_id'];
                break;
            case 'WZ':
                $rid = $data['werkzeug_id'];
                break;

            default:
                $rid = 0;
        }
        $rsrcName = ($rid) ?  $modelDataRsrc->getName($rid) : $resourceType.'?';
        
        try {
            if ( $modelRsrc->delete($id) ) {
                /* @var $modelLogger Model_TourenDispoLog */            
                $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
                $modelLogger->logResource($resourceType, $id, 'removed', $data['tour_id']);

                return $this->sendJsonSuccess( "Die Ressource $rsrcName wurde aus der Tour {$data['tour_id']} entfernt!" );
            } else {
                return $this->sendJsonError( "Fehler beim Entfernen der $resourceType-Ressource ($id) mit der id " . $id . "!" );
            }
        } catch (Exception $e) {
            return $this->sendJsonError( "Fehler beim Entfernen der Resource!\n" . $e->getMessage() );
        }
    }
    
    public function removeresourcedefaultAction()
    {        
        $rq = $this->getRequest();
        $id = $rq->getParam('id', null);
        $resourceType = $rq->getParam('resourceType', null);

        $resourceModelClass = (array_key_exists($resourceType, $this->_resourceModels)) ? $this->_resourceModels[$resourceType] : '';
        $resourceDataModelClass = (array_key_exists($resourceType, $this->_resourceDataModels)) ? $this->_resourceDataModels[$resourceType] : '';

        $this->view->ajax_response->test[] = __LINE__;
        
        // Pre-Conditions
        if (!$id || !$resourceType) {
            return $this->sendJsonError( "Zu wenig Paramerter`. Erwartete Werte sind id und resourceType: FP, MA oder WZ!" );
        }

        if ( !$resourceModelClass ) {
            return $this->sendJsonError( "Ungültiger ResourceTyp `$resourceType`. Erwarteter Wert FP, MA oder WZ!" );
        }

        /* @var $modelRsrc MyProject_Model_TourenResourceInterface */
        $modelRsrc = MyProject_Model_Database::loadModel( $resourceModelClass );
        $data = $modelRsrc->fetchEntry($id);

        /* @var Model_Mitarbeiter $modelDataRsrc  */
        $modelDataRsrc = MyProject_Model_Database::loadModel( $resourceDataModelClass );
        
        if ( !$data ) {
            return $this->sendJsonError( "Es wurde kein Eintrag (" . $resourceType . ") mit der id " . $id . " gefunden!" );
        }

        switch($resourceType) {
            case 'MA':
                $rid = $data['mitarbeiter_id'];
                break;
            case 'FP':
                $rid = $data['fuhrpark_id'];
                break;
            case 'WZ':
                $rid = $data['werkzeug_id'];
                break;

            default:
                $rid = 0;
        }
        $rsrcName = ($rid) ?  $modelDataRsrc->getName($rid) : $resourceType.'?';
        
        /* @var $modelRsrc Model_TourenDispoFuhrpark */
        $modelRsrc = MyProject_Model_Database::loadModel( $resourceModelClass );
        
        try {
            if ($aUnlinkedTourIds = $modelRsrc->removeDefault($id) ) {
                /* @var $modelLogger Model_TourenDispoLog */
                $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');

                foreach($aUnlinkedTourIds as $_tour_id) {
                    $modelLogger->logResource($resourceType, $rid, 'removed-default', $_tour_id);
                }

                $iUnlinkedTours = count($aUnlinkedTourIds);
                return $this->sendJsonSuccess(
                    "Default-Ressource $rsrcName (" . $resourceType . ") "
                    ."mit der id " . $id . " wurde aus $iUnlinkedTours Touren (inkl. Default-Leiste) gelöscht!");
            } else {
                return $this->sendJsonError(
                    "Default-Ressource  $rsrcName (" . $resourceType . ") "
                    ."mit der id " . $id . " konnte nicht geloescht werden!");
            }
        } catch (Exception $e) {
            return $this->sendJsonError("Fehler beim Löschen der Default-Resourcen!\n" . $e->getMessage() );
        }
    }
    
    public function vorgangsabschlussAction()
    {
        $this->_helper->viewRenderer->setRender('vorgangsabschluss');
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        
        $this->view->tour_id = $tour_id;
    }
    
    /**
     * @todo Redundanz-Entfernen:
     * Funktion ist in Bezug auf den Datenabruf eigentlich identisch mit vorgangspositionenAction()
     * @see vorgangspositionenAction
     */
    public function vorgangsabschlussPositionenAction() 
    {
        $this->_helper->viewRenderer->setRender('vorgangsabschluss-positionen');
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);

        
        $role = MyProject_Auth_Adapter::getUserRole();
        
        $acl = Zend_Registry::get('acl');
        $this->view->ajax_response->allowed = new stdClass();
        $this->view->ajax_response->allowed->updateabschlusspositionen =
            $acl->isAllowed($role, 'touren_ajax', 'updateabschlusspositionen');
        $this->view->ajax_response->allowed->finishtourabschluss =
            $acl->isAllowed($role, 'touren_ajax', 'finishtourabschluss');
        $this->view->ajax_response->allowed->finishauftragsabschluss =
            $acl->isAllowed($role, 'touren_ajax', 'finishauftragsabschluss');        
        
        $this->view->ajax_response = new stdClass();

        
        $role = MyProject_Auth_Adapter::getUserRole();        
        $acl = Zend_Registry::get('acl');
        $this->view->ajax_response->allowed = new stdClass();
        $this->view->ajax_response->allowed->updateabschlusspositionen =
            $acl->isAllowed($role, 'touren_ajax', 'updateabschlusspositionen');
        $this->view->ajax_response->allowed->finishtourabschluss =
            $acl->isAllowed($role, 'touren_ajax', 'finishtourabschluss');
        $this->view->ajax_response->allowed->finishauftragsabschluss =
            $acl->isAllowed($role, 'touren_ajax', 'finishauftragsabschluss');
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $NAME = Zend_Db_Table::NAME;
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblDA = MyProject_Model_Database::loadStorage('tourenDispoAuftraege')->info($NAME);        
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format = $rq->getParam('format', 'json');
        $posBelongTo = $rq->getParam( 'belong', 'all');
        
        $sqlVorgangsIdByTourId =
             'SELECT dv.*, da.auftrag_abgeschlossen_am, da.auftrag_abgeschlossen_user, da.auftrag_disponiert_am, da.auftrag_disponiert_user  '
            .'FROM ' . $tblDV .  ' dv '
            .'LEFT JOIN ' . $tblDA . ' da  USING(Mandant, Auftragsnummer) '
            .'WHERE dv.tour_id = :tour_id LIMIT 1';
        $row = $db->fetchRow($sqlVorgangsIdByTourId, array(':tour_id' => $tour_id), Zend_Db::FETCH_ASSOC);
        
        $mandant = $row['Mandant'];
        $auftragsnr = $row['Auftragsnummer'];
        
        $sqlVorgangsStati =
             'SELECT dv.tour_id, tour_abgeschlossen_am, tour_abgeschlossen_user, tour_disponiert_am, tour_disponiert_user  '
            .'FROM ' . $tblDV .  ' dv '
            .'LEFT JOIN ' . $tblDA . ' da  USING(Mandant, Auftragsnummer) '
            .'WHERE Mandant = :mandant AND Auftragsnummer = :auftragsnr LIMIT 1';
        $tourenStati = $db->fetchAll($sqlVorgangsStati, array(
            ':mandant'    => $mandant,
            ':auftragsnr' => $auftragsnr,
            ), Zend_Db::FETCH_ASSOC);
        
        $this->view->ajax_response->data = new stdClass();
        $this->view->ajax_response->data->tour_id = $tour_id;
        $this->view->ajax_response->data->mandant = $mandant;
        $this->view->ajax_response->data->auftragsnummer = $auftragsnr;
        $this->view->ajax_response->data->auftrag_disponiert_am      = $row['auftrag_disponiert_am'];
        $this->view->ajax_response->data->auftrag_disponiert_user    = $row['auftrag_disponiert_user'];
        $this->view->ajax_response->data->auftrag_abgeschlossen_am   = $row['auftrag_abgeschlossen_am'];
        $this->view->ajax_response->data->auftrag_abgeschlossen_user = $row['auftrag_abgeschlossen_user'];
        $this->view->ajax_response->data->tourenStati  = $tourenStati;
        $this->view->ajax_response->data->dispovorgang = $row;
        $this->view->ajax_response->msg = ''; //$sqlAPositionen;

        if ($format == 'json')
            $this->_helper->viewRenderer->setRender('jsonresponse');
        else
            $this->_helper->viewRenderer->setRender('vorgangsabschluss-positionen');
        
        try {
            /* @var $modelDP Model_TourenDispoPositionen */
            $modelDP = MyProject_Model_Database::loadModel('tourenDispoPositionen');
            $this->view->ajax_response->data->positionen = $modelDP->getPositionen($tour_id, $posBelongTo);
        } catch(Exception $e) {
            die( $e->getTraceAsString() );
            $this->view->ajax_response->error = $e->getMessage() . PHP_EOL .$e->getTraceAsString();
        }
        
    }
    
    public function vorgangsabschlussZeiterfassungAction() 
    {
        $this->_helper->viewRenderer->setRender('vorgangsabschluss-zeiterfassung');
        
        $role = MyProject_Auth_Adapter::getUserRole();
        
        $acl = Zend_Registry::get('acl');
        $this->view->ajax_response->allowed = new stdClass();
        $this->view->ajax_response->allowed->updatetourabschlusszeiten =
            $acl->isAllowed($role, 'touren_ajax', 'updatetourabschlusszeiten');
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        
        $model = new Model_TourenDispoVorgaenge();
        $modelV = new Model_Vorgaenge();
        $modelVS = new Model_TourenDispoAuftraege();
        
        $tour = $model->fetchEntry($tour_id);
        $this->view->ajax_response->tour_id = $tour_id;
        $this->view->ajax_response->tour = $tour;
        $this->view->ajax_response->vorgang = $modelV->fetchEntry( $tour['Mandant'], $tour['Auftragsnummer']);
        $this->view->ajax_response->vorgangsstatus = $modelVS->fetchEntry( $tour['Mandant'], $tour['Auftragsnummer']);
        $this->view->ajax_response->resources = $model->getResources($tour_id);

        $touren = $model->fetchEntries(array(
            'where' => $this->db->quoteInto('Mandant = ?', $tour['Mandant']) 
                     . $this->db->quoteInto(' AND Auftragsnummer = ?', $tour['Auftragsnummer'], 'Integer')
                     . $this->db->quoteInto(' AND tour_id != ?', $tour_id, 'Integer'),
            'order' => array('DatumVon ASC', 'ZeitVon ASC', 'ZeitBis ASC') ));
        
        foreach($touren as &$_tour) {
            $_tour['resources'] = $model->getResources($_tour['tour_id']);
        }
        $this->view->ajax_response->touren = $touren;
        $this->view->ajax_response->tourenZeitenErfasstStatus = $model->statusTourenZeitenErfassung($tour['Mandant'], $tour['Auftragsnummer']);

        $modelLstg = new Model_Db_Leistung();
        $order = array('ressourcen_typ', 'leistungs_name');
        $this->view->ajax_response->leistungen = $modelLstg->fetchAll(null, $order);
    }
    
    public function updatetourabschlusszeitenAction()
    {
        $this->_helper->viewRenderer->setRender('vorgangsabschluss-zeiterfassung');
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        $ma = $rq->getParam('ma', null);
        $fp = $rq->getParam('fp', null);
        $finish = $rq->getParam('finish', null);
        $finishAuftrag = $rq->getParam('finishauftrag', null);
        
        $model   = new Model_TourenDispoVorgaenge();
        
        if ($model->isLocked($tour_id) < Model_TourenDispoVorgaenge::STATUS_AUFTRAG_ABGESCHLOSSEN) {
            $modelMA = new Model_TourenDispoMitarbeiter();
            $modelFP = new Model_TourenDispoFuhrpark();
            $tour = $model->fetchEntry($tour_id);

            if ($ma) $modelMA->updateAufwand ($ma);
            if ($fp) $modelFP->updateAufwand ($fp);
            
            if ($finish && ($cntURZ = $model->countUnerfassteResourcenZeitenByTourId($tour_id)) )  {
                $this->_helper->json(array(
                    'error' => 'Auftrag kann nicht abgeschlossen werden!' . PHP_EOL
                             . 'Es liegen noch ' . $cntURZ . ' zeitlich nicht erfasste Resourcen vor!',
                ));
                exit;
            }

            if ($finish) {
                $model->finishtourzeitenabschluss($tour_id,  MyProject_Auth_Adapter::getUserName());
                $model->finishtourabschluss($tour_id, MyProject_Auth_Adapter::getUserName());
            }
            
            $this->view->ajax_response->tour_id = $tour_id;
            $this->view->ajax_response->resources = $model->getResources($tour_id);            
        } else {
            $this->_helper->json(array(
                'error' => 'Auftrag wurde bereits abgeschlossen! '
            ));
        }
        
        if ($finishAuftrag 
             && ($unerfassteTouren = $model->countTourenZeitenUnerfasst($tour['Mandant'], $tour['Auftragsnummer'])) ) {
                $this->view->ajax_response->error = 'Vorgang kann nicht abgeschlossen werden, da bei ' 
                        . $unerfassteTouren . ' Touren die Zeiterfassung noch nicht abgeschlossen ist.';
                return;
        }
        
        if ($finishAuftrag) {
            $modelDA = new Model_TourenDispoAuftraege();
            $modelDA->finishdispo($tour['Mandant'], $tour['Auftragsnummer'], MyProject_Auth_Adapter::getUserName());
        }
    }
    
    public function updatetourenabschlusszeitenAction()
    {
        $this->_helper->viewRenderer->setRender('vorgangsabschluss-zeiterfassung');
        $username = MyProject_Auth_Adapter::getUserName();
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        $ma = $rq->getParam('ma', null);
        $fp = $rq->getParam('fp', null);
        $finish = $rq->getParam('finish', null);
        $finishAuftrag = $rq->getParam('finishauftrag', null);
        
        $role = MyProject_Auth_Adapter::getUserRole();        
        $acl = Zend_Registry::get('acl');
        $this->view->ajax_response->allowed = new stdClass();
        $this->view->ajax_response->allowed->updatetourabschlusszeiten =
            $acl->isAllowed($role, 'touren_ajax', 'updatetourabschlusszeiten');

        $touren = array();
        if (is_array($ma)) foreach($ma as $_tour_id => $v) { 
            $touren[$_tour_id]['ma'] = $v;
        }
        if (is_array($fp)) foreach($fp as $_tour_id => $v) {
            $touren[$_tour_id]['fp'] = $v;
        }
        
        $model   = new Model_TourenDispoVorgaenge();
        $modelMA = new Model_TourenDispoMitarbeiter();
        $modelFP = new Model_TourenDispoFuhrpark();
        $tour = $model->fetchEntry($tour_id);

        $mid = $tour['Mandant'];
        $anr = $tour['Auftragsnummer'];
        
        $this->view->ajax_response->tour_id = $tour_id;
        $this->view->ajax_response->resources = $model->getResources($tour_id);

        foreach($touren as $_tour_id => $_rsrc) {
            
            if ($model->isLocked($_tour_id) < Model_TourenDispoVorgaenge::STATUS_AUFTRAG_ABGESCHLOSSEN) {

                if (!empty($_rsrc['ma'])) {
                    $modelMA->updateAufwand ( $_rsrc['ma'] );
                }
                
                if (!empty($_rsrc['fp'])) {
                    $modelFP->updateAufwand ( $_rsrc['fp'] );
                }
                
                if ($finish && ($cntURZ = $model->countUnerfassteResourcenZeitenByTourId($_tour_id)) )  {
                    return $this->sendJsonError(
                        "Auftrag kann nicht abgeschlossen werden!\n"
                        . 'Es liegen noch ' . $cntURZ . ' zeitlich nicht erfasste Resourcen in Tour ' . $_tour_id . ' vor!');
                }

                if ($finish) {                    
                    $model->finishtourzeitenabschluss($_tour_id,  $username);
                    $model->finishtourabschluss($_tour_id, $username);
                }
                
            } elseif(0) {
                return $this->sendJsonError( 'Auftrag wurde bereits abgeschlossen!' );
            }
        }
        
        if ($finishAuftrag && ($unerfassteTouren = $model->countTourenZeitenUnerfasst($mid, $anr)) ) {
            return $this->sendJsonError( 'Vorgang kann nicht abgeschlossen werden, da bei '
                        . $unerfassteTouren . ' Touren die Zeiterfassung noch nicht abgeschlossen ist.');
        }
        
        if ($finishAuftrag) {
            $modelDA = new Model_TourenDispoAuftraege();
            $modelDA->finishdispo($mid, $anr, $username);
            $modelDA->finishauftrag($mid, $anr, array(), $username);
        }
        
        $this->_helper->json( $this->view->ajax_response );
    }
    
    public function finishtourenabschlusszeitenAction()
    {
        $r = $this->getRequest();
        $db = $this->db;
        $tourid = (int)$r->getParam('tour_id');
        $model = new Model_TourenDispoVorgaenge();
        
        die( '<pre>#' . __LINE__ . ' ' . __METHOD__ . PHP_EOL . print_r($this->getRequest()->getParams(), 1) . '</pre>' );
    }
    
    public function reopentourenabschlusszeitenAction()
    {
        die( '<pre>#' . __LINE__ . ' ' . __METHOD__ . PHP_EOL . print_r($this->getRequest()->getParams(), 1) . '</pre>' );
    }
    
    public function reopentourabschlusszeitenAction()
    {
        $this->_helper->viewRenderer->setRender('vorgangsabschluss-zeiterfassung');
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        
        $model   = new Model_TourenDispoVorgaenge();
        
        if ($model->isLocked($tour_id) < Model_TourenDispoVorgaenge::STATUS_AUFTRAG_ABGESCHLOSSEN) {
            $model->opentourzeitenabschluss($tour_id,  MyProject_Auth_Adapter::getUserName());
            
            $this->view->ajax_response->tour_id = $tour_id;
            $this->view->ajax_response->resources = $model->getResources($tour_id);
        } else {
            return $this->sendJsonError('Auftrag wurde bereits abgeschlossen! ');
        }
    }
    
    public function vorgangsdatenAction()
    {
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        $format = $rq->getParam('format', 'html');
        $withResources = (int)$rq->getParam('with-resources', 0);
        $this->view->vorgangsdaten = new stdClass();
        $this->view->vorgangsdaten->error = '';
        $this->view->vorgangsdaten->msg = '';
        $this->view->vorgangsdaten->data = array();
        
        $role = MyProject_Auth_Adapter::getUserRole();
        
        $acl = Zend_Registry::get('acl');
        $this->view->vorgangsdaten->allowResourceRemove = 
            $acl->isAllowed($role, 'touren_ajax', 'removeresource');
        
        /* @var $modelDV Model_TourenDispoVorgaenge */
        $modelDV = new Model_TourenDispoVorgaenge();
        $modelDA = new Model_TourenDispoAuftraege();
        $data = $this->view->vorgangsdaten->data = $modelDV->getVorgang($tour_id);
        $this->view->vorgangsdaten->data_auftragsstatus = $modelDA->fetchEntry($data['Mandant'], $data['Auftragsnummer']);
        
        if ($withResources) 
            $this->view->vorgangsdaten->resources = $modelDV->getResources($tour_id);
        
        $this->_helper->viewRenderer->setRender(
            $format == 'html-base' ? 'vorgangsdaten-base':'vorgangsdaten'
        );
    }
    
    
    public function vorgangsdatendefaultsAction()
    {   
        $this->_helper->viewRenderer->setRender('vorgangsdatendefaults');
    }

    public function vorgangshistorieAction()
    {
        $rq = $this->getRequest();
        $tour_id  = $rq->getParam('tour_id', null);
        $format   = $rq->getParam('format', 'html');

        $modelLogger = new Model_TourenDispoLog();

        $gridConverter = new MyProject_Jqgrid_Converter();

        $result = $modelLogger->getTourHistorie($tour_id);

        if ($format == 'json') {
            if (!$result) {
                $this->json->error('Historien-Einträge konnten nicht geladen werden!');
            }

            $numRows = count($result['rows']);

            $gridResult = $gridConverter::rowsToGridResult(
                $result['rows'],
                $result['total'],
                $result['offset'],
                $result['limit']
            );

            $this->_helper->json($gridResult);
        }

        $this->_helper->viewRenderer->setRender('vorgangshistorie');
        $this->view->rows = $result['rows'];

    }

    public function vorgangsbemerkungenAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $rq = $this->getRequest();
        $tour_id   = $rq->getParam('tour_id', null);
        $bemerkung = $rq->getParam('bemerkung', null);
        $avisiert  = $rq->getParam('avisiert', 0);
        $attachments = $rq->getParam('attachments', 0);
        $attribute = $rq->getParam('attr', null);

        if (@isset($attribute['dummie'])) {
            unset($attribute['dummie']);
        }
        if (!is_array($attribute) || count($attribute) == 0) {
            $attribute = null;
        }
        $saved = true;
        
        $action = $this->view->baseUrl() . '/'.$rq->getModuleName().'/'.$rq->getControllerName().'/'.$rq->getActionName();
        
        
        $this->view->tour_id = $tour_id;
        $this->view->vorgangsbemerkungen = new stdClass();
        $this->view->vorgangsbemerkungen->error = '';
        $this->view->vorgangsbemerkungen->msg = '';
        $this->view->vorgangsbemerkungen->data = array();
        $this->view->vorgangsbemerkungen->action = $action;
        $this->_helper->viewRenderer->setRender('vorgangsbemerkungen');
        
        $baseUrl = $this->getFrontController()->getBaseUrl();
        $modelAttachments = new Model_TourenDispoAttachments();
        $this->view->attachments = (object)array(
          'uploadForm' => $modelAttachments->getUploadForm($tour_id, array(
              'action' => $this->getFrontController()->getBaseUrl() . '/touren/attachments/index/?ajaxcallback=parent.XFb.uploadFinished'
          ) ),
          'list' => $modelAttachments->getTableList($tour_id, array(
                    'ofld' => 'created', 
                    'odir' => 'ASC',
                    'filepath' => $baseUrl . '/touren/attachments/file/tour_id/' . $tour_id,
                    'droppath' => $baseUrl . '/touren/attachments/drop/tour_id/' . $tour_id,
                    'listpath' => $baseUrl . '/touren/attachments/list/tour_id/' . $tour_id
          )),
        );
        
        try {
            /* @var $tourModelTxt Model_TourenDispoVorgaengeText */
            $tourModel = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
            $tourModelTxt = MyProject_Model_Database::loadModel('tourenDispoVorgaengeText');

            if ($tour_id && $rq->isPost() ) {
                $tourModel->update(array(
                    'avisiert'=>(int) $avisiert,
                    'attachments'=>(int) $attachments,
                ), $tour_id);

                if ($attribute) {
//                        $tourModelTxt->updateAttributes($tour_id, $attribute);
                    $tourModelTxt->updatePrintFlag($tour_id, $attribute);
                }

                if ($bemerkung) {
                    $saved = $tourModelTxt->saveBemerkung($tour_id, $bemerkung);
                    if (!$saved){
                        $this->view->vorgangsbemerkungen->error = 'Toureintrag mit der Id '.$tour_id.' konnte nicht ermittelt werden!';
                    }
                }

                $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
                $modelLogger->logTour($tour_id, 'bemerkung');

                $this->_helper->viewRenderer->setRender('vorgangsbemerkungen-saved');

                if (!$saved) {
                    $this->view->vorgangsbemerkungen->error = 'Toureintrag mit der Id '.$tour_id.' konnte nicht ermittelt werden!';
                }
            }
            $row = $tourModel->fetchEntry($tour_id);
            
            $rowB = $tourModelTxt->getBemerkungen($tour_id);
            
            $this->view->vorgangsbemerkungen->data = array_merge($row, $rowB);
        } catch(Exception $e) {
            $storage = $tourModelTxt = MyProject_Model_Database::loadStorage('tourenDispoVorgaengeText');
            die( '<pre>' 
                    . $e->getMessage(). PHP_EOL 
                    . $e->getTraceAsString() . PHP_EOL 
                    . print_r($storage->info(),1) . PHP_EOL );
            ;
        }
    }
    
    public function vorgangskopfdatenAction()
    {
        
    }
    
    public function vorgangsdispodialogAction() {
        $rq = $this->getRequest();
        $lager_id = $rq->getParam('lager', 1);
        
        $ini = new Zend_Config_Ini(APPLICATION_PATH . '/configs/grosskunden.ini', 'grosskunden');
        $kunden = $ini->toArray();
        $this->view->grosskunden = array();
        foreach($kunden as $k => $v) {
            if ($v) $this->view->grosskunden[] = $k;
        }
        
        $this->_helper->viewRenderer->setRender('vorgangsdispodialog');
        
        $this->view->lager_id = $lager_id;
        $this->view->DatumVon = $rq->getParam( 'DatumVon', date('Y-m-d'));
        $this->view->DatumBis = $rq->getParam( 'DatumBis', date('Y-m-d', time()+(30*24*3600)));
        $this->view->allowed = new stdClass;
        
        $role = MyProject_Auth_Adapter::getUserRole();
        
        $acl = Zend_Registry::get('acl');       
        $this->view->allowed->addportletserie = 
            $acl->isAllowed($role, 'touren_ajax', 'addportletserie');
    }



    public function potentialconflictsAction()
    {
        $rq = $this->getRequest();
        $sMonth = $rq->getParam('month', '');
        $sFrom = $rq->getParam('from', '');
        $sBis = $rq->getParam('bis', '');

        $from = '';
        $bis = '';


        $validator = new MyProject_Validate_Date();

        if ( $sMonth && $validator->isValid("$sMonth-01") ) {
            if (preg_match('#^(\d{4})-(\d{1,2})$#', $sMonth, $m)) {
                $iMonthTime = strtotime("$sMonth-01");
                $iDaysOfMonth = date('t', $iMonthTime);
                $from = date('Y-m-d', strtotime("$sMonth-01"));
                $bis = date('Y-m-d', strtotime("$sMonth-$iDaysOfMonth"));
            }
        }

        if ( $sFrom && $sBis && $validator->isValid($sFrom) && $validator->isValid($sBis)) {
            if (strtotime($sFrom) < strtotime($sBis) ) {
                $from = date('Y-m-d', strtotime($sFrom));
                $bis = date('Y-m-d', strtotime($sBis));
            }
        }

        if (!$from || !$bis) {
            return $this->sendJsonError( 'Invalid Date-Range-Params! Use month/YYYY-MM OR from/YYYY-MM-DD/bis/YYYY-MM-DD' );
        }

        $db = Zend_Db_Table::getDefaultAdapter();

        $sqlScanMA = MyProject_Helper_String::stripMargin(<<<EOT
                |select
                |	p.datum,
                |	tma.mitarbeiter_id,
                |	ma.name,
                |	ma.extern_id
                |	, count(distinct( p.portlet_id )) num_portlets
                |	, count(distinct( t.tour_id )) num_tours
                |	, count(distinct( t.timeline_id )) num_timelines
                | FROM mr_touren_portlets AS p
                | LEFT JOIN mr_touren_timelines AS tl ON (p.portlet_id = tl.portlet_id)
                | LEFT JOIN mr_touren_dispo_vorgaenge t ON (tl.timeline_id = t.timeline_id)
                | LEFT JOIN mr_touren_dispo_mitarbeiter tma ON (t.tour_id = tma.tour_id)
                | LEFT JOIN mr_mitarbeiter ma ON (tma.mitarbeiter_id = ma.mid)
                | WHERE p.datum BETWEEN :from AND :bis
                |    AND mitarbeiter_id IS NOT NULL
                | GROUP BY p.datum, tma.mitarbeiter_id, ma.name, ma.extern_id
                | HAVING count(distinct( t.timeline_id )) > 1
EOT
        );

        $sqlScanFP = MyProject_Helper_String::stripMargin(<<<EOT
                |select
                |	p.datum,
                |	tfp.fuhrpark_id,
                |	CONCAT(fp.kennzeichen, ' ', fp.hersteller, ' ', fp.modell, ' ', fp.fahrzeugart) AS fahrzeug,
                |	fp.extern_id
                |	, count(distinct( p.portlet_id )) num_portlets
                |	, count(distinct( t.tour_id )) num_tours
                |	, count(distinct( t.timeline_id )) num_timelines
                | FROM mr_touren_portlets AS p
                | LEFT JOIN mr_touren_timelines AS tl ON (p.portlet_id = tl.portlet_id)
                | LEFT JOIN mr_touren_dispo_vorgaenge t ON (tl.timeline_id = t.timeline_id)
                | LEFT JOIN mr_touren_dispo_fuhrpark tfp ON (t.tour_id = tfp.tour_id)
                | LEFT JOIN mr_fuhrpark fp ON (tfp.fuhrpark_id = fp.fid)
                | WHERE p.datum BETWEEN :from AND :bis
                |    AND fuhrpark_id IS NOT NULL
                | GROUP BY p.datum, tfp.fuhrpark_id, fp.extern_id, fp.kennzeichen, fp.hersteller, fp.modell, fp.fahrzeugart
                | HAVING count(distinct( t.timeline_id )) > 1
EOT
        );

        $aScanMA = $db->fetchAll($sqlScanMA, [
            'from' => $from,
            'bis' => $bis,
        ], Zend_Db::FETCH_ASSOC);

        $aScanFP = $db->fetchAll($sqlScanFP, [
            'from' => $from,
            'bis' => $bis,
        ], Zend_Db::FETCH_ASSOC);

        $this->sendJsonSuccess('', [
            'Mitarbeiter' => $aScanMA,
            'Fuhrpark' => $aScanFP,
        ]);
    }

}
