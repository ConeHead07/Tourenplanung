<?php

class Touren_WwsAjaxController extends Zend_Controller_Action 
{
    
    public function abholungenlisteAction()
    {
        $rq = $this->getRequest();
        /* @var $db Zend_Db_Adapter_Sqlsrv */
        $db = Zend_Registry::get('wwsdb');
        
        $name = $rq->getParam('name');
        
        $error = '';
        
        try {
            $sql = $db->select()
             ->from( 
                array('K' => 'Kontakte'),
                array('Mandant','KontaktNummer','Kurzname','IstLieferant','Hauptanschrift')
            )->join(
                array('A' => 'Anschriften'),
                'K.IstLieferant = 1 AND K.HauptAnschrift = A.Anschriftsnummer',
                array('Name','Strasse','Postleitzahl','Ort','OrtPostfach','Land')
            )->where(
                'K.Kurzname LIKE ?', "$name%"
            )->orWhere(
                'A.Name LIKE ?', "$name%" 
            )->limit(15);
            
        
            //echo '#' . __LINE__ . ' <pre>sql: ' . (string)$sql . '</pre>' . PHP_EOL;
            $rows = $db->fetchAll($sql, array(), Zend_Db::FETCH_ASSOC);
            
            $this->_helper->json(array(
                'type'    => 'success',
                'success' => true,
                'rows'    => $rows
            ));
            
        } catch(Zend_Db_Exception $e) {
            $error = $e->getMessage() . '<br/>' . PHP_EOL;
            echo $e->getTraceAsString() . '<br/>' . PHP_EOL;
        }
        
        $this->_helper->json(array(
            'type'    => 'error',
            'success' => false,
            'error'   => $error,
            'items'   => null,
        ));
    }
}
?>
