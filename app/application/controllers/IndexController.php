<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_redirect('/touren/index');
    }

    public function indexAction()
    {
        $role = MyProject_Auth_Adapter::getUserRole();
        if (Zend_Registry::get('acl')->isAllowed($role, 'touen', 'index')) {
            $this->_forward('index', 'index', 'touren');
            return;
        }
        die('#'.__LINE__ . ' ' . __METHOD__);
        // action 
        //echo $this->baseUrl();
        echo Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
    }
    
    public function jqexampleAction()
    {
//        $this->_helper->layout->setLayout( 'layoutfoo' );        
    }

}

