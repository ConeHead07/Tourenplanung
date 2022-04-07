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

    public static function getProfiledQueryList(Zend_Db_Adapter_Abstract $db = null): array
    {
        if (is_null($db)) {
            /** @var Zend_Db_Adapter_Abstract $db */
            $db = Zend_Registry::get('db');
        }

        $aQueryProfiles = $db->getProfiler()->getQueryProfiles();
        $aList = [];
        if (is_array($aQueryProfiles)) foreach($aQueryProfiles as $_qryProfile) {
            /** @var Zend_Db_Profiler_Query $_p */
            $_p = $_qryProfile;
            $aList[] =
                '['.date('Y-m-d H:i:s', (int)$_p->getStartedMicrotime()) . ', '
                . round($_p->getElapsedSecs(), 3) . 's] '
                . $_p->getQuery()
                . ', params' .  json_encode($_p->getQueryParams())
            ;
        }
        return $aList;
    }

    public static function logSiteQueries()
    {

        $aQueries = self::getProfiledQueryList();
        $numQueries = count( $aQueries );

        if (!$numQueries) {
            return;
        }

        $uri = str_replace('/', '_', $_SERVER['REQUEST_URI']);
        $sLogUri = substr(rawurlencode($uri), 0, 50);
        $sLogDir = APPLICATION_PATH . "/log/queries/per-request/$numQueries";
        $sLogFile = $sLogDir . '/' . $sLogUri . '.txt';

        if (!is_dir($sLogDir)) {
            if (!mkdir($sLogDir, 0777, true) && !is_dir($sLogDir)) {
                return;
            }
        }

        file_put_contents($sLogFile, print_r($aQueries,1));
    }
}


