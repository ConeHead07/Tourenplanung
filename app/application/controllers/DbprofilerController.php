<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DbprofilerController
 *
 * @author rybka
 */
class DbprofilerController extends Zend_Controller_Action {
    //put your code here
    
    public function indexAction()
    {
        $this->view->dbprofiler = '';
        $db = Zend_Registry::get( 'db' );
        $profiler = $db->getProfiler();
        $this->view->dbprofiler .= '<div>APPLICATION_ENV: ' . APPLICATION_ENV . '</div>' . "\n";

        if ( $profiler->getEnabled() )  {
            if ($profiler->getQueryProfiles()) {
                foreach($profiler->getQueryProfiles() as $query) {
                    $_sql = $query->getQuery();
                    if (!preg_match('#user_pw|MD5\(#', $_sql)) {
                        $this->view->dbprofiler .= $_sql . ' [' . $query->getElapsedSecs() . "<br>\n";
                    } else {
                        $this->view->dbprofiler .= substr($_sql, 0, 25) . '... [' . $query->getElapsedSecs() . "<br>\n";
                    }
                }
            }
        }
    }
}

