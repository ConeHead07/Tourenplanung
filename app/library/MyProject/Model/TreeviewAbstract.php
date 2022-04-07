<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TreeviewAbstract
 * abstract We use ' . $this->_lftFld . ' and ' . $this->_rgtFld . ' because left and right are reserved 
 * words in MySQL, see http://dev.mysql.com/doc/mysql/en/reserved-words.html 
 * for the full list of reserved words.
 * @author rybka
 */
abstract class MyProject_Model_TreeviewAbstract extends MyProject_Model_Database {
    //put your code here
    
    /** @var $_db Zend_Db_Adapter_Abstract */
    protected $_db;
    protected $_tableName;       //= 'nested_category';
    protected $_idFld;    //= 'category_id';
    protected $_nameFld;     //= 'name';
    protected $_lftFld;      //= 'lft';
    protected $_rgtFld ;     //= 'rgt';
    
    const FIRST_CHILD  = 'firstChild';
    const LAST_CHILD   = 'lastChild';
    const NEXT_SIBLING = 'nextSibling';
    const PREV_SIBLING = 'prevSibling';
    
    /**
     *
     * @param array $fld_config 
     * @throws TreeviewException
     */
    public function __construct(array $fld_config = null)
    {
        if (is_array($fld_config))
        foreach($fld_config as $fldKey => $fldName) {
            if (property_exists($this, '_' . $fldKey))
                $this->{"_$fldKey"} = (string) $fldName;
        }
        
        if (!$this->_tableName)     
            throw new MyProject_Model_TreeviewException("Treeview-Tabelle ist nicht definiert!");
        
        if (!$this->_idFld)  
            throw new MyProject_Model_TreeviewException("Treeview-Id-Feld ist nicht definiert!");
        
        if (!$this->_nameFld)   
            throw new MyProject_Model_TreeviewException("Treeview-Name-Feld ist nicht definiert!");
        
        if (!$this->_lftFld)    
            throw new MyProject_Model_TreeviewException("Treeview-Left-Feld ist nicht definiert!");
        
        if (!$this->_rgtFld)    
            throw new MyProject_Model_TreeviewException("Treeview-Right-Feld ist nicht definiert!");
        
        
        $this->_db = $this->getStorage()->getAdapter();
    }
    
    public function getCatIdFld() {
        return $this->_idFld;
    }

    public function setCatIdFld($_idFld) {
        $this->_idFld = $_idFld;
    }

    public function getNameFld() {
        return $this->_nameFld;
    }

    public function setNameFld($nameFld) {
        $this->_nameFld = $nameFld;
    }

    public function getLftFld() {
        return $this->_lftFld;
    }

    public function setLftFld($lftFld) {
        $this->_lftFld = $lftFld;
    }

    public function getRgtFld() {
        return $this->_rgtFld;
    }

    public function setRgtFld($rgtFld) {
        $this->_rgtFld = $rgtFld;
    }

        
    public function getTable() {
        return $this->_tableName;
    }

    public function setTable($table) {
        $this->_tableName = $table;
    }
    
    /**
     *
     * @return string 
     */
    public function createTable()
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        $keyFld  = $db->quoteIdentifier($this->_idFld);
        $lftFld  = $db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $db->quoteIdentifier($this->_rgtFld);
        $nameFld = $db->quoteIdentifier($this->_nameFld);
        $table   = $db->quoteIdentifier($this->_tableName);
        
        $sql = 
            'CREATE TABLE IF NOT EXISTS ' . $table . ' ( ' . PHP_EOL
            . $keyFld   . ' INT AUTO_INCREMENT PRIMARY KEY,'   . PHP_EOL
            . $nameFld  . ' VARCHAR(40) NOT NULL,'             . PHP_EOL
            . $lftFld   . ' INT NOT NULL,'                     . PHP_EOL
            . $rgtFld   . ' INT NOT NULL'                      . PHP_EOL
            .')';
        
        $db->query($sql);
    }
    
    /**
     *
     * @return string
     */
    public function clearTable()
    {
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        $sql = 'TRUNCATE ' . $table;
        $this->_db->query($sql);
    }
    
    /**
     *
     * @return string 
     */
    public function insertTestData()
    {
        $keyFld  = $this->_db->quoteIdentifier($this->_idFld);
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
        $sql = 
             'INSERT INTO ' . $table         . PHP_EOL
            .'(' 
            . $keyFld . ', ' . $nameFld . ', ' . $lftFld . ' , ' . $rgtFld 
            . ')'                                   . PHP_EOL
            .'VALUES(1,'. $this->_db->quote('ELECTRONICS'). ',1,20)'       . PHP_EOL
            .',(2,'.  $this->_db->quote('TELEVISIONS'). ',2,9)'            . PHP_EOL
            .',(3,'.  $this->_db->quote('TUBE'). ',3,4)'                   . PHP_EOL
            .',(4,'.  $this->_db->quote('LCD'). ',5,6)'                    . PHP_EOL
            .',(5,'.  $this->_db->quote('PLASMA'). ',7,8)'                 . PHP_EOL
            .',(6,'.  $this->_db->quote('PORTABLE ELECTRONICS'). ',10,19)' . PHP_EOL
            .',(7,'.  $this->_db->quote('MP3 PLAYERS'). ',11,14)'          . PHP_EOL
            .',(8,'.  $this->_db->quote('FLASH'). ',12,13)'                . PHP_EOL
            .',(9,'.  $this->_db->quote('CD PLAYERS'). ',15,16)'           . PHP_EOL
            .',(10,'. $this->_db->quote('2 WAY RADIOS'). ',17,18)';
        
        $this->_db->query($sql);
    }
    
    
    /**
     * Get width of a node
     *
     * @param $elementId|int    Id of the node
     *
     * @return int
     */
    private function _getNodeWidth($elementId)
    {
        $db = $this->_db;
        
        $keyFld  = $db->quoteIdentifier($this->_idFld);
        $lftFld  = $db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $db->quoteIdentifier($this->_rgtFld);
        $nameFld = $db->quoteIdentifier($this->_nameFld);
        $table   = $db->quoteIdentifier($this->_tableName);
        

        $stmt = $db->query(
             'SELECT ' . $rgtFld .'  - ' . $lftFld . ' + 1 '
            .'  FROM ' . $table . ' '
            .' WHERE ' . $keyFld . ' = ' . $db->quote($elementId)
        );
        $width = $stmt->fetchColumn();

        return $width;
    }
    
    /**
     * Retrieving a Single Path
     * @abstract 
     * With the nested set model, we can retrieve a single path 
     * without having multiple self-joins:
     * @param string $nodeName
     * @return array
     */
    public function getNodePath($nodeName) 
    {
        $keyFld  = $this->_db->quoteIdentifier($this->_idFld);
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
        $sql = 
             'SELECT parent.' . $nameFld                 . PHP_EOL
            .'FROM ' . $table . ' AS node, '             . PHP_EOL
            .$table . ' AS parent'                       . PHP_EOL
            .'WHERE node.' . $lftFld . ' '
            .' BETWEEN parent.' . $lftFld 
            .' AND parent.' . $rgtFld . ' '              . PHP_EOL
            .'AND node.' . $nameFld . ' = \''. $nodeName . '\' '    . PHP_EOL
            .'ORDER BY parent.' . $lftFld;
        $this->_db->fetchAll($sql);
    }
    
    /**
     * Finding the Depth of the Nodes
     * @abstract
     * We have already looked at how to show the entire tree, 
     * but what if we want to also show the depth of each node in the tree, 
     * to better identify how each node fits in the hierarchy? 
     * This can be done by adding a COUNT function and a GROUP BY clause 
     * to our existing query for showing the entire tree:
     * @return string
     */
    public function getNodesDepth()
    {
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
        $select = 
            'SELECT node.' . $nameFld . ', '
           .'(COUNT(parent.' . $nameFld . ') - 1) AS depth'        . PHP_EOL
           .'FROM ' . $tableName . ' AS node,'                     . PHP_EOL
           . $tableName . ' AS parent'                             . PHP_EOL
           .'WHERE node.' . $lftFld . ' BETWEEN parent.' . $lftFld . PHP_EOL
           .' AND parent.' . $rgtFld                               . PHP_EOL
           .'GROUP BY node.' . $nameFld                            . PHP_EOL
           .'ORDER BY node.' . $lftFld;
        
         return $this->_db->fetchAll($select);
    }
    
    /**
     * 
     * @return array 
     */
    public function fetchTree()
    {
        $keyFld  = $this->_db->quoteIdentifier($this->_idFld);
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
                          
        $select = 'SELECT node.' . $nameFld . ' AS name, '
                 .'node.' . $lftFld . ', '
                 .'(COUNT(parent.' . $nameFld . ')-1) depth, '
                 .'node.' . $rgtFld . ', ' . PHP_EOL
                 .'node.' . $keyFld  . PHP_EOL
                 .'FROM ' . $table . ' node, ' . PHP_EOL
                 .$table . ' parent'           . PHP_EOL
                 .' WHERE node.' . $lftFld
                 .' BETWEEN parent.' . $lftFld .' AND parent.' . $rgtFld . PHP_EOL
                 .' GROUP BY node.' . $keyFld . PHP_EOL
                 .' ORDER BY node.' . $lftFld;
//         echo $select;
         return $this->_db->fetchAll($select);        
    }
    
    /**
     * @abstract
     * We can use the depth value to indent our category names with the 
     * CONCAT and REPEAT string functions:
     * @return array
     */
    public function getNodesIndented()
    {
        $keyFld  = $this->_db->quoteIdentifier($this->_idFld);
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
        $select = 'SELECT CONCAT( REPEAT(\'-\', COUNT(parent.' . $nameFld . ') - 1), node.' . $nameFld . ') AS name, node.' . $keyFld . ', ' . PHP_EOL
                 .'COUNT(parent.' . $nameFld . ') AS depth, node.' . $lftFld         . PHP_EOL
                 .'FROM ' . $table . ' node, ' . $table . ' parent' . PHP_EOL
                 .'WHERE node.' . $lftFld . ' BETWEEN parent.' . $lftFld .' AND parent.' . $rgtFld . PHP_EOL
                 .'GROUP BY node.' . $nameFld . PHP_EOL
                 .'ORDER BY node.' . $lftFld;
//        echo $select;
        
        return $this->_db->fetchAll($select); 
    }
    
    /**
     * Get all nodes without children
     *
     * @return array
     */
    public function getLeafs()
    {
        $db = $this->_db;
        
        $lftFld  = $db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $db->quoteIdentifier($this->_rgtFld);
        
        $select = $db
            ->select()
            ->from($this->_tableName, array($this->_idFld, $this->_nameFld))
            ->where("{$rgtFld} = {$lftFld} + 1");
            
        $stmt   = $db->query($select);
        return $stmt->fetchAll();
    }
    
    
    /**
     * Get the parent of an element.
     *
     * @param int $elementId    Element ID
     * @param int $depth        Depth of the parent, compared to the child.
     *                          Default is 1 (as immediate)
     *
     * @return array|false
     */
    public function getParent($elementId, $depth = 1)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        $lftFld  = $db->quoteIdentifier($this->_lftFld);

        $select = $db
            ->select()
            ->from($this->_tableName, array($this->_lftFld, $this->_rgtFld))
            ->where($this->_idFld . ' = ?', $elementId);

        /* @var $stmt Zend_Db_Statement */
        $stmt  = $db->query($select);
        if (!$stmt->rowCount()) return false;
        
        $child = $stmt->fetch();
        
        /* @var $select Zend_Db_Select */
        $select = $db
            ->select()
            ->from($this->_tableName, array($this->_idFld, $this->_nameFld))
            ->where($this->_lftFld . ' < ?', $child[$this->_lftFld])
            ->where($this->_rgtFld . ' > ?', $child[$this->_rgtFld])
            ->order('(' . $child[$this->_lftFld] . ' - ' . $lftFld . ')')
            ->limit($depth, 1);
        
//        echo $select->assemble();
        
        $stmt   = $db->query($select);
        $result = $stmt->fetch();

        return $result;
    }
    
    /**Depth of a Sub-Tree
     * @abstract
     * When we need depth information for a sub-tree, we cannot limit either 
     * the node or parent tables in our self-join because it will corrupt our 
     * results. Instead, we add a third self-join, along with a sub-query to 
     * determine the depth that will be the new starting point for our sub-tree:
     * @param string $nodeName
     * @return array
     */
    public function getDepthOfSubTree($nodeName)
    {
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
        $select = 'SELECT node.' . $nameFld . ', (COUNT(parent.' . $nameFld . ') - (sub_tree.depth + 1)) AS depth ' . PHP_EOL
            .'FROM ' . $table . ' AS node, '             . PHP_EOL
            .'	nested_category AS parent, '             . PHP_EOL
            .'	nested_category AS sub_parent, '         . PHP_EOL
            .'	( ' . PHP_EOL
            .'		SELECT node.' . $nameFld . ', (COUNT(parent.' . $nameFld . ') - 1) AS depth ' . PHP_EOL
            .'		FROM ' . $table . ' AS node, '   . PHP_EOL
            .'		nested_category AS parent '      . PHP_EOL
            .'		WHERE node.' . $lftFld . ' BETWEEN parent.' . $lftFld . ' AND parent.' . $rgtFld . ' ' . PHP_EOL
            .'		AND node.' . $nameFld . ' = \'' . $nodeName . '\' ' . PHP_EOL
            .'		GROUP BY node.' . $nameFld . ' ' . PHP_EOL
            .'		ORDER BY node.' . $lftFld . ' '  . PHP_EOL
            .'	)AS sub_tree ' . PHP_EOL
            .'WHERE node.' . $lftFld . ' BETWEEN parent.' . $lftFld . ' AND parent.' . $rgtFld . ' ' . PHP_EOL
            .'	AND node.' . $lftFld . ' BETWEEN sub_parent.' . $lftFld . ' AND sub_parent.' . $rgtFld . ' ' . PHP_EOL
            .'	AND sub_parent.' . $nameFld . ' = sub_tree.' . $nameFld . ' ' . PHP_EOL
            .'GROUP BY node.' . $nameFld . ' ' . PHP_EOL
            .'ORDER BY node.' . $lftFld;
         
//        echo '#' . __LINE__ . ' select: ' . $select . PHP_EOL;
        return $this->_db->fetchAll($select);
    }
    
    /**
     * Find the Immediate Subordinates of a Node
     * Imagine you are showing a category of electronics products on a retailer 
     * web site. When a user clicks on a category, you would want to show the 
     * products of that category, as well as list its immediate sub-categories, 
     * but not the entire tree of categories beneath it. For this, we need to 
     * show the node and its immediate sub-nodes, but no further down the tree. 
     * For example, when showing the PORTABLE ELECTRONICS category, we will 
     * want to show MP3 PLAYERS, CD PLAYERS, and 2 WAY RADIOS, but not FLASH.
     * This can be easily accomplished by adding a HAVING clause to our 
     * previous query:
     * @param string $nodeName
     * @return array
     */
    public function getImmediateSubsOfNode($nodeName)
    {
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
        $select =
            'SELECT node.' . $nameFld . ', (COUNT(parent.' . $nameFld . ') - (sub_tree.depth + 1)) AS depth' . PHP_EOL
            .'FROM ' . $table . ' AS node,' . PHP_EOL
            .'	nested_category AS parent,' . PHP_EOL
            .'	nested_category AS sub_parent,' . PHP_EOL
            .'	(' . PHP_EOL
            .'		SELECT node.' . $nameFld . ', (COUNT(parent.' . $nameFld . ') - 1) AS depth' . PHP_EOL
            .'		FROM ' . $table . ' AS node,' . PHP_EOL
            .'		nested_category AS parent' . PHP_EOL
            .'		WHERE node.' . $lftFld . ' BETWEEN parent.' . $lftFld . ' AND parent.' . $rgtFld . PHP_EOL
            .'		AND node.' . $nameFld . ' = \'' . $nodeName . '\'' . PHP_EOL
            .'		GROUP BY node.' . $nameFld . '' . PHP_EOL
            .'		ORDER BY node.' . $lftFld . PHP_EOL
            .'	)AS sub_tree' . PHP_EOL
            .'WHERE node.' . $lftFld . ' BETWEEN parent.' . $lftFld . ' AND parent.' . $rgtFld . PHP_EOL
            .'	AND node.' . $lftFld . ' BETWEEN sub_parent.' . $lftFld . ' AND sub_parent.' . $rgtFld . PHP_EOL
            .'	AND sub_parent.' . $nameFld . ' = sub_tree.' . $nameFld . '' . PHP_EOL
            .'GROUP BY node.' . $nameFld . '' . PHP_EOL
            .'HAVING depth <= 1' . PHP_EOL
            .'ORDER BY node.' . $lftFld;
         
         return $this->_db->fetchAll($select);
    }

    
    /**
     * Returns the number of descendant (all ChildNodes) of an element.
     *
     * @params $elementId|int   ID of the element
     *
     * @return int
     */
    public function numberOfDescendant($elementId)
    {
        $width = $this->_getNodeWidth($elementId);
        $result = ($width - 2) / 2;

        return $result;
    }

    
    /**
     * Adding New Nodes
     * @abstract
     * Now that we have learned how to query our tree, we should take a look 
     * at how to update our tree by adding a new node. Let's look at our 
     * nested set diagram again:
     * 
     * If we wanted to add a new node between 
     * the TELEVISIONS and PORTABLE ELECTRONICS nodes, 
     * the new node would have ' . $this->_lftFld . ' and ' . $this->_rgtFld . ' values of 10 and 11, 
     * and all nodes to its right would have their ' . $this->_lftFld . ' and ' . $this->_rgtFld . ' values 
     * increased by two. We would then add the new node with the appropriate 
     * ' . $this->_lftFld . ' and ' . $this->_rgtFld . ' values. While this can be done with a stored procedure 
     * in MySQL 5, I will assume for the moment that most readers are 
     * using 4.1, as it is the latest stable version, and I will isolate 
     * my queries with a LOCK TABLES statement instead:
     * @param string $newNodeName
     * @param string $neighbourNode
     * @return array
     */
    public function addNode($newNodeName, $neighbourNode)
    {
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
                
        $this->_db->beginTransaction();
        try 
        {  
            $sql = 'SELECT ' . $rgtFld . ' FROM ' . $table . ' WHERE ' . $nameFld . ' = ' . $this->_db->quote($neighbourNode);
            $myRight = (int)$this->_db->fetchOne($sql);

            $sql = 'UPDATE ' . $table . ' SET ' . $rgtFld . ' = (' . $rgtFld . ' + 2) WHERE ' . $rgtFld . ' > ' . $myRight;
            $this->_db->query($sql);
            
            $sql = 'UPDATE ' . $table . ' SET ' . $lftFld . ' = (' . $lftFld . ' + 2) WHERE ' . $lftFld . ' > ' . $myRight;
            $this->_db->query($sql);

            $sql = 'INSERT INTO nested_category(name, ' . $lftFld .', ' . $rgtFld . ') ' . PHP_EOL
                     .'VALUES(\'' . $newNodeName . '\', (' . $myRight . ' + 1), (' . $myRight . ' + 2) )';
            $this->_db->query($sql);
        
            $this->_db->commit();
            return true;
        }
        catch(Exception $e)
        {
            $this->_db->rollBack();
            echo $e->getMessage();
        }
        return false;
    }
    
    /** 
     * @abstract
     * If we instead want to add a node as a child of a node that has no 
     * existing children, we need to modify our procedure slightly. 
     * Let's add a new FRS node below the 2 WAY RADIOS node:
     * @return array
     */
    public function addChild($newNodeName, $parentNodeName)
    {        
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
//        SELECT @myLeft := lft FROM nested_category WHERE name = '2 WAY RADIOS';
//        UPDATE nested_category SET rgt = rgt + 2 WHERE rgt > @myLeft;
//        UPDATE nested_category SET lft = lft + 2 WHERE lft > @myLeft;
//        INSERT INTO nested_category(name, lft, rgt) VALUES('FRS', @myLeft + 1, @myLeft + 2);

        
        $this->_db->beginTransaction();
        try {
            $sql = 'SELECT ' . $lftFld . ' FROM ' . $table . ' WHERE ' . $nameFld . ' = ' . $this->_db->quote($parentNodeName);
            $myLeft = (int) $this->_db->fetchOne($sql);
//            echo '#' . __LINE__ . ' sql: ' . $sql . '; myLeft: ' . $myLeft . "<br>\n";

            $sql = 'UPDATE ' . $table . ' SET ' . $rgtFld . ' = ' . $rgtFld . ' + 2 WHERE ' . $rgtFld . ' > ' . $myLeft;
//            echo '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $this->_db->query($sql);
            
            $sql = 'UPDATE ' . $table . ' SET ' . $lftFld . ' = ' . $lftFld . ' + 2 WHERE ' . $lftFld . ' > ' . $myLeft;
//            echo '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $this->_db->query($sql);
            

            $sql = 'INSERT INTO ' . $table . '(name, ' . $lftFld . ', ' . $rgtFld . ') '
                  .'VALUES(\'' . $newNodeName. '\', (' . $myLeft . ' + 1), (' . $myLeft . ' + 2) )';
//            echo '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $this->_db->query($sql);
            
            $this->_db->commit();
            return true;

        } catch (Exception $e) {
            // If any of the queries failed and threw an exception,
            // we want to roll back the whole transaction, reversing
            // changes made in the transaction, even those that succeeded.
            // Thus all changes are committed together, or none are.
            $this->_db->rollBack();
            echo $e->getMessage();
        }

        return false;
    }
    
    
    /**
     * Method for adding new node.
     *
     * @param array $data
     * @param int|null $objectiveNodeId
     * @param string $position Position regarding on objective node.
     * @return int The number of affected rows.
     */
    public function insertNodeByPosition($data, $objectiveNodeId = null, $position = self::LAST_CHILD)
    {
        if (!$this->checkNodePosition($position)) {
            throw new Exception('Invalid node position is supplied.');
        }
		
        echo '#' . __LINE__ . PHP_EOL;
        $data = array_merge($data, $this->_getLftRgt($objectiveNodeId, $position));
        echo '#' . __LINE__ . PHP_EOL;
        
        $cols = array_keys($data);
        $vals = array();
        foreach ($cols as $i=>$col) {
            $cols[$i] = $this->_db->quoteIdentifier($col);
            $vals[] = '?';
        }

        $sql = 'INSERT INTO '
             . $this->_db->quoteIdentifier($this->_tableName)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';
        echo '#' . __LINE__ . ' sql: ' . $sql . PHP_EOL;
        $stmt = $this->_db->prepare($sql);
        $stmt->execute(array_values($data));

        return $stmt->rowCount();
    }
	
    /**
     * Updates info of some node.
     *
     * @param array $data
     * @param int $id Id of a node that is being updated.
     * @param int|null $objectiveNodeId
     * @param string $position Position regarding on objective node.
     * @return int The number of affected rows.
     */
    public function updateNodeByPosition($data, $id, $objectiveNodeId, $position = self::LAST_CHILD)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        $id = (int)$id;
        $objectiveNodeId = (int)$objectiveNodeId;

        if (!$this->checkNodePosition($position)) {
            throw new Exception('Invalid node position is supplied.');
        }

        //Only if the objective id differs.
        if ($objectiveNodeId != $this->_getCurrentObjectiveId($id, $position)) { 
            $this->_reduceWidth($id);
            $data = array_merge($data, $this->_getLftRgt($objectiveNodeId, $position, $id));
        }

        $set = array();
        foreach ($data as $col=>$val) {
            $set[] = $db->quoteIdentifier($col) . ' = ' . $db->quote($val);
        }

        $retval = 0;
        if (!empty($set)) { //Has some data to update?
            $table   = $db->quoteIdentifier($this->_tableName);
            $nameFld = $db->quoteIdentifier($this->_nameFld);
            $keyFld  = $db->quoteIdentifier($this->_idFld);
            
            $stmt = $db->query(
                'UPDATE ' . $table 
               .'SET ' . implode(', ', $set) . ' '
               .'WHERE ' . $keyFld . ' = ' . $db->quote($id, Zend_Db::PARAM_INT));
            
            $retval = $stmt->rowCount();
        }

        return $retval;
    }
    
    /**
     * Checks whether valid node position is supplied.
     *
     * @param string $position Position regarding on objective node.
     * @return bool
     */
    public function checkNodePosition($position)
    {
        switch($position) 
        {
            case self::FIRST_CHILD:
            case self::LAST_CHILD:
            case self::NEXT_SIBLING:
            case self::PREV_SIBLING:
                return true;
            
            default:
                return false;
        }
    }
	
	/**
	 * Reduces lft and rgt values of some nodes, on which some 
	 * node that is changing position in tree, or being deleted, 
	 * has effect.
	 *
	 * @param int $id Id of a node.
	 * @return void
	 */
    protected function _reduceWidth($id)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        $table  = $db->quoteIdentifier($this->_tableName);
        $keyFld = $db->quoteIdentifier($this->_idFld);
        $lftFld = $db->quoteIdentifier($this->_lftFld);
        $rgtFld = $db->quoteIdentifier($this->_rgtFld);

        $sql = "SELECT $lftFld, $rgtFld, ($rgtFld - $lftFld + 1) AS myWidth FROM $table WHERE $keyFld = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $id, Zend_Db::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) { //Only if supplied node exists.
            $result = $stmt->fetch(Zend_Db::FETCH_ASSOC);

            $left  = $result[$this->_lftFld];
            $right = $result[$this->_rgtFld];
            $width = $result['myWidth'];

            if ((int)$width > 2) { //Some node that has childs.
                //Updating children.
                $sql = "UPDATE $table SET $rgtFld = $rgtFld - 1, $lftFld = $lftFld - 1 WHERE $lftFld BETWEEN $left AND $right";
                $db->query($sql);
            }

            //Updating parent nodes and nodes on next levels.
            $sql = "UPDATE $table SET $lftFld = $lftFld - 2 WHERE $lftFld > $left AND $rgtFld > $right";
            $db->query($sql);

            $sql = "UPDATE $table SET $rgtFld = $rgtFld - 2 WHERE $rgtFld > $right";
            $db->query($sql);
        }
    }

	/**
	 * Gets id of some node's current objective node.
	 *
	 * @param mixed Node id.
	 * @param string Position in tree.
	 * @return int|null
	 */
    protected function _getCurrentObjectiveId($nodeId, $position)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        $sql = '';
        
        $nodeId = $db->quote($nodeId, Zend_Db::PARAM_INT);
        $table  = $db->quoteIdentifier($this->_tableName);
        $keyFld = $db->quoteIdentifier($this->_idFld);
        $lftFld = $db->quoteIdentifier($this->_lftFld);
        $rgtFld = $db->quoteIdentifier($this->_rgtFld);

        switch ($position) {
            case self::FIRST_CHILD :
                $sql = "SELECT node.$keyFld
                FROM $table node, (SELECT $lftFld, $rgtFld FROM $table WHERE $keyFld = $nodeId) AS current
                WHERE current.$lftFld BETWEEN node.$lftFld +1 AND node.$rgtFld AND current.$lftFld - node.$lftFld = 1
                ORDER BY node.$lftFld DESC";

                break;
            case self::LAST_CHILD :
                $sql = "SELECT node.$keyFld
                FROM $table node, (SELECT $lftFld, $rgtFld FROM $table WHERE $keyFld = $nodeId) AS current
                WHERE current.$lftFld BETWEEN node.$lftFld+1 AND node.$rgtFld AND node.$rgtFld - current.$rgtFld = 1
                ORDER BY node.$lftFld DESC";

                break;
            case self::NEXT_SIBLING :
                $sql = "SELECT node.$keyFld
                FROM $table node, (SELECT $lftFld FROM $table WHERE $keyFld = $nodeId) AS current
                WHERE current.$lftFld - node.$rgtFld = 1";

                break;
            case self::PREV_SIBLING :
                $sql = "SELECT node.$keyFld
                FROM $table node, (SELECT $rgtFld FROM $table WHERE $keyFld = $nodeId) AS current
                WHERE node.$lftFld - current.$rgtFld = 1";

                break;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(Zend_Db::FETCH_ASSOC);
            
            return (int)$result[$this->_idFld];
        }
        else {
            return null;
        }
    }
    
    public function deleteNodeByName($nodeName, $withChilds = true)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        $table   = $db->quoteIdentifier($this->_tableName);
        $keyFld  = $db->quoteIdentifier($this->_idFld);
        $nameFld = $db->quoteIdentifier($this->_nameFld);
        
        $sql = 
            'SELECT ' . $keyFld . ' AS id '
           .'FROM ' . $table . ' '
           .'WHERE ' . $nameFld . ' = ' . $db->quote($nodeName);
        
        $nodeId = $db->fetchOne($sql);
        
        return $this->deleteNode($nodeId, $withChilds);
    }
    /**
     * Deleting Nodes
     * @abstract
     * The last basic task involved in working with nested sets is the removal 
     * of nodes. The course of action you take when deleting a node depends on 
     * the node's position in the hierarchy; deleting leaf nodes is easier than 
     * deleting nodes with children because we have to handle the orphaned nodes.
     * When deleting a leaf node, the process if just the opposite of adding 
     * a new node, we delete the node and its width from every node to its right:
     * @param string $nodeName
     * @param bool $withChilds default true
     * @return array
     */
    public function deleteNode($nodeId, $withChilds = true)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        if ($withChilds === false) {
            return $this->deleteNodeWithoutChilds($nodeId);
        }
        
        $keyFld  = $db->quoteIdentifier($this->_idFld);
        $lftFld  = $db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $db->quoteIdentifier($this->_rgtFld);
        $nameFld = $db->quoteIdentifier($this->_nameFld);
        $table   = $db->quoteIdentifier($this->_tableName);
        
        $db->beginTransaction();
        try {
            $sql = 'SELECT ' . $lftFld . ' myLeft, ' . $rgtFld . ' myRight, '
                  .'(' . $rgtFld . ' - ' . $lftFld . ' + 1) myWidth ' . PHP_EOL
                  .'FROM ' . $table . ' ' . PHP_EOL
                  .'WHERE ' . $keyFld . ' = ' . $db->quote($nodeId) . ' ';
            $row = $db->fetchRow($sql);
            echo Zend_Debug::dump($row);
            $myLeft  = (int) $row['myLeft'];
            $myRight = (int) $row['myRight'];
            $myWidth = (int) $row['myWidth'];

            $sql = 'DELETE FROM ' . $table . ' WHERE ' . $lftFld . ' BETWEEN ' . $myLeft . ' AND ' . $myRight;
            $db->query($sql);

            $sql = 'UPDATE ' . $table . ' SET ' . $rgtFld . ' = (' . $rgtFld . ' - ' .$myWidth . ') WHERE ' . $rgtFld . ' > ' . $myRight;
            $db->query($sql);
            
            $sql = 'UPDATE ' . $table . ' SET ' . $lftFld . ' = (' . $lftFld . ' - ' . $myWidth . ') WHERE ' . $lftFld . ' > ' . $myRight;
            $db->query($sql);
            
            $db->commit();
            return true;
            
        } catch (Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }
        return false;
    }
    
    /**
     * @abstract
     * The other scenario we have to deal with is the deletion of a parent 
     * node but not the children. In some cases you may wish to just change 
     * the name to a placeholder until a replacement is presented, such as 
     * when a supervisor is fired. In other cases, the child nodes should 
     * all be moved up to the level of the deleted parent:
     * @param string $nodeName
     * @return array
     */
    public function deleteNodeWithoutChilds($nodeId)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        $keyFld  = $db->quoteIdentifier($this->_idFld);
        $lftFld  = $db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $db->quoteIdentifier($this->_rgtFld);
        $nameFld = $db->quoteIdentifier($this->_nameFld);
        $table   = $db->quoteIdentifier($this->_tableName);
        
        $db->beginTransaction();
        try {
            $sql = 'SELECT ' . $lftFld . ' myLeft, ' . $rgtFld . ' myRight, '
                  .'(' . $rgtFld . ' - ' . $lftFld . ' + 1) myWidth ' . PHP_EOL
                  .'FROM ' . $table . ' ' . PHP_EOL
                  .'WHERE ' . $keyFld . ' = ' . $db->quote($nodeId) . ' ';
            $row = $db->fetchRow($sql);
            $myLeft  = (int) $row['myLeft'];
            $myRight = (int) $row['myRight'];
            $myWidth = (int) $row['myWidth'];

            $sql = 'DELETE FROM ' . $table . ' WHERE ' . $lftFld . ' = ' . $myLeft;
            $db->query($sql);

            $sql = 'UPDATE ' . $table  . ' SET ' 
                     .$rgtFld . ' = ' . $rgtFld . ' - 1, ' 
                     .$lftFld . ' = ' . $lftFld . ' - 1 ' . PHP_EOL
                     .'WHERE '  . $lftFld . ' BETWEEN ' . $myLeft . ' AND ' . $myRight;
            $db->query($sql);
            
            $sql = 'UPDATE ' . $table  . ' SET ' . $rgtFld . ' = ' . $rgtFld . ' - 2 WHERE ' . $rgtFld . ' > ' . $myRight;
            $db->query($sql);
            
            $sql = 'UPDATE ' . $table  . ' SET ' . $lftFld . ' = ' . $lftFld . ' - 2 WHERE ' . $lftFld . ' > ' . $myRight;
            $db->query($sql);
            
            $db->commit();
            return true;

        } catch (Exception $e) {
            // If any of the queries failed and threw an exception,
            // we want to roll back the whole transaction, reversing
            // changes made in the transaction, even those that succeeded.
            // Thus all changes are committed together, or none are.
            $db->rollBack();
            echo $e->getMessage();
        }
        return false;

    }
    
    
    /**
     * @param $elementId|int    Id of the element to move
     * @param $referenceId|int  Id of the reference element     *
     * return $this
     */
    public function moveTreeInto($elementId, $referenceId)
    {
        $db = $this->_db;

        $reference = $this->_getElement($referenceId);
        $element   = $this->_getElement($elementId); // @TODO get one level, we don't need all this tree

        // error handling
        if (empty($element) || empty($reference)) {
            return false;
        }
        
        $log = '';
        
        try {
            // Case INTO

            // Check it can be moved into. XXX change when we'll get one level
            if ($element[0][$this->_lftFld] > $reference[0][$this->_lftFld] &&
                $element[0][$this->_lftFld] < $reference[0][$this->_rgtFld]) {
                // already into
                return false;
            }

            $db->beginTransaction();
            // first make room into reference
            // @TODO make a private method to make room
            // with must always be a pair number
            $elementWidth = $this->_getNodeWidth($elementId);

            // move right
            $referenceRight = $reference[0][$this->_rgtFld];
            $sql = 
                "UPDATE {$this->_tableName}
                    SET {$this->_rgtFld} = {$this->_rgtFld} + $elementWidth
                  WHERE {$this->_rgtFld} >= $referenceRight ";
                 
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);
            
            // move left
            $sql = 
                "UPDATE {$this->_tableName}
                    SET {$this->_lftFld} = {$this->_lftFld} + $elementWidth
                  WHERE {$this->_lftFld} > $referenceRight ";
            
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);

            // then move element (and it's children)
            $element    = $this->_getElement($elementId);
            $elementIds = array();
            foreach ($element as $one) {
                array_push($elementIds, $one[$this->_idFld]);
            }
            $elementIds = implode(', ', $elementIds);

            $difference = $reference[0][$this->_rgtFld] - $element[0][$this->_lftFld];
            
            // Berechnen der Left-Right Werte der zu verschiebenden Tree-Nodes
            $sql = 
                 "UPDATE {$this->_tableName}
                    SET {$this->_lftFld}  = {$this->_lftFld}  + $difference,
                        {$this->_rgtFld} = {$this->_rgtFld} + $difference
                  WHERE {$this->_idFld} IN ($elementIds) ";
                  
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);
            
            $sql = 
                 "UPDATE {$this->_tableName}
                     SET {$this->_lftFld} = {$this->_lftFld} - $elementWidth
                   WHERE {$this->_lftFld} > {$element[0][$this->_lftFld]} ";
            
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            // move what was on the right of the element
            $stmt = $db->query($sql);
            
            $sql = 
                 "UPDATE {$this->_tableName}
                    SET {$this->_rgtFld} = {$this->_rgtFld} - $elementWidth
                  WHERE {$this->_rgtFld} > {$element[0][$this->_rgtFld]} ";
                  
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);

            $db->commit();
        }
        catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e->getMessage().$log);
        }

        return true;
    }
    
    /**
     * @param $elementId|int    Id of the element to move
     * @param $referenceId|int  Id of the reference element     *
     * return $this
     */
    public function moveTreeBeforeTo($elementId, $referenceId)
    {
        $db = $this->_db;

        $reference = $this->_getElement($referenceId);
        $element   = $this->_getElement($elementId); // @TODO get one level, we don't need all this tree

        // error handling
        if (empty($element) || empty($reference)) {
            return false;
        }
        
        $log = '';
        
        try {
            // Case INTO

            // Check it can be moved into. XXX change when we'll get one level
            if ($element[0][$this->_lftFld] > $reference[0][$this->_lftFld] &&
                $element[0][$this->_lftFld] < $reference[0][$this->_rgtFld]) {
                // already into
                return false;
            }

            $db->beginTransaction();
            // first make room into reference
            // @TODO make a private method to make room
            // with must always be a pair number
            $elementWidth = $this->_getNodeWidth($elementId);

            // move right
            $referenceRight = $reference[0][$this->_rgtFld];
            $sql = 
                "UPDATE {$this->_tableName}
                    SET {$this->_rgtFld} = {$this->_rgtFld} + $elementWidth
                  WHERE {$this->_rgtFld} >= $referenceRight ";
                 
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);
            
            // move left
            $referenceLeft = $reference[0][$this->_lftFld];
            $sql = 
                "UPDATE {$this->_tableName}
                    SET {$this->_lftFld} = {$this->_lftFld} + $elementWidth
                  WHERE {$this->_lftFld} >= $referenceLeft ";
            
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);

            // then move element (and it's children)
            $element    = $this->_getElement($elementId);
            $elementIds = array();
            foreach ($element as $one) {
                array_push($elementIds, $one[$this->_idFld]);
            }
            $elementIds = implode(', ', $elementIds);

            $difference = $reference[0][$this->_lftFld] - $element[0][$this->_lftFld];
            
            // Berechnen der Left-Right Werte der zu verschiebenden Tree-Nodes
            $sql = 
                 "UPDATE {$this->_tableName}
                    SET {$this->_lftFld}  = {$this->_lftFld}  + $difference,
                        {$this->_rgtFld} = {$this->_rgtFld} + $difference
                  WHERE {$this->_idFld} IN ($elementIds) ";
                  
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);
            
            $sql = 
                 "UPDATE {$this->_tableName}
                     SET {$this->_lftFld} = {$this->_lftFld} - $elementWidth
                   WHERE {$this->_lftFld} > {$element[0][$this->_lftFld]} ";
            
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            // move what was on the right of the element
            $stmt = $db->query($sql);
            
            $sql = 
                 "UPDATE {$this->_tableName}
                    SET {$this->_rgtFld} = {$this->_rgtFld} - $elementWidth
                  WHERE {$this->_rgtFld} > {$element[0][$this->_rgtFld]} ";
                  
            $log.= '#' . __LINE__ . ' sql: ' . $sql . "<br>\n";
            $stmt = $db->query($sql);

            $db->commit();
        }
        catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e->getMessage().$log);
        }

        return true;
    }
    
    
    public function createDummyCategorizedData($dataTable, $dataTableKey, $categoryKey)
    {
        $dataTable      = $this->_db->quoteIdentifier($dataTable);
        $dataTableKey   = $this->_db->quoteIdentifier($dataTableKey);
        $categoryKey    = $this->_db->quoteIdentifier($categoryKey);
        
        $sql = 
             'CREATE TABLE IF NOT EXISTS ' . $dataTable .'( '
            .$dataTableKey . ' INT AUTO_INCREMENT PRIMARY KEY, '
            .'name VARCHAR(40), '
            .$categoryKey . ' INT NOT NULL '
            .')';
        $this->_db->query($sql);

        $sql =
             'INSERT INTO product(name, ' . $categoryKey . ') '
            .'VALUES('. $this->_db->qutoe('20" TV') . ',3),     ('. $this->_db->qutoe('36" TV') . ',3), '
            .'('. $this->_db->qutoe('Super-LCD 42"') . ',4),    ('. $this->_db->qutoe('Ultra-Plasma 62"') . ',5),'
            .'('. $this->_db->qutoe('Value Plasma 38"') . ',5), '
            .'('. $this->_db->qutoe('Power-MP3 5gb') . ',7),    ('. $this->_db->qutoe('Super-Player 1gb') . ',8),'
            .'('. $this->_db->qutoe('Porta CD') . ',9),         ('. $this->_db->qutoe('CD To go!') . ',9), '
            .'('. $this->_db->qutoe('Family Talk 360') . ',10)';
        $this->_db->query($sql);
        
        $sql = 'SELECT * FROM ' . $dataTable;
        
        return $this->_db->fetchAll($sql);
    }
    
        
    /**
     * Aggregate Functions in a Nested Set
     * Let's add a table of products that we can use to demonstrate aggregate functions with:
     * @return void
     */
    public function getCategorizedData($dataTable, $dataTableKey, $categoryKey)
    {
        $keyFld  = $this->_db->quoteIdentifier($this->_idFld);
        $lftFld  = $this->_db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $this->_db->quoteIdentifier($this->_rgtFld);
        $nameFld = $this->_db->quoteIdentifier($this->_nameFld);
        $table   = $this->_db->quoteIdentifier($this->_tableName);
        
        $dataTable      = $this->_db->quoteIdentifier($dataTable);
        $dataTableKey   = $this->_db->quoteIdentifier($dataTableKey);
        
        $select = 
            'SELECT parent.' . $nameFld . ', COUNT(cd.' . $nameFld . ')' . PHP_EOL
           .'FROM ' . $table . ' AS node ,' . PHP_EOL
           .$table . ' AS parent,' . PHP_EOL
           .$dataTable . ' cd ' . PHP_EOL
           .'WHERE node.' . $lftFld . ' BETWEEN parent.' . $lftFld . ' AND parent.' . $rgtFld . PHP_EOL
           .'AND node.' . $keyFld . ' = ' . $dataTable .'.' . $dataTableKey . PHP_EOL
           .'GROUP BY parent.' . $nameFld . PHP_EOL
           .'ORDER BY node.' . $lftFld;
        
        return $this->_db->fetchAll($select);
    }
    
    
    /**
     * Public method to get an element
     *
     */
    public function getElement($elementId, $depth = null)
    {
        $element = $this->_getElement($elementId, $depth);
        return $element;
    }

    /**
     * Get one element with its children.
     * @TODO depth
     *
     * @param int $elementId    Element Id
     * @param int $depth        Optional, depth of the tree. Default null means
     *                          full tree
     * @param string $order
     *
     * @return array
     */
    private function _getElement($elementId, $depth = null, $order = 'ASC')
    {
        // @TODO: test -> if multiple elements with depth 1 are found -> error
        $db        = $this->_db;
        $elementId = (int) $elementId;

        // Get main element left and right
        $select = $db
            ->select()
            ->from($this->_tableName, array($this->_lftFld, $this->_rgtFld))
            ->where($this->_idFld . ' = ?', $elementId);

        $stmt    = $db->query($select);
        $element = $stmt->fetch();

        // Get the tree
        $query = "
            SELECT
                node.{$this->_idFld},
                node.{$this->_nameFld},
                node.{$this->_lftFld},
                node.{$this->_rgtFld},
                COUNT(parent.{$this->_nameFld}) - 1 AS depth
              FROM
                {$this->_tableName} AS node,
                {$this->_tableName} AS parent
             WHERE node.{$this->_lftFld} BETWEEN parent.{$this->_lftFld} AND parent.{$this->_rgtFld}
               AND node.{$this->_lftFld} BETWEEN {$element[$this->_lftFld]} AND {$element[$this->_rgtFld]}
             GROUP BY node.{$this->_idFld}, node.{$this->_nameFld}, node.{$this->_lftFld}, node.{$this->_rgtFld}
             ORDER BY node.{$this->_lftFld} $order
        ";

        $stmt  = $this->_db->query($query);
        $nodes = $stmt->fetchAll();

        return $nodes;
    }
    
    	
    /**
     * Generates left and right column value, based on id of a
     * objective node.
     *
     * @param mixed Id of a objective node.
     * @param string Position in tree.
     * @return array
     */
    protected function _getLftRgt($objectiveNodeId, $position, $id = null)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $this->_db;
        
        $lftRgt = array();
        
        $table   = $db->quoteIdentifier($this->_tableName);
        $nameFld = $db->quoteIdentifier($this->_nameFld);
        $keyFld  = $db->quoteIdentifier($this->_idFld);
        $lftFld  = $db->quoteIdentifier($this->_lftFld);
        $rgtFld  = $db->quoteIdentifier($this->_rgtFld);

        $lft = null;
        $rgt = null;
        
        if ($objectiveNodeId) {
            $sql = "SELECT $lftFld, $rgtFld FROM $table WHERE $keyFld = " . (int)$objectiveNodeId;
            $stmt = $db->query($sql);
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(Zend_Db::FETCH_ASSOC);
                $lft = (int)$result[$this->_lftFld];
                $rgt = (int)$result[$this->_rgtFld];
            }
        }
        
        if ($lft !== null && $rgt !== null) { //Existing objective id?
            $sql1 = '';
            $sql2 = '';
            switch ($position) {
                case self::FIRST_CHILD :
                    $sql1 = "UPDATE $table SET $rgtFld = $rgtFld + 2 WHERE $rgtFld > $lft";
                    $sql2 = "UPDATE $table SET $lftFld = $lftFld + 2 WHERE $lftFld > $lft";

                    $lftRgt[$this->_lftFld] = $lft + 1;
                    $lftRgt[$this->_rgtFld] = $lft + 2;

                    break;
                case self::LAST_CHILD :
                    $sql1 = "UPDATE $table SET $rgtFld = $rgtFld + 2 WHERE $rgtFld >= $rgt";
                    $sql2 = "UPDATE $table SET $lftFld = $lftFld + 2 WHERE $lftFld > $rgt";

                    $lftRgt[$this->_lftFld] = $rgt;
                    $lftRgt[$this->_rgtFld] = $rgt + 1;

                    break;
                case self::NEXT_SIBLING :
                    $sql1 = "UPDATE $table SET $rgtFld = $rgtFld + 2 WHERE $rgtFld > $rgt";
                    $sql2 = "UPDATE $table SET $lftFld = $lftFld + 2 WHERE $lftFld > $rgt";

                    $lftRgt[$this->_lftFld] = $rgt + 1;
                    $lftRgt[$this->_rgtFld] = $rgt + 2;

                    break;
                case self::PREV_SIBLING :
                    $sql1 = "UPDATE $table SET $rgtFld = $rgtFld + 2 WHERE $rgtFld > $lft";
                    $sql2 = "UPDATE $table SET $lftFld = $lftFld + 2 WHERE $lftFld >= $lft";

                    $lftRgt[$this->_lftFld] = $lft;
                    $lftRgt[$this->_rgtFld] = $lft + 1;
                    break;
            }

            $db->query($sql1);
            $db->query($sql2);
        }
        else {
            $sql = "SELECT MAX($rgtFld) AS \"max_rgt\" FROM $table";
            if ($id !== null) {
                $sql .= " WHERE $keyFld != " . (int)$id;
            }
            $stmt = $db->query($sql);
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(Zend_Db::FETCH_ASSOC);
                $lftRgt[$this->_lftFld] = $result['max_rgt'] + 1;
            }
            else {
                //No data? First node...
                $lftRgt[$this->_lftFld] = 1;
            }

            $lftRgt[$this->_rgtFld] = $lftRgt[$this->_lftFld] + 1;
        }
        
        return $lftRgt;
    }
    
    /**
     * Convert a tree array (with depth) into a hierarchical array.
     *
     * @param $tree|array   Array with depth value.
     *
     * @return array
     */
    public function toArray($tree = null)
    {
        if (empty($tree) || !is_array($tree)) {
            $nodes = $this->fetchTree();
        }
        else {
            $nodes = $tree;
        }

        $result     = array();
        $stackLevel = 0;

        if (count($nodes) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();

            foreach ($nodes as $node) {
                $node['children'] = array();

                // Number of stack items
                $stackLevel = count($stack);

                // Check if we're dealing with different levels
                while ($stackLevel > 0 && $stack[$stackLevel - 1]['depth'] >= $node['depth']) {
                    array_pop($stack);
                    $stackLevel--;
                }

                // Stack is empty (we are inspecting the root)
                if ($stackLevel == 0) {
                    // Assigning the root node
                    $i = count($result);

                    // $result[$i] = $item;
                    $result[$i] = $node;
                    $stack[] =& $result[$i];
                }
                else {
                    // Add node to parent
                    $i = count($stack[$stackLevel - 1]['children']);

                    $stack[$stackLevel - 1]['children'][$i] = $node;
                    $stack[] =& $stack[$stackLevel - 1]['children'][$i];
                }
            }
        }

        return $result;
    }

    /**
     * Convert a tree array (with depth) into a hierarchical XML string.
     *
     * @param $tree|array   Array with depth value.
     *
     * @return string
     */
    public function toXml($tree = null)
    {
        $keyFld = $this->_idFld;
        $lftFld = $this->_lftFld;
        $rgtFld = $this->_rgtFld;
        $nameFld= $this->_nameFld;
        
        if (empty($tree) || !is_array($tree)) {
            $nodes = $this->fetchTree();
        }
        else {
            $nodes = $tree;
        }

        $xml  = new DomDocument('1.0');
        $xml->formatOutput = true;
        $root = $xml->createElement('root');
        $xml->appendChild($root);

        $depth = 0;
        $currentChildren = array();

        foreach ($nodes as $node) {
            $element = $xml->createElement('element');
            $element->setAttribute('id',   $node[$keyFld]);
            $element->setAttribute('name', $node[$nameFld]);
            $element->setAttribute('lft',  $node[$lftFld]);
            $element->setAttribute('rgt',  $node[$rgtFld]);

            $children = $xml->createElement('children');
            $element->appendChild($children);

            if ($node['depth'] == 0) {
                // Handle root
                $root->appendChild($element);
                $currentChildren[0] = $children;
            }
            elseif ($node['depth'] > $depth) {
                // is a new sub level
                $currentChildren[$depth]->appendChild($element);
                $currentChildren[$node['depth']] = $children;
            }
            elseif ($node['depth'] == $depth || $node['depth'] < $depth) {
                // is at the same level
                $currentChildren[$node['depth'] - 1]->appendChild($element);
            }

            $depth = $node['depth'];
        }

        return $xml->saveXML();
    }

    /**
     * Return nested set as JSON
     *
     * @params $tree|array          Original 'flat' nested tree
     *
     * @return string
     */
    public function toJson($tree = null)
    {
        $nestedArray = $this->toArray($tree);
        $result      = json_encode($nestedArray);

        return $result;
    }

    /**
     * Returns all elements as <ul>/<li> structure
     *
     * Possible options:
     *  - list (simple <ul><li>)
     *
     * @return string
     */
    public function toHtml($tree = null)
    {
        if (empty($tree) || !is_array($tree)) {
            $nodes = $this->fetchTree();
        }
        else {
            $nodes = $tree;
        }
        
        $result = "<ul>\n";
        $depth  = $nodes[0]['depth'];

        foreach ($nodes as $node) {

            if ($depth < $node['depth']) {
                $result .= "\n<ul>\n";
            }
            elseif ($depth == $node['depth'] && $depth > $nodes[0]['depth']) {
                $result .= "</li>\n";
            }
            elseif ($depth > $node['depth']) {
                for ($i = 0; $i < ($depth - $node['depth']); $i++) {
                    $result .= "</li></ul>\n";
                }
            }

            // XXX Currently it outputs results according to my actual needs
            // for testing purpose.
            $result .= "<li>{$node[$this->_nameFld]} (id: {$node[$this->_idFld]} left: {$node[$this->_lftFld]} right: {$node[$this->_rgtFld]})";

            $depth = $node['depth'];
        }

        $result .= "</li></ul>\n";
        $result .= "</ul>\n";

        /** XXX include into test
         *
        $ulStart = substr_count($result, '<ul>');
        $ulEnd   = substr_count($result, '</ul>');
        $liStart = substr_count($result, '<li>');
        $liEnd   = substr_count($result, '</li>');

        if ($ulStart != $ulEnd) {
            echo "Bad count of <ul> ($ulStart/$ulEnd)";
        }

        if ($liStart != $liEnd) {
            echo "Bad count of <li> ($liStart/$liEnd)";
        }
         */

        return $result;
    }


}





