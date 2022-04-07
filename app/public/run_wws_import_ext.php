<?php
set_time_limit(900);

require(dirname(__FILE__) . '/../../mertens_rm_sysdef.php');
if (!defined('WHOAMI')) define('WHOAMI', 'undefined');


// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
//echo APPLICATION_PATH . PHP_EOL;

// Define application environment
define('APPLICATION_ENV', (WHOAMI == 'mt-10.10.1.23' ? 'production' : 'development'));
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

//echo get_include_path();
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
/* @var $autoloader Zend_Loader_Autoloader */
$autoloader = Zend_Loader_Autoloader::getInstance();

require_once 'MyProject/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->pushAutoloader(new MyProject_Loader_Autoloader());

class Webf_Controller_Router_Cli 
extends Zend_Controller_Router_Abstract 
implements Zend_Controller_Router_Interface {
    public function assemble($userParams, $name = null, $reset = false, $encode = true) { }
    public function route(Zend_Controller_Request_Abstract $dispatcher) {}
}
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

//        echo Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
$application->bootstrap(array('db'));  // array('db', 'acl')

$WwsImporter = new MyProject_Model_WwsFilterImport;
echo '#' . __LINE__ . ' import Auftragskoepfe<br/>' . PHP_EOL;
$WwsImporter->import_auftragskoepfe();
echo '#' . __LINE__ . ' import Bestellkoepfe<br/>' . PHP_EOL;
$WwsImporter->import_bestellkoepfe();
echo '#' . __LINE__ . ' import Auftragspositionen<br/>' . PHP_EOL;
$WwsImporter->import_auftragspositionen();
echo '#' . __LINE__ . ' import Bestellpositionen<br/>' . PHP_EOL;
$WwsImporter->import_bestellpositionen();
ob_flush();
die('Import abgeschlossen!');



