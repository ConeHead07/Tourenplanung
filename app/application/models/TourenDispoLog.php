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
class Model_TourenDispoLog extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'tourenDispoLog';
    
    /** var $_storage Zend_Db_Table_Abstract */
    protected $_storage = null;
    
    public const OBJ_TOURENPLAN         = 'TP';
    public const OBJ_TIMELINE           = 'TL';
    public const OBJ_TOUR               = 'T';
    public const OBJ_DISPO_FUHRPARK     = 'FP';
    public const OBJ_DISPO_MITARBEITER  = 'MA';
    public const OBJ_DISPO_WERKZEUG     = 'WZ';
    
    public function __construct() {
        parent::__construct();
        /* @var $this->_storage Zend_Db_Table_Abstract */
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
    }

    public function getTourLogDetails(int $tour_id) {
        $modelDV = new Model_TourenDispoVorgaenge();

        $sql = 'SELECT '
            . '   Auftragsnummer, '
            . '   DatumVon, '
            . '   ZeitVon, '
            . '   ZeitBis'
            . ' FROM ' . $modelDV ->getTable()
            . ' WHERE ' . $modelDV ->key() . ' = ' . $tour_id;
        $aRow = $this->_db->fetchRow( $sql );

        if ($aRow) {
            return $aRow;
        } else {
            return [
                'Auftragsnummer' => 0,
                'DatumVon' => null,
                'ZeitVon' => '',
                'ZeitBis' => '',
            ];
        }

    }

    public function log($oType, $oId, $action, $tour_id = 0, $uid = null, $sperrzeiten_id = null, array $aDetails = []) {
        if ($uid === null) {
            $uid = MyProject_Auth_Adapter::getUserId ();
        }

        $aLogData = [
            'tour_id' => $tour_id,
            'user_id' => (int)$uid,
            'object_type' => $oType,
            'object_id' => $oId,
            'action' => $action,
            'action_time' => new Zend_Db_Expr('NOW()'),
            'sperrzeiten_id' => $sperrzeiten_id
        ];

        if (count($aDetails)) {
            $_anr = ($aDetails['Auftragsnummer'] ?? '');
            $_dd = $aDetails['DatumVon'] ?? '';
            $_zv = $aDetails['ZeitVon'] ?? '';
            $_zb = $aDetails['ZeitBis'] ?? '';
            $_bm = $aDetails['bemerkung'] ?? '';

            if (is_numeric($_anr) && (int)$_anr > 0) {
                $aLogData['tour_anr'] = (int)$_anr;
            }

            if (!empty($_dd)) {
                if ($_dd instanceof \DateTime) {
                    $aLogData['dispo_datum'] = $_dd->format('Y-m-d');
                } elseif (is_string($_dd) && preg_match('#^\d\d\d\d-\d\d-\d\d\b#', $_dd)) {
                    $aLogData['dispo_datum'] = substr($_dd, 0, 10);
                } elseif (is_string($_dd) && $_ddTime = strtotime($_dd)) {
                    $aLogData['dispo_datum'] = date('Y-m-d', $_ddTime);
                } else {
                    error_log('Invalid Date-Type for Tour-Logging: ' . var_export($_dd, 1));
                }
            }

            if (is_string($_zv) && preg_match('#^\d\d:\d\d\b#', $_zv)) {
                $aLogData['dispo_zeit_von'] = substr($_zv, 0, 5) . ':00';
            }

            if (is_string($_zb) && preg_match('#^\d\d:\d\d\b#', $_zb)) {
                $aLogData['dispo_zeit_bis'] = substr($_zb, 0, 5) . ':00';
            }

            if (is_string($_bm) && strlen(trim($_bm)) > 0) {
                $aLogData['bemerkung'] = $_bm;
            }
        }

        if (!empty($aLogData['bemerkung']) && strlen($aLogData['bemerkung']) > 200) {
            $aLogData['bemerkung'] = substr($aLogData['bemerkung'], 0, 197) . '...';
        }

        $this->insert( $aLogData );
        
        if ($tour_id) {
            // $tourLogger = MyProject_Model_Database::loadModel('tourenDispoAktivitaet');
            $tourLogger = new Model_TourenDispoAktivitaet();
            $tourLogger->log($tour_id, $action, $uid);
            $tourLogger->log($tour_id, $action.'-'.$oType, $uid);
        }
    }
    
    public function logTour($tour_id, $action, $uid = null, array $aDetails = [] ) {
        $this->log(self::OBJ_TOUR, $tour_id, $action, $tour_id, $uid, null, $aDetails);
    }

    public function logTimeline($timeline_id, $action, $uid = null) {
        $this->log(self::OBJ_TIMELINE, $timeline_id, $action, 0, $uid);
    }

    public function logPortlet($portlet_id, $action, $uid = null) {
        $this->logTourenplan($portlet_id, $action, $uid);
    }
    
    public function logTourenplan($portlet_id, $action, $uid = null, array $aDetails = []) {
        $this->log(self::OBJ_TOURENPLAN, $portlet_id, $action, 0, $uid, null, $aDetails);
    }
    
    public function logResourceFP($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []) {
        $this->log(self::OBJ_DISPO_FUHRPARK, $resource_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails);
    }
    
    public function logResourceMA($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []) {
        $this->log(self::OBJ_DISPO_MITARBEITER, $resource_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails);
    }
    
    public function logResourceWZ($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []) {
        $this->log(self::OBJ_DISPO_WERKZEUG, $resource_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails);
    }
    
    public function logResource($resource_type, $resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []) {
        switch($resource_type) {
            case 'FP':
            $this->logResourceFP($resource_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails);
            break;
            
            case 'MA':
            $this->logResourceMA($resource_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails);
            break;
            
            case 'WZ':
            $this->logResourceWZ($resource_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails);
            break;
        }
    }

    public function getTourHistorie(int $iTourID, array $aQueryOptions = [])
    {
        $iOffset = (int)($aQueryOptions['offset'] ?? 0);
        $iLimit  = (int)($aQueryOptions['limit'] ?? 100);
        $joinIsRequiredForCountQuery = false;

        $qb = $this->buildQuery([]);
        $qb->setSelect(
            'date_format(l.action_time, "%d.%m.%Y %H:%i") `Log-Zeit`, 
            u.user_name,
            l.object_type Typ,
            if (m.mid is not null, m.name, if (f.fid is not null,f.kennzeichen, \'\')) Resource,
            l.action,
            l.tour_anr, 
            date_format(l.dispo_datum, "%d.%m.%Y") `Dispo-Datum`, 
            date_format(l.dispo_zeit_von, "%H:%i") `Von`, 
            date_format(l.dispo_zeit_bis, "%H:%i") `Bis`,' . PHP_EOL
            .' if (m.mid is not null, m.mid, if (f.fid is not null,f.fid, \'\')) AS `Rsrc-ID`' . PHP_EOL)
            ->setFrom('mr_touren_dispo_log l')
            ->setJoin('LEFT JOIN mr_user u ON (l.user_id = u.user_id)' . PHP_EOL
//                .' LEFT JOIN mr_touren_dispo_mitarbeiter dm ON (l.object_type = \'MA\' AND l.tour_id = dm.tour_id AND l.object_id = dm.id)' . PHP_EOL
                .' LEFT JOIN mr_mitarbeiter m ON (l.object_type = \'MA\' AND l.object_id = m.mid )' . PHP_EOL
//                .' LEFT JOIN mr_touren_dispo_fuhrpark df ON (l.object_type = \'FP\' AND l.tour_id = df.tour_id AND l.object_id = df.id)' . PHP_EOL
                .' LEFT JOIN mr_fuhrpark f ON ( l.object_type = \'FP\' AND l.object_id = f.fid  )',
                $joinIsRequiredForCountQuery)
            ->setWhere('l.tour_id = :tour_id')
            ->setOrder('action_time')
            ->setOrderDir('DESC')
            ->setOffset($iOffset)
            ->setLimit($iLimit)
            ->setParam('tour_id', $iTourID);

//        echo $qb->assemble();
//        exit;

        return [
            'total' => $this->_db->fetchOne( $qb->assembleCount() ),
            'offset' => $iOffset,
            'limit' => $iLimit,
            'rows' => $this->_db->fetchAll( $qb->assemble() )
        ];

    }
    
}
