<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function my_assert_handler($file, $line, $code)
{
    echo "<hr>Assertion Failed:
        File '$file'<br />
        Line '$line'<br />
        Code '$code'<br /><hr />";
}
/**
 * Description of User
 *
 * @author rybka
 */
class Model_TourenPortlets extends MyProject_Model_Database {

    //put your code here
    protected $_storageName = 'tourenPortlets';
    protected $_storage = null;
    protected $_db = null;
    protected $_tbl = null;

    public function __construct() {
        parent::__construct();
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);
    }

    public function dispoLog($portlet_id, $action, $aDetails) {
        $uid = MyProject_Auth_Adapter::getUserId();
        $this->getTourDispoLogger()->logTourenplan($portlet_id, $action, $uid, $aDetails);
    }

    public function operationIsAllowedById(int $id, object $userIdentity, string $action ) {

        if ($userIdentity->user_role == 'innendienst') {
            if (in_array($action, ['updateportlettitle'])) {
                return true;
            }
        }
        return true;
    }
    
    public function updatepositions($datum = '')
    {
//        die(__METHOD__);
        $last_datum = '';
        $last_daynr = 0;
        /* @var $this->_storage Model_Db_TourenPortlets */
        
        $where = null;
        if ($datum) {
            if ( strtotime($datum) ) {
                $where = ' datum = '.$this->_db->quote(date('Y-m-d', strtotime($datum)));
            } else {
                throw new Exception("Ungueltiges Datumskriterium: " . $datum . "!");
            }
        }
        
        /* @var $rows Zend_Db_Table_Rowset_Abstract */
        $rows = $this->_storage->fetchAll($where, array('datum','position'));
//        die( print_r($rows,1));
        
        /* @var $row Zend_Db_Table_Row */
        foreach($rows as $row) {
//            die(__METHOD__ . ' row is connected: ' . ($row->isConnected() ? 'true' : 'false') );
            if ($last_datum != $row->datum) {
                $last_daynr = 0;
                $last_datum = $row->datum;
            }
            $row->position = ++$last_daynr;
            $row->save();
        }
    }
    
    public function getNewTagesnr($datum, $lager_id)
    {
        $dateValidator = new MyProject_Validate_Date();
        if (!$dateValidator->isValid($datum)) {
            throw new Exception("Ungueltiges Datum " . $datum);
        }
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db  = $this->_storage->getAdapter();
        $sql = 'SELECT ifnull( MAX( tagesnr ) , 0 ) +1 neuenr FROM ' . $this->_tbl . PHP_EOL
              .'WHERE datum = ' . $db->quote($datum) . ' AND lager_id = ' . $db->quote($lager_id);
        
        return $db->fetchOne($sql);
    }

    /**
     *
     * @param string $datum Format YYYY-MM-DD (ISO 8601)
     * @return string number of max position
     */
    public function getMaxPos($datum, $lager_id) {
        return $this->_db->fetchOne(
                        'SELECT count(1) FROM ' . $this->_db->quoteIdentifier($this->_tbl) . PHP_EOL
                        . 'WHERE datum = :datum'
                        . ' AND lager_id = :lager_id', 
                        array(':datum' => $datum, ':lager_id' => $lager_id));
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
        $id = null;
        $rgxIsoDate = ':^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[0-2])$:';
        if (array_key_exists('datum', $data) && preg_match($rgxIsoDate, $data['datum'])) {
            $data['tagesnr'] = $this->getNewTagesnr($data['datum'], $data['lager_id']);
            $id = $this->insert($data);
            $this->movePosition($id, $toPos);
            $this->updatepositions($data['datum']);
            $this->dispoLog($id, 'insert', ['DatumVon' => $data['datum'], 'bemerkunng' => json_encode($data)]);
            return $id;
        }
        if (!$id) {
            throw new Exception('ungueltige Daten. Portlet konnte nicht gespeichert werden!' . PHP_EOL . print_r($data,1));
        }
        return null;
    }
    
    /**
     * 
     * @param array $data
     * @param array $tlData
     * @param int|string $toPos
     * @return array| ids [portlet_id, timeline_id]null
     */
    public function addPortletAndTimeline($data, $tlData = array(), $toPos = 'last')
    {
        $portlet_id  = $this->add($data, $toPos);
        
        if (!$portlet_id) return null;
        
        $tlData['portlet_id'] = $portlet_id;
        
        $modelTL = new Model_TourenTimelines();
        $timeline_id = $modelTL->add($tlData);
        
        return array($portlet_id, $timeline_id);        
    }
        
    /**
     *
     * @param int $id
     * @return bool success
     * @throws Exception
     */
    public function delete($id) 
    {
        $storage  = $this->getStorage();
        $db       = $storage->getAdapter();
        $posFld   = $db->quoteIdentifier('position');
        $groupFld = $db->quoteIdentifier('datum');
        
        try {
            $db->beginTransaction();
            $data = $this->fetchEntry($id);

            $SqlDeleteDefaults = 
                 'DELETE FROM mr_touren_dispo_vorgaenge WHERE timeline_id IN'
                .'('
                .' SELECT timeline_id FROM mr_touren_timelines '
                .' WHERE portlet_id = :id'
                .')'
                .' AND IsDefault = 1';
//            die(strtr($SqlDeleteDefaults, array(':id' => $id) ));
            $db->query($SqlDeleteDefaults, array(':id' => $id));
            
            parent::delete($id);
            
            $groupVal = $db->quote($data['datum']);
            $pos = $data['position'];

            $cond = $posFld . ' > ' . $pos . ' AND ' . $groupFld . ' = ' . $groupVal;

            $storage->update(
                    array('position'=>new Zend_Db_Expr($posFld.'-1')),
                    $cond
            );
            
            $db->commit();
        } catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return true;
    }


    public function movePosition($id, $toPos) {
        $storage = $this->getStorage();
        $db = $storage->getAdapter();
        $data = $this->fetchEntry($id);
        $tbl = $storage->info(Zend_Db_Table::NAME);

        if ($data['position'] == $toPos)
            return;
        
        $maxPos = $this->getMaxPos($data['datum'], $data['lager_id']);

        switch ($toPos) {
            case 'first': $toPos = 1;
                break;
            case 'last': $toPos = $maxPos+1;
                break;
            case 'prev': $toPos = $data['position'] - 1;
                break;
            case 'next': $toPos = $data['position'] + 1;
                break;
        }
        if ($toPos < 1)
            $toPos = 1;
        elseif ($toPos > $maxPos)
            $toPos = $maxPos;

//      die (__METHOD__ . ' :-( ');
        if ($data['position'] == $toPos)
            return;

        $fromPos = (int) $data['position'];
        $sql = 'update ' . $tbl . ' SET position = position';
        if ($toPos > $fromPos) {
            $sql.= '- 1 WHERE position > ' . $fromPos . ' AND position <= ';
        } else {
            $sql.= ' + 1 WHERE position < ' . $fromPos . ' AND position >= ';
        }
        $sql.= $db->quote($toPos, 'int') . ' AND datum = :datum AND lager_id = :lager_id';
        $stmt = $db->query($sql, array(':datum' => $data['datum'], ':lager_id' => $data['lager_id']));

        $this->update(array('position' => $toPos), $id);
    }
    
    public function getTimelines($portlet_id) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        /* @var $storageT Model_Db_TourenTimelines */
        $storageT = MyProject_Model_Database::loadStorage('tourenTimelines');
        
        $storage = new Model_Db_TourenTimelines();
        return $storage->fetchAll('portlet_id = ' . $db->quote($portlet_id, Zend_Db::INT_TYPE), 'position' )->toArray();
    }
    
    public function countTimelines($portlet_id) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        /* @var $storage Model_Db_TourenTimelines */        
        $storage = new Model_Db_TourenTimelines();
        $sql = 'SELECT COUNT(1) FROM ' . $storage->info(Zend_Db_Table::NAME)
              .' WHERE portlet_id = '  . $db->quote($portlet_id, Zend_Db::INT_TYPE);
        return $db->fetchOne($sql);
    }

    public function getDatum(int $portlet_id)
    {
        $sql = 'SELECT datum FROM ' . $this->getTable() . ' WHERE ' . $this->key() . ' = ' . (int)$portlet_id;
        return $this->_db->fetchOne( $sql );
    }

}
