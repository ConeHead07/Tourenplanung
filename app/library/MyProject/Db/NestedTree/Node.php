<?php
//namespace MyProject\Db\NestedTree;

class MyProject_Db_NestedTree_Node implements MyProject_Db_NestedTree_NodeInterface {

    private $_data;
    private $_position = 0;
    /** var $_treeController NestedTree */
    protected $_treeController = null;
    protected $_primary = 'id';
    protected $_caption = 'name';
    protected $_id = 0;

    public function __construct(array $data, MyProject_Db_NestedTree_Controller $treeController) {
        $this->_data = $data;
        $this->_treeController = $treeController;
        $this->_primary = $this->_treeController->info('primary');
        $this->_caption = $this->_treeController->info('caption');
        $this->_id = $data[$this->_primary];
        
//        echo '#' . __LINE__ . ' ' . __METHOD__ . ' data: ' . print_r($data,1) . PHP_EOL;
//        echo '#' . __LINE__ . ' ' . __METHOD__ . ' node->_id: ' . $this->_id . PHP_EOL;
    }
    
    /**
     * @return array Node-Data
     */
    public function getData() {
        return $this->_data;
    }
    
    /**
     * @return string NodeName
     */
    public function getNodeName() {
        return $this->_data[$this->_caption];
    }
    
    
    /**
     * @return int NodeId
     */
    public function getNodeId() {
        return $this->_id;
    }

    /**
     * adds node as last child of record.
     * @param string|mixed $record
     * @return void
     */
    function addChild($record, $pos = 'last') {
        return $this->_treeController->insertNode($record, $this->_id, $pos);
    }

    /**
     * deletes node and it's descendants.
     * @param bool $withChilds
     * @return void 
     */
    function delete($withChilds = true) {
        $this->_treeController->removeNode($this->_id, $withChilds);
    }

    /**
     * gets ancestors for node.
     * @return MyProject_Db_NestedTree_Nodes 	
     */
    function getAncestors() {
        return $this->_treeController->getAncestors($this->_id);
    }

    /**
     * gets children for node (direct descendants only).
     * @return MyProject_Db_NestedTree_Nodes
     */
    function getChildren() {
        return new MyProject_Db_NestedTree_Nodes(
                $this->_treeController->getChildrenList($this->_id),
                $this->_treeController);
    }

    /**
     * gets descendants for node (direct descendants only).
     * @return Iterator 	
     */
    function getDescendants() {
        return $this->getChildren();
    }

    /**
     * gets record of first child or empty record.
     * @return MyProject_Db_NestedTree_Node 	
     */
    function getFirstChild() {
        return $this->_treeController->getChild($this->_id, 'first');
    }

    /**
     * gets record of last child or empty record.
     * @return MyProject_Db_NestedTree_Node 	
     */
    function getLastChild() {
        return $this->_treeController->getChild($this->_id, 'last');
    }

    /**
     * gets level (depth) of node in the tree.
     * @return int 	
     */
    function getLevel() { 
        return $this->_treeController->getLevel($this); 
    }

    /**
     * gets record of next sibling or empty record.
     * @return MyProject_Db_NestedTree_Node	
     */
    function getNextSibling() {
        return $this->_treeController->getNextSibling($this->_id);
    }

    /**
     * gets number of children (direct descendants).
     * @return int 	
     */
    function getNumberChildren() {
        return $this->_treeController->getChildrenCount($this->_id);
    }

    /**
     * gets number of descendants (children and their children).
     * @return int 	
     */
    function getNumberDescendants() {
        $l = $this->_treeController->info('left');
        $r = $this->_treeController->info('right');
        $d = &$this->_data;
        return intval(($d[$r] - $d[$l] - 1) / 2);
    }

    /**
     * gets record of parent or empty record.
     * @return MyProject_Db_NestedTree_Node	
     */
    function getParent() {
        return $this->_treeController->getParent($this->_id);
    }

    /**
     * gets path to node from root, uses record::toString() method to get node names.
     * @param string $separator
     * @param bool $includeNode Default true
     * @return string 	
     */
    function getPath($seperator, $includeNode = true) {
        $captionFld = $this->_treeController->info('caption');
        $elms = array();
        $nodes = $this->_treeController->getAncestors($this->_id);
        foreach($nodes as $node) $elms[] = $node->getNodeName();
        if ($includeNode) $elms[] = $this->_data[$captionFld];
        return implode($seperator, $elms);
    }

    /**
     * gets record of prev sibling or empty record.
     * @return MyProject_Db_NestedTree_Node 	
     */
    function getPrevSibling() {
        return $this->_treeController->getPreviousSibling($this->_id);
    }

    /**
     * gets siblings for node.
     * @param bool includeNode
     * @return MyProject_Db_NestedTree_Nodes 	
     */
    function getSiblings($includeNode = true) {
        return $this->_treeController->getSiblings($this->_id, $includeNode);
    }

    /**
     * test if node has children.
     * @return bool 	
     */
    function hasChildren() {
        return ($this->getNumberDescendants($this->_id) > 0);
    }

    /**
     * test if node has next sibling.
     * @return bool 	
     */
    function hasNextSibling() {
        return (!is_null( $this->_treeController->getNextSibling($this->_id) ));
    }

    /**
     * test if node has parent.
     * @return bool 	
     */
    function hasParent() {
        return (!is_null($this->_treeController->getParent($this->_id)));
    }

    /**
     * test if node has previous sibling.
     * @retrun bool 	
     */
    function hasPrevSibling() {
        $data = $this->_treeController->getPreviousSibling($this->_id);
        return (!empty( $data ));
    }

    /**
     * inserts node as first child of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @return bool
     */
    function insertAsFirstChildOf($dest) {
        $this->_treeController->insertNode($this->_data, $dest, 'first');
        // insertNode($data, $dstNodeId = null, $pos = ' last')
    }

    /**
     * inserts node as first child of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @return bool
     */
    function insertAsLastChildOf($dest) {
        $this->_treeController->insertNode($this->_data, $dest, 'last');
    }

    /**
     * inserts node as next sibling of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @return bool 	
     */
    function insertAsNextSiblingOf($dest) {
        $this->_treeController->insertNode($this->_data, $dest, 'next');
    }

    /**
     * inserts node as parent of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @return bool 	
     */
    function insertAsParentOf($dest) {
        $this->_treeController->insertNode($this->_data, $dest, 'parent');
        return true;
    }

    /**
     * inserts node as previous sibling of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @param bool $withChilds Default true
     * @return bool 	
     */
    function insertAsPrevSiblingOf($dest) {
        $this->_treeController->insertNode($this->_data, $dest, 'prev');
    }

    /**
     * determines if node is child of subject node.
     * @param MyProject_Db_NestedTree_Node $subj
     * @return bool 	
     */
    function isDescendantOf( $subj ) {
        
        if (!($subj instanceof MyProject_Db_NestedTree_Node)) 
            throw new MyProject_Db_NestedTree_Exception('subj must be instance of MyProject_Db_NestedTree_Node!');
        
        $lft = $this->_treeController->info('left');
        $rgt = $this->_treeController->info('right');
        $d = $subj->getData();
        return ($d[$lft] < $this->_data[$lft] && $d[$rgt] > $this->_data[$rgt]);
    }

    /**
     * determines if node is child of or sibling to subject node.
     * @param MyProject_Db_NestedTree_Node $subj
     * @todo Not yet implemented
     * @return bool 	
     */
    function isDescendantOfOrEqualTo($subj) {
        return ($subj->getNodeId() == $this->_id || $this->isDescendantOf($subj));
    }

    /**
     * determines if node is equal to subject node.
     * @param MyProject_Db_NestedTree_Node $subj
     * @return bool 	
     */
    function isEqualTo($subj) {
        return ($subj->getNodeId() == $this->_id);
    }

    /**
     * determines if node is leaf.
     * @return bool 	
     */
    function isLeaf() {
        $lft = $this->_treeController->info('left');
        $rgt = $this->_treeController->info('right');
        return ($this->_data[$lft]+1 == $this->_data[$rgt]);        
    }

    /**
     * determines if node is root.
     * @return bool
     */
    function isRoot() {
        $data = $this->_treeController->getParent($this->_id);
        return (empty( $data ));
    }

    /**
     * determines if node is valid.
     * @todo Not yet implemented
     * @return bool
     */
    function isValidNode() {
        return $this->_treeController->_isValidNode($this);
    }

    /**
     * moves node as first child of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @param bool $withChilds Default true
     * @return void
     */
    function moveAsFirstChildOf($dest, $withChilds = true) {
        $this->_treeController->moveNode($this->_id, 'first', $dest, $withChilds);
    }

    /**
     * moves node as last child of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @param bool $withChilds Default true
     * @return void 	
     */
    function moveAsLastChildOf($dest, $withChilds = true) {
        $this->_treeController->moveNode($this->_id, 'last', $dest, $withChilds);
    }

    /**
     * moves node as next sibling of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @param bool $withChilds Default true
     * @return void 	
     */
    function moveAsNextSiblingOf($dest, $withChilds = true) {
        $this->_treeController->moveNode($this->_id, 'next', $dest, $withChilds);
    }

    /**
     * moves node as prev sibling of dest record.
     * @param MyProject_Db_NestedTree_Node $dest
     * @param bool $withChilds Default true
     * @return void
     */
    function moveAsPrevSiblingOf($dest, $withChilds = true) {
        $this->_treeController->moveNode($this->_id, 'prev', $dest, $withChilds);
    }
    
    public function __toString() {
        return $this->_data[$this->_caption];
    }
}





