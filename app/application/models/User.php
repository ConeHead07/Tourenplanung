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
        $uniqueValidator = new MyProject_Validate_DbUnique(
                $this->loadStorage($this->_storageName),
                'user_name',
                $id);
        
        if (!$uniqueValidator->isValid($data['user_name'])) {
            throw new Exception('Benutzername '.$data['user_name'].' ist nicht eindeutig!');
        }
        
        $data['user_id'] = $id;
        $userProfile = new Model_UserProfile();
        $userProfile->update($data, $id);
        return parent::update($data, $id);        
    }
    
    public function delete($id) 
    {
        $userProfile = new Model_UserProfile();
        $userProfile->delete($id);
        parent::delete($id);
    }
}
