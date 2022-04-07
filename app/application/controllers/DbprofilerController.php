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
        //return;
        $this->view->dbprofiler = '';
        $db = Zend_Registry::get( 'db' );
        $profiler = new Zend_Db_Profiler();

        $this->view->dbprofiler.= '<div style="margin-top:1rem;margin-botttom:.5rem;color:#ccc">Db-Profiler is ' . ($profiler->getEnabled() ? 'enabled' : 'disabled'). "<br>\n";

        if (Zend_Registry::get('dbprofiler_enabled') )  {
            $profiler = $db->getProfiler();
            if ($profiler->getQueryProfiles()) {
                foreach($profiler->getQueryProfiles() as $query) {
                    $this->view->dbprofiler.= $query->getQuery() . ' [' . $query->getElapsedSecs(). "<br>\n";
                }
            }
        }
        $this->view->dbprofiler.= "</div>\n";
    }
}

