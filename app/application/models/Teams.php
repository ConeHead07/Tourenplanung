<?php

class Model_Teams extends MyProject_Model_Database
{
    protected $_storageName = 'teams';
    
    protected $_list = null;
    
    protected function loadList()
    {
        $cacheId = __CLASS__ . '___' . __FUNCTION__;
        /** @var $cache MyProject_Cache_Backend_Apcu */
        $cache = Zend_Registry::get( 'cache' );
        $this->_list = $cache->load($cacheId);

        if (empty($this->_list)) {
            $this->_list = $this->fetchEntries();
            $cache->save($this->_list, $cacheId, [], 900);
        }
    }

    
    public function getList()
    {
        $this->loadList();
        return $this->_list;
    }

    /**
     *
     * return assoc array indizes are the TeamIDs
     *
     * @return array
     */
    public function getAssocTeams() {
        $aList = $this->getList();
        $aTeams = [];
        foreach($aList as $_team) {
            $_key = $_team['team_id'];
            $_team = $_team['team'];
            $aTeams[ $_key ] = $_team;
        }
        return $aTeams;
    }
    
    public function getHtmlOptions($default = '')
    {
        $this->loadList();
        $re = '';
        $selected = '';
        foreach($this->_list as $v) {
            if ($default) $selected = ($default == $v['team_id'] || $default == $v['team']) ? 'selected="true"' : '';
            $re.= '<option ' . $selected . ' value="' . $v['team_id'] . '">' . $v['team'] . '</option>' . PHP_EOL;
        }
        return $re;
    }
}
