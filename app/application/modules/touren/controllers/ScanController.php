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
        $db = Zend_Db_Table::getDefaultAdapter();

        $sql = stripMargin(<<<EOT
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
                | WHERE p.datum BETWEEN '2019-04-01' AND '2019-05-31'
                |    AND mitarbeiter_id IS NOT NULL
                | GROUP BY p.datum, tma.mitarbeiter_id, ma.name, ma.extern_id
                | HAVING count(distinct( t.timeline_id )) > 1
EOT
        );

        $aScanResults = $db->fetchAll($sql, [], Zend_Db::FETCH_ASSOC);

        $this->_helper->json($aScanResults);
    }
}