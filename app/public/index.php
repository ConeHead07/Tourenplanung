<?php
set_time_limit(90);

if (file_exists( dirname(__FILE__) . '/../etc/sysdef.local.php')) {
    require(dirname(__FILE__) . '/../etc/sysdef.local.php');
} else {
    require(dirname(__FILE__) . '/../etc/sysdef.php');
}
if (!defined('WHOAMI')) define('WHOAMI', 'undefined');

function date_de($format, $time) {
    if (preg_match('/\d{4}-\d{2}-\d{2}/', $time)) $time = strtotime($time);
    if (!preg_match('/D|l|F/', $format)) return date( $format, $time);

    static $D = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');
    static $l = array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag');
    static $F = array('', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober','November', 'Dezember');
    static $M = array('', 'Jan', 'Feb', 'Mrz', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez');

    $format = strtr( $format, array('D'=>'&', 'l'=>'§', 'F'=>'$', 'M'=>'?'));

    list($w, $n) = explode('-', date('w-n', $time));

    return strtr( date( $format, $time), array(
        '&' => $D[$w],
        '§' => $l[$w],
        '$' => $F[$n],
        '?' => $M[$n]
    ));
}
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define public url to application
defined('APPLICATION_BASE_URL')
        || define('APPLICATION_BASE_URL', '/mertens_rm/public');
//echo APPLICATION_PATH . PHP_EOL;

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (WHOAMI == 'mt-10.10.1.23' ? 'production' : 'development'));
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

//echo get_include_path();
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../library/JqGrid'),
    realpath(APPLICATION_PATH),
    get_include_path(),
)));
//echo print_r(get_include_path()) . PHP_EOL;


require_once 'Zend/Loader/Autoloader.php';
/* @var $autoloader Zend_Loader_Autoloader */
$autoloader = Zend_Loader_Autoloader::getInstance();

require_once 'MyProject/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->pushAutoloader(new MyProject_Loader_Autoloader());

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

//        echo Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
$application->bootstrap();  // array('db', 'acl')

//        echo Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
$application->run();
//echo Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();

//print_r(get_included_files());