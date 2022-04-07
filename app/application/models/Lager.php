<?php

class Model_Lager extends MyProject_Model_Database
{
    protected $_storageName = 'lager';
    
    protected $_list = null;
    
    protected function loadList()
    {
        $cacheId = __CLASS__ . '___' . __FUNCTION__;
        /** @var $cache MyProject_Cache_Backend_Apcu */
        $cache = Zend_Registry::get( 'cache' );
        $this->_list = $cache->load($cacheId);
        $test = [ 0,1,2,3,4,5,6,7,8,9]; // = [0];


        if (empty($this->_list)) {
            $this->_list = $this->fetchEntries();
            $cache->save($this->_list, $cacheId, [], 600);
            $test = $cache->load($cacheId);
        }

        header('x-log-lager-loadlist-0: '. $cacheId);
        header('x-log-lager-loadlist-1: '. gettype($cache));
        header('x-log-lager-loadlist-2: '. count($this->_list));
        header('x-log-lager-loadlist-3: '. count($test));
    }


    
    public function getList()
    {
        $this->loadList();
        return $this->_list;
    }
    
    public function getHtmlOptions($default = '')
    {
        $this->loadList();
        $re = '';
        $selected = '';
        foreach($this->_list as $v) {
            if ($default) $selected = ($default == $v['lager_id'] || $default == $v['lager_name']) ? 'selected="true"' : '';
            $re.= '<option ' . $selected . ' value="' . $v['lager_id'] . '">' . $v['lager_name'] . '</option>' . PHP_EOL;
        }
        return $re;
    }
    
    /**
     * 
     * @return array associated Array array('Neuss'=>'Neuss', ...)
     */
    public function getAssocLagerNames()
    {
        $this->loadList();
        $re = new stdClass();
        foreach($this->_list as $v) $re->{$v['lager_name']} = $v['lager_name'];
        return $re;
    }
}
