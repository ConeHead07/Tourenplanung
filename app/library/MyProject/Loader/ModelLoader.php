<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModelLoader
 *
 * @author rybka
 */
class MyProject_Loader_ModelLoader extends Zend_Loader_PluginLoader 
{
    //put your code here
    public function addModule($module = 'default')
    {
        if ('default' == $module) {
            $path   = APPLICATION_PATH . '/models';
            $prefix = 'Model_';
        } else {
            $path  = APPLICATION_PATH . '/modules/';
            $path .= $module . '/models';
            $prefix= ucfirst($module) . '_Model_';
        }
        if (!in_array($prefix, array_keys($this->getPaths() ))) {
//            echo '#'.__LINE__ . ' ' . __METHOD__ . ' prefix=' . $prefix . ' path=' . $path . PHP_EOL;
            $this->addPrefixPath($prefix, $path);
        }
    }
}
