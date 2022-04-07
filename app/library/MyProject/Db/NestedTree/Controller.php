<?php
$_DIR_ = (__DIR__) ? __DIR__ : dirname(__FILE__);
include_once( $_DIR_ . '/NodeInterface.php');
include_once( $_DIR_ . '/Nodes.php');
include_once( $_DIR_ . '/Node.php');
include_once( $_DIR_ . '/Exception.php');

class MyProject_Db_NestedTree_Controller {

    // Default Identifiers
    protected $_name = 'nested_category';
    protected $_primary = 'category_id';
    protected $_caption = 'name';
    protected $_left = 'lft';
    protected $_right = 'rgt';
    /** @var $_db PDO **/
    protected $_db = null;
    // Default Quote Identifiers (Without quoting)
    protected $_qname = 'nested_category';
    protected $_qprimary = 'category_id';
    protected $_qcaption = 'name';
    protected $_qleft = 'lft';
    protected $_qright = 'rgt';
    
    protected $_schema = array();

    public function __construct(array $props = null) {
        foreach ($props as $k => $v) {
            if (property_exists($this, '_' . $k))
                $this->{'_' . $k} = $v;
            if (property_exists($this, '_q' . $k))
                $this->{'_q' . $k} = $v;
        }
        if ($this->_db)
            $this->_quoteIdentifiers();
        
        $this->_schema = array(
            'name' => $this->_name,
            'primary' => $this->_primary,
            'caption' => $this->_caption,
            'left' => $this->_left,
            'right' => $this->_right
        );
        
        /* @var $this->_db PDO */
        if (!$this->_db) {
            throw new MyProject_Db_NestedTree_Exception('Invalid DB-Adapter db:' . print_r($this->_db));
        }
    }
    
    /**
     * Liefert Benennungen node-relevanter Benennnungen (Tabelle und Felder)
     * @param string $key [name,caption,primary,left,right]
     * @return string|array
     */
    public function info($key = 'all') {
        if ($key == 'all') return $this->_schema;
        if (array_key_exists($key, $this->_schema)) return $this->_schema[$key];
        return null;
    }
    
    /**
     * DB-spezifische Maskierung der Feldangaben
     * @return void
     */
    protected function _quoteIdentifiers() {
        /* @var $this->_db PDO */
        $this->_qname = $this->_db->quoteIdentifier($this->_name);
        $this->_qprimary = $this->_db->quoteIdentifier($this->_primary);
        $this->_qleft = $this->_db->quoteIdentifier($this->_left);
        $this->_qright = $this->_db->quoteIdentifier($this->_right);
    }

    /**
     *
     * @param PDO|Zend_Db_Adapter_Abstract $db 
     */
    public function setDbAdapter($db) {
        $this->_db = $db;
        $this->_quoteIdentifiers();
    }

    public function hasChildrenByNodeData($data) {
        return ($data[$this->_right] > $data[$this->_left] + 1);
    }

    public function getChildrenByNodeData($data) {
        return $this->getChildren($data[$this->_primary]);
    }
    
    /**
     * Lücke schliessen
     * @param int $lft
     * @param int $rgt
     * @param bool $withChilds 
     */
    protected function _reduceLftRgt($lft, $rgt, $withChilds) {

        $width = $rgt - $lft + 1;
        $countChildsByWidth = ($rgt - $lft - 1) / 2;
        $reduceWidth = ($withChilds || !$countChildsByWidth) ? $width : 2;

        /* @var $stmt PDOStatement */
        if ($withChilds || !$countChildsByWidth) {

            // Bestehenden Tree reduzieren
            // Reduce Left-Values
            $sql = "UPDATE " . $this->_qname . " SET $this->_qleft = $this->_qleft - $reduceWidth WHERE $this->_qleft > $lft";
            $stmt = $this->_db->query($sql);

            // Reduce Right-Values
            $sql = "UPDATE " . $this->_qname . " SET $this->_qright = $this->_right - $reduceWidth WHERE $this->_qright > $rgt";
            $stmt = $this->_db->query($sql);
        } else {

            // Entstandene Lücke des Parent-Elements schliessen, in die Child-Elemente um Eins aufrücken
            // Reduce Childs um Eins
            $sql = "UPDATE " . $this->_qname . " SET $this->_qleft = $this->_qleft - 1, $this->_qright = $this->_qright - 1 "
                    . "WHERE $this->_qleft between $lft AND $rgt AND $this->_qright between $lft AND $rgt";
            $stmt = $this->_db->query($sql);

            // Bestehenden Tree reduzieren
            // Reduce Left
            $sql = "UPDATE " . $this->_qname . " SET $this->_qleft = $this->_qleft - $reduceWidth WHERE $this->_qleft > $rgt";
            $stmt = $this->_db->query($sql);

            // Reduce Right
            $sql = "UPDATE " . $this->_qname . " SET $this->_qright = $this->_qright - $reduceWidth WHERE $this->_qright > $rgt";
            $stmt = $this->_db->query($sql);
        }
    }

    /**
     * Teilt den Baum, um Platz für das Einfügen bzw. Verschieben von Elementen zu schaffen
     * @param int $relLeft
     * @param int $relRight
     * @param string $dstPos
     * @param int $width 
     * @return int newLeftStart
     */
    protected function _divideLftRgt($relLeft, $relRight, $dstPos = ' last', $width = 2) {
        switch ($dstPos) {
            // rel => Relation-Destination-Node-Values
            case 'prev' :
                // Pos to before left
                $extendLftCond = "$this->_qleft >= " . $relLeft;
                $extendRgtCond = "$this->_qright >= " . $relLeft;
                $newLftStart = $relLeft;
                break;

            case 'next' :
                // Pos to behind right
                $extendLftCond = "$this->_qleft > " . $relRight;
                $extendRgtCond = "$this->_qright > " . $relRight;
                $newLftStart = $relRight + 1;
                break;

            // relNodeId AS ParentId
            case 'first' :
                $extendLftCond = "$this->_qleft > " . $relLeft;
                $extendRgtCond = "$this->_qright > " . $relLeft;
                $newLftStart = $relLeft + 1;
                break;
            // 
            case 'last' :
                $extendLftCond = "$this->_qleft >= " . $relRight;
                $extendRgtCond = "$this->_qright >= " . $relRight;
                $newLftStart = $relRight;
                break;
            
            case 'parent':
                $width = 1;
                // Pos to before left
                $extendLftCond = "$this->_qleft >= " . $relLeft;
                $extendRgtCond = "$this->_qright >= " . $relLeft;
                $newLftStart = $relLeft;
                
                // Pos to behind right
                $extendLftCond2 = "$this->_qleft > " . $relRight;
                $extendRgtCond2 = "$this->_qright > " . $relRight;
                break;

            default:
                throw new MyProject_Db_NestedTree_Exception("Invalid Position " . $dstPos . ' ! Valid Values are next, prev, first, last.');
        }

        // Zwischenraum schaffen
        // Extend Left-Values
        $sql = "UPDATE $this->_qname SET $this->_qleft = $this->_qleft + $width WHERE " . $extendLftCond;
        $this->_db->exec($sql);

        // Extend Right-Values
        $sql = "UPDATE $this->_qname SET $this->_qright = $this->_qright + $width WHERE " . $extendRgtCond;
        $this->_db->exec($sql);
        
        // If Parent, schaffe auch nach dem Destination-Node Raum um eine Stelle ($width = 1)
        if ($dstPos == 'parent') {
            // Extend Left-Values
            $sql = "UPDATE $this->_qname SET $this->_qleft = $this->_qleft + $width WHERE " . $extendLftCond2;
            $this->_db->exec($sql);

            // Extend Right-Values
            $sql = "UPDATE $this->_qname SET $this->_qright = $this->_qright + $width WHERE " . $extendRgtCond2;
            $this->_db->exec($sql);
        }

        return $newLftStart;
    }

    /**
     * @param int|MyProject_Db_NestedTree_Node $id NodeId
     * @param bool $withChilds
     * @return int Left of temp Position as maxRightValues for the official TreeRange
     */
    protected function _deleteNodeEntry($id, $withChilds) 
    {
        /* @var $stmt PDOStatement */
        if ($withChilds) {
            $data = $this->getNodeData($id);
            $l = $data[$this->_left];
            $r = $data[$this->_right];
            
            // Node mit Subnode ausserhalb der Range sichern
            $sql = "DELETE FROM " . $this->_qname 
                 . " WHERE $this->_qleft between $l AND $r AND $this->_qright between $l AND $r";
        } else {
            // Node ohne Subnode ausserhalb der Range sichern
            $sql = "DELETE FROM " . $this->_qname . " WHERE $this->_qprimary = " . (int) $id;
        }
        $stmt = $this->_db->query($sql);
    }

    /**
     * Node or SubTree ausserhalb des offiziellen Trees verschieben,
     * um Neupositionierung im Tree vorzubereiten
     * @param int|MyProject_Db_NestedTree_Node Node-ID
     * @param bool $withChilds
     * @return int Left of temp Position as maxRightValues for the official TreeRange
     */
    protected function _moveNodeOutOfTree($id, $withChilds) {

        $data = $this->getNodeData($id);
        $l = $data[$this->_left];
        $r = $data[$this->_right];
        $w = $r - $l + 1;

        $maxRight = $this->_getMaxRight();

        /* @var $stmt PDOStatement */
        if ($withChilds || $w == 2) {
            // Node mit Subnode ausserhalb der Range sichern
            $sql =
                    "UPDATE " . $this->_qname
                    . " SET $this->_qleft = $this->_qleft + $maxRight, $this->_qright = $this->_qright + $maxRight "
                    . " WHERE $this->_qleft between $l AND $r AND $this->_qright between $l AND $r";
        } else {
            // Node ohne Subnode ausserhalb der Range sichern
            $sql =
                    "UPDATE " . $this->_qname
                    . " SET $this->_qleft = $l + $maxRight, $this->_qright = $l + $maxRight + 1 "
                    . " WHERE $this->_qprimary = " . (int) $id;
        }
        $stmt = $this->_db->query($sql);

        return $maxRight;
    }
    
    /**
     * Node an vorgegeben Left-Value positionieren
     * @param int|MyProject_Db_NestedTree_Node $id Node-Id
     * @param int $dstLftStart 
     */
    protected function _moveNodeIntoSpace($id, $dstLftStart) {
        /* Get Temp-Position of Object to move */
        $tmpData = $this->getNodeData($id);
        $tmpLft = $tmpData[$this->_left];
        $tmpRgt = $tmpData[$this->_right];
        
        // TmpLft-Value sollte immer gr��er sein als dstLft
        $offset = ($tmpLft > $dstLftStart) ? "-" . ($tmpLft - $dstLftStart) : "+" . ($dstLftStart - $tmpLft);

        // Tree in neu geschaffenen Zwischenraum verschieben
        /* @var $stmt PDOStatement */
        $sql = "UPDATE " . $this->_qname . " SET $this->_qleft = $this->_qleft $offset, $this->_qright = $this->_qright $offset WHERE $this->_qleft between $tmpLft AND $tmpRgt AND $this->_qright between $tmpLft AND $tmpRgt";
        $stmt = $this->_db->query($sql);
    }
    
    /**
     *
     * @param array|int $data array mit NodeData oder int NodeId
     * @return MyProject_Db_NestedTree_Node
     */
    protected function _getNode($data) {
        if (!$data) return NULL;
        if ($data instanceof MyProject_Db_NestedTree_Node) $id = $data->getNodeId();
        elseif (is_array($data) && array_key_exists($this->_primary, $data)) $id = $data[$this->_primary];
        elseif (is_numeric($data)) $id = (int)$data;
        else            throw new MyProject_Db_NestedTree_Exception('Ungültige Datenstruktur!');
        
        $nodeData = $this->getNodeData($id);        
        return (count($data)) ? new MyProject_Db_NestedTree_Node($nodeData, $this) :  NULL;
    }
    
    /**
     * Prüft, ob Node
     * @param array|int $data array mit NodeData oder int NodeId
     * @return bool isValid
     */
    public function _isValidNode($data) {
        if (!$data || !$data instanceof MyProject_Db_NestedTree_Node) return false;
        $id = $data->getNodeId();
        
        $nodeData = $this->getNodeData($id);
        return (is_array($nodeData) && !@empty($nodeData[$this->_primary]));
    }
    
    /**
     *
     * @param array $data list of NodeDataElements
     * @return MyProject_Db_NestedTree_Nodes 
     */
    protected function _getNodes($data) {
        return new MyProject_Db_NestedTree_Nodes($data, $this);
    }
    
    /**
     *
     * @param int|MyProject_Db_NestedTree_Node $id Node-ID
     * @return array Node-Data [assoziativ]
     */
    public function getNodeData($id) {
        if (is_numeric($id)) $nodeId = $id;
        elseif ($id instanceof MyProject_Db_NestedTree_Node) $nodeId = $id->getNodeId();
        
        // Get Node-Data
        /* @var $stmt PDOStatement */
        $sql = 'SELECT node.*, (COUNT(parent.name) - 1) AS level
            FROM ' . $this->_qname . ' AS node, ' . $this->_qname . ' AS parent
            WHERE node.' . $this->_qleft . ' BETWEEN parent.' . $this->_qleft . ' AND parent.' . $this->_qright . '
            AND node.' . $this->_qprimary . ' = ' . (int) $nodeId . '
            GROUP BY node.' . $this->_qprimary;
        
        $stmt = $this->_db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return int max right-value of Tree
     */
    protected function _getMaxRight() 
    {
        /* @var $stmt PDOStatement */
        $sql = "SELECT MAX($this->_qright) maxRight FROM " . $this->_qname . "";
        $stmt = $this->_db->query($sql);
        list($maxRight) = $stmt->fetch(PDO::FETCH_NUM);
        return $maxRight;
    }
    
    /**
     *
     * @param string|array $data Node-Data
     * @param int|MyProject_Db_NestedTree_Node $dstNodeId
     * @param string $pos [prev|next|first|last|parent]
     * @return void
     */
    function insertNode($data, $dstNodeId = null, $pos = ' last') 
    {
        // Ermittel zu schaffenden Freiraum
        if ($dstNodeId) {
            $relData = $this->getNodeData($dstNodeId);
        } else {
            $relData = array($this->_qleft => 1, $this->_qright => $this->_getMaxRight());
        }

        $dstLftStart = $this->_divideLftRgt($relData[$this->_left], $relData[$this->_right], $pos, 2);
        
        if (is_scalar($data)) $data = array($this->_caption => $data);
        
        // Ermittelte Left und Right-Values im Objekt speichern
        $data[$this->_left] = $dstLftStart;
        $data[$this->_right] = ($pos!='parent') ? $dstLftStart + 1 : $relData[$this->_right]+1;

        // Object hinzuf�gen
        $cols = array();
        $vals = array();
        foreach ($data as $k => $v) {
            $cols[] = $this->_db->quoteIdentifier($k);
            $vals[] = $this->_db->quote($v);
        }
        
        $sql = 'INSERT INTO ' . $this->_qname;
        $sql.= '(' . implode(' ,', $cols) . ' ) VALUES (' . implode(' ,', $vals) . ' )';
        //die ('#' . __LINE__ . ' sql: ' . $sql);
        $this->_db->exec($sql);

        return $this->_getNode($this->_db->lastInsertId());
    }
    
    /**
     * Node an Hand der Node-Id aus Tree entfernen
     * @param int|MyProject_Db_NestedTree_Node $id Node
     * @param bool $withChilds Default true
     */
    function removeNode($id, $withChilds = true) {
//        die( 'removeNode(' . print_r(func_get_args(),1).')');
        // Objekt-Daten abrufen
        $data = $this->getNodeData($id);
        $l = $data[$this->_left];
        $r = $data[$this->_right];
        // Objekt l�schen
        $this->_deleteNodeEntry($id, $withChilds);

        // Entstandene L�cke schliessen
        $this->_reduceLftRgt($l, $r, $withChilds);
    }
    
    /**
     *
     * @param int|MyProject_Db_NestedTree_Node $id Node-ID
     * @param string $dstPos [first|last|prev|next]
     * @param int|MyProject_Db_NestedTree_Node $dstNodeId
     * @param bool $withChilds optional Default true
     */
    function moveNode($id, $dstPos, $dstNodeId, $withChilds = true) {
        // Object-Daten abrufen
        $data = $this->getNodeData($id);
        $l = $data[$this->_left];
        $r = $data[$this->_right];
        $w = $r - $l + 1;
        $countChildsByWidth = ($r - $l - 1) / 2;
        $reduceWidth = ($withChilds || !$countChildsByWidth) ? $w : 2;

        // Objekt tempor�r ausserhalb des Baums positionieren
        $maxRight = $this->_moveNodeOutOfTree($id, $withChilds);

        // Entstandene L�cke schliessen
        $this->_reduceLftRgt($l, $r, $withChilds);

        // Daten des Objekts abrufen, zu dem die Positionierung relativ erfolgen soll
        // Get relNode-Data of Destination-Note (As new Parent or as new Sibling)
        $relData = $this->getNodeData($dstNodeId);
//        echo 'move relData: ' . print_r($relData,1) . PHP_EOL;

        // Insert bzw. abschliessender Move-Part
        if ($dstNodeId) {
//            echo '#' . __LINE__ . ' Create Space _divideLftRgt('.$relData[$this->_left].','.$relData[$this->_right].','.$dstPos.','.$reduceWidth.')' . PHP_EOL;
            $dstLftStart = $this->_divideLftRgt($relData[$this->_left], $relData[$this->_right], $dstPos, $reduceWidth);

//            echo '#' . __LINE__ . ' Move Node Into Space id:'.$id.', dstLftStart:'.$dstLftStart.'!' . PHP_EOL;
            $this->_moveNodeIntoSpace($id, $dstLftStart);
        } else {
            throw new MyProject_Db_NestedTree_Exception('Konnte Element nicht verschieben: ' . print_r($data,1).'!');            
// Eigentlich Error, weil undefiniert
            // Tempor�r verschobenes Objekt bleibt nun offiziell am verschobenen Ort
        }
    }
    
    /**
     * 
     * @param string $resultMode options list|count
     * @param int|MyProject_Db_NestedTree_Node $parentId
     * @param int $maxDepth Default 0 => without limit
     * @return int|array count Rows oder array mit Rows
     */
    public function getTreeData($resultMode = 'list', $parentId = null, $maxDepth = 0) {

        if ($parentId) {
            $pData = $this->getNodeData($parentId);
            $where = ' AND node.' . $this->_qleft . ' > ' . $pData[$this->_left]
                    . ' AND node.' . $this->_qright . '< ' . $pData[$this->_right] . ' ';
        } else {
            $where = '';
        }
        $having = ($maxDepth == 1) ? ('HAVING level = ' . ($parentId ? $pData['level']+1 : 0).' ') : '';
        
        $sql =
                'SELECT node.*, (COUNT(parent.' . $this->_qprimary . ' ) - 1) AS "level"'
                . ' FROM ' . $this->_qname . ' AS node , ' . $this->_qname . ' AS parent '
                . ' WHERE node.' . $this->_qleft . ' BETWEEN parent.' . $this->_qleft . ' AND parent.' . $this->_qright . $where
                . ' GROUP BY node.' . $this->_qprimary
                . ($maxDepth ? ' HAVING level < ' . $maxDepth : '')
                . ' ORDER BY node.' . $this->_qleft;
        
        $sql = 'SELECT node.*, ( COUNT( parent.' . $this->_qprimary . ' ) -1 ) AS level '
             . 'FROM ' . $this->_qname . ' AS node, ' . $this->_qname . ' AS parent '
             . 'WHERE node.' . $this->_qleft . ' BETWEEN parent.' . $this->_qleft . ' AND parent.' . $this->_qright . ' '
             . $where
             . 'GROUP BY node.' . $this->_qprimary . ' '
             . $having
             . 'ORDER BY node.' . $this->_qleft;
        
//        if ($parentId) die($sql);
        /** @var $stmt PDOStatement */
        $stmt = $this->_db->query($sql);
        if ($resultMode == 'count') return $stmt->rowcount();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 
     * @param string $term
     * @param string $trunc [both,front,end]
     * @return array Rows Assoc
     */
    public function query($term = '', $trunc = 'both') 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;

        if ($term) {
            $frontTrunc = ($trunc == 'both' || $trunc == 'front') ? '%' : '';
            $endTrunc   = ($trunc == 'both' || $trunc == 'end')   ? '%' : '';
            $where = ' WHERE ' . $this->_qcaption . ' LIKE ' . $db->quote($frontTrunc . $term . $endTrunc );
        } else {
            $where = '';
        }
        
        $sql = 'SELECT * '
             . 'FROM ' . $this->_qname . ' '
             . $where . ' '
             . 'ORDER BY ' . $this->_qcaption . ' '
             . 'LIMIT 20';
        
        /** @var $stmt PDOStatement */
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     *
     * @param type $node
     * @param type $n_lvl
     * @param type $n_lft
     * @param type $n_rgt
     * @return type 
     * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:nested_set_model
     */
    public function getImmediateGridSubs($node = 0, $n_lvl = 0, $n_lft = 0, $n_rgt = 0)
    {
        // detect if here we post the data from allready loaded tree
        // we can make here other checks
        if( $node >0) {

            $n_lvl = $n_lvl+1;
            
            $sql = 'SELECT node.*, (COUNT(parent.name) - 1) AS level
            FROM ' . $this->_qname . ' AS node, ' . $this->_qname . ' AS parent
            WHERE node.' . $this->_qleft . ' BETWEEN parent.' . $this->_qleft . ' AND parent.' . $this->_qright . '
              AND  node.' . $this->_qleft . ' > '.$n_lft . ' AND node.' . $this->_qright . ' < '.$n_rgt . ' 
            GROUP BY node.' . $this->_qprimary . '
            HAVING level = ' . $n_lvl.'
            ORDER BY node.' . $this->_qleft;
        } else { 
             // initial grid
            $sql = 'SELECT node.*, (COUNT(parent.name) - 1) AS level
            FROM ' . $this->_qname . ' AS node, ' . $this->_qname . ' AS parent
            WHERE node.' . $this->_qleft . ' BETWEEN parent.' . $this->_qleft . ' AND parent.' . $this->_qright . '
            GROUP BY node.' . $this->_qprimary . '
            HAVING level = 0
            ORDER BY node.' . $this->_qleft;
        }
        
        $stmt = $this->_db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 
     * @param string $resultMode options list|count
     * @param int|MyProject_Db_NestedTree_Node $parentId
     * @return int|array
     */
    public function getChildrenData($resultMode, $parentId = null) {
        return $this->getTreeData($resultMode, $parentId = null, $maxDepth = 1);
    }
    
    /**
     * Liefert rekursiv iterierbares Collection-Object MyProject_Db_NestedTree_Nodes
     * @param int|MyProject_Db_NestedTree_Node $parentId
     * @return MyProject_Db_NestedTree_Nodes 
     */
    public function getChildren($parentId = null) {
        return $this->_getNodes($this->getChildrenData('list', $parentId));
    }
    
    /**
     * Gibt Array mit direkte Kindelementen zur ParentId zur�ck, Top-Nodes wenn
     * keine ParentId angegeben wurde
     * @param int|MyProject_Db_NestedTree_Node $parentId default null
     * @return array of immediate children 
     */
    public function getChildrenList($parentId = null) {
        return $this->getChildrenData('list', $parentId);
    }
    
    /**
     * Gibt alternativ Anzahl Top-Nodes zur�ck, wenn Aufruf ohne ParentId erfolgt
     * @param int|MyProject_Db_NestedTree_Node $parentId default null
     * @return int count immediate children
     */
    public function getChildrenCount($parentId = null) {
        return $this->getChildrenData('count', $parentId);
    }
    
    /**
     * Gibt alle Vorfahren zur�ck, sofern nicht durch Param $depth begrenzt.
     * Direkte Vorfahren zuerst
     * @param string $resultMode. Options list|count
     * @param int|MyProject_Db_NestedTree_Node $nodeId
     * @param int $depth
     * @return array|int array of elements or int count of parent-elements
     */
    public function getAncestorsData($resultMode, $node, $depth = null) 
    {
        $nodeId = ($node instanceof MyProject_Db_NestedTree_Node) ? $node->getNodeId() : $node;
        $sql =
                'SELECT ' . ($resultMode=='list' ? 'parent.*' : 'count(1)')
                . ' FROM ' . $this->_qname . ' AS node, ' . $this->_qname . ' AS parent '
                . ' WHERE node.' . $this->_qleft . ' BETWEEN parent.' . $this->_qleft . ' AND parent.' . $this->_qright
                . ' AND node.' . $this->_qprimary . ' = ' . $nodeId . ' AND parent.' . $this->_qprimary . ' != ' . $nodeId
                . ' ORDER BY parent.' . $this->_qleft . ' DESC '
                . ( ($resultMode=='list' && $depth) ? ' LIMIT ' . $depth : '');

        /** @var $stmt PDOStatement */
        $stmt = $this->_db->query($sql);
        if ($resultMode=='list') return $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $stmt->fetchColumn();
    }
    
    /**
     * Gibt alle Vorfahren zur�ck, sofern nicht durch Param $depth begrenzt.
     * Direkte Vorfahren zuerst
     * @param int|MyProject_Db_NestedTree_Node $nodeId
     * @param int $depth
     * @return array  
     */
    public function getAncestors($node, $depth = null) 
    {
        return $this->_getNodes($this->getAncestorsData('list', $node, $depth = null));
    }
    
    public function getLevel($node) 
    {
        return ( (int)$this->getAncestorsData('count', $node)+1);
    }
    
    /**
     * @param int|MyProject_Db_NestedTree_Node $nodeId
     * @return array data of parentElement
     */
    public function getParent($nodeId) {
        $re = $this->getAncestors($nodeId, 1);
        return (count($re)) ? $re->current() : null;
    }
    
    /**
     *
     * @param int|MyProject_Db_NestedTree_Node $nodeId or Object 
     * @param bool $includeNode
     * @return MyProject_Db_NestedTree_Nodes
     */
    public function getSiblings($node, $includeNode = true) {
        $nodeId = ($node instanceof MyProject_Db_NestedTree_Node) ? $node->getNodeId() : (int)$node;
        $parent = $this->getParent($nodeId);
        $parentId = (!is_null($parent) ? $parent->getNodeId() : null);
        $siblings = $this->getChildrenList($parentId);
        if (!$includeNode) {
            for($i = 0; $i < count($siblings); ++$i) {
                if ($siblings[$i][$this->_primary] == $nodeId) {
                    $siblings = array_merge(array_slice($siblings,0,$i), array_slice($siblings, $i+1));
                    break;
                }
            }
        }
        return $this->_getNodes($siblings);
        
    }
    
    /**
     * Liefert Geschwisterelement auf gleicher Ebene
     * @param int|MyProject_Db_NestedTree_Node $nodeId
     * @param string $dir Default next. options: prev|next
     * @return null|MyProject_Db_NestedTree_Node data of SiblingElement 
     */
    public function getSibling($nodeId, $dir = 'next') 
    {
        $nodeData = $this->getNodeData($nodeId);
        if ('next' == $dir) {
            $r = (int) $nodeData[$this->_right];
            $sql = 'SELECT * FROM ' . $this->_qname . ' WHERE ' . $this->_qleft . ' = ' . $r . ' +1';
        } else { // prev
            $l = (int) $nodeData[$this->_left];
            $sql = 'SELECT * FROM ' . $this->_qname . ' WHERE ' . $this->_qright . ' = ' . $l . ' -1';
        }

        /** @var $stmt PDOStatement */
        $stmt = $this->_db->query($sql);
        return ($stmt && $stmt->rowCount()) ? $this->_getNode($stmt->fetch(PDO::FETCH_ASSOC)) : NULL;
    }
    
    /**
     * Liefert nachfolgendes Geschwisterelement auf gleicher Ebene
     * @param int|MyProject_Db_NestedTree_Node $nodeId
     * @return null|MyProject_Db_NestedTree_Node data of SiblingElement
     */
    public function getNextSibling($nodeId) 
    {
        return $this->getSibling($nodeId, 'next');
    }
    
    /**
     * Liefert voranstehendes Geschwisterelement auf gleicher Ebene
     * @param int|MyProject_Db_NestedTree_Node $nodeId
     * @return null|MyProject_Db_NestedTree_Node data of SiblingElement
     */
    public function getPreviousSibling($nodeId) 
    {
        return $this->getSibling($nodeId, 'prev');
    }    
    
    /**
     * Liefert Kindelement an angegebener Position
     * @param int|MyProject_Db_NestedTree_Node $parentId
     * @param int|string $childPos. Default first. Options ZeroBased-Index|first|last
     * @return null|MyProject_Db_NestedTree_Node of ChildElement
     */
    public function getChild($parentId, $childPos = 'first') {

        if ($parentId) {
            $pData = $this->getNodeData($parentId);
            $where = ' AND parent.' . $this->_qleft . ' > ' . $pData[$this->_left] . ' AND parent.' . $this->_qright . '< ' . $pData[$this->_right];
        }

        $sortDir = ' ASC';
        if ($childPos == 'first' || $childPos == 'last') {
            $limit = ' LIMIT 0,1';
            if ($childPos == 'last') $sortDir = ' DESC';
        } else {
            $limit = ' LIMIT ' . ((int) $childPos) . ' ,1';
        }

        $sql =
                ' SELECT node.*, (COUNT(parent.' . $this->_qprimary . ' ) - 1) AS "level" '
                . ' FROM ' . $this->_qname . ' AS node , ' . $this->_qname . ' AS parent '
                . ' WHERE node.' . $this->_qleft . ' BETWEEN parent.' . $this->_qleft . ' AND parent.' . $this->_qright . $where
                . ' GROUP BY node.' . $this->_qprimary
                . ' HAVING level = 0 '
                . ' ORDER BY node.' . $this->_qleft . $sortDir;
        $sql.= $limit;

        /** @var $stmt PDOStatement */
        $stmt = $this->_db->query($sql);
        return $this->_getNode($stmt->fetch(PDO::FETCH_ASSOC));
    }
    
    /**
     * Liefert erstes Kindelement
     * @param int|MyProject_Db_NestedTree_Node $parentId
     * @return null|MyProject_Db_NestedTree_Node data of Childelement 
     */
    public function getFirstChild($parentId) {
        return $this->getChild($parentId, 'first');
    }
    
    /**
     * Liefert letztes Kindelement
     * @param int|MyProject_Db_NestedTree_Node $parentId
     * @return null|MyProject_Db_NestedTree_Node data of Childelement 
     */
    public function getLastChild($parentId) {
        return $this->getChild($parentId, 'last');
    }
    
    /**
     * Liefert Treenode an Hand der Node-Id oder einem
     * �bergebenen assoziativen Array mit Tree-Node-Struktur
     * @param id|array $id
     * @return null|NestedTreeNod 
     */
    public function getNodeById($id) {
        $data = $this->getNodeData($id);
        $node = $this->_getNode($data);
        $nodeGetData = $node->getData();
        
        return $this->_getNode($this->getNodeData($id));
    }
    
    
    
    /**
     *
     * @param string|array $data Node-Data
     * @param int|MyProject_Db_NestedTree_Node $dstNodeId
     * @param string $pos [prev|next|first|last|parent]
     * @return void
     */
    function update($data, $id) 
    {
        $sql = 'UPDATE ' . $this->_qname;
        $sql.= ' SET ' . $this->_qcaption . ' = ' . $this->_db->quote($data[$this->_caption]);
        $sql.= ' WHERE ' . $this->_qprimary . ' = ' . $this->_db->quote($id);
        $this->_db->exec($sql);

        return $this->_getNode($this->_db->lastInsertId());
    }
}

