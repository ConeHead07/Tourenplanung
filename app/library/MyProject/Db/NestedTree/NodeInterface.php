<?php 
//namespace MyProject\Db\NestedTree;

interface MyProject_Db_NestedTree_NodeInterface 
{
    /**
     * adds node as last child of record.
     * @param mixed $record
     * @return void
     */
    function addChild($record);

    /**
     * deletes node and it's descendants.
     * @return void 
     */
    function delete();

    /**
     * gets ancestors for node.
     * @return Node-Collection 	
     */
    function getAncestors();

    /**
     * gets children for node (direct descendants only).
     * @return array 	
     */
    function getChildren();

    /**
     * gets descendants for node (direct descendants only).
     * @return Iterator 	
     */
    function getDescendants();

    /**
     * gets record of first child or empty record.
     * @return Node-Record 	
     */
    function getFirstChild();

    /**
     * gets record of last child or empty record.
     * @return Doctrine_Record 	
     */
    function getLastChild();

    /**
     * gets level (depth) of node in the tree.
     * @return int 	
     */
    function getLevel();

    /**
     * gets record of next sibling or empty record.
     * @return Doctrine_Record 	
     */
    function getNextSibling();

    /**
     * gets number of children (direct descendants).
     * @return int 	
     */
    function getNumberChildren();

    /**
     * gets number of descendants (children and their children).
     * @return int 	
     */
    function getNumberDescendants();

    /**
     * gets record of parent or empty record.
     * @return Node-Record 	
     */
    function getParent();

    /**
     * gets path to node from root, uses record::toString() method to get node names.
     * @param string $separator
     * @param bool $includeNode
     * @return string 	
     */
    function getPath($seperator, $includeNode);

    /**
     * gets record of prev sibling or empty record.
     * @return Node-Record 	
     */
    function getPrevSibling();

    /**
     * gets siblings for node.
     * @param mixed includeNode
     * @return array 	
     */
    function getSiblings($includeNode);

    /**
     * test if node has children.
     * @return bool 	
     */
    function hasChildren();

    /**
     * test if node has next sibling.
     * @return bool 	
     */
    function hasNextSibling();

    /**
     * test if node has parent.
     * @return bool 	
     */
    function hasParent();

    /**
     * test if node has previous sibling.
     * @retrun bool 	
     */
    function hasPrevSibling();

    /**
     * inserts node as first child of dest record.
     * @param Node Destination
     * @return bool 	
     */
    function insertAsFirstChildOf($dest);

    /**
     * inserts node as first child of dest record.
     * @param Node Destination
     * @return bool
     */
    function insertAsLastChildOf($dest);

    /**
     * inserts node as next sibling of dest record.
     * @param Node Destination
     * @return bool 	
     */
    function insertAsNextSiblingOf($dest);

    /**
     * inserts node as parent of dest record.
     * @param Node Destination
     * @return bool 	
     */
    function insertAsParentOf($dest);

    /**
     * inserts node as previous sibling of dest record.
     * @param Node Destination
     * @return bool 	
     */
    function insertAsPrevSiblingOf($dest);

    /**
     * determines if node is child of subject node.
     * @param Node Subject
     * @return bool 	
     */
    function isDescendantOf($subj);

    /**
     * determines if node is child of or sibling to subject node.
     * @param Node Subject
     * @return bool 	
     */
    function isDescendantOfOrEqualTo($subj);

    /**
     * determines if node is equal to subject node.
     * @param mixed $subj
     * @return bool 	
     */
    function isEqualTo($subj);

    /**
     * determines if node is leaf.
     * @return bool 	
     */
    function isLeaf();

    /**
     * determines if node is root.
     * @return bool
     */
    function isRoot();

    /**
     * determines if node is valid.
     * @return bool
     */
    function isValidNode();

    /**
     * moves node as first child of dest record.
     * @param Node-Record (Dest-Node)
     * @return void
     */
    function moveAsFirstChildOf($dest);

    /**
     * moves node as last child of dest record.
     * @param Node-Record (Dest-Node)
     * @return void 	
     */
    function moveAsLastChildOf($dest);

    /**
     * moves node as next sibling of dest record.
     * @param Node-Record (Dest-Node)
     * @return void 	
     */
    function moveAsNextSiblingOf($dest);

    /**
     * moves node as prev sibling of dest record.
     * @param Node-Record (Dest-Node)
     * @return void
     */
    function moveAsPrevSiblingOf($dest);
}

