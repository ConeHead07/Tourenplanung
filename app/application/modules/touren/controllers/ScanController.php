<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 02.05.2019
 * Time: 11:35
 */

class Touren_ScanController extends Zend_Controller_Action
{

    public function potentialConflictsAction()
    {
        $rq = $this->getRequest();
        $sMonth = $rq->getParam('month', '');
        $sFrom = $rq->getParam('from', '');
        $sBis = $rq->getParam('to', '');

        $from = '';
        $bis = '';

        $validator = new MyProject_Validate_Date();

        if ( $sMonth && $validator->isValid("$sMonth-01") ) {
            if (preg_match('#^(\d{4})-(\d{1,2})$#', $sMonth, $m)) {
                $iMonthTime = strtotime("$sMonth-01");
                $iDaysOfMonth = date('t', $iMonthTime);
                $from = date('Y-m-d', strtotime("$sMonth-01"));
                $bis = date('Y-m-d', strtotime("$sMonth-$iDaysOfMonth"));
            }
        }

        if ( $sFrom && $sBis && $validator->isValid($sFrom) && $validator->isValid($sBis)) {
            if (strtotime($sFrom) < strtotime($sBis) ) {
                $from = date('Y-m-d', strtotime($sFrom));
                $bis = date('Y-m-d', strtotime($sBis));
            }
        }

        if (!$from || !$bis) {
            return $this->sendJsonError( 'Invalid Date-Range-Params! Use month/YYYY-MM OR from/YYYY-MM-DD/to/YYYY-MM-DD' );
        }

        $db = Zend_Db_Table::getDefaultAdapter();

        $sqlScanMA = MyProject_Helper_String::stripMargin(<<<EOT
                |select
                |	p.datum,
                |	tma.mitarbeiter_id,
                |	ma.name,
                |	ma.extern_id
                |	, count(distinct( p.portlet_id )) num_portlets
                |	, count(distinct( t.tour_id )) num_tours
                |	, count(distinct( t.timeline_id )) num_timelines
                | FROM mr_touren_portlets AS p
                | LEFT JOIN mr_touren_timelines AS tl ON (p.portlet_id = tl.portlet_id)
                | LEFT JOIN mr_touren_dispo_vorgaenge t ON (tl.timeline_id = t.timeline_id)
                | LEFT JOIN mr_touren_dispo_mitarbeiter tma ON (t.tour_id = tma.tour_id)
                | LEFT JOIN mr_mitarbeiter ma ON (tma.mitarbeiter_id = ma.mid)
                | WHERE p.datum BETWEEN :from AND :bis
                |    AND mitarbeiter_id IS NOT NULL
                | GROUP BY p.datum, tma.mitarbeiter_id, ma.name, ma.extern_id
                | HAVING count(distinct( t.timeline_id )) > 1
EOT
        );

        $sqlScanFP = MyProject_Helper_String::stripMargin(<<<EOT
                |select
                |	p.datum,
                |	tfp.fuhrpark_id,
                |	CONCAT(fp.kennzeichen, ' ', fp.hersteller, ' ', fp.modell, ' ', fp.fahrzeugart) AS fahrzeug,
                |	fp.extern_id
                |	, count(distinct( p.portlet_id )) num_portlets
                |	, count(distinct( t.tour_id )) num_tours
                |	, count(distinct( t.timeline_id )) num_timelines
                | FROM mr_touren_portlets AS p
                | LEFT JOIN mr_touren_timelines AS tl ON (p.portlet_id = tl.portlet_id)
                | LEFT JOIN mr_touren_dispo_vorgaenge t ON (tl.timeline_id = t.timeline_id)
                | LEFT JOIN mr_touren_dispo_fuhrpark tfp ON (t.tour_id = tfp.tour_id)
                | LEFT JOIN mr_fuhrpark fp ON (tfp.fuhrpark_id = fp.fid)
                | WHERE p.datum BETWEEN :from AND :bis
                |    AND fuhrpark_id IS NOT NULL
                | GROUP BY p.datum, tfp.fuhrpark_id, fp.extern_id, fp.kennzeichen, fp.hersteller, fp.modell, fp.fahrzeugart
                | HAVING count(distinct( t.timeline_id )) > 1
EOT
        );

        $aScanMA = $db->fetchAll($sqlScanMA, [
            'from' => $from,
            'bis' => $bis,
        ], Zend_Db::FETCH_ASSOC);

        $aScanFP = $db->fetchAll($sqlScanFP, [
            'from' => $from,
            'bis' => $bis,
        ], Zend_Db::FETCH_ASSOC);

        MyProject_Response_Json::send([
            'Mitarbeiter' => $aScanMA,
            'Fuhrpark' => $aScanFP,
        ]);
    }

    public function lostMaLinksAction(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $sqlScan = MyProject_Helper_String::stripMargin(<<<EOT
                 |SELECT
                 |    tr.id, r.name, tr.tour_id, t.tour_id t_tour_id, t.DatumVon, 
                 |    t.timeline_id, l.timeline_id l_timeline_id, l.portlet_id, 
                 |    p.portlet_id p_portlet_id
                 | FROM mr_touren_dispo_mitarbeiter tr
                 | LEFT JOIN mr_touren_dispo_vorgaenge t USING(tour_id) 
                 | LEFT JOIN mr_touren_timelines l USING(timeline_id) 
                 | LEFT JOIN mr_touren_portlets p ON(l.portlet_id = p.portlet_id) 
                 | LEFT JOIN mr_mitarbeiter r ON(tr.mitarbeiter_id = r.mid) 
                 | WHERE p.portlet_id is null
EOT
        );

        $aScan = $db->fetchAll($sqlScan, [], Zend_Db::FETCH_ASSOC);

        MyProject_Response_Json::send([
            'count' => count($aScan),
            'Mitarbeiter' => $aScan,
        ]);
    }

    public function lostFpLinksAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sqlScan = MyProject_Helper_String::stripMargin(<<<EOT
                 |SELECT
                 |    tr.id, r.kennzeichen, r.extern_id, tr.tour_id, t.tour_id t_tour_id, t.DatumVon, 
                 |    t.timeline_id, l.timeline_id l_timeline_id, l.portlet_id, 
                 |    p.portlet_id p_portlet_id
                 | FROM mr_touren_dispo_fuhrpark tr
                 | LEFT JOIN mr_touren_dispo_vorgaenge t USING(tour_id) 
                 | LEFT JOIN mr_touren_timelines l USING(timeline_id) 
                 | LEFT JOIN mr_touren_portlets p ON(l.portlet_id = p.portlet_id) 
                 | LEFT JOIN mr_fuhrpark r ON(tr.fuhrpark_id = r.fid) 
                 | WHERE p.portlet_id is null
EOT
        );

        $aScan = $db->fetchAll($sqlScan, [], Zend_Db::FETCH_ASSOC);

        MyProject_Response_Json::send([
            'count' => count($aScan),
            'Fuhrpark' => $aScan,
        ]);
    }

    public function lostWzLinksAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sqlScan = MyProject_Helper_String::stripMargin(<<<EOT
                 |SELECT
                 |    tr.id, r.bezeichnung, r.extern_id, tr.tour_id, t.tour_id t_tour_id, t.DatumVon, 
                 |    t.timeline_id, l.timeline_id l_timeline_id, l.portlet_id, 
                 |    p.portlet_id p_portlet_id
                 | FROM mr_touren_dispo_werkzeug tr
                 | LEFT JOIN mr_touren_dispo_vorgaenge t USING(tour_id) 
                 | LEFT JOIN mr_touren_timelines l USING(timeline_id) 
                 | LEFT JOIN mr_touren_portlets p ON(l.portlet_id = p.portlet_id) 
                 | LEFT JOIN mr_werkzeug r ON(tr.werkzeug_id = r.wid) 
                 | WHERE p.portlet_id is null
EOT
        );

        $aScan = $db->fetchAll($sqlScan, [], Zend_Db::FETCH_ASSOC);

        MyProject_Response_Json::send([
            'count' => count($aScan),
            'Fuhrpark' => $aScan,
        ]);
    }

    public function lostTourLinksAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sqlScan = MyProject_Helper_String::stripMargin(<<<EOT
                 |SELECT 
                 |    t.*
                 | FROM mr_touren_dispo_vorgaenge t 
                 | LEFT JOIN mr_touren_timelines l USING(timeline_id) 
                 | WHERE l.timeline_id is null
EOT
        );

        $aScan = $db->fetchAll($sqlScan, [], Zend_Db::FETCH_ASSOC);

        MyProject_Response_Json::send([
            'count' => count($aScan),
            'Touren' => $aScan,
        ]);
    }

    public function lostTimelineLinksAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sqlScan = MyProject_Helper_String::stripMargin(<<<EOT
                 |SELECT 
                 |    p.portlet_id p_portlet_id, l.*
                 | FROM mr_touren_timelines l
                 | LEFT JOIN  mr_touren_portlets p USING(portlet_id) 
                 | WHERE p.portlet_id is null
EOT
        );

        $aScan = $db->fetchAll($sqlScan, [], Zend_Db::FETCH_ASSOC);

        MyProject_Response_Json::send([
            'count' => count($aScan),
            'Timelines' => $aScan,
        ]);
    }



    /**
     * Compare open Vorgaenge in App with WWS, to find allready closed or removed Items
     * and update APP-Items
     * Note:
     * - it updates only the status of App-Items by re-checking their Status by it's according WWS-Items
     * - it does not remove Items in App, just set the status + 100, to close it and remember old status
     * - it does not add new Item, this is part of cron-jobs
     *
     */
    public function unsyncedStatusAction()
    {

        header('X-Accel-Buffering: no');
        $this->_helper->layout->disableLayout();
        ob_end_clean();

        echo "Start Diff of open App and WWS-Items ...<br>\n";

        $sync = new app\library\MyProject\Wwssync\Bearbeitungsstatus();

        $aStatus = $sync
            ->runDiff()
            ->getProcessStatus()
        ;

        echo '<pre>'
            . json_encode(
                [ 'Status' => $aStatus, ],
                JSON_PRETTY_PRINT
            )
            . '</pre>';
        exit;
    }

    /**
     * Compare open Vorgaenge in App with WWS, to find allready closed or removed Items
     * and update APP-Items
     * Note:
     * - it updates only the status of App-Items by re-checking their Status by it's according WWS-Items
     * - it does not remove Items in App, just set the status + 100, to close it and remember old status
     * - it does not add new Item, this is part of cron-jobs
     *
     */
    public function resyncStatusAction()
    {

        header('X-Accel-Buffering: no');
        $this->_helper->layout->disableLayout();
        ob_end_clean();

        echo "Start Diff of open App and WWS-Items ...<br>\n";

        $sync = new app\library\MyProject\Wwssync\Bearbeitungsstatus();

        $aStatus = $sync
            ->runDiff()
            ->saveChanges()
            ->getProcessStatus()
        ;

        echo '<pre>'
            . json_encode(
                [ 'Status' => $aStatus, ],
                JSON_PRETTY_PRINT
            )
            . '</pre>';
        exit;
    }
}