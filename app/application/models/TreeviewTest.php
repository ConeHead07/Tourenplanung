<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Treeview
 *
 * @author Administrator
 */
         
class Model_TreeviewTest extends MyProject_Model_Treeview {
    /* @var $db PDO */
    protected $db = null;
    protected $_storageName = 'treeview';
    
    public function __construct(PDO $db) 
    {
        /* @var $this->db PDO */
        $this->db = $db;
    }
    
    public function setup($table = '') 
    {
        if ($table) $this->setTable ($table);
        
        $this->_execute($this->getSqlCreateTable());
        
        $this->_execute($this->getSqlClearTable());
        $this->_execute($this->getSqlInsertTestData());
    }
    
    public function listNodes()
    {
        $this->_execute($this->getSqlNodesIndented());
    }
    
    public function getNodePath()
    {
        $this->_execute($this->getSQlQuerySinglePath());
    }
    
    public function list1stLevelChilds()
    {
        $this->_execute($this->getSqlImmediateSubsOfNode());
    }
    
    public function _toTable($array) {
//        echo '#' . __LINE__ . ' ' . print_r($array);
        echo '<table>' . PHP_EOL;
        foreach($array as $row) {
            echo '<tr>';
            echo '<td>' . implode('</td><td>', $row) . '</td>';
            echo '</tr>' . PHP_EOL;
        }
        echo '</table>' . PHP_EOL;
    }
    
    protected function _execute($sql)
    {
//        $sql = 'SHOW TABLE STATUS ';
        if (is_string($sql)) {
            /* @var $stmt PDOStatement */
            $stmt = $this->db->query($sql);
            echo '<hr/>' . __LINE__ . ' ' . $sql . PHP_EOL;
            if (method_exists($stmt, 'fetchAll')) {
                $this->_toTable($stmt->fetchAll(PDO::FETCH_NUM),1);
                echo '#' . __LINE__ . PHP_EOL;
            }
            echo $this->db->errorInfo();
            return;            
        }
        
        if (is_array($sql)) {
            echo '<hr/><ol>' . PHP_EOL;
            foreach($sql as $q) {
                $stmt = $this->db->query($q);
                echo '<hr/>' . __LINE__ . ' ' . $q . PHP_EOL;
                if (method_exists($stmt, 'fetchAll')) {
                    $this->_toTable($stmt->fetchAll(PDO::FETCH_NUM),1);
                    echo '#' . __LINE__ . PHP_EOL;
                }
            }
            echo '</ol><hr/>' . PHP_EOL;
        }
    }
    
    public function addNode($parentNode = null)
    {
        if (!$parentNode) {
            $this->_execute($this->getSqlAddNode());
        } else {
            $this->_execute($this->getSqlAddNodeToChild());
        }
    }
    
    public function deleteNode($withChilds = true) 
    {
        if ($withChilds) {
            $this->_execute($this->getSqlDeleteNodeWithoutChilds());
        } else {
            $this->_execute($this->getSqlDeleteNodeWithChilds());
        }
    }
}
