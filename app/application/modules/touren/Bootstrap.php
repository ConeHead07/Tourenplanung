<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Touren_Bootstrap extends Zend_Application_Module_Bootstrap
{
    

    protected function _initLayout()
    {
    }
    
    public function activeInitLayout()
    {
        $Layout = Zend_Layout::getMvcInstance();
        $Layout->setLayoutPath(APPLICATION_PATH . '/modules/touren/layouts/scripts/');
        $Layout->setLayout('layout');

        $view = $Layout->getView();
        $view->headTitle('Der neue Title der Seite', 'SET');
        
//        $view->setHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper');
        $view->setHelperPath(APPLICATION_PATH . '/modules/touren/views/helpers', 'Touren_View_Helper');
    }
}

