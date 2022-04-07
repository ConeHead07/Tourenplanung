<?php

class Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
//        $view = new Zend_View();
        $view = Zend_Layout::getMvcInstance()->getView(); 
        $view->addHelperPath('MyProject/View/Helper/', 'MyProject_View_Helper');
        
        Zend_Registry::set('myViewHelper', $view);
        
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->init();

        // set up variables that the view may want to know
        $viewRenderer->view->module = $request->getModuleName();
        $viewRenderer->view->controller = $request->getControllerName();
        $viewRenderer->view->action = $request->getActionName();
        $viewRenderer->view->user = MyProject_Auth_Adapter::getIdentity();
    }
}


