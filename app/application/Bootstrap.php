<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap 
{
    protected function _initCache()
    {
        $frontendOptions = array(
            'lifetime' => 7200, // Lebensdauer des Caches 2 Stunden
            'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/cache/' // Verzeichnis, in welches die Cache Dateien kommen
        );

        // $backend = 'File';
        $backend = new MyProject_Cache_Backend_Apcu();

        // Ein Zend_Cache_Core Objekt erzeugen
        $cache = Zend_Cache::factory(
            'Core',
            $backend,
            $frontendOptions,
            $backendOptions);

        Zend_Registry::set('cache', $cache);
    }

    protected function _initBaseUrl() 
    {
        if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
        $front = Zend_Controller_Front::getInstance()->setBaseUrl(APPLICATION_BASE_URL);
        if (defined('APPLICATION_URL')) return;
        
        $protocol = current(explode('/', $_SERVER["SERVER_PROTOCOL"]));
        $protocol.= empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? "s" : "";        
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
        define('APPLICATION_URL', $protocol."://".$_SERVER['SERVER_NAME'].$port.$front->getBaseUrl() );       
    }

    protected function _initDoctype()
    {
        if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    protected function _initTimeline()
    {
        if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
        $t = $this->getOption( 'timeline');
        Zend_Registry::set('timeline', $t);
    }
    
    protected function _initDb() 
    {
        if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
        // Zuerst muss der Cache vorbereitet werden
        $frontendOptions = array(
            'automatic_serialization' => true
            );
        
        // cachedir ist noch nicht offiziell festgelegt !!!
        $backendOptions  = array(
            'cache_dir'                => APPLICATION_PATH . '/cache'
        );

        $backend = 'File';
        // $backend = new MyProject_Cache_Backend_Apcu();
        $metaDataCache = Zend_Cache::factory('Core',
            $backend,
            $frontendOptions,
            $backendOptions);

        // Als naechstes, den Cache setzen der mit allen Tabellenobjekten verwendet werden soll
        Zend_Db_Table_Abstract::setDefaultMetadataCache($metaDataCache);

        
        $r = $this->getOption( 'resources');
        $rdb = $r['db'];
        /* @var $db Zend_Db_Adapter_Mysqli */
        $db = Zend_Db::factory($rdb['adapter'], array(
            'host' => $rdb['params']['host'],
            'port' => $rdb['params']['port'],
            'dbname' => $rdb['params']['dbname'],
            'username' => $rdb['params']['username'],
            'password' => $rdb['params']['password'],
            'charset' => $rdb['params']['charset'],
            'profiler' => isset($rdb['profiler']) ? $rdb['profiler'] : null,
        ));
        
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
        
        if ( 
            isset($rdb['profiler']['enabled'])
            && $rdb['profiler']['enabled'] 
        ) {
            $db->getProfiler()->setEnabled(true);
        }
        Zend_Registry::set('dbprofiler_enabled', $db->getProfiler()->getEnabled());
    }


    protected function connectWWSDbByIniConf($optionKey, $dbKey)
    {
        if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
        //Zend_Registry::set('wwsdb', Zend_Registry::set('db') );
        //return;

        // optionKey = 'wws'
        // dbKey = 'db'
        $r = $this->getOption( $optionKey);
        $rdb = $r[$dbKey];

        if ($rdb['enabled']) {
            try {
                if (0) { // Funktioniert, aber sehr lahm !!! ??
                    /* @var $db = new Zend_Db_Adapter_Mysqli */
                    $db = Zend_Db::factory($rdb['adapter'], array(
                        'host' => $rdb['params']['host'],
                        'dbname' => $rdb['params']['dbname'],
                        'username' => $rdb['params']['username'],
                        'password' => $rdb['params']['password']
                    )); //$rdb); // ['adapter'], $rdb['params']);
                }

                /* @var $db = MyProject_Db_Sqlsrv */
                return MyProject_Db_Sqlsrv::factory( array(
                    'host' => $rdb['params']['host'],
                    'dbname' => $rdb['params']['dbname'],
                    'username' => $rdb['params']['username'],
                    'password' => $rdb['params']['password']
                ) );
            } catch(Exception $e) {
                die("Cannot connect to WWS-DB: " . $e->getMessage() );
            }
        }
    }

    
    protected function _initWwsDb() 
    {
        // Option- und DB-Key in application.ini
        $optionKey = 'wws';
        $dbKey = 'db';

        // Connect
        $sqlsrvdb = $this->connectWWSDbByIniConf($optionKey, $dbKey);

        // Save Connenction-Adaptor in to Registry
        Zend_Registry::set('sqlsrvdb', $sqlsrvdb);
        Zend_Registry::set('wwsdb', $sqlsrvdb);
        return;


        if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
        //Zend_Registry::set('wwsdb', Zend_Registry::set('db') );
        //return;
        $r = $this->getOption( 'wws');
        $rdb = $r['db'];
        
        // defaults
        $sqlsrvdb = null;
        $db = null;
        
        if ($rdb['enabled']) {        
            try {
                if (0) { // Funktioniert, aber sehr lahm !!! ??
                    /* @var $db = new Zend_Db_Adapter_Mysqli */
                    $db = Zend_Db::factory($rdb['adapter'], array(
                            'host' => $rdb['params']['host'],
                          'dbname' => $rdb['params']['dbname'],
                        'username' => $rdb['params']['username'],
                        'password' => $rdb['params']['password']
                    )); //$rdb); // ['adapter'], $rdb['params']);
                }

                /* @var $db = MyProject_Db_Sqlsrv */
                $sqlsrvdb = MyProject_Db_Sqlsrv::factory( array(
                        'host' => $rdb['params']['host'],
                      'dbname' => $rdb['params']['dbname'],
                    'username' => $rdb['params']['username'],
                    'password' => $rdb['params']['password']
                ) );
            } catch(Exception $e) {
                die("Cannot connect to WWS-DB: " . $e->getMessage() );
            }
        }

        Zend_Registry::set('sqlsrvdb', $sqlsrvdb);
        Zend_Registry::set('wwsdb', $db);
    }



    protected function _initWwsDb2()
    {
        // Option- und DB-Key in application.ini
        $optionKey = 'wws';
        $dbKey = 'db2';

        // Connect
        $sqlsrvdb = $this->connectWWSDbByIniConf($optionKey, $dbKey);

        // Save Connenction-Adaptor in to Registry
        Zend_Registry::set('sqlsrvdb2', $sqlsrvdb);
        Zend_Registry::set('wwsdb2', $sqlsrvdb);
        return;
    }
    
    protected function _initWwsDispoFilter() 
    {
        return;
    }
    
    protected function _initLager() 
    {
        if (0) if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
        try {
            $modelLg = new Model_Lager();
            Zend_Registry::set('lager', $modelLg->getList() );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        if (0) echo '#' . __LINE__ . ' ' . __METHOD__ . '<br/>';
    }
    
    protected function _initAcl() 
    {
        $cacheId = __CLASS__ . '___' . __FUNCTION__;
        $cache = Zend_Registry::get('cache');

        if (!$acl = $cache->load('acl')) {
            require_once 'MyProject/Acl.php';
            $acl = new MyProject_Acl();
            $cache->save($acl, $cacheId, [], 600);
        }
        Zend_Registry::set( 'acl', $acl );
    }
    
    protected function _initLog()
    {
        $fblogger = new Zend_Log_Writer_Firebug();
        $txlogger = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/log/'.date('Ymd').'.txt', 'a+');

        $log = new Zend_Log($txlogger);
        $log->addWriter($fblogger);
        Zend_Registry::set('log', $log);
    }
    
    protected function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Plugin', APPLICATION_PATH . '/controllers/plugins');
        
        if (0) {
            $pluginTest = $loader->load('Test');
            $front->registerPlugin(new $pluginTest());
        }
        
        $pluginAuth = $loader->load('Auth');
        $front->registerPlugin(new $pluginAuth());
        
        $pluginAuth = $loader->load('ActiveInitModule');
        $front->registerPlugin(new $pluginAuth());
        
        $pluginViewSetup = $loader->load('ViewSetup');
        $front->registerPlugin(new $pluginViewSetup());
        
        
    }
    
    protected function _initMyActionHelper()
    {
        /**
        * Setup the Custom Helpers
        */
        Zend_Controller_Action_HelperBroker::addPrefix('MyProject_Helper');
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/controllers/helpers',
            'Helper');
    }
    
    protected function _initMyViewHelper()
    {
//        Zend_Debug::dump(Zend_Registry::get('myViewHelper'));
    }
    
    protected function _initUserProfile()
    {        
        $identity = MyProject_Auth_Adapter::getIdentity();
//        $modelProfile = MyProject_Model_Database::loadModel('userProfile');
        
        if (!is_object($identity) || !property_exists($identity, 'user_id')) {
            Zend_Registry::set('user', null);
            Zend_Registry::set('userProfile', null);
            return;
        }
        $modelProfile = new Model_UserProfile();
        $profile = $modelProfile->getProfile($identity->user_id);
        
        Zend_Registry::set('user', $identity);
        Zend_Registry::set('userProfile', $profile);
    }
    
    protected function _initJQuery()
    {
        $view = new Zend_View();
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        $view->jQuery()->setLocalPath($view->baseUrl().'/js/jquery-1.5.1.js');
        $view->jQuery()->setUiLocalPath($view->baseUrl().'/ui/jquery-ui-1.8.12.custom.js');
        $view->jQuery()->addStylesheet($view->baseUrl().'/themes/jq_base/jquery.ui.all.css');
        
        // Wenn jquery-bibliotheken bereits statisch im Html-Header definiert sind,
        // kann durch die Renderflags vorgegeben werden welche Angaben geerendert werden sollen.
        // In diesem Fall werden nur die gesetzten Flags berücksichtigt
        // Per Default, wird alles gerendert => ZendX_JQuery::RENDER_ALL
        $view->jQuery()->setRenderMode(ZendX_JQuery::RENDER_JAVASCRIPT |
                         ZendX_JQuery::RENDER_JQUERY_ON_LOAD);
        $view->jQuery()->enable();
        $view->jQUery()->uiEnable();
        
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        
        
        $view2 = new Zend_View();
        $view2->addHelperPath('MyProject/View/Helper/', 'MyProject_View_Helper');
        $viewRenderer2 = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer2->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

    protected function _initGitBranch()
    {
        $GitDir = __DIR__ . '/../.git/';
        $GitHeadFile = $GitDir . 'HEAD';
        $GitOrigHeadFile = $GitDir . '/ORIG_HEAD';


        $branchName = str_replace('ref: refs/heads/', '/', file_exists($GitHeadFile)
            ? file_get_contents($GitHeadFile)
            : ''
        );

        $GitCommitFile = trim($GitDir . 'refs/heads' . $branchName);
        $GitBranchCommit = file_exists($GitCommitFile)
            ? file_get_contents($GitCommitFile)
            : "";

        Zend_Registry::set('BRANCH_NAME', $branchName);

        Zend_Registry::set('BRANCH_COMMIT', $GitBranchCommit );
        Zend_Registry::set('BRANCH_COMMIT_SHORT', substr($GitBranchCommit, 0, 7) );
    }


    protected function _initDbInfo()
    {
        $r = $this->getOption( 'resources');
        $rdb = $r['db'];

        Zend_Registry::set('DB_HOST', $rdb['params']['host']);
        Zend_Registry::set('DB_PORT', $rdb['params']['port']);
        Zend_Registry::set('DB_NAME', $rdb['params']['dbname']);
    }
}

