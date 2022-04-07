<?php

/**
 * Description of Adapter
 *
 * @author rybka
 */
class MyProject_Auth_Adapter
//    extends Zend_Auth_Adapter_DbTable
    implements Zend_Auth_Adapter_Interface
{
    protected $_username;
    protected $_password;

    protected $_authResultRow = [];

    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;

    }

    /**
     * @return int
     */
    public static function getUserId():int {
        if ( Zend_Auth::getInstance()->hasIdentity() ) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            return $identity->user_id;
        }
        return 0;
    }

    /**
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
        $modelUser = new Model_User();
    	$modelUserLdap = new Model_UserLdap();
    	$this->_authResultRow = (array)$modelUserLdap->getAuth( $this->_username, $this->_password);

        if(empty($this->_authResultRow)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_username);
        }
        $user_id = $this->_authResultRow['user_id'];
        $modelUser->setLogin((int) $user_id);

        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_username);
    }

    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|boolean
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null)
    {
        if ( empty($this->_authResultRow) ) {
            return false;
        }

        $row = $this->_authResultRow;

        if (is_array($returnColumns) && count($returnColumns)) {
            $row = array_intersect_key($row, array_flip($returnColumns));
        }

        if (is_array($omitColumns) && count($omitColumns)) {
            $row = array_diff_key($row, array_flip($omitColumns));
        }

        return (object)$row;
    }
}
