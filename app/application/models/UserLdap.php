<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 28.01.2019
 * Time: 10:15
 */


class Model_UserLdap
{
    /** @var \MyProject_Ldap_Ldap */
    private $_ldapConn = null;
    private $_ldapHost = '';
    private $_ldapPort = '';
    private $_ldapDom = '';

    private $_errors = [];

    public function __construct()
    {
        $cnf = Zend_Registry::get('ldap');
        $this->_ldapHost = $cnf['host'];
        $this->_ldapPort = $cnf['port'];
        $this->_ldapDom = $cnf['dom'];

        error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__. '() cnf:' . json_encode($cnf) );
    }

    private function _connect() {
        $this->_ldapConn = new \MyProject_Ldap_Ldap($this->_ldapHost, $this->_ldapPort);
    }

    public function getAuth(string $username, string $password) {
        $this->_connect();
        $userModel = new \Model_User();
        $user = $userModel->getActiveUserByName( $username );
        error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__. '() user:' . json_encode($user) );

        if (!$user) {
            $this->_errors[] = "Ldap-Abfrage wurde nicht gestartet, da kein Benutzer mit dem Benutzernamen $username existiert!";
            error_log('ERROR [' . date('H:i:s') . '] ' . __METHOD__. '() NO active user FOUND with name "' . $username . '"; return null!!');
            return null;
        }
        if (strcasecmp($user['user_name'], $username) == 0 && $user['user_pw'] === md5($password)) {
            error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__. '() Login without Ldap: "' . $username . '"; return userdata!!');
            return (object)$user;
        }
        $user = (object)$user;

        if ($user->user_name == $username && $user->ldap_user) {
            $dn = $this->_ldapDom . $user->ldap_user;
        } elseif ($user->ldap_user == $username) {
            $dn = $this->_ldapDom . $username;
        } else {
            $dn = '';
            error_log('ERROR [' . date('H:i:s') . '] ' . __METHOD__. '() NO dn return null!!');
            return null;
        }
        error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__. '() dn:' . json_encode($dn) );

        try {
            @$this->_ldapConn->bind($dn, $password);
            error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__. '() NO exception!' );
        } catch(Exception $e) {
            error_log('ERROR [' . date('H:i:s') . '] ' . __METHOD__. '() exception:' . json_encode($e) );
            return null;
        }

        $isBinded = $this->_ldapConn->isBinded();
        $isBindedAndUserExists = $isBinded && $this->_ldapConn->userExists($user->ldap_user);

        if ($isBinded) {
            error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__ . '() Ldap isBinded = TRUE  :-) !' );
            if ($isBindedAndUserExists) {
                error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__. '() LdapUser exists = TRUE ;-) !' );
            } else {
                error_log('ERROR [' . date('H:i:s') . '] ' . __METHOD__. '() LdapUser exists = FALSE :-( !' );
            }
        } else {
            error_log('ERROR [' . date('H:i:s') . '] ' . __METHOD__ . '() Ldap isBinded = FALSE :-( !');
        }

        if ($isBindedAndUserExists) {
            error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__ . '() Ldap auth SUCCESS return user data:' . json_encode($user) . '!');
            return $user;
        }
        error_log('INFO [' . date('H:i:s') . '] ' . __METHOD__ . '() Ldap auth FAILURE return null!');
        return null;
    }

    public function __destruct()
    {
    }
}