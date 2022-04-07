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
    
    // Start: Zu �berschreibende Properties
    protected $_storageName   = '';  // Bsp: 'tourenDispoFuhrpark';
    protected $_resourceName  = ''; // Bsp: 'Fuhrpark';
    protected $_resourceModel =''; // Bsp:  'Model_Fuhrpark';
    protected $_resourceType  ='';  // Bsp:  MA|FP|WZ;
    protected $_prmRsrcKey    = '';   // Bsp: 'fid';
    // Ende: Zu �berschreibende Properties
    
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

    protected $_tblCtgName = '';
    protected $_tblCtgKey = 'category_id';

    protected $_tblCtgLnkName = '';
    protected $_tblCtgLnkKey = 'category_id';
    protected $_tblCtgLnkRsrcKey = ''; // mitarbeiter_id | fuhrpark_id | werkzeug_id


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

        $this->categoryTbl= $this->_db->quoteIdentifier( $this->_tblCtgName );
        $this->categoryLnkTbl= $this->_db->quoteIdentifier( $this->_tblCtgLnkName );
    }

    abstract public function getSqlSelectExprAsLabel(): string;


    public function dispoLog(int $rsrc_id, string $action, int $tour_id, array $aDetails = []) {
        $uid = MyProject_Auth_Adapter::getUserId();
        $sperrzeiten_id = $aDetails['sperrzeiten_id'] ?? null;
        switch($this->_resourceType) {
            case 'MA':
                $this->getTourDispoLogger()->logResourceMA(
                    $rsrc_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails
                );
                break;

            case 'FP':
                $this->getTourDispoLogger()->logResourceFP(
                    $rsrc_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails
                );
                break;

            case 'WZ':
                $this->getTourDispoLogger()->logResourceWZ(
                    $rsrc_id, $action, $tour_id, $uid, $sperrzeiten_id, $aDetails
                );
                break;
        }
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

    public function getDatum(int $id): string
    {
        $modelTouren = new Model_TourenDispoVorgaenge();

        $sql = 'SELECT t.DatumVon FROM ' . $this->rsrcLnkTbl . ' lnk '
              .' LEFT JOIN ' . $modelTouren->getTable() . ' t '
              .' ON (lnk.tour_id = t.tour_id) '
              .' WHERE lnk.id = ' . (int)$id;

        return $this->_db->fetchOne($sql);
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
            if ($id) {
                $this->dispoLog(
                    $rsrcId, 'insert', $tourId,
                    $tourEntry + ['bemerkung' => json_encode($rsrcEntry)]
                );
                return $id;
            }
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
        
        
        // Pr�fen, ob Resource disponierbar ist:
        // - Zeitliche �berschneidung (bereits verbucht)
        // - F�r Disposition freigegeben
        
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
        if ($id) {
            $this->dispoLog(
                $rsrcId, 'insert-default', $tourId,
                $tourEntry + ['bemerkung' => json_encode($tourEntry)]
            );
            return $id;
        }
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
            // L�sungsidee f�r Default-Resourcen
            // Erst alle Buchungs-ID f�r die Resource aus alter Timeline holen
            // Resourcen-Pr�fung mit Ausnahme f�r ermittelte Buchungs-IDs
            // Wenn frei:
            // Eintr�ge mit den alten Buchungs-IDs l�schen
            // Neue Eintr�ge anlegen: Die Aufgabe kann wieder die Methode drop �bernehmen
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
        if (!$_chckFree->free) {
            $returnObject->rsrcConflictData = $_chckFree->data;
            $returnObject->message = $_chckFree->message;
            return $returnObject;
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        try {
            $db->beginTransaction();
            $sql = 'DELETE FROM ' . $this->_tbl . ' WHERE ' . $this->tourKey . ' IN ('.implode(',', $filterTourIds) . ') AND ' . $this->rsrcLnkKey . ' = '.$rsrcId;
            $db->query($sql);
            foreach($filterTourIds as $_tour_id) {
                $this->dispoLog(
                    $rsrcId, 'remove-move', $_tour_id,
                    $data + ['bemerkung' => json_encode($data)]
                );
            }

            $newId = $this->drop(
                array_merge($dstTourEntry, $rsrcEntry, array('route_id'=>$dstTourEntry['tour_id']))
            );

            if ($newId) {
                $this->dispoLog(
                    $rsrcId, 'apply-defaults', $dstTourEntry['tour_id'],
                    $dstTourEntry + ['bemerkung' => json_encode($dstTourEntry)]
                );
            }

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
        // denen die Resource noch nicht hinzugef�gt wurden
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
     * @param int $iTourId
     * @param int $iRsrcId
     * @return array
     */
    public function getTourenStatForApplyDefaultRsrc(int $iTourId, int $iRsrcId): array
    {
        $db = $this->_db;
        $tourRsrcTbl = $this->rsrcLnkTbl;
        $tourRsrcKey = $this->rsrcLnkKey;

        $tourTbl = $this->tourTbl;
        $tlTbl = Model_Db_TourenTimelines::obj()->tableName();
        $ptlTbl = Model_Db_TourenPortlets::obj()->tableName();

        $sqlTourenStat = "SELECT MIN(pt.datum) datum, MIN(dv2.ZeitVon) MinZeitVon,  Max(dv2.ZeitVon) MaxZeitBis, 
                 count(1) num_touren, dv.IsDefault, dv.timeline_id,
                 group_concat(DISTINCT dv2.tour_id) all_tour_ids,
                 group_concat(distinct if(dv2.locked = 1, dv2.tour_id, null)) locked_tour_ids,
                 group_concat(distinct if(dr.$tourRsrcKey IS NOT NULL, dv2.tour_id, null)) rsrc_in_tour_ids,
                 group_concat(distinct if(dr.$tourRsrcKey IS NULL && dv2.locked = 0, dv2.tour_id, null)) possible_tour_ids                 
             FROM $tourTbl dv
             JOIN $tlTbl tl ON(dv.timeline_id = tl.timeline_id)
             JOIN $ptlTbl pt ON(tl.portlet_id = pt.portlet_id)
             JOIN $tourTbl dv2 ON (dv.tour_id = dv2.tour_id OR (dv.IsDefault = 1 AND tl.timeline_id = dv2.timeline_id))
             LEFT JOIN $tourRsrcTbl dr ON (dv2.tour_id = dr.tour_id AND dr.$tourRsrcKey = :rid)
             WHERE dv.tour_id = :tour_id";
        $aTourenStat = $db->fetchRow(
            $sqlTourenStat,
            [
                'rid' => $iRsrcId,
                'tour_id' => $iTourId
            ],
            Zend_Db::FETCH_ASSOC);

        $aTourenStat['all_tour_ids'] = explode(',', $aTourenStat['all_tour_ids']);
        $aTourenStat['locked_tour_ids'] = explode(',', $aTourenStat['locked_tour_ids']);
        $aTourenStat['rsrc_in_tour_ids'] = explode(',', $aTourenStat['rsrc_in_tour_ids']);
        $aTourenStat['possible_tour_ids'] = explode(',', $aTourenStat['possible_tour_ids']);

        return $aTourenStat;
    }

    public function getPossibleTourenListForApplyDefaultRsrc(int $iTourId, $iRsrcId): array
    {
        $db = $this->_db;
        $tourRsrcTbl = $this->rsrcLnkTbl;
        $tourRsrcKey = $this->rsrcLnkKey;

        $tourTbl = $this->tourTbl;
        $tlTbl = Model_Db_TourenTimelines::obj()->tableName();
        $ptlTbl = Model_Db_TourenPortlets::obj()->tableName();

        $sqlTourenList = "SELECT 
             pt.datum, 
             dv2.timeline_id, dv2.IsDefault, dv2.tour_id, dv2.ZeitVon, dv2.ZeitBis, dv2.locked, dv2.Auftragsnummer
             FROM $tourTbl dv
             JOIN $tlTbl tl ON(dv.timeline_id = tl.timeline_id)
             JOIN $ptlTbl pt ON(tl.portlet_id = pt.portlet_id)
             JOIN $tourTbl dv2 
                  ON (dv.tour_id = dv2.tour_id OR (dv.IsDefault = 1 AND tl.timeline_id = dv2.timeline_id))
             LEFT JOIN $tourRsrcTbl dr 
                  ON (dv2.tour_id = dr.tour_id AND dr.$tourRsrcKey = :rid)
             WHERE dv.tour_id = :tour_id AND dv.locked = 0 AND dr.$tourRsrcKey IS NULL";

        return $db->fetchAll(
            $sqlTourenList,
            [
                'rid' => $iRsrcId,
                'tour_id' => $iTourId
            ],
            Zend_Db::FETCH_ASSOC);
    }

    /**
     * @param int $iRsrcId
     * @param string $datum
     * @param string $zeitVon
     * @param string $zeitBis
     * @return array
     */
    public function getKannzeitenForExternRsrc(int $iRsrcId, string $datum, string $zeitVon, string $zeitBis): array
    {
        $db = $this->_db;
        $dispoTbl = Model_Db_ResourcesDispozeiten::obj()->tableName();

        $sqlRsrcExternKannzeiten = "
            SELECT rz.gebucht_von, rz.gebucht_bis, rz.gebucht_zeit_von, rz.gebucht_zeit_bis 
            FROM $dispoTbl rz 
            WHERE 
                rz.ressourcen_id = :rid
                AND rz.ressourcen_typ = :rsrcType
                AND :datum BETWEEN rz.gebucht_von AND rz.gebucht_bis
                AND (rz.gebucht_zeit_von is null or rz.gebucht_zeit_von <= :zeitVon)
                AND (rz.gebucht_zeit_bis is null or rz.gebucht_zeit_bis >= :zeitBis)
          
            LIMIT 1";

        return $db->fetchAll(
            $sqlRsrcExternKannzeiten,
            [
                'rid' => $iRsrcId,
                'rsrcType' => $this->_resourceType,
                'datum' => $datum,
                'zeitVon' => $zeitVon,
                'zeitBis' => $zeitBis,
            ],
            Zend_Db::FETCH_ASSOC);
    }

    /**
     * @param int $iRsrcId
     * @param string $datum
     * @return array
     */
    public function getSperrzeitenForRsrcByDatum(int $iRsrcId, string $datum): array
    {
        $db = $this->_db;
        $sperrTbl = Model_Db_ResourcesSperrzeiten::obj()->tableName();

        $sqlRsrcSperrzeiten = "
            SELECT sz.ressourcen_typ, sz.gesperrt_von, sz.gesperrt_bis 
            FROM $sperrTbl sz 
            WHERE
                sz.ressourcen_id = :rid
                AND sz.ressourcen_typ = :rsrcType
                AND :datum BETWEEN sz.gesperrt_von AND sz.gesperrt_bis
            LIMIT 1";

        return $db->fetchAll(
            $sqlRsrcSperrzeiten,
            [ 'rid' => $iRsrcId, 'rsrcType' => $this->_resourceType, 'datum' => $datum ],
            Zend_Db::FETCH_ASSOC
        );
    }

    public function getBelegteZeitenForRsrcByDatumZeit(int $iRsrcId, string $datum, string $zeitVon, string $zeitBis): array
    {
        $db = $this->_db;

        $tourenRsrcTbl = $this->rsrcLnkTbl;
        $tourenRsrcKey = $this->rsrcLnkKey;
        $tourTbl = $this->tourTbl;
        $ptlTbl = Model_Db_TourenPortlets::obj()->tableName();
        $tlTbl = Model_Db_TourenTimelines::obj()->tableName();

        $sqlRsrcBelegteZeiten = " 
            SELECT pt.datum, dv.timeline_id, dv.IsDefault, dv.tour_id, dv.ZeitVon, dv.ZeitBis
             FROM $ptlTbl pt 
             JOIN $tlTbl tl ON (pt.datum = :datum AND pt.portlet_id = tl.portlet_id)
             JOIN $tourTbl dv ON(tl.timeline_id = dv.timeline_id)
             JOIN $tourenRsrcTbl dr ON(dv.tour_id = dr.tour_id AND dr.$tourenRsrcKey = :rid)
             WHERE 
              dv.ZeitVon BETWEEN :zeitVon AND :zeitBis
              OR dv.ZeitBis BETWEEN :zeitVon AND :zeitBis
              OR :zeitVon BETWEEN dv.ZeitVon AND dv.ZeitBis ";

        return $db->fetchAll(
            $sqlRsrcBelegteZeiten,
            [
                'rid' => $iRsrcId,
                'datum' => $datum,
                'zeitVon' => $zeitVon,
                'zeitBis' => $zeitBis,
            ],
            Zend_Db::FETCH_ASSOC);
    }

    public function dropQuick(int $iTourId, int $iRsrcId, string $sRsrcType)
    {
        $startMicrotime = microtime(true);
        $tlog = function() use($startMicrotime) {
            return round(microtime(true) - $startMicrotime, 3) . 's: ';
        };

        $db = $this->_db;
        $tourTbl = $this->tourTbl;
        $rsrcTbl = $this->rsrcTbl;
        $rsrcKey = $this->rsrcKey;
        $rsrcType = $this->_resourceType;
        $tourRsrcTbl = $this->rsrcLnkTbl;
        $tourRsrcKey = $this->rsrcLnkKey;
        $rsrcLblExpr = $this->getSqlSelectExprAsLabel();

        $aExistingTouren = [];
        $aKonfliktTouren = [];

        $sqlRsrcInfo = "SELECT '$rsrcType' AS resourceType, r.$rsrcKey AS rid, r.$rsrcKey, r.extern_id, 
                $rsrcLblExpr AS label, $rsrcLblExpr AS name 
              FROM $rsrcTbl r 
              WHERE r.$rsrcKey = " . $iRsrcId;

        $aRsrcInfo = $db->fetchRow($sqlRsrcInfo, [], Zend_Db::FETCH_ASSOC);

        $aReturnVars = [ 'error', 'iTourId', 'iRsrcId', 'sqlRsrcInfo', 'aRsrcInfo' ];

        if (!$aRsrcInfo) {
            return MyProject_Response_Json::sendError($tlog() . 'Rsrc not found!', compact($aReturnVars));
        }

        $sqlTourInfo = "SELECT t.* FROM $tourTbl t WHERE t.tour_id = " . $iTourId;
        $aTourInfo = $db->fetchRow($sqlTourInfo, [], Zend_Db::FETCH_ASSOC);
        array_push($aReturnVars, 'sqlTourInfo', 'aTourInfo');

        if (!$aTourInfo) {
            return MyProject_Response_Json::sendError($tlog() . 'Tour not found!', compact($aReturnVars));
        }

        $aTourenStat = $this->getTourenStatForApplyDefaultRsrc($iTourId, $iRsrcId);

        $aReturnVars[] = 'aTourenStat';
        if (!$aTourenStat['possible_tour_ids']) {
            return MyProject_Response_Json::sendError($tlog() . 'NO POSSIBLE Tour found, Tours locked or having allready booked the resource!', compact($aReturnVars));
        }

        if ($aRsrcInfo['extern_id']) {
            $aRsrcExternKannzeiten = $this->getKannzeitenForExternRsrc($iRsrcId, $aTourenStat['datum'], $aTourenStat['MinZeitVon'], $aTourenStat['MaxZeitBis']);

            $aReturnVars[] = 'aRsrcExternKannzeiten';

            if (!count($aRsrcExternKannzeiten)) {
                return MyProject_Response_Json::sendError($tlog() . 'Externe Resource ist für den Zeitraum nicht gebucht!', compact($aReturnVars));
            }
        }

        $aRsrcSperrzeiten = $this->getSperrzeitenForRsrcByDatum($iRsrcId, $aTourenStat['datum']);

        $aReturnVars[] = 'aRsrcSperrzeiten';

        if (is_array($aRsrcSperrzeiten) && count($aRsrcSperrzeiten)) {
            return MyProject_Response_Json::sendError(
                $tlog() . 'Resource ist gesperrt von ' . $aRsrcSperrzeiten['gesperrt_von'] . ' bis ' .$aRsrcSperrzeiten['gesperrt_von'] . '!',
                compact($aReturnVars)
            );
        }

        $aPossibleTouren = $this->getPossibleTourenListForApplyDefaultRsrc($iTourId, $iRsrcId);


        $aReturnVars[] = 'aPossibleTouren';

        $aRsrcBelegteZeiten =
            (!$aRsrcInfo['extern_id'])
            ? $this->getBelegteZeitenForRsrcByDatumZeit(
                $iRsrcId,
                $aTourenStat['datum'],
                $aTourenStat['MinZeitVon'],
                $aTourenStat['MaxZeitBis']
            )
            : [];
        $aReturnVars[] = 'aRsrcBelegteZeiten';



        if (count($aRsrcBelegteZeiten) > 0) {
            // Touren mit Konflikt ausfiltern

            $aPossibleTouren = array_filter($aPossibleTouren, function($_t)
            use($aRsrcBelegteZeiten, &$aKonfliktTouren, &$aExistingTouren) {
                foreach($aRsrcBelegteZeiten as $_b) {
                    // Belegt . . . . . . [ - - - - - - - ] . . . . .
                    // TOUR   . . . . [ - - - ] . . . . . . . . . . . => R1: bv between tv AND tb
                    // TOUR   . . . . . . . . . . . . [ - - - ] . . . => R2: bz BETWEEN tv AND tb
                    // TOUR   . . . . . . . . [ - - - ] . . . . . . . => R3: tv BETWEEN bv AND bb
                    if ($_b['tour_id'] == $_t['tour_id']) {
                        $aExistingTouren[] = $_t;
                        return false;
                    }
                    if ($_b['timeline_id'] == $_t['timeline_id'] && $_b['IsDefault']) {
                        continue;
                    }
                    if ($_b['ZeitVon'] >= $_t['ZeitVon'] && $_b['ZeitVon'] < $_t['ZeitBis'] ) {
                        $aKonfliktTouren[] = ['Plan' => $_t, 'Belegt' => $_b, 'Rule-Conflict' => 'R1'];
                        return false;
                    }

                    if ($_b['ZeitBis'] > $_t['ZeitVon'] && $_b['ZeitBis'] <= $_t['ZeitBis'] ) {
                        $aKonfliktTouren[] = ['Plan' => $_t, 'Belegt' => $_b, 'Rule-Conflict' => 'R2' ];
                        return false;
                    }
                    if ($_b['ZeitVon'] <= $_t['ZeitVon'] && $_b['ZeitBis'] >= $_t['ZeitBis'] ) {
                        $aKonfliktTouren[] = ['Plan' => $_t, 'Belegt' => $_b, 'Rule-Conflict' => 'R3'];
                        return false;
                    }
                }
                return true;
            });
            $aReturnVars[] = 'aKonfliktTouren';
        }
        $aInsertTouren = $aPossibleTouren;

        if (!count($aInsertTouren)) {
            MyProject_Response_Json::sendError( $tlog() . 'Resource couldnt be added to any Tour!', compact($aReturnVars));
        }

        $insertValues = implode('),(', array_map(function($_t) use($iRsrcId){
            return (int)$_t['tour_id'] . ', ' . (int)$iRsrcId;
        }, $aInsertTouren));

        $sqlInsert = 'INSERT IGNORE INTO'
            . "$tourRsrcTbl( tour_id, $tourRsrcKey)\n"
            . ' VALUES (' . $insertValues . ')';
        $stmt = $db->query($sqlInsert);
        // $stmt = $db->query('SELECT 1');
        $errorCode = $stmt->errorCode();
        $errorInfo = $stmt->errorInfo();
        $affectedRows = $stmt->rowCount();

        $aTourIds = array_column($aInsertTouren, 'tour_id');
        $sTourIds = implode(',', $aTourIds);

        array_push($aReturnVars,
            'errorCode', 'errorInfo','affectedRows', 'aPossibleTouren',
            'sqlInsert',  'aTourIds', 'sTourIds');

        $sqlSelectInsertIds = "SELECT id, tour_id, $tourRsrcKey, $tourRsrcKey as rsrc_id, $tourRsrcKey as $rsrcKey FROM $tourRsrcTbl WHERE $tourRsrcKey = :rid AND tour_id IN($sTourIds)";

        $aLastInserts = $this->_db->fetchAll($sqlSelectInsertIds, [ 'rid' => $iRsrcId]);
        $aInsertedTourIds = array_column($aLastInserts, 'tour_id');
        $aInsertTouren = array_filter($aInsertTouren, function($itm) use($aInsertedTourIds) { return in_array($itm['tour_id'], $aInsertedTourIds); });

        array_push($aReturnVars,
            'errorCode', 'errorInfo','affectedRows', 'aPossibleTouren',
            'sqlInsert',  'aTourIds', 'sTourIds', 'sqlSelectInsertIds', 'aLastInserts');

        $iTourInsertedRsrcId = 0;
        $aRsrcInsertedValues = array_map(
            function($_ins) use($aInsertTouren, $aTourInfo, $aRsrcInfo, &$iTourInsertedRsrcId) {
                foreach($aInsertTouren as $_tour) {
                    if ($_ins['tour_id'] == $aTourInfo['tour_id']) {
                        $iTourInsertedRsrcId = $_ins['id'];
                    }
                    if ($_ins['tour_id'] == $_tour['tour_id']) {
                        return [
                            'tour' => $_tour,
                            'rsrc' => array_merge( $aRsrcInfo, [ 'id' => $_ins['id'] ] )
                        ];
                    }
                }
            },
            $aLastInserts
        );

        (new Model_TourenDispoLog())->logAddDefaultResource($aTourenStat['datum'], $aInsertTouren, $this->_resourceType, $iRsrcId, $aRsrcInfo);

        $dropExecutionTime = $tlog();
        array_push($aReturnVars,
                'aLastInserts', 'aRsrcInsertedValues',
                'dropExecutionTime');
        // return MyProject_Response_Json::send(compact($aReturnVars));

        return [
            'iTourInsertedRsrcId' => $iTourInsertedRsrcId,
            'aRsrcInfo' => $aRsrcInfo,
            'aTourInfo' => $aTourInfo,
            'aInsertTouren' => $aInsertTouren,
            'aRsrcInsertedValues' => $aRsrcInsertedValues,
            'aLastInserts' => $aLastInserts,
            'aExistingTouren' => $aExistingTouren,
            'aConflictTouren' => $aKonfliktTouren,
            'affectedRows' => $affectedRows,
            'aLog' => compact($aReturnVars),
        ];
        MyProject_Response_Json::send(compact($aReturnVars));
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
        // denen die Resource noch nicht hinzugef�gt wurden
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
                $id = $this->drop(array(  // Nachtr�glich ge�ndert von insert auf drop => Enth�lt Dispo-Pr�fung
                    $this->_tblRsrcKey => $rid,
                    $this->_tblRsrcLnkKey => $rid,
                    $this->_tblTourKey => $_tour_id,
                    'route_id' => $_tour_id,
                    'timeline_id' => $tid,
                ), $_tourData);

                if (0) {
                    $json = json_encode(['line'=>__LINE__, 'file'=>__FILE__, 'queries' => MyProject_Db_Profiler::getProfiledQueryList()]);

                    header('Content-Type: application/json; charset=UTF-8');
                    header("Content-Length: ". strlen($json));
                    echo $json;
                    exit;
                }

                if ($id) {
                    $this->dispoLog(
                        $rid, 'apply-defaults', $_tour_id,
                        $_tourData + ['bemerkung' => json_encode($_tourData)]
                    );
                }

                $aAppliedTourIDs[] = ['newID' => $id, 'tourID' => $_tour_id];
            }
        } catch(Exception $e) {
            throw $e;
        }

        return $aAppliedTourIDs;
    }

    public function removeDefault($id) {
        return $this->removeQuick($id);
    }

    public function removeQuick($id)
    {
        $id = (int)$id;

        $tourTbl = $this->tourTbl;
        $tourRsrcTbl = $this->rsrcLnkTbl;
        $tourRsrcKey = $this->rsrcLnkKey;
        $rsrcKey = $this->rsrcKey;
        $rsrcType = $this->_db->quote($this->_resourceType);
        $rsrcTbl = $this->rsrcTbl;

        $aRemoveResult = [];

        $sqlDeleteStat = "
            SELECT 
                t.tour_id base_tour_id, t.IsDefault base_tour_IsDefault, t.DatumVon, tr.id base_lnk_id, t.timeline_id, 
                $rsrcType AS type,  tr.$tourRsrcKey, tr.$tourRsrcKey $rsrcKey, tr.$tourRsrcKey rid,
                t2.tour_id, t2.IsDefault, t2.ZeitVon, t2.ZeitBis, t2.locked, t2.Auftragsnummer,
                tr2.id
               FROM $tourRsrcTbl tr
               JOIN $tourTbl t ON (tr.tour_id = t.tour_id)
               JOIN $tourTbl t2 
                 ON(
                   t.timeline_id = t2.timeline_id
                   AND (t.tour_id = t2.tour_id OR t.IsDefault = 1)
                 )
               JOIN $tourRsrcTbl tr2 
                 ON(
                   t2.tour_id = tr2.tour_id 
                   AND tr.$tourRsrcKey = tr2.$tourRsrcKey
                 )
               WHERE tr.id = :linkId
               ORDER BY t2.IsDefault DESC, t2.ZeitVon
        ";

        $aDeleteStat = $this->_db->fetchAll($sqlDeleteStat, [ 'linkId' => $id], Zend_Db::FETCH_ASSOC);
        $aRemoveResult['sqlDeleteStat'] = $sqlDeleteStat;
        $aRemoveResult['aDeleteStat'] = $aDeleteStat;

        if (!count($aDeleteStat)) {
            return ['error' => "No Tours Found with this resource!!"]
                + $aRemoveResult;
        }

        $rsrcLblExpr = $this->getSqlSelectExprAsLabel();
        $iRsrcId = $aDeleteStat[0]['rid'];
        $sqlRsrcInfo = "SELECT '{$this->_resourceType}' AS resourceType, r.$rsrcKey AS rid, r.$rsrcKey, r.extern_id, 
                $rsrcLblExpr AS label, $rsrcLblExpr AS name 
              FROM $rsrcTbl r 
              WHERE r.$rsrcKey = " . $iRsrcId;

        $aRsrcInfo = $this->_db->fetchRow($sqlRsrcInfo, [], Zend_Db::FETCH_ASSOC);

        $aLinkIds = array_map(function($itm){ return (int)$itm['id'];}, $aDeleteStat);
        $sLinkIds = implode(',', $aLinkIds);
        $numLinksFound = count($aLinkIds);

        $sqlFromWhereDelIds = "FROM $tourRsrcTbl WHERE id IN($sLinkIds)";
        $sqlDeleteLinks = "DELETE $sqlFromWhereDelIds LIMIT ". count($aLinkIds);
        $stmt = $this->_db->query($sqlDeleteLinks);
        $numLinksDeleted = $stmt->rowCount();

        $sqlRemainingLinks = 'SELECT id ' . $sqlFromWhereDelIds;
        $aStillExistingLinks = $this->_db->fetchCol($sqlRemainingLinks);

        $aDeletedLinks = [];
        $aUndeletedLinks = [];

        if (!count($aStillExistingLinks)) {
            $aDeletedLinks = $aDeleteStat;
        } else {
            foreach ($aDeleteStat as $_itm) {
                if (in_array($_itm['tour_id'], $aStillExistingLinks)) {
                    $aUndeletedLinks[] = $_itm;
                } else {
                    $aDeletedLinks[] = $_itm;
                }
            }
        }
        $numLinksRemain = count($aStillExistingLinks);

        $aRemoveResult['aLinkIds'] = $aLinkIds;
        $aRemoveResult['aDeletedLinks'] = $aDeletedLinks;
        $aRemoveResult['aUndeletedLinks'] = $aUndeletedLinks;
        $aRemoveResult['aStillExistingLinks'] = $aStillExistingLinks;
        $aRemoveResult['numLinksFound'] = $numLinksFound;
        $aRemoveResult['numLinksDeleted'] = $numLinksDeleted;
        $aRemoveResult['numLinksRemain'] = $numLinksRemain;

        $datum = $aDeleteStat[0]['DatumVon'];
        $isDefault = $aDeleteStat[0]['base_tour_IsDefault'];
        if ($isDefault || $numLinksDeleted > 1) {
            (new Model_TourenDispoLog())->logRemoveDefaultResource($datum, $aDeletedLinks, $this->_resourceType, $iRsrcId, $aRsrcInfo);
        } else {
            $aTourInfo = $aDeletedLinks[0];
            $tourId = $aTourInfo['tour_id'];
            (new Model_TourenDispoLog())->logResource(
                $this->_resourceType, $iRsrcId, 'remove-rsrc', $tourId, null, null,
                $aTourInfo + [ 'bemerkung' => $aRsrcInfo['label']]);
        }

        if ($numLinksRemain) {
            return ['error' =>
                "$numLinksRemain von $numLinksFound ermittelten Einträgen konnten nicht gelöscht werden!"]
                + $aRemoveResult;
        }

        return $aRemoveResult;

    }
    
    /**
     * @param int $id ResourceLnkId
     */
    public function removeDefault0($id)
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

        foreach($aTourIDs as $_tour_id) {
            $this->dispoLog(
                $rid, 'removed-default', $_tour_id
            );
        }

        if ($iDeleted != $iSelected) {
            throw new Exception("Die ermittelte Anzahl Touren ($iSelected) entspricht nicht der Anzahl Touren ($iDeleted) aus der die Ressource entfernt wrude!");
        }

        return $aTourIDs;
              
    }
    
    /**
     * @param int $tour_id
     * @param bool $keysOnly default = false, gibt alle Felder zur�ck, bei true nur die IDs
     * @return array withd IDs if keysOnly is true, else Rows with resource-Data, first Field is named id
     */
    public function getResourcesByTourId($tour_id, $keysOnly = false) 
    {

        $iTourId = (int)$tour_id;
        $tourKey = $this->tourKey;
        $tourRsrcTbl = $this->rsrcLnkTbl;
        $tourRsrcKey = $this->rsrcLnkKey;
        $rsrcTbl = $this->rsrcTbl;
        $rsrcKey = $this->rsrcKey;
        $lstgTbl = Model_Db_Leistung::obj()->tableName();
        $labelExpr = $this->getSqlSelectExprAsLabel();

        $columns = ($keysOnly) ? $rsrcKey : $labelExpr . ' AS label, tr.*, r.*, l.*';

        $sql = "SELECT $columns
                FROM $tourRsrcTbl tr
                JOIN $rsrcTbl r ON(tr.$tourKey = $iTourId AND tr.$tourRsrcKey = r.$rsrcKey)
                LEFT JOIN $lstgTbl l ON(r.leistungs_id=l.leistungs_id)";
        
        return ($keysOnly)
                ? $this->_db->fetchCol($sql)
                : $this->_db->fetchAll($sql, Zend_Db::FETCH_ASSOC);
    }
    
    public function getResourceKey() {
        return $this->_tblRsrcKey;
    }
    
    public function getResourceLinkKey() {
        return $this->_tblRsrcLnkKey;
    }
    
    /**
     * @param int $tour_id
     * @param bool $keysOnly default = false, gibt alle Felder zur�ck, bei true nur die IDs
     * @return array withd IDs if keysOnly is true, else Rows with resource-Data, first Field is named id
     */
    public function getResourcesByTimelineId($timeline_id, $keysOnly = false) 
    {
        $tourTbl = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $rsrcTbl = $this->rsrcTbl;
        $rsrcKey = $this->rsrcKey;
        $tourRsrcTbl = $this->rsrcLnkTbl;
        $tourRsrcKey = $this->rsrcLnkKey;
        $labelExpr = $this->getSqlSelectExprAsLabel();

        $columns = ($keysOnly) ? $rsrcKey : $labelExpr . ' AS label, t.DatumVon, t.DatumBis, t.ZeitVon, t.ZeitBis, r.*, dr.* ';

        $sql = "SELECT $columns
            FROM $tourTbl t
            JOIN $tourRsrcTbl tr ON (t.tour_id = tr.tour_id)
            JOIN $rsrcTbl r ON(tr.$tourRsrcKey = r.$rsrcKey) 
            WHERE dv.timeline_id = {(int)$timeline_id}"
        ;
        
        return ($keysOnly)
                ? $this->_db->fetchCol ($sql)
                : $this->_db->fetchAll($sql, Zend_Db::FETCH_ASSOC);
    }
    
    /**
     * @abstract
     * Pr�ft, ob Resource f�r Ziel-Slot (siehe Param $filter) frei und gibt ergebnis-Objekt mit Details zur�ck
     * tourData enth�lt rows mit den Feldern ResourceId, Resource und allen tour-Feldern
     * @param int $rsrc_id
     * @param array $filter assoc (
     *          DatumVon=>YYYY-MM-DD,
     *          DatumBis=>YYYY-MM-DD,
     *          ZeitVon=>HH:MM,
     *          ZeitBis=>HH:MM,
     *          ignoreTourIds=>[id,...]
     * )
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
        $re->stackTrace = [];
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
        
        $dzWhere = '';
        if ($ignoreTourIds) {
            $dzWhere.= $this->tourKey.' NOT IN (' . implode(',', $ignoreTourIds) . ') ' . PHP_EOL;
        }
        if ($timeline_id) {
            $dzWhere.=
                ($dzWhere ? ' AND ' : '')
               .'(timeline_id != ' . (int)$timeline_id . ' OR IsDefault = 0)' . PHP_EOL;
        }

        // Bereits Disponierte
        if ($dVon || $dBis || $zVon || $zBis) {
            $dzWhere.= ($dzWhere ? ' AND ' : '') . ' (';
            if ($dVon || $dBis ) {
                $dzWhere.= ' (';
                if ($dVon == $dBis || !$dVon || $dBis) {
                    $dTag = $dVon ?: $dBis;
                    $dzWhere.= $db->quoteInto('(t.DatumVon = ? OR (t.DatumVon <= ? AND t.DatumBis >= ?))', $dTag) . PHP_EOL;
                } else {
                    $dzWhere.= '(
                        OR "' . $dVon . '" = t.DatumVon
                        OR "' . $dVon . '" BETWEEN t.DatumVon AND t.DatumBis
                        OR "' . $dBis . '" BETWEEN t.DatumVon AND t.DatumBis
                        OR ("' . $dVon . '" >= t.DatumVon AND "' . $dBis . '" <= t.DatumBis)
                        OR ("' . $dVon . '" <= t.DatumVon AND "' . $dBis . '" >= t.DatumBis)
                    )';
                }
                $dzWhere.= ')';
            }

            if ($zVon || $zBis) {
                $dzWhere.= ' AND (';
                if ($zVon) {
                    $dzWhere.= $db->quoteInto('(t.ZeitVon <= ? AND t.ZeitBis > ?)', $zVon) . PHP_EOL;
                }
                if ($zBis && $zBis !== $zVon) {
                    $dzWhere.= ($zVon ? ' OR ' :'')
                          .  $db->quoteInto('(t.ZeitVon < ? AND t.ZeitBis >= ?)', $zBis) . PHP_EOL;
                }
                $dzWhere.= ')';
            }
            $dzWhere.= ')';
        }

        // Sperrzeiten
        $szWhere = '';
        if ($dVon || $dBis) {
            $condRsrcTyp = $db->quoteInto('ressourcen_typ = ?', $this->_resourceType);
            if ($dVon == $dBis || !$dVon || $dBis) {
                $dTag = $dVon ?: $dBis;
                $szWhere.= $db->quoteInto('(' . $condRsrcTyp . ' AND ? BETWEEN gesperrt_von AND gesperrt_bis)', $dTag) . PHP_EOL;
            } else {
                $szWhere.= '(' . $condRsrcTyp . ' AND ' . '(                        
                        OR "' . $dVon . '" BETWEEN gesperrt_von AND gesperrt_bis
                        OR "' . $dBis . '" BETWEEN gesperrt_von AND gesperrt_bis
                        OR ("' . $dVon . '" >= gesperrt_von AND "' . $dBis . '" <= gesperrt_bis)
                        OR ("' . $dVon . '" <= gesperrt_von AND "' . $dBis . '" >= gesperrt_bis)
                    ))';
            }
        }

        $re->sqlDZ =
             'SELECT '.$this->rsrcKey.' ResourceId, '.$this->_rsrcTitleField.' AS Resource, t.* ' . PHP_EOL
            .'FROM '.$this->rsrcTbl.' r ' . PHP_EOL
            .'LEFT JOIN '.$this->rsrcLnkTbl.' ON('.$this->rsrcKey.'='.$this->rsrcLnkKey.') ' . PHP_EOL
            .'LEFT JOIN '.$this->tourTbl.' t USING('. $this->tourKey . ') ' . PHP_EOL
            .'WHERE '. PHP_EOL
            .$this->rsrcKey . ' = ' . (int)$rsrc_id . PHP_EOL
           .' AND (' . $dzWhere . ')' . "\n";


        $re->sqlSZ =
            'SELECT '.$this->rsrcKey.' ResourceId, '.$this->_rsrcTitleField.' AS Resource, s.gesperrt_von, s.gesperrt_bis ' . PHP_EOL
            .'FROM '.$this->rsrcTbl.' r ' . PHP_EOL
            .'LEFT JOIN '.$szTable.' s ON(' . PHP_EOL
            .'  s.ressourcen_typ = '.$db->quote($this->_resourceType) . ' ' . PHP_EOL
            .'  AND s.ressourcen_id = ' . $this->rsrcKey . ') ' . PHP_EOL
            .'WHERE '. PHP_EOL
            .$this->rsrcKey . ' = ' . (int)$rsrc_id . PHP_EOL
            .' AND (' . $szWhere . ')';

        $re->sql = $re->sqlDZ . ";\n" . $re->sqlSZ;
        $re->data = (array)$db->fetchAll($re->sqlDZ);
        $re->dataSZ = (array)$db->fetchAll($re->sqlSZ);
        $re->free = (count($re->data) == 0 && count($re->dataSZ) == 0);
        if (count($re->data) > 0 || count($re->dataSZ) > 0) {
            $re->message = 'Konflikt: Resource ist für den Zielzeitraum nicht disponierbar!' . PHP_EOL;
            foreach($re->data as $_d) {
                $re->message.= '- Gebucht für ANR:' . $_d['Auftragsnummer']
                    . ' am ' . date("d.m.Y", strtotime($_d['DatumVon']))
                    . ' um ' . substr($_d['ZeitVon'], 0, 5) . '' . PHP_EOL;
            }

            foreach($re->dataSZ as $_d) {
                $re->message.= '- Gesperrt '
                    . ' von ' . date("d.m.", strtotime($_d['gesperrt_von']))
                    . ' bis ' . date("d.m.Y", strtotime($_d['gesperrt_bis'])) . PHP_EOL;
                $re->data[] = $_d + ['DatumVon' => $_d['gesperrt_von'], 'DatumBis' => $_d['gesperrt_bis'], 'tour_id' => null, 'Auftragsnummer' => 'Gesperrt', 'ZeitVon' => '00:00'];
            }

            try { throw new Exception('DEBUG Stacktrace');} catch(Exception $e) {
                $re->stackTrace = $e->getTrace();
            }
        }
        if (0) try { throw new Exception('DEBUG Stacktrace');} catch(Exception $e) {
            ob_end_clean();
            $re->stackTrace = $e->getTrace();
            echo '<pre>' . print_r( (array)$re, 1) . '</pre>';
            exit;
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
    public function getTourResourceFilterSql0($filter)
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

    public function getListOfAvailableItems(array $aDateTimeRange, array $aFilter, MyProject_Model_QueryBuilder $oQueryOpts = null): MyProject_Model_QueryResult
    {
        $db = $this->_db;
        $rsrcType = $this->_resourceType;
        $rsrcKey = $this->_tblRsrcKey;
        $sExternFilter = $aFilter['extFilter'];
        $aTimeIn['Model'] = microtime(true);

        if (is_null($oQueryOpts)) {
            $oQueryOpts = new MyProject_Model_QueryBuilder();
        }

        $result = new MyProject_Model_QueryResult();

        $extrnTbl = Model_Db_Extern::obj()->tableName();
        $lstgTbl = Model_Db_Leistung::obj()->tableName();
        $rsrcTbl = $this->rsrcTbl;
        $rsrcLblExpr = $this->getSqlSelectExprAsLabel();

        $oQueryOpts->setSelect($rsrcLblExpr . " AS label, $rsrcTbl.*, e.*, l.*")
            ->setFrom($rsrcTbl)
            ->setJoin("LEFT JOIN $extrnTbl e ON (e.extern_id = $rsrcTbl.extern_id)", true)
            ->addJoin( " LEFT JOIN $lstgTbl l ON(l.leistungs_id = $rsrcTbl.leistungs_id)", true)
            ;


        if (!empty($aFilter['categoryTerm'])) {
            $sqlInCtg = $this->getSqlSelectIdsInCategorie($aFilter['categoryTerm']);
            $oQueryOpts->andWhere(
                $this->rsrcKey. ' IN(' . $sqlInCtg . ')'
            );
        }

        if ($sExternFilter == 'int') {
            $sqlInTouren = $this->getSqlSelectIdsInDisponiert($aDateTimeRange);
            $oQueryOpts->andWhere($this->_tblRsrcKey . " NOT IN ($sqlInTouren)");

            $sqlInSperre = $this->getSqlSelectIdsInSperrzeiten($aDateTimeRange['DatumVon'], $aDateTimeRange['DatumBis']);

            $oQueryOpts->andWhere(
                $this->rsrcKey. ' NOT IN(' . $sqlInSperre . ')');

            $oQueryOpts->andWhere(
                '('.$this->rsrcTbl. '.extern_id IS NULL OR ' . $this->rsrcTbl . '.extern_id = 0)'
            );

        } elseif ($sExternFilter == 'ext') {
            $tblDz   = Model_Db_ResourcesDispozeiten::obj()->tableName();
            $sqlInExtern = $this->getSqlSelectIdsInDispozeiten($aDateTimeRange);
            $oQueryOpts->addJoin(
                "LEFT JOIN $tblDz dz ON( dz.ressourcen_typ = '$rsrcType' AND $rsrcKey = dz.ressourcen_id)", true
            );
            $oQueryOpts->andWhere(
                'IFNULL(' . $this->rsrcTbl . '.extern_id, 0) > 0'
            );
            $oQueryOpts->andWhere(
                $this->rsrcKey. ' IN(' . $sqlInExtern . ')'
            );
        }

        $aTimeIn['SqlCountQuery'] = microtime(true);
        $iTotal = (int)$db->fetchOne( $oQueryOpts->assembleCount() );
        $aTimeOut['SqlCountQuery'] = microtime(true);

        if ($oQueryOpts->getOrder()) {
            $result->setOrder( "{$oQueryOpts->getOrder()} {$oQueryOpts->getOrderDir()}" );
        }
        // die( $oQueryOpts->assemble() );
        $result->setSql( $oQueryOpts->assemble() );
        $result->setOffset( (int)$oQueryOpts->getOffset() );
        $result->setLimit( (int)$oQueryOpts->getLimit() );
        $result->setTotal($iTotal);


        if ($iTotal > $oQueryOpts->getOffset()) {
            $aTimeIn['SqlRowsQuery'] = microtime(true);
            $result->setRows( $db->fetchAll( $oQueryOpts->assemble(), Zend_Db::FETCH_ASSOC));
            $aTimeOut['SqlRowsQuery'] = microtime(true);
        }
        $aTimeOut['Model'] = microtime(true);
        foreach($aTimeIn as $_what => $_timeIn) {
            $result->addLog("Time for $_what: " . ($aTimeOut[$_what] - $_timeIn), 3);
        }
        return $result;

    }

    public function getSqlSelectIdsInDisponiert(array $aTimeRange, int $iTourId = 0)
    {
        $db = $this->_db;
        $dVon = $aTimeRange['DatumVon'] ?? '';
        $dBis = $aTimeRange['DatumVon'] ?? '';
        $zVon = $aTimeRange['ZeitVon'] ?? '';
        $zBis = $aTimeRange['ZeitBis'] ?? '';

        if ($iTourId) {
            $aRowTour = Model_Db_TourenDispoVorgaenge::get($iTourId);
            $tlId = $aRowTour['timeline_id'];
            $isDef = $aRowTour['IsDefault'];
            $dVon = new DateTime($aRowTour['DatumVon']);
            $dBis = new DateTime($aRowTour['DatumBis']);
            $zVon = $aRowTour['ZeitVon'];
            $zBis = $aRowTour['ZeitBis'];
        }

        if ($dVon instanceof DateTime) {
            $dVon = $dVon->format('Y-m-d');
        }
        if ($dBis instanceof DateTime) {
            $dBis = $dBis->format('Y-m-d');
        }

        $this->_require(strtotime($dVon),
            'Missing valid Paramter: DatumFilter. Given: ' . $dVon . '!');

        $this->_require(empty($dBis) || strtotime($dBis),
            'Invalid optional Parameter DatumBis: ' . $dBis);

        $this->_require(empty($zVon) || preg_match('#^\d\d:\d\d(:\d\d)?$#', $zVon),
            'Invalid optional Parameter ZeitVon: '. $zVon);

        $this->_require(empty($zBis) || preg_match('#^\d\d:\d\d(:\d\d)?$#', $zBis),
            'Invalid optional Parameter ZeitBis: '. $zBis);

        $portletTbl  = Model_Db_TourenPortlets::obj()->tableName();
        $timelineTbl = Model_Db_TourenTimelines::obj()->tableName();
        $tourTbl     = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $tourRsrcTbl = $this->rsrcLnkTbl;
        $tourRsrcKey = $this->rsrcLnkKey;

        $dateWhere = (!$dBis || $dBis == $dVon)
                    ? 'pt.datum = ' . $db->quote($dVon)
                    : 'pt.datum BETWEEN ' . $db->quote($dVon) . ' AND ' . $db->quote($dBis);

        if ($zVon && $zBis) {
            $dateWhere.= '   AND ' . PHP_EOL
                .'   ( ' . PHP_EOL
                ."      dv.ZeitVon BETWEEN '$zVon' AND '$zBis' " . PHP_EOL
                ."      OR dv.ZeitBis BETWEEN '$zVon' AND '$zBis' " . PHP_EOL
                .'   )';
        }


        $sqlSelectIds = 'SELECT DISTINCT(dm.' . $tourRsrcKey . ') ' . PHP_EOL
	                    .' FROM ' . $portletTbl . ' pt ' . PHP_EOL
                        .' JOIN ' . $timelineTbl . ' tl ON (pt.portlet_id = tl.portlet_id) ' . PHP_EOL
                        .' JOIN ' . $tourTbl . ' dv ON (tl.timeline_id = dv.timeline_id)  ' . PHP_EOL
                        .' JOIN ' . $tourRsrcTbl . ' dm ON (dv.tour_id = dm.tour_id) ' . PHP_EOL
                        .' WHERE ' . PHP_EOL
                        . $dateWhere;

        return $sqlSelectIds;
    }

    public function getSqlSelectIdsInSperrzeiten(DateTime $dVon, DateTime $dBis = null) {

        $db = $this->_db;
        $szTbl = Model_Db_ResourcesSperrzeiten::obj()->tableName();
        $rsrcType = $this->_resourceType;
        $rsrcTypeQuoted = $db->quote($rsrcType);
        $dVonFormatted = $dVon->format('Y-m-d');
        $dBisFormatted = (is_null($dBis)) ? '' : $dBis->format('Y-m-d');

        $dateWhere = (is_null($dBis) || $dVonFormatted === $dBisFormatted)
                    ? $db->quoteInto( ' (gesperrt_von <= ? AND gesperrt_bis >= ?) ', $dVonFormatted) . PHP_EOL
                    : $db->quoteInto( ' (gesperrt_von BETWEEN ? AND ', $dVonFormatted)
                        . $db->quote($dBisFormatted) . PHP_EOL
                        . $db->quoteInto( ' OR (gesperrt_bis BETWEEN ? AND ', $dVonFormatted)
                        . $db->quote($dBisFormatted) . PHP_EOL;

        $sqlSelectIds = 'SELECT ressourcen_id ' . PHP_EOL
                       .' FROM ' . $szTbl . PHP_EOL
                       .' WHERE ressourcen_typ = ' . $rsrcTypeQuoted . ' AND ' . $dateWhere . ' ';

        return $sqlSelectIds;
    }

    public function getSqlSelectIdsInDispozeiten(array $aDateTimeRange)
    {

        $db = $this->_db;

        $dVon = $aDateTimeRange['DatumVon'];
        $dBis = $aDateTimeRange['DatumBis'];
        $zVon = $aDateTimeRange['ZeitVon'];
        $zBis = $aDateTimeRange['ZeitBis'];

        $dzTbl = Model_Db_ResourcesDispozeiten::obj()->tableName();
        $rsrcType = $this->_resourceType;
        $rsrcTypeQuoted = $db->quote($rsrcType);
        $dVonFormatted = $dVon->format('Y-m-d');
        $dBisFormatted = is_null($dBis) ? '' : $dBis->format('Y-m-d');

        $dateWhere = (is_null($dBis) || $dVonFormatted === $dBisFormatted)
                    ? $db->quoteInto( ' gebucht_von <= ? AND gebucht_bis >= ? ', $dVonFormatted) . PHP_EOL
                    : $db->quoteInto( ' (gebucht_von BETWEEN ? AND ', $dVonFormatted)
                        . $db->quote($dBisFormatted) . PHP_EOL
                        . $db->quoteInto( ' OR gebucht_bis BETWEEN ? AND ', $dVonFormatted)
                        . $db->quote($dBisFormatted) . ')' . PHP_EOL;

        $timeFormat = '#^\d\d:\d\d\$#';

        if ($zVon && $zBis && preg_match($timeFormat, $zVon) && preg_match($timeFormat, $zVon)) {
            $dateWhere.= ' AND IFNULL(gebucht_von,"00:00") <= ' . $db->quote($zVon)
                        .' AND IFNULL(gebucht_bis, "24:00") >= ' . $db->quote($zBis);
        }

        $sqlSelectIds = 'SELECT ressourcen_id ' . PHP_EOL
            .' FROM ' . $dzTbl . PHP_EOL
            .' WHERE ressourcen_typ = ' . $rsrcTypeQuoted . ' AND ' . $dateWhere . ' ';

        return $sqlSelectIds;
    }

    public function getSqlSelectIdsInCategorie($term) {
        $db = $this->_db;

        $ctgTbl = $this->_tblCtgName;
        $ctgKey = $this->_tblCtgKey;
        $ctgLnkTbl = $this->_tblCtgLnkName;
        $ctgLnkKey = $this->_tblCtgLnkKey;

        $ctgLnkRsrcKey = $this->_tblCtgLnkRsrcKey;

        $parentWhere = is_numeric($term) ? 'category_id='.(int)$term : 'name LIKE ' . $db->quote("$term%");

        $sqlSelectIds = 'SELECT lnk.' . $ctgLnkRsrcKey .' ' . PHP_EOL
                        .' FROM ' . $ctgTbl . ' AS parent ' . PHP_EOL
                        .' JOIN ' . $ctgTbl . ' AS node ON (parent.'.$parentWhere.' AND node.lft BETWEEN parent.lft AND parent.rgt)' . PHP_EOL
                        .' JOIN ' . $ctgLnkTbl . ' AS lnk ON (node.'.$ctgKey.' = lnk.' . $ctgLnkKey . ')';

        return $sqlSelectIds;
    }

    public function getTourResourceFilterSql($aFilter): string
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;

        // START Tour-Filter
        $tourId = (array_key_exists('tour_id', $aFilter) ? (int)$aFilter['tour_id'] : 0);

        if ($tourId) {
            $aDateTimeRange = Model_Db_TourenDispoVorgaenge::get($tourId);

            $this->_require(!empty($aDateTimeRange), 'Tour not found with ID: ' . $tourId);

            $aDateTimeRange['DatumVon'] = new DateTime( $aDateTimeRange['DatumVon'] );
            $aDateTimeRange['DatumBis'] = new DateTime( $aDateTimeRange['DatumBis'] );

        } else {
            $aDateTimeRange = $aFilter;
            $this->_require(!empty($aDateTimeRange['DatumVon']),
                'Missing Parameter DatumVon');
            $this->_require(strtotime($aDateTimeRange['DatumVon']),
                'Invalid Parameter-Value for DatumVon: ' . $aDateTimeRange['DatumVon']);

            if (!($aDateTimeRange['DatumVon'] instanceof DateTime)) {
                $aDateTimeRange['DatumVon'] = new DateTime($aDateTimeRange['DatumVon']);
            }

            if( empty($aDateTimeRange['DatumBis'])) {
                $aDateTimeRange['DatumBis'] = '';
            } elseif (!($aDateTimeRange['DatumBis'] instanceof DateTime)) {
                $aDateTimeRange['DatumBis'] = new DateTime($aDateTimeRange['DatumBis']);
            }

            if( !isset($aDateTimeRange['ZeitVon'])) {
                $aDateTimeRange['ZeitVon'] = '';
            }
            if( !isset($aDateTimeRange['ZeitBis'])) {
                $aDateTimeRange['ZeitBis'] = '';
            }
        }

        $oQueryOpts = new MyProject_Model_QueryBuilder();
        $oQueryOpts->setSelect($this->rsrcKey);
        $oQueryOpts->setFrom($this->_tblRsrcName);

        // SQL-Part Already in Dispo
        $sqlInTouren = $this->getSqlSelectIdsInDisponiert($aDateTimeRange, $tourId);
        $oQueryOpts->andWhere($this->rsrcKey . " NOT IN ($sqlInTouren)");

        // SQL-Part Sperre
        $tblExt = Model_Db_Extern::obj()->tableName();
        $sqlInSperre = $this->getSqlSelectIdsInSperrzeiten(
            $aDateTimeRange['DatumVon'], $aDateTimeRange['DatumBis'] ?: null);

        $oQueryOpts->andWhere(
            $this->rsrcKey . ' NOT IN(' . $sqlInSperre . ')');

        // SQL-Part Category
        if (!empty($aFilter['categoryTerm'])) {
            $sqlInCtg = $this->getSqlSelectIdsInCategorie($aFilter['categoryTerm']);
            $oQueryOpts->andWhere(
                $this->rsrcKey . ' IN(' . $sqlInCtg . ')'
            );
        }

        return $oQueryOpts->assemble();
    }


    // TOUR-RESOURCE-BASE-FILTER
    // Aufruf z.B. aus FuhrparkControler::gridresponsedataAction()
    /**
     * Liefert SQL-Abfrage, der als Negativ-Ausdruck ( tour_id NOT IN({subSql}) als Sub-Sql verwendet werden kann
     * @param array $filter
     * @return string sql
     */
    public function getTourResourceFilterSqlNEU20190823($filter)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;

        // START Tour-Filter
        $tourId  = (array_key_exists('tour_id', $filter) ? $filter['tour_id'] : '' );

        if ($tourId) {
            $row = Model_Db_TourenDispoVorgaenge::get($tourId);

            $dVon = $row['DatumVon'];
            $dBis = $row['DatumBis'];
            $zVon = $row['ZeitVon'];
            $zBis = $row['ZeitBis'];

        } else {
            $dVon  = (array_key_exists('DatumVon', $filter) ? $filter['DatumVon'] : '' );
            $dBis  = (array_key_exists('DatumBis', $filter) ? $filter['DatumBis'] : '' );
            $zVon  = (array_key_exists('ZeitVon',  $filter) ? $filter['ZeitVon']  : '' );
            $zBis  = (array_key_exists('ZeitBis',  $filter) ? $filter['ZeitBis']  : '' );
        }

        $aDateTimeRange = [
            'DatumVon' => strtotime($dVon) ? new DateTime(strtotime($dVon)) : null,
            'DatumBis' => strtotime($dBis) ? new DateTime(strtotime($dBis)) : null,
            'ZeitVon' => $zVon,
            'ZeitBis' => $zBis,
        ];
        $oQueryOpts = new MyProject_Model_QueryBuilder();

        $tblExt = Model_Db_Extern::obj()->tableName();
        $tblLstg = Model_Db_Leistung::obj()->tableName();

        $oQueryOpts->setSelect('*')
            ->setFrom($this->_tblRsrcName)
            ->setJoin("LEFT JOIN $tblExt e ON (e.extern_id = $this->_tblRsrcName.extern_id)", true)
            ->addJoin( " LEFT JOIN $tblLstg l ON(l.leistungs_id = {$this->_tblRsrcName}.leistungs_id)", true)
        ;

        $sqlInTouren = $this->getSqlSelectIdsInDisponiert($aDateTimeRange, $tourId);
        $oQueryOpts->andWhere($this->_tblRsrcKey . " NOT IN ($sqlInTouren)");

        $sqlInSperre = $this->getSqlSelectIdsInSperrzeiten($aDateTimeRange['DatumVon'], $aDateTimeRange['DatumBis']);
        $oQueryOpts->andWhere(
            $this->rsrcKey. ' NOT IN(' . $sqlInSperre . ')');
        $oQueryOpts->andWhere(
            '('.$this->rsrcTbl. '.extern_id IS NULL OR ' . $this->rsrcTbl . '.extern_id = 0)'
        );

        if (!empty($aFilter['categoryTerm'])) {
            $sqlInCtg = $this->getSqlSelectIdsInCategorie($aFilter['categoryTerm']);
            $oQueryOpts->andWhere(
                $this->rsrcKey. ' IN(' . $sqlInCtg . ')'
            );
        }



        $portletTbl = Model_Db_TourenPortlets::obj()->tableName();
        $timelineTbl = Model_Db_TourenTimelines::obj()->tableName();
        $tourenTbl = Model_Db_TourenDispoVorgaenge::obj()->tableName();
        $tourenRsrcTbl = $this->rsrcLnkTbl;
        $rsrcTbl = $this->rsrcTbl;
        $szTbl = Model_Db_ResourcesSperrzeiten::obj()->tableName();

        $rsrcType = $this->_resourceType;
        $rsrcTypeQuoted = $db->quote($rsrcType);
        $rsrcKey = $this->rsrcKey;
        $tourenRsrcKey = $this->rsrcLnkKey;

        $gesperrtWhere = '';
        $disponiertWhere = '';
        if ($dVon) {
            $gesperrtWhere.= $db->quoteInto(
                    ' (gesperrt_von <= ? AND gesperrt_bis >= ?) ', $dVon ) . PHP_EOL;

            if ($dBis && $dBis != $dVon) {
                $disponiertWhere.= $db->quoteInto(
                        ' (p.datum <= ? AND p.datum >= ?)', $dVon) . PHP_EOL
                        . $db->quoteInto(
                        ' AND (p.datum <= ? AND p.datum >= ?)', $dBis) . PHP_EOL;
            } else {
                $disponiertWhere.= $db->quoteInto(' (p.datum = ?)', $dVon) . PHP_EOL;
            }
        }

        if ($dVon && $dBis && $dBis !== $dVon) {
            $gesperrtWhere.= ($gesperrtWhere ? ' OR ' : '') . $db->quoteInto(
                    ' OR (gesperrt_von <= ? AND gesperrt_bis >= ?) ', $dBis ) . PHP_EOL;

            $disponiertWhere.= ($gesperrtWhere ? ' AND ' : '') . $db->quoteInto(
                    ' AND (p.datum <= ? AND p.datum >= ?)', $dBis) . PHP_EOL;
        }

        if ($dVon && $zVon)  {
            $disponiertWhere.= ' AND (';
            $disponiertWhere.= $db->quoteInto(
                    ' (t.ZeitVon <= ? AND t.ZeitBis > ?)', $zVon) . PHP_EOL;

            if ($zBis && $zBis !== $zVon) {
                $disponiertWhere.= $db->quoteInto(
                        ' OR (t.ZeitVon < ? AND t.ZeitBis >= ?)', $zBis) . PHP_EOL;
            }
            $disponiertWhere.= ')';
        }

        if ($gesperrtWhere) {
            $gesperrtQuery = " AND $rsrcKey NOT IN( SELECT ressourcen_id FROM $szTbl WHERE ressourcen_typ=$rsrcTypeQuoted AND ($gesperrtWhere) )";
        } else {
            $gesperrtQuery = '';
        }

        if ($disponiertWhere) {
            $disponiertWhere = "WHERE $disponiertWhere";
        }

        $subSql = <<<EOT
SELECT $rsrcKey FROM $rsrcTbl  
WHERE ($rsrcKey NOT IN(
	SELECT distinct(rsrc.$rsrcKey)
	 FROM $portletTbl p
	 JOIN $timelineTbl tl ON (p.portlet_id = tl.portlet_id)
	 JOIN $tourenTbl t ON (tl.timeline_id = t.timeline_id)
	 JOIN $tourenRsrcTbl tr ON (t.tour_id = tr.tour_id)
	 JOIN $rsrcTbl rsrc ON (tr.$tourenRsrcKey = rsrc.$rsrcKey)
	 $disponiertWhere	 
 ))
 $gesperrtQuery
 ORDER BY $rsrcKey
EOT;


        return $subSql;
    }

    /**
     * @param int $id Ressource-ID (e.g. Mitarbeiter-ID)
     * @param string $DatumVon format YYYY-mm-dd
     * @param string $DatumBis format YYYY-mm-dd
     * @return array [ [ id, tour_id, DatumVon, DatumBis ] ]
     */
    public function getTourlistByIdAndDaterange($id, $DatumVon, $DatumBis)
    {
        $dbDatumVon = $this->_db->quote($DatumVon);
        $dbDatumBis = $this->_db->quote($DatumBis);

        $sqlFetch = 'select r.id, t.tour_id, t.DatumVon, t.ZeitVon '
            .'FROM '.$this->rsrcLnkTbl.' r '
            .' LEFT JOIN mr_touren_dispo_vorgaenge t USING(tour_id) '
            .' WHERE '.$this->_rsrcLnkKey .' = ' . intval($id)
            .'  AND t.IsDefault = 0 '
            .'  AND DatumVon >= ' . $dbDatumVon
            .'  AND DatumVon <= ' . $dbDatumBis
            .' ORDER BY DatumVon, ZeitVon';

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
        $reCountRemoved = 0;
        foreach($aTourIDs as $_tourID) {
            $sqlDelete = 'DELETE FROM '.$this->rsrcLnkTbl.' '
                .' WHERE '.$this->_rsrcLnkKey .' = ' . (int)$id
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

                $this->dispoLog(
                    $id, 'removed', $_tourID,
                    [ 'sperrzeiten_id' => $sperrzeiten_id ]
                );
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

