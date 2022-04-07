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
    
    const OBJ_TOURENPLAN         = 'TP';
    const OBJ_TIMELINE           = 'TL';
    const OBJ_TOUR               = 'T';
    const OBJ_DISPO_FUHRPARK     = 'FP';
    const OBJ_DISPO_MITARBEITER  = 'MA';
    const OBJ_DISPO_WERKZEUG     = 'WZ';
    
    public function __construct() {
        /* @var $this->_storage Zend_Db_Table_Abstract */
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
    }

    public function log($oType, $oId, $action, $tour_id = 0, $uid = null, $sperrzeiten_id = null) {
        if ($uid === null) $uid = MyProject_Auth_Adapter::getUserId ();
        $this->insert( array(
            'tour_id' => $tour_id,
            'user_id' => (int)$uid,
            'object_type' => $oType,
            'object_id' => $oId,
            'action' => $action,
            'action_time' => new Zend_Db_Expr('NOW()'),
            'sperrzeiten_id' => $sperrzeiten_id
        ));
        
        if ($tour_id) {
            // $tourLogger = MyProject_Model_Database::loadModel('tourenDispoAktivitaet');
            $tourLogger = new Model_TourenDispoAktivitaet();
            $tourLogger->log($tour_id, $action, $uid);
            $tourLogger->log($tour_id, $action.'-'.$oType, $uid);
        }
    }
    
    public function logTour($tour_id, $action, $uid = null) {
        $this->log(self::OBJ_TOUR, $tour_id, $action, $tour_id, $uid);
    }
    
    public function logTimeline($timeline_id, $action, $uid = null) {
        $this->log(self::OBJ_TIMELINE, $timeline_id, $action, 0, $uid);
    }
    
    public function logTourenplan($portlet_id, $action, $uid = null) {
        $this->log(self::OBJ_TOURENPLAN, $portlet_id, $action, 0, $uid);
    }
    
    public function logResourceFP($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null) {
        $this->log(self::OBJ_DISPO_FUHRPARK, $resource_id, $action, $tour_id, $uid, $sperrzeiten_id);
    }
    
    public function logResourceMA($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null) {
        $this->log(self::OBJ_DISPO_MITARBEITER, $resource_id, $action, $tour_id, $uid, $sperrzeiten_id);
    }
    
    public function logResourceWZ($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null) {
        $this->log(self::OBJ_DISPO_WERKZEUG, $resource_id, $action, $tour_id, $uid, $sperrzeiten_id);
    }
    
    public function logResource($resource_type, $resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null) {
        switch($resource_type) {
            case 'FP':
            $this->logResourceFP($resource_id, $action, $tour_id, $uid, $sperrzeiten_id);
            break;
            
            case 'MA':
            $this->logResourceMA($resource_id, $action, $tour_id, $uid, $sperrzeiten_id);
            break;
            
            case 'WZ':
            $this->logResourceWZ($resource_id, $action, $tour_id, $uid, $sperrzeiten_id);
            break;
        }
    }

    public function getTourHistorie(int $iTourID, array $aQueryOptions = [])
    {
        $iOffset = (int)($aQueryOptions['offset'] ?? 0);
        $iLimit  = (int)($aQueryOptions['limit'] ?? 100);
        $joinIsRequiredForCountQuery = false;

        $qb = $this->buildQuery([]);
        $qb->setSelect('l.*, u.user_name,' . PHP_EOL
            .' if (m.mid is not null, m.mid, if (f.fid is not null,f.fid, \'\')) rID,' . PHP_EOL
            .' if (m.mid is not null, m.name, if (f.fid is not null,f.kennzeichen, \'\')) rName ')
            ->setFrom('mr_touren_dispo_log l')
            ->setJoin('LEFT JOIN mr_user u ON (l.user_id = u.user_id)' . PHP_EOL
                .' LEFT JOIN mr_touren_dispo_mitarbeiter dm ON (l.object_type = \'MA\' and l.object_id = dm.id)' . PHP_EOL
                .' LEFT JOIN mr_mitarbeiter m ON (l.object_type = \'MA\' && (l.object_id = m.mid OR dm.mitarbeiter_id = m.mid) )' . PHP_EOL
                .' LEFT JOIN mr_touren_dispo_fuhrpark df ON (l.object_type = \'FP\' and l.object_id = df.id)' . PHP_EOL
                .' LEFT JOIN mr_fuhrpark f ON (l.object_type = \'FP\' && (l.object_id = f.fid OR df.fuhrpark_id = f.fid)  )',
                $joinIsRequiredForCountQuery)
            ->setWhere('l.tour_id = :tour_id')
            ->setOrder('action_time')
            ->setOrderDir('DESC')
            ->setOffset($iOffset)
            ->setLimit($iLimit)
            ->setParam('tour_id', $iTourID);

        return [
            'total' => $this->_db->fetchOne( $qb->assembleCount() ),
            'offset' => $iOffset,
            'limit' => $iLimit,
            'rows' => $this->_db->fetchAll( $qb->assemble() )
        ];

    }
    
}
