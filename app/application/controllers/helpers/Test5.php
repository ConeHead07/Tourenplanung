<?php

/**
 *   Action-Helper-Vzs./-Prefix registrieren
 *   Danach können beliebig Action-Helper in den Verzeichnissen
 *   angelegt und in ActionController via $this->_helper->test5
 *   aufgerufen werden.
 *  
 *   protected function _initMyActionHelper()
 *   {
 *       // Setup dieses the Custom Helpers test5 in Standard-Vzs
 *       Zend_Controller_Action_HelperBroker::addPath(
 *           APPLICATION_PATH . '/controllers/helpers',
 *           'Helper');
 *
 *       // Alternative: Setup in eigenem Projekt-Vzs
 *       Zend_Controller_Action_HelperBroker::addPrefix('MyProject_Helper');
 *   }
 * 
 *   ActionHelper können auch explizit geladen und hinzugefügt werden.
 *   Bietet sich z.B. in der init-Methode eines Controllers an
 *   public function init()
 *   {
 *      $helper = new Helper_Test5();
 *      Zend_Controller_Action_HelperBroker::addHelper($helper);
 *      
 *      Und er kann auch wieder entfernt werden
 *      if (Zend_Controller_Action_HelperBroker::hasHelper('test5')) {
 *          Zend_Controller_Action_HelperBroker::removeHelper('test5');
 *      }
 *   }
 * 
 *   Aufrufmöglichkeiten im Action-Controller
 *   public function anyAction()
 *   {
 *      if (Zend_Controller_Action_HelperBroker::hasHelper('test5')) {
 *         $redirector =
 *       Zend_Controller_Action_HelperBroker::getExistingHelper('test5');
 *      }
 *      
 *      ODER 
 *      
 *      $helper = $this->_helper->getHelper('test5');
 * 
 * 
 * 
 *   }
 */
class Helper_Test5 extends Zend_Controller_Action_Helper_Abstract 
{
 
    /**
     * Constructor: initialize plugin loader
     *
     * @return void
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }
    
    public function getTest5($param)
    {
        return __METHOD__ . ' ' .$param;
    }
    
    /**
     * Strategy pattern: call helper as broker method
     *
     * @param  string $param
     * @return string
     */
    public function direct($param)
    {
        return $this->getTest5($param);
    }
}

?>
