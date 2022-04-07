<?php
/**
 *
 * @author rybka
 */
class Model_TourenDispoAktivitaet extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'tourenDispoAktivitaet';
    
    /** var $_storage Zend_Db_Table_Abstract */
    protected $_storage = null;
    
    public function __construct() {
        parent::__construct();
        /* @var $this->_storage Zend_Db_Table_Abstract */
        $this->_storage = $this->getStorage();
        $this->_tbl = $this->_storage->info(Zend_Db_Table_Abstract::NAME);
        $this->_db = $this->_storage->getAdapter();
    }
    
    public function log($tour_id, $action, $uid = null, array $aTourData = [])
    {
        if ($uid === null) {
            $uid = MyProject_Auth_Adapter::getUserId();
        }
        
        $sql = 'SELECT T.tour_id, T.DatumVon, T.Mandant, T.Auftragsnummer, '
              . ' TP.lager_id, TL.portlet_id, TL.timeline_id '
              . ' FROM mr_touren_dispo_vorgaenge T '
              . ' JOIN mr_touren_timelines TL ON (T.timeline_id = TL.timeline_id) '
              . ' JOIN mr_touren_portlets TP ON (TL.portlet_id = TP.portlet_id) '
              . ' WHERE T.tour_id = ' . ((int)$tour_id) . ' '
              . ' LIMIT 1';

        $data = $this->_db->fetchRow($sql, NULL, Zend_Db::FETCH_ASSOC);

        if (empty($data) && !empty($aTourData)) {
            if (!empty($aTourData['DatumVon'])) $data['DatumVon'] = $aTourData['DatumVon'];
            elseif (!empty($aTourData['datum'])) $data['datum'] = $aTourData['datum'];
            if (!empty($aTourData['Mandant'])) $data['Mandant'] = $aTourData['Mandant'];
            if (!empty($aTourData['Auftragsnummer'])) $data['Auftragsnummer'] = $aTourData['Auftragsnummer'];
            if (!empty($aTourData['lager_id'])) $data['lager_id'] = $aTourData['lager_id'];
            if (!empty($aTourData['portlet_id'])) $data['portlet_id'] = $aTourData['portlet_id'];
            if (!empty($aTourData['timeline_id'])) $data['timeline_id'] = $aTourData['timeline_id'];
        }

        if (!empty($aTourData['IsDefault'])) {
            $data['Auftragsnummer'] = 0;
            $data['Mandant'] = 0;
        }

        try {
            if (0 && empty($data['Mandant'])) {
                throw new Exception("");
            }
        } catch(Exception $e) {
            print_r(compact(['aTourData', 'data']));
            exit;
        }

        if (empty($data)) {
            return;
        }

        $data['tour_id'] = $tour_id;
        $data['user_id'] = $uid;
        $data['aktion']  = $action;
        
        $this->replace( $data );
    }
    
    public function find($tour_id, $max_age = 86400) {
        
        $db = $this->_db;
        $select = $this->getStorage()->select();
        $select->where('tour_id = ?', $tour_id);
        if ($max_age)
            $select->where('zugriffszeit >= DATE_ADD(NOW(), INTERVAL -' . (int)$max_age . ' SECOND)');
        
        return $db->fetchAll($select);
    }
    
    public function findRecent($tour_id, $max_age = 1800) {
        return $this->find($tour_id, $max_age);
    }
    
    public function getHistoryByTourId($tour_id) {
        
        $db = $this->_db;
        $select = $this->getStorage()->select();
        $select->where('tour_id = ?', $tour_id);
        
        return $db->fetchAll($select);
    }
    
}
