<?php 

/*
 * Bearbeitungsstatus = 2
 * 
 * Vorgaenge nach Datum selektieren
 SELECT 
 Lieferwoche, 
 Lieferjahr, 
 Liefertermin, 
 LieferterminFix
 / *,Angebotsfreigabedatum, 
 Aufbestfreigabedatum, 
 Packzettelfreigabedatum, 
 Lieferscheinfreigabedatum, 
 Rechnungsfreigabedatum * /
 FROM AuftragsKoepfe
 WHERE Auftragsnummer = 1047035
 
 SELECT 
 Lieferwoche,
 Lieferjahr,
 Liefertermin,
 LieferterminFix,
 AvisierterTermin,
 AvisierteWoche,
 AvisiertesJahr,
 AvisierterTerminFix
 FROM auftragspositionen
 WHERE Auftragsnummer = 1047035
 
 SELECT 
 Lieferwoche,
 Lieferjahr,
 Liefertermin,
 LieferterminFix
 FROM bestellkoepfe
 WHERE Auftragsnummer = 1047035
 
 SELECT
 Lieferwoche,
 Lieferjahr,
 Liefertermin,
 LieferterminFix
 FROM bestellpositionen
 WHERE Auftragsnummer = 1047035
 */

class Touren_AjaxController extends Zend_Controller_Action
{
    protected $_resourceModels = array(
        "FP" => 'tourenDispoFuhrpark',
        "MA" => 'tourenDispoMitarbeiter',
        "WZ" => 'tourenDispoWerkzeug'
    );
    
    // TourenDispoLogger: kann MyProject_Model_Database::loadModel geladen werden
    protected $_modelLoggerName = 'tourenDispoLog';
    
    public function init() {
//        die(__METHOD__);
        // Rückgabe-Objekt initialisieren
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->type = true;
        $this->view->ajax_response->msg = '';
        $this->view->ajax_response->test = array(__LINE__);
        
        // Warning:
        // Never Use Underscores in your viewscript-Filename: e.g. json_response doesn't work!
        $this->_helper->viewRenderer->setRender('jsonresponse');
        
        // TourenDispoLogger
        $this->_modelLoggerName = 'tourenDispoLog';
        
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
        
        if ($checkTour)        $sql.= 'AND A.tour_id = ' . $db->quote($checkTour) . PHP_EOL;
        elseif ($checkTimeline)$sql.= 'AND timeline_id = :timeline_id ' . $db->quote($checkTimeline) . PHP_EOL;
        elseif ($checkPortlet) $sql.= 'AND portlet_id = :portlet_id ' . $db->quote($checkPortlet) . PHP_EOL;
        elseif ($checkLager)   $sql.= 'AND lager_id = :lager_id ' . $db->quote($checkLager) . PHP_EOL;
        
        if ($checkVorgang) {
            $from.= ' LEFT JOIN mr_touren_dispo_vorgaenge T ON (A.tour_id = T.tour_id)' . PHP_EOL;
            $where.= 'AND T.Auftragsnummer = ' . $db->quote($checkVorgang)
                    .'AND T.Mandant = ' . $db->quote($checkMandant) . PHP_EOL;
        }
        
        $sql = 'SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where;
        
        $CountActivities = (int)$db->fetchOne($sql);
        
        die($CountActivities . PHP_EOL . $sql);
        
        // Send Result direct as Json without use of MVC
        $this->_helper->json($CountActivities);
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
        
        if ($checkTour)        $sql.= 'AND A.tour_id = ' . $db->quote($checkTour) . PHP_EOL;
        elseif ($checkTimeline)$sql.= 'AND timeline_id = :timeline_id ' . $db->quote($checkTimeline) . PHP_EOL;
        elseif ($checkPortlet) $sql.= 'AND portlet_id = :portlet_id ' . $db->quote($checkPortlet) . PHP_EOL;
        elseif ($checkLager)   $sql.= 'AND lager_id = :lager_id ' . $db->quote($checkLager) . PHP_EOL;
        
        if ($checkVorgang) {
            $from.= ' LEFT JOIN mr_touren_dispo_vorgaenge T ON (A.tour_id = T.tour_id)' . PHP_EOL;
            $where.= 'AND T.Auftragsnummer = ' . $db->quote($checkVorgang)
                    .'AND T.Mandant = ' . $db->quote($checkMandant) . PHP_EOL;
        }
        
        $sql = 'SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where;
        
        $CountActivities = (int)$db->fetchOne($sql);
        
        die($CountActivities . PHP_EOL . $sql);
        
        // Send Result direct as Json without use of MVC
        $this->_helper->json($CountActivities);
    }
    
    public function vorgangstimetableAction()
    {
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $NAME = Zend_Db_Table::NAME;
        $TL = MyProject_Model_Database::loadModel('tourenTimelines');
        $TV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $rq = $this->getRequest();
        $format = $rq->getParam('format', 'html');
        $tour_id = $rq->getParam('tour_id', 0);
        $tldata = $rq->getParam('data', array());
        
        if ($tour_id) {
            $this->view->ajax_response->data = $TV->fetchEntry($tour_id);
        }
        
        if ($format == 'json')
        $this->_helper->viewRenderer->setRender('jsonresponse');
        else
        $this->_helper->viewRenderer->setRender( $rq->getActionName() );
        
    }
    
    public function vorgangspositionenAction() 
    {        
        $this->view->ajax_response = new stdClass();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $NAME = Zend_Db_Table::NAME;
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);        
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format = $rq->getParam('format', 'json');
        $posBelongTo = $rq->getParam( 'belong', 'all');
        
        $sqlVorgangsIdByTourId =
             'SELECT Mandant, Auftragsnummer '
            .'FROM ' . $tblDV .  ' '
            .'WHERE tour_id = :tour_id';
        $rows = $db->fetchAll($sqlVorgangsIdByTourId, array(':tour_id' => $tour_id), Zend_Db::FETCH_ASSOC);

        $mandant = $rows[0]['Mandant'];
        $auftragsnr = $rows[0]['Auftragsnummer'];
        $this->view->ajax_response->data = new stdClass();
        $this->view->ajax_response->data->tour_id = $tour_id;
        $this->view->ajax_response->data->mandant = $mandant;
        $this->view->ajax_response->data->auftragsnummer = $auftragsnr;
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
    
    public function vorgangsresourcenAction() 
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->data = new stdClass();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format  = $rq->getParam('format', 'html');
        
        if ($format == 'json')
            $this->_helper->viewRenderer->setRender('jsonresponse');
        else
            $this->_helper->viewRenderer->setRender('vorgangsresourcen');
        
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblAP = MyProject_Model_Database::loadStorage('auftragspositionen')->info($NAME);
        $tblBK = MyProject_Model_Database::loadStorage('bestellkoepfe')->info($NAME);
        $tblBP = MyProject_Model_Database::loadStorage('bestellpositionen')->info($NAME);
        
        $tblDP = MyProject_Model_Database::loadStorage('tourenDispoPositionen')->info($NAME);
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblDF = MyProject_Model_Database::loadStorage('tourenDispoFuhrpark')->info($NAME);
        $tblDM = MyProject_Model_Database::loadStorage('tourenDispoMitarbeiter')->info($NAME);
        $tblDW = MyProject_Model_Database::loadStorage('tourenDispoWerkzeug')->info($NAME);
        
        $tblDMB = MyProject_Model_Database::loadStorage('tourenDispoMitarbeiterText')->info($NAME);
        
        $tblTP = MyProject_Model_Database::loadStorage('tourenPortlets')->info($NAME);
        $tblTL = MyProject_Model_Database::loadStorage('tourenTimelines')->info($NAME);
        $tblFP = MyProject_Model_Database::loadStorage('fuhrpark')->info($NAME);
        $tblMA = MyProject_Model_Database::loadStorage('mitarbeiter')->info($NAME);
        $tblWZ = MyProject_Model_Database::loadStorage('werkzeug')->info($NAME);
        
        $sqlVorgangsIdByTourId =
             'SELECT Mandant, Auftragsnummer '
            .'FROM mr_touren_dispo_vorgaenge '
            .'WHERE tour_id = :tour_id';
        $rows = $db->fetchAll($sqlVorgangsIdByTourId, array(':tour_id' => $tour_id));

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
        
        // Send Result direct as Json without use of MVC
        //$this->_helper->json($result);
    }
    
    public function vorgangsresourcendefaultsAction() 
    {
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id');
        $format  = $rq->getParam('format', 'html');
        
        $this->view->data = new stdClass();
        
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
        $TL = MyProject_Model_Database::loadModel('tourenTimelines');
        $TV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $rq = $this->getRequest();
        $format = $rq->getParam('format', 'html');
        $tlid = $rq->getParam('id', 0);
        $tour_id = $rq->getParam('tour_id', 0);
        $tldata = $rq->getParam('data', array());
        
        if (!$tlid && $tour_id) {
            $tourData = $TV->fetchEntry($tour_id);
            $tlid = $tourData['timeline_id'];
        }
        
        if ($tlid) {
            $this->view->ajax_response->data = $TL->fetchEntry($tlid);
        }
        
        if ($format == 'json')
        $this->_helper->viewRenderer->setRender('jsonresponse');
        else
        $this->_helper->viewRenderer->setRender('timelinedata');
    }
    
    public function updateresourcesAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $NAME = Zend_Db_Table::NAME;
        $DMB = MyProject_Model_Database::loadModel('tourenDispoMitarbeiterText');
        
        $rq = $this->getRequest();
        $data = $rq->getParam('rsrc', array());
        
        foreach($data as $k => $_data) {
            list($_rsrcType, $_id) = explode(':', $k);            
            $DMB->replace( array_merge($_data, array('id'=>$_id)));
            echo 'update record with id ' . $_id . '<br>' . PHP_EOL;
        }        
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
        $tlid = $rq->getParam('id', 0);
        $tour_id = $rq->getParam('tour_id', 0);
        $tldata = $rq->getParam('data', array());
        
        if (!$tlid && $tour_id) {
            $tourData = $TV->fetchEntry($tour_id);
            $tlid = $tourData['timeline_id'];
        }
        
        if ($tlid) {
            $TL->update($tldata, $tlid);
        }        
    }
    
    public function updatetimetableAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $NAME = Zend_Db_Table::NAME;
        $TV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', 0);
        $data = $rq->getParam('data', array());
        
        if ($tour_id) {
            if (!$TV->update($data, $tour_id)) {
                $this->view->ajax_response->error = $TV->error();
            }
        }
    }
    
    public function updatepositionenAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblAP = MyProject_Model_Database::loadStorage('auftragspositionen')->info($NAME);
        $tblBK = MyProject_Model_Database::loadStorage('bestellkoepfe')->info($NAME);
        $tblBP = MyProject_Model_Database::loadStorage('bestellpositionen')->info($NAME);
        $tblDP = MyProject_Model_Database::loadStorage('tourenDispoPositionen')->info($NAME);
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_ASSOC);

            $rq = $this->getRequest();
            $tour_id = (int) $rq->getParam('tour_id');
            $mandant = (int) $rq->getParam('Mandant');
            $auftragsnr = (int) $rq->getParam('Auftragsnummer');
            $positionsMengen = $rq->getParam('PositionsMengen');

            $auftragspositionen = array_keys($positionsMengen);

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

            $newPositionen = array_diff($auftragspositionen, $existingPositionen);

            $updateSql = 
                'UPDATE ' . $tblDP . ' '
                .'SET DisponierteMenge = :menge '
                .'WHERE tour_id = '.$db->quote($tour_id).' '
                .'AND Mandant = '.$db->quote($mandant).' '
                .'AND Auftragsnummer = '.$db->quote($auftragsnr).' '
                .'AND Positionsnummer = :posnr';
            
//          echo 'existingPositionen: ' . print_r($existingPositionen, 1) . PHP_EOL;
            foreach($existingPositionen as $_posnr) {                
                $db->query($updateSql, array(
                    ':menge' => $positionsMengen[$_posnr],
                    ':posnr' => $_posnr));
            }
            
            /* @var $modelLogger Model_TourenDispoLog */
            $modelLogger = new Model_TourenDispoLog();
            $modelLogger->logTour($tour_id, 'update positionen');
            
            $insertSql = 
                'INSERT ' . $tblDP . ' '
                .'SET DisponierteMenge = :menge, '
                .'tour_id = '.$db->quote($tour_id).', '
                .'Mandant = '.$db->quote($mandant).', '
                .'Auftragsnummer = '.$db->quote($auftragsnr).', '
                .'Positionsnummer = :posnr';

            foreach($newPositionen as $_posnr) {
                $_menge = 
                $db->query($insertSql, array(
                    ':menge' => $positionsMengen[$_posnr],
                    ':posnr' => $_posnr
                ));
            }
        
        } catch(Exception $e) {
            die( $e->getTraceAsString() );
            $this->view->ajax_response->error = $e->getMessage() . PHP_EOL .$e->getTraceAsString();
        }
    }
    
    public function calendarweekdataAction()
    {
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        
        $rq = $this->getRequest();
        $date = $rq->getParam('date', '');
        $kw   = $rq->getParam('kw', '');
        
        if ($kw && preg_match('/^(?P<YEAR>\d{4})-(?P<KW>\d\d?)$/', $kw, $m)) {
            // Ermittel das Start-Datum (Montag) der ersten KW
            // Kann auch im Vorjahr liegen
            // ISO-Regel, die Woche die den ersten Donnerstag des neuen Jahres enthält gilt als 1. KW
            $yrStartTime = mktime(4,0,0,1,1,$m['YEAR']);
            $yrStartDay = date('w', $yrStartTime);
            $moDiff = 0;
            $moTime = 0;
            
            if      ($yrStartDay == 0)  $moDiff = 1;
            elseif  ($yrStartDay <  5)  $moDiff = $yrStartDay - 1;
            else    $moDiff = 8 - $yrStartDay;
            
            $moTime = $yrStartTime + ($moDiff*(24*3600));
            $kwTime = $moTime + ($m['KW'] * (7 * 24 * 3600));
            
            
            echo '#'.__LINE__ . ' ' . date('Y-m-d w n', $moTime). "<br>\n";
            die( print_r($m,1));
        }
        if ($date && preg_match('/^(?P<Y>\d{4}-(?P<M>\d\d)-(?P<D>\d{2}$/', $date, $m)) {
            $time = mktime(1,0,0,$m['M'],$m['D'],$m['Y']);
            $wDay = date( 'w', $time);
            $moDiff = 0;
            
            $moDiff = ($wDay > 0) ? $wDay - 1 : -6;
            $kwTime = $yrStartTime + ($moDiff*(24*3600));
        }
        
        $dateRange = array(
          'Von' => date('Y-m-d', $kwTime),
          'Bis' >= date('Y-m-d', $kwTime + (7*24*3600))
        );
        die( print_r($m,1));
        
        header( 'X-Log-Z'.__LINE__ . ': ' . __FILE__ );
        
        
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        
        $sqlTouren =
         "SELECT T.*, CONCAT(Auftragsnummer,' ',A.Vorgangstitel,', ',A.LieferungOrt) name, A.LieferterminFix "
        ."FROM " . $tblDV . " T "
        ."LEFT JOIN " . $tblAK . " A USING(Mandant,Auftragsnummer) "
        ."WHERE timeline_id = :timeline_id "
        ."ORDER BY ZeitVon";
        
    }
    
    public function calendardaydataAction() 
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $this->view->ajax_response = new stdClass();
        
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblAP = MyProject_Model_Database::loadStorage('auftragspositionen')->info($NAME);
        $tblBK = MyProject_Model_Database::loadStorage('bestellkoepfe')->info($NAME);
        $tblBP = MyProject_Model_Database::loadStorage('bestellpositionen')->info($NAME);
        $tblDP = MyProject_Model_Database::loadStorage('tourenDispoPositionen')->info($NAME);
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblDF = MyProject_Model_Database::loadStorage('tourenDispoFuhrpark')->info($NAME);
        $tblDM = MyProject_Model_Database::loadStorage('tourenDispoMitarbeiter')->info($NAME);
        $tblDW = MyProject_Model_Database::loadStorage('tourenDispoWerkzeug')->info($NAME);
        $tblTP = MyProject_Model_Database::loadStorage('tourenPortlets')->info($NAME);
        $tblTL = MyProject_Model_Database::loadStorage('tourenTimelines')->info($NAME);
        $tblFP = MyProject_Model_Database::loadStorage('fuhrpark')->info($NAME);
        $tblMA = MyProject_Model_Database::loadStorage('mitarbeiter')->info($NAME);
        $tblWZ = MyProject_Model_Database::loadStorage('werkzeug')->info($NAME);
        
        try {
            header( 'X-Log-Z'.__LINE__ . ': ' . __FILE__ );
            $rq = $this->getRequest();
            $date = $rq->getParam('date', date('Y-m-d'));
            $lager_id = $rq->getParam('lager_id', '1');
            header( 'X-Log-Z'.__LINE__ . ': ' . __FILE__ );
            
            $sqlPortlets = 
                    "SELECT * FROM " . $tblTP . " " //mr_touren_portlets "
                   ."WHERE portlet_id IN ("
                   ."SELECT portlet_id FROM " . $tblDV . " " // `mr_touren_dispo_vorgaenge` "
                   ."WHERE DatumVon = :date"
                   .")"
                   ."ORDER BY position";
            
            $sqlPortlets = 
                    "SELECT * FROM " . $tblTP . " " //mr_touren_portlets "
                   ."WHERE datum = :date AND lager_id = :lager_id "
                   ."ORDER BY position"; 
            
            $sqlTimelines = 
                     "SELECT *, `interval` stepWidth FROM " . $tblTL . " " // mr_touren_timelines "
                    ."WHERE portlet_id = :portlet_id "
                    ."ORDER BY position";
            
            $sqlTouren =
                     "SELECT T.*, CONCAT(Auftragsnummer,' ',A.Vorgangstitel,', ',A.LieferungOrt) name, A.LieferterminFix "
                    ."FROM " . $tblDV . " T " // mr_touren_dispo_vorgaenge T "
                    ."LEFT JOIN $tblAK A USING(Mandant,Auftragsnummer) "
                    ."WHERE timeline_id = :timeline_id "
                    ."ORDER BY ZeitVon";
            
            $sqlRsrc['FP'] = 
                   "SELECT tr.id, 'FP' resourceType, r.*, CONCAT(r.hersteller,' ',r.modell) name "
                  ."FROM $tblDF tr "
                  ."LEFT JOIN $tblFP r ON tr.fuhrpark_id = r.fid "
                  ."WHERE tr.tour_id = :tour_id";
            //die( $sqlRsrc['FP']);
            $sqlRsrc['MA'] = 
                   "SELECT tr.id, 'MA' resourceType, r.*, CONCAT(r.vorname,' ',r.name,' [',r.eingestellt_als,']') name "
                  ."FROM $tblDM tr "
                  ."LEFT JOIN $tblMA r ON tr.mitarbeiter_id = r.mid "
                  ."WHERE tr.tour_id = :tour_id";
            
            $sqlRsrc['WZ'] = 
                   "SELECT tr.id, 'WZ' resourceType, r.*, bezeichnung name "
                  ."FROM $tblDW tr "
                  ."LEFT JOIN $tblWZ r ON tr.werkzeug_id = r.wid "
                  ."WHERE tr.tour_id = :tour_id";

            header( 'X-Log-Z'.__LINE__ . ': ' . __FILE__ );
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_ASSOC);
            
            $data = array();
            $portlets = $db->fetchAll($sqlPortlets, array(':date'=>$date, ':lager_id'=>$lager_id));
            
            
//            echo str_replace( ':date', $db->quote($date), $sqlPortlets) . PHP_EOL;
//            die( '<pre>'.print_r($portlets,1) );
            $this->view->ajax_response->data = &$portlets;
            
            
            foreach($portlets as $pi => $_p) {
                $portlets[$pi]['id'] = $_p['portlet_id'];
                $portlets[$pi]['timelines'] = $db->fetchAll($sqlTimelines, array(':portlet_id'=>$_p['portlet_id']));
                $timelines = &$portlets[$pi]['timelines'];
                
                foreach($timelines as $tli => $_tl) {
                    $timelines[$tli]['id'] = $_tl['timeline_id'];
                    $timelines[$tli]['touren'] = $db->fetchAll($sqlTouren, array(':timeline_id'=>$_tl['timeline_id']));
                    
                    foreach($timelines[$tli]['touren'] as $vi => $_tour) {
                        $timelines[$tli]['touren'][$vi]['id'] = $_tour['tour_id'];
                        $timelines[$tli]['touren'][$vi]['resources'] = array();
                        $resources = &$timelines[$tli]['touren'][$vi]['resources'];
                        
                        foreach($sqlRsrc as $_sql) {
                            $stmt = $db->query( $_sql, array(':tour_id' => $_tour['tour_id']) );
                            while($row = $stmt->fetch()) $resources[] = $row;                                
                        }
                    }
                }
            }
            return;
            
            

            $sql = "SELECT tv.*, tl.position "
                  ."tl_position, tl.start tl_start, tl.end tl_end, "
                  ."tp.position tp_position FROM mr_touren_dispo_vorgaenge tv "
                  ."LEFT JOIN mr_touren_timelines tl USING(timeline_id) "
                  ."LEFT JOIN mr_touren_portlets tp ON tl.portlet_id = tp.portlet_id "
                  ."WHERE DatumVon = :datum "
                  ."ORDER BY tp.position, tl.position, tv.ZeitVon";
            
            $data = $db->fetchAll($sql, array(':datum'=>$date));
            $this->view->ajax_response->data = $db->fetchAll($sql, array(':datum'=>$date));
            $this->view->ajax_response->resources = array();
            $tour_ids = array();
            $rsrcData = array();
            
            foreach($this->view->ajax_response->data as $i => $tour) {
                $tour_ids[] = $tour['tour_id'];
                $this->view->ajax_response->data[$i]['resources'] = array();
                foreach($sqlRsrc as $_sql) {
                    $stmt = $db->query( str_replace(':tour_id', implode(',',$tour_ids), $_sql) );
                    while($row = $stmt->fetch()) 
                        $this->view->ajax_response->data[$i]['resources'][] = $row;
                }
            }
            
            
            
            
            
            
        } catch(Exception $e) {
            $this->view->ajax_response->error = $e->getMessage() . PHP_EOL .$e->getTraceAsString();
        }
        $this->_helper->viewRenderer->setRender('jsonresponse');
        
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
            $data = $rq->getParam('data');
            
            /* @var $model Model_TourenPortlets */
            $model = MyProject_Model_Database::loadModel('tourenPortlets');

            $newID = $model->add($data);
            $this->view->ajax_response->msg = "Methode wurde aufgerufen (Neue ID:$newID): " . __METHOD__ . PHP_EOL . print_r($data,1);
            $this->view->ajax_response->id = $newID;
            $this->view->ajax_response->data = $model->fetchEntry($newID);
        } catch(Exception $e) {
            die($e->getMessage() . PHP_EOL . $e->getTraceAsString());
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
            if (!$toPos)          throw new Exception('Fehlender Paramter pos!');
            
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

            $model->delete($id);
            
            /* @var $modelLogger Model_TourenDispoLog */            
            $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
            $modelLogger->logTourenplan(id, 'remove');
        } catch(Exception $e) {
            $this->view->ajax_response->type = false;
            $this->view->ajax_response->msg.= $e->getMessage() . PHP_EOL;
            $this->view->ajax_response->msg.= $e->getTraceAsString();
        }
    }
    
    public function addtimelineAction()
    {
        try {
//        die(__METHOD__);
            $rq = $this->getRequest();
            $data = $rq->getParam('data');

//        $model = new Model_TourenPortlets();
            $model = MyProject_Model_Database::loadModel('tourenTimelines');

            $newID = $model->add($data);
            $this->view->ajax_response->msg = "Methode wurde aufgerufen (Neue ID:$newID): " . __METHOD__ . PHP_EOL . print_r($data,1);
            $this->view->ajax_response->id = $newID;
        } catch(Exception $e) {
            die($e->getMessage() . PHP_EOL . $e->getTraceAsString());
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

            /* @var $model Model_TourenDispoVorgaenge */
            $model = MyProject_Model_Database::loadModel('tourenTimelines');
    //        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;

            if ( $model->delete($id, $confirm) ) {
                /* @var $modelLogger Model_TourenDispoLog */
                $modelLogger = new Model_TourenDispoLog();
                $modelLogger->logTimeline($id, 'remove');                
            } else {
                $this->view->ajax_response->type = false;
                $this->view->ajax_response->msg.= $model->getError();
                $this->view->ajax_response->askForConfirm = 1;
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
            if (!$toPos)          throw new Exception('Fehlender Paramter pos!');
            
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
    
    public function droprouteAction()
    {
//      die(__METHOD__);
        $rq = $this->getRequest();
        $data = $rq->getParam('data');
        
//        $model = new Model_TourenPortlets();
        $model = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        try {
            if (empty($data['id'])) {                
                $newID = $model->drop($data);
                
                /* @var $model Model_TourenDispoLog */
                $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
                $modelLogger->logTour($newID, 'insert');
                
                $model->addDefaultResources( $newID );
                $this->view->ajax_response->msg = "Methode " . __METHOD__ . " (drop) wurde aufgerufen (Neue ID:$newID): " . __METHOD__ . PHP_EOL . print_r($data,1);
                $this->view->ajax_response->id = $newID;
            }
        } catch(Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }
    }
    
    public function removerouteAction()
    {
//        die(__METHOD__);
        $rq = $this->getRequest();
        $id = $rq->getParam('id', null);
        if (!$id)            throw new Exception('Fehlender Paramter id!');
        
        /* @var $model Model_TourenDispoVorgaenge */
        $model = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
//        $this->view->ajax_response->msg = '#'.__LINE__.print_r(Zend_Registry::get('db')->fetchAll('Select * FROM mr_touren_dispo_vorgaenge tour_id= '.$id),1).PHP_EOL;
        
        /* @var $modelLogger Model_TourenDispoLog */
        $modelLogger = new Model_TourenDispoLog();
        $modelLogger->logTour($id, 'remove'); 
        
        if ( $model->delete($id) ) {           
        } else {
            $this->view->ajax_response->error = "Die Tour mit der id " . $id . " konnte nicht geloescht werde!";
        }
    }
    
    public function moverouteAction()
    {
//        die(__METHOD__);
        $rq = $this->getRequest();
        $data = $rq->getParam('data');
        
//        $model = new Model_TourenDispoVorgaenge();
        $model = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        try {
            if (!empty($data['id'])) {
                $id = $data['id'];
                if ($model->move($data)) {
                    /* @var $model Model_TourenDispoLog */
                    $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
                    $modelLogger->logTour($id, 'moved');
                    $this->view->ajax_response->msg = "Methode " . __METHOD__ . " wurde aufgerufen (ID:$id): " . __METHOD__ . PHP_EOL . print_r($data,1);
                    $this->view->ajax_response->id = $id;
                } else {
                    $this->view->ajax_response->msg = "FEHLER !!! (ID:$id): " . __METHOD__ . PHP_EOL . print_r($data,1);
                    $this->view->ajax_response->msg.= $model->error();
                    $this->view->ajax_response->error = $model->error();
                }
            }
        } catch(Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }
    }
    
    public function resizerouteAction()
    {
//        die(__METHOD__);
        $rq = $this->getRequest();
        $data = $rq->getParam('data');
        
//        $model = new Model_TourenPortlets();
        $model = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        try {
            if (!empty($data['id'])) {
                $id = $data['id'];
                if ($model->resize($data)) {
                    /* @var $modelLogger Model_TourenDispoLog */
                    $modelLogger = new Model_TourenDispoLog();
                    $modelLogger->logTour($id, 'resize');
                    
                    $this->view->ajax_response->msg = "Methode " . __METHOD__ . " wurde aufgerufen (ID:$id): " . __METHOD__ . PHP_EOL . print_r($data,1);
                    $this->view->ajax_response->id = $id;
                } else {
                    $this->view->ajax_response->msg = "FEHLER !!! (ID:$id): " . __METHOD__ . PHP_EOL . print_r($data,1);
                    $this->view->ajax_response->msg.= $model->error();
                    $this->view->ajax_response->error = $model->error();
                }
            }
        } catch(Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            Zend_Controller_Front::getInstance()->getResponse()->setException($e);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
        }
    }
    
    public function dropresourceAction() 
    {
        $rq = $this->getRequest();
        $data = $rq->getParam('data');
        $resourceType = (array_key_exists('resourceType', $data) ? $data['resourceType'] : '');
        $resourceModelClass = (array_key_exists($resourceType, $this->_resourceModels)) ? $this->_resourceModels[$resourceType] : '';
        
        /* @var $modelTour Model_TourenDispoVorgaenge */
        $modelTour = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $tourData = $modelTour->fetchEntry($data['route_id']);
        $data['tour_id'] = $data['route_id'];
         
        $this->view->ajax_response->test[] = __LINE__;
        
        $error = "";
        // Pre-Condition
        if (!$resourceModelClass) {
            $this->view->ajax_response->test[] = __LINE__;
            $this->view->ajax_response->error = "Ungültiger ResourceTyp `$resourceType`. Erwarteter Wert FP, MA oder WZ!";
            return;
        }
        $this->view->ajax_response->test[] = __LINE__;
        
        /* @var $modelRsrc MyProject_Model_TourenResourceInterface */
        $modelRsrc = MyProject_Model_Database::loadModel($resourceModelClass);
        
        /* @var $modelLogger Model_TourenDispoLog */        
        $modelLogger = MyProject_Model_Database::loadModel($this->_modelLoggerName);
        $this->view->ajax_response->test[] = __LINE__;
        
        
        try {
            $newID = $modelRsrc->drop($data);
            if ($tourData['IsDefault']) $modelRsrc->applyDefaults( $modelRsrc->fetchEntry( $newID ) );
            
            $modelLogger = new Model_TourenDispoLog();
            $modelLogger->logResource($resourceType, $newID, 'Drop', $data['tour_id']);
            
            $this->view->ajax_response->msg = "Methode " . __METHOD__ . " (drop) wurde aufgerufen (Neue ID:$newID): " . __METHOD__ . PHP_EOL . print_r($data,1);
            $this->view->ajax_response->id = $newID;
        } catch (Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
            if (APPLICATION_ENV == 'development')
            $this->view->ajax_response->error.= "\n".$e->getTraceAsString();
            
            $this->view->ajax_response->error.= print_r(Zend_Registry::get('db')->getProfiler()->getQueryProfiles(),1);
        }
        $this->view->ajax_response->test[] = __LINE__;
    }
    
    public function removeresourceAction()
    {
        
        $rq = $this->getRequest();
        $id = $rq->getParam('id', null);
        $resourceType = $rq->getParam('resourceType', null);
        $resourceModelClass = (array_key_exists($resourceType, $this->_resourceModels)) ? $this->_resourceModels[$resourceType] : '';
        
        $this->view->ajax_response->test[] = __LINE__;
        
        $error = "";
        
        // Pre-Conditions
        if (!$id || !$resourceType) {
            $this->view->ajax_response->error = "Zu wenig Paramerter`. Erwartete Wert sind id und resourceType: FP, MA oder WZ!";
            return;
        }        
        if ( !$resourceModelClass ) {
            $this->view->ajax_response->error = "Ungültiger ResourceTyp `$resourceType`. Erwarteter Wert FP, MA oder WZ!";
            return;
        }
        
        /* @var $modelRsrc MyProject_Model_TourenResourceInterface */
        $modelRsrc = MyProject_Model_Database::loadModel( $resourceModelClass );
        $data = $modelRsrc->fetchEntry($id);
        
        if ( !$data ) {
            $this->view->ajax_response->error = "Es wurde kein Eintrag (" . $resourceType . ") mit der id " . $id . " gefunden!";
            return;
        }
        
        $this->view->ajax_response->test[] = __LINE__;
        
        $this->view->ajax_response->test[] = __LINE__;
        
        try {
            if ( $modelRsrc->delete($id) ) {
                /* @var $modelLogger Model_TourenDispoLog */            
                $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
                $modelLogger->logResource($resourceType, $id, 'removed', $data['tour_id']);

                $this->view->ajax_response->msg = "Methode " . __METHOD__ . " (remove ".$resourceType.' ID '.$id.") wurde aufgerufen!";
            } else {
                $this->view->ajax_response->error = "Eintrag (" . $resourceType . ") mit der id " . $id . " konnte nicht geloescht werde!";
            }
        } catch (Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
            if (APPLICATION_ENV == 'development')
            $this->view->ajax_response->error.= "\n".$e->getTraceAsString();
            
            $this->view->ajax_response->error.= print_r(Zend_Registry::get('db')->getProfiler()->getQueryProfiles(),1);
        }
        $this->view->ajax_response->test[] = __LINE__; 
    }
    
    public function removeresourcedefaultAction()
    {
        
        $rq = $this->getRequest();
        $id = $rq->getParam('id', null);
        $resourceType = $rq->getParam('resourceType', null);
        
        $resourceModelClass = (array_key_exists($resourceType, $this->_resourceModels)) ? $this->_resourceModels[$resourceType] : '';
        $this->view->ajax_response->test[] = __LINE__;
        
        $error = "";
        
        // Pre-Conditions
        if (!$id || !$resourceType) {
            $this->view->ajax_response->error = "Zu wenig Paramerter`. Erwartete Wert sind id und resourceType: FP, MA oder WZ!";
            return;
        }        
        if ( !$resourceModelClass ) {
            $this->view->ajax_response->error = "Ungültiger ResourceTyp `$resourceType`. Erwarteter Wert FP, MA oder WZ!";
            return;
        }
        
        /* @var $modelRsrc MyProject_Model_TourenResourceInterface */
        $modelRsrc = MyProject_Model_Database::loadModel( $resourceModelClass );
        $data = $modelRsrc->fetchEntry($id);
        
        if ( !$data ) {
            $this->view->ajax_response->error = "Es wurde kein Eintrag (" . $resourceType . ") mit der id " . $id . " gefunden!";
            return;
        }
        
        $this->view->ajax_response->test[] = __LINE__;
        
        /* @var $modelRsrc Model_TourenDispoFuhrpark */
        $modelRsrc = MyProject_Model_Database::loadModel( $resourceModelClass );
        $this->view->ajax_response->test[] = __LINE__;
        
        try {
            if ( $modelRsrc->removeDefault($id) ) {
                /* @var $modelLogger Model_TourenDispoLog */            
                $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
                $modelLogger->logResource($resourceType, $id, 'removed-default', $data['tour_id']);

                $this->view->ajax_response->msg = "Methode " . __METHOD__ . " (remove ".$resourceType.' ID '.$id.") wurde aufgerufen!";
            } else {
                $this->view->ajax_response->error = "Eintrag (" . $resourceType . ") mit der id " . $id . " konnte nicht geloescht werden!";
            }
        } catch (Exception $e) {
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(400);
            $this->view->ajax_response->error = '#' . $e->getLine() . ' ' . $e->getFile() . PHP_EOL . $e->getMessage();
            if (APPLICATION_ENV == 'development')
            $this->view->ajax_response->error.= "\n".$e->getTraceAsString();
            
            $this->view->ajax_response->error.= print_r(Zend_Registry::get('db')->getProfiler()->getQueryProfiles(),1);
        }
        $this->view->ajax_response->test[] = __LINE__; 
    }
    
    public function vorgangsdatenAction()
    {   
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        $mandant = $rq->getParam('mandant', null);
        $auftragsnr = $rq->getParam('auftragsnummer', null);
        $format = $rq->getParam('format', 'html');
        $this->view->vorgangsdaten = new stdClass();
        $this->view->vorgangsdaten->error = '';
        $this->view->vorgangsdaten->msg = '';
        $this->view->vorgangsdaten->data = array();
        
        switch($format) {
            case 'html-base':
                $this->_helper->viewRenderer->setRender('vorgangsdaten-base');
                break;
            
            default:
                $this->_helper->viewRenderer->setRender('vorgangsdaten');
        }
        
        
        try {
            if ($tour_id && (!$mandant || $auftragsnr)) {
                /* @var $tourModel Model_TourenDispoVorgaenge */
                $tourModel = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
                $storage = $tourModel->getStorage();

                $select = $db->select();
                $select->from( $storage->info(Zend_Db_Table::NAME), array('Mandant', 'Auftragsnummer') );
                $select->where('tour_id = ?', $tour_id);
                
                $rows = $db->fetchAll($select);
                //echo $select->assemble();
                $mandant = $rows[0]['Mandant'];
                $auftragsnr = $rows[0]['Auftragsnummer'];
            }
            if (!$mandant ||!$auftragsnr)
                $this->view->vorgangsdaten->error = 'Mandant und Auftragsnummer konnten nicht ermittelt werden!';

//            echo 'mandant: '.$mandant.'; anr:'.$auftragsnr;
//
            $model = MyProject_Model_Database::loadModel('vorgaenge');
//            /* @var $model Zend_Db_Table */
            $storage = $model->getStorage();
            $select = $storage->select()->where('Mandant = ?', $mandant)->where('Auftragsnummer = ?', $auftragsnr);
//
//            die ( print_r($db->fetchRow($select),1));
            $row = $db->fetchRow($select);
            $this->view->vorgangsdaten->data = $row;
        } catch(Exception $e) {
            die( $select->assemble());
            die( '<pre>' . $e->getMessage(). PHP_EOL . $e->getTraceAsString() . PHP_EOL . print_r($storage->info(),1) );
            print_r($storage->info());
        }
    }
    
    
    public function vorgangsdatendefaultsAction()
    {   
        $this->_helper->viewRenderer->setRender('vorgangsdatendefaults');
    }
    
    public function vorgangsbemerkungenAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $rq = $this->getRequest();
        $tour_id = $rq->getParam('tour_id', null);
        $bemerkung = $rq->getParam('bemerkung', null);        
        
        $action = $this->view->baseUrl() . '/'.$rq->getModuleName().'/'.$rq->getControllerName().'/'.$rq->getActionName();
        
        $this->view->vorgangsbemerkungen = new stdClass();
        $this->view->vorgangsbemerkungen->error = '';
        $this->view->vorgangsbemerkungen->msg = '';
        $this->view->vorgangsbemerkungen->data = array();
        $this->view->vorgangsbemerkungen->action = $action;
        $this->_helper->viewRenderer->setRender('vorgangsbemerkungen');
        
        try {
            if ($tour_id) {
                /* @var $tourModel Model_TourenDispoVorgaenge */
                $tourModel = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
                $storage = $tourModel->getStorage();
                
                $tourModelTxt = MyProject_Model_Database::loadModel('tourenDispoVorgaengeText');
                /* @var $storageTxt Zend_Db_Table */                
                $storageTxt = $tourModelTxt->getStorage();

                $select = $db->select();
                $select->from( array('T'=>$storage->info(Zend_Db_Table::NAME)), 'tour_id' );
                $select->joinLeft(
                        array( 'TX' => $storageTxt->info(Zend_Db_Table::NAME)), 
                        'T.tour_id = TX.tour_id',
                        array('bemerkung', new Zend_Db_Expr('TX.tour_id txtid') ) );
                $select->where('T.tour_id = ?', $tour_id);
                
                $row = $db->fetchRow($select);
                
                if ($row && $bemerkung) {
                    $uname = MyProject_Auth_Adapter::getUserName();
                    
                    $entry = '<div class="entry">'
                            .'<div class="bemerkung-meta"><span class="user">'.$uname . '</span>, '
                            .'<span class="datetime">' . date("Y-m-d H:i:s") . '</span></div>'
                            .'<div class="bemerkung">' . $bemerkung . '</div>'
                            .'</div>';
                    
                    $data = array(
                        "tour_id" => $row["tour_id"],
                        "bemerkung" => $row['bemerkung'] . PHP_EOL . $entry
                    );
                    
                    if (!$row['txtid']) {
                        $tourModelTxt->insert($data);
                    } else {
                        $tourModelTxt->update($data, $row['txtid']);
                    }
                    $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');
                    $modelLogger->logTour($tour_id, 'bemerkung');
                    
                    $row = $db->fetchRow($select);
                    $this->_helper->viewRenderer->setRender('vorgangsbemerkungen-saved');
                }
            }
            if (!$row)
                $this->view->vorgangsbemerkungen->error = 'Toureintrag mit der Id '.$tour_id.' konnte nicht ermittelt werden!';
            
//            die ( print_r($db->fetchRow($select),1));
            $row = $db->fetchRow($select);
            $this->view->vorgangsbemerkungen->data = $row;
        } catch(Exception $e) {
            die( '<pre>' 
                    . $e->getMessage(). PHP_EOL 
                    . $e->getTraceAsString() . PHP_EOL 
                    . print_r($storage->info(),1) . PHP_EOL 
                    . $select->assemble() . PHP_EOL
                    . print_r($storage->info(), 1) );
            ;
        }
    }
    
    public function vorgangskopfdatenAction()
    {
        
    }
}
