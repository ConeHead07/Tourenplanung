<?php

class MyProject_Db_Profiler extends Zend_Db_Profiler 
{
    protected $_logpath = '';
    public function __construct($enabled = false) {
        parent::__construct($enabled);
        $this->_logpath = APPLICATION_PATH . '/log/queries';
    }
    
    public function queryEnd($queryId) {
        if ( ($r = parent::queryEnd($queryId)) == parent::STORED) {
            $q = $this->_queryProfiles[$queryId];
           
            if ($q->getElapsedSecs() > 2.0) {
                $logfile = $this->_logpath . '/' . date('Ymd') . '.long.txt';
                file_put_contents($logfile, number_format($q->getElapsedSecs(),4) . ' | ' .$q->getQuery() . PHP_EOL, FILE_APPEND );
            } else {
                $logfile = $this->_logpath . '/' . date('Ymd') . '.txt';
                file_put_contents($logfile, number_format($q->getElapsedSecs(),4) . ' | ' .$q->getQuery() . PHP_EOL, FILE_APPEND );
            }
            
//            die('#' . __LINE__ . ' ' . __METHOD__ . ' queryEnd: ' . $r . ' parent::stored: ' . parent::STORED . PHP_EOL . file_get_contents($this->_logfile));
        }
    }
}


