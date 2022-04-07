<?php

/**
 * @author rybka
 */
class Model_TourenTimelines extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'tourenTimelines';
    protected $_errors = '';
    
    public function getError() 
    {
        return $this->_errors;
    }
    
    protected function _reset()
    {
        $this->_errors = '';
    }
    
    protected function _addError($txt)
    {
        $this->_errors.= ($this->_errors ? PHP_EOL : '') . $txt; 
    }
    
    /**
     *
     * @param array $data
     * @param int $id
     * @return stdClass $result-Object (re->success, re->conflicts, re->error)
     */
    public function update(array $data, $id) 
    {
        $this->_reset();
        $re = new stdClass();
        $re->success = false;
        $re->conflicts = array();
        $re->error = '';
        
        $timeValidator = new MyProject_Validate_Time();
        
        $storage = $this->getStorage();
        $db   = $storage->getAdapter();

        $NAME = Zend_Db_Table::NAME;
        $modelTV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        $tblTV = $modelTV->getStorage()->info($NAME);
    
        $stored = $this->fetchEntry($id);
        if (!$stored) return false;
        
        $chckTouren = array('start'=>'', 'end'=>'');
        $conflict = false;
        
        if (isset($data['start'])) {
            if (!$timeValidator->isValid($data['start'])) {
                $re->error.= 'Ungueltiger Startwert ' . $data['start'];
                return $re;
            }
            if ($stored['start'] < $data['start'])
            $chckTouren['start'] = $data['start']; 
        }
        
        if (isset($data['end'])) { 
            if (!$timeValidator->isValid($data['end'])) {
                $re->error.= 'Ungueltiger Endwert ' . $data['end'];
                return $re;
            }
            if ($stored['end'] > $data['end'])     
            $chckTouren['end'] = $data['end'];
        }
        
        if ($chckTouren['start'] || $chckTouren['end']) {
            $sql = 'SELECT * FROM ' . $db->quoteIdentifier($tblTV) . ' WHERE ' . PHP_EOL;
            $sql.= 'IsDefault != 1'  . PHP_EOL;
            $sql.= ' AND timeline_id = ' . $db->quote($id) . PHP_EOL;
            if ($chckTouren['start'])
            $sql.= ' AND ZeitVon < ' . $db->quote($chckTouren['start']) . PHP_EOL;
            if ($chckTouren['end'])
            $sql.= ' AND ZeitBis > ' . $db->quote($chckTouren['end']) . PHP_EOL;
            $re->sql = $sql;
            $re->conflicts = $db->fetchAll($sql);
        }
        
        if (!count($re->conflicts)) {
            parent::update($data, $id);
            $re->success = true;
        }
        //print_r($re);
        return $re;
    }
    
    public function generateGroupKey($timeline_id)
    {
        $data = $this->fetchEntry($timeline_id);
        if (!$data['group_key']) {
            $data['group_key'] = $timeline_id;
            $this->update($data, $timeline_id);
        }
        return $data['group_key'];
    }
    
    /**
     *
     * @param array $data
     * @param string|int $toPos
     * @return int|null new id or null if error
     * @throws Exception
     */
    public function add($data, $toPos = 'last') 
    {
        $this->_reset();
        $tl = Zend_Registry::get('timeline');
        $default = ($tl && $tl['default']) ? $tl['default'] : array();
        
        $data = array_merge($default, $data);
        
        $id = null;
        if (array_key_exists('portlet_id', $data) ) {
            $id = $this->insert($data);
            $this->movePosition($id, $toPos);
            return $id;
        }
        if (!$id) {
            throw new Exception('ungueltige Daten. Timeline konnte nicht gespeichert werden!' . PHP_EOL . print_r($data,1));
            
        }
        return null;
    }
    
    
    /**
     *
     * @param int $id
     * @return bool success
     * @throws Exception
     */
    public function delete($id, $force = false) 
    {
        $this->_reset();
        
        $storage = $this->getStorage();
        $db   = $storage->getAdapter();
        $posFld   = $db->quoteIdentifier('position');
        $groupFld = $db->quoteIdentifier('portlet_id');
        $hasTours = 0;
        $tourIds = array();
        
        /* @var $modelTV Model_TourenDispoVorgaenge */
        $modelTV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        /* @var $storeTV Model_Db_TourenDispoVorgaenge */
        $storeTV = $modelTV->getStorage();
                
        $modelKey = $modelTV->getStorage()->info(Zend_Db_Table::PRIMARY);
        if (is_array($modelKey)) $modelKey = current($modelKey);
        
        $touren = $modelTV->fetchEntries(array(
           'where' => $db->quoteIdentifier('timeline_id') . ' = ' . $db->quote($id) 
        ));
        
        foreach($touren as $_tour) {
            if (!(int)$_tour['IsDefault']) $hasTours+= 1;
            $tourIds[] = $_tour[$modelKey];
        }
        
        // Wenn force => Lösche alle Touren
        // Wenn keine Touren zugeordnet sind lösche Tour-Default
        if (count($tourIds) ) {
            if ($force || !$hasTours) {
                $storeTV->delete($modelKey.' IN (' . implode(',', $tourIds) . ')');
            } else {
                $this->_addError( 'Konflikt: Der Timeline sind noch ' . $hasTours . ' Touren zugeordnet!');
                return false;
            }
        }
        
        try {
            
            //$db->beginTransaction();
            $data = $this->fetchEntry($id);
            $groupVal = $db->quote($data['portlet_id'], 'int');
            $pos = $data['position'];

            $cond = $posFld . ' > ' . $pos . ' AND ' . $groupFld . ' = ' . $groupVal;

            $storage->update(
                    array('position'=>new Zend_Db_Expr($posFld.'-1')),
                    $cond
            );
            
            parent::delete($id);
            //$db->commit();
        } catch(Exception $e) {
            //$db->rollBack();
            throw $e;
            return false;
        }
        return true;
    }
    
    public function movePosition($id, $toPos) 
    {
        $this->_reset();
        
        $storage = $this->getStorage();
        $db   = $storage->getAdapter();
        $data = $this->fetchEntry($id);
        $tbl  = $db->quoteIdentifier($storage->info(Zend_Db_Table::NAME));
        
        $posFld   = $db->quoteIdentifier('position');
        $groupFld = $db->quoteIdentifier('portlet_id');
        $groupVal = $db->quote($data['portlet_id'], 'int');

        if ($data['position'] == $toPos) return;
        
        $maxPos = $db->fetchOne(
            'SELECT count(1) FROM ' . $tbl . PHP_EOL
           .'WHERE ' . $groupFld . ' = ' . $groupVal
        );
        
        switch($toPos) {
            case 'first': $toPos = 1; break;
            case  'last': $toPos = $maxPos; break;
            case  'prev': $toPos = $data['position'] - 1; break;
            case  'next': $toPos = $data['position'] + 1; break;
        }
        if ($toPos < 1) $toPos = 1;
        elseif ($toPos > $maxPos) $toPos = $maxPos;
        
        if ($data['position'] == $toPos) return;
        
        $fromPos = (int) $data['position'];
        $sql = 'update ' . $tbl . ' SET ' . $posFld . ' = ' . $posFld;
        if ($toPos > $fromPos) {
             $sql.= '- 1 WHERE ' . $posFld . ' > ' . $fromPos . ' AND ' . $posFld . ' <= ';
        } else {
            $sql.= ' + 1 WHERE ' . $posFld . ' < ' . $fromPos . ' AND ' . $posFld . ' >= ';
        }
        $sql.= $db->quote($toPos,'int') . ' AND ' . $groupFld . ' = ' . $groupVal;
        $stmt = $db->query($sql);
        
        $this->update(array('position'=>$toPos), $id);
    }
    
    public function groupedTimelineExists($group_key, $datum)
    {
        $this->getStorage();
        $pTbl = MyProject_Model_Database::loadStorage('tourenPortlets')->info(Zend_Db_Table::NAME);
        $tTbl = $this->getStorage()->info(Zend_Db_Table::NAME);
        $db = $this->getStorage()->getDefaultAdapter();
        
        $sql = 'SELECT COUNT(1) FROM ' . $pTbl . ' p ' . PHP_EOL 
              .'LEFT JOIN ' . $tTbl . ' t ON (p.portlet_id = t.portlet_id) ' . PHP_EOL
              .'WHERE p.datum = :datum AND t.group_key = :group_key';
        
        $params = array('group_key'=>$group_key, 'datum'=>$datum);
        foreach($params as $k => $v) $sql = str_replace(':'.$k, $db->quote($v), $sql);
        return $db->fetchOne($sql ) ;
        $db->fetchOne($sql);
        
    }
    
    public function moveTimeline($timeline_id, $toPos, $toPortletId) 
    {
        $returnObject = new stdClass();
        $returnObject->lockedTouren = array();
        $returnObject->unfreeResources = array();
        $returnObject->success = false;
        $returnObject->message = '';
        
        $this->_reset();
        
        $storage  = $this->getStorage();
        $db       = $storage->getAdapter();
        $timeline = $this->fetchEntry($timeline_id);        
        
        $portletModel = MyProject_Model_Database::loadModel('tourenPortlets');
        $toPortlet = $portletModel->fetchEntry($toPortletId);
        $fromPortlet = $portletModel->fetchEntry($timeline['portlet_id']);
                
        $tourModel = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        
        $ignoreTourIds = array();
        $moveToOtherDay = ($toPortlet['datum'] != $fromPortlet['datum']);
        // Erst mal pruefen, ob die Portlets an unterschiedlichen Tagen eingetragen
        // sind, denn dann müssen keine Touren u. Resourcen umgebucht werden.
        if ( $moveToOtherDay )
        {
            $tourModel = new Model_TourenDispoVorgaenge();
            $touren = $tourModel->getTourenByTimelineId($timeline_id, true);


            foreach($touren as $_tour) {
                $_isLocked = $tourModel->isLocked($_tour['tour_id']);
                $ignoreTourIds[] = $_tour['tour_id'];
                if ($_isLocked) {
                    $returnObject->lockedTouren[]=
                        $_tour['Auftragsnummer'].' '.$_tour['DatumVon'] . ' ' . $_tour['ZeitVon'] 
                            . Model_TourenDispoVorgaenge::getLockTextByCode($_isLocked) . PHP_EOL;
                }
            }

            if (count($returnObject->lockedTouren)) {
                $returnObject->message = 
                    count($returnObject->lockedTouren). ' Touren sind geblockt und müssen erst wieder freigegeben werden:' . PHP_EOL
                   .implode(PHP_EOL, $returnObject->lockedTouren);
                return $returnObject;                    
            }

            $rsrcModels = array(
                'FP' => MyProject_Model_Database::loadModel('tourenDispoFuhrpark'),
                'MA' => MyProject_Model_Database::loadModel('tourenDispoMitarbeiter'),
                'WZ' => MyProject_Model_Database::loadModel('tourenDispoWerkzeug'),
            );

            $num_unfree = 0;
            $resources = array();
            /* @var $_model Model_TourenDispoResourceAbstract */
            foreach($rsrcModels as $k => $_model) {
                $returnObject->unfreeResources[$k] = array();
                $resources = $_model->getResourcesByTimelineId($timeline_id);
                $_rsrcKey = $_model->getResourceKey() ;
                foreach($resources as $_rsrc) {            
                    if (!isset($_rsrc[$_rsrcKey]))
                    die('#'.__LINE__.' ' . __METHOD__ . ' rsrcKey not found ' . $_rsrcKey. ' in ' . print_r($_rsrc,1));
                    
                    $chckFree = $_model->checkResourceIsFree(
                        $_rsrc[$_rsrcKey], 
                        array(
                            'DatumVon' => $toPortlet['datum'],
                            'DatumBis' => $toPortlet['datum'],
                            'ZeitVon'  => $_rsrc['ZeitVon'],
                            'ZeitBis'  => $_rsrc['ZeitBis'],
                            'ignoreTourIds' => $ignoreTourIds,
                        ), 
                        0, 
                        $timeline_id
                    );

                    if (!$chckFree->free) {
                        $returnObject->unfreeResources[$k] = array_merge($returnObject->unfreeResources[$k], $chckFree->data);
                        $num_unfree+= count($chckFree->data);
                    }
                }
            }

            if ($num_unfree) {
                $returnObject->message = $num_unfree . ' Resources sind im Zielbereich bereits verbucht!' . PHP_EOL;
                foreach($returnObject->unfreeResources as $k => $_resources) {
                    if (!count($_resources)) continue;
                    $returnObject->message.= $k . ': ' . count($_resources) . PHP_EOL;
                    foreach($_resources as $_rsrc) 
                        $returnObject->message.= $_rsrc['Auftragsnummer'] . ' / ' .$_rsrc['Resource'] . ' : ' . $_rsrc['DatumVon'] . ' ' . substr($_rsrc['ZeitVon'],0,5) . PHP_EOL;
                }
                return $returnObject;
            }
        }
        
        try {
            $db->beginTransaction();
//            echo '#'.__LINE__ . ' ' . __METHOD__ . PHP_EOL;            
            
            if ( $moveToOtherDay )
            {
                foreach($touren as $_tour) {
//                    echo '#'.__LINE__ . ' ' . __METHOD__ . ' tour_id:'.$_tour['tour_id'].' to ' . $toPortlet['datum'] . PHP_EOL;
                    $tourModel->update(array(
                        'DatumVon' => $toPortlet['datum'],
                        'DatumBis' => $toPortlet['datum'],
                    ), $_tour['tour_id']);
                }
            }

//            echo '#'.__LINE__ . ' ' . __METHOD__ . ' Change Portlet-ID of Timeline: ' . $toPortletId . PHP_EOL;
            if (!$this->update(array('portlet_id' => $toPortletId), $timeline_id)) {
                throw new Exception('Fehler beim Aktualisieren der Portlet-Zuordnung!');
            }

//            echo '#'.__LINE__ . ' ' . __METHOD__ . PHP_EOL;
            $this->updatepositions($toPortletId);
            
//            echo '#'.__LINE__ . ' ' . __METHOD__ . PHP_EOL;
            $this->movePosition($timeline_id, $toPos);   
            
//            echo '#'.__LINE__ . ' ' . __METHOD__ . PHP_EOL;
            $db->commit();
            $returnObject->success = true;
            
        } catch(Exception $e) {
//            echo '#'.__LINE__ . ' ' . __METHOD__ . PHP_EOL;
            $db->rollBack();
            throw $e;
        }
        return $returnObject;
    }
    
    public function updatepositions($portlet_id)
    {
//        die(__METHOD__);
        $last_datum = '';
        $last_daynr = 0;
        /* @var $this->_storage Model_Db_TourenPortlets */
        
        $where = null;
        if ( $portlet_id ) {
            $where = ' portlet_id = '.(int)$portlet_id;
        } else {
            throw new Exception("Ungueltige Portlet-ID: " . $portlet_id . "!");
        }
        
        /* @var $rows Zend_Db_Table_Rowset_Abstract */
        $rows = $this->_storage->fetchAll($where, array('portlet_id','position'));
//        die( print_r($rows,1));
        
        /* @var $row Zend_Db_Table_Row */
        foreach($rows as $row) {
//            die(__METHOD__ . ' row is connected: ' . ($row->isConnected() ? 'true' : 'false') );
            if ($last_datum != $row->portlet_id) {
                $last_daynr = 0;
                $last_datum = $row->portlet_id;
            }
            $row->position = ++$last_daynr;
            $row->save();
        }
    }
    
    public function getDispoVorgaenge($timeline_id, $withDefaults = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        /* @var $storageDV Model_Db_TourenDispoVorgaenge */
        $storageDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge');
        $storageV = MyProject_Model_Database::loadStorage('vorgaenge');
        
        $select = $db->select()->from(array('dv'=>$storageDV->info(Zend_Db_Table::NAME)))
                     ->joinLeft(array('v'=>$storageV->info(Zend_Db_Table::NAME)), 'dv.Mandant=v.Mandant AND dv.Auftragsnummer=v.Auftragsnummer' )
                     ->where(
                        (!$withDefaults ? 'IsDefault = 0 AND ' : '')
                        .'timeline_id = ' . $db->quote($timeline_id, Zend_Db::INT_TYPE),
                        array('DatumVon','ZeitVon')
         )->order(['DatumVon', 'ZeitVon', 'ZeitBis']);
        
        return $db->fetchAll($select, null, Zend_Db::FETCH_ASSOC);
    }
    
    public function countVorgaenge($timeline_id, $withDefaults = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        /* @var $storageDV Model_Db_TourenDispoVorgaenge */
        $storageDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge');
        $storageV = MyProject_Model_Database::loadStorage('vorgaenge');
        
        
        $select = $db->select()->from(array('dv'=>$storageDV->info(Zend_Db_Table::NAME)), new Zend_Db_Expr('COUNT(1)'))
                     ->joinLeft(array('v'=>$storageV->info(Zend_Db_Table::NAME)), 'dv.Mandant=v.Mandant AND dv.Auftragsnummer=v.Auftragsnummer' )
                     ->where(
                        (!$withDefaults ? 'IsDefault = 0 AND ' : '')
                        .'timeline_id = ' . $db->quote($timeline_id, Zend_Db::INT_TYPE), array('DatumVon','ZeitVon') 
         );
        
        return $db->fetchOne($select);
    }
}
