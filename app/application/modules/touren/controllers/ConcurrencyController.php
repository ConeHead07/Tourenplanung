<?php 

class Touren_ConcurrencyController extends Zend_Controller_Action
{
    public function init() {
        parent::init();
        $this->_request = $this->getRequest();
    }
    
    public function checkAction()
    {
        $rq = $this->getRequest();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $checkTour      = (int)$rq->getParam('tour_id', '');
        $checkTimeline  = (int)$rq->getParam('timeline_id', '');
        $checkPortlet   = (int)$rq->getParam('portlet_id', '');
        $checkVorgang   = (int)$rq->getParam('Auftragsnummer', '');
        $checkMandant   = (int)$rq->getParam('Mandant', ($checkVorgang ? 10 : '') );
        $checkLager     = (int)$rq->getParam('lager_id', '');
        $checkMaxAge    = (int)$rq->getParam('maxage', 5);
        
        $checkUserId    = MyProject_Auth_Adapter::getUserId();
        
        $select = 'COUNT(1) ';
        $from   = 'mr_touren_dispo_aktivitaet A ' . PHP_EOL;
        $where  = 'A.user_id <> ' . $db->quote($checkUserId) . PHP_EOL
                  .'AND zugriffszeit >= DATE_ADD(NOW(), INTERVAL -'.$checkMaxAge.' MINUTE)' . PHP_EOL;
                  ;
        
        if ($checkTour)        $sql.= 'AND A.tour_id = ' . $db->quote($checkTour) . PHP_EOL;
        elseif ($checkTimeline)$sql.= 'AND timeline_id = :timeline_id ' . $db->quote($checkTimeline) . PHP_EOL;
        elseif ($checkPortlet) $sql.= 'AND portlet_id = :portlet_id ' . $db->quote($checkPortlet) . PHP_EOL;
        elseif ($checkLager)   $sql.= 'AND lager_id = :lager_id ' . $db->quote($checkLager) . PHP_EOL;
        
        if ($checkVorgang) {
            $from.= ' LEFT JOIN mr_touren_dispo_vorgaenge T ON (A.tour_id = T.tour_id)' . PHP_EOL;
            $where.= 'AND T.Auftragsnummer = ' . $db->quote($checkVorgang)
                    .'AND T.Mandant = ' . $db->quote($checkMandant) . PHP_EOL;
        }
        
        $sql = 'SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where;
        
        $CountActivities = (int)$db->fetchOne($sql);
        
        // Send Result direct as Json without use of MVC
        $this->_helper->json($CountActivities);
    }
    
    public function listAction() 
    {
        $rq = $this->getRequest();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $checkTour      = (int)$rq->getParam('tour_id', '');
        $checkTimeline  = (int)$rq->getParam('timeline_id', '');
        $checkPortlet   = (int)$rq->getParam('portlet_id', '');
        $checkVorgang   = (int)$rq->getParam('Auftragsnummer', '');
        $checkMandant   = (int)$rq->getParam('Mandant', ($checkVorgang ? 10 : '') );
        $checkLager     = (int)$rq->getParam('lager_id', '');
        $checkDatumVon     = (int)$rq->getParam('DatumVon', '');
        $checkMaxAge    = (int)$rq->getParam('maxage', 5);
        
        $checkUserId    = MyProject_Auth_Adapter::getUserId();
        
        $select = 'user_name, A.*';
        $from   = 'mr_touren_dispo_aktivitaet A ' . PHP_EOL
                 .'LEFT JOIN mr_user U ON(A.user_id = U.user_id) ' . PHP_EOL;
        
        $where  = '1 ' //A.user_id <> ' . $db->quote($checkUserId) . PHP_EOL
                  .'AND zugriffszeit >= DATE_ADD(NOW(), INTERVAL -'.$checkMaxAge.' MINUTE)' . PHP_EOL;
        ;
        
        if ($checkTour)         $sql.= 'AND A.tour_id = '   . $db->quote($checkTour)     . PHP_EOL;
        elseif ($checkTimeline) $sql.= 'AND timeline_id = ' . $db->quote($checkTimeline) . PHP_EOL;
        elseif ($checkPortlet)  $sql.= 'AND portlet_id = '  . $db->quote($checkPortlet)  . PHP_EOL;
        elseif ($checkLager)    $sql.= 'AND lager_id = '    . $db->quote($checkLager)    . PHP_EOL;
        elseif ($checkDatumVon) $sql.= 'AND DatumVon = '    . $db->quote($checkDatumVon) . PHP_EOL;
        
        if ($checkVorgang) {
            $from.= ' LEFT JOIN mr_touren_dispo_vorgaenge T ON (A.tour_id = T.tour_id)' . PHP_EOL;
            $where.= 'AND T.Auftragsnummer = ' . $db->quote($checkVorgang)
                    .'AND T.Mandant = ' . $db->quote($checkMandant) . PHP_EOL;
        }
        
        $sql = 'SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where;
        
        $ListActivities = $db->fetchAll($sql);
        
//        die( print_r($ListActivities, 1) . PHP_EOL . $sql);
        
        // Send Result direct as Json without use of MVC
        $this->_helper->json($ListActivities);
    }
}
