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
abstract class Model_TourenDispoResourceAbstract extends MyProject_Model_Database
implements MyProject_Model_TourenResourceInterface
{
    // abstract Muss in abgeleiteter Klasse definiert werden
    protected $_rsrcTitleField = '0';
    
    // Start: Zu überschreibende Properties
    protected $_storageName   = '';  // Bsp: 'tourenDispoFuhrpark';
    protected $_resourceName  = ''; // Bsp: 'Fuhrpark';
    protected $_resourceModel =''; // Bsp:  'Model_Fuhrpark';
    protected $_resourceType  ='';  // Bsp:  MA|FP|WZ;
    protected $_prmRsrcKey    = '';   // Bsp: 'fid';
    // Ende: Zu überschreibende Properties
    
    protected $_db = null;
    protected $_map = null;
    protected $_tbl = '';
    
    protected $_prmTourKey       = 'route_id';
    protected $_tblRsrcModelName = '';
    protected $_tblRsrcLnkKey    = '';
    protected $_tblRsrcLnkName   = '';
    protected $_tblRsrcKey       = '';
    protected $_tblTourKey  = '';    
    protected $_tblRsrcName = '';
    protected $_tblTourName = '';

    public function __construct() {
        parent::__construct();
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);
        $this->_cnf = $this->_storage->info();
        $this->_map = $this->_storage->info('referenceMap');

        $rsrcRefTableClass  = $this->_map['resource']['refTableClass'];
        /* @var $_tblRsrcObj Zend_Db_Table */
        $this->_tblRsrcObj  = new $rsrcRefTableClass();
        $this->_tblRsrcName = $this->_tblRsrcObj->info(Zend_Db_Table::NAME);
        $this->_tblRsrcKey  = $this->_map['resource']['refColumns'];
        
        // Resource-Verknüpfungs-Tabelle
        $this->_tblRsrcLnkName = $this->_cnf['name'];
        $this->_tblRsrcLnkKey  = $this->_map['resource']['columns'];

        $vgRefTableClass    = $this->_map['vorgang']['refTableClass'];
        /* @var $_tblTourObj Zend_Db_Table */
        $_tblTourObj = new $vgRefTableClass();
        $this->_tblTourName = $_tblTourObj->info('name');  
        $this->_tblTourKey  = $this->_map['vorgang']['columns'];         
        
        // Quoted Identifiers
        $this->rsrcTbl    = $this->_db->quoteIdentifier($this->_tblRsrcName);
        $this->rsrcKey    = $this->_db->quoteIdentifier($this->_tblRsrcKey);
        $this->rsrcLnkTbl = $this->_db->quoteIdentifier( $this->_tblRsrcLnkName );
        $this->rsrcLnkKey = $this->_db->quoteIdentifier( $this->_tblRsrcLnkKey );
//        echo $this->rsrcKey . ';' . $this->rsrcLnkKey . '<br>' . PHP_EOL;
        $this->tourTbl    = $this->_db->quoteIdentifier( $this->_tblTourName );
        $this->tourKey    = $this->_db->quoteIdentifier( $this->_tblTourKey );        
    }
    
    public function countUnerfassteZeitenByTourId($tourid) {
        $sql = 'SELECT SUM( IF(einsatzdauer IS NULL' 
             . ( ($this->_resourceType == 'FP') ? ' AND km IS NULL' : '') . ', 1, 0)) '
             . ' FROM ' . $this->_db->quoteIdentifier($this->_tbl) . ' tr '
             . ' JOIN ' . $this->rsrcTbl . ' r ON (r.' . $this->rsrcKey . ' = tr.' . $this->rsrcLnkKey . ')'
             . ' WHERE tour_id = :tourid';
        $cnt = $this->_db->fetchOne($sql, array('tourid' => $tourid));
        if ($cnt) echo '//#' . __LINE__ . ' ' . __METHOD__ . ' ' . $cnt. '; tourid:'.$tourid.'; Treffer => sql: ' . str_replace(':tourid', $tourid, $sql) . '<br>' . PHP_EOL;
        return $cnt; //$this->_db->fetchOne($sql, array('tourid' => $tourid));
    }
    
    public function getRsrcKey() {
        return $this->_prmRsrcKey;
    }
    
    /**
     * @param array $data
     * @param array
     * @return int|null new id or null if error
     * @throws Exception
     */
    public function drop($data, array $tourData = [])
    {
        if (!empty($data[$this->tourKey]) && empty($data[$this->_prmTourKey])) 
            $data[$this->_prmTourKey] = $data[$this->tourKey];
        
        // Pre-Condition
        if (!array_key_exists($this->_prmRsrcKey, $data)) {
            throw new Exception('#'.__LINE__.' Missing ' . $this->_prmRsrcKey . ' in data: '. print_r($data,1));
        }
        if (!array_key_exists($this->_prmTourKey, $data)) {
            throw new Exception('#'.__LINE__.' Missing ' . $this->_prmTourKey . ' in data: '. print_r($data,1));
        }

        $missing = array();
        if (!(int)$data[$this->_prmRsrcKey]) {
            $missing[] = 'Fehlende oder leere Resource ('.$this->_resourceName.') '.$this->_prmRsrcKey.'!';
        }
        if (!(int)$data[$this->_prmTourKey]) {
            $missing[] = 'Fehlende oder leere TourId '.$this->_prmTourKey.'!';
        }
        
        if (count($missing)){
            throw new Exception(implode('\n', $missing) . PHP_EOL . ' data:' . print_r($data, 1));
        }
        
        $rsrcId = (int)$data[$this->_prmRsrcKey];
        $tourId = (int)$data[$this->_prmTourKey];
        
        // Hole Fuhrpark-Record
        /* @var $rsrc Model_Fuhrpark  */
        $modelClass = $this->_resourceModel;
        $rsrc = new $modelClass();
        $rsrcEntry = $rsrc->fetchEntry( $rsrcId );
        
        if (!$rsrcEntry)
            throw new Exception(
                "Ungueltige Resource-ID (".$this->_resourceName."): `".$rsrcId."`. "
               ."Eintrag wurde nicht gefunden");

        if (empty($tourData)) {
            // Hole Tour-Record
            $tour = new Model_TourenDispoVorgaenge();
            $tourEntry = $tour->fetchEntry( $tourId );
        } else {
            $tourEntry = $tourData;
        }
        if (!$tourEntry) {
            throw new Exception("Ungueltige Tour-ID `" . $tourId . "`. Toureneintrag wurde nicht gefunden");
        }
        
        $db = $this->_db;
        
        // Hole Timeline-ID
        $sql = 'SELECT timeline_id, IsDefault FROM '.$this->tourTbl
              .' WHERE '.$this->tourKey.' = '.$tourId;
        $row = $db->fetchRow($sql);
        $tid = (int) $row['timeline_id'];
        $IsDefault = (int) $row['timeline_id'];
        
        $filter = array(
            'DatumVon' => $tourEntry['DatumVon'],
            'DatumBis' => $tourEntry['DatumBis'],
            'ZeitVon'  => $tourEntry['ZeitVon'],
            'ZeitBis'  => $tourEntry['ZeitBis']);

        $check = $this->checkResourceIsFree($rsrcId, $filter, $tourId, ($IsDefault)?$tourEntry['timeline_id']:0);

        if ($check->free) {
            $id = $this->insert(array(
                $this->_tblRsrcLnkKey => $rsrcId,
                $this->_tblTourKey => $tourId
            ));
            if ($id) return $id;
            else {
                throw new Exception('Ungueltige Daten. Tour konnte nicht gespeichert werden!' . PHP_EOL . print_r($data,1));
            }
        } else {
            throw new Exception( $check->message . PHP_EOL . $check->sql );
        }
        return null;
    }
    
    /**
     * @param array $data
     * @param string|int $toPos
     * @return int|null new id or null if error
     * @throws Exception
     */
    public function dropDefault($data)
    {        
        // Pre-Condition
        // require fid: fuhrpark_id
        // require tour_id
        $missing = array();
        if (!(int)$data[$this->_prmRsrcKey]) $missing[] = 'Fehlende oder leere Resource ('.$this->_resourceName.') '.$this->_prmRsrcKey.'!';
        if (!(int)$data[$this->_prmTourKey]) $missing[] = 'Fehlende oder leere TourId '.$this->_prmTourKey.'!';
        if (count($missing))            throw new Exception(implode('\n', $missing));
        
        $rsrcId = (int)$data[$this->_prmRsrcKey];
        $tourId = (int)$data[$this->_prmTourKey];
        // Hole Fuhrpark-Record
        /* @var $rsrc Model_Fuhrpark  */
        $modelClass = $this->_resourceModel;
        $rsrc = new $modelClass();
        $rsrcEntry = $rsrc->fetchEntry( $rsrcId );
        
        if (!$rsrcEntry)
            throw new Exception("Ungueltige Resource-ID (".$this->_resourceName."): `".$rsrcId."`. Eintrag wurde nicht gefunden");
        
        // Hole Tour-Record
        $tour = new Model_TourenDispoVorgaenge();
        $tourEntry = $tour->fetchEntry( $tourId );
        if (!$tourEntry)
            throw new Exception("Ungueltige Tour-ID `".$tourId."`. Toureneintrag wurde nicht gefunden");
        
        $db = $this->_db;
        
        // Hole Timeline-ID
        $tid = $db->fetchOne('SELECT timeline_id FROM '.$this->tourTbl.' WHERE '.$this->tourKey.' = '.$tourId);
        
        
        // Prüfen, ob Resource disponierbar ist:
        // - Zeitliche überschneidung (bereits verbucht)
        // - für Disposition freigegeben
        
        $sql = 'SELECT * FROM '.$this->rsrcLnkTbl.' r
            LEFT JOIN '.$this->tourTbl.' t 
            ON r.'.$this->tourKey.' = t.'.$this->tourKey.'
            WHERE '.$this->rsrcLnkKey.' = :rid
            AND t.DatumVon >= :datumVon
            AND t.DatumBis <= :datumBis
            AND t.ZeitVon >= :zeitVon
            AND t.ZeitBis <= :zeitBis
            AND (t.IsDefault != 1 OR t.timeline_id != '.$tid.')
            ';        
        
        $rowsBelegt = $this->_db->fetchAll($sql, array(
            ':rid' => $rsrcId,
            ':datumVon' => $tourEntry['DatumVon'],
            ':datumBis' => $tourEntry['DatumBis'],
            ':zeitVon'  => $tourEntry['ZeitVon'],
            ':zeitBis'  => $tourEntry['ZeitBis']
        ), Zend_Db::FETCH_ASSOC);
        
        if (count($rowsBelegt)) {
            throw new Exception('Resource ist bereits verbucht!');
        }
        
        $id = $this->insert(array(
            $this->_tblRsrcLnkKey => $rsrcId,
            $this->_tblTourKey => $tourId
        ));
        if ($id) return $id;
        else {
            throw new Exception('Ungueltige Daten. Tour konnte nicht gespeichert werden!' . PHP_EOL . print_r($data,1));
        }
        return null;
    }
    
    /**
     * @param array $data
     * @param string|int $toPos
     * @return int|null new id or null if error
     * @throws Exception
     */
    public function move($data) 
    {
        $returnObject = new stdClass();
        $returnObject->gebuchteTouren = array();
        $returnObject->lockedTouren   = array();
        $returnObject->success        = false;
        $returnObject->message        = '';
        
        $rsrcId = (int)$data[$this->_prmRsrcKey];
        $tourId = (int)$data[$this->_prmTourKey];
        
        // Hole Fuhrpark-Record
        /* @var $rsrc Model_Fuhrpark  */
        $modelClass = $this->_resourceModel;
        $rsrc = new $modelClass();
        $rsrcTbl = $rsrc->getStorage()->info(Zend_Db_Table::NAME);
        $rsrcEntry = $rsrc->fetchEntry( $rsrcId );
        
        $tour = new Model_TourenDispoVorgaenge();
        $tourTbl = $tour->getStorage()->info(Zend_Db_Table::NAME);
        
        
        $rsrcDispo = MyProject_Model_Database::loadModel($this->_storageName);
        $rsrcDispoTbl = $rsrcDispo->getStorage()->info(Zend_Db_Table::NAME);
        $rsrcDispoEntry = $rsrcDispo->fetchEntry($data['id']);
        if (!$rsrcDispoEntry)
            throw new Exception("Ungueltige Resourcen-Dispo-ID (".$this->_storageName."): `".$data['id']."`. Eintrag wurde nicht gefunden");
        
        if (!$rsrcEntry)
            throw new Exception("Ungueltige Resource-ID (".$this->_resourceName."): `".$rsrcId."`. Eintrag wurde nicht gefunden");
        
        // Hole Record der bisher zugeordneten Tour
        $srcTourEntry = $tour->fetchEntry( $rsrcDispoEntry['tour_id'] );
//        die(print_r($srcTourEntry,1));
        
        // Hole Record der neu zugeordneten Tour
        $dstTourEntry = $tour->fetchEntry( $tourId );
        
        if ($srcTourEntry['IsDefault']) {
            // Lösungsidee für Default-Resourcen
            // Erst alle Buchungs-ID für die Resource aus alter Timeline holen
            // Resourcen-Prüfung mit Ausnahme für ermittelte Buchungs-IDs
            // Wenn frei:
            // Einträge mit den alten Buchungs-IDs löschen
            // Neue Einträge anlegen: Die Aufgabe kann wieder die Methode drop übernehmen
            $tdaTbl = MyProject_Model_Database::loadStorage('tourenDispoAuftraege')->info(Zend_Db_Table::NAME);

            $sql = 'SELECT * FROM '.$tourTbl . ' t '
                  .' LEFT JOIN '.$rsrcDispoTbl . ' r ON (t.tour_id = r.tour_id) '
                  .' LEFT JOIN '.$tdaTbl . ' a ON (t.Mandant = a.Mandant AND t.Auftragsnummer = a.Auftragsnummer) '
                  .' WHERE t.timeline_id = ' . $srcTourEntry['timeline_id']
                  .' AND r.'.$this->rsrcLnkKey . ' = ' . $rsrcEntry[$this->_prmRsrcKey];
            $gebuchteTouren = $this->_db->fetchAll($sql);
        } else {
            $gebuchteTouren = array($srcTourEntry);
        }
        
        $lockedTouren = array();
        $filterTourIds= array();
        foreach($gebuchteTouren as $_tour) 
        {
            if ($_tour['auftrag_abgeschlossen_user']) {
                $lockType = 'Auftrag abgeschlossen';
            } elseif ($_tour['auftrag_disponiert_user']) {
                $lockType = 'Auftrag disponiert';
            } elseif ($_tour['tour_abgeschlossen_user']) {
                $lockType = 'Tour abgeschlossen';
            } elseif ($_tour['tour_disponiert_user']) {
                $lockType = 'Tour disponiert';
            } elseif ($_tour['locked']) { 
                $lockType = 'locked';
            } else {
                $lockType = '';
                $filterTourIds[] = $_tour['tour_id'];
            }
            
            if ($lockType) {
                $lockedTouren[$_tour['tour_id']] = $_tour;
                $lockedTouren[$_tour['tour_id']]['lockType'] = $lockType;
            }
        }
        
        $returnObject->gebuchteTouren = $gebuchteTouren;
        $returnObject->lockedTouren   = $lockedTouren;
        
        if (count($lockedTouren)) {
            $returnObject->message = 'Resource befindet sich in bereits geschlossenen oder gesperrten Vorgaengen:' . PHP_EOL;
            foreach($lockedTouren as $_tour_id => $_tour) {
                $returnObject->message.= '-' .$_tour['lockType'] . ': ' . $_tour['Auftragsnummer'] . ' '.$_tour['DatumVon'].' '.$_tour['ZeitVon'] . PHP_EOL;
            }
            if ($checkStrict = 0) return $returnObject;
        }
        
        $returnObject->rsrcConflictData = array();
        $filter = array_merge( $dstTourEntry, array('ignoreTourIds' => $filterTourIds));
        $_chckFree = $this->checkResourceIsFree($rsrcId, $filter, $dstTourEntry['tour_id']);
        if (!$_chckFree->free) $returnObject->rsrcConflictData = $_chckFree->data;
        
        if (count($returnObject->rsrcConflictData)) {
            $returnObject->message = 'Konflikt: Resource ist für den Zielzeitraum bereits gebucht oder gesperrt!' . PHP_EOL;
            foreach($returnObject->rsrcConflictData as $_tour) {
                $returnObject->message.= '-' . $_tour['Auftragsnummer'] . ' '.$_tour['DatumVon'].' '.$_tour['ZeitVon'] . PHP_EOL;
            }
            return $returnObject;
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        try {
            $db->beginTransaction();
            $sql = 'DELETE FROM ' . $this->_tbl . ' WHERE ' . $this->tourKey . ' IN ('.implode(',', $filterTourIds) . ') AND ' . $this->rsrcLnkKey . ' = '.$rsrcId;
            $db->query($sql);
            $newId = $this->drop(array_merge($dstTourEntry, $rsrcEntry, array('route_id'=>$dstTourEntry['tour_id'])));
            $returnObject->success = true;
            $returnObject->dispoRsrcId = $newId;
            $db->commit();
        } catch(Exception $e) {
            $db->rollBack();
            throw new Exception('Logik-Fehler: Resourcen konnten nicht verschoben werden: ' . $e->getMessage() . '; sql:'.$sql);
        }
//        die('<pre>#'.__LINE__ . ' ' . __METHOD__ . ' gebuchteTouren: ' . print_r($gebuchteTouren).'</pre>');
        
        return $returnObject;
    }
    
    public function getDefaultsIds( $tid )
    {
        $db  = $this->_db;
        
        // Hole alle Touren der Timeline, 
        // denen die Resource noch nicht hinzugefügt wurden
        $sql = 'SELECT r.'.$this->rsrcLnkKey.' FROM '.$this->tourTbl.' t JOIN '
              . $this->rsrcLnkTbl.' r USING('.$this->tourKey.') '
              .' WHERE timeline_id = '.$db->quote($tid).' AND IsDefault = 1';
        //echo '#' . __LINE__ . ' sql: ' . $sql . PHP_EOL;
        return $db->fetchCol($sql);
    }
    
    public function addTourDefaults( $tourData )
    {
        // Hole Ids der Resourcen, die als Default der Timeline zugewiesen wurden
        $rsrcIds = $this->getDefaultsIds( $tourData['timeline_id'] );
        //echo '#' . __LINE__ . ' rsrcIds:' . print_r($rsrcIds,1) . PHP_EOL;
        
        // Default-Resourcen der Tour zuweisen
        foreach($rsrcIds as $_rid) {
            $id = $this->insert(array(
                $this->_tblRsrcLnkKey => $_rid,
                $this->_tblTourKey => $tourData['tour_id']
            ));
        }        
    }
    
    /**
     * @param array $data ResourceData
     */
    public function applyDefaults( $data, $tid = 0 )
    {
        $rid     = (int)$data[$this->_tblRsrcLnkKey];
        $tourId  = (int)$data[$this->_tblTourKey];
        $db      = $this->_db;
        
        if (!$tid) {
            $sql = 'SELECT timeline_id FROM '.$this->tourTbl.' WHERE '.$this->tourKey.' = '.$db->quote($tourId);
            $tid = $db->fetchOne($sql);        
//            echo '#'.__LINE__.' '.__METHOD__.' timeline_id:'.$tid.'<br/>' . PHP_EOL . $sql . '<br/>' . PHP_EOL; 
        }

        // $this->tourKey, timeline_id, IsDefault, DatumVon, DatumBis, ZeitVon, ZeitBis
        // Hole alle Touren der Timeline, 
        // denen die Resource noch nicht hinzugefügt wurden
        $sql = 'SELECT '.$this->tourKey .', timeline_id, IsDefault, DatumVon, DatumBis, ZeitVon, ZeitBis '
              .' FROM '.$this->tourTbl
              .' WHERE timeline_id = '.$db->quote($tid).' AND IsDefault = 0 '
              .' AND '.$this->tourKey.' NOT IN( '
              .'  SELECT t.'.$this->tourKey
              .'  FROM '.$this->tourTbl.' t '
              .'  JOIN '.$this->rsrcLnkTbl.' r USING('.$this->tourKey.') '
              .'  WHERE r.'.$this->rsrcLnkKey.' = '. $db->quote($rid)
              .'   AND t.timeline_id = ' . $db->quote($tid)
              .'  GROUP BY ' . $this->tourKey . ' '
              .')';
        
        $aTouren = $db->fetchAll($sql, [], Zend_Db::FETCH_ASSOC);
        // die(print_r([__LINE__, __FILE__, __METHOD__, 'sql' => $sql, 'aTouren'=>$aTouren]));
        // die('#' . __LINE__ . ' ' . __FILE__ . PHP_EOL . print_r($aTouren,1));

        header('X-Apply-Default:'
            .json_encode('#' . __LINE__ . ' ' .__METHOD__ . ' sql: ' . $sql . PHP_EOL
                . 'aTouren:' . PHP_EOL . print_r($aTouren,1)
            )
        );

        $lastElm = null;
        $aAppliedTourIDs = [];
        try {
//        echo '#'.__LINE__.' '.__METHOD__.' Add(drop) '.count($aTouren) . ':<br/>' . PHP_EOL . $sql . '<br/>' . PHP_EOL;
            foreach ($aTouren as $_tourData) {
                $lastElm = $_tourData;
                $_tour_id = $_tourData[$this->_tblTourKey];
//            echo '#'.__LINE__.' '.__METHOD__.' Drop Rsrc to tour_id: ' . $_tour_id . '<br/>' . PHP_EOL;
                $id = $this->drop(array(  // Nachträglich geändert von insert auf drop => Enthält Dispo-Prüfung
                    $this->_tblRsrcKey => $rid,
                    $this->_tblRsrcLnkKey => $rid,
                    $this->_tblTourKey => $_tour_id,
                    'route_id' => $_tour_id,
                    'timeline_id' => $tid,
                ), $_tourData);

                $aAppliedTourIDs[] = ['newID' => $id, 'tourID' => $_tour_id];
            }
        } catch(Exception $e) {
            throw $e;
        }

        return $aAppliedTourIDs;
    }
    
    /**
     * @param int $id ResourceLnkId
     */
    public function removeDefault($id)
    {     
        //die('#' . __LINE__  . ' ' . __METHOD__ . ' data: ' . print_r($data,1));
        $rows = $this->getStorage()->find($id);
        if (!$rows) return false;
        
        $data = $rows->current()->toArray();
        
        $rid     = (int)$data[$this->_tblRsrcLnkKey];
        $tourId  = (int)$data[$this->_tblTourKey];
        $db      = $this->_db;
        
        $tid = $db->fetchOne('SELECT timeline_id FROM '.$this->tourTbl.' '
              .'WHERE '.$this->tourKey.' = '.$db->quote($tourId));

        $where = " " . $this->_tblRsrcLnkKey . " = " . $rid . " AND " 
                ." " . $this->tourKey . " IN ("
                ." SELECT " . $this->tourKey . " FROM " . $this->_tblTourName
                ." WHERE timeline_id = " . $tid 
                .") ";

        $sqlFetch = "SELECT tour_id FROM " . $this->rsrcLnkTbl . ' WHERE  ' . $where;
        $aTourIDs = $db->fetchCol($sqlFetch);
        $iSelected = count($aTourIDs);

        $iDeleted = $db->delete($this->_tblRsrcLnkName, $where );

        if ($iDeleted != $iSelected) {
            throw new Exception("Die ermittelte Anzahl Touren ($iSelected) entspricht nicht der Anzahl Touren ($iDeleted) aus der die Ressource entfernt wrude!");
        }

        return $aTourIDs;
              
    }
    
    /**
     * @param int $tour_id
     * @param bool $keysOnly default = false, gibt alle Felder zurück, bei true nur die IDs
     * @return array withd IDs if keysOnly is true, else Rows with resource-Data, first Field is named id
     */
    public function getResourcesByTourId($tour_id, $keysOnly = false) 
    {
        $modelLstg = new Model_Db_Leistung();
        $tblLstg   = $modelLstg->info(Zend_Db_Table::NAME);
        
        $sql =
        'SELECT ' . ($keysOnly ? $this->rsrcKey : 'id, r.*, l.*, lg.*') . ' FROM '.$this->rsrcTbl.' r '
        .'LEFT JOIN '.$this->rsrcLnkTbl.' l ON(r.'.$this->rsrcKey.'=l.'.$this->rsrcLnkKey.')'
        .'LEFT JOIN '.$tblLstg.' lg ON(r.leistungs_id=lg.leistungs_id)'
        .'WHERE ' . $this->tourKey . ' = ' . (int)$tour_id;
        
        if ($keysOnly) return $this->_db->fetchCol ($sql);
        else return $this->_db->fetchAll($sql, Zend_Db::FETCH_ASSOC);
    }
    
    public function getResourceKey() {
        return $this->_tblRsrcKey;
    }
    
    public function getResourceLinkKey() {
        return $this->_tblRsrcLnkKey;
    }
    
    /**
     * @param int $tour_id
     * @param bool $keysOnly default = false, gibt alle Felder zurück, bei true nur die IDs
     * @return array withd IDs if keysOnly is true, else Rows with resource-Data, first Field is named id
     */
    public function getResourcesByTimelineId($timeline_id, $keysOnly = false) 
    {
        $tlStorage = MyProject_Model_Database::loadStorage('tourenTimelines');
        $tlTbl = $tlStorage->info(Zend_Db_Table::NAME);
        
        $tourStorage = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge');
        $tourTbl = $tourStorage->info(Zend_Db_Table::NAME);
        
        $sql =
        'SELECT ' . ($keysOnly ? $this->rsrcKey : 'id, r.*, l.*, t.DatumVon,t.DatumBis,t.ZeitVon,t.ZeitBis') . ' FROM '.$this->rsrcTbl.' r '
        .'LEFT JOIN '.$this->rsrcLnkTbl.' l ON(r.'.$this->rsrcKey.'=l.'.$this->rsrcLnkKey.')'
        .'LEFT JOIN '.$tourTbl.' t ON(l.'.$this->tourKey.'=t.'.$this->tourKey.')'
        .'WHERE t.timeline_id = ' . (int)$timeline_id;
        
        if ($keysOnly) return $this->_db->fetchCol ($sql);
        else return $this->_db->fetchAll($sql, Zend_Db::FETCH_ASSOC);
    }
    
    /**
     * @abstract
     * Prüft, ob Resource für Ziel-Slot (siehe Param $filter) frei und gibt ergebnis-Objekt mit Details zurück
     * tourData enthält rows mit den Feldern ResourceId, Resource und allen tour-Feldern
     * @param int $rsrc_id
     * @param array $filter assoc (DatumVon=>YYYY-MM-DD,DatumBis=>YYYY-MM-DD,ZeitVon=>HH:MM,ZeitBis=>HH:MM
     * @param int $tour_id
     * @param int $timeline_id
     * @return stdClass with members: bool free (true if free), data: tourdata, where rsrc is booked, message
     */
    public function checkResourceIsFree($rsrc_id, $filter, $tour_id = 0, $timeline_id = 0) 
    {
        $re = new stdClass();
        $re->free = false;
        $re->data = array();
        $re->message = '';
        $re->sql = '';
        
        $szStorage = MyProject_Model_Database::loadStorage('resourcesSperrzeiten');
        $szTable   = $szStorage->info(Zend_Db_Table::NAME);
        
        $db = $this->_db;
        $re->sql = 
             'SELECT extern_id ' . PHP_EOL
            .'FROM '.$this->rsrcTbl.' r ' . PHP_EOL
            .'WHERE '. PHP_EOL
            .$this->rsrcKey . ' = ' . (int)$rsrc_id . PHP_EOL
			;
        
        $extern_id = (int)$db->fetchOne($re->sql);

		if ($extern_id) {
			$re->free = true;
			return $re;
		}
		
        $dVon = (array_key_exists('DatumVon', $filter) ? $filter['DatumVon'] : '' );
        $dBis = (array_key_exists('DatumBis', $filter) ? $filter['DatumBis'] : '' );
        $zVon = (array_key_exists('ZeitVon',  $filter) ? $filter['ZeitVon']  : '' );
        $zBis = (array_key_exists('ZeitBis',  $filter) ? $filter['ZeitBis']  : '' );

        if (array_key_exists('ignoreTourIds', $filter)) {
            $ignoreTourIds = (array)$filter['ignoreTourIds'];
        } else {
            $ignoreTourIds = array();
        }

        if ($tour_id) {
            $ignoreTourIds[] = $tour_id;
        }
        
        $where = '';
        if ($ignoreTourIds) {
            $where.= $this->tourKey.' NOT IN (' . implode(',', $ignoreTourIds) . ') ' . PHP_EOL;
        }
        if ($timeline_id) {
            $where.= 
                ($where ? ' AND ' : '')
               .'(timeline_id != ' . (int)$timeline_id . ' OR IsDefault = 0)' . PHP_EOL;
        }
        
        if ($dVon || $dBis || $zVon || $zBis) {
            $where.= ($where ? ' AND ' : '') . ' (';
            if ($dVon || $dBis ) {
                $where.= ' (';
                if ($dVon) {
                    $where.= $db->quoteInto('(t.DatumVon <= ? AND t.DatumBis >= ?)', $dVon) . PHP_EOL;
                }
                if ($dBis && $dBis !== $dVon) {
                    $where.= ($dVon ? ' OR ' :'')
                           . $db->quoteInto('(t.DatumVon <= ? AND t.DatumBis >= ?)', $dBis) . PHP_EOL;
                }
                $where.= ')';
            }

            if ($zVon || $zBis) {
                $where.= ' AND (';
                if ($zVon) {
                    $where.= $db->quoteInto('(t.ZeitVon <= ? AND t.ZeitBis > ?)', $zVon) . PHP_EOL;
                }
                if ($zBis && $zBis !== $zVon) {
                    $where.= ($zVon ? ' OR ' :'')
                          .  $db->quoteInto('(t.ZeitVon < ? AND t.ZeitBis >= ?)', $zBis) . PHP_EOL;
                }
                $where.= ')';
            }
            $where.= ')';
        }
        
        if ($dVon || $dBis ) {
            $condRsrcTyp = $db->quoteInto('ressourcen_typ = ?', $this->_resourceType);
            if ($dVon) {
                $where.= ($where ? ' OR ' :'')
                       . ' (' . $condRsrcTyp . ' AND '.$db->quoteInto('gesperrt_von <= ? AND gesperrt_bis >= ?', $dVon).') ' . PHP_EOL;
            }
            if ($dBis && $dBis !== $dVon) {
                $where.= ($where ? ' OR ' :'')
                       . ' (' . $condRsrcTyp . ' AND '.$db->quoteInto('gesperrt_von <= ? AND gesperrt_bis >= ?', $dBis).')' . PHP_EOL;
            }
        }

        $re->sql = 
             'SELECT '.$this->rsrcKey.' ResourceId, '.$this->_rsrcTitleField.' AS Resource, t.*, ' . PHP_EOL
            .' s.gesperrt_von, s.gesperrt_bis ' . PHP_EOL
            .'FROM '.$this->rsrcTbl.' r ' . PHP_EOL
            .'LEFT JOIN '.$this->rsrcLnkTbl.' ON('.$this->rsrcKey.'='.$this->rsrcLnkKey.') ' . PHP_EOL
            .'LEFT JOIN '.$this->tourTbl.' t USING('. $this->tourKey . ') ' . PHP_EOL
            .'LEFT JOIN '.$szTable.' s ON(' . PHP_EOL
            .'  s.ressourcen_typ = '.$db->quote($this->_resourceType) . ' ' . PHP_EOL
            .'  AND s.ressourcen_id = ' . $this->rsrcKey . ') ' . PHP_EOL
            .'WHERE '. PHP_EOL
            .$this->rsrcKey . ' = ' . (int)$rsrc_id . PHP_EOL
           .' AND (' . $where . ')';
        
        $re->data = $db->fetchAll($re->sql);
        $re->free = (!is_array($re->data) || count($re->data) == 0);
        if (count($re->data)) {
            $re->message = 'Konflikt: Resource ist für den Zielzeitraum nicht disponierbar!' . PHP_EOL;
            foreach($re->data as $_d) {
                if ($_d['tour_id'])
                    $re->message.= '-Gebucht für' . $_d['Auftragsnummer'] . ' '.$_d['DatumVon'].' '.$_d['ZeitVon'] . PHP_EOL;
                else
                    $re->message.= '-Gesperrt von '.$_d['gesperrt_von'].' bis '.$_d['gesperrt_bis'] . PHP_EOL;
            }
        }
        return $re;
    }
    
    // TOUR-RESOURCE-BASE-FILTER
    // Aufruf z.B. aus FuhrparkControler::gridresponsedataAction() 
    /**
     * Liefert SQL-Abfrage, der als Negativ-Ausdruck ( tour_id NOT IN({subSql}) als Sub-Sql verwendet werden kann
     * @param array $filter
     * @return string sql 
     */
    public function getTourResourceFilterSql($filter) 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        $szStorage = MyProject_Model_Database::loadStorage('resourcesSperrzeiten');
        $szTable   = $szStorage->info(Zend_Db_Table::NAME);
        
        $dzStorage = MyProject_Model_Database::loadStorage('resourcesDispozeiten');
        $dzTable   = $szStorage->info(Zend_Db_Table::NAME);

        // START Tour-Filter
        $tourId  = (array_key_exists('tour_id', $filter) ? $filter['tour_id'] : '' );
        $showExt = (int) (array_key_exists('showExt', $filter) ? $filter['showExt'] : 0 );
        $subSql = '';
        $subSqlWhere = '';
        $chckTSql = '';

        if ($tourId) {
            $sql = 'SELECT * FROM '.$this->tourTbl.' ';
            $sql.= 'WHERE '.$this->tourKey.' = ' . $db->quote($tourId);
            $row = $db->fetchRow($sql);
            
            $tid = $row['timeline_id'];
            $IsDefault = $row['IsDefault'];
            $dVon = $row['DatumVon'];
            $dBis = $row['DatumBis'];
            $zVon = $row['ZeitVon'];
            $zBis = $row['ZeitBis'];
            
            if (!$IsDefault) $chckTSql = 
                ' (timeline_id != ' . $tid . (!$IsDefault ? ' OR IsDefault = 0' : '').')';
            
        } else {
            $dVon  = (array_key_exists('DatumVon', $filter) ? $filter['DatumVon'] : '' );
            $dBis  = (array_key_exists('DatumBis', $filter) ? $filter['DatumBis'] : '' );
            $zVon  = (array_key_exists('ZeitVon',  $filter) ? $filter['ZeitVon']  : '' );
            $zBis  = (array_key_exists('ZeitBis',  $filter) ? $filter['ZeitBis']  : '' );
        }
        
        $chckD = ($dVon  || $dBis);
        $chckZ = ($zVon  || $zBis);
        $chckDZ = ($chckD || $chckZ);
        
        if ($chckTSql) $subSqlWhere.=
            $chckTSql . PHP_EOL
           .($chckDZ ? ' AND (' : '') . PHP_EOL;
        
        if ($chckDZ)$subSqlWhere.=
            '(' . PHP_EOL;
        
        if ($chckD) $subSqlWhere.= 
            ' (' . PHP_EOL;
        if ($dVon)  $subSqlWhere.= $db->quoteInto(
            '  (t.DatumVon <= ? AND t.DatumBis >= ?)', $dVon) . PHP_EOL;

        if ($dBis && $dBis !== $dVon)  $subSqlWhere.= 
            ( $dVon ? 
            '  OR ' . PHP_EOL :'' ) . $db->quoteInto(
            '  (t.DatumVon <= ? AND t.DatumBis >= ?)', $dBis) . PHP_EOL;
        
        if ($chckD) $subSqlWhere.= 
            ' )' . PHP_EOL;

        if ($chckZ) $subSqlWhere.= 
            ($chckD ? 
            ' AND ' : '')  . PHP_EOL
           .' (' . PHP_EOL;

        if ($zVon)  $subSqlWhere.= $db->quoteInto(
            '  (t.ZeitVon <= ? AND t.ZeitBis > ?)', $zVon) . PHP_EOL;

        if ($zBis && $zBis !== $zVon)  $subSqlWhere.= 
            ( $zVon ? 
            '  OR ' :'') . PHP_EOL . $db->quoteInto(
            '  (t.ZeitVon < ? AND t.ZeitBis >= ?)', $zBis) . PHP_EOL;

        if ($chckZ) $subSqlWhere.= 
            ' )' . PHP_EOL;

        if ($chckDZ)$subSqlWhere.=
            ')' . PHP_EOL;
        
        if ($chckTSql && $chckDZ) $subSqlWhere.=
            ')' . PHP_EOL;    
        
        if ($dVon) $subSqlWhere.= $db->quoteInto(
            ' OR (gesperrt_von <= ? AND gesperrt_bis >= ?) ', $dVon ) . PHP_EOL;
        
        if ($dBis && $dBis !== $dVon) $subSqlWhere.= $db->quoteInto(
            ' OR (gesperrt_von <= ? AND gesperrt_bis >= ?) ', $dBis ) . PHP_EOL;
        
        if ( $subSqlWhere ) $subSql = 
             'SELECT '.$this->rsrcKey.' FROM '.$this->rsrcTbl.' ' . PHP_EOL
            .'  LEFT JOIN '.$this->rsrcLnkTbl.' ON('.$this->rsrcKey.'='.$this->rsrcLnkKey.')' . PHP_EOL
            .'  LEFT JOIN '.$this->tourTbl.' t USING('. $this->tourKey . ')' . PHP_EOL
            .'  LEFT JOIN '.$szTable.' s ON(' . PHP_EOL
            .'  s.ressourcen_typ = '.$db->quote($this->_resourceType) . ' ' . PHP_EOL
            .'  AND s.ressourcen_id = ' . $this->rsrcKey . ') ' . PHP_EOL
            .'WHERE '. $subSqlWhere;
        // ENDE Tour-Filter        
        
        return $subSql;
    }

    /**
     * @param int $id Ressource-ID (e.g. Mitarbeiter-ID)
     * @param string $DatumVon format YYYY-mm-dd
     * @param string $DatumBis format YYYY-mm-dd
     * @return array [ [ id, tour_id, DatumVon, DatumBis ] ]
     */
    public function getTourlistByIdAndDaterange($id, $DatumVon, $DatumBis, bool $bWithDefaultTour = false)
    {
        $dbDatumVon = $this->_db->quote($DatumVon);
        $dbDatumBis = $this->_db->quote($DatumBis);

        $sqlFetch = 'select r.id, t.tour_id, t.DatumVon, t.ZeitVon '
            .'FROM '.$this->rsrcLnkTbl.' r '
            .' LEFT JOIN mr_touren_dispo_vorgaenge t USING(tour_id) '
            .' WHERE '.$this->_rsrcLnkKey .' = ' . intval($id);

        if ($bWithDefaultTour === false) {
            $sqlFetch .= '  AND t.IsDefault = 0 ';
        }

        $sqlFetch.= '  AND DatumVon >= ' . $dbDatumVon
            .'  AND DatumVon <= ' . $dbDatumBis
            .' ORDER BY DatumVon, ZeitVon, IsDefault';

        return $this->_db->fetchAll( $sqlFetch );
    }

    /**
     * @param int $id
     * @param array $aTourIDs
     * @param null $sperrzeiten_id
     * @return int
     * @throws Zend_Db_Statement_Exception
     */
    public function removeRessourceFromTourlist($id, $aTourIDs, $sperrzeiten_id = null)
    {
        /** @var Model_TourenDispoLog $modelLogger */
        $modelLogger = MyProject_Model_Database::loadModel('tourenDispoLog');

        $reCountRemoved = 0;
        foreach($aTourIDs as $_tourID) {

            $sqlDelete = 'DELETE FROM '.$this->rsrcLnkTbl.' '
                .' WHERE '.$this->_rsrcLnkKey .' = ' . intval($id)
                .'  AND tour_id = ' . $this->_db->quote($_tourID);

            $stmt = $this->_db->query($sqlDelete);

            if ($stmt->rowCount()) {
                $reCountRemoved++;
                if (0) die(print_r([
                    'line' => __LINE__,
                    'file' => __FILE__,
                    'rsrcType' => $this->_resourceType,
                    'id' => $id,
                    'action' => 'removed',
                    'tour_id' => $_tourID,
                    'sperrzeiten_id' => $sperrzeiten_id
                ],1));
                $modelLogger->logResource($this->_resourceType, $id, 'removed', $_tourID, null, $sperrzeiten_id);
            }
        }

        return $reCountRemoved;
    }


    public function getTourlistByResourceId($id, array $listOpts = [])
    {
        $re = new stdClass();
        $offset = (isset($listOpts['offset']) && is_numeric($listOpts['offset'])) ? $listOpts['offset'] : 0;
        $limit = (isset($listOpts['count']) && is_numeric($listOpts['count'])) ? $listOpts['count'] : 100;
        $sidx = (!empty($listOpts['sidx'])) ? $listOpts['sidx'] : 't.Datumvon';
        $sord = (!empty($listOpts['sord']) && in_array($listOpts['sord'], ['ASC','DESC'])) ? $listOpts['sord'] : 'ASC';

        if ($sidx === 't.DatumVon') {
            $sidx = "t.DatumVon $sord, t.ZeitVon";
        }

        if (empty($listOpts['where'])) {
            $andWhere = ' AND t.DatumVon >= ' . $this->_db->quote(date('Y-m-d'));
        } elseif (strpos($listOpts['where'], 'DatumVon') === false) {
            $andWhere = ' AND t.DatumVon >= ' . $this->_db->quote(date('Y-m-d')) . ' AND (' . $listOpts['where'] . ')';
        } else {
            $andWhere = ' AND (' . $listOpts['where'] . ')';
        }

        $sqlCount = 'select count(1) '
            .'FROM '.$this->rsrcLnkTbl.' r '
            .' LEFT JOIN mr_touren_dispo_vorgaenge t USING(tour_id) '
            .(strpos($andWhere, 'ak.') === false ? '' :
                ' LEFT JOIN mr_auftragskoepfe_dispofilter ak ON (t.Mandant = ak.Mandant AND t.Auftragsnummer = ak.Auftragsnummer)')
            .(strpos($andWhere, 'p.') === false ? '' :
                 ' LEFT JOIN mr_touren_timelines tl ON (t.timeline_id = tl.timeline_id) '
                .' LEFT JOIN mr_touren_portlets p ON (tl.portlet_id = p.portlet_id) '
            )
            .' WHERE '.$this->_rsrcLnkKey .' = ' . intval($id) . ' AND t.IsDefault = 0 ' . $andWhere;

        $sqlFetch = 'select r.id, '.$this->_rsrcLnkKey .' AS ressourcen_id, t.tour_id, p.tagesnr, ak.Mandant, ak.Auftragsnummer, '
            .'"' . $this->_resourceType . '" AS ressourcen_typ, '
            .'ak.LieferungName, ak.Vorgangstitel, t.DatumVon, t.ZeitVon, t.ZeitBis, t.DatumBis '
            .'FROM '.$this->rsrcLnkTbl.' r '
            .' LEFT JOIN mr_touren_dispo_vorgaenge t USING(tour_id) '
            .' LEFT JOIN mr_auftragskoepfe_dispofilter ak ON (t.Mandant = ak.Mandant AND t.Auftragsnummer = ak.Auftragsnummer) '
            .' LEFT JOIN mr_touren_timelines tl ON (t.timeline_id = tl.timeline_id) '
            .' LEFT JOIN mr_touren_portlets p ON (tl.portlet_id = p.portlet_id) '
            .' WHERE '.$this->_rsrcLnkKey .' = ' . intval($id) . ' AND t.IsDefault = 0 ' . $andWhere . ' '
            .' ORDER BY ' . $sidx . ' ' . $sord
            .' LIMIT ' . $offset . ', ' . $limit;

        $re->total_records = $this->_db->fetchOne( $sqlCount );
        // die('sqlCount: ' . $sqlCount . ': ' . $re->total_records . PHP_EOL . $sqlFetch);
        $re->total_pages   = ($re->total_records && $limit) ? ceil($re->total_records / $limit) : 0;
        $re->page = ceil($offset/$limit)+1;
        if ($re->page > $re->total_pages) $re->page = $re->total_pages;
        $re->rows  = $this->_db->fetchAll( $sqlFetch );
        return $re;

    }
}

