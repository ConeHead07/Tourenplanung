<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author rybka
 */
abstract class MyProject_Model_Abstract implements MyProject_Model_Interface
{
    //put your code here
    protected static $_pluginLoader;
    protected $_storage = null;
    protected $_storageName = null;
    
    /**
     *
     * @return MyProject_Loader_ModelLoader
     */
    static public function getLoader()
    {
        if (null === self::$_pluginLoader) {
            self::$_pluginLoader = new MyProject_Loader_ModelLoader();
            $front = Zend_Controller_Front::getInstance();
            $request= $front->getRequest();
            $module = ($request) ? $request->getModuleName() : 'default';
//            echo '#'.__LINE__ . ' ' . __METHOD__ . ' module=' . $module . PHP_EOL;
            self::$_pluginLoader->addModule($module);
            if ('default' != $module) self::$_pluginLoader->addModule('default');
        }
        return self::$_pluginLoader;
    }
    
    /**
     *
     * @param string $name 
     * @return MyProject_Model_Database Description
     */
    static public function loadModel($name)
    {
        $name  = ucfirst( (string) $name);
        $class = self::getLoader()->load($name);
        return new $class();
    }
    
    /**
     *
     * @param string $name
     * @return Zend_Db_Table 
     */
    static public function loadStorage($name)
    {
        
        $name  = 'Db_' . ucfirst( (string) $name);
        $class = self::getLoader()->load($name);
        return new $class();
    }
    
    /**
     *
     * @return Zend_Db_Table 
     */
    public function getStorage()
    {
        if (null === $this->_storage) {
            $this->_storage = self::loadStorage($this->_storageName);
        }
        return $this->_storage;
    }
}

