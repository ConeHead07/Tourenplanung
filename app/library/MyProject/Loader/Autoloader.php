<?php

class MyProject_Loader_Autoloader implements Zend_Loader_Autoloader_Interface
{
    public function autoload($class)
    {
//      echo __CLASS__ . '::' . __METHOD__ . '(' . $class . ')' . "\n";
        $classPath = str_replace('_', '/', $class) . '.php';
        $paths = explode(PATH_SEPARATOR, get_include_path());
        
        foreach ($paths as $path)
            if (file_exists($path . '/' . $classPath)) 
            {
                require_once(($path . '/' . $classPath));
                if (class_exists($class)) return;
            }
    }
}