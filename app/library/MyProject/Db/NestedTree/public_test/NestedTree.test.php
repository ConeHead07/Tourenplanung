<?php
error_reporting(E_ALL);
require_once '../library/MyProject/Db/NestedTree/Controller.php';

$dsn = 'mysql:dbname=phpproject;host=127.0.0.1';
$user = 'root';
$password = '';

$_name = 'nested_category'; // Table
$_primary = 'category_id';  // Node-Id
$_caption = 'name';         // Node-Name
$_left = 'lft';             // Node-Left-Value
$_right = 'rgt';            // Node-Right-Value
$db = null;

class MyPDO extends PDO {
    public function quoteIdentifier($identifier) {
        return $identifier;
    }
}

try {
    $db = new MyPDO($dsn, $user, $password);
} catch (PDOException $e) {
    throw new PDOException('Connection failed: ' . $e->getMessage());
}


echo '<pre>';

$tree = new MyProject_Db_NestedTree_Controller(
            array(
                'name'    => 'nested_category',
                'caption' => 'name',
                'primary' => 'category_id',
                'left'    => 'lft',
                'right'   => 'rgt',
                'db'      => $db));

$treeNodes = $tree->getChildren();

recursiveIteratorTest( $treeNodes );



$id = getRequest('id', 7);
$dstPos = getRequest('newPos', 1);
$dstNodeId = getRequest('relNodeId', 0);
$withChilds = getRequest('withChilds', true);

// Move TreeNode with or without Childs
echo '<h2>MOVE NODE </h2>' . PHP_EOL;
restoreTree($db, $_name);
echo showCategories($db);
$tree->moveNode($id, $dstPos, $dstNodeId, $withChilds);
echo showCategories($db);

// Remove TreeNode with or without Childs
echo '<h2>REMOVE NODE </h2>' . PHP_EOL;
restoreTree($db, $_name);
echo showCategories($db);
$tree->removeNode($id, $withChilds);
echo showCategories($db);

// Insert TreeNode without Childs
$newNode = getRequest('newNode', '');
if ($newNode) {
    echo '<h2>INSERT NODE TEST </h2>' . PHP_EOL;
    restoreTree($db, $_name);
    echo showCategories($db);
    $newID = $tree->insertNode(array($_caption => $newNode), $dstNodeId, $dstPos);
    echo showCategories($db);
}

// Insert TreeNode as Parent of Dest-Node
echo '<h2>INSERT NODE AS PARENT OF ' . $dstNodeId . ' </h2>' . PHP_EOL;
echo showCategories($db);
$newID = $tree->insertNode(array($_caption => $newNode), $dstNodeId, 'parent');
echo showCategories($db);

// GET IMMEDIATE CHILDS
echo '<h2>GET CHILDS OF (PARENTID: ' . $id . ' )</h2>' . PHP_EOL;
$res = $tree->getChildren($id);
recursiveIteratorTest( $res, 1 );

// GET IMMEDIATE Top-Nodes
echo '<h2>GET TOP-NODES</h2>' . PHP_EOL;
$res = $tree->getChildren();
recursiveIteratorTest( $res, 1 );

// GET IMMEDIATE PARENT
echo '<h2> IMMEDIATE PARENT</h2>' . PHP_EOL;
$res = $tree->getParent($id);
showNodeData($res);

// GET PARENTS
echo '<h2>GET PARENTS</h2>' . PHP_EOL;
$res = $tree->getAncestors($id);
recursiveIteratorTest( $res, 1 );

// GET SIBLINGS
echo '<h2>GET SIBLINGS (includeNode)</h2>' . PHP_EOL;
$res = $tree->getSiblings($id);
recursiveIteratorTest( $res, 1 );
echo '<h2>GET SIBLINGS (includeNode=false)</h2>' . PHP_EOL;
$res = $tree->getSiblings($id, false);
recursiveIteratorTest( $res, 1 );

// GET PREVIOUS SIBLING
echo '<h2>GET PREVIOUS SIBLING</h2>' . PHP_EOL;
$res = $tree->getPreviousSibling($id);
showNodeData($res);

// GET NEXTS SIBLING
echo '<h2>GET NEXT SIBLING</h2>' . PHP_EOL;
$res = $tree->getNextSibling($id);
showNodeData($res);

// GET FIRST CHILD
echo '<h2>GET FIRST CHILD</h2>' . PHP_EOL;
$res = $tree->getFirstChild($id);
showNodeData($res);

// GET LAST CHILD
echo '<h2>GET LAST CHILD</h2>' . PHP_EOL;
$res = $tree->getLastChild($id);
showNodeData($res);

// GET CHILD BY INDEX
$childIndex = getRequest('childIndex', 2);
echo '<h2>GET CHILD BY INDEX (' . $childIndex . ' ), ZERO-BASED</h2>' . PHP_EOL;
$res = $tree->getChild($id, $childIndex);
showNodeData($res);


// TEST-SUITE WITH ASSERTS FOR NODE-METHODS


// Active assert and make it quiet
assert_options (ASSERT_ACTIVE, 1);
assert_options (ASSERT_WARNING, 0);
assert_options (ASSERT_QUIET_EVAL, 1);
// Create a handler function
function my_assert_handler ($file, $line, $code) {
    echo "<hr>Assertion Failed:
        File '$file'
        Line '$line'>
        Code '$code'<hr>";
}
// Set up the callback
assert_options (ASSERT_CALLBACK, 'my_assert_handler');

restoreTree($db, $_name);
$treeNodes = $tree->getChildren();
recursiveIteratorTest( $treeNodes );

$nodeId = 6;

include_once('../library/MyProject/Db/NestedTree/Node.php');
/* @var $node MyProject_Db_NestedTree_Node */
$node = $tree->getNodeById( $nodeId);

$nodeName = $node->getNodeName();
echo 'nodeId: ' . $nodeId . PHP_EOL;

echo 'nodeName: ' . $nodeName . PHP_EOL;
assert('"PORTABLE ELECTRONICS" == $nodeName');


echo '<h2>addChild</h2>' . PHP_EOL;
echo (string) $node->addChild('testAddChild');

echo '<h2>addChild As First Child</h2>' . PHP_EOL;
echo (string) $node->addChild('testAddChild2', 'first');

echo '<h2>getAncestors</h2>' . PHP_EOL;
echo (string) $node->getAncestors();

echo '<h2>getChildren</h2>' . PHP_EOL;
echo (string) $node->getChildren();

echo '<h2>getData</h2>' . PHP_EOL;
print_r($node->getData());

echo '<h2>getDescendants</h2>' . PHP_EOL;
echo (string) $node->getDescendants();

echo '<h2>getFirstChild</h2>' . PHP_EOL;
echo (string) $node->getFirstChild();

echo '<h2>getLastChild</h2>' . PHP_EOL;
echo (string) $node->getLastChild();

echo '<h2>getLevel</h2>' . PHP_EOL;
echo (string) $node->getLevel();

echo '<h2>getNextSibling</h2>' . PHP_EOL;
echo (string) $node->getNextSibling();

echo '<h2>getNodeId</h2>' . PHP_EOL;
echo (string) $node->getNodeId();

echo '<h2>getNodeName</h2>' . PHP_EOL;
echo (string) $node->getNodeName();

echo '<h2>getNumberChildren</h2>' . PHP_EOL;
echo (string) $node->getNumberChildren();

echo '<h2>getNumberDescendants</h2>' . PHP_EOL;
echo (string) $node->getNumberDescendants();

echo '<h2>getParent</h2>' . PHP_EOL;
echo (string) $node->getParent();

echo '<h2>getPath( -> , $includeNode = true)</h2>' . PHP_EOL;
echo (string) $node->getPath(' -> ', $includeNode = true);

echo '<h2>getPath( -> , $includeNode = false)</h2>' . PHP_EOL;
echo (string) $node->getPath(' -> ', $includeNode = false);

echo '<h2>getPrevSibling</h2>' . PHP_EOL;
echo (string) $node->getPrevSibling();

echo '<h2>getSiblings($includeNode = true)</h2>' . PHP_EOL;
echo (string) $node->getSiblings($includeNode = true);

echo '<h2>getSiblings($includeNode = false)</h2>' . PHP_EOL;
echo (string) $node->getSiblings($includeNode = false);

echo '<h2>hasChildren</h2>' . PHP_EOL;
echo (string) $node->hasChildren();

echo '<h2>hasNextSibling</h2>' . PHP_EOL;
echo (string) $node->hasNextSibling();

echo '<h2>hasParent</h2>' . PHP_EOL;
echo (string) $node->hasParent();

echo '<h2>hasPrevSibling</h2>' . PHP_EOL;
echo (string) $node->hasPrevSibling();

$destId = 11;
$dest = $tree->getNodeById($destId);
echo 'destNodeId: ' . $destId . PHP_EOL;
echo 'destNodeName: ' . $dest->getNodeName() . PHP_EOL;

echo '<h2>insertAsFirstChildOf</h2>' . PHP_EOL;
$node->insertAsFirstChildOf($dest);

echo '<h2>insertAsLastChildOf</h2>' . PHP_EOL;
$node->insertAsLastChildOf($dest);

echo '<h2>insertNextSiblingOf</h2>' . PHP_EOL;
$node->insertAsNextSiblingOf($dest);

echo '<h2>insertAsFirstChildOf</h2>' . PHP_EOL;
$node->insertAsParentOf($dest);

echo '<h2>insertAsFirstChildOf</h2>' . PHP_EOL;
$node->insertAsFirstChildOf($dest);

echo '<h2>insertAsPrevSibling</h2>' . PHP_EOL;
$node->insertAsPrevSiblingOf($dest);

recursiveIteratorTest( $tree->getChildren() );

echo 'nodeId: ' . $nodeId . PHP_EOL;
$node = $tree->getNodeById( $nodeId);
echo 'node->getNodeId(): ' . $node->getNodeId() . PHP_EOL;
$subj = $node->getParent();
echo '<h2>test if ' . (string)$node . ' isDescendantOf: ' . (string)$subj. '</h2>' . PHP_EOL;
print_r($node->isDescendantOf($subj));
echo '<h2>isDescendantOfOrEqualTo ' . (string)$subj. '</h2>' . PHP_EOL;
print_r($node->isDescendantOfOrEqualTo($subj));

echo '<h2>isEqualTo self | parent</h2>' . PHP_EOL;
print_r($node->isEqualTo($node)); echo ' | ';
print_r($node->isEqualTo($subj));

echo '<h2>isLeaf</h2>' . PHP_EOL;
print_r($node->isLeaf());

echo '<h2>isRoot</h2>' . PHP_EOL;
print_r($node->isRoot());

echo '<h2>isValidNode</h2>' . PHP_EOL;
print_r($node->isValidNode());

echo '<h2>MOVE NODE (moveAsFirstChildOf ' . (string)$dest . ') WITH CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
$node->moveAsFirstChildOf($dest,  $withChilds = true);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>MOVE NODE (moveAsFirstChildOf ' . (string)$dest . ') WITHOUT CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
$node->moveAsFirstChildOf($dest,  $withChilds = false);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>MOVE NODE (moveAsLastChildOf ' . (string)$dest . ') WITH CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
$node->moveAsLastChildOf($dest,   $withChilds = true);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>MOVE NODE (moveAsLastChildOf ' . (string)$dest . ') WITHOUT CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
$node->moveAsLastChildOf($dest,   $withChilds = false);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>MOVE NODE (moveAsNextSiblingOf ' . (string)$dest . ') WITH CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
$node->moveAsNextSiblingOf($dest, $withChilds = true);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>MOVE NODE (moveAsNextSiblingOf ' . (string)$dest . ') WITHOUT CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
$node->moveAsNextSiblingOf($dest, $withChilds = false);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>MOVE NODE (moveAsPrevSiblingOf ' . (string)$dest . ') WITH CHILDS</h2>' . PHP_EOL;
echo 'dest: ' . print_r($dest,1) . PHP_EOL;
restoreTree($db, $_name);
echo showCategories($db);
$node->moveAsPrevSiblingOf($dest, $withChilds = true);
recursiveIteratorTest( $tree->getChildren() );
echo showCategories($db);

echo '<h2>MOVE NODE (moveAsPrevSiblingOf ' . (string)$dest . ') WITHOUT CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
$node->moveAsPrevSiblingOf($dest, $withChilds = false);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>DELETE NODE WITHOUT CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
recursiveIteratorTest( $tree->getChildren() );
$node->delete($withChilds = false);
recursiveIteratorTest( $tree->getChildren() );

echo '<h2>DELETE NODE WITH CHILDS</h2>' . PHP_EOL;
restoreTree($db, $_name);
recursiveIteratorTest( $tree->getChildren() );
$node->delete($withChilds = true);
recursiveIteratorTest( $tree->getChildren() );

return;


// F U N C T I O N S

function getRequest($key, $default) {
    return (array_key_exists($key, $_REQUEST)) ? $_REQUEST[$key] : $default;
}

/**
 *
 * @param array $rows Assoziatives Array
 * @return string html Table
 */
function rowsToTable(array $rows) {
    $i = 0;
    $t = '<table>';
    foreach ($rows as $row) {
        if (++$i == 1)
            $t.= '<thead><tr><th>#<th>' . implode('</th><th>', array_keys($row)) . '</th></tr></thead>' . PHP_EOL;
        $t.= '<tr><td>' . $i . '<td>' . implode('</td><td>', array_values($row)) . '</td></tr>' . PHP_EOL;
    }
    $t.= '</table>';
    return $t;
}

function queryToTable($sql, $db) {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return rowsToTable($stmt->fetchAll(PDO::FETCH_ASSOC)); // (Zend_Db::FETCH_ASSOC);
}

function showCategories($db) {
    global $_name;
    global $_primary;
    global $_caption;
    global $_left;
    global $_right;

    $sql =    ' SELECT CONCAT(REPEAT(\' -\' , COUNT(parent.' . $_caption . ' ) - 1), node.' . $_caption . ' ) AS indent, '
            . ' node.*, (COUNT(parent.' . $_primary . ' ) - 1) AS "depth" '
            . ' FROM ' . $_name . ' AS node , ' . $_name . ' AS parent '
            . ' WHERE node.' . $_left . ' BETWEEN parent.' . $_left . ' AND parent.' . $_right
            . ' GROUP BY node.' . $_primary
            . ' ORDER BY node.' . $_left;
    return queryToTable($sql, $db);
}

function restoreTree(PDO $db) {
    global $_name;
    global $_primary;
    global $_caption;
    global $_left;
    global $_right;
    
    $sql = "TRUNCATE TABLE $_name";
    $db->exec($sql);

    $sql = <<<EOT
    INSERT INTO $_name ({$_primary}, {$_caption}, {$_left}, {$_right}) VALUES
    (1, 'ELECTRONICS', 1, 24),   (2, 'TELEVISIONS', 2, 9),      (3, 'TUBE', 3, 4),
    (4, 'LCD', 5, 6),            (5, 'PLASMA', 7, 8),           (6, 'PORTABLE ELECTRONICS', 10, 19),
    (7, 'MP3 PLAYERS', 11, 14),  (8, 'FLASH', 12, 13),          (9, 'CD PLAYERS', 15, 16),
    (10, '2 WAY RADIOS', 17, 18),(11, 'In the kitchen', 20, 23),(12, 'Quirl', 21, 22)
EOT;
    $db->exec($sql);
}


function recursiveIteratorTest(MyProject_Db_NestedTree_Nodes $obj, $maxDeep = 0, $deep = 0) {
    /* @var $obj MyProject_Db_NestedTree_Nodes */
    /* @var $node MyProject_Db_NestedTree_Node */
    echo '<ul>';
    foreach($obj as $node) {
        echo '<li>' . $node->getNodeName();
        if ($node->hasChildren() && (0==$maxDeep || ($maxDeep>0 && $deep+1 < $maxDeep))) 
            recursiveIteratorTest($node->getChildren (), $maxDeep, $deep+1);
    }
    echo '</ul>';
    if ($deep == 0) echo PHP_EOL;
}

function showNodeData($node) {
    if (is_object($node) && method_exists($node, 'getData')) print_r($node->getData());
    else print_r($node);
    echo PHP_EOL;
}