<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of vorgaengeController
 * @author rybka
 */
class VorgaengeController extends Zend_Controller_Action 
{
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
                
    }
    
    //put your code here
    public function indexAction() {
        $db = Zend_Registry::get('db');
        $model = MyProject_Model_Database::loadModel('vorgaenge');
        $this->view->vorgaengelist = $model->fetchEntries(array('count'=>10, 'offset'=>15));
        
        /* @var $storage Model_Db_vorgaenge */
        $storage = $model->getStorage();
        $this->view->tblCols = $storage->info(); //'cols');
    }
    
    public function sidebarAction() 
    {
        $this->_helper->viewRenderer->setResponseSegment('sidebar');
        $this->view->sidebar = array(
            'openOnload' => '13',
            'x' => 14, 
            'y' => 15, 
            'z' => 16);    
    }
    
    public function insertpoolAction()
    {
        $rq = $this->getRequest();        
        $this->view->params = $rq->getParams();
        
        $tourDatum = $rq->getParam('DatumVon', date('Y-m-d')); // 2013-03-11
        $tourTime = strtotime($tourDatum);
        
        $this->view->tourNodeId = $rq->getParam('tourNodeId', ''); // 2013-03-11
        $this->view->data = (object)array(
            'Mandant' => 110,
            'Auftragsnummer' => '',
            'Vorgangstitel' => '',
            'LieferungName' => '',
            'AnsprechpartnerNachnameLief' => '',
            'LieferungStrassePostfach' => '',
            'LieferungPostleitzahl' => '',
            'LieferungOrt' => '',
            'LieferungLand' => 'D',
            'Lieferwoche' => date('W', $tourTime),
            'Lieferjahr' => date('y', $tourTime),
            'Liefertermin' => date('Y-m-d', $tourTime),
            'LieferterminFix' => '0',
            'LieferterminHinweisText' => '',
        );        
        $this->view->mandantList = array(
            '110' => 'Pool merTens'
        );
        
        $this->view->format = $rq->getParam( 'format', '');
        $this->view->layout = (int)$rq->getParam( 'layout', 1);
        if ($this->view->format == 'partial' || !$this->view->layout) {
            $this->getHelper( 'layout' )->disableLayout();
        } else {
            $this->_helper->actionStack('sidebar');
        }
        $this->view->wwsRefItems = array();        
        
        $this->_helper->viewRenderer('editpool');
    }
    
    public function editpoolAction()
    {
        $rq = $this->getRequest();
        
        $this->error = '';
        $this->view->data = array();
        $this->view->wwsRefItems = array();
        
        $this->view->mandantList = array(
            '110' => 'Pool merTens'
        );
        
        $this->view->format = $rq->getParam( 'format', '');
        $this->view->layout = (int)$rq->getParam( 'layout', 1);
        if ($this->view->format == 'partial' || !$this->view->layout) 
            $this->getHelper( 'layout' )->disableLayout();
        
        $mandant = $rq->getParam('Mandant', 110);
        $anr = $rq->getParam('id');
        if (!$anr) $rq->getParam('Auftragsnummer');
        if (!$anr || !$mandant) {
            $this->view->error = 'Fehlende ID!';
            return;
        }        
        
        $model = new Model_Vorgaenge();
        try {
            $this->view->data = (object)$model->fetchEntry($mandant, $anr);
            //die (print_r($this->view->data));

            $this->view->wwsRefItems = $model->getWwsRefItems($mandant, $anr);
        } catch(Exception $e) {
            die(
                Zend_Debug::dump($e->getMessage(), 'Exception-Message', FALSE) 
                .PHP_EOL
                .Zend_Debug::dump($e->getTraceAsString(), 'Exception-Trace', FALSE) 
                .PHP_EOL
                .Zend_Debug::dump($model->getStorage()->info(Zend_Db_Table::PRIMARY), 'PrimaryKeys', FALSE)   
            );
        }        
    }
    
    public function savepoolAction()
    {
        $rq = $this->getRequest();
        $auftragsnr = $rq->getParam('Auftragsnummer', '');
        $mandant = (int)$rq->getParam('Mandant', '');
        
        $data = $this->getRequest()->getParams();
        $data['AngelegtAm'] = new Zend_Db_Expr('NOW()');
        $data['Lieferjahr'] = substr($data['Liefertermin'], 2, 2);

        $model = new Model_Vorgaenge();
        $modelDA = new Model_TourenDispoAuftraege();
        
        $msg = '';
        $err = '';
        $id = '';

        try {
            if (!$auftragsnr) {
                $data['Kundennummer'] = (int)$data['Kundennummer'];
                if ($id = $model->insert(array_merge($data, array('Bearbeitungsstatus' => 2)))) {
                    $modelDA->importAuftrag($id['Mandant'], $id['Auftragsnummer']);
                    $msg = 'Vorgang wurde gespeichert';
                    $mandant = $id['Mandant'];
                    $auftragsnr = $id['Auftragsnummer'];
                } else {
                    $err = 'Fehler!';
                }
            } elseif ($mandant) {
                if ($model->update($data, array($mandant, $auftragsnr))) {
                    $msg = 'Vorgang wurde gespeichert';
                } else {
                    $err = 'Fehler!';
                }
            }
        } catch(Exception $e) {
            $err = "Poolvorgang konnte nicht gespeichert werden.\n";
            $err.= $e->getMessage();
        }
        
        $this->_helper->json(array(
            'type'    => (!$err) ? 'success' : 'error',
            'success' => empty($err),
            'error'   => $err,
            'message' => $msg,
            'Mandant' => $mandant,
            'Auftragsnummer' => $auftragsnr,
            'id'      => $id,
            'data'    => ($mandant && $auftragsnr) ? $model->fetchEntry( $mandant, $auftragsnr ) : null,
        ));
    }

    public function listAction() {
    }

    public function subgridlistAction() {
    }
    public function subgridlist2Action() {
    }
    public function subgridlisttest01Action() {
    }
    

    public function datalistAction() {
        /* @var $model Model_vorgaenge */
        $model = MyProject_Model_Database::loadModel('vorgaenge');
        $this->view->datalist = $model->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function selectmandantdialogAction()
    {
        $this->view->SelectTreeId = $this->getRequest()->getParam('treeID', null);
    }
    
    public function selectmandantlistAction()
    {
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        $rootId = $rq->getParam('root', '');
        
        $model = MyProject_Model_Database::loadModel('vorgaenge');
        $this->view->mandanten = $model->getMandanten();
    }
    
    
    public function selectgbdialogAction()
    {
        $this->view->SelectTreeId = $this->getRequest()->getParam('treeID', null);
    }
    
    public function selectgblistAction()
    {
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        $rootId = $rq->getParam('root', '');
        
        $model = MyProject_Model_Database::loadModel('vorgaenge');
        $this->view->gblist = $model->getGeschaeftsbereiche();
        
//        die( print_r($this->view->gblist, 1));
    }
    
    
    public function grideditdataAction()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $model Model_Vorgaenge */
        $model = MyProject_Model_Database::loadModel('vorgaenge');
        
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
        
        /* @var $storage Model_Db_vorgaenge */
        $storage = $model->getStorage();
        
        print_r($_REQUEST);
        Zend_Layout::getMvcInstance()->disableLayout();
        exit;
    }

    public function gridresponsedataAction() 
    {
        /* @var $model Model_Vorgaenge */
        $modelVorgaenge = new Model_Vorgaenge(array('useFilter'=>false)); // MyProject_Model_Database::loadModel('vorgaenge');
        $modelVorgaenge->useDispoFilter(false);
        
        $response   = new stdClass();
        $request    = $this->getRequest();
        
        $mandantid  = (int) $request->getParam('mandant', ''); // default: 10
        $date       = $request->getParam('date', 1);
        $dateTo     = $request->getParam('dateTo', '');
        $dateKwOnly = $request->getParam('dateKwOnly', '>=');
        $page       = (int) $request->getParam('page', 1);
        $limit      = (int) $request->getParam('rows', 100);
        $sidx       = $request->getParam('sidx', null);
        $sord       = $request->getParam('sord', 'ASC');
        $filters    = $request->getParam('filters', '');
        
        if ( $filters ) {
            $request->setParam('filters', utf8_encode($filters) );
            $filters = $request->getParam('filters', '');
        }
        
        $view       = $request->getParam('view', '');
        
        $dispoStatus = $request->getParam('dispoStatus', '');
        $dispoStatusWV = $request->getParam('dispoStatusWV', '');
        
        $vorgaenge = $modelVorgaenge->query(
                $filters,
                array(
                    'sidx' => $sidx,
                    'sord' => $sord,
                ),
                array(
                    'page' => $page,
                    'rows' => $limit,
                ),
                array(
                    'mandant' => $mandantid,
                    'date'    => $date,
                    'dateTo'  => $dateTo,
                    'dateKwOnly'    => $dateKwOnly,
                    'dispoStatus'   => $dispoStatus,
                    'dispoStatusWV' => $dispoStatusWV,
                    'view'    => $view,
                )
        );
        $response->sql    = $vorgaenge->sql;
        $response->page    = $vorgaenge->page;
        $response->total   = $vorgaenge->total_pages;
        $response->records = $vorgaenge->total;
        $response->rows    = $vorgaenge->result->fetchAll(Zend_Db::FETCH_ASSOC);
        
        $this->view->gridresponsedata = $response;
        
        header('X-Debug-PartBySearch:  ' . json_encode(utf8_encode($vorgaenge->sqlFilterByDate)));
        header('X-Debug-SearchRequest: ' . json_encode($_REQUEST));
        header('X-Debug-Vorgaenge-Tbl: ' . json_encode($vorgaenge->modelName));
        header('X-Debug-SQL: ' . json_encode( utf8_encode($vorgaenge->sql) ) );
    }

    public function findvorgaengeAction() 
    {
        /* @var $model Model_Vorgaenge */
        $modelVorgaenge = new Model_Vorgaenge(); // MyProject_Model_Database::loadModel('vorgaenge');
        $modelVorgaenge->useDispoFilter(false);
        
        $response   = new stdClass();
        $request    = $this->getRequest();
        
        $page       = (int) $request->getParam('page', 1);
        $limit      = (int) $request->getParam('rows', 100);
        $sidx       = $request->getParam('sidx', null);
        $sord       = $request->getParam('sord', 'ASC');
        $filters    = $request->getParam('filters', '');
        $view       = $request->getParam('view', '');
        $mandant    = $request->getParam('Mandant', '');
//        die('#'.__LINE__ . ' ' . __METHOD__ . ' filter: ' . $filters);
        
        if ($filters && is_string($filters)) {
            $filters = json_decode($filters);
        }
        
        if ($mandant) {
            if (!$filters) $filters = (object)array(
                'groupOp' => 'AND',
                'rules'   => array(),
            );
            
            $filters->rules[] = (object)array(
                'field' => 'Mandant',
                'op'    => 'eq',
                'data'  => (int)$mandant,
            );
        }
        
        $vorgaenge = $modelVorgaenge->searchTable(
            $filters,
            array(
                'sidx' => $sidx,
                'sord' => $sord,
            ),
            array(
                'page' => $page,
                'rows' => $limit,
            )
        );        
        
        $response->page            = $vorgaenge->page;
        $response->total           = $vorgaenge->total_pages;
        $response->records         = $vorgaenge->total;
        $response->rows            = $vorgaenge->rows;
        
        header('X-Debug-SearchRequest: ' . json_encode($_REQUEST));
        header('X-Debug-SQL: ' . json_encode( utf8_encode($vorgaenge->sql) ) );
        
        $this->_helper->json($response);
    }
    
    public function reloadfilterAction()
    {
        /* @var $model Model_VorgaengeDispoFilter */
        $model = MyProject_Model_Database::loadModel('vorgaengeDispoFilter');
        $model->reloadData();
        
        /* @var $model Model_VorgaengeDispoFilter */
        $modelBDF = MyProject_Model_Database::loadModel('bestellkoepfeDispoFilter');
        $modelBDF->reloadData();
    }
    
    public function reloadmetastatAction()
    {
        $modelDA = new Model_TourenDispoAuftraege();
        $modelDA->refreshAllToursDispoCount();
        $modelDA->refreshAllToursFinishCount();
    }
    

    public function autocompleteabholungenAction()
    {
        $rq = $this->getRequest();
        /* @var $db Zend_Db_Adapter_Sqlsrv */
        //$db = Zend_Registry::get('wwsdb');

        /* @var $db MyProject_Db_Sqlsrv */
        $sqlsrvdb = Zend_Registry::get('sqlsrvdb');
        
        $term  = $rq->getParam('term', '');
        $field = $rq->getParam('field', 'name');
        
        $validFields = array(
            'anschriftsnummer' => 'A.Anschriftsnummer',
            'name' => 'A.Name',
            'strasse' => 'A.Strasse',
            'plz'  => 'A.Postleitzahl',
            'ort'  => 'A.Ort',
            'pf'   => 'A.OrtPostfach',
            'land' => 'A.Land'
        );
        
        if (!isset($validFields[$field])) {
            $this->getResponse()->setHttpResponseCode(404);
            die("Ungueltige Feldauswahl'$field': gueltige Felder sind: ". implode(', ', array_keys($validFields) ));
        }
        $rows = array();
        
        $error = '';
        //die('#' . __LINE__ . ' ' . __METHOD__ . Zend_Debug::dump($db, 'wwsdb', true);
        try {
            
            $sql = 'SELECT TOP 15 '
                  .' A.Anschriftsnummer, A.Name, A.Strasse, A.Postleitzahl, A.Ort, A.OrtPostfach, A.Land, '
                  .'MIN(K.IstLieferant) AS IstLieferant '
                  .'FROM Anschriften AS A INNER JOIN Kontakte AS K ON K.IstLieferant = 1 AND K.HauptAnschrift = A.Anschriftsnummer '
                  .'WHERE ' . $field . ' LIKE :term '
                  .'GROUP BY A.Anschriftsnummer, A.Name, A.Strasse, A.Postleitzahl, A.Ort, A.OrtPostfach, A.Land';
            $TimeIn = time();
            $sqlsrvdb->setFetchMode(Sqlsrv::FETCH_ASSOC);
            $sqlsrvdb->setScrollableCursor(SQLSRV_CURSOR_STATIC);
            $rows = $sqlsrvdb->fetchAll($sql, array('term' => "$term%") );
            
            $TimeDur = time() - $TimeIn;
            foreach($rows as $i => $row) {
                foreach($row as $k => $v) $rows[$i][$k] = $v;
                $rows[$i]['time'] = $TimeDur . 's';
                $rows[$i]['value'] = $rows[$i]['Name'];
                $rows[$i]['label'] = $rows[$i]['Name'] 
                                   . ', ' . $rows[$i]['Strasse']
                                   . ' ,' . $rows[$i]['Postleitzahl']
                                   . ' '  . $rows[$i]['Ort']
                                   . ', PF ' . $rows[$i]['OrtPostfach']
                                   . ', ' . $rows[$i]['Land'];
            }
            if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . ' <pre>rows: ' . print_r($rows,1) . '</pre>' . PHP_EOL;
            
            $this->_helper->json($rows);
            
        } catch(Zend_Db_Exception $e) {
            $error = '#' . __LINE__ . ' ' . utf8_encode($e->getMessage()) . '<br/>' . PHP_EOL;
            echo '#' . __LINE__ . ' ' . (string)$e->getMessage() . '<br/>' . PHP_EOL;
            echo '#' . __LINE__ . ' ' . $e->getTraceAsString() . '<br/>' . PHP_EOL;
        } catch(Exception $e) {
            $error = '#' . __LINE__ . ' ' . utf8_encode($e->getMessage()) . '<br/>' . PHP_EOL;
            echo '#' . __LINE__ . ' ' . $e->getTraceAsString() . '<br/>' . PHP_EOL;
        }
        
        $this->_helper->json(array(
            'type'    => 'error',
            'success' => false,
            'error'   => $error . ' <pre>sql: ' . (string)$sql . '</pre>'
        ));
    }
}
