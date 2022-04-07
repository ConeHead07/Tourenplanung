<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Plugin_Test extends Zend_Controller_Plugin_Abstract
{
//    protected $_debug = true;
    protected $_debug = false;
    
    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        parent::routeStartup($request);
        if ($this->_debug) {
            echo '#' . __LINE__ . ' ' .__METHOD__ . '<br>' . "\n";
        
            echo implode(', ', array(
                $request->getModuleKey(),
                $request->getModuleName(),
                $request->getControllerKey(),
                $request->getControllerName(),
                $request->getActionKey(),
                $request->getActionName()
            )) . '<br>' . "\n";
        }
    }
    
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        parent::routeShutdown($request);
        if ($this->_debug) {
            echo '#' . __LINE__ . ' ' .__METHOD__ . '<br>' . "\n";
        
            echo implode(', ', array(
                $request->getModuleKey(),
                $request->getModuleName(),
                $request->getControllerKey(),
                $request->getControllerName(),
                $request->getActionKey(),
                $request->getActionName()
            )) . '<br>' . "\n";
        }
    }
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        parent::dispatchLoopStartup($request);
        if ($this->_debug) {
            echo '#' . __LINE__ . ' ' .__METHOD__ . '<br>' . "\n";
        
            echo implode(', ', array(
                $request->getModuleKey(),
                $request->getModuleName(),
                $request->getControllerKey(),
                $request->getControllerName(),
                $request->getActionKey(),
                $request->getActionName()
            )) . '<br>' . "\n";
        }
    }
    
    public function dispatchLoopShutdown() {
        parent::dispatchLoopShutdown();
        if ($this->_debug) {
            echo '#' . __LINE__ . ' ' .__METHOD__ . '<br>' . "\n";
            echo "Alle Anfragen abgearbeit, kein Request-Objekt mehr vorhanden!";
            die();
        }
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        if ($this->_debug) {
            echo '#' . __LINE__ . ' ' .__METHOD__ . '<br>' . "\n";
            echo implode(', ', array(
                $request->getModuleKey(),
                $request->getModuleName(),
                $request->getControllerKey(),
                $request->getControllerName(),
                $request->getActionKey(),
                $request->getActionName()
            )) . '<br>' . "\n";
            
            if ($request->getControllerName()=='error') {
                echo '<pre>' . PHP_EOL;
                $errors = Zend_Controller_Front::getInstance()->getRequest()->getParam('error_handler');
//                $errors = $this->_getParam('error_handler');
                foreach($errors as $k => $v) {
                    echo 'error->'.$k . ' = ' . print_r($v,1) . PHP_EOL;
                }
                throw new Exception(__METHOD__ . ' Page not Found!');
            }
        }
    }
    
    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        parent::postDispatch($request);
        if ($this->_debug) {
            echo '#' . __LINE__ . ' ' .__METHOD__ . '<br>' . "\n";
        
            echo implode(', ', array(
                $request->getModuleKey(),
                $request->getModuleName(),
                $request->getControllerKey(),
                $request->getControllerName(),
                $request->getActionKey(),
                $request->getActionName()
            )) . '<br>' . "\n";
        }
    }
    
}
?>
