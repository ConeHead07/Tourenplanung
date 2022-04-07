<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author rybka
 */
class Model_User extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'user';
    
    public function fetchRandomEntry()
    {
        $select = $this->getStorage()->select();
        $select->order(new Zend_Db_Expr('RANDOM()'));
        return $row = $this->getStorage()->fetchRow($select)->toArray();
    }
    
    public function fetchEntryByName($username)
    {
        $select = $this->getStorage()->select();
        $row = $this->getStorage()->fetchRow(
            $select->where('user_name = ?', $username)        
        );
        
        return ($row) ? $row->toArray() : null;
//      echo Zend_Debug::dump($select);
    }
    
    public function insertDummies()
    {
        $select = $this->getStorage()->select();
        $_storage = $this->getStorage();
        
        $data = array(
            'user_name' => '',
            'user_pw'  => '',
            'user_role' => 'user',
        );
        for ($i = 1; $i < 100; ++$i) {
            $data['user_name'] = 'TestUser' . substr('0'.$i, -2);
            $data['user_pw'] = 'TestUser' . substr('0'.$i, -2);
            $_storage->insert($data);
        }
        
    }
    
    public function insert(array $data) 
    {
        $uniqueValidator = new MyProject_Validate_DbUnique(
                $this->loadStorage($this->_storageName),
                'user_name',
                null);
        
        if (!$uniqueValidator->isValid($data['user_name'])) {
            throw new Exception('Benutzername '.$data['user_name'].' ist nicht eindeutig!');
        }
        $id = parent::insert($data);
        
        $data['user_id'] = $id;
        $userProfile = new Model_UserProfile();
        $userProfile->insert($data);
        return $id;
    }
    
    public function update(array $data, $id)
    {
        if ( isset($data['user_name']) ) {
            $uniqueValidator = new MyProject_Validate_DbUnique(
                $this->loadStorage($this->_storageName),
                'user_name',
                $id);

            if (!$uniqueValidator->isValid($data['user_name'])) {
                throw new Exception('Benutzername ' . $data['user_name'] . ' ist nicht eindeutig!');
            }
        }
        
        $data['user_id'] = $id;
        $userProfile = new Model_UserProfile();
        $userProfile->update($data, $id);
        return parent::update($data, $id);        
    }

    public function setLogin(int $user_id)
    {
        $db = $this->getStorage()->getAdapter();
        session_regenerate_id(true);
        session_create_id();

        $this->update([
            'login_status' => 'logged-in',
            'login_counter' => new Zend_Db_Expr('login_counter + 1'),
            'last_login' => new Zend_Db_Expr('NOW()'),
        ], $user_id);

        $aBindData = [
            ':user_id' => $user_id,
            ':phpsessid' => session_id(),
            ':ip' => $_SERVER['REMOTE_ADDR'],
            ':ua' => $_SERVER['HTTP_USER_AGENT'],
        ];

        $sql = 'INSERT INTO mr_user_log(user_id, phpsessid, ip, user_agent) '
        . ' VALUES(:user_id, :phpsessid, :ip, :ua)';

        $db->query($sql, $aBindData);

        $_SESSION['log_id'] = $db->lastInsertId();

        print_r($aBindData);
        // exit;


    }

    public function setLogout(int $user_id)
    {
        $db = $this->getStorage()->getAdapter();

        $this->update([
            'login_status' => 'logged-out',
            'last_logout' => new Zend_Db_Expr('NOW()'),
        ], $user_id);

        if (!empty($_SESSION['log_id'])) {
            $sql = 'UPDATE mr_user_log SET logout_date = :logout_date WHERE session_id = :id AND logout_date is null';
            $db->query($sql, [
                'logout_date' => date('Y-m-d H:i:s'),
                'id' => $_SESSION['log_id']
            ]);
        }

    }
    
    public function delete($id) 
    {
        $userProfile = new Model_UserProfile();
        $userProfile->delete($id);
        parent::delete($id);
    }

    public function getUserByName(string $username)
    {
        $sql = 'SELECT * FROM ' . $this->getTable() . ' WHERE user_name LIKE :username OR ldap_user LIKE :username';

        return $this->_db->fetchRow( $sql, [ 'username' => $username]);
    }

    public function getActiveUserByName(string $username)
    {
        $sql = 'SELECT * FROM ' . $this->getTable()
            . ' WHERE user_name LIKE :username OR ldap_user LIKE :username'
            . ' AND deleted = 0 AND freigegeben = :Ja';

        return $this->_db->fetchRow( $sql, [ 'username' => $username, 'Ja' => 'Ja']);
    }
}
