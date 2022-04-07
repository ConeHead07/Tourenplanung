<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AjaxController extends Zend_Controller_Action
{
   public function init() {
       parent::init();
       $this->_request = $this->getRequest();
   }

   public function addportletAction()
    {
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        $data = $rq->getParams();
        $this->view->ajax_response = new stdClass();
        $this->view->ajax_response->msg = "Methode wurde aufgerufen: " . __METHOD__ . PHP_EOL . print_r($data,1);
        $this->render("json_response", "ajax");
    }
}

