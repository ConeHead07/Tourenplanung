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
        $format   = $rq->getParam('format', 'html');
        $aQueryParams = [
            'offset' => (int)$rq->getParam('offset', 0),
            'limit'  => (int)$rq->getParam('limit', 100),
            'lastModifiedFrom' => $rq->getParam('lastModifiedFrom', ''),
            'lastModifiedTo' => $rq->getParam('lastModifiedTo', ''),
            'tourId' => $rq->getParam('tourId', ''),
            'objectType' => $rq->getParam('objectType', ''),
            'objectId'   => $rq->getParam('objectId', ''),
            'objectName' => $rq->getParam('objectName', ''),
            'actionType' => $rq->getParam('actionType', ''),
        ];

        $this->view->disableLayout = true;

        $this->_helper->viewRenderer->setRender('grid');
    }



    public function listAction()
    {
        $rq = $this->getRequest();
        $format   = $rq->getParam('format', 'html');
        $aQueryParams = [
            'offset' => (int)$rq->getParam('offset', 0),
            'limit'  => (int)$rq->getParam('limit', 100),
            'lastModifiedFrom' => $rq->getParam('lastModifiedFrom', ''),
            'lastModifiedTo' => $rq->getParam('lastModifiedTo', ''),
            'tourId' => $rq->getParam('tourId', ''),
            'objectType' => $rq->getParam('objectType', ''),
            'objectId'   => $rq->getParam('objectId', ''),
            'objectName' => $rq->getParam('objectName', ''),
            'actionType' => $rq->getParam('actionType', ''),
        ];

        $modelLogger = new Model_TourenDispoLog();

        $gridConverter = new MyProject_Jqgrid_Converter();

        $result = $modelLogger->getHistorie( $aQueryParams );

        if ($format == 'json') {
            if (!$result) {
                $this->json->error('Historien-Einträge konnten nicht geladen werden!');
            }

            $numRows = count($result['rows']);

            $gridResult = $gridConverter::rowsToGridResult(
                $result['rows'],
                $result['total'],
                $result['offset'],
                $result['limit']
            );

            $this->_helper->json($gridResult);
        }

        $this->_helper->viewRenderer->setRender('list');
        $this->view->rows = $result['rows'];
    }



    public function griddataAction()
    {
        $rq = $this->getRequest();
        $format   = $rq->getParam('format', 'html');
        $aQueryParams = [
            'rows'  => (int)$rq->getParam('rows', 100),
            'page'  => (int)$rq->getParam('page', 100),
            'sortfld'  => $rq->getParam('sidx', 'action_time'),
            'sortdir'  => $rq->getParam('sord', 'DESC'),
            'lastModifiedFrom' => $rq->getParam('lastModifiedFrom', ''),
            'lastModifiedTo' => $rq->getParam('lastModifiedTo', ''),
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