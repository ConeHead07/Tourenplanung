<?php

/*
CREATE VIEW view_verbrauch2 AS
SELECT 
 t.tour_id, t.Mandant, t.Auftragsnummer, 
 lg.lager_name, p.datum,
 'Fuhrpark' typ,
 CONCAT(ifnull(f.kennzeichen,''),' ',ifnull(f.fahrzeugart,'')) name,
 df.km verbrauch, 'km' einheit,
 df.kosten
  FROM
 `mr_touren_dispo_vorgaenge` t 
 LEFT JOIN mr_touren_dispo_fuhrpark df ON t.tour_id = df.tour_id
 LEFT JOIN mr_fuhrpark f ON df.fuhrpark_id = f.fid 
 LEFT JOIN mr_touren_timelines TL ON t.timeline_id = tl.timeline_id
 LEFT JOIN mr_touren_portlets P ON tl.portlet_id = p.portlet_id
 LEFT JOIN mr_lager lg ON p.lager_id = lg.lager_id
 WHERE tour_abgeschlossen_am IS NOT NULL AND df.fuhrpark_id IS NOT NULL
UNION 
SELECT 
 t.tour_id, t.Mandant, t.Auftragsnummer, 
 lg.lager_name, p.datum,
 'Mitarbeiter' typ,
 CONCAT(ifnull(m.vorname,''), ' ', ifnull(m.name,''), ' [', ifnull(m.eingestellt_als,''), ']') name, 
 dm.einsatzdauer verbrauch, 'std' einheit,
 dm.kosten
  FROM
 `mr_touren_dispo_vorgaenge` t 
 LEFT JOIN mr_touren_dispo_mitarbeiter dm ON t.tour_id = dm.tour_id
 LEFT JOIN mr_mitarbeiter m ON dm.mitarbeiter_id = m.mid 
 LEFT JOIN mr_touren_timelines TL ON t.timeline_id = tl.timeline_id
 LEFT JOIN mr_touren_portlets P ON tl.portlet_id = p.portlet_id
 LEFT JOIN mr_lager lg ON p.lager_id = lg.lager_id
 WHERE tour_abgeschlossen_am IS NOT NULL AND dm.mitarbeiter_id IS NOT NULL 
UNION 
SELECT 
 t.tour_id, t.Mandant, t.Auftragsnummer, 
 lg.lager_name, p.datum,
 'Werkzeug' typ,
 bezeichnung name, 
 dw.einsatzdauer verbrauch, 'std' einheit, 
 dw.kosten
  FROM
 `mr_touren_dispo_vorgaenge` t 
 LEFT JOIN mr_touren_dispo_werkzeug dw ON t.tour_id = dw.tour_id
 LEFT JOIN mr_werkzeug w ON dw.werkzeug_id = w.wid 
 LEFT JOIN mr_touren_timelines TL ON t.timeline_id = tl.timeline_id
 LEFT JOIN mr_touren_portlets P ON tl.portlet_id = p.portlet_id
 LEFT JOIN mr_lager lg ON p.lager_id = lg.lager_id
 WHERE tour_abgeschlossen_am IS NOT NULL AND dw.werkzeug_id IS NOT NULL

**/
?>
