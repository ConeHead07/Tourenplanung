<?php
require_once 'forms/user/Abstract.php';

class Form_User_Register extends User_Abstract
{
    public function __construct($options = NULL)
    {
        echo '#' . __LINE__ . ' ' . __METHOD__ . "<br>\n";
        /* @var $config Zend_Config_Ini */
        $config = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/forms/user.ini', 
                'register'
        );
        
        $this->setConfig($config);
        
        $inputUserName = $this->getElement('user_name');
        $this->getElement('user_name')->addValidator('UserNameUnique');
        
        Zend_Debug::dump($inputUserName);
        
        //$config->merge($configDecorators);
        parent::__construct($options);
        
        $this->renderProjectDecorators();
    }
    
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
//        echo '#' . __METHOD__ . "<br>\n";
    }


}

