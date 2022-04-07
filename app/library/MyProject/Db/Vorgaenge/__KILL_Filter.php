<?php

class MyProject_Db_Vorgaenge_Filter
{
    protected $_auftragskoepfe = 'auftragskoepfe';
    protected $_bestellkoepfe = 'bestellkoepfe';
    protected $_auftragspositionen = 'auftragspositionen';
    protected $_bestellpositionen = 'bestellpositionen';
    protected $_dispoauftragspositionen = 'mr_touren_dispo_auftragspositionen';
    
    public function filterByDate($date, $format = 'date', $dateKwOnly = true)
    {
        $limit = (isset($_REQUEST['limit'])) ? $_REQUEST['limit'] : 200;
        $filterTime = ( $date ) ? strtotime( $date ) : time();
        if (!$filterTime) $filterTime = time();

        $filterD = date('Y-m-d', $filterTime);
        $filterY = date('y', $filterTime);
        $filterW = date('W', $filterTime);
        
        $dateCompareOp = ($dateKwOnly) ? '=' : '<=';

        return <<<EOT
SELECT 
-- Vereinbarter Ausliefertermin Geamtauftrag an Kunden
A.Mandant,
A.Auftragsnummer,
A.Lieferwoche AWoche,
CASE
    WHEN UNIX_TIMESTAMP( A.Liefertermin ) >0
    THEN A.Liefertermin
    WHEN A.Lieferjahr >=1 AND A.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL A.Lieferjahr YEAR ) , INTERVAL ((A.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS ALieferdatum, 
IF( UNIX_TIMESTAMP( A.Liefertermin ) >0, 'Datum', IF(A.Lieferjahr>=1 AND A.Lieferwoche>=1,'KW', NULL) ) ALieferGenauigkeit, 

-- Ankunftstermine gebündelter Bestellungen lt. Lieferant
/*
CASE
    WHEN UNIX_TIMESTAMP( B.Liefertermin ) >0
    THEN B.Liefertermin
    WHEN B.Lieferjahr >=1 AND B.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL B.Lieferjahr YEAR ) , INTERVAL ((B.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS BLieferdatum, 
IF( UNIX_TIMESTAMP( B.Liefertermin ) >0, 'Datum', IF(B.Lieferjahr>=1 AND B.Lieferwoche>=1,'KW', NULL) ) BLieferGenauigkeit,
*/

-- Auftragspositionen
-- Vereinbarte Ausliefertermine Einzelpositionen an Kunden
AP.`Positionsnummer` AP_PosNr,
AP.`Liefertermin` AP_Liefertermin,
AP.Lieferwoche APWoche,
CASE
    WHEN UNIX_TIMESTAMP( AP.Liefertermin ) >0
    THEN AP.Liefertermin
    WHEN AP.Lieferjahr >=1 AND AP.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL AP.Lieferjahr YEAR ) , INTERVAL ((AP.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS APLieferdatum, 
IF( UNIX_TIMESTAMP( AP.Liefertermin ) >0, 'Datum', IF(AP.Lieferjahr>=1 AND AP.Lieferwoche>=1,'KW', NULL) ) APLieferGenauigkeit, 

-- Bestellpositionen
-- Ankunftstermine einzelner Positionen lt. Lieferant
BP.`Positionsnummer` BP_PosNr,
BP.Liefertermin BP_Liefertermin, 
CASE
    WHEN UNIX_TIMESTAMP( BP.Liefertermin ) >0
    THEN BP.Liefertermin
    WHEN BP.Lieferjahr >=1 AND BP.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL BP.Lieferjahr YEAR ) , INTERVAL ((BP.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS BPLieferdatum,

-- DispoMengen
SUM(AP.Bestellmenge) GesamtBestellMenge,
D.DisponierteMenge,
CASE
    WHEN D.tour_id IS NOT NULL THEN
        IF(D.DisponierteMenge <= AP.Bestellmenge, AP.Bestellmenge - D.DisponierteMenge, 0)
    ELSE
        AP.Bestellmenge
END AS DispoRestMengen,
CASE
    WHEN D.tour_id IS NOT NULL THEN
        IF(D.DisponierteMenge < AP.Bestellmenge, 1, 0)
    ELSE
        1
END AS IstNochNichtVollDisponiert,
COUNT(*) AnzahlPositionen,
SUM( 
    IF(D.tour_id IS NULL, 1, IF(D.DisponierteMenge <= AP.Bestellmenge, 1, 0) )
) SumNichtDisponiert

FROM auftragskoepfe A
-- LEFT JOIN bestellkoepfe B ON(
--     A.Mandant = B.Mandant
--     A.Auftragsnummer = B.Auftragsnummer)
LEFT JOIN auftragspositionen AP ON(
    A.Mandant = AP.Mandant
    AND A.Auftragsnummer = AP.Auftragsnummer
)
LEFT JOIN mr_touren_dispo_auftragspositionen D ON
(
    D.Mandant = AP.Mandant
    AND D.Auftragsnummer = AP.Auftragsnummer
    AND D.Positionsnummer = AP.Positionsnummer
)
LEFT JOIN bestellpositionen BP ON
(
    AP.`Mandant` = BP.`Mandant` 
    AND AP.`Auftragsnummer` = BP.`Auftragsnummer`
    AND AP.`Positionsnummer` = BP.`AuftragsPositionsnummer`  
)
WHERE
-- Filter Bearbeitungsstatus
A.Bearbeitungsstatus = 2
AND AP.Positionsart = 1
AND AP.AlternativPos <> 1
-- Terminfilter
-- Zeige Aufträge bzw. die Positionen enthalten die 
--   exakt für diesen Tag vorgesehen
--   oder vorher und noch nicht 100% gebucht sind
-- die für keinen Tag exakt vorgesehen, aber für diese Woche
--   oder vorher und noch nicht 100% gebucht sind
AND
(    
    (
        (UNIX_TIMESTAMP( A.Liefertermin ) = 0 AND A.Lieferwoche IS NULL OR A.Lieferwoche = 0)
        OR (DATE(A.Liefertermin) $dateCompareOp '$filterD')
        OR (A.Lieferjahr $dateCompareOp '$filterY' && A.Lieferwoche $dateCompareOp '$filterW')
    )
    OR
    (
        (UNIX_TIMESTAMP( AP.Liefertermin ) = 0 AND AP.Lieferwoche IS NULL OR AP.Lieferwoche = 0)
        OR (DATE(AP.Liefertermin) $dateCompareOp '$filterD')
        OR (AP.Lieferjahr $dateCompareOp '$filterY' && AP.Lieferwoche $dateCompareOp '$filterW')
    )
)
GROUP BY A.Mandant, A.Auftragsnummer
EOT;

    }
}

?>