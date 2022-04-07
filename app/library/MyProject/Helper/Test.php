<?php

/**
 * Description of Test
 *
 * @author rybka
 */
class MyProject_Helper_Test extends Zend_Controller_Action_Helper_Abstract 
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
    
    public function getTest($param)
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
        return $this->getTest($param);
    }
}

?>
