<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author rybka
 */
class testsController extends Zend_Controller_Action {
    
    public function init() 
    {
        echo '<div>Tests</div>' . PHP_EOL;
        echo '<pre>';
    }
    
    public function dispoqueriesAction()
    {
        //put your code here
        $this->view->layout()->disableLayout();
        
        /* @var $modelPtl Model_Db_TourenPortlets */
        $modelPtl = MyProject_Model_Database::loadModel('tourenPortlets');
        /* @var $modelTml Model_Db_TourenTimelines */
        $modelTml = MyProject_Model_Database::loadModel('tourenTimelines');
        /* @var $modelDpV Model_Db_TourenDispoVorgaenge */
        $modelDpV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');
        /* @var $modelDpR Model_Db_TourenDispoResources */
        $modelDpR = MyProject_Model_Database::loadModel('tourenDispoResources');
        
        
        $sqlCleanTbl = array(
            $modelDpR, $modelDpV, $modelPtl, $modelTml
        );
        
        $db = $modelPtl->getStorage()->getAdapter();
        
        /* @var $_model MyProject_Model_Database */
        foreach($sqlCleanTbl as $_model) {            
            $cleanedRows = $_model->getStorage()-> delete('1 > 0');
            $stmt = $db->query('truncate table ' . $_model->getStorage()->info( Zend_Db_Table::NAME));
            $stmt->execute();
            echo get_class($_model) . ' deleted ' . $cleanedRows . ' rows!' . PHP_EOL;
        }
        
        $date = date('Y-m-d');
        for($iPtl = 1; $iPtl < 5; ++$iPtl) {
            // ADD A PORTLET
            $ptlID = $modelPtl->insert(array(
                'datum' => $date,
                'position' => $iPtl
            ));
            
            for($iTml = 1; $iTml < 4; ++$iTml) {
                
                        
                $tmlID = $modelTml->insert(
                    array(
                        'portlet_id' => $ptlID,
                        'position' => $iTml,
                        'locked' => 0,
                        'start' => '08:00',
                        'end' => '22:00',
                        'minutes_interval' => 30
                    )
                );
                
                for($iDpv = 1; $iDpv < 6; ++$iDpv) {
                    
                    $anr = '9'.$ptlID.$tmlID.'1' . $iDpv; 
                    $dpvID = $modelDpV->insert(
                        array(
                            'Mandant' => 10,
                            'Auftragsnummer' => $anr,
                            'timeline_id' => $tmlID,
                            'DatumVon' => $date,
                            'ZeitVon' => substr('0'.(($iTml*2)+7),-2).':00',
                            'DatumBis' => $date,
                            'ZeitBis' => substr('0'.(($iTml*2)+9),-2).':00',
                        )
                    );
                    echo get_class($modelDpR) . PHP_EOL;
                    for($iDpr = 1; $iDpr < 8; ++$iDpr) {
                        $r_id = $ptlID.$tmlID . $iDpv . $iDpr;
                        try {
                            $sql = 'INSERT INTO ' . $db->quoteIdentifier(
                                    'mr_touren_dispo_resources'
                            ). ' (resource_id, Mandant, Auftragsnummer) VALUES ' . PHP_EOL
                            .'  (' . $db->quote($r_id) . ',' . $db->quote(10). ',' . $db->quote($anr) . ')';
                            $db->query($sql)->execute();
                               
                        } catch(Exception $e) {
                            echo $sql . PHP_EOL;
                            echo $e->getMessage();
                        }
                    }
                }
            }
        }
        exit;
    }
    
    public function dispomoveportletsAction()
    {
        //put your code here
        $this->view->layout()->disableLayout();
                
        /* @var $modelPtl Model_TourenPortlets */
        $modelPtl = MyProject_Model_Database::loadModel('tourenPortlets');
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $modelPtl->getStorage()->getAdapter();
        
        $select = $modelPtl->getStorage()->select()->order('position');
        /* @var $stmt Zend_Db_Statement_Interface */
        
        /* @var $rq Zend_Controller_Request_Abstract */
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        
        $rows = $db->query($select)->fetchAll();
        $id = $rq->getParam('id', $rows[0]['portlet_id']);
        $pos= $rq->getParam('pos', 'last');
        
        echo $this->arrayToTable($rows);
        $modelPtl->movePosition($id, $pos);
        echo $this->arrayToTable($db->query($select)->fetchAll());
        
        echo $this->view->action('index', 'dbprofiler');
        die();
    }
    
    public function dispomovetimelinesAction()
    {
        //put your code here
        $this->view->layout()->disableLayout();
                
        /* @var $modelPtl Model_TourenPortlets */
        $model = MyProject_Model_Database::loadModel('tourenTimelines');
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $model->getStorage()->getAdapter();
        
        $select = $model->getStorage()->select()->order('position')
                ->where('portlet_id = ?', 1);
        /* @var $stmt Zend_Db_Statement_Interface */
        
        /* @var $rq Zend_Controller_Request_Abstract */
        $rq = Zend_Controller_Front::getInstance()->getRequest();
        
        $rows = $db->query($select)->fetchAll();
        $id = $rq->getParam('id', $rows[0]['timeline_id']);
        $pos= $rq->getParam('pos', 'last');
        
        echo $this->arrayToTable($rows);
        $model->movePosition($id, $pos);
        echo $this->arrayToTable($db->query($select)->fetchAll());
        
//      echo $this->view->action('index', 'dbprofiler');
        die();
    }
    
    public function arrayToTable($array)
    {
        $colsHeaderRendered = false;
        $t = '';
//      echo '#' . __LINE__ . ' ' . print_r($array);
        $t.= '<table cellpadding=2 cellspacing=0 border=1>' . PHP_EOL;
        foreach($array as $row) {
            if (!$colsHeaderRendered) {
                $t.= '<tr>';
                foreach($row as $fld => $val)
                    $t.= '<th>' . $fld . '</th>';
                $t.= '</tr>' . PHP_EOL;
                $colsHeaderRendered = true;
            }
            $t.= '<tr>';
            foreach($row as $fld => $val)
                $t.= '<td>' . $val . '</td>';
            $t.= '</tr>' . PHP_EOL;
        }
        $t.= '</table>' . PHP_EOL;
        return $t;
    }
    
    public function __destruct() {
        echo '</pre>';
        die();
    }
}

?>
