<?php

class TourenDispoIds {
    public $portlet_id = 0;
    public $timeline_id = 0;
    public $tour_id = 0;
    
    public function __construct($ids = array()) {
        if (isset($ids['portlet_id']))  $this->portlet_id  = $ids['portlet_id'];
        if (isset($ids['timeline_id'])) $this->timeline_id = $ids['timeline_id'];
        if (isset($ids['tour_id']))     $this->tour_id     = $ids['tour_id'];
    }
}

class Model_TourenDispoVorgaenge extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'tourenDispoVorgaenge';
    protected $_numAll = 0;
    protected $_error = '';

    /** @var null|Model_Db_TourenDispoVorgaenge  */
    protected $_storage = null;

    /** @var null|Zend_Db_Adapter_Abstract  */
    protected $_db = null;

    /** @var string|null  */
    protected $_tbl = null;
    
    const STATUS_AUFTRAG_ABGESCHLOSSEN = 5;
    const STATUS_AUFTRAG_DISPONIERT    = 4;
    const STATUS_TOUR_ABGESCHLOSSEN    = 3;
    const STATUS_TOUR_DISPONIERT       = 2;
    const STATUS_TOUR_LOCKED           = 1;

    /** @var string  */
    private $_lastErrorTourOperationNotAllowed = '';
    
    public function __construct() {
        parent::__construct();
        /* @var $this->_storage Model_Db_TourenDispoVorgaenge */
        $this->_storage = $this->getStorage();
        
        /* @var $this->_db Zend_Db_Adapter_Abstract */        
        $this->_db = $this->_storage->getAdapter();
        
        /* @var $this->_tbl string */
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);        
    }

    public function dispoLog($tour_id, $action, $aDetails) {
        $uid = MyProject_Auth_Adapter::getUserId();
        $this->getTourDispoLogger()->logTour($tour_id, $action, $uid, $aDetails);
    }
    
    public function statusTourenZeitenErfassung($mandant, $auftragsnr) {
        
        $sql = 'SELECT count( 1 ) num_touren, ' . PHP_EOL
              .' SUM( IF( zeiten_erfasst_am IS NOT NULL , 1, 0 ) ) num_erfasst, ' . PHP_EOL 
              .' SUM( IF( zeiten_erfasst_am IS NOT NULL , 0, 1 ) ) num_unerfasst ' . PHP_EOL
              .' FROM ' . $this->_db->quoteIdentifier($this->_tbl) .  PHP_EOL
              .' WHERE Mandant = :mandant AND Auftragsnummer = :auftragsnr';
        
        return $this->_db->fetchRow($sql, array(
            'mandant' => $mandant,
            'auftragsnr' => $auftragsnr,
        ), Zend_Db::FETCH_OBJ);
    }
    
    public function countUnerfassteResourcenZeitenByTourId($tourid) {
        $ma = new Model_TourenDispoMitarbeiter();
        $fp = new Model_TourenDispoFuhrpark();
        
        return $ma->countUnerfassteZeitenByTourId($tourid) 
                + $fp->countUnerfassteZeitenByTourId($tourid);
    }
    
    public function countTourenZeitenUnerfasst($mandant, $auftragsnr) {
        
        $sql = 'SELECT SUM( IF( zeiten_erfasst_am IS NOT NULL , 0, 1 ) ) ' . PHP_EOL
              .' FROM ' . $this->_db->quoteIdentifier($this->_tbl) .  PHP_EOL
              .' WHERE Mandant = :mandant AND Auftragsnummer = :auftragsnr';
//        die( $sql . '<br>' . PHP_EOL );
        return $this->_db->fetchOne($sql, array(
            'mandant' => $mandant,
            'auftragsnr' => $auftragsnr,
        ), Zend_Db::FETCH_OBJ);
    }
    
    public static function getLockTextByCode($code) 
    {
        switch($code) {
            case self::STATUS_AUFTRAG_ABGESCHLOSSEN:
            return 'Auftrag abgeschlossen';
                
            case self::STATUS_AUFTRAG_DISPONIERT:
                return 'Auftrag disponiert';
                
            case self::STATUS_TOUR_ABGESCHLOSSEN:
                return 'Tour abgeschlossen';
                
            case self::STATUS_TOUR_DISPONIERT:
                return 'Tour disponiert';
                
            case self::STATUS_TOUR_LOCKED:
                return 'Tour gesperrt';
        }
    }
    
    public function auftragssummenByAbgeschlossenAm($monat = '', $orderby = 'LieferungName ASC') {

        return [];
        if (!$monat) $monat = date('Y-m');
        // echo $monat . ' ' . $orderby;
        /*
         * SELECT ap.Mandant, ap.Auftragsnummer, dp.Mandant, ap.Positionsnummer, dp.Auftragsnummer, dp.Positionsnummer
FROM mr_auftragspositionen_dispofilter ap
LEFT JOIN `mr_touren_dispo_auftragspositionen` dp ON ap.Mandant = dp.Mandant
AND ap.Auftragsnummer = dp.Auftragsnummer
AND ap.Positionsnummer = dp.Positionsnummer
WHERE 1
LIMIT 30 
          
SELECT dv.Mandant, dv.Auftragsnummer, 
       COUNT(ap.Positionsnummer) ap_cnt, SUM(ap.LieferMenge) ap_sum, 
       dp.cnt dp_cnt, dp.sum_menge dp_sum_menge, 
       ak.Kundennummer, ak.LieferungName, ak.Auftragswert
 FROM
 mr_touren_dispo_vorgaenge dv  
 JOIN `mr_auftragskoepfe_dispofilter` ak 
 ON dv.Mandant = ak.Mandant AND dv.Auftragsnummer = ak.Auftragsnummer 
 LEFT JOIN `mr_auftragspositionen_dispofilter` ap 
 ON dv.Mandant = ap.Mandant AND dv.Auftragsnummer = ap.Auftragsnummer AND ap.Positionsart = 1 
 LEFT JOIN (
  SELECT ap.Mandant, ap.Auftragsnummer, COUNT(DISTINCT(ap.Positionsnummer)) cnt, SUM(ap.DisponierteMenge) sum_menge
  FROM mr_touren_dispo_vorgaenge dv 
  LEFT JOIN mr_touren_dispo_auftragspositionen ap ON dv.Mandant = ap.Mandant AND dv.Auftragsnummer = ap.Auftragsnummer
  WHERE dv.DatumVon BETWEEN '2014-01-00' AND '2014-02-00'
  GROUP BY dv.Mandant, dv.Auftragsnummer
 ) as dp ON dv.Mandant = dp.Mandant AND dv.Auftragsnummer = dp.Auftragsnummer
 WHERE dv.DatumVon BETWEEN '2014-01-00' AND '2014-02-00'
 GROUP BY dv.Mandant, dv.Auftragsnummer 
 
          
         */
        
        $sql = 'SELECT d.Mandant, d.Auftragsnummer, '
              .' d.`auftrag_abgeschlossen_am`, '
              .' a.Kundennummer, a.LieferungName, a.Auftragswert '
              .' FROM `mr_touren_dispo_auftraege` d '
              .' JOIN mr_auftragskoepfe_dispofilter a ON ( d.Mandant = a.Mandant '
              .' AND d.Auftragsnummer = a.Auftragsnummer ) '
              .' WHERE date_format( d.`auftrag_abgeschlossen_am` , \'%Y-%m\' ) = :monat '
//              .' GROUP BY a.Kundennummer '
              .' ORDER BY :orderby '
              .' LIMIT 0 , 30';
        
        $sqlNEU = '
 SELECT count(distinct(dv.tour_id)) Tourenv, dv.Mandant, dv.Auftragsnummer, 
 MIN(dv.DatumVon) DatumVon, MAX(dv.DatumVon) DatumBis, 
 ak.Kundennummer, ak.LieferungName, ak.Auftragswert, 
 da.*, dv.* 
 FROM 
`mr_touren_dispo_vorgaenge` dv 
 LEFT JOIN mr_touren_dispo_auftraege da  
  ON (dv.Mandant = da.Mandant AND dv.Auftragsnummer = da.Auftragsnummer) 
 LEFT JOIN mr_auftragskoepfe_dispofilter ak  
  ON (dv.Mandant = ak.Mandant AND dv.Auftragsnummer = ak.Auftragsnummer) 
 LEFT JOIN mr_auftragspositionen_dispofilter ap 
  ON(dv.Mandant = ap.Mandant AND dv.Auftragsnummer = ap.Auftragsnummer AND ap.AlternativPos = 0) 
 WHERE 
 dv.Auftragsnummer IS NOT NULL AND dv.Auftragsnummer > 0 AND 
 date_format(dv.DatumVon , \'%Y-%m\' ) = ' . $this->_db->quote($monat) . ' 
 GROUP BY dv.Mandant, dv.Auftragsnummer' . PHP_EOL;
        
 $sql = 'SELECT dv.Mandant, dv.Auftragsnummer, 
       COUNT(ap.Positionsnummer) ap_cnt, SUM(ap.LieferMenge) ap_sum, 
       dp_cnt, dp_sum_menge,
       (dp_cnt * 100 / COUNT(ap.Positionsnummer)) pos_pct, (dp_sum_menge * 100 / SUM(ap.LieferMenge)) sum_pct,
       if ( (dp_cnt * 100 / COUNT(ap.Positionsnummer)) > 80 AND (dp_sum_menge * 100 / SUM(ap.LieferMenge)) > 80, 1, 0) stat,
       if (da.auftrag_disponiert_am IS NOT NULL, DATE_FORMAT(da.auftrag_disponiert_am,\'%d.%m.%Y\'), DATE_FORMAT(da.auftrag_abgeschlossen_am,\'%d.%m.%Y\')) DispoDatum, 
       ak.Kundennummer, ak.LieferungName Kunde, ak.Auftragswert 
 FROM
 mr_touren_dispo_vorgaenge dv  
 JOIN `mr_auftragskoepfe_dispofilter` ak 
 ON dv.Mandant = ak.Mandant AND dv.Auftragsnummer = ak.Auftragsnummer  
 JOIN `mr_touren_dispo_auftraege` da 
 ON dv.Mandant = da.Mandant AND dv.Auftragsnummer = da.Auftragsnummer 
 LEFT JOIN `mr_auftragspositionen_dispofilter` ap 
 ON dv.Mandant = ap.Mandant AND dv.Auftragsnummer = ap.Auftragsnummer AND ap.Positionsart = 1 
 LEFT JOIN (
  SELECT ap.Mandant, ap.Auftragsnummer, 
       COUNT(DISTINCT(ap.Positionsnummer)) dp_cnt, SUM(ap.DisponierteMenge) dp_sum_menge
  FROM mr_touren_dispo_vorgaenge dv 
  LEFT JOIN mr_touren_dispo_auftragspositionen ap ON dv.Mandant = ap.Mandant AND dv.Auftragsnummer = ap.Auftragsnummer
  WHERE date_format(dv.DatumVon , \'%Y-%m\' ) = ' . $this->_db->quote($monat) . '
  GROUP BY dv.Mandant, dv.Auftragsnummer
 ) as dp ON dv.Mandant = dp.Mandant AND dv.Auftragsnummer = dp.Auftragsnummer
 WHERE date_format(dv.DatumVon , \'%Y-%m\' ) = ' . $this->_db->quote($monat) . ' AND dp_cnt IS NOT NULL 
 GROUP BY dv.Mandant, dv.Auftragsnummer 
 ORDER BY stat DESC';
if ($orderby) {
    $sql.= ', ' . $orderby . PHP_EOL;
}
        $this->_db->query( "set names 'utf8'"  );
        $stmt = $this->_db->query( $sql  );
        $stmt->setFetchMode(Zend_Db::FETCH_OBJ);
        try {
            return $stmt->fetchAll();
        } catch (Exception $ex) {
            die($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }
    
    /**
     * @abstract
     * Enthaelt Angaben zu sich zeitlich ueberschneidenden Resourcen,
     * wenn versucht wird eine disponierte zeitlich umzudisponieren
     * @return string 
     */
    public function error() {
        return $this->_error;
    }
//    id 	Mandant 	Auftragsnummer 	timeline_id 	DatumVon 	ZeitVon 	DatumBis 	ZeitBis
    
    // Mandant, Auftragsnummer, timeline_id, DatumVon, ZeitVon, DatumBis, ZeitBis
    
    /**
     *
     * @param array $data
     * @param string|int $toPos
     * @return int|null new id or null if error
     * @throws Exception
     */
    public function drop($data, $toPos = 'last') 
    {
        $id = null;
        $rgxIsoDate = ':^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[0-2])$:';
        if (array_key_exists('DatumVon', $data) && preg_match($rgxIsoDate, $data['DatumVon'])) {
            if (empty($data['DatumBis'])) {
                $data['DatumBis'] = $data['DatumVon'];
            }
            $id = $this->insert($data);
            $data['bemerkung'] = json_encode($data);
            $this->dispoLog($id, 'insert', $data);
            return $id;
        }
        if (!$id) {
            throw new Exception('ungueltige Daten. Route konnte nicht gespeichert werden!' . PHP_EOL . print_r($data,1));
            
        }
        return null;
    }
    
    /**
     *
     * @param array $data
     * @param string|int $toPos
     * @return int|null new id or null if error
     * @throws Exception
     */
    public function move($checkdata) 
    {
        $fields4UpdateOnly = array(
            'DatumVon'=>null,'DatumBis'=>null,'ZeitVon'=>null,'ZeitBis'=>null,'timeline_id'=>null,'id'=>null
        );
        
        $data = array();
        foreach($fields4UpdateOnly as $_f => $_) {
            if (isset($checkdata[$_f])) $data[$_f] = $checkdata[$_f];
        }
        
        $portlet = $this->getPortletByTimelineId($data['timeline_id']);
        $data['DatumVon'] = $portlet['datum'];
        $data['DatumBis'] = $portlet['datum'];
        
//        die( '<pre>' . print_r( array($data, $fields4UpdateOnly, $checkdata), 1 ));
        
        $id = null;
        $rgxIsoDate = ':^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[0-2])$:';
        if (array_key_exists('DatumVon', $data) && preg_match($rgxIsoDate, $data['DatumVon'])) {
            $updateSuccess = $this->update($data, $data['id']);
            $this->dispoLog($data['id'], 'moved', $data + ['bemerkung'=>json_encode($data)]);
            return $updateSuccess;
        }

        if (!$id) {
            throw new Exception('ungueltige Daten. Route konnte nicht gespeichert werden!' . PHP_EOL . print_r($checkdata,1));
            
        }
        return null;
    }
    
    public function resize($d) 
    {
        $fields4UpdateOnly = array(
            'DatumVon'=>null,'DatumBis'=>null,'ZeitVon'=>null,'ZeitBis'=>null,'timeline_id'=>null,'id'=>null
        );
        $data = array();
        foreach($fields4UpdateOnly as $_f => $_) {
          if (isset($d[$_f])) $data[$_f] = $d[$_f];
        }
        
        $rgxIsoDate = ':^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$:';
        $b = preg_match($rgxIsoDate, $data['DatumVon'], $m);
        
        if (isset($data['DatumVon']) && preg_match($rgxIsoDate, $data['DatumVon'])) {
            $updateSuccess = $this->update($data, $data['id']);
            $this->dispoLog($data['id'], 'resize', $data + ['bemerkung'=>json_encode($data)]);
            return $updateSuccess;
        }
        return null;
    }
    
    /**
     * 
     * @param int $id
     * @return boolean true
     * @throws Exception, wenn Auftrag bereits als fertig abgeschlossen oder disponiert markiert ist
     */
    public function delete($id) {
        $storage = $this->getStorage();
        $db = $storage->getAdapter();
        $where = ' `tour_id` ='.(int)$id . ' ';        
        
        $tour = $storage->find($id)->current();        
        
        $da = new Model_TourenDispoAuftraege();
        $fp = new Model_Db_TourenDispoFuhrpark();
        $ma = new Model_Db_TourenDispoMitarbeiter();
        $wz = new Model_Db_TourenDispoWerkzeug();
        $dp = new Model_Db_TourenDispoPositionen();
        $dpt = new Model_Db_TourenDispoPositionenText();
        
        $status = $da->isLocked($tour->Mandant, $tour->Auftragsnummer);
        $error  = '';
        
        switch($status) {
            case Model_TourenDispoAuftraege::STATUS_ABGESCHLOSSEN:
                $error = 'Tour kann nicht geloescht werden, da der Auftrag bereits abgeschlossen wurde!';
                break;
            case Model_TourenDispoAuftraege::STATUS_DISPONIERT:
                $error = 'Tour kann nicht geloescht werden, da der Auftrag bereits fertig disponiert wurde!';
                break;
        }
        
        if ($error) {
            throw new Exception($error);
        }
        
        $db->beginTransaction();
        try {
            $fp->delete($where);
            $ma->delete($where);
            $wz->delete($where);
            $dp->delete($where);
            $dpt->delete($where);
            $storage->delete($where);
            
            $da->refreshTourDispoCount($tour->Mandant, $tour->Auftragsnummer);
            $da->refreshTourFinishCount($tour->Mandant, $tour->Auftragsnummer);
            
            $db->commit();
            return true;
        } catch(Zend_Db_Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
            echo $e->getTraceAsString();
            die(__METHOD__ . print_r([' id' => $id ],1));
        }
    }
    
    public function finishtourabschluss($id, $username) 
    {
        $tour = $this->getStorage()->find($id)->current();
//        if ($tour->tour_abgeschlossen_am !== null ) return true;
        
        $modelDP = new Model_TourenDispoPositionen();
        $NLRow = $modelDP->getStorage()->fetchRow( 'tour_id = '.(int)$id . ' AND AbschlussNLMenge > 0' );
        $neulieferung = ( $NLRow ) ? 1 : 0;
        
        $reId = $tour->setFromArray(array(
            'tour_abgeschlossen_am'   => new Zend_Db_Expr("NOW()"),
            'tour_abgeschlossen_user' => $username,
            'neulieferung' => $neulieferung
        ))->save();
        
        $modelDA = new Model_TourenDispoAuftraege();
        $modelDA->refreshTourFinishCount($tour->Mandant, $tour->Auftragsnummer);
        $modelDA->refreshTourNeulieferungenCount($tour->Mandant, $tour->Auftragsnummer);        
        return $reId;
    }
    
    public function opentourabschluss($id) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
            
        $tour = $this->getStorage()->find($id)->current();

//        $db->query('UPDATE ' . $this->getStorage()->info(Zend_Db_Table::NAME) 
//                  .' SET tour_abgeschlossen_am   = NULL, ' 
//                  .'     tour_abgeschlossen_user = NULL ' 
//                  .' WHERE ' . implode(',',array_slice($this->getStorage()->info(Zend_Db_Table::PRIMARY),0,1)) . ' = ' . (int)$id
//                );

        $tour->setFromArray(array(
            'tour_abgeschlossen_am'   => new Zend_Db_Expr("NULL"),
            'tour_abgeschlossen_user' => new Zend_Db_Expr("NULL")
        ))->save();
        
        // Auftrags-Statistik aktualisieren
        $sql = 'SELECT COUNT(1) FROM ' . $this->getStorage()->info(Zend_Db_Table::NAME)
              .' WHERE '
              .' Mandant = ' . $db->quote($tour->Mandant)  
              .' AND Auftragsnummer = ' . $db->quote($tour->Auftragsnummer)
              .' AND tour_abgeschlossen_user IS NOT NULL';
        
        $count = $db->fetchOne($sql);
        
        $da = new Model_Db_TourenDispoAuftraege;
        $da->find($tour->Mandant, $tour->Auftragsnummer)->current()->setFromArray(array(
            'tour_abschluss_count' => $db->quote($count)
        ))->save();
        return;
    }
    
    
    
    public function finishtourzeitenabschluss($id, $username) 
    {
        $tour = $this->getStorage()->find($id)->current();
        
        $reId = $tour->setFromArray(array(
            'zeiten_erfasst_am'   => new Zend_Db_Expr("NOW()"),
            'zeiten_erfasst_user' => $username,
        ))->save();        
        return $reId;
    }
    
    public function opentourzeitenabschluss($id) 
    {            
        $tour = $this->getStorage()->find($id)->current();

        $reId = $tour->setFromArray(array(
            'zeiten_erfasst_am'   => new Zend_Db_Expr("NULL"),
            'zeiten_erfasst_user' => new Zend_Db_Expr("NULL")
        ))->save();      
        return $reId;
    }
    
    public function finishdispositionen($id, $username) 
    {
        $tour = $this->getStorage()->find($id)->current();
        $reId = $tour->setFromArray(array(
            'tour_disponiert_am' => new Zend_Db_Expr("NOW()"),
            'tour_disponiert_user' => $username
        ))->save();
        
        $modelDA = new Model_TourenDispoAuftraege();
        $modelDA->refreshTourDispoCount($tour->Mandant, $tour->Auftragsnummer);
        
        return $reId;
    }
    
    public function finishdispoByAuftragsnr($auftragsnr, $mandant, $username)
    {
        $storage = $this->getStorage();
        $db = $this->getStorage()->getAdapter();
        
        $select = $this->getStorage()->select(false)->from(
            $storage->info(Zend_Db_Table::NAME), 
            $storage->info(Zend_Db_Table::PRIMARY)
        )->where(
            'Auftragsnummer = :auftragsnr AND Mandant = :mandant'
           .' AND (tour_disponiert_user is null OR tour_disponiert_user LIKE ' . $db->quote('') . ')'
        )->bind(array(
            'auftragsnr' => $auftragsnr,
            'mandant'    => $mandant,          
        ));
        
        try {
            $tour_ids = $db->fetchCol($select, NULL, Zend_Db::FETCH_ASSOC);
            foreach($tour_ids as $_id) {
                $this->finishdispositionen($_id, $username);
            }
//            die(Zend_Debug::dump($tour_ids, null, false) . PHP_EOL . $select->assemble() );
        } catch(Exception $e) {
            throw $e;
        }
        return true;
    }
    
    public function opendispositionen($id) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
            
        $tour = $this->getStorage()->find($id)->current();
        $reId = $tour->setFromArray(array(
            'tour_disponiert_am'   => null, // new Zend_Db_Expr("NULL"), // 
            'tour_disponiert_user' => null, // new Zend_Db_Expr("NULL"), // 
        ))->save();
        
        // Auftrags-Statistik aktualisieren
        $sql = 'SELECT COUNT(1) FROM ' . $this->getStorage()->info(Zend_Db_Table::NAME)
              .' WHERE '
              .' Mandant = ' . $db->quote($tour->Mandant)  
              .' AND Auftragsnummer = ' . $db->quote($tour->Auftragsnummer)
              .' AND tour_disponiert_user IS NOT NULL';
        
        $dispo_count = $db->fetchOne($sql);
        
        $da = new Model_Db_TourenDispoAuftraege;
        $da->find($tour->Mandant, $tour->Auftragsnummer)->current()->setFromArray(array(
            'tour_dispo_count' => $db->quote($dispo_count)
        ))->save();
        return;
    }

    /**
     * @param int $id
     * @return string
     * @throws Zend_Db_Table_Exception
     */
    public function getDatum(int $id): string {
        $sql = 'SELECT DatumVon FROM ' . $this->getTable() . ' WHERE ' . $this->key() . ' = ' . (int)$id;

        return $this->_db->fetchOne( $sql );
    }
    
    public function update(array $data, $id) {
        $tourData = $this->fetchEntry($id);

        if (empty($tourData)) {
            $this->_error = 'Tour mit ID ' . $id . ' konnte nicht gefunden werden!';
            return false;
        }
        
        $checkResources = false;
        $slotFields = array('DatumVon', 'DatumBis', 'ZeitVon', 'ZeitBis');
        
        $timeValidator = new MyProject_Validate_Time();
        $dateValidator = new MyProject_Validate_Date();
        
        foreach($data as $_f => $_d) {
            switch($_f) {
                case 'tour_disponiert_am':
                case 'tour_abgeschlossen_am':
                    break;
                
                case 'tour_disponiert_user':
                case 'tour_abgeschlossen_user':
                    break;
            }
        }
        
        foreach($slotFields as $_f) {
            if (array_key_exists($_f, $data)) { 
                if (substr($_f,0,4)=='Zeit' && !$timeValidator->isValid($data[$_f])) {
                    $this->_error = "Tour konnte wegen ungueltiger Zeitwerte nicht aktualisiert werden:" . PHP_EOL . $_f . ': ' . $data[$_f];
                    return false;
                } 
                if (substr($_f,0,4)=='Datum' && !$dateValidator->isValid($data[$_f])) {
                    $this->_error = "Tour konnte wegen ungueltiger Datumswerte nicht aktualisiert werden:" . PHP_EOL . $_f . ': ' . $data[$_f];
                    return false;
                }
                if ($data[$_f] != $tourData[$_f]) {
                    $checkResources = true;
                }
            } else {
                $data[$_f] = $tourData[$_f];
            }            
        }
        
        if (!empty($data['timeline_id']) && !empty($tourData['timeline_id'])
            && $data['timeline_id'] != $tourData['timeline_id']) {
            $this->removeAllResources($id);
        }
        
        if ($checkResources) {
            $validation = $this->checkResources(
                            $id, 
                            $data['DatumVon'], 
                            $data['ZeitVon'],
                            $data['DatumBis'], 
                            $data['ZeitBis']);
            
            if (!$validation->ok) {
                $this->_error = "Tour konnte wegen Resourcen-Ueberschneidungen nicht aktualisiert werden!";
                // . PHP_EOL . $validation->msg;
                return false;
            }            
        }
        
        
        $re = parent::update($data, $id);
        
        if (!empty($data['timeline_id']) && !empty($tourData['timeline_id'])
            && $data['timeline_id'] != $tourData['timeline_id']) {
            $this->addDefaultResources($id);
        }
        return $re;
    }

    public function getFullDayData($date, $lager_id) {
        return $this->getFullDayData3($date, $lager_id);
    }

    public function getFullDayData3($date, $lager_id)
    {
        $db = $this->_db;

        $return = (object)[ 'data' => null, 'error' => null];

        $portletTbl = Model_Db_TourenPortlets::obj()->tableName();
        $timelineTbl = Model_Db_TourenTimelines::obj()->tableName();
        $tourenTbl = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $tourMaTbl = Model_Db_TourenDispoMitarbeiter::obj()->tableName();
        $tourFpTbl = Model_Db_TourenDispoFuhrpark::obj()->tableName();
        $tourWzTbl = Model_Db_TourenDispoWerkzeug::obj()->tableName();
        $tourAuftTbl = Model_Db_TourenDispoAuftraege::obj()->tableName();
        $akTbl = Model_Db_Vorgaenge::obj()->tableName();
        $maTbl = Model_Db_Mitarbeiter::obj()->tableName();
        $fpTbl = Model_Db_Fuhrpark::obj()->tableName();
        $wzTbl = Model_Db_Werkzeug::obj()->tableName();

        $maLabelExp = (new Model_Mitarbeiter())->getSqlSelectExprAsLabel();
        $fpLabelExp = (new Model_Fuhrpark())->getSqlSelectExprAsLabel();
        $wzLabelExp = (new Model_Werkzeug())->getSqlSelectExprAsLabel();

        $_hday = MyProject_Date_Holidays::getHolidayByDate($date);
        if ( !$_hday ) {
            $aHDay['holiday'] = '';
            $aHDay['holiday_frei'] = 0;
            $aHDay['holiday_halb'] = 0;
            $aHDay['holiday_only'] = '';
        } else {
            $aHDay['holiday'] = $_hday['name'];
            $aHDay['holiday_frei'] = $_hday['frei'];
            $aHDay['holiday_halb'] = $_hday['halb'];
            $aHDay['holiday_only'] = $_hday['only'];
        }

        $pFields = array_flip([
                                'portlet_id', 'lager_id', 'date', 'datum',
                                'position', 'tagesnr', 'topcustom', 'title']);
        $tlFields = array_flip([
                                'portlet_id', 'timeline_id', 'start', 'end', 'interval', 'stepWidth', 'tl_position']);
        $tFields = array_flip([
                                'id','tour_id', 'Mandant', 'Auftragsnummer', 'DatumVon', 'DatumBis', 'ZeitVon', 'ZeitBis',
                                'IsDefault', 'avisiert', 'farbklasse', 'locked', 'name', 'Vorgangstitel',
                                'LieferungName', 'LieferungOrt', 'dispoStatus',
                                'created_uid', 'modified_uid', 'created_role', 'modified_role'
        ]);

        $portletSql = "SELECT 
              p.portlet_id, p.lager_id, p.datum date, p.datum, 
              p.position, p.tagesnr, p.topcustom, p.title,
              
              tl.timeline_id, tl.position tl_position, tl.start, tl.end, tl.interval, tl.title tl_title, tl.interval stepWidth,
              
              t.tour_id id, t.tour_id, t.Mandant, t.Auftragsnummer, t.DatumVon, t.DatumBis, t.ZeitVon, t.ZeitBis,
              t.IsDefault, t.avisiert, t.farbklasse, t.locked,
              
              CONCAT(ak.Auftragsnummer,', ',ak.LieferungOrt) name, 
              ak.Vorgangstitel, ak.LieferungName, ak.LieferungOrt,
              
              CASE 
                WHEN t.DatumVon is null 
                THEN 'neu' 
                
                WHEN t.DatumVon is not null  
                    AND ta.auftrag_disponiert_am IS NULL  
                    AND (ta.tour_dispo_count IS NULL OR ta.tour_dispo_count = 0)
                THEN 'beauftragt'
                
                WHEN t.zeiten_erfasst_am IS NOT NULL 
                THEN 'fertig'
                
                WHEN ta.auftrag_abgeschlossen_am IS NULL 
                    AND ta.tour_neulieferungen_count > 0
                THEN 'neulieferung'
                
                WHEN ta.auftrag_disponiert_am IS NULL  
                    AND ta.tour_dispo_count IS NOT NULL 
                    AND ta.tour_dispo_count > 0
                THEN 'teil'
                
                WHEN ta.auftrag_disponiert_am IS NOT NULL
                THEN 'teil'
                
                ELSE
                   -- nicht bestimmbar
                   NULL
              END AS dispoStatus,
              t.created_uid,
              t.modified_uid,
              cu.user_role created_role,
              mu.user_role modified_role
              
              FROM $portletTbl p
              JOIN $timelineTbl tl 
                ON (datum=:datum AND lager_id=:lager_id AND p.portlet_id = tl.portlet_id)
              JOIN $tourenTbl t 
                ON (tl.timeline_id = t.timeline_id)
              LEFT JOIN $akTbl ak 
                ON (t.Mandant = ak.Mandant AND t.Auftragsnummer = ak.Auftragsnummer)
              LEFT JOIN $tourAuftTbl ta
                ON (t.Mandant = ta.Mandant AND t.Auftragsnummer = ta.Auftragsnummer)
              LEFT JOIN mr_user cu 
                ON (t.created_uid = cu.user_id) 
              LEFT JOIN mr_user mu 
                ON (t.modified_uid = mu.user_id) 
              ORDER BY p.position, p.portlet_id, tl_position, tl.timeline_id, t.ZeitVon";

        $sql = strtr($portletSql, [':datum'=>$db->quote($date), ':lager_id'=>(int)$lager_id]);
        $rows = $db->fetchAll($sql);
        $aData = [];
        $lastPId = 0;
        $lastTLId = 0;
        $lastP = null;
        $lastTL = null;
        $lastT = null;
        $_pix = -1;
        $_tlix = -1;
        $_tix = -1;
        $aTourId2Row = [];
        foreach($rows as $row) {
            if ($lastPId != $row['portlet_id']) {
                $lastPId = $row['portlet_id'];
                ++$_pix;
                $_tlix = -1;
                $aData[$_pix] = ['id'=>$lastPId] + $aHDay + array_intersect_key($row, $pFields);
                $lastP = &$aData[$_pix];

            }
            if ($lastTLId != $row['timeline_id']) {
                $lastTLId = $row['timeline_id'];
                ++$_tlix;
                $_tix = -1;
                $lastP['timelines'][$_tlix] = ['id'=>$lastTLId, 'title'=>$row['tl_title']] + array_intersect_key($row, $tlFields);
                $lastTL = &$lastP['timelines'][$_tlix];
            }
            ++$_tix;
            $_id = $row['tour_id'];
            $lastTL['touren'][$_tix] = array_intersect_key($row, $tFields);
            $aTourId2Row[ $_id ] = &$lastTL['touren'][$_tix];

        }

        if (0) MyProject_Response_Json::send([
                'sql' => $sql,
                'rows' => $rows,
                'aData' => $aData,
            ]);

        $rsrcSql = "
            SELECT 
             tm.tour_id, 'MA' resourceType, tm.id, $maLabelExp label, $maLabelExp name, 
             m.mid resourceId, extern_id, leistungs_id, tmt.einsatz_ab,
             m.name nachname, '' kennzeichen, '' fahrzeugart, '' modell, '' bezeichnung
            FROM $tourMaTbl tm JOIN $maTbl m ON (tm.mitarbeiter_id = m.mid)
            LEFT JOIN mr_touren_dispo_mitarbeiter_txt tmt ON(tm.id = tmt.id)
            WHERE tm.tour_id IN (:tour_ids)
            UNION SELECT 
             tf.tour_id, 'FP' resourceType, tf.id, $fpLabelExp label, $fpLabelExp name, 
             f.fid resourceId, f.extern_id, f.leistungs_id, '' einsatz_ab,
            '' nachname, f.kennzeichen, f.fahrzeugart, f.modell, '' bezeichnung
            FROM $tourFpTbl tf JOIN $fpTbl f ON (tf.fuhrpark_id = f.fid)
            WHERE tf.tour_id IN (:tour_ids)
            UNION SELECT 
             tw.tour_id, 'WZ' resourceType, id, $wzLabelExp label, $wzLabelExp name, 
             w.wid resourceId, w.extern_id, w.leistungs_id, '' einsatz_ab,
            '' nachname, '' kennzeichen, '' fahrzeugart, '' modell, w.bezeichnung
            FROM $tourWzTbl tw JOIN $wzTbl w ON (tw.werkzeug_id = w.wid)
            WHERE tw.tour_id IN (:tour_ids)
            ORDER BY tour_id, resourceType, resourceId
        ";

        $rows = count($aTourId2Row)
                ? $db->fetchAll( str_replace(':tour_ids', implode(',', array_keys($aTourId2Row)), $rsrcSql))
                : [];

        foreach($rows as $row ) {
            $_tid = $row['tour_id'];
            $_rid = $row['resourceId'];
            switch($row['resourceType']) {
                case 'MA':
                    $row['mid'] = $_rid;
                    $row['mitarbeiter_id'] = $_rid;
                    break;
                case 'FP':
                    $row['fid'] = $_rid;
                    $row['fuhrpark_id'] = $_rid;
                    break;
                case 'WZ':
                    $row['wid'] = $_rid;
                    $row['werkzeug_id'] = $_rid;
            }
            $aTourId2Row[ $_tid ]['resources'][] = $row;
        }

        // return MyProject_Response_Json::send( ['sql'=>$sql, 'data'=>$aData] );

        $return->data = $aData;
        return $return;
    }

    public function getFullDayData0($date, $lager_id)
    {
        $return = new stdClass();
        $return->data  = null;
        $return->error = null;

        $NAME = Zend_Db_Table::NAME;
        $tblDV = $this->getStorage()->info($NAME);

        $tblDA = self::getStorageByClass('TourenDispoAuftraege')->info($NAME);
        $tblAK = self::getStorageByClass('Vorgaenge')->info($NAME);
        
//        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);        
//        $tblAP = self::getStorageByClass('Auftragspositionen')->info($NAME);        
//        $tblBK = self::getStorageByClass('Bestellkoepfe')->info($NAME);        
//        $tblBP = self::getStorageByClass('Bestellpositionen')->info($NAME);        
//        $tblDP = self::getStorageByClass('TourenDispoPositionen')->info($NAME);

        $tblDF = self::getStorageByClass('TourenDispoFuhrpark')->info($NAME);
        $tblDM = self::getStorageByClass('TourenDispoMitarbeiter')->info($NAME);        
        $tblMT = self::getStorageByClass('TourenDispoMitarbeiterText')->info($NAME);        
        $tblDW = self::getStorageByClass('TourenDispoWerkzeug')->info($NAME);        
        $tblTP = self::getStorageByClass('TourenPortlets')->info($NAME);        
        $tblTL = self::getStorageByClass('TourenTimelines')->info($NAME);        
        $tblFP = self::getStorageByClass('Fuhrpark')->info($NAME);        
        $tblMA = self::getStorageByClass('Mitarbeiter')->info($NAME);        
        $tblWZ = self::getStorageByClass('Werkzeug')->info($NAME);        
        $tblUsr = self::getStorageByClass('User')->info($NAME);
        
        try {
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_ASSOC);
            
                    
            $sqlRsrc['FP'] = 
                   "SELECT tr.id, 'FP' resourceType, "
                  ." r.fid,r.extern_id,r.leistungs_id,r.standort,r.kennzeichen,r.hersteller,r.modell,r.fahrzeugart,FKL, "
                  ." CONCAT(ifnull(r.kennzeichen,''),' ',ifnull(r.fahrzeugart,'')) name "
                  ." FROM $tblDF tr "
                  ." LEFT JOIN $tblFP r ON tr.fuhrpark_id = r.fid "
                  ." WHERE tr.tour_id = :tour_id";
            
            $sqlRsrc['MA'] = 
                   "SELECT tr.id, 'MA' resourceType, "
                  ." r.mid, r.anrede, r.extern_id, r.leistungs_id, r.standort, "
                  ." CONCAT(ifnull(r.vorname,''), ' ', ifnull(r.name,''), ' [', ifnull(r.eingestellt_als,''), ']') name, name nachname, "
                  ." tt.einsatz_ab, tt.bemerkung "
                  ."FROM $tblDM tr "
                  ."LEFT JOIN $tblMT tt ON tr.id = tt.id "
                  ."LEFT JOIN $tblMA r ON tr.mitarbeiter_id = r.mid "
                  ."WHERE tr.tour_id = :tour_id";

            $sqlRsrc['WZ'] =
                   "SELECT tr.id, 'WZ' resourceType, r.*, bezeichnung name "
                  ."FROM $tblDW tr "
                  ."LEFT JOIN $tblWZ r ON tr.werkzeug_id = r.wid "
                  ."WHERE tr.tour_id = :tour_id";
            
            if (1) {
             $sql = "SELECT "
                // Portlet-Felder
                . "p.portlet_id, " . PHP_EOL
                 ."p.lager_id p_lager_id, " . PHP_EOL
                 ."p.datum p_datum, " . PHP_EOL
                 ."p.position p_position, " . PHP_EOL
                 ."p.tagesnr p_tagesnr, " . PHP_EOL
                 ."p.title p_title, " . PHP_EOL
                 ." p.topcustom p_topcustom, "

                // Timeline-Felder
                 .' tl.timeline_id, ' . PHP_EOL
                 . "tl.timeline_id tl_timeline_id, " . PHP_EOL
                 ." tl.position tl_position, " . PHP_EOL
                 ."tl.locked tl_locked, " . PHP_EOL
                 ."tl.start tl_start, " . PHP_EOL
                 ."tl.end tl_end, " . PHP_EOL
                 ."tl.interval tl_interval, " . PHP_EOL
                 ."tl.title tl_title, " . PHP_EOL
                 ." `interval` tl_stepWidth, "

                // Touren-Felder
                // ." DV.*, " . PHP_EOL
                 ." DV.tour_id, " . PHP_EOL
                 ." DV.Mandant, " . PHP_EOL
                 ." DV.Auftragsnummer, " . PHP_EOL
                 ." DV.ZeitVon, " . PHP_EOL
                 ." DV.ZeitBis, " . PHP_EOL
                 ." DV.DatumVon, " . PHP_EOL
                 ." DV.DatumBis, " . PHP_EOL
                 ." DV.IsDefault, " . PHP_EOL
                 ." DV.avisiert, " . PHP_EOL
                 ." DV.farbklasse, " . PHP_EOL
                 ." DV.locked, " . PHP_EOL
                 ." CONCAT(A.Auftragsnummer,', ',A.LieferungOrt) name, " . PHP_EOL
                 ." A.LieferterminFix, " . PHP_EOL
                 ." A.LieferungName, " . PHP_EOL
                 ." A.Vorgangstitel, "
                 ." A.LieferungOrt, " . PHP_EOL
                 ." A.LieferterminHinweisText, ". PHP_EOL
                 ." DA.auftrag_disponiert_user, " . PHP_EOL
                 ." DA.auftrag_disponiert_am, " . PHP_EOL
                 ." DA.auftrag_abgeschlossen_user, " . PHP_EOL
                 ." DA.auftrag_abgeschlossen_am, " . PHP_EOL
                 ." DA.tour_dispo_count, " . PHP_EOL
                 ." DA.tour_abschluss_count, " . PHP_EOL

                 .'CASE '
                 .' WHEN DV.DatumVon is null ' . PHP_EOL
                 .'  THEN \'neu\' '             . PHP_EOL
                 .' WHEN DV.DatumVon is not null  AND DA.auftrag_disponiert_am IS NULL  AND (DA.tour_dispo_count IS NULL OR DA.tour_dispo_count = 0) ' . PHP_EOL
                 .'  THEN \'beauftragt\' '      . PHP_EOL
                 .' WHEN DV.zeiten_erfasst_am IS NOT NULL ' . PHP_EOL
                 .'  THEN \'fertig\' '          . PHP_EOL
                 .' WHEN DA.auftrag_abgeschlossen_am IS NULL AND DA.tour_neulieferungen_count > 0 ' . PHP_EOL
                 .'  THEN \'neulieferung\' '          . PHP_EOL
                 .' WHEN DA.auftrag_disponiert_am IS NULL  AND DA.tour_dispo_count IS NOT NULL AND DA.tour_dispo_count > 0 ' . PHP_EOL
                 .'  THEN \'teil\' '            . PHP_EOL
                 .' WHEN DA.auftrag_disponiert_am IS NOT NULL ' . PHP_EOL
                 .'  THEN \'teil\' '          . PHP_EOL
                 .' ELSE '                     . PHP_EOL
                 .'  -- nicht bestimmbar '      . PHP_EOL
                 .'  NULL '                     . PHP_EOL
                 .'END AS dispoStatus, '      . PHP_EOL

                 ." DV.created_uid, " . PHP_EOL
                 ." DV.modified_uid, " . PHP_EOL
                 .' cu.user_role created_role,' . PHP_EOL
                 .' mu.user_role modified_role' . PHP_EOL

                 ." FROM " . $tblTP . " p " //mr_touren_portlets "
                 ." LEFT JOIN " . $tblTL . " tl ON p.portlet_id = tl.portlet_id "
                 ." LEFT JOIN " . $tblDV . " DV ON tl.timeline_id = DV.timeline_id"
                 ." LEFT JOIN $tblAK A  ON(DV.Mandant = A.Mandant AND DV.Auftragsnummer = A.Auftragsnummer) " . PHP_EOL
                 ." LEFT JOIN $tblDA DA ON(DV.Mandant = DA.Mandant AND DV.Auftragsnummer = DA.Auftragsnummer) " . PHP_EOL
                 ." LEFT JOIN $tblUsr cu ON (DV.created_uid = cu.user_id) " . PHP_EOL
                 ." LEFT JOIN $tblUsr mu ON (DV.modified_uid = mu.user_id) " . PHP_EOL
                 ." WHERE p.datum = :date AND p.lager_id = :lager_id "
                 ." ORDER BY p_position, p.portlet_id, tl_position, tl.timeline_id, ZeitVon";
            
//            die('#'.__LINE__ . ' <pre>' . strtr($sql, array(':date'=>$date, ':lager_id'=>$lager_id)) . '</pre>' .PHP_EOL);
            $rows =  $db->fetchAll($sql, array(':date'=>$date, ':lager_id'=>$lager_id));
			// die( '<pre>' . print_r($rows, 1) . '</pre>' );
            
            $lastPid = -1;
            $lastTlid= -1;
            
            $P = array();
            foreach($rows as $row) {
                $pid = $row['portlet_id'];
                $tlid = $row['timeline_id'];
                
                if ($lastPid != $pid) {
                    $p = count($P);
                    $P[$p] = array('id' => $pid, 'portlet_id' => $pid);
                    foreach($row as $k => $v) 
                        if (substr($k, 0, 2) == 'p_') $P[$p][substr($k, 2)] = $v;
                    
                    $_hday = MyProject_Date_Holidays::getHolidayByDate($P[$p]['datum']);
                    if ( !$_hday ) {
                        $P[$p]['holiday'] = '';
                        $P[$p]['holiday_frei'] = 0;
                        $P[$p]['holiday_halb'] = 0;
                        $P[$p]['holiday_only'] = '';
                    } else {
                        $P[$p]['holiday'] = $_hday['name'];
                        $P[$p]['holiday_frei'] = $_hday['frei'];
                        $P[$p]['holiday_halb'] = $_hday['halb'];
                        $P[$p]['holiday_only'] = $_hday['only'];
                    }
                    
                    $P[$p]['timelines'] = array();
                }
                
                if ($lastTlid != $tlid) {
                    $tl = count($P[$p]['timelines']);
                    $P[$p]['timelines'][$tl] = array('id' => $tlid,'timeline_id' => $tlid,'portlet_id' => $pid);
                    foreach($row as $k => $v) if (substr($k, 0, 3) == 'tl_'){
                        $P[$p]['timelines'][$tl][substr($k, 3)] = $v;
                    }
                    
                    $P[$p]['timelines'][$tl]['touren'] = array();
                }
                
				if ($row['tour_id']) {
					$t = count($P[$p]['timelines'][$tl]['touren']);
					$P[$p]['timelines'][$tl]['touren'][$t] = array('id' => $row['tour_id']);
					
					foreach($row as $k => $v) {
					    if (substr($k, 0, 2) !== 'p_' && substr($k, 0, 3) !== 'tl_'){
					        $P[$p]['timelines'][$tl]['touren'][$t][$k] = $v;
                        }
                    }
					
					$P[$p]['timelines'][$tl]['touren'][$t]['resources'] = array();
					
					foreach($sqlRsrc as $_r => $_sql) {
						$stmt = $db->query( $_sql, array(':tour_id' => $row['tour_id']) );
						while($rsrc = $stmt->fetch()) {
							$P[$p]['timelines'][$tl]['touren'][$t]['resources'][] = $rsrc;
						}
					}
				}
                    
                $lastPid = $pid;
                $lastTlid = $tlid;
            }
            $return->data = $P;
            }
            else {
            $sqlPortlets = 
                    "SELECT * FROM " . $tblTP . " " //mr_touren_portlets "
                   ."WHERE datum = :date AND lager_id = :lager_id "
                   ."ORDER BY position"; 
            
            $sqlTimelines = 
                     "SELECT *, `interval` stepWidth FROM " . $tblTL . " " // mr_touren_timelines "
                    ."WHERE portlet_id = :portlet_id "
                    ."ORDER BY position";
            
            $sqlTouren =
                     "SELECT "
                    ." DV.*, " . PHP_EOL
                    ." CONCAT(Auftragsnummer,', ',A.LieferungOrt) name, " . PHP_EOL
                    ." A.LieferterminFix, " . PHP_EOL
                    ." A.LieferungName, "
                    ." A.LieferungOrt, " . PHP_EOL
                    ." A.LieferterminHinweisText, ". PHP_EOL
                    ." DA.auftrag_disponiert_user, " . PHP_EOL
                    ." DA.auftrag_disponiert_am, " . PHP_EOL
                    ." DA.auftrag_abgeschlossen_user, " . PHP_EOL
                    ." DA.auftrag_abgeschlossen_am, " . PHP_EOL
                    ." DA.tour_dispo_count, " . PHP_EOL
                    ." DA.tour_abschluss_count, " . PHP_EOL
                    
                    .'CASE '
                    .' WHEN DV.DatumVon is null ' . PHP_EOL
                    .'  THEN \'neu\' '             . PHP_EOL
                    .' WHEN DV.DatumVon is not null  AND DA.auftrag_disponiert_am IS NULL  AND (DA.tour_dispo_count IS NULL OR DA.tour_dispo_count = 0) ' . PHP_EOL
                    .'  THEN \'beauftragt\' '      . PHP_EOL
                    .' WHEN DA.auftrag_abgeschlossen_am IS NULL AND DA.tour_neulieferungen_count > 0 ' . PHP_EOL
                    .'  THEN \'neulieferung\' '          . PHP_EOL
                    .' WHEN DA.auftrag_disponiert_am IS NULL  AND DA.tour_dispo_count IS NOT NULL AND DA.tour_dispo_count > 0 ' . PHP_EOL
                    .'  THEN \'teil\' '            . PHP_EOL
                    .' WHEN DA.auftrag_disponiert_am IS NOT NULL ' . PHP_EOL
                    .'  THEN \'fertig\' '          . PHP_EOL
                    .' ELSE '                     . PHP_EOL
                    .'  -- nicht bestimmbar '      . PHP_EOL
                    .'  NULL '                     . PHP_EOL
                    .'END AS dispoStatus '       . PHP_EOL
                    
                    ."FROM " . $tblDV . " DV " . PHP_EOL // mr_touren_dispo_vorgaenge T "
                    ."LEFT JOIN $tblAK A  USING(Mandant,Auftragsnummer) " . PHP_EOL
                    ."LEFT JOIN $tblDA DA USING(Mandant,Auftragsnummer) " . PHP_EOL
                    ."WHERE timeline_id = :timeline_id " . PHP_EOL
                    ."ORDER BY ZeitVon";

            
            
            $portlets = $db->fetchAll($sqlPortlets, array(':date'=>$date, ':lager_id'=>$lager_id));
            
            $return->data = &$portlets;
            
            foreach($portlets as $pi => $_p) {
                $portlets[$pi]['id'] = $_p['portlet_id'];
                $portlets[$pi]['timelines'] = $db->fetchAll($sqlTimelines, array(':portlet_id'=>$_p['portlet_id']));
//                die('<pre>#' . __LINE__ . ' ' . __METHOD__ . ' ' . print_r($portlets[$pi]['timelines'], 1) . '</pre>');
                $timelines = &$portlets[$pi]['timelines'];
                        
                $_hday = MyProject_Date_Holidays::getHolidayByDate($portlets[$pi]['datum']);
                if ( !$_hday ) {
                    $portlets[$pi]['holiday'] = '';
                    $portlets[$pi]['holiday_frei'] = 0;
                    $portlets[$pi]['holiday_halb'] = 0;
                    $portlets[$pi]['holiday_only'] = '';
                } else {
                    $portlets[$pi]['holiday'] = $_hday['name'];
                    $portlets[$pi]['holiday_frei'] = $_hday['frei'];
                    $portlets[$pi]['holiday_halb'] = $_hday['halb'];
                    $portlets[$pi]['holiday_only'] = $_hday['only'];
                }
                
                foreach($timelines as $tli => $_tl) {
                    $timelines[$tli]['id'] = $_tl['timeline_id'];
                    $timelines[$tli]['touren'] = $db->fetchAll($sqlTouren, array(':timeline_id'=>$_tl['timeline_id']));
                    
                    foreach($timelines[$tli]['touren'] as $vi => $_tour) {
                        $timelines[$tli]['touren'][$vi]['id'] = $_tour['tour_id'];
                        $timelines[$tli]['touren'][$vi]['resources'] = array();
                        $resources = &$timelines[$tli]['touren'][$vi]['resources'];
                        
                        $_hday = MyProject_Date_Holidays::getHolidayByDate($timelines[$tli]['touren'][$vi]['DatumVon']);
                        if ( !$_hday ) {
                            $timelines[$tli]['touren'][$vi]['holiday'] = '';
                            $timelines[$tli]['touren'][$vi]['holiday_frei'] = 0;
                            $timelines[$tli]['touren'][$vi]['holiday_halb'] = 0;
                            $timelines[$tli]['touren'][$vi]['holiday_only'] = '';
                        } else {
                            $timelines[$tli]['touren'][$vi]['holiday'] = $_hday['name'];
                            $timelines[$tli]['touren'][$vi]['holiday_frei'] = $_hday['frei'];
                            $timelines[$tli]['touren'][$vi]['holiday_halb'] = $_hday['halb'];
                            $timelines[$tli]['touren'][$vi]['holiday_only'] = $_hday['only'];
                        }
                        
                        foreach($sqlRsrc as $_sql) {
                            $stmt = $db->query( $_sql, array(':tour_id' => $_tour['tour_id']) );
                            while($row = $stmt->fetch()) $resources[] = $row;                                
                        }
                    }
                }
            }
            }
            return $return;
        } catch(Exception $e) {
            $return->error = new stdClass();
            $return->error->code     = $e->getCode();
            $return->error->file     = $e->getFile();
            $return->error->line     = $e->getLine();
            $return->error->message  = $e->getMessage();
            $return->error->previous = $e->getPrevious();
            $return->error->trace    = $e->getTrace();
            $return->error->traceAsString = $e->getTraceAsString();
            return $return;
        }
    }


    public function getVorgangsResourcenDefaults(int $tour_id)
    {
        $tourenTbl = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $tourMaTbl = Model_Db_TourenDispoMitarbeiter::obj()->tableName();
        $tourFpTbl = Model_Db_TourenDispoFuhrpark::obj()->tableName();
        $tourWzTbl = Model_Db_TourenDispoWerkzeug::obj()->tableName();
        $maTbl = Model_Db_Mitarbeiter::obj()->tableName();
        $fpTbl = Model_Db_Fuhrpark::obj()->tableName();
        $wzTbl = Model_Db_Werkzeug::obj()->tableName();

        $vorgaengeSql = "
            SELECT t2.*
            FROM $tourenTbl t1
            JOIN $tourenTbl t2 ON (t1.tour_id = $tour_id AND t1.IsDefault = 1 AND t1.timeline_id = t2.timeline_id)
            ORDER BY t2.IsDefault DESC, t2.ZeitVon
        ";

        $aReData = [
            'default' => [],
            'defaultResources' => [ 'FP' => [], 'MA' => [], 'WZ' => []],
            'timeline' => [],
            'vorgaenge' => [],
        ];

        $aRows = $this->_db->fetchAll($vorgaengeSql, [], Zend_Db::FETCH_ASSOC);

        if (empty($aRows)) {
            return $aReData;
        }

        $fpLabelExp = Model_Fuhrpark::getSingleton()->getSqlSelectExprAsLabel();
        $maLabelExp = Model_Mitarbeiter::getSingleton()->getSqlSelectExprAsLabel();
        $wzLabelExp = Model_Werkzeug::getSingleton()->getSqlSelectExprAsLabel();

        $aTourIds = array_map('intval', array_column($aRows, 'tour_id'));
        $defaultTourId = $aRows[0]['tour_id'];
        $aReData['default'] = $aRows[0];
        $aReData['vorgaenge'] = array_slice($aRows, 1);
        $aReData['timeline'] = Model_Db_TourenTimelines::get($aRows[0]['timeline_id']);

        $aTourId2Row = [];
        foreach($aReData['vorgaenge'] as &$_row) {
            $_row['resources'] = [];
            $aTourId2Row[ $_row['tour_id'] ] = &$_row;
        }


        $resourcenSql = "
            SELECT tm.tour_id, 'MA' resourceType, id, mid resourceId, 
            $maLabelExp label, 
            $maLabelExp name,
            vorname, anrede, eingestellt_als, '' kennzeichen, '' fahrzeugart, '' modell, '' bezeichnung
            FROM $tourMaTbl tm 
            JOIN $maTbl m ON (tm.mitarbeiter_id = m.mid)
            WHERE tm.tour_id IN(:tour_ids)
            UNION 
            SELECT tf.tour_id, 'FP' resourceType, id, fid resourceId, 
            $fpLabelExp label, 
            $fpLabelExp name,
            '' vorname, '' anrede, '' eingestellt_als, kennzeichen, fahrzeugart, modell, '' bezeichnung
            FROM $tourFpTbl tf 
            JOIN $fpTbl f ON (tf.fuhrpark_id = f.fid)
            WHERE tf.tour_id IN(:tour_ids)
            UNION 
            SELECT tw.tour_id, 'WZ' resourceType, id, wid resourceId, $wzLabelExp label, $wzLabelExp name,
            '' vorname, '' anrede, '' eingestellt_als, '' kennzeichen, '' fahrzeugart, '' modell, bezeichnung
            FROM $tourWzTbl tw 
            JOIN $wzTbl w ON (tw.werkzeug_id = w.wid)
            WHERE tw.tour_id IN(:tour_ids)
            ORDER BY tour_id, resourceType
        ";

        $aResources = count($aTourIds)
            ? $this->_db->fetchAll(str_replace(':tour_ids', implode(',', $aTourIds), $resourcenSql),
                                    [], Zend_Db::FETCH_ASSOC)
            : [];

        foreach($aResources as $_rsrc) {
            $_tid = $_rsrc['tour_id'];
            $_typ = $_rsrc['resourceType'];

            switch($_typ) {
                case 'MA':
                    $_rsrc['mid'] = $_rsrc['resourceId'];
                    $_rsrc['mitarbeiter_id'] = $_rsrc['resourceId'];
                    break;
                case 'FP':
                    $_rsrc['fid'] = $_rsrc['resourceId'];
                    $_rsrc['fuhrpark_id'] = $_rsrc['resourceId'];
                    break;
                case 'WZ':
                    $_rsrc['wid'] = $_rsrc['resourceId'];
                    $_rsrc['werkzeug_id'] = $_rsrc['resourceId'];
                    break;
            }

            if ($_tid == $defaultTourId) {
                $aReData['defaultResources'][ $_typ ][] = $_rsrc;
            } else {
                $aTourId2Row[$_tid]['resources'][$_typ][] = $_rsrc;
            }
        }

        return $aReData;
    }

    public function getCalendarweekdata(int $lager_id, $dateVon, $dateBis)
    {
        $tourenTbl = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $tourMaTbl = Model_Db_TourenDispoMitarbeiter::obj()->tableName();
        $tourFpTbl = Model_Db_TourenDispoFuhrpark::obj()->tableName();
        $tourWzTbl = Model_Db_TourenDispoWerkzeug::obj()->tableName();
        $maTbl = Model_Db_Mitarbeiter::obj()->tableName();
        $fpTbl = Model_Db_Fuhrpark::obj()->tableName();
        $wzTbl = Model_Db_Werkzeug::obj()->tableName();

        $aReObj = (object)[
            'touren' => [],
            'resources' => [],
        ];


        $fpLabelExp = Model_Fuhrpark::getSingleton()->getSqlSelectExprAsLabel();
        $maLabelExp = Model_Mitarbeiter::getSingleton()->getSqlSelectExprAsLabel();
        $wzLabelExp = Model_Werkzeug::getSingleton()->getSqlSelectExprAsLabel();

        $tblPT = Model_Db_TourenPortlets::obj()->tableName();
        $tblTL = Model_Db_TourenTimelines::obj()->tableName();
        $tblDV = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $tblAK = Model_Db_Vorgaenge::obj()->tableName();

        $sqlTouren =
            "SELECT DV.*, PT.*, PT.topcustom PortletTopCustom, PT.title PortletTitle, TL.*, TL.title TimelineTitle, A.* " . PHP_EOL
            ."FROM " . $tblPT . " PT " . PHP_EOL
            ."LEFT JOIN " . $tblTL . " TL ON PT.portlet_id  = TL.portlet_id " . PHP_EOL
            ."LEFT JOIN " . $tblDV . " DV ON TL.timeline_id = DV.timeline_id " . PHP_EOL
            ."LEFT JOIN " . $tblAK . " A  ON (DV.Mandant = A.Mandant AND DV.Auftragsnummer = A.Auftragsnummer) " . PHP_EOL

            ." WHERE " . PHP_EOL
            ." PT.lager_id = :lager_id AND PT.datum BETWEEN :DatumVon AND :DatumBis " . PHP_EOL
            ." AND (PT.title IS NOT NULL OR DV.IsDefault = 0) "  . PHP_EOL
            ."ORDER BY DV.timeline_id, ZeitVon";

        $aReObj->touren = $this->_db->fetchAll($sqlTouren, [
            'lager_id' => $lager_id,
            'DatumVon'=> $dateVon,
            'DatumBis'=> $dateBis
        ]);

        $aTourIds = array_map( 'intval', array_column($aReObj->touren, 'tour_id'));

        $resourcenSql = "
            SELECT tm.tour_id, 'MA' resourceType, id, mid resourceId, 
            $maLabelExp label, 
            $maLabelExp name,
            vorname, anrede, eingestellt_als, '' kennzeichen, '' fahrzeugart, '' modell, '' bezeichnung
            FROM $tourMaTbl tm 
            JOIN $maTbl m ON (tm.mitarbeiter_id = m.mid)
            WHERE tm.tour_id IN(:tour_ids)
            UNION 
            SELECT tf.tour_id, 'FP' resourceType, id, fid resourceId, 
            $fpLabelExp label, 
            $fpLabelExp name,
            '' vorname, '' anrede, '' eingestellt_als, kennzeichen, fahrzeugart, modell, '' bezeichnung
            FROM $tourFpTbl tf 
            JOIN $fpTbl f ON (tf.fuhrpark_id = f.fid)
            WHERE tf.tour_id IN(:tour_ids)
            UNION 
            SELECT tw.tour_id, 'WZ' resourceType, id, wid resourceId, $wzLabelExp label, $wzLabelExp name,
            '' vorname, '' anrede, '' eingestellt_als, '' kennzeichen, '' fahrzeugart, '' modell, bezeichnung
            FROM $tourWzTbl tw 
            JOIN $wzTbl w ON (tw.werkzeug_id = w.wid)
            WHERE tw.tour_id IN(:tour_ids)
            ORDER BY tour_id, resourceType
        ";
        $aResources = count($aTourIds)
            ? $this->_db->fetchAll( str_replace(':tour_ids', implode(',', $aTourIds), $resourcenSql),
                [], Zend_Db::FETCH_ASSOC)
            : [];

        $aReObj->tourResources = [];

        $lastTId = '';
        foreach($aResources as $_rsrc) {
            $_tid = $_rsrc['tour_id'];
            if ($_tid != $lastTId) {
                $aReObj->tourResources[$_tid] = ['MA' => [], 'FP' => [], 'WZ' => []];
                $lastTId = $_tid;
            }
            $_typ = $_rsrc['resourceType'];
            $aReObj->tourResources[ $_tid ][$_typ][] = $_rsrc;
        }

        return $aReObj;
    }


    public function getCalendarmonthdata(int $lager_id, DateTime $datumVon, DateTime $datumBis)
    {
        $db = $this->_db;
        
        $vorgaengeTbl = Model_Db_Vorgaenge::obj()->tableName();
        $tourTbl = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $timelineTbl = Model_Db_TourenTimelines::obj()->tableName();
        $portletTbl = Model_Db_TourenPortlets::obj()->tableName();

        $dateRange = [
            'Von' => new DateTime($datumVon->format('Y-m-d')),
            'Bis' => new DateTime($datumBis->format('Y-m-d')),
        ];

        $oRe = (object)['data' => []];

        $sqlTouren =
            "SELECT t.*, p.*, p.title PortletTitle, tl.*, tl.title TimelineTitle, v.* " . PHP_EOL
            ."FROM " . $portletTbl . " p " . PHP_EOL
            ."LEFT JOIN " . $timelineTbl . " tl ON p.portlet_id  = tl.portlet_id " . PHP_EOL
            ."LEFT JOIN " . $tourTbl . " t ON tl.timeline_id = t.timeline_id " . PHP_EOL
            ."LEFT JOIN " . $vorgaengeTbl . " v  ON (t.Mandant = v.Mandant AND t.Auftragsnummer = v.Auftragsnummer) " . PHP_EOL
            ." WHERE " . PHP_EOL
            ." p.lager_id = :lager_id " . PHP_EOL
            ." AND (" . PHP_EOL
            ." (p.title IS NOT NULL AND p.datum BETWEEN :DatumVon AND :DatumBis) "  . PHP_EOL
            ." OR (t.IsDefault = 0  "  . PHP_EOL
            ." AND t.DatumVon BETWEEN :DatumVon AND :DatumBis) "  . PHP_EOL
            ." )" . PHP_EOL
            ."ORDER BY t.timeline_id, ZeitVon";

        $oRe->data = $db->fetchAll($sqlTouren, [
            ':lager_id' => $lager_id,
            ':DatumVon' => $dateRange['Von']->format('Y-m-d'),
            ':DatumBis' => $dateRange['Bis']->format('Y-m-d'),
        ]);

        return $oRe;
    }
    
    /**
     * Liefert gruppiert in Unter-Array die Resourcen-Datens�tze
     * @param int $tour_id
     * @param bool $keysOnly
     * @return array data array(FP => array Rows, MA => array Rows, WZ => array Rows)
     */
    public function getResources($tour_id, $keysOnly = false) 
    {
        $rsrcs = array(
            'FP' => new Model_TourenDispoFuhrpark(),
            'MA' => new Model_TourenDispoMitarbeiter(),
            'WZ' => new Model_TourenDispoWerkzeug(),
        );
        $rsrcsData = array();
        
        /* @var $_model Model_TourenDispoResourceAbstract */
        foreach($rsrcs as $_key => $_model) {
            $rsrcsData[$_key] = $_model->getResourcesByTourId($tour_id, $keysOnly);
        }
        
        return $rsrcsData;        
    }
    
    public function getPortlet($tour_id)
    {
        if (!(int) $tour_id) return null;
        
        $NAME = Zend_Db_Table::NAME;
        $db = $this->_db;
        
        /** @var $modelTL Model_TourenTimelines */   
        $modelTL = MyProject_Model_Database::loadModel('tourenTimelines');
        
        /** @var $modelP Model_TourenPortlets */
        $modelP = MyProject_Model_Database::loadModel('tourenPortlets');
        
        
        
        $sql = 'SELECT p.* FROM ' . $this->getStorage()->info($NAME)
                .' LEFT JOIN ' . $modelTL->getStorage()->info($NAME) . ' tl USING(timeline_id)'
                .' LEFT JOIN ' . $modelP->getStorage()->info($NAME) . ' p ON(p.portlet_id = tl.portlet_id)'
                .' WHERE tour_id = ' . $db->quote((int)$tour_id);
        
        return $db->fetchRow($sql);
    }
    
    public function getPortletByTimelineId($timeline_id)
    {
        if (!(int) $timeline_id) return null;
        
        $NAME = Zend_Db_Table::NAME;
        $db = $this->_db;
        
        /** @var $modelTL Model_TourenTimelines */   
        $modelTL = MyProject_Model_Database::loadModel('tourenTimelines');
        
        /** @var $modelP Model_TourenPortlets */
        $modelP = MyProject_Model_Database::loadModel('tourenPortlets');
        
        $sql = 'SELECT p.* FROM ' . $modelTL->getStorage()->info($NAME) . ' tl ' . PHP_EOL
                .' LEFT JOIN ' . $modelP->getStorage()->info($NAME) . ' p ON(p.portlet_id = tl.portlet_id)' . PHP_EOL
                .' WHERE timeline_id = ' . $db->quote((int)$timeline_id);
        
        return $db->fetchRow($sql);
    }
    
    public function getTimeline($tour_id)
    {
        if (!(int) $tour_id) return null;
        
        $NAME = Zend_Db_Table::NAME;
        $db = $this->_db;
        
        /** @var $modelTL Model_TourenTimelines */   
        $modelTL = MyProject_Model_Database::loadModel('tourenTimelines');
        
        /** @var $modelP Model_TourenPortlets */
        $modelP = MyProject_Model_Database::loadModel('tourenPortlets');
        
        
        
        $sql = 'SELECT tl.* FROM ' . $this->getStorage()->info($NAME)
                .' LEFT JOIN ' . $modelTL->getStorage()->info($NAME) . ' tl USING(timeline_id)'
                .' WHERE tour_id = ' . $db->quote((int)$tour_id);
        
        return $db->fetchRow($sql);
    }
    
    public function getTourenByTimelineId($timeline_id, $withDefaults = false)
    {
        if (!(int) $timeline_id) return null;
        
        $NAME = Zend_Db_Table::NAME;
        $db = $this->_db;
        
        $sql = 'SELECT * FROM ' . $this->getStorage()->info($NAME)
              .' WHERE timeline_id = ' . (int)$timeline_id;
        if (!$withDefaults) $sql.= ' AND IsDefault = 0';
        
        return $db->fetchAll($sql);
    }

    public function getVorgangAndStatus( $tour_id )
    {
        $NAME = Zend_Db_Table::NAME;
        // $db = Zend_Db_Table::getDefaultAdapter();
        $db = $this->_db;

        $sql = 'SELECT DV.*, '
            .' auftrag_disponiert_am, auftrag_disponiert_user, '
            .' auftrag_abgeschlossen_am, auftrag_abgeschlossen_user '
            .'FROM ' . $this->getStorage()->info($NAME) . ' DV '
            .'LEFT JOIN ' . MyProject_Model_Database::loadStorage('tourenDispoAuftraege')->info($NAME) . ' DA '
            .' USING(Mandant, Auftragsnummer) '
            .'WHERE tour_id = ' . $db->quote($tour_id);

        return $db->fetchRow($sql, null, Zend_Db::FETCH_ASSOC);
    }
    
    public function getVorgang($tour_id)
    {
        $db = $this->_db;
        $userStorage = MyProject_Model_Database::loadStorage('user');
        $userTbl = $userStorage->info(Zend_Db_Table::NAME);
        
        $select = $db->select()->from(array('dv' => $this->_tbl))
                ->joinLeft(array('cuser' => $userTbl), 'dv.created_uid=cuser.user_id',  array( 'created_user'  => 'user_name'))
                ->joinLeft(array('muser' => $userTbl), 'dv.modified_uid=muser.user_id', array( 'modified_user' => 'user_name'))
                ->where('dv.tour_id = ' . $db->quote($tour_id) );
        
        $dispoData = $db->fetchRow($select, array(), Zend_Db::FETCH_ASSOC);
        
        if (!is_array($dispoData)) return null;
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        
        /* @var $vorgaengeStorage Model_Db_Vorgaenge */
        $vorgaengeStorage = MyProject_Model_Database::loadStorage('vorgaenge');
		
        $vorgangsData = $vorgaengeStorage->fetchRow(
            'Auftragsnummer = ' . $db->quote($dispoData['Auftragsnummer']) 
           .' AND Mandant   = ' . $db->quote($dispoData['Mandant']));
        
        $data = array_merge( 
                (is_array($dispoData)     ? $dispoData    : array() ), 
                (is_object($vorgangsData) ? $vorgangsData->toArray() : (array)$vorgangsData)  );
                
        return $data;
    }
	
    
    public function deleteVorgaengeByDateRange($refTourId, DateTime $dVon = NULL, DateTime $dBis = NULL, $wochentage, $removePortletIfEmpty = false)
    {
        $re = new stdClass();
        $re->numDeletes = 0;
        $re->numErrors = 0;
        $re->error = '';
        
        if (is_null($dVon) && is_null($dBis)) {
            $re->error = 'Fehler: Datum Von und Bis sind null. Mindestens ein Wert ist erforderlich!' . PHP_EOL;
            return $re;
        }
        
        $db = $this->_db;
        
        $wtMap = array('so'=>0,'mo'=>1,'di'=>2,'mi'=>3,'do'=>4,'fr'=>5,'sa'=>6);
        
        $tour = $this->fetchEntry($refTourId);
        $tbl = $this->getStorage()->info(Zend_Db_Table::NAME);
        $tblTL = self::getStorageByClass('TourenTimelines')->info(Zend_Db_Table::NAME);
        
        $modelTL = new Model_TourenTimelines();
        $modelP  = new Model_TourenPortlets();
        
        if ($tour) {
            
            $sql = 'SELECT t.tour_id, t.DatumVon, t.timeline_id, tl.portlet_id ' . PHP_EOL
                  .' FROM ' . $tbl . ' t ' . PHP_EOL
                  .' LEFT JOIN ' . $tblTL . ' tl USING(timeline_id)' . PHP_EOL
                  .' WHERE Mandant = ' . $db->quote($tour['Mandant']) . PHP_EOL
                  .' AND Auftragsnummer = ' . $db->quote($tour['Auftragsnummer']) . PHP_EOL;
            
            if ($dVon) $sql.= ' AND DatumVon >= ' . $db->quote($dVon->format('Y-m-d'));
            if ($dBis) {
                $sql.= ($dVon ? ' AND' : '') . ' DatumVon <= ' . $db->quote($dBis->format('Y-m-d'));
            }
            
            if (count($wochentage)) {
                $sql.= ' AND(';
                for($i = 0; $i < count($wochentage); ++$i) {
                    $w = strtolower($wochentage[$i]);
                    if ($i) $sql.= ' OR';
                    $sql.= ' DATE_FORMAT(DatumVon, "%w") = ' . $db->quote((isset($wtMap[$w]) ? $wtMap[$w] : $w));
                }
                $sql.= ')' . PHP_EOL;
            }
            $sql.= 'ORDER BY DatumVon, ZeitVon' . PHP_EOL;
            $sql.= 'LIMIT 1000' . PHP_EOL;
            
            $rows = $db->fetchAll($sql, null, Zend_Db::FETCH_ASSOC);
            
            foreach($rows as $row) {
//                echo '#' . __LINE__ . ' ' . __METHOD__ . ' delete Tour: ' . $row['tour_id'] . ' -> ' . $row['DatumVon'] . '<br>' . PHP_EOL;
//                continue;
                
                if ( $this->delete($row['tour_id']) ) {
                    ++$re->numDeletes;
                    if ($removePortletIfEmpty) {
                        if ($modelTL->countVorgaenge($row['timeline_id'], false) == 0) {
                            $modelTL->delete( $row['timeline_id']);
                        }

                        if ($modelP->countTimelines($row['portlet_id']) == 0) {
                            $modelP->delete($row['portlet_id']);
                        }                
                    }
                } else {
                    ++$re->numErrors;
                    $re->error.= "Die Tour mit der id " . $row['tour_id'] . " konnte nicht geloescht werde!";
                }  
            }
        }
        return $re;
    }
    
    /**
     *
     * @param int $tour_id
     * @param string $dVon YYYY-MM-DD
     * @param string $zvon HH:MM
     * @param string $dBis YYYY-MM-DD
     * @param string $dBis HH:MM
     * @return stdClass with Member bool ok, string msg listet �berschneidungen auf (zeilenweise)
     */
    public function checkResources($tour_id, $dVon, $zVon, $dBis, $zBis)
    {
        $re = new stdClass();
        $re->ok = true;
        $re->msg= '';
        $tourData = $this->fetchEntry($tour_id);
        
        $filter = array(
          'DatumVon' => $dVon,
          'DatumBis' => $dBis,
          'ZeitVon'  => $zVon,  
          'ZeitBis'  => $zBis,  
        );
        
        $rsrcs = array(
            'FP' => new Model_TourenDispoFuhrpark(),
            'MA' => new Model_TourenDispoMitarbeiter(),
            'WZ' => new Model_TourenDispoWerkzeug(),
        );
        
        /* @var $_model Model_TourenDispoResourceAbstract */
        foreach($rsrcs as $_key => $_model) {
            $rsrcKeys = $_model->getResourcesByTourId($tour_id, $keysOnly = true);
            if (!is_array($rsrcKeys) || count($rsrcKeys) == 0) continue;
            
            foreach($rsrcKeys as $_rsrcKey) {
                $r = $_model->checkResourceIsFree($_rsrcKey, $filter, $tour_id, $tourData['timeline_id']);
                if (!$r->free) {
                    $re->msg.= $r->message;
                }
            }
        }
        if ( trim($re->msg) ) {
            $re->ok = false;
        }
        return $re;        
    }
    
    /**
     *
     * @param int $tour_id
     * @param string $dVon YYYY-MM-DD
     * @param string $zvon HH:MM
     * @param string $dBis YYYY-MM-DD
     * @param string $dBis HH:MM
     * @return stdClass with Member bool ok, string msg listet �berschneidungen auf (zeilenweise)
     */
    public function checkResourcesByTime($resources, $dVon, $zVon, $dBis, $zBis)
    {
        $re = new stdClass();
        $re->ok = true;
        $re->msg= '';
        
        $filter = array(
          'DatumVon' => $dVon,
          'DatumBis' => $dBis,
          'ZeitVon'  => $zVon,  
          'ZeitBis'  => $zBis,  
        );
        
        $rsrcs = array(
            'FP' => new Model_TourenDispoFuhrpark(),
            'MA' => new Model_TourenDispoMitarbeiter(),
            'WZ' => new Model_TourenDispoWerkzeug(),
        );
        
        $re->conflicts = '';
        /* @var $_model Model_TourenDispoResourceAbstract */
        foreach($rsrcs as $_key => $_model) {
            $rsrcKeys = (isset($resources[$_key]) ? $resources[$_key] : null);
            if (!is_array($rsrcKeys) || count($rsrcKeys) == 0) continue;
            
            foreach($rsrcKeys as $_rsrcKey) {
                $r = $_model->checkResourceIsFree($_rsrcKey, $filter);
                if (!$r->free) {
                    $re->conflicts.= $r->message;
                }
            }
        }
        if ( trim($re->conflicts) ) {
            $re->ok = false;
        }
        
        return $re;        
    }
    
    public function addDefaultResources($id)
    {
        "SELECT tf.fuhrpark_id, tm.mitarbeiter_id, tw.werkzeug_id FROM mr_touren_dispo_vorgaenge t 
        JOIN mr_touren_dispo_vorgaenge t2 ON(t.tour_id = $id AND t2.IsDefault=1 AND t.timeline_id = t2.timeline_id)
        JOIN mr_touren_dispo_fuhrpark tf ON(t2.tour_id = tf.tour_id)
        JOIN mr_touren_dispo_mitarbeiter tm ON(t2.tour_id = tm.tour_id)
        JOIN mr_touren_dispo_werkzeug tw ON(t2.tour_id = tw.tour_id) 
        LIMIT 1
";
        $tourData = $this->fetchEntry($id);
        
        $fp = new Model_TourenDispoFuhrpark();
        $ma = new Model_TourenDispoMitarbeiter();
        $wz = new Model_TourenDispoWerkzeug();
        
        $fp->addTourDefaults( $tourData );
        $ma->addTourDefaults( $tourData );
        $wz->addTourDefaults( $tourData );
    }
    
    public function addResource($tour_id, $rsrc_id, $rsrc_typ )
    {
        //die('#'.__LINE__ . ' '.$tour_id.', '.$rsrc_id.', ' . $rsrc_typ );
        switch($rsrc_typ) {
            case 'FP':
                $rsrc = new Model_TourenDispoFuhrpark();
                return $rsrc->drop(array('route_id'=>$tour_id, 'fid'=>$rsrc_id) );
                break;
            
            case 'MA':
                $rsrc = new Model_TourenDispoMitarbeiter();
                return $rsrc->drop(array('route_id'=>$tour_id, 'mid'=>$rsrc_id) );
                break;
            
            case 'WZ':
                $rsrc = new Model_TourenDispoWerkzeug();
                return $rsrc->drop(array('route_id'=>$tour_id, 'wid'=>$rsrc_id) );
                break;
            
            default:
                return false;
        }   
    }
    
    /**
     *
     * @param type $tour_id
     * @param type $resources array('FP'=>array(12, 44, 55), 'MA'=>array(88, 87)); 
     */
    public function addResources($tour_id, $resources)
    {
        $re = array();
        foreach($resources as $_rsrc_typ => $_resources) {
            switch($_rsrc_typ) {
                case 'FP':
                case 'MA':
                case 'WZ':
                    //die('#'.__LINE__.' '.print_r($_resources,1));
                    foreach($_resources as $_rsrc_id) 
                        $re[$_rsrc_typ][] = $this->addResource($tour_id, $_rsrc_id, $_rsrc_typ);
                    break;
                    
                default:
                    die('Unbekannter rsrctyp ' . $_rsrc_typ);

            }
        }
        return $re;
    }
    
    
    /**
     *
     * @param type $tour_id
     * @param type $resources array('FP'=>array(12, 44, 55), 'MA'=>array(88, 87)); 
     */
    public function removeAllResources($tour_id)
    {
        $resources = $this->getResources($tour_id, $keysOnly = false);
        
        $fp = new Model_TourenDispoFuhrpark();
        $ma = new Model_TourenDispoMitarbeiter();
        $wz = new Model_TourenDispoWerkzeug();
        //return;
        //die('#'.__LINE__ . ' ' . __FILE__ . ' ' . __METHOD__ . ' ' . print_r($resources,1));
        
        foreach($resources as $_rsrc_typ => $_resources) {
            
            foreach($_resources as $props) {
                $_rsrc_id = $props['id'];
                //die('#'.__LINE__ . ' tour_id: ' . $tour_id . ' ' . $_rsrc_typ . ': ' . $_rsrc_id);
                switch($_rsrc_typ) {
                    case 'FP':
                        $fp->delete($_rsrc_id); break;
                    case 'MA':
                        $ma->delete($_rsrc_id); break;
                    case 'WZ':
                        $wz->delete($_rsrc_id); break;
                    default:
                        die('#'.__LINE__ . ' ' . $_rsrc_typ . ': ' . $_rsrc_id . ' ' );
                }
            }
        }
    }    
    
    public function tourlistColNames()
    {
        return array(
            'tour_id', 'tagesnr', 'timeline_id', 'Auftragsnummer', 'DatumVon', 'DatumBis', 'ZeitVon', 'ZeitBis',
            'Vorgangstitel', 'LieferungName', 'LieferungPostleitzahl', 'LieferungOrt', 'Kundennummer', 
            'NumAP', 'NumDP', 'NumDF', 'NumDM', 'name'
            );
    }
    
    public function tourlist2ColNames()
    {
        return array(
            'tour_id', 'tagesnr', 'timeline_id', 'Auftragsnummer', 'avisiert', 'DatumVon', 'DatumBis', 'ZeitVon', 'ZeitBis',
            'Vorgangstitel', 'LieferungName', 'LieferungPostleitzahl', 'LieferungOrt', 'Kundennummer',
            'auftrag_disponiert_am','auftrag_abgeschlossen_am',
            'tour_disponiert_am', 'tour_abgeschlossen_am',
        );
    }
    
    /**
     * @abstract
     * Im Argument bitte Tabellenfelder mit vorangestelltem Tabellen-Alias uebergeben
     *  'DT' => 'mr_touren_dispo_vorgaenge'
     *  'AK' => 'wws_auftragskoepfe'
     *  'AP' => 'wws_auftragspositionen'
     *  'DP' => 'mr_touren_dispo_auftragspositionen' 
     *  'DF' => 'mr_touren_dispo_fuhrpark' 
     *  'DM' => 'mr_touren_dispo_mitarbeiter' 
     *  'MA' => 'mr_mitarbeiter' 
     *  'TL' => 'mr_touren_timelines' 
     *  'P' => 'mr_touren_portlets' 
     * 
     * @todo
     * Usage-Pruefung, ob in bisherigen Aufrufen Tabellen-Aliase verwendet werden
     * @param array $baseFilter(Dvon => 'YYYY-MM-DD', DBis => 'YYYY-MM-DD', lager_id => int)
     * @param string $where
     * @param int $size
     * @param int $offset
     * @param string $order
     * @param string $orderdir
     * @return stdClass 
     */
    public function tourlist2($baseFilter, $where = '', $size = 30, $offset = 0, $order = '', $orderdir = '' ) 
    {
        $timeIn = time();
        //die( '#' . __LINE__ . ' ' . __METHOD__ . ' ' . print_r($order, 1));
        $storage = $this->getStorage();
        $db      = $storage->getAdapter();
		
		$storeBKF = new Model_Db_BestellkoepfeDispoFilter();
		
        $storeAKF = new Model_Db_VorgaengeDispoFilter;
        
        $tblBKF = $storeBKF->info(Zend_Db_Table::NAME);
        $tblAKF = $storeAKF->info(Zend_Db_Table::NAME);
        
        $result  = new stdClass();
        
        $DVon    = (array_key_exists('DVon',$baseFilter)) ? $baseFilter['DVon'] : '';
        $DBis    = (array_key_exists('DBis',$baseFilter)) ? $baseFilter['DBis'] : '';
        $lagerId = (array_key_exists('lager_id',$baseFilter)) ? $baseFilter['lager_id'] : '';
        $avisiert = (array_key_exists('avisiert',$baseFilter)) ? $baseFilter['avisiert'] : '';
        
        $cols = '
        DT.Mandant, DT.tour_id, DT.timeline_id, DT.avisiert, 
        tour_disponiert_am, tour_disponiert_user, 
        auftrag_disponiert_am, auftrag_disponiert_user,
        auftrag_abgeschlossen_am, auftrag_abgeschlossen_user,
        
        P.lager_id, P.tagesnr, AK.Auftragsnummer, 
        DatumVon, DatumBis, ZeitVon, ZeitBis,
        AK.Vorgangstitel,
        AK.Kundennummer,
        AK.LieferungName,
        AK.LieferungPostleitzahl,
        AK.LieferungOrt
        ';
        
//        if (!$DVon && !$DBis) {
//            $DVon = date('Y-m-d', time()-86400);
//        }
        
        $baseWhere = 'IsDefault = 0';
        if ($DVon && $DBis) {
            $baseWhere.= ' AND
          ( (DatumVon >= :DVon AND DatumVon <= :DBis )
           OR (DatumBis >= :DVon AND DatumBis <= :DBis ) )
        ';
        }
        elseif ($DVon) {
            $baseWhere.= ' AND 
          (DatumVon >= :DVon OR DatumBis >= :DVon )
        ';
        }
        elseif ($DBis) { 
            $baseWhere.= ' AND
          (DatumVon >= :DBis OR DatumBis >= :DBis )
        ';
        }
        
        if ($lagerId) $baseWhere = $baseWhere.' AND lager_id =' . $db->quote($lagerId);
        if ($avisiert !== '') $baseWhere = $baseWhere.' AND avisiert =' . $db->quote($avisiert);
        
        $group = 'DT.tour_id';        
        if (trim($order) ) {
            if (preg_match('/^asc|desc$/i', $orderdir)) $order.= ' ' . $orderdir;
        }
        else $order = array('DatumVon', 'ZeitVon', 'DatumBis', 'ZeitBis');
        $bind = array(':DVon' => $DVon, ':DBis' => $DBis);
        
        $select = $db->select();
        $select
        ->from( array( 'DT' => 'mr_touren_dispo_vorgaenge'), '' )
        ->joinLeft( 
            array( 'AK' => $tblAKF ),
            '(AK.Mandant=DT.Mandant AND AK.Auftragsnummer = DT.Auftragsnummer)',
            '')
        ->joinLeft( 
            array( 'DA' => 'mr_touren_dispo_auftraege' ),
            '(DT.Mandant=DA.Mandant AND DT.Auftragsnummer = DA.Auftragsnummer)',
            '')
        ->joinLeft( 
            array( 'TL' => 'mr_touren_timelines' ),
            'TL.timeline_id = DT.timeline_id',
            '')
        ->joinLeft( 
            array( 'P' => 'mr_touren_portlets' ),
            'P.portlet_id = TL.portlet_id',
            '')
        ->where($baseWhere);
                
        $select->columns(new Zend_Db_Expr($cols));
        $select->group($group);
                
        $selectCount = $db->select();
        $selectCount->from( array('tl' => new Zend_Db_Expr('('. $select->assemble() . ')')), '' );
        $selectCount->columns( new Zend_Db_Expr('COUNT(DISTINCT(tour_id))'));
//        if ($where) $selectCount->where($where);
        
        $result->numAll = (int)$db->fetchOne($selectCount, $bind);
        
//        $sql = $selectCount->assemble();
//        foreach($bind as $k => $v) $sql = str_replace($k, $db->quote($v), $sql);
//        echo '#'.__LINE__ . ' ' . (time()-$timeIn) . __METHOD__ . 's sql: ' . $sql . '<br/>' . PHP_EOL;
//        die('#'.__LINE__ . ' selectCount: ' . strtr($selectCount->assemble(), $rplBindParams ) );
        
        $selectList = $db->select();
        $selectList->from( array('tl' => new Zend_Db_Expr('('. $select->assemble() . ')')), '' );
        $selectList->columns( '*' );
        if ( $where ) $selectList->where( $where );
        if ( $order ) $selectList->order( $order );
        $selectList->limit($size, $offset);        
        $selectList->bind($bind);
//        die( $selectList->assemble() . PHP_EOL . print_r($bind, 1) );
        
        $result->rows = $db->fetchAll($selectList, $bind, Zend_Db::FETCH_ASSOC);
        
//        $sql = $selectList->assemble();
//        foreach($bind as $k => $v) $sql = str_replace($k, $db->quote($v), $sql);
//        echo '#'.__LINE__ . ' ' . (time()-$timeIn) . 's ' . __METHOD__ . ' sql: ' . $sql . '<br/>' . PHP_EOL;
        
        $result->sqlCount = $selectCount->assemble();
        $result->sqlList  = $selectList->assemble();
        
        return $result;
    }

    public function tourIDsByDateRange(int $iMandant, int $iANR, DateTime $dVon, DateTime $dBis = null, array $aWochentageNrs = [])
    {
        $model = new Model_TourenDispoVorgaenge();
        $dvTblName = $model->getStorage()->info(Zend_Db_Table::NAME);

        $qb = MyProject_Model_QueryBuilder::getInstance()
            ->setSelect('tour_id, DatumVon, ZeitVon, ZeitBis')
            ->setFrom($dvTblName)
            ->setWhere( 'Mandant = :Mandant')
            ->setParam('Mandant', $iMandant)
            ->andWhere( 'locked = 0')
            ->andWhere( 'Auftragsnummer = :ANR')
            ->setParam( 'ANR', $iANR)
            ->andWhere( 'DatumVon >= :DatumVon')
            ->setParam ( 'DatumVon', $dVon->format('Y-m-d'))
            ->setOrder('DatumVon, ZeitVon')
        ;

        if ($dBis) {
            $qb->andWhere('DatumVon <= :DatumBis')->setParam('DatumBis', $dBis->format('Y-m-d'));
        }

        if (count($aWochentageNrs)) {
            $qb->andWhere('date_format(DatumVon, \'%w\') IN (:aWNum)')->setParam('aWNum', $aWochentageNrs);
        }

        $rows = $this->_db->fetchAll( $qb->assemble());
        // die(print_r([__LINE__, __FILE__, __METHOD__, 'assembled query:', $qb->assemble(), $rows, 'end'],1));

        return $rows;

    }
    
    
    /**
     * @abstract
     * Im Argument bitte Tabellenfelder mit vorangestelltem Tabellen-Alias uebergeben
     *  'DT' => 'mr_touren_dispo_vorgaenge'
     *  'AK' => 'wws_auftragskoepfe'
     *  'AP' => 'wws_auftragspositionen'
     *  'DP' => 'mr_touren_dispo_auftragspositionen' 
     *  'DF' => 'mr_touren_dispo_fuhrpark' 
     *  'DM' => 'mr_touren_dispo_mitarbeiter' 
     *  'MA' => 'mr_mitarbeiter' 
     *  'TL' => 'mr_touren_timelines' 
     *  'P' => 'mr_touren_portlets' 
     * 
     * @todo
     * Usage-Pruefung, ob in bisherigen Aufrufen Tabellen-Aliase verwendet werden
     * @param array $baseFilter(Dvon => 'YYYY-MM-DD', DBis => 'YYYY-MM-DD', lager_id => int)
     * @param string $where
     * @param int $size
     * @param int $offset
     * @param string $order
     * @param string $orderdir
     * @return stdClass 
     */
    public function tourlist($baseFilter, $where = '', $size = 30, $offset = 0, $order = '', $orderdir = '' ) 
    {
        $storeBKF = new Model_Db_Bestellkoepfe;
        $storeAKF = new Model_Db_Vorgaenge;
        $storeAP  = new Model_Db_Auftragspositionen;
        
        $tblBKF = $storeBKF->info(Zend_Db_Table::NAME);
        $tblAKF = $storeAKF->info(Zend_Db_Table::NAME);
        $tblAP  = $storeAP->info(Zend_Db_Table::NAME);
        
        $timeIn = time();
        //die( '#' . __LINE__ . ' ' . __METHOD__ . ' ' . print_r($order, 1));
        $storage = $this->getStorage();
        $db      = $storage->getAdapter();
        
        $result  = new stdClass();
        
        $DVon    = (array_key_exists('DVon',$baseFilter)) ? $baseFilter['DVon'] : '';
        $DBis    = (array_key_exists('DBis',$baseFilter)) ? $baseFilter['DBis'] : '';
        $lagerId = (array_key_exists('lager_id',$baseFilter)) ? $baseFilter['lager_id'] : '';
        
        $cols = '
        DT.Mandant, DT.tour_id, DT.timeline_id, 
        tour_disponiert_am, tour_disponiert_user, 
        auftrag_disponiert_am, auftrag_disponiert_user,
        auftrag_abgeschlossen_am, auftrag_abgeschlossen_user,
        
        P.lager_id, P.tagesnr, AK.Auftragsnummer, 
        DatumVon, DatumBis, ZeitVon, ZeitBis,
        AK.Vorgangstitel,
        AK.Kundennummer,
        AK.LieferungName,
        AK.LieferungPostleitzahl,
        AK.LieferungOrt,
        COUNT(DISTINCT(AP.Positionsnummer)) NumAP,
        COUNT(DISTINCT(DP.Positionsnummer)) NumDP,
        COUNT(DISTINCT(DF.fuhrpark_id    )) NumDF,
        COUNT(DISTINCT(DM.mitarbeiter_id )) NumDM,
        MA.name
        ';
        
//        if (!$DVon && !$DBis) {
//            $DVon = date('Y-m-d', time()-86400);
//        }
        
        $baseWhere = 'IsDefault = 0';
        if ($DVon && $DBis) $baseWhere.= ' AND
          ( (DatumVon >= :DVon AND DatumVon <= :DBis )
           OR (DatumBis >= :DVon AND DatumBis <= :DBis ) )
        ';
        elseif ($DVon) $baseWhere.= ' AND
          (DatumVon >= :DVon OR DatumBis >= :DVon )
        ';
        elseif ($DBis) $baseWhere.= ' AND
          (DatumVon >= :DBis OR DatumBis >= :DBis )
        ';
        
        if ($lagerId) $baseWhere = '('.$baseWhere.') AND lager_id =' . $db->quote($lagerId);
        
        $group = 'DT.tour_id';        
        if (trim($order) ) {
            if (preg_match('/^asc|desc$/i', $orderdir)) $order.= ' ' . $orderdir;
        }
        else $order = array('DatumVon', 'ZeitVon', 'DatumBis', 'ZeitBis');
        $bind = array(':DVon' => $DVon, ':DBis' => $DBis);
        
        $select = $db->select();
        $select
        ->from( array( 'DT' => 'mr_touren_dispo_vorgaenge'), '' )
        ->joinLeft( 
            array( 'AK' => $tblAKF ),
            '(AK.Mandant=DT.Mandant AND AK.Auftragsnummer = DT.Auftragsnummer)',
            '')
        ->joinLeft( 
            array( 'DA' => 'mr_touren_dispo_auftraege' ),
            '(DT.Mandant=DA.Mandant AND DT.Auftragsnummer = DA.Auftragsnummer)',
            '')
        ->joinLeft( 
            array( 'AP' => $tblAP ),
            '(AP.Mandant=AK.Mandant AND AP.Auftragsnummer = AK.Auftragsnummer)',
            '')
        ->joinLeft( 
            array( 'DP' => 'mr_touren_dispo_auftragspositionen' ),
            'DT.tour_id = DP.tour_id',
            '')
        ->joinLeft( 
            array( 'DF' => 'mr_touren_dispo_fuhrpark' ),
            'DF.tour_id = DP.tour_id',
            '')
        ->joinLeft( 
            array( 'DM' => 'mr_touren_dispo_mitarbeiter' ),
            'DM.tour_id = DP.tour_id',
            '')
        ->joinLeft( 
            array( 'MA' => 'mr_mitarbeiter' ),
            'MA.mid = DM.mitarbeiter_id',
            '')
        ->joinLeft( 
            array( 'TL' => 'mr_touren_timelines' ),
            'TL.timeline_id = DT.timeline_id',
            '')
        ->joinLeft( 
            array( 'P' => 'mr_touren_portlets' ),
            'P.portlet_id = TL.portlet_id',
            '')
        ->where($baseWhere);
                
        $select->columns(new Zend_Db_Expr($cols));
        $select->group($group);
                
        $selectCount = $db->select();
        $selectCount->from( array('tl' => new Zend_Db_Expr('('. $select->assemble() . ')')), '' );
        $selectCount->columns( new Zend_Db_Expr('COUNT(DISTINCT(tour_id))'));
//        if ($where) $selectCount->where($where);
        
        $result->numAll = (int)$db->fetchOne($selectCount, $bind);
        
//        $sql = $selectCount->assemble();
//        foreach($bind as $k => $v) $sql = str_replace($k, $db->quote($v), $sql);
//        echo '#'.__LINE__ . ' ' . (time()-$timeIn) . __METHOD__ . 's sql: ' . $sql . '<br/>' . PHP_EOL;
//        die('#'.__LINE__ . ' selectCount: ' . strtr($selectCount->assemble(), $rplBindParams ) );
        
        $selectList = $db->select();
        $selectList->from( array('tl' => new Zend_Db_Expr('('. $select->assemble() . ')')), '' );
        $selectList->columns( '*' );
        if ( $where ) $selectList->where( $where );
        if ( $order ) $selectList->order( $order );
        $selectList->limit($size, $offset);        
        $selectList->bind($bind);
//        die( $selectList->assemble() . PHP_EOL . print_r($bind, 1) );
        
        $result->rows = $db->fetchAll($selectList, $bind, Zend_Db::FETCH_ASSOC);
        
//        $sql = $selectList->assemble();
//        foreach($bind as $k => $v) $sql = str_replace($k, $db->quote($v), $sql);
//        echo '#'.__LINE__ . ' ' . (time()-$timeIn) . 's ' . __METHOD__ . ' sql: ' . $sql . '<br/>' . PHP_EOL;
        
        $result->sqlCount = $selectCount->assemble();
        $result->sqlList  = $selectList->assemble();
        
        return $result;
    }
    
    /**
     * @abstract
     * Im Argument bitte Tabellenfelder mit vorangestelltem Tabellen-Alias uebergeben
     *  'DT' => 'mr_touren_dispo_vorgaenge'
     *  'AK' => 'wws_auftragskoepfe'
     *  'AP' => 'wws_auftragspositionen'
     *  'DP' => 'mr_touren_dispo_auftragspositionen' 
     *  'DF' => 'mr_touren_dispo_fuhrpark' 
     *  'DM' => 'mr_touren_dispo_mitarbeiter' 
     *  'MA' => 'mr_mitarbeiter' 
     *  'TL' => 'mr_touren_timelines' 
     *  'P' => 'mr_touren_portlets' 
     * 
     * @todo
     * Usage-Pruefung, ob in bisherigen Aufrufen Tabellen-Aliase verwendet werden
     * @param array $baseFilter(Dvon => 'YYYY-MM-DD', DBis => 'YYYY-MM-DD', lager_id => int)
     * @param string $where
     * @param int $size
     * @param int $offset
     * @param string $order
     * @param string $orderdir
     * @return stdClass|null 
     */
    public function tourlistByANR($Mandant, $ANR, $baseFilter, $where = '', $size = 30, $offset = 0, $order = '', $orderdir = '' ) 
    {
        $storeAK = new Model_Db_Vorgaenge;
        $tblAK   = $storeAK->info(Zend_Db_Table::NAME);
        
        $GetTourSum = false;
        $timeIn = time();
        
        //die( '#' . __LINE__ . ' ' . __METHOD__ . ' ' . print_r($order, 1));
        $storage = $this->getStorage();
        $db      = $storage->getAdapter();
        
        if (!$Mandant || !$ANR) {
            return null;
        }        
        
        $selectAuftrag = $db->select()
        ->from(
            array( 'AK' => $tblAK ), 
            explode(',', 'Mandant,Auftragsnummer,Vorgangstitel,Kundennummer,LieferungName,LieferungPostleitzahl,LieferungOrt')
        )
        ->joinLeft( 
            array( 'DA' => 'mr_touren_dispo_auftraege' ),
            '(AK.Mandant=DA.Mandant AND AK.Auftragsnummer = DA.Auftragsnummer)',
            explode(',', 'auftrag_disponiert_user,auftrag_disponiert_am,auftrag_abgeschlossen_user,auftrag_abgeschlossen_am,tour_dispo_count,tour_abschluss_count,wws_last_geaendertam')
        )
        ->where('AK.Mandant = ?', $Mandant)
        ->where('AK.Auftragsnummer = ?', $ANR);        
//        $auftragsdaten = $db->fetchRow($selectAuftrag);
        
        $result  = new stdClass();
        
        $DVon    = (array_key_exists('DVon',$baseFilter)) ? $baseFilter['DVon'] : '';
        $DBis    = (array_key_exists('DBis',$baseFilter)) ? $baseFilter['DBis'] : '';
        $lagerId = (array_key_exists('lager_id',$baseFilter)) ? $baseFilter['lager_id'] : '';
        
        $cols = '
        DT.Mandant, DT.tour_id, DT.timeline_id, 
        P.lager_id, L.lager_name, P.tagesnr, tour_disponiert_am, tour_disponiert_user,
        DatumVon, DatumBis, ZeitVon, ZeitBis';
        
        if ($GetTourSum)
        $cols.= ',
        COUNT(DISTINCT(DF.fuhrpark_id    )) NumDF,
        COUNT(DISTINCT(DM.mitarbeiter_id )) NumDM';
        else 
        $cols.= ',
        \'-\' NumAP,
        \'-\' NumDP,
        \'-\' NumDF,
        \'-\' NumDM';
        
        $baseWhere = 'IsDefault = 0';
        if ($DVon && $DBis) $baseWhere.= ' AND
          ( (DatumVon >= :DVon AND DatumVon <= :DBis )
           OR (DatumBis >= :DVon AND DatumBis <= :DBis ) )
        ';
        elseif ($DVon) $baseWhere.= ' AND
          (DatumVon >= :DVon OR DatumBis >= :DVon )
        ';
        elseif ($DBis) $baseWhere.= ' AND
          (DatumVon >= :DBis OR DatumBis >= :DBis )
        ';
        
        if ($lagerId) $baseWhere = '('.$baseWhere.') AND lager_id =' . $db->quote($lagerId);
            
        if (trim($order) ) {
            if (preg_match('/^asc|desc$/i', $orderdir)) $order.= ' ' . $orderdir;
        }
        else $order = array('DatumVon', 'ZeitVon', 'DatumBis', 'ZeitBis');
        $bind = array(':DVon' => $DVon, ':DBis' => $DBis);
        
        $select = $db->select()
            ->from( array( 'DT' => 'mr_touren_dispo_vorgaenge'), '' )
            ->joinLeft(
                array( 'TL' => 'mr_touren_timelines' ),
                'TL.timeline_id = DT.timeline_id',
                '')
            ->joinLeft(
            array( 'P' => 'mr_touren_portlets' ),
            'TL.portlet_id = P.portlet_id',
            '' )
            ->joinLeft(
                array( 'L' => 'mr_lager' ),
                'P.lager_id = L.lager_id',
                '');
        
        if ($GetTourSum)
        $select
        ->joinLeft( 
            array( 'DF' => 'mr_touren_dispo_fuhrpark' ),
            'DF.tour_id = DP.tour_id',
            '')
        ->joinLeft( 
            array( 'DM' => 'mr_touren_dispo_mitarbeiter' ),
            'DM.tour_id = DP.tour_id',
            '');
        
        $select
        ->where( 'DT.Mandant = ?', $Mandant) 
        ->where( 'DT.Auftragsnummer = ?', $ANR) 
        ->where( $baseWhere);
                
        $select->columns(new Zend_Db_Expr($cols));
        
        $selectCount = $db->select();
        $selectCount->from( array('tl' => new Zend_Db_Expr('('. $select->assemble() . ')')), '' );
        $selectCount->columns( new Zend_Db_Expr('COUNT(DISTINCT(tour_id))'));
//        if ($where) $selectCount->where($where);
        
        $result->numAll = (int)$db->fetchOne($selectCount, $bind);
        
//        $sql = $selectCount->assemble();
//        foreach($bind as $k => $v) $sql = str_replace($k, $db->quote($v), $sql);
//        echo '#'.__LINE__ . ' ' . (time()-$timeIn) . __METHOD__ . 's sql: ' . $sql . '<br/>' . PHP_EOL;
//        die('#'.__LINE__ . ' selectCount: ' . strtr($selectCount->assemble(), $rplBindParams ) );
        
        $selectList = $db->select();
        $selectList->from( array('tl' => new Zend_Db_Expr('('. $select->assemble() . ')')), '' );
        $selectList->columns( '*' );
        if ( $where ) $selectList->where( $where );
        if ( $order ) $selectList->order( $order );
        $selectList->limit($size, $offset);        
        $selectList->bind($bind);
//        die( $selectList->assemble() . PHP_EOL . print_r($bind, 1) );
        
        $result->rows = $db->fetchAll($selectList, $bind, Zend_Db::FETCH_ASSOC);
        
//        for($i = 0; $i < count($result->rows); ++$i) {
//            foreach($auftragsdaten as $ak => $av) $result->rows[$i][$ak] = $av;
//        }
        
//        $sql = $selectList->assemble();
//        foreach($bind as $k => $v) $sql = str_replace($k, $db->quote($v), $sql);
//        echo '#'.__LINE__ . ' ' . (time()-$timeIn) . 's ' . __METHOD__ . ' sql: ' . $sql . '<br/>' . PHP_EOL;
        
        $result->sqlCount = $selectCount->assemble();
        $result->sqlList  = $selectList->assemble();
        
        return $result;
    }
    
    public function NotInUse_tourlistVFrom() 
    {
        $tblTV = $this->getStorage()->info(Zend_Db_Table::NAME);
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info(Zend_Db_Table::NAME);
        $sql = 'SELECT T.tour_id, T.timeline_id, T.Mandant, T.Auftragsnummer, T.DatumVon,
        T.ZeitVon, T.DatumBis, T.ZeitBis, T.modified,
        
        T.tour_id, T.timeline_id, T.Auftragsnummer, T.DatumVon, T.ZeitVon, T.ZeitBis
        Vorgangstitel, Rechnungsanschrift, RechnungOrt, Lieferanschrift, LieferungOrt,
        Lieferwoche, Lieferjahr, Liefertermin, LieferterminFix, Auftragswert
        
        Bearbeitungsstatus, UnterBearbeitungsstatus, Vorgangstitel,        
        Rechnungsanschrift,  RechnungStrassePostfach, 
        RechnungPostleitzahl, RechnungOrt, RechnungLand,        
        Lieferanschrift, LieferungName, LieferungStrassePostfach, 
        LieferungPostleitzahl, LieferungOrt, LieferungLand,   
        
        Lieferwoche, Lieferjahr, Liefertermin, LieferterminFix, LieferterminHinweisText, 
        LieferterminHinweisText, Versandbedingung, Lieferbedingung,        
        AbschlussStatus
        
        FROM ' . $tblTV . ' T LEFT JOIN ' . $tblAK . ' V ';
        $sql.= 'USING(Mandant, Auftragsnummer) ';
        
    }
    
    
    /**
     *
     * @param int $tour_id
     * @param string $posBelongTo options(own, other, none, all)
     * @return array|null
     * @throws Exception 
     */
    public function getBestellungen($tour_id, $posBelongTo = 'all')
    {
        $response = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        
        $NAME = Zend_Db_Table::NAME;
        $tblAK = MyProject_Model_Database::loadStorage('vorgaenge')->info($NAME);
        $tblAP = MyProject_Model_Database::loadStorage('auftragspositionen')->info($NAME);
        $tblBK = MyProject_Model_Database::loadStorage('bestellkoepfe')->info($NAME);
        $tblBP = MyProject_Model_Database::loadStorage('bestellpositionen')->info($NAME);
        $tblDP = MyProject_Model_Database::loadStorage('tourenDispoPositionen')->info($NAME);
        $tblWB = MyProject_Model_Database::loadStorage('warenbewegungen')->info($NAME);
        
        try {
            /** @var $posBelongTo 
             * Positionsfilter - Optionen:
             * own:   zeige alle Positionen die dieser Tour zugeordnet sind
             * other: zeige alle Positionen die einer anderen zugeordnet sind
             * none:  zeige aller Positionen die noch keiner Tour zugeordnet sind
             * all: zeige alle 
             */
            
            switch($posBelongTo) {
                case 'own':
                   $sqlFilterBelong = 'D.tour_id = ' . $db->quote($tour_id);
                    break;
                case 'other':
                    $sqlFilterBelong = '(D.tour_id IS NOT NULL AND D.tour_id <> '.$db->quote($tour_id);
                    break;
                case 'none':
                    $sqlFilterBelong = '(D.tour_id IS NULL)';
                    break;
                
                case 'all':
                default:
                    $sqlFilterBelong = '';
            }
            
            $sqlVorgangsIdByTourId =
                 'SELECT Mandant, Auftragsnummer '
                .'FROM mr_touren_dispo_vorgaenge '
                .'WHERE tour_id = :tour_id';
            $rows = $db->fetchAll($sqlVorgangsIdByTourId, array(':tour_id' => $tour_id));

            $mandant = $rows[0]['Mandant'];
            $auftragsnr = $rows[0]['Auftragsnummer'];
            
            $bindParams = array(':Mandant'=>$mandant,':Auftragsnummer'=>$auftragsnr);

            // Es gilt noch zu unterscheiden zwischen 
            // offenen Positionen
            // disponierte Positionen
            // teildisponierte Positionen
            // abh�ngig von disponierten Mengen in mr_touren_dispo_auftragspositionen
            $sqlAPositionen = 
                 'SELECT BK.BestellName, BK.Lagerkennung, BK.Bestellnummer, A.Mandant, A.Auftragsnummer, '
                .'count(BK.Bestellnummer) AnzahlPositionen ' . PHP_EOL
                .', WB.Lagerkennung WLagerkennung, WB.Stellplatz WStellplatz, WB.Menge WMenge ' . PHP_EOL
                .'FROM '.$tblDP.' D ' . PHP_EOL
                .'LEFT JOIN '.$tblAP.' A ' . PHP_EOL
                .'USING(Mandant,Auftragsnummer,Positionsnummer) ' . PHP_EOL
                .'LEFT JOIN '.$tblBP.' B ' . PHP_EOL
                .'ON ( ' . PHP_EOL
                .'   A.Mandant = B.Mandant ' . PHP_EOL
                .'   AND A.Auftragsnummer = B.Auftragsnummer ' . PHP_EOL
                .'   AND A.Positionsnummer = B.AuftragsPositionsnummer) ' . PHP_EOL
                .'LEFT JOIN ' . $tblWB . ' WB ' . PHP_EOL
                .'ON ( ' . PHP_EOL
                .'   A.Mandant = WB.Mandant ' . PHP_EOL
                .'   AND A.Auftragsnummer  = WB.Auftragsnummer ' . PHP_EOL
                .'   AND A.Positionsnummer = WB.Positionsnummer) ' . PHP_EOL
                .'LEFT JOIN ' . $tblBK . ' BK ' . PHP_EOL
                .'ON ( ' . PHP_EOL
                .'   BK.Mandant = B.Mandant ' . PHP_EOL
                .'   AND BK.Bestellnummer = B.Bestellnummer) ' . PHP_EOL
                .'' . PHP_EOL
                .'WHERE ' . PHP_EOL
                .($posBelongTo != 'own' ? 'A.Mandant = :Mandant AND A.Auftragsnummer = :Auftragsnummer AND ' : '') . PHP_EOL
                .' A.Positionsart = 1 ' . PHP_EOL
                .($sqlFilterBelong ? ' AND ' . $sqlFilterBelong . ' ' : '') . PHP_EOL
                .'GROUP BY BK.Bestellnummer ' . PHP_EOL
                .'ORDER BY BK.Bestellnummer';
//            echo '<pre>#' . __LINE__ . ' ' . strtr($sqlAPositionen, $bindParams) . PHP_EOL;
            return $db->fetchAll($sqlAPositionen, array(':Mandant'=>$mandant,':Auftragsnummer'=>$auftragsnr));
            
        } catch(Exception $e) {
            throw new Exception( $e->getMessage() . PHP_EOL .$e->getTraceAsString() );
        }
        return null;
    }

    /**
     * @param int $tourid
     * @return array|false
     * @throws Zend_Db_Table_Exception
     */
    public function getTourWithUserRole(int $tourid)
    {
        $sql = 'SELECT t.tour_id, t.Mandant, t.Auftragsnummer, farbklasse,
                t.timeline_id, t.DatumVon, t.ZeitVon, t.DatumBis, t.ZeitBis, 
                t.IsDefault, t.locked, t.created_uid,  t.modified_uid, 
                u.user_role, u.user_role created_role, u.user_name
                FROM ' . $this->_tbl . ' t '
            . ' LEFT JOIN mr_user u ON (t.created_uid = u.user_id) WHERE tour_id = ' . (int)$tourid;

        return $this->_db->fetchRow($sql, [], Zend_Db::FETCH_ASSOC);
    }

    public function getLagerIdByTourId( int $iTourId ): int
    {

        $sql = 'SELECT p.lager_id
                FROM ' . $this->_tbl . ' t '
            . ' LEFT JOIN mr_touren_timelines tl ON (t.timeline_id = tl.timeline_id) '
            . ' LEFT JOIN mr_touren_portlets p ON (tl.portlet_id = p.portlet_id) WHERE tour_id = ' . $iTourId;

        return (int)$this->_db->fetchOne($sql);
    }


    public function tourOperationIsAllowedById(int $tourId, object $userIdentity, string $action = '')
    {
        $aTour = $this->getTourWithUserRole($tourId);

        return $this->tourOperationIsAllowedByData( $aTour, $userIdentity, $action );
    }

    public function getLastErrorTourOperationNotAllowed() : string {
        return $this->_lastErrorTourOperationNotAllowed;
    }

    private function setLastErrorTourOperationNotAllowed(string $error) {
        $this->_lastErrorTourOperationNotAllowed = $error;
    }

    public function tourOperationIsAllowedByData(array $aTour, object $userIdentity, string $action = '')
    {
        $this->setLastErrorTourOperationNotAllowed('');

        $user_id = $userIdentity->user_id;
        $user_role = $userIdentity->user_role;
        $verbose = 0;

        if ($verbose && $aTour['created_uid'] == '200' && $aTour['tour_id'] == '129926' && $action === "drag" ) {
            print_r([
                'aTour' => $aTour,
                'userIdentity' => $userIdentity,
                'action' => $action,
                'user_id' => $user_id,
                'user_role' => $user_role,
            ]);
            $dbg = true;
        } else {
            $dbg = false;
        }

        switch ($user_role) {
            case 'innendienst':
            case 'controller':
                if ($aTour['locked']) {
                    $this->setLastErrorTourOperationNotAllowed( "Tour ist bereits gesperrt!" );
                    if ($dbg) {
                        echo "\nlocked=" . $aTour['locked'] . "\n";
                        exit;
                    }
                    return false;
                }
                if ( ($aTour['farbklasse'] ?? '') === 'Gruen') {
                    $this->setLastErrorTourOperationNotAllowed( "Tour ist als VIP-Tour markiert und wurde gesperrt!" );
                    if ($dbg) {
                        echo "\nlocked=" . $aTour['locked'] . "\n";
                        exit;
                    }
                    return false;
                }
                if (0 && $aTour['created_role'] != $user_role) {
                    if ($dbg) {
                        echo "\nTour wurde nicht von einem $user_role-Mitarbeiter angelegt!\n";
                        exit;
                    }
                    return false;
                }
                if (!in_array($action, ['edit', 'resize', 'remove', 'move', 'drag', 'set-Tour-Farbklasse', 'setTourFarbklasse'])) {
                    $this->setLastErrorTourOperationNotAllowed( "Tour-Aktion " . $action . " ist für die Benutzergruppe " . $user_role . " gesperrt!" );
                    if ($dbg) {
                        echo "\nUmzulässige Tour-Operation " . $action . "!\n";
                        exit;
                    }
                    return false;
                }
                break;
        }
        if ($dbg) {
            echo "\nTour-Operation is ALLOWED: " . $action . "!\n";
            exit;
        }
        return true;
    }
    
    /**
     *
     * @param type $tourData
     * @param type $lager_id
     * @param type $resources
     * @param type $checkIfExists
     * @param type $opts Aktuell nur f�r Angabe des Timeline-Titels $opts = array('timeline_data'=>array('title'=>'xyz'));
     * @return TourenDispoIds ids der DefaultRoute (portlet_id, timeline_id, tour_id)
     * @throws Exception 
     */
    public function addPortletAndDefaultRoute($tourData, $lager_id, $resources, $checkIfExists = false, $opts = array())
    {
        if (!isset($tourData['tour_id'])) return false;
        
        $re = $this->checkResourcesByTime(
                $resources, 
                $tourData['DatumVon'],
                $tourData['ZeitVon'],
                $tourData['DatumBis'], 
                $tourData['ZeitBis']
        );
        $re2 = null;        
        
        if ($re && $re->ok)
        {
            unset($tourData['tour_id']);
            /** @var TourenDispoIds */
            $re2 = $this->addPortletAndRoute($tourData, $lager_id, $checkIfExists, $opts);
            if ($re2->tour_id) {
                // Gibt die neuen Ressourceneintraege zurueck
                $this->addResources($re2->tour_id, $resources);
            }
            return $re2;
        }
        if ($re->conflicts) {
            throw new Exception('Es existieren Resourcen-Konflikte:'.PHP_EOL.$re->conflicts);
        }
        throw new Exception('Die Resourcen konnten nicht uebernommen werden!');
    }
    
    /**
     * Tour wird Standalone mit eigenem Portlet und eigener Timeline angelegt
     * @param array $tourData assoc. Keys muessen DB-Feld-Benennung entsprechen
     * @param int $lager_id
     * @return TourenDispoIds new inserted ids (portlet_id, timeline_id, tour_id)
     * @todo Validierung / Pre-Conditions, Aktuell liegt Verantwortung bei Controller!!     * 
     */
    public function addPortletAndRoute($tourData, $lager_id, $checkIfExists = false, $opts = array())
    {
            /** @var Model_TourenPortlets */
            $modelP = new Model_TourenPortlets();

            /** @var $modelTL Model_TourenTimelines */
            $modelTL = new Model_TourenTimelines(); // MyProject_Model_Database::loadModel('tourenTimelines');
            
            if ($checkIfExists && $this->tourExists($tourData['Mandant'], $tourData['Auftragsnummer'], $tourData['DatumVon'], $lager_id) ) {
                return false;
            }
            
            $portlet_id = $modelP->add(array(
                'datum'    => $tourData['DatumVon'],
                'lager_id' => $lager_id
            ));
            
            $tlData = array(
                'portlet_id' => $portlet_id,
            );
            if ($opts && @isset($opts['timeline_data'])) {
                $tlData = array_merge(
                        $tlData,
                        $opts['timeline_data']);
            }
            
//            $modelTL = new Model_TourenTimelines();
            $timeline_id = $modelTL->add($tlData);

            $tourData['timeline_id'] = $timeline_id;
            
            //die('#' . __LINE__ . ' ' );
            // Die Bezeichnung Drop fuer 'Hinzufuegen' wurde analog zur 
            // Drag'n Drop Funktionalitaet gewaehlt
            $tour_id = $this->drop($tourData);

            /* @var $model Model_TourenDispoLog */
            $modelLogger = new Model_TourenDispoLog(); // MyProject_Model_Database::loadModel('tourenDispoLog');
            $modelLogger->logTour($tour_id, 'insert');
            
            return new TourenDispoIds(array(
                'portlet_id' => $portlet_id,
                'timeline_id' => $timeline_id,
                'tour_id' => $tour_id,
            ));
    }
    
    public function tourExists($mandant, $auftragsnr, $date = '', $lager_id = '') 
    {
        $NAME = Zend_Db_Table::NAME;
        $db = $this->_db;
        $storageTL = MyProject_Model_Database::loadStorage('tourenTimelines');
        $storageP  = MyProject_Model_Database::loadStorage('tourenPortlets');
              
        $select = $db->select()->from(array('dv' => $this->_tbl), new Zend_Db_Expr('count(1) count'))
                ->joinLeft(array('t' => $storageTL->info($NAME)), 'dv.timeline_id=t.timeline_id', null)
                ->joinLeft(array('p' => $storageP->info($NAME)), 't.portlet_id=p.portlet_id', null)
                ->where('dv.Mandant = ' . $db->quote($mandant) )
                ->where('dv.Auftragsnummer = ' . $db->quote($auftragsnr) );
                if ($date) $select->where('dv.DatumVon = ' . $db->quote($date) );
                if ($lager_id) $select->where('p.lager_id = ' . $db->quote($lager_id) );
        
        return $db->fetchOne($select);  
    }
    
    public function tourStatus($tour_id) 
    {
        $NAME = Zend_Db_Table::NAME;
        // $db = Zend_Db_Table::getDefaultAdapter();
        $db = $this->_db;
        
        $sql = 'SELECT tour_id, Mandant, Auftragsnummer, '
              .' locked, '
              .' tour_disponiert_am, tour_disponiert_user, '
              .' tour_abgeschlossen_am, tour_abgeschlossen_user, '
              .' zeiten_erfasst_am, zeiten_erfasst_user, '
              .' auftrag_disponiert_am, auftrag_disponiert_user, '
              .' auftrag_abgeschlossen_am, auftrag_abgeschlossen_user '
              .'FROM ' . $this->getStorage()->info($NAME) . ' DV '
              .'LEFT JOIN ' . MyProject_Model_Database::loadStorage('tourenDispoAuftraege')->info($NAME) . ' DA '
              .' USING(Mandant, Auftragsnummer) '
              .'WHERE tour_id = ' . $db->quote($tour_id);
        
        return $db->fetchRow($sql, null, Zend_Db::FETCH_ASSOC);
    }

    /**
     *
     * @param  int $tour_id
     * @return int Status Moegliche Werte entsprechende den Konstanten self:STATUS_...
     *
     */
    public function isLockedByRow( array $row )
    {
//        if ((int)$row['auftrag_abgeschlossen_am']) return self::STATUS_AUFTRAG_ABGESCHLOSSEN;
//        if ((int)$row['auftrag_disponiert_am'])    return self::STATUS_AUFTRAG_DISPONIERT;
//        if ((int)$row['tour_abgeschlossen_am'])    return self::STATUS_TOUR_ABGESCHLOSSEN;
//        if ((int)$row['tour_disponiert_am'])       return self::STATUS_TOUR_DISPONIERT;
        if ($row['locked'])                        return self::STATUS_TOUR_LOCKED;
        return 0;
    }

    /**
     *
     * @param  int $tour_id
     * @return int Status Moegliche Werte entsprechende den Konstanten self:STATUS_...
     *
     */
    public function isLocked($tour_id)
    {
        $row = $this->tourStatus($tour_id);
//        if ((int)$row['auftrag_abgeschlossen_am']) return self::STATUS_AUFTRAG_ABGESCHLOSSEN;
//        if ((int)$row['auftrag_disponiert_am'])    return self::STATUS_AUFTRAG_DISPONIERT;
//        if ((int)$row['tour_abgeschlossen_am'])    return self::STATUS_TOUR_ABGESCHLOSSEN;
//        if ((int)$row['tour_disponiert_am'])       return self::STATUS_TOUR_DISPONIERT;
        if ($row['locked'])                        return self::STATUS_TOUR_LOCKED;
        return 0;
    }


    public function setFarbklasse(int $tour_id, string $sFarbklasse): int
    {
        $iAffected = $this->_db->update(
            $this->_tbl,
            [ 'farbklasse' => $sFarbklasse],
            'tour_id = ' . $this->_db->quote($tour_id)
        );

        if ($iAffected) {
            $this->dispoLog($tour_id, 'color-' . $sFarbklasse, []);
        }

        return $iAffected;
    }


    public function getFarbklasse(int $tour_id): string
    {
        $sql = 'SELECT farbklasse FROM ' . $this->_tbl . ' '
            . ' WHERE tour_id = ' . $this->_db->quote($tour_id);

        return $this->_db->fetchOne( $sql );
    }
}
