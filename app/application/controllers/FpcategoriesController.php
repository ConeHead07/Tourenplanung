<?php

/**
 * @author Administrator
 */
class FpcategoriesController extends Zend_Controller_Action {
    //put your code here
    
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }
   
    public function autocompleteAction()
    {
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        $term = $rq->getParam('term', '');
        $trunc = $rq->getParam('trunc', 'both');
        
//        if ($nodeId > 0) $level+= 1;
        /* @var $treeview Model_FuhrparkCategories */
        $treeview = MyProject_Model_Database::loadModel('fuhrparkCategories');
        $this->view->autocomplete = $treeview->query($term, $trunc);  
        
    }
    
    public function listAction()
    {
        /* @var $treeview Model_Treeview */
        // http://localhost/jqgrid_demo40/jqgrid_demo40/server.php?q=tree
        //&_search=false
        //&nd=1329410106579
        //&rows=20&page=1
        //&sidx=
        //&sord=asc
        //&nodeid=1
        //&n_left=1
        //&n_right=8
        //&n_level=0
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        $op = $rq->getParam('oper', 'list');
        $nodeId = $rq->getParam('nodeid', 0);
        $level = $rq->getParam('n_level', 0);
        $left = $rq->getParam('n_left', 0);
        $right = $rq->getParam('n_right', 0);
        $showall = $rq->getParam('showall', '');
        
//        if ($nodeId > 0) $level+= 1;
        $treeview = MyProject_Model_Database::loadModel('fuhrparkCategories');
        if ($showall) {
            $this->view->treelist = $treeview->getTreeData('list', $nodeId);
            $this->view->defaultExpanded = true;
        } else {
//        die( '<pre>#' . __LINE__ . ' ' . __METHOD__ . ' treedata: ' . print_r($treedata,1) . '</pre>');
            $this->view->treelist = $treeview->getImmediateGridSubs($nodeId, $level, $left, $right);
        }
        return;   
    }
    
    public function nodeAction()
    {
        /* @var $rq Zend_Controller_Request_Abstract */
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        
        $id = $rq->getParam('id', '_empty');
        $oper = $rq->getParam('oper', 'load');
        $parent_id = $rq->getParam('parent_id', null);
        $pos = $rq->getParam('pos', 'last');
        
        $data = array();
        foreach($_REQUEST as $k => $v) {
            switch($k) {
                case 'id':
                case 'jsonReader':
                case 'oper':
                case 'parent_id':
                case 'pos':
                    break;
                
                default:
                    $data[$k] = $v;
            }
        }
        
        /* @var $treeview Model_FuhrparkCategories */
        $treeview = MyProject_Model_Database::loadModel('fuhrparkCategories');
        
        switch($oper) {
            case 'edit':
                // name TELEVISIONS2
                $treeview->update($data, $id);
                die('#' . __LINE__ . ' oper: ' . $oper);
                break;
            
            case 'add':
            // id	_empty
            // name	test
            // oper	add
            // parent_id	2
//                die('#' . __LINE__ . ' oper: ' . $oper . ' insertNode('.print_r($data,1).','.$parent_id.','.$pos.')');
                $id = $treeview->insertNode($data, $parent_id, $pos);
                break;
            
            case 'del':
                $withChilds = $rq->getParam('withchilds', false);
                if ($withChilds == 'false') $withChilds = false;
                //die('#' . __LINE__ . ' oper: ' . $oper . ' removeNode('.$id.','.print_r($withChilds,1).')');
                $treeview->removeNode($id, $withChilds);
                break;
            
            default:
                echo '#' . __LINE__ . ' oper: ' . $oper;
        }
        
        $nodeData = ($id) ? $treeview->getNodeData($id) : NULL;
        
        $this->view->node = $nodeData;
        return;
        $this->view->debugData = array(
            'params' => array(
                'id' => $id,
                'name' => $name,
                'oper' => $oper,
                'parent_id' => $parent_id,
                'pos' => $pos,
            ),
            'request' => $_REQUEST
        );
    }
    
    public function indexAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('fuhrparkCategories');
        $this->view->treelist = $treeview->getTreeData();
        //die('#' . __LINE__ . ' ' . __FILE__ );
    }
    
    public function selectdialogAction()
    {
        /* @var $treeview Model_Treeview */
        $treeview = MyProject_Model_Database::loadModel('fuhrparkCategories');
        $this->view->treelist = $treeview->getTreeData();
        $this->view->SelectTreeId = $this->getRequest()->getParam('treeID', null);
    }
    
    public function selecttreelistAction()
    {
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        $rootId = $rq->getParam('root', '');
        
        /* @var $treeview Model_WerkzeugCategories */
        $treeview = MyProject_Model_Database::loadModel('fuhrparkCategories');
        $this->view->treelist = $treeview->getTreeData();  
    }
    
    public function addAction()
    {
    }
    
    public function delAction()
    {
    }
    
    public function moveAction()
    {
    }
    public function movetreeintoAction()
    {
    }
    
    public function movetreebeforetoAction()
    {
    }
    
    public function renameAction()
    {
    }

    public function restoreAction()
    {
        $this->_forward('index');
    }
}

