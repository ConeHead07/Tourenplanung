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
            $_sql = $q->getQuery();

            if (!preg_match('#user_pw|MD5\(#', $_sql)) {
                $_sql .= substr($_sql, 0, 30) . '...';
            }
           
            if ($q->getElapsedSecs() > 2.0) {
                $logfile = $this->_logpath . '/' . date('Ymd') . '.long.txt';
                file_put_contents($logfile, number_format($q->getElapsedSecs(),4) . ' | ' .$_sql . PHP_EOL, FILE_APPEND );
            } else {
                $logfile = $this->_logpath . '/' . date('Ymd') . '.txt';
                file_put_contents($logfile, number_format($q->getElapsedSecs(),4) . ' | ' .$_sql . PHP_EOL, FILE_APPEND );
            }
        }
    }
}


