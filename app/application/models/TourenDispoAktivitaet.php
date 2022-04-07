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
        /* @var $this->_storage Zend_Db_Table_Abstract */
        $this->_storage = $this->getStorage();
        $this->_tbl = $this->_storage->info(Zend_Db_Table_Abstract::NAME);
        $this->_db = $this->_storage->getAdapter();
    }
    
    public function log($tour_id, $action, $uid = null) {
        if ($uid === null) MyProject_Auth_Adapter::getUserId();
        
        $sql = 'SELECT T.tour_id, T.DatumVon, T.Mandant, T.Auftragsnummer, TP.lager_id, TL.portlet_id, TL.timeline_id FROM '
              .'mr_touren_dispo_vorgaenge T '
              .'LEFT JOIN mr_touren_timelines TL ON (T.timeline_id = TL.timeline_id) '
              .'LEFT JOIN mr_touren_portlets TP ON (TL.portlet_id = TP.portlet_id) '
              .'WHERE T.tour_id = ' . ((int)$tour_id) . ' '
              .'LIMIT 1';
        
        //die( '#' . __LINE__ . ' ' . __METHOD__ . ' sql: ' . PHP_EOL . $sql);
        $data = $this->_db->fetchRow($sql, NULL, Zend_Db::FETCH_ASSOC);
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
