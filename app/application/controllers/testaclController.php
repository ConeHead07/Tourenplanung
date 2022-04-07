<?php

class TestaclController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $a = 'allowed';
        $d = 'denied';
        /* @var $acl MyProject_Acl */
        $acl = Zend_Registry::get( 'acl' );
        
        $roles = $acl->getRoles();
        $resources = $acl->getResources();
        
        foreach ($roles as $ro) {
            foreach($resources as $re) {
                echo $ro . '.' . $re . ' => ' . ($acl->isAllowed($ro, $re) ? $a : $d) . "<br>\n";
            }
        }
        
        $l = array(
            array('allow', 'guest', 'treeview', 'all'),
            array('allow', 'user', 'treeview', 'all'),
            array('allow', 'guest', 'user', 'all'),
            array('allow', 'user', 'user', 'all'),
            array('deny', 'dispo', 'user', 'all'),
            array('deny', 'admin', 'user', 'all'),
            array('allow', 'user', 'index', 'all'),
            array('allow', 'guest', 'index', 'all'),
            array('allow', 'user', 'dummie', 'all'),
            array('allow', 'guest', 'vorgaenge', 'all')
        );
        
        foreach($l as $i) {
            echo $i[0] . ' : ' . $i[1] . '.' . $i[2] . ' => ' . ($acl->isAllowed($i[1], $i[2]) ? $a : $d) . "<br>\n";
        }
        
        /* @var $acl Zend_Acl */
        $acl = new Zend_Acl();
        
        $resourceAutos = new Zend_Acl_Resource( 'Autos' );
        $roleFahrer = new Zend_Acl_Role( 'fahrer' );
        
        $acl->addResource( $resourceAutos );
        $acl->addResource( 'Bikes' );
        $acl->addRole(  $roleFahrer );
        $acl->addRole( 'gast'  );
        $acl->addRole( 'admin' );
        
        $acl->deny( 'gast', $resourceAutos);
        $acl->deny( $roleFahrer );
        
        echo $acl->isAllowed('gast', $resourceAutos, 'fahren') ? 'allowed' : 'denied';
        echo "<br>\n";
        echo $acl->isAllowed( $roleFahrer, $resourceAutos, 'fahren') ? 'allowed' : 'denied';
        echo "<br>\n";
        echo 'hi ...' . date('H:i:s');
        die();
    }
}
