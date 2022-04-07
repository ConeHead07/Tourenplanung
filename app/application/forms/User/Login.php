<?php
require_once 'forms/User/Abstract.php';

class Form_User_Login extends Form_User_Abstract
{
    public function __construct($options = NULL)
    {
        /* @var $config Zend_Config_Ini */
        $config = new Zend_Config_Ini(         APPLICATION_PATH . '/configs/forms/user.ini', 'login');
        $this->setConfig($config);
        
        //$config->merge($configDecorators);
        parent::__construct($options);
    }
    
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
//        echo '#' . __METHOD__ . "<br>\n";
    }
}


