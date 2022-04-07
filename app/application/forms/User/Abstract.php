<?php

abstract class Form_User_Abstract extends MyProject_Form {
    //put your code here
    
    public function __construct($options = null) {
        parent::__construct(MyProject_Model_Database::loadModel('user'));
    }
}
