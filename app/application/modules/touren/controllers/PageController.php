<?php

class Touren_PageController extends Zend_Controller_Action
{
    protected $_resourceModels = array(
        "FP" => 'tourenDispoFuhrpark',
        "MA" => 'tourenDispoMitarbeiter',
        "WZ" => 'tourenDispoWerkzeug'
    );
        
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }

    public function indexAction()
    {
        // action body
    }
    
    public function printportletAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $portlet_id = $this->getRequest()->getParam('id', '');
        if (!$portlet_id) return null;
        
        /* @var $modelP Model_TourenPortlets */
        $modelP = MyProject_Model_Database::loadModel('tourenPortlets');
        /* @var $storageP Model_Db_TourenPortlets */
        $storageP = MyProject_Model_Database::loadStorage('tourenPortlets');
        
        /* @var $storageL Model_Db_Lager */
        $storageL = MyProject_Model_Database::loadStorage('lager');
        
        $this->view->data = new stdClass();
        
        if ($portlet_id) {
            $portlet = $storageP->fetchRow('portlet_id = ' . $db->quote($portlet_id, Zend_Db::INT_TYPE))->toArray();
            if (!$portlet) return null;
            
            $this->view->data->portlet = &$portlet;
            
            $this->view->data->lager = $storageL->fetchRow('lager_id = ' . $portlet['lager_id'])->toArray();
            $timelines = $modelP->getTimelines($portlet['portlet_id']);
            
            if (is_array($timelines) && count($timelines) ) {
                $this->view->data->timelines = &$timelines;
                
                /* @var $storageT Model_Db_TourenTimelines */
                $storageT = MyProject_Model_Database::loadStorage('tourenTimelines');
                /* @var $modelT Model_TourenTimelines */
                $modelT   = MyProject_Model_Database::loadModel('tourenTimelines');                
                
                /* @var $modelDV Model_TourenDispoVorgaenge */
                $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');           
                
                /* @var $modelDV Model_TourenDispoVorgaengeText */
                $modelDVB = MyProject_Model_Database::loadModel('tourenDispoVorgaengeText');
                
                /* @var $modelDP Model_TourenDispoPositionen */
                // $modelDP = MyProject_Model_Database::loadModel('tourenDispoPositionen');
                
                /* @var Model_TourenDispoAttachments */
                $modelAtt = new Model_TourenDispoAttachments();
                
                $baseUrl = $this->getFrontController()->getBaseUrl();
                
                foreach($timelines as $_i => $_tl) {
                    $vorgaenge = $modelT->getDispoVorgaenge($_tl['timeline_id'], false);
                    $timelines[$_i]['vorgaenge'] = $vorgaenge;
                    foreach($vorgaenge as $_j => $_dv) {
                        $timelines[$_i]['vorgaenge'][$_j]['bemerkungen'] = $modelDVB->getPrintBemerkungen($_dv['tour_id']);
                        $timelines[$_i]['vorgaenge'][$_j]['resources']   = $modelDV->getResources($_dv['tour_id']);
//                        $timelines[$_i]['vorgaenge'][$_j]['positionen']  = $modelDV->getBestellungen($_dv['tour_id'], 'own');
                        $timelines[$_i]['vorgaenge'][$_j]['positionen']  = []; // $modelDP->getPositionen($_dv['tour_id'], 'own');
                        $timelines[$_i]['vorgaenge'][$_j]['plaene']  = $modelAtt->getTableList($_dv['tour_id'], array(
                            'ofld' => 'created', 
                            'odir' => 'ASC',
                            'removable' => false,
                            'sortable' => false,
                            'filepath' => $baseUrl . '/touren/attachments/file/tour_id/' . $_dv['tour_id'],
                        ));
//                        echo '<pre>' . print_r($timelines[$_i]['vorgaenge'][$_j]['positionen'], 1) . '</pre>';
                    }
                }
            }
            
        }
    }
    
    public function printportletsdayAction()
    {
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $lager_id = $this->getRequest()->getParam('lager_id', '');
        $date = $this->getRequest()->getParam('date', date('Y-m-d'));
        
        if (!$date || !$lager_id) return null;
        
        /* @var $modelP Model_TourenPortlets */
        $modelP = MyProject_Model_Database::loadModel('tourenPortlets');
        
        /* @var $storageP Model_Db_TourenPortlets */
        $storageP = MyProject_Model_Database::loadStorage('tourenPortlets');
        
        /* @var $storageL Model_Db_Lager */
        $storageL = MyProject_Model_Database::loadStorage('lager');
        
        $this->view->data = new stdClass();
        $this->view->data->portlets = array();

        $portlets = $storageP->fetchAll(
                    'datum = '.$db->quote($date)
                   .' AND lager_id = ' . $db->quote($lager_id), 
                    'position')->toArray();
        if (!$portlets) return null;
        
            $timelines = array();
            foreach($portlets as $_pi => $portlet) {
//                $portlet = $storageP->fetchRow('portlet_id = ' . $db->quote($portlet_id, Zend_Db::INT_TYPE))->toArray();
                

                $this->view->data->portlets[$_pi] = new stdClass();
                $this->view->data->portlets[$_pi]->data = new stdClass();
                $this->view->data->portlets[$_pi]->data->portlet = $portlet;

                $this->view->data->portlets[$_pi]->data->lager = $storageL->fetchRow('lager_id = ' . $portlet['lager_id'])->toArray();
                $timelines[$_pi] = $modelP->getTimelines($portlet['portlet_id']);

                if (is_array($timelines[$_pi]) && count($timelines[$_pi]) ) {
                    $this->view->data->portlets[$_pi]->data->timelines = &$timelines[$_pi];

                    /* @var $storageT Model_Db_TourenTimelines */
                    $storageT = MyProject_Model_Database::loadStorage('tourenTimelines');
                    /* @var $modelT Model_TourenTimelines */
                    $modelT   = MyProject_Model_Database::loadModel('tourenTimelines');                

                    /* @var $modelDV Model_TourenDispoVorgaenge */
                    $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');           

                    /* @var $modelDV Model_TourenDispoVorgaengeText */
                    $modelDVB = MyProject_Model_Database::loadModel('tourenDispoVorgaengeText');

                    /* @var $modelDP Model_TourenDispoPositionen */
                    // $modelDP = MyProject_Model_Database::loadModel('tourenDispoPositionen');
                    
                    try {
                        foreach($timelines[$_pi] as $_i => $_tl) {
                            $timelines[$_pi][$_i]['vorgaenge'] = $modelT->getDispoVorgaenge($_tl['timeline_id'], false);
                            foreach($timelines[$_pi][$_i]['vorgaenge'] as $_j => $_dv) {
                                $timelines[$_pi][$_i]['vorgaenge'][$_j]['bemerkungen'] = $modelDVB->getPrintBemerkungen($_dv['tour_id']);
                                $timelines[$_pi][$_i]['vorgaenge'][$_j]['resources']   = $modelDV->getResources($_dv['tour_id']);
                                $timelines[$_pi][$_i]['vorgaenge'][$_j]['positionen']  = []; // $modelDP->getPositionen($_dv['tour_id'], 'own');
//                                $timelines[$_pi][$_i]['vorgaenge'][$_j]['positionen']  = $modelDV->getBestellungen($_dv['tour_id'], 'own');
                            }
                        }
                    } catch(Exception $e) {
                        $error = $e->getCode() . ' <br>' . PHP_EOL
                               . $e->getFile() . ' <br>' . PHP_EOL
                               . $e->getLine() . ' <br>' . PHP_EOL
                               . $e->getMessage() . ' <br>' . PHP_EOL
                               . $e->getTraceAsString() . ' <br>' . PHP_EOL;
                        die( $error );
                    }
                }
            }
//            die(print_r($this->view->data));
        
    }
}

