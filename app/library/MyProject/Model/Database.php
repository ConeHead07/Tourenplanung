<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author rybka
 */
class MyProject_Model_Database extends MyProject_Model_Abstract 
{
    protected $_storage = null;
    protected $_db = null;
    protected $_tbl = null;
    protected $_key = null;

    public function __construct()
    {
        $this->_storage = $this->getStorage();
        $this->_db = $this->_storage->getAdapter();
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);

        $a = $this->keys();
        if (is_array($a) && count($a)) {
            $this->_key = $a[0];
        } elseif (is_string($a)) {
            $this->_key = $a;
        }
    }

    /**
     * 
     * @param string $class
     * @return Zend_Db_Table_Abstract
     */
    public static function getStorageByClass($storageName)
    {
        $storageClass = 'Model_Db_' . ucfirst($storageName);
        return new $storageClass;
    }

    /**
     * @throws Zend_Db_Table_Exception
     * @return array of Primary-Keys
     */
    public function keys()
    {
        $this->_storage->info(Zend_Db_Table::PRIMARY);
    }

    /**
     * @param  int index of Primary-Key should be returned from array of Primary-Keys
     * @throws Zend_Db_Table_Exception
     * @return string Name of indexed Primary-Key
     */
    public function key(int $idx = 0)
    {
        $a = $this->_storage->info(Zend_Db_Table::PRIMARY);
        return $a[$idx];
    }

    /**
     * @param $sQuery string SQL-Query
     * @param $sSrc string Source like File, Method, Line-Number, etc
     * @param $iQueryID Frei definierbare ID für ein SQL-Statement als Filterkriterium, ob logging erfolgen soll, etc.
     */
    protected function logQuery($sQuery, $sSrc, $iQueryID)
    {
        $sLogFileHeute = APPLICATION_PATH . '/log/app/heute.sql.txt';
        $sLogFileGestern = APPLICATION_PATH . '/log/app/gestern.sql.txt';

        if (file_exists($sLogFileHeute) && date('Y-m-d', filemtime($sLogFileHeute)) < date('Y-m-d')) {
            unlink($sLogFileGestern);
            rename($sLogFileHeute, $sLogFileGestern);
            touch($sLogFileHeute);
        }

        file_put_contents(
            $sLogFileHeute,
            date('Y-m-d H:i:s') . ' QueryID:' . $iQueryID . ' ' . $sSrc . "\r\n" . $sQuery . "\r\n" . "\r\n",
            FILE_APPEND);
    }
    /**
     * Daten einfuegen
     *
     * @param array $data einzufuegende Daten
     * @throws MyProject_Model_Exception 
     * @return string neuer Datensatzschluessel
     */
    public function insert(array $data)
    {
        // Fange Exception ab
        try {
            $uid = self::_getUID();
            // Hole Spalten
            $columns = $this->getStorage()->info(Zend_Db_Table_Abstract::COLS);

            // Durchlaufe Daten und entferne unbekannte Spalten
            foreach ($data as $column => $value) {
                if (!in_array($column, $columns)) {
                    unset($data[$column]);
                }
            }

            if (in_array('created', $columns)
                && @empty($data['created'])) $data['created'] = new Zend_Db_Expr('NOW()');

            if (in_array('created_uid', $columns)
                && @empty($data['created_uid'])) $data['created_uid'] = $uid;

            if (in_array('modified_uid', $columns)
                && @empty($data['modified_uid'])) $data['created_uid'] = $uid;

            // Erstelle neue Zeile
            $row = $this->getStorage()->createRow($data);

            // Speichere Zeile
            $newId = $row->save();

            //die('#'.__LINE__.'' . __METHOD__ . ' ' .PHP_EOL . ' save' . PHP_EOL . print_r($data,1));
        } catch (Zend_Db_Exception $e) {
            // Werfe eigene Exception
            throw new MyProject_Model_Exception('Eintrag konnte nicht gespeichert werden:'.$e->getMessage());
            //."\n".$e->getTraceAsString());
        }

        // Speichern war erfolgreich
        return $newId;
    }

    /**
     * Daten aendern
     *
     * @param array $data zu aendernde Daten
     * @param string $id Datensatzschluessel
     * @throws MyProject_Model_Exception
     * @return mixed string|array on success
     */
    public function update(array $data, $id)
    {
        // Fange Exception ab
        try {
            $uid = self::_getUID();
            $s = $this->getStorage();

            // Hole Spalten
            $columns = $s->info(Zend_Db_Table_Abstract::COLS);

            // Durchlaufe Daten und entferne unbekannte Spalten
            foreach ($data as $column => $value) {
                if (!in_array($column, $columns)) {
                    unset($data[$column]);
                }
            }

            if (in_array('modified_uid', $columns)
                && @empty($data['modified_uid'])) $data['modified_uid'] = $uid;

            // Lade Zeile
            if (is_array($id)) {
//                die('#'.__LINE__ . ' ' . __METHOD__ . ' id: ' . print_r($id,1));
                $rowset = call_user_func_array(array($s, 'find'), $id);
                $row = $rowset->current();
            } else {
//                die('#'.__LINE__ . ' ' . __METHOD__);
                $row = $s->find($id)->current();
            }
            if ($row) {
                // Befuelle Zeile mit Daten
                $row->setFromArray($data);

                // Speichere Zeile
                return $row->save();
//                echo '#' . __LINE__ . ' saved: '; var_dump($saved);
            } else {
                throw new MyProject_Model_Exception('CanNot get Record with id ' . $id);
            }

        } catch (Zend_Db_Exception $e) {
            // Werfe eigene Exception
            throw new MyProject_Model_Exception('Aenderung konnte nicht gespeichert werden: ' . $e->getMessage());
        }

        // Speichern war erfolgreich
        return false;
    }

    /**
     * MySQL-Spezifische Replace-Anweisung
     * Wenn bereits Daten mit gleichen Keys vorliegen, werden diese aktualisiert
     * andernfalls hinzugefügt
     * @param array $data
     * @return bool
     */
    public function replace(array $data)
    {
        // Fange Exception ab
        try {
            // Hole Spalten
            $columns = $this->getStorage()->info(Zend_Db_Table_Abstract::COLS);

            $db = $this->getStorage()->getDefaultAdapter();
            $tbl = $this->getStorage()->info(Zend_Db_Table_Abstract::NAME);
            $sql = 'REPLACE ' . $tbl . ' SET ';
            $set = '';
            // Durchlaufe Daten und entferne unbekannte Spalten
            foreach ($data as $column => $value) {
                if (in_array($column, $columns)) {
                    $set.= ($set ? ",\n" : '') . $db->quoteIdentifier($column) . ' = ' . $db->quote($value);
                }
            }
            if ($set) {
                $db->query($sql . $set);
                return true;
            }
        } catch (Zend_Db_Exception $e) {
            // Werfe eigene Exception
            throw new MyProject_Model_Exception('Aendern war nicht erfolgreich' . PHP_EOL . $e->getMessage() );
        }

        // Speichern war erfolgreich
        return false;
    }

    /**
     * Daten loeschen
     *
     * @param string $id Datensatzschluessel
     * @throws MyProject_Model_Exception
     * @return boolean
     */
    public function delete($id)
    {

        // Fange Exception ab
        try {
            // Lade Zeile
            $s = $this->getStorage();
            $rowset = call_user_func_array(array($s, 'find'), func_get_args());
            $row    = $rowset->current();

            // Lösche Zeile, returns num rows deleted
            return ($row) ? $row->delete() : false;

//            $t = new Zend_Db_Table();
//            $t->delete( /* where */ );
        } catch (Zend_Db_Exception $e) {
            // Werfe eigene Exception
//            die('#'.__LINE__ . ' ' . __METHOD__ . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            throw new MyProject_Model_Exception('Löschen war nicht erfolgreich' . PHP_EOL . $e->getMessage() );
        } catch(Exception $e) {
            die('#' . __LINE__ . ' ' . __METHOD__ . ': ' . $e->getMessage() );
        }

        // Loeschen war erfolgreich
        return false;
    }

    protected static function _getUID() {
        return MyProject_Auth_Adapter::getUserId();
    }

    /**
     * Alle Datensaetze holen
     * @param array $options Possible keys: where=>string, order=>string, count=>int, offset=>int
     * @return array
     */
    public function fetchEntries($options = array())
    {
        // Gebe Daten der Zeilen zurueck
        /* @var $_storage Zend_Db_Table */
        $_storage = $this->getStorage();

        $where  = (array_key_exists('where', $options)  ? $options['where']  : NULL);
        $order  = (array_key_exists('order', $options)  ? $options['order']  : NULL);
        $count  = (array_key_exists('count', $options)  ? $options['count']  : NULL);
        $offset = (array_key_exists('offset', $options) ? $options['offset'] : NULL);

        $select = $_storage->select()->where($where);
//        echo $select->assemble() . PHP_EOL;
        return $_storage->fetchAll($where, $order, $count, $offset)->toArray();
    }

    /**
     * @param array $aQueryOptions
     * @return MyProject_Model_QueryBuilder
     */
    public function buildQuery(array $aQueryOptions)
    {
        return new MyProject_Model_QueryBuilder($aQueryOptions);
    }

    /**
     * Count
     *
     * @param string $where
     * @return int
     */
    public function fetchCount($where)
    {
        // Gebe Daten der Zeile zurueck
        $fromTbl = $this->getStorage()->info(Zend_Db_Table::NAME);
        $select = $this->getStorage()->select(false)
            ->from($fromTbl, new Zend_Db_Expr('count(1)'))
            ->where($where);
        $db = $this->getStorage()->getAdapter();

        return (int) $db->fetchOne($select);
    }



    /**
     * Einen Datensatz holen
     *
     * @param string ... $id Datensatzschluessel
     * @return array
     */
    public function fetchEntry($id)
    {
        // Gebe Daten der Zeile zurueck
        $s = $this->getStorage();
        $rows = call_user_func_array(array($s, 'find'), func_get_args());
        $row = $rows->current();
        return is_null($row) ? null : $row->toArray();
    }

    /**
     * @abstract Konvertiert Datenarray über Tabellen-Struktur
     * aus Zend_Db_Table_Abstract::info() in Datenstruktur zu
     * Verwendung mit alten System-Komponenten zur Auswertung
     * in JqGridSearch
     * @return array Table-Field-Configuration
     */
    public function infoToTblConf() {
        $TblInfo = $this->getStorage()->info();
        if (!$TblInfo) return null;
//        die(print_r($TblInfo,1));

        $TblCnf = array();
        $TblCnf['Db'] = '';
        $TblCnf['Table'] = $TblInfo['name'];
        $TblCnf['Title'] = $TblInfo['name'];
        $TblCnf['ConfName'] = $TblInfo['name'];

        $fCnfDefault = array(
            "htmlType" => "text",
            "default" => "",
            "required" => true,
            "null" => false,
            "unique" => false,
            "min" => null,
            "max" => null);
        $TblCnf['Fields'] = array(); //$TblInfo['metadaten'];
        foreach($TblInfo['metadata'] as $i => $fCnf) {
            $k = $fCnf['COLUMN_NAME'];
            $TblCnf['Fields'][$k] = $fCnfDefault;
            $TblCnf['Fields'][$k]['dbField'] = $k;
            $TblCnf['Fields'][$k]['key'] = ($fCnf['PRIMARY'] ? 'PRI' : '');
            $TblCnf['Fields'][$k]['label'] = $k;
            $TblCnf['Fields'][$k]['listlabel'] = $k;
            $TblCnf['Fields'][$k]['type'] = $fCnf['DATA_TYPE'];
            $TblCnf['Fields'][$k]['systype'] = $fCnf['DATA_TYPE'];
            $TblCnf['Fields'][$k]['default'] = $fCnf['DEFAULT'];
            $TblCnf['Fields'][$k]['null'] = (bool)$fCnf['NULLABLE'];
            $TblCnf['Fields'][$k]['size'] = $fCnf['LENGTH'];
        }
        return $TblCnf;
    }

    public function getWhereByJGridFilter($filter, $prefix)
    {
        $cols = $this->getStorage()->info(Zend_Db_Table::COLS);
        $re = (object)array(
            'where' => '',
            'unknown' => array(),
        );

        if (empty($filter->groupOp) || !in_array(strtoupper($filter->groupOp), array('AND','OR'))) {
            $filter->groupOp = 'AND';
        }

        foreach($filter->rules as $i => $v)
        {
            if (in_array($v->field, $cols)) {
                $where[] = ($prefix ? "$prefix." : '') . $v->field . ' ' . $this->getSearchOperation($v->data, $v->op);
            } else {
                $re->unknown[] = $v->field;
            }
        }
        $re->where = implode($filter->groupOp, $where);
        return $re;
    }

    /**
     *
     * @param string $term
     * @param string $oper
     * @return string
     */
    public function getSearchOperation($term, $oper)
    {
        // ['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc']

        $db = $this->getStorage()->getAdapter();
        switch($oper) {
            case "gt": // groesser</option>
                $oper = '>';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;

            case "ge": // groesser gleich</option>
                $oper = '>=';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;

            case "lt": // kleiner</option>
                $oper = '<';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;

            case "le": // kleiner gleich</option>
                $oper = '<=';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;

            case "eq": // gleich</option>
                $oper = '=';
                $term = $db->quote($term);
                break;

            case "ne": // ungleich</option>
                $oper = '<>';
                $term = $db->quote($term);
                break;

            case "bw": // beginnt mit</option>
                $oper = 'LIKE';
                $term = $db->quote($term . '%');
                break;

            case "bn": // beginnt nicht mit</option>
                $oper = 'NOT LIKE';
                $term = $db->quote($term . '%');
                break;

            case "ew": // endet mit</option>
                $oper = 'LIKE';
                $term = $db->quote('%' . $term);
                break;

            case "en": // endet nicht mit</option>
                $oper = 'Not LIKE';
                $term = $db->quote('%' . $term);
                break;

            case "cn": // enthält</option>
                $oper = 'LIKE';
                $term = $db->quote('%' . $term . '%');
                break;

            case "nc": // enthält nicht</option>
                $oper = 'NOT LIKE';
                $term = $db->quote('%' . $term . '%');
                break;

            case "null":
            case "nu": // is null</option>
                $oper   = 'IS';
                $term = 'NULL';
                break;

            case "nn": // is not null</option>
                $oper   = 'IS NOT';
                $term = 'NULL';
                break;

            case "in": // ist in</option>
                $oper   = 'LIKE';
                $term = $db->quote($term);
                break;

            case "ni": // ist nicht in</option>
                $oper   = 'NOT LIKE';
                $term = $db->quote($term);
                break;
        }
        return trim($oper . ' ' . $term);
    }
}

