<?php

class Touren_IndexController extends Zend_Controller_Action
{
    /* @var $rq Zend_Controller_Request_Http */
    protected $_rq = null;
    public function init() {
        $this->_rq = Zend_Controller_Front::getInstance()->getRequest();
    }

    public function indexAction()
    {
        /* @var $this->rq Touren_IndexController */        
        $this->_rq = $this->getRequest();
        $date  = $this->_rq->getParam('date', '');
        $lager = $this->_rq->getParam('lager_id', 0);
        
        $userProfile = Zend_Registry::get('userProfile');
        
        if (!$lager)
            $lager = (is_object($userProfile) && property_exists($userProfile, 'standort')) ? $userProfile->standort : '';
        
        if (!preg_match(':^\d{4}-\d{2}-\d{2}$:', $date)) $date = date('Y-m-d');
        
        $this->view->date = $date;
        
        list($y,$m,$d) = explode('-', $date);
        $this->view->date2 = date('Y-m-d', mktime(0,0,0,$m,$d,$y) + 86400 ); 
        
        $this->getResponse()->append(
            'sidebar', 
            $this->view->action('sidebar','index','touren'));
        
        $modelLg = MyProject_Model_Database::loadModel('lager');
        $this->view->lagerData = $modelLg->getList();
        $this->view->lagerHtmlOptions = $modelLg->getHtmlOptions($lager);
    }
    
    public function sidebarAction() 
    {        
    }

}

