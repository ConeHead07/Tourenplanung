<?php

/**
 * Description of Adapter
 *
 * @author rybka
 */
class MyProject_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    protected $_username;
    protected $_password;
    public function __construct($username,$password)
    {
        $this->_username=$username;
        $this->_password=$password;
    }
    
    public static function getUserId() {
        if ( Zend_Auth::getInstance()->hasIdentity() ) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            return $identity->user_id;
        }
        return 0;
    }
    
    /**
     * 
     * @return mixed|null
     */
    public static function getIdentity() {
        if ( Zend_Auth::getInstance()->hasIdentity() ) {
            return Zend_Auth::getInstance()->getIdentity();
        }
        return null;
    }
    
    /**
     * 
     * @return string|null
     */
    public static function getUserName() {
        if ( Zend_Auth::getInstance()->hasIdentity() ) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            return $identity->user_name;
        }
        return '';
    }
    
    
    /**
     * 
     * @return string|null
     */
    public static function getUserRole() {
        if ( Zend_Auth::getInstance()->hasIdentity() ) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            return $identity->user_role;
        }
        return 'guest';
    }

    /**
     * 
     * @return \Zend_Auth_Result
     */
    public function authenticate()
    {
    	$user = MyProject_Model_Database::loadModel( 'user' );
    	$auth = $user->fetchEntryByName($this->_username);
        
        if(!$auth) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_username);
        }
        
        //echo print_r($auth,1);
        if( $auth['user_pw'] != md5($this->_password) ) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_username);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_username);
    }
}
