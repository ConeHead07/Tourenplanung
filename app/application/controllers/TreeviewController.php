<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TreeviewController
 *
 * @author Administrator
 */
class TreeviewController extends Zend_Controller_Action {
    //put your code here
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }
    
    public function listAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
        $this->view->treelist = $treeview->fetchTree();
        return;   
    }
    
    public function indexAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
//        ELECTRONICS 
//        -TELEVISIONS 
//        --TUBE 
//        --LCD 
//        --PLASMA 
//        -CD PLAYERS 
//        -2 WAY RADIOS 
//        --FRS 
//        AddNode 
        $this->view->nodePositions = array(
            Model_Treeview::FIRST_CHILD  => 'Als erster Unterpunkt',
            Model_Treeview::LAST_CHILD   => 'Als letzter Unterpunkt',
            Model_Treeview::PREV_SIBLING => 'Davor',
            Model_Treeview::NEXT_SIBLING => 'Danach'
        );
        
        $this->view->treelist = $treeview->fetchTree();
        return;
        $treeview->addNode('COOKING2', 'TELEVISIONS');
        
        
        $treeview->addChild('ElecChild', 'ELECTRONICS');
        
        $treeview->deleteNodeByName('2 WAY RADIOS', $withChilds=true);
        
        $treeview->deleteNodeByName('TELEVISIONS', $withChilds=false);
        
        $this->view->treelist[] = $treeview->getDepthOfSubTree('FRS');
        
        $this->view->treelist[] = $treeview->getSinglePath('FRS');
        
        $this->view->treelist[] = $treeview->getImmediateSubsOfNode('ELECTRONICS');
        
        $this->view->treelist[] = $treeview->getNodesDepth();
        
        $this->view->treelist[] = $treeview->fetchTree();
        
//        $this->view->treelist = $treeview->fetchTree();
        
//        $this->view->treelist = $treeview->fetchEntries();
    }
    
    public function addAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
        
        $newNodeName  = $this->getRequest()->getParam('CName', null);
        $newNodeRefId = $this->getRequest()->getParam('CRefId', null);
        $newNodeToPos = $this->getRequest()->getParam('CPos', Model_Treeview::LAST_CHILD);
        
        if (!$treeview->checkNodePosition($newNodeToPos)) {
            throw new Exception('Invalid Position for addAction!');
        }
            
        if (!$newNodeName) {
            throw new Exception('Missing NodeName for addAction!');
            return false;
        }
        
        $nameCol = $treeview->getNameFld();
        
        $treeview->insertNodeByPosition(
                array($nameCol=>$newNodeName), 
                $newNodeRefId, 
                $newNodeToPos);
        
        $this->_forward('index');
    }
    
    public function delAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
        
        $nodeId = $this->getRequest()->getParam('CId', NULL);
        $withChilds = (bool)$this->getRequest()->getParam('withChilds', false);
        
        $treeview->deleteNode($nodeId, $withChilds);
        
        $this->_forward('index');
    }
    
    public function moveAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
        
        $nodeId    = $this->getRequest()->getParam('CId', null);
        $nodeRefId = $this->getRequest()->getParam('CRefId', null);
        $nodeToPos = $this->getRequest()->getParam('CPos', Model_Treeview::LAST_CHILD);
        
        if (!$treeview->checkNodePosition($nodeToPos)) {
            throw new Exception('Invalid Position for moveAction!');
        }
        
        $treeview->updateNodeByPosition(array(), $nodeId, $nodeRefId, $nodeToPos);
        
        $this->_forward('index');
    }
    public function movetreeintoAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
        
        $nodeId    = $this->getRequest()->getParam('CId', null);
        $nodeRefId = $this->getRequest()->getParam('CRefId', null);
        $nodeToPos = $this->getRequest()->getParam('CPos', Model_Treeview::LAST_CHILD);
        
        $treeview->moveTreeInto($nodeId, $nodeRefId);
        
        $this->_forward('index');
    }
    
    public function movetreebeforetoAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
        
        $nodeId    = $this->getRequest()->getParam('CId', null);
        $nodeRefId = $this->getRequest()->getParam('CRefId', null);
        
        $treeview->moveTreeBeforeTo($nodeId, $nodeRefId);
        
        $this->_forward('index');
    }
    
    public function renameAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('treeview');
        
        $nodeId      = $this->getRequest()->getParam('CId', null);
        $newNodeName = $this->getRequest()->getParam('CName', null);
        
        $treeview->update(array($treeview->getNameFld()=>$newNodeName), $nodeId);
        
        $this->_forward('index');
    }

    public function restoreAction()
    {
        /* @var $treeview Model_Treeview */
        $model = MyProject_Model_Database::loadModel('treeview');
        
        $tableInfos = $model->getStorage()->info();
        $tableName = $model->getStorage()->info('name');
//        echo '<pre>' . print_r($tableName, 1) . '</pre>';
//        echo '<pre>' . print_r($tableInfos, 1) . '</pre>';
//        $conn = mysql_connect('localhost', 'root');
//        $selectdb = mysql_select_db('phpproject');
        
        /* @var $db Zend_Db_Adapter_Pdo_Mysql */
        $db = Zend_Registry::get('db');
        
        $sql = array();
        $sql[] =
            'CREATE TABLE IF NOT EXISTS ' . $db->quoteIdentifier($tableName) . ' ( '
           .'category_id INT AUTO_INCREMENT PRIMARY KEY, '
           .'name VARCHAR(20) NOT NULL, '
           .'lft INT NOT NULL, '
           .'rgt INT NOT NULL '
           .')';
        
        $sql[] = 
            'TRUNCATE ' . $db->quoteIdentifier($tableName) . '';
        
        $sql[] = 
            'INSERT INTO ' . $db->quoteIdentifier($tableName) . ' '
           .'(category_id, name, lft , rgt) '
           .'VALUES(1,\'ELECTRONICS\',1,20) '
           .',(2,\'TELEVISIONS\',2,9) '
           .',(3,\'TUBE\',3,4) '
           .',(4,\'LCD\',5,6) '
           .',(5,\'PLASMA\',7,8) '
           .',(6,\'PORTABLE ELECTRONICS\',10,19) '
           .',(7,\'MP3 PLAYERS\',11,14) '
           .',(8,\'FLASH\',12,13) '
           .',(9,\'CD PLAYERS\',15,16)'
           .',(10,\'2 WAY RADIOS\',17,18) ';
        
        foreach($sql as $query) {
            echo '#' . __LINE__ . ' query: ' . $query . "<br>\n";
            /* @var $stmt Zend_Db_Statement_Pdo */
            $stmt = $db->query($query);
            if (intval($stmt->errorCode())) {
                echo '#' . __LINE__ . ' errorInfo:' . print_r($stmt->errorInfo(), 1) . "<br>\n";
                echo '#' . __LINE__ . ' errorCode:' . print_r($stmt->errorCode(), 1) . "<br>\n";
                exit;
            }
        }
        
        $this->_forward('index');
    }
}

