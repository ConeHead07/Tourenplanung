<?php 
//namespace MyProject\Db\NestedTree;

class MyProject_Db_NestedTree_Nodes implements RecursiveIterator, SeekableIterator, Serializable, Countable 
{
    protected $_data;
    protected $_position = 0;
    protected $_count = 0;

    /** @var $_treeMyProject_Db_NestedTree_Controller*/
    protected $_treeController = null;

    public function __construct(array $data,MyProject_Db_NestedTree_Controller$treeController) {
        $this->_data = $data;
        $this->_count = count($data);
        $this->_treeController = $treeController;
    }

    // Implement Countable
    public function count()     { 
        return $this->_count; }
        
    // Implement Serializable
    public function serialize() { 
        return serialize($this->_data); }
        
    public function unserialize($_data) 
        { $this->_data = unserialize($_data); }
    
    public function get_data()  
        { return $this->_data; }

    // Implement SeekableIterator {
    public function seek($_position) {
        $this->_position = $_position;
        if (!$this->valid()) throw new OutOfBoundsException("invalid seek _position ($_position)");
    }

    // Implement SeekableIterator
    function rewind()   { $this->_position = 0; }
    function current()  { 
        return new MyProject_Db_NestedTree_Node($this->_data[$this->_position], $this->_treeController); 
    }
    function key()      { return $this->_position; }
    function next()     { ++$this->_position; }
    function valid()    { return isset($this->_data[$this->_position]); }

    // Implement RecursiveIterator
    public function getChildren() {
        $class = __CLASS__;
        return new $class(
                $this->_treeController->getChildrenList($this->_data[$this->_position]),
                $this->_treeController
            );
    }

    /**
     * @return bool
     */
    public function hasChildren() {
        return $this->_treeController->hasChildrenByNodeData($this->_data[$this->_position]);
    }
    
    public function __toString() {
        $s = '';
        $_caption = $this->_treeController->info('caption');
        for($i = 0; $i < count($this->_data); ++$i) {
            $s.= ($i?', ':'') . $this->_data[$i][$_caption];
        }
        return $s;
    }
}
