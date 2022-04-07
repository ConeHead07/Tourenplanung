<?php

/**
 * Description of NavController
 * @author rybka
 */
class NavController extends Zend_Controller_Action {
    //put your code here
    public function indexAction()
    {
        /**
         * @var MyProject_Acl
         */
        $acl = Zend_Registry::get( 'acl' );
//        $acl = new Zend_Acl();
        echo 'acl->isAllowed( user, index):' . (string)$acl->isAllowed( 'user', 'index');
        $this->view->nav = array( 
            'Start' => $this->view->url(
                    array(
                        'controller' => 'index',
                        'action' => 'index'
                    ),
                    null, true
                    ),
                    'Dummie-Modul'=> $this->view->url(
                    array(
                        'module' => 'dummie',
                        'controller' => 'index',
                        'action' => 'index'
                    ),
                    null, true
                    ));
    }
}

?>
