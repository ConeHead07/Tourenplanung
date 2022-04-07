<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ConfigController extends Zend_Controller_Action
{
    protected $showSidebar = false;
    
    public function init() {
        parent::init();
        $this->_rq = $this->getRequest();

        $this->view->format = $this->_rq->getParam( 'format', '');
        $this->view->layout = (int)$this->_rq->getParam( 'layout', 1);

        if ($this->view->format == 'partial' || !$this->view->layout) {
            $this->getHelper( 'layout' )->disableLayout();
        } else {
            $this->showSidebar = true;
        }
    }
    
    public function sidebarAction() 
    {
        $role = MyProject_Auth_Adapter::getUserRole();
        $this->_helper->viewRenderer->setResponseSegment('sidebar');
        $this->view->sidebar = array('openOnload' => true);
        $this->view->items = array(
            array(
                'label' => 'Benutzer',
                'link'  => APPLICATION_BASE_URL . '/user/list'
            ),
            array(
                'label' => 'Grosskunden',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=configs.grosskunden'
            ),
            array(
                'label' => 'Abschlussmengen',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=configs.abschlussmengen'
            ),
            array(
                'label' => 'FP Suchfelder',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=modules.touren.configs.fuhrpark_suchfelder'
            ),
            array(
                'label' => 'MA Suchfelder',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=modules.touren.configs.mitarbeiter_suchfelder'
            ),
            array(
                'label' => 'Pool Suchfelder',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=modules.touren.configs.poolvorgaenge_suchfelder'
            ),
            array(
                'label' => 'Vorg. Suchfelder',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=modules.touren.configs.vorgaenge_suchfelder'
            ),
            array(
                'label' => 'WZ Suchfelder',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=modules.touren.configs.werkzeug_suchfelder'
            )
        );
        
        if ('admin' === $role) {
            $this->view->items[] = array(
                'label' => 'AccessControl',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=configs.acl'
            );
            $this->view->items[] = array(
                'label' => 'Application',
                'link'  => APPLICATION_BASE_URL . '/config/load/?confid=configs.application'
            );
        }
    }

   public function loadAction()
   {
        /** @var Zend_Controller_Request_Http */       
        $rq = $this->_rq;
        
        if ($this->showSidebar) {
            $this->_helper->actionStack('sidebar');
        }
        
        $this->view->params = $rq->getParams();
        $this->view->formTitle = '';
        
        $confid = $rq->getParam( 'confid', '');
        $confFile = APPLICATION_PATH . '/'.str_replace('.', '/', $confid) . '.ini';
//        die ( 'confFile: ' . $confFile );
        if (file_exists( $confFile )) {
            $this->view->config = file_get_contents( $confFile );
            $this->view->formTitle = 'Konfiguration ' . ucfirst($confid);
        }

        $this->view->confid = $confid;
        $this->view->configFile = $confFile;
        $this->view->configModified = filemtime($confFile);
        $this->view->save = APPLICATION_BASE_URL . '/config/save/';

   }
   
   public function saveAction()
   {
        $rq = $this->_rq;

        $this->view->params = $rq->getParams();
        $this->view->response = new stdClass();
        $this->view->response->config = '';
        $this->view->response->configModified = '';
        $this->view->response->type = 'error';
        
        $confid = $rq->getParam( 'confid', '');
        $config = $rq->getParam( 'config', '');
        $confFile = APPLICATION_PATH . '/'.str_replace('.', '/', $confid) . '.ini';
        
        if (file_exists( $confFile )) {
            // Create Backup
            $pathBkup = APPLICATION_PATH . '/configs_backup/';
            $nameBkup = array_slice(explode('.', $confid), 0, -1);
            $tpl = APPLICATION_PATH . '/configs_backup/'.str_replace('.', '/', $confid) . '.' . date('Ymd') . '-%d.ini';
            $d = 0;
            foreach($nameBkup as $i => $_p) {
                $_dir = $pathBkup . implode('/', array_slice($nameBkup, 0, $i+1) ) ;
                if (!file_exists( $_dir ))  {
                    mkdir($_dir);
                } elseif (!is_dir($_dir)) {
                    $tpl = APPLICATION_PATH . '/configs_backup/' . $confid . '.' . date('Ymd') . '-%d.ini';
                    break;
                }                        
            }
            while(file_exists( str_replace('%d', ++$d, $tpl)));            
            $confBkup = str_replace('%d', $d, $tpl);
            file_put_contents( $confBkup, file_get_contents($confFile) );
            file_put_contents( $confFile, $config );
            
            // Aenderung loggen
            $confLog = APPLICATION_PATH . '/log/configs_edit/'.$confid . '-'.date('Ymd').'.ini';
            
            $log = array(
                date('Y-m-d H:i:s '),
                MyProject_Auth_Adapter::getUserId(),
                MyProject_Auth_Adapter::getUserName(),
                'Letzter Stand: ' . $confBkup,
                
            );
            
            // Aenderung speichern
            file_put_contents($confLog, implode('\t', $log), FILE_APPEND );
            
            $this->view->response->type   = 'success';            
            $this->view->response->config = file_get_contents( $confFile );
            $this->view->response->configFile = $confFile ; 
            $this->view->response->configModified = filemtime( $confFile );  
        } else {
            $this->view->response->error= 'Datei ' . $confFile . ' wurde nicht gefunden!';
        }        
        
        $this->_helper->json($this->view->response);
   }
   
   public function userAction()
   {
       
        if ($this->showSidebar) {
            $this->_helper->actionStack('sidebar');
        }
       
       $action = 'list';
       $controller = 'user';
       $module = 'default';
       $params = null;
       $this->_forward($action, $controller, $module, $params);
       
   }
   
   
   
   public function indexAction()
   {
       $action = 'load';
       $controller = 'config';
       $module = 'default';
       $params = array('confid' => 'grosskunden');
       $this->_forward($action, $controller, $module, $params);
//       die('#'. __LINE__ . ' ' . __METHOD__ );
   }
}
