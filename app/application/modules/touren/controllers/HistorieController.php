<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 15.08.2019
 * Time: 16:25
 */

class Touren_HistorieController extends Zend_Controller_Action
{

    public function gridAction()
    {
        // $this->_helper->layout()->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(true);

        $rq = $this->getRequest();
        $lager_id   = $rq->getParam('lager_id', 0);
        $date   = $rq->getParam('date', '');
        if ($date && is_string($date) && ($t = strtotime($date))) {
            $datumVon = date('Y-m-d', $t);
        } else {
            $datumVon = date('Y-m-d');
        }
        $dateBis   = $rq->getParam('dateBis', '');
        if ($dateBis && is_string($dateBis) && ($tb = strtotime($dateBis))) {
            $datumBis = date('Y-m-d', $tb);
        } else {
            $datumBis = $datumVon;
        }

        $this->view->disableLayout = true;
        $this->view->lager_id = $lager_id;
        $this->view->datumVon = $datumVon;
        $this->view->datumBis = $datumBis;

        $this->_helper->viewRenderer->setRender('grid');
    }

    public function griddataAction()
    {
        $rq = $this->getRequest();

        $aQueryParams = [
            'rows'  => (int)$rq->getParam('rows', 100),
            'page'  => (int)$rq->getParam('page', 100),
            'sortfld'  => $rq->getParam('sidx', 'action_time'),
            'sortdir'  => $rq->getParam('sord', 'DESC'),
            'lastModifiedFrom' => $rq->getParam('lastModifiedFrom', ''),
            'lastModifiedTo' => $rq->getParam('lastModifiedTo', ''),
            'datumVon' => $rq->getParam('datumVon', date('Y-m-d')),
            'datumBis' => $rq->getParam('datumBis', date('')),
            'lager_id' => $rq->getParam('lager_id', ''),
        ];
        $aQueryParams['search'] = [];

        $gridFilters = $rq->getParam('filters', '{}');
        $aGridFilter = json_decode($gridFilters);
        if (isset($aGridFilter->rules)) foreach($aGridFilter->rules as $_rule) {
            $_fld = $_rule->field;
            $_op = $_rule->op;
            $_qy = $_rule->data;

            switch($_fld) {
                case 'id':
                case 'tour_id':
                case 'object_type':
                case 'object_id':
                case 'resource':
                case 'action':
                case 'user_id':
                case 'user':
                case 'action_time':
                case 'tour_anr':
                case 'dispo_datum':
                case 'dispo_zeit_von':
                case 'dispo_zeit_bis':
                case 'bemerkung':
                $aQueryParams['search'][$_fld] = $_qy;  break;
            }
        }

        $modelLogger = new Model_TourenDispoLog();

        $gridConverter = new MyProject_Jqgrid_Converter();

        $result = $modelLogger->getHistorie( $aQueryParams );

        if (!$result) {
            $this->json->error('Historien-Einträge konnten nicht geladen werden!');
        }

        $gridResult = $gridConverter::rowsToGridResult(
            $result['rows'],
            $result['total'],
            $result['offset'],
            $result['limit']
        );

        $this->_helper->json($gridResult);
    }

    public function sidebarAction()
    {
    }

}