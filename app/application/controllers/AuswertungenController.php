<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuswertungenController
 *
 * @author frankb
 */
class AuswertungenController extends Zend_Controller_Action {
    //put your code here
    
    public function init() {
        
    }
    
    public function indexAction() {
        $this->_forward('auftragssummen');
    }
    
    public function auftragssummenAction() {
        $this->view->month = $this->getRequest()->getParam('monat', '');
        $this->view->sidx  = $this->getRequest()->getParam('sidx',  '');
        $this->view->sord  = $this->getRequest()->getParam('sord',  '');
        $this->view->gridUrl = $this->view->baseUrl() . '/touren/data/auswertung_auftragssummen.jqgrid.js';
    }
    
    public function auftragssummendataAction() {
        $monat = $this->getRequest()->getParam('monat', '');
        $orderby = $this->getRequest()->getParam('sidx', '');
        $orderdir= $this->getRequest()->getParam('sord', '');
        if ($orderby && $orderdir) $orderby.= ' ' . $orderdir;
        $model = new Model_TourenDispoVorgaenge;
        
        $response = (object)array('page'=>1,'total'=>1,'records'=>0,'rows'=>array());
        
        $response->rows = $model->auftragssummenByAbgeschlossenAm($monat, $orderby);
        $response->records = count($response->rows);
        
        $this->_helper->json($response);
    }
}
