<?php


$tree = new MyProject_Model_TreeviewSql(array());
echo '<pre>' . $tree->getNodesIndented();

/**
 * Description of TreeviewAbstract
 * abstract We use ' . $this->_lftFld . ' and ' . $this->_rgtFld . ' because left and right are reserved 
 * words in MySQL, see http://dev.mysql.com/doc/mysql/en/reserved-words.html 
 * for the full list of reserved words.
 * @author rybka
 */
class MyProject_Model_TreeviewSql {
    //put your code here
    
    protected $_table       = 'mr_fuhrpark_categories';
    protected $_catIdFld    = 'category_id';
    protected $_nameFld     = 'name';
    protected $_lftFld      = 'lft';
    protected $_rgtFld      = 'rgt';
    
    public function __construct(array $fld_config)
    {
        foreach($fld_config as $fldKey => $fldName) {
            if (property_exists($this, '_' . $fldKey))
                $this->{"_$fldKey"} = (string) $fldName;
        }
        
        
    }
    
    public function getCatIdFld() {
        return $this->_catIdFld;
    }

    public function setCatIdFld($_catIdFld) {
        $this->_catIdFld = $_catIdFld;
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
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }
    
    /**
     *
     * @return string 
     */
    public function getCreateTable()
    {
        $sql = 
            'CREATE TABLE ' . $this->_table . ' ( '                 . PHP_EOL
            . $this->_catIdFld . ' INT AUTO_INCREMENT PRIMARY KEY,' . PHP_EOL
            . $this->_nameFld  . ' VARCHAR(20) NOT NULL,'           . PHP_EOL
            . $this->_lftFld   . ' INT NOT NULL,'                   . PHP_EOL
            . $this->_rgtFld   . ' INT NOT NULL'                    . PHP_EOL
            .')';
        
        return $sql;
    }
    
    /**
     *
     * @return string
     */
    public function getClearTable()
    {
        return 'TRUNCATE ' . $this->_table;
    }
    
    /**
     *
     * @return string 
     */
    public function getInsertTestData()
    {
        $sql = 
             'INSERT INTO ' . $this->_table         . PHP_EOL
            .'(' . $this->_catIdFld . ', ' 
            . $this->_nameFld . ', ' 
            . $this->_lftFld . ' , ' 
            . $this->_rgtFld 
            . ')'                                   . PHP_EOL
            .'VALUES(1,\'ELECTRONICS\',1,20)'       . PHP_EOL
            .',(2,\'TELEVISIONS\',2,9)'             . PHP_EOL
            .',(3,\'TUBE\',3,4)'                    . PHP_EOL
            .',(4,\'LCD\',5,6)'                     . PHP_EOL
            .',(5,\'PLASMA\',7,8)'                  . PHP_EOL
            .',(6,\'PORTABLE ELECTRONICS\',10,19)'  . PHP_EOL
            .',(7,\'MP3 PLAYERS\',11,14)'           . PHP_EOL
            .',(8,\'FLASH\',12,13)'                 . PHP_EOL
            .',(9,\'CD PLAYERS\',15,16)'            . PHP_EOL
            .',(10,\'2 WAY RADIOS\',17,18)';
        
        return $sql;
    }
    
    /**
     * Retrieving a Single Path
     * @abstract 
     * With the nested set model, we can retrieve a single path 
     * without having multiple self-joins:
     * @return string
     */
    public function getSinglePath($nodeName = 'FLASH') 
    {
        $sql = 
             'SELECT parent.' . $this->_nameFld                 . PHP_EOL
            .'FROM ' . $this->_table . ' AS node, '             . PHP_EOL
            .$this->_table . ' AS parent'                       . PHP_EOL
            .'WHERE node.' . $this->_lftFld . ' '
            .' BETWEEN parent.' . $this->_lftFld 
            .' AND parent.' . $this->_rgtFld . ' '              . PHP_EOL
            .'AND node.' . $this->_nameFld . ' = \''. $nodeName . '\' '    . PHP_EOL
            .'ORDER BY parent.' . $this->_lftFld;
        
        return $sql;
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
        $sql = 
            'SELECT node.' . $this->_nameFld . ', '                 . PHP_EOL
           .'(COUNT(parent.' . $this->_nameFld . ') - 1) AS depth'  . PHP_EOL
           .'FROM ' . $this->_table . ' AS node,'                   . PHP_EOL
           . $this->_table . ' AS parent'                           . PHP_EOL
           .'WHERE node.' . $this->_lftFld . ' BETWEEN parent.' . $this->_lftFld . PHP_EOL
           .' AND parent.' . $this->_rgtFld                         . PHP_EOL
           .'GROUP BY node.' . $this->_nameFld                      . PHP_EOL
           .'ORDER BY node.' . $this->_lftFld;
        
        return $sql;
    }
    
    /**
     * @abstract
     * We can use the depth value to indent our category names with the 
     * CONCAT and REPEAT string functions:
     * @return string
     */
    public function getNodesIndented()
    {
        $sql = 
            'SELECT CONCAT( REPEAT(\'-\', COUNT(parent.' . $this->_nameFld . ') - 1), node.' . $this->_nameFld . ') AS name ' . PHP_EOL
            .'FROM ' . $this->_table . ' AS node,'      . PHP_EOL
            .'' . $this->_table . ' AS parent'          . PHP_EOL
            .'WHERE node.' . $this->_lftFld . ' BETWEEN parent.' . $this->_lftFld . ' AND parent.' . $this->_rgtFld . ' ' . PHP_EOL
            .'GROUP BY node.' . $this->_nameFld . ' '    . PHP_EOL
            .'ORDER BY node.' . $this->_lftFld . '';
        
        return $sql;
    }
    
    
    /**Depth of a Sub-Tree
     * @abstract
     * When we need depth information for a sub-tree, we cannot limit either 
     * the node or parent tables in our self-join because it will corrupt our 
     * results. Instead, we add a third self-join, along with a sub-query to 
     * determine the depth that will be the new starting point for our sub-tree:
     * @return string
     */
    public function getDepthOfSubTree($nodeName = 'PORTABLE ELECTRONICS')
    {
        $sql = 
             'SELECT node.' . $this->_nameFld . ', (COUNT(parent.' . $this->_nameFld . ') - (sub_tree.depth + 1)) AS depth ' . PHP_EOL
            .'FROM ' . $this->_table . ' AS node, ' . PHP_EOL
            .'	nested_category AS parent, ' . PHP_EOL
            .'	nested_category AS sub_parent, ' . PHP_EOL
            .'	( ' . PHP_EOL
            .'		SELECT node.' . $this->_nameFld . ', (COUNT(parent.' . $this->_nameFld . ') - 1) AS depth ' . PHP_EOL
            .'		FROM ' . $this->_table . ' AS node, ' . PHP_EOL
            .'		nested_category AS parent ' . PHP_EOL
            .'		WHERE node.' . $this->_lftFld . ' BETWEEN parent.' . $this->_lftFld . ' AND parent.' . $this->_rgtFld . ' ' . PHP_EOL
            .'		AND node.' . $this->_nameFld . ' = \'' . $nodeName . '\' ' . PHP_EOL
            .'		GROUP BY node.' . $this->_nameFld . ' ' . PHP_EOL
            .'		ORDER BY node.' . $this->_lftFld . ' ' . PHP_EOL
            .'	)AS sub_tree ' . PHP_EOL
            .'WHERE node.' . $this->_lftFld . ' BETWEEN parent.' . $this->_lftFld . ' AND parent.' . $this->_rgtFld . ' ' . PHP_EOL
            .'	AND node.' . $this->_lftFld . ' BETWEEN sub_parent.' . $this->_lftFld . ' AND sub_parent.' . $this->_rgtFld . ' ' . PHP_EOL
            .'	AND sub_parent.' . $this->_nameFld . ' = sub_tree.' . $this->_nameFld . ' ' . PHP_EOL
            .'GROUP BY node.' . $this->_nameFld . ' ' . PHP_EOL
            .'ORDER BY node.' . $this->_lftFld;
        
        return $sql;
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
     * @return string
     */
    public function getImmediateSubsOfNode($nodeName = 'PORTABLE ELECTRONICS')
    {
        $sql =
            'SELECT node.' . $this->_nameFld . ', (COUNT(parent.' . $this->_nameFld . ') - (sub_tree.depth + 1)) AS depth' . PHP_EOL
            .'FROM ' . $this->_table . ' AS node,' . PHP_EOL
            .'	nested_category AS parent,' . PHP_EOL
            .'	nested_category AS sub_parent,' . PHP_EOL
            .'	(' . PHP_EOL
            .'		SELECT node.' . $this->_nameFld . ', (COUNT(parent.' . $this->_nameFld . ') - 1) AS depth' . PHP_EOL
            .'		FROM ' . $this->_table . ' AS node,' . PHP_EOL
            .'		nested_category AS parent' . PHP_EOL
            .'		WHERE node.' . $this->_lftFld . ' BETWEEN parent.' . $this->_lftFld . ' AND parent.' . $this->_rgtFld . PHP_EOL
            .'		AND node.' . $this->_nameFld . ' = \'' . $nodeName . '\'' . PHP_EOL
            .'		GROUP BY node.' . $this->_nameFld . '' . PHP_EOL
            .'		ORDER BY node.' . $this->_lftFld . PHP_EOL
            .'	)AS sub_tree' . PHP_EOL
            .'WHERE node.' . $this->_lftFld . ' BETWEEN parent.' . $this->_lftFld . ' AND parent.' . $this->_rgtFld . PHP_EOL
            .'	AND node.' . $this->_lftFld . ' BETWEEN sub_parent.' . $this->_lftFld . ' AND sub_parent.' . $this->_rgtFld . PHP_EOL
            .'	AND sub_parent.' . $this->_nameFld . ' = sub_tree.' . $this->_nameFld . '' . PHP_EOL
            .'GROUP BY node.' . $this->_nameFld . '' . PHP_EOL
            .'HAVING depth = 1' . PHP_EOL
            .'ORDER BY node.' . $this->_lftFld;
        
        return $sql;
    }
    
    /**
     * Aggregate Functions in a Nested Set
     * Let's add a table of products that we can use to demonstrate aggregate functions with:
     * @return void
     */
    public function getAggregateNestedTable()
    {
        $sql = <<<EOT
        CREATE TABLE product(
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(40),
        category_id INT NOT NULL
        );


        INSERT INTO product(name, {$this->_catIdFld}) VALUES('20" TV',3),('36" TV',3),
        ('Super-LCD 42"',4),('Ultra-Plasma 62"',5),('Value Plasma 38"',5),
        ('Power-MP3 5gb',7),('Super-Player 1gb',8),('Porta CD',9),('CD To go!',9),
        ('Family Talk 360',10);

        SELECT * FROM product
        
        #Now let's produce a query that can retrieve our category tree, 
        #along with a product count for each category:

        SELECT parent.{$this->_nameFld}, COUNT(product.{$this->_nameFld})
        FROM {$this->_table} AS node ,
        {$this->_table} AS parent,
        product
        WHERE node.{$this->_lftFld} BETWEEN parent.{$this->_lftFld} AND parent.{$this->_rgtFld}
        AND node.{$this->_catIdFld} = product.{$this->_catIdFld}
        GROUP BY parent.{$this->_nameFld}
        ORDER BY node.{$this->_lftFld};
EOT;
        return $sql;
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
     * @return array
     */
    public function getAddNode($newNodeName, $neighbourNodeName)
    {
        $sqls = array();
        $sqls[] = 'LOCK TABLE ' . $this->_table . ' WRITE';

        $sqls[] = 'SELECT @myRight := ' . $this->_rgtFld . ' FROM nested_category' . PHP_EOL
                 .'WHERE name = \'' . $neighbourNodeName . '\'';

        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_rgtFld . ' = ' . $this->_rgtFld . ' + 2 WHERE ' . $this->_rgtFld . ' > @myRight';
        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_lftFld . ' = ' . $this->_lftFld . ' + 2 WHERE ' . $this->_lftFld . ' > @myRight';

        $sqls[] = 'INSERT INTO nested_category(name, ' . $this->_lftFld .', ' . $this->_rgtFld . ') ' . PHP_EOL
                 .'VALUES(\'' . $newNodeName . '\', @myRight + 1, @myRight + 2)';

        $sqls[] = 'UNLOCK TABLES';
        
        return $sqls;
    }
    
    /** 
     * @abstract
     * If we instead want to add a node as a child of a node that has no 
     * existing children, we need to modify our procedure slightly. 
     * Let's add a new FRS node below the 2 WAY RADIOS node:
     * @return array
     */
    public function getAddNodeAsChild($newNodeName = 'FRS', $parentNodeName = '2 WAY RADIOS')
    {
        $sqls = array();
        
        $sqls[] = 'LOCK TABLE ' . $this->_table . ' WRITE';

        $sqls[] = 'SELECT @myLeft := ' . $this->_lftFld . ' FROM ' . $this->_table . ' ' . PHP_EOL
                 .'WHERE name = \'' . $parentNodeName. '\'';

        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_rgtFld . ' = ' . $this->_rgtFld . ' + 2 WHERE ' . $this->_rgtFld . ' > @myLeft';
        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_lftFld . ' = ' . $this->_lftFld . ' + 2 WHERE ' . $this->_lftFld . ' > @myLeft';

        $sqls[] = 'INSERT INTO nested_category(name, ' . $this->_lftFld . ', ' . $this->_rgtFld . ') VALUES(\'' . $newNodeName. '\', @myLeft + 1, @myLeft + 2)';

        $sqls[] = 'UNLOCK TABLES';
        
        return $sqls;
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
     * @return array
     */
    public function getDeleteNode($nodeName = 'GAME CONSOLES')
    {
        $sqls = array();
        
        $sqls[] = 'LOCK TABLE ' . $this->_table . ' WRITE';
        
        $sqls[] = 'SELECT @myLeft := lft, @myRight := rgt, @myWidth := ' . $this->_rgtFld . ' - ' . $this->_lftFld . ' + 1 ' . PHP_EOL
                 .'FROM ' . $this->_table . ' ' . PHP_EOL
                 .'WHERE name = \'' . $nodeName . '\'';

        $sqls[] = 'DELETE FROM ' . $this->_table . ' WHERE ' . $this->_lftFld . ' BETWEEN @myLeft AND @myRight';

        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_rgtFld . ' = ' . $this->_rgtFld . ' - @myWidth WHERE ' . $this->_rgtFld . ' > @myRight';
        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_lftFld . ' = ' . $this->_lftFld . ' - @myWidth WHERE ' . $this->_lftFld . ' > @myRight';

        $sqls[] = 'UNLOCK TABLES';
        
        return $sqls;
    }
    
    /**
     * This approach works equally well to delete a node and all its children
     * @return array
     */
    public function getDeleteNodeWithChilds($nodeName = 'MP3 PLAYERS')
    {
        $sql = array();

        $sqls[] = 'LOCK TABLE ' . $this->_table . ' WRITE';

        $sqls[] = 'SELECT @myLeft := ' . $this->_lftFld . ', @myRight := ' . $this->_rgtFld . ', @myWidth := ' . $this->_rgtFld . ' - ' . $this->_lftFld . ' + 1 ' . PHP_EOL
                 .'FROM ' . $this->_table . ' ' . PHP_EOL
                 .'WHERE name = \'' . $nodeName . '\'';

        $sqls[] = 'DELETE FROM ' . $this->_table . ' WHERE ' . $this->_lftFld . ' BETWEEN @myLeft AND @myRight';

        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_rgtFld . ' = ' . $this->_rgtFld . ' - @myWidth WHERE ' . $this->_rgtFld . ' > @myRight';
        $sqls[] = 'UPDATE ' . $this->_table . ' SET ' . $this->_lftFld . ' = ' . $this->_lftFld . ' - @myWidth WHERE ' . $this->_lftFld . ' > @myRight';

        $sqls[] = 'UNLOCK TABLES';
        
        return $sqls;
    }
    
    /**
     * @abstract
     * The other scenario we have to deal with is the deletion of a parent 
     * node but not the children. In some cases you may wish to just change 
     * the name to a placeholder until a replacement is presented, such as 
     * when a supervisor is fired. In other cases, the child nodes should 
     * all be moved up to the level of the deleted parent:
     * @return array
     */
    public function getDeleteNodeWithoutChilds($nodeName = 'PORTABLE ELECTRONICS')
    {
        $sqls = array();
        
        $sqls[] = 'LOCK TABLE ' . $this->_table . ' WRITE';

        $sqls[] = 'SELECT @myLeft := ' . $this->_lftFld . ', @myRight := ' . $this->_rgtFld . ', @myWidth := ' . $this->_rgtFld . ' - ' . $this->_lftFld . ' + 1 ' . PHP_EOL
                 .'FROM ' . $this->_table . ' ' . PHP_EOL
                 .'WHERE name = \'' . $nodeName . '\'';

        $sqls[] = 'DELETE FROM ' . $this->_table . ' WHERE ' . $this->_lftFld . ' = @myLeft';

        $sqls[] = 'UPDATE ' . $this->_table  . ' SET ' . $this->_rgtFld . ' = ' . $this->_rgtFld . ' - 1, ' . $this->_lftFld . ' = ' . $this->_lftFld . ' - 1 ' . PHP_EOL
                 .'WHERE '  . $this->_lftFld . ' BETWEEN @myLeft AND @myRight';
        $sqls[] = 'UPDATE ' . $this->_table  . ' SET ' . $this->_rgtFld . ' = ' . $this->_rgtFld . ' - 2 WHERE ' . $this->_rgtFld . ' > @myRight';
        $sqls[] = 'UPDATE ' . $this->_table  . ' SET ' . $this->_lftFld . ' = ' . $this->_lftFld . ' - 2 WHERE ' . $this->_lftFld . ' > @myRight';

        $sqls[] = 'UNLOCK TABLES';
        
        return $sqls;
    }

}





