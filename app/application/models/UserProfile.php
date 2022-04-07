<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserProfile
 *
 * @author rybka
 */
class Model_UserProfile extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'userProfile';
        
    public function insert(array $data) 
    {
        $uniqueValidator = new MyProject_Validate_DbUnique(
                $this->loadStorage($this->_storageName),
                'user_id',
                null);
        
        if (!$uniqueValidator->isValid($data['user_id'])) {
            throw new Exception('User-ID '.$data['user_id'].' ist nicht eindeutig!');
        }        
        return parent::insert($data);
    }
    
    public function update(array $data, $id)
    {
        
        if ( !$this->getStorage()->find($id)->count() ) {
            return $this->insert(array_merge($data, array('user_id'=>$id)));
        }
        
        $uniqueValidator = new MyProject_Validate_DbUnique(
                $this->loadStorage($this->_storageName),
                'user_id',
                $id);
        
        if (isset($data['user_id']) && !$uniqueValidator->isValid($data['user_id'])) {
            throw new Exception('User-ID '.$data['user_id'].' ist nicht eindeutig!');
        }
        return parent::update($data, $id);        
    }
    
    /**
     * 
     * @param int $id
     * @return stdClass
     */
    public function getProfile($id)
    {
        if (0) {
            $row = $this->getStorage()->find($id)->current();
            return json_decode($row->profile_json);
        }

        $cacheId = __CLASS__ . '___' . __FUNCTION__ . '_' . $id;
        /** @var $cache MyProject_Cache_Backend_Apcu */
        $cache = Zend_Registry::get( 'cache' );
        $profile = $cache->load($cacheId);
        $fromCache = 1;

        if (empty($profile)) {
            $profile = $this->getStorage()->find($id)->current();
            $cache->save($profile, $cacheId, [], 600);
        }

        header('x-log-UserProfile-getProfile-0: '. $cacheId);
        header('x-log-UserProfile-getProfile-1: '. gettype($cache));
        header('x-log-UserProfile-getProfile-2: '. $profile->profile_json);
        header('x-log-UserProfile-getProfile-3: '. $fromCache);

        return json_decode($profile->profile_json);
    }
    
    /**
     * 
     * @param int $id
     * @param string $property
     * @return scalar
     */
    public function getProfileItem($id, $property)
    {
        $profile = $this->getProfile($id);
        return (property_exists($profile, $property)) ? $profile->{$property} : null;
        
    }
    
    /**
     * 
     * @param int $id
     * @param string $name
     * @param mixed $value
     */
    public function setProfileItem($id, $name, $value)
    {
        $profile = $this->getProfile($id);
        $profile->{$name} = $value;
        $this->update(array('profile_json' => json_encode($profile)), $id);
    }
    
    /**
     * 
     * @param int $id
     * @param stdClass|array $properties
     */
    public function updateProfile($id, $properties)
    {
        $profile = $this->getProfile($id);
        if (!$profile) $profile = new stdClass;
        foreach($properties as $k => $v) $profile->{$k} = $v;
        
        $this->update(array('profile_json' => json_encode($profile)), $id);
        
    }
    
    /**
     * 
     * @param int $id
     * @param stdClass|array $properties
     */
    public function saveProfile($id, $properties)
    {
        $this->update(array('profile_json' => json_encode($properties)), $id);
    }
}
