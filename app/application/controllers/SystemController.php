<?php

// ACHTUNG fuer DateTime-Abfragen in MSSQL muss Tag und Monat vertauscht werden:
// Statt 2012-12-23 00:00:00 Muss 2012-23-12 00:00:00 (Muss man nicht verstehen, aber ist so!!!)
// SELECT COUNT(1) as count FROM BestellKoepfe 
// WHERE (AngelegtAm > DATEADD(SECOND, 0, '2012-25-12 00:00:00')) 
// OR  (GeaendertAm > DATEADD(SECOND, 0, '2012-23-02 00:00:00'))  
error_reporting(E_ALL | E_STRICT);
set_time_limit(14400);
ignore_user_abort(1);

function getWwsImporterPidFile() {
 return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . '9999.pid.txt';
}
error_log('#' . __LINE__ . ' wwsImporterPidFile: ' . getWwsImporterPidFile());
// 
function clearPID() {
	$wwsImporterPidFile = getWwsImporterPidFile();
	error_log('Remove Unique Import-Process-File ' . $wwsImporterPidFile);
	if (file_exists($wwsImporterPidFile)) unlink($wwsImporterPidFile);
}

function createPID() {
	$wwsImporterPidFile = getWwsImporterPidFile();
	error_log('Create New Unique Import-Process-File ' . $wwsImporterPidFile);
	clearPID();
	file_put_contents($wwsImporterPidFile, time());
	touch($wwsImporterPidFile);
}

function isRunningPID() {
	$wwsImporterPidFile = getWwsImporterPidFile();
	return (file_exists($wwsImporterPidFile) && (time() - filectime($wwsImporterPidFile) < 1800));
}

function startPID() {
	$wwsImporterPidFile = getWwsImporterPidFile();
	if (!isRunningPID()) {
		createPID();
		register_shutdown_function('clearPID');
	} else {
		try { throw new Exception(''); } catch(Exception $e) { $stackTrace = $e->getTraceAsString(); }
		$error = 'Another Instance of this Job is already in Process since ' . date('Y-m-d H:i:s', filectime($wwsImporterPidFile)). '!'
		        .'Otherwise delete PID-File ' . $wwsImporterPidFile . '!';
		error_log($error);
		error_log('Abort Script. Stack-Trace: ' . $stackTrace);
		echo $error;
		exit;
	}
}

/**
  update mr_touren_dispo_auftraege SET
  auftrag_abgeschlossen_am = now(),
  auftrag_abgeschlossen_user = 'system'
  where
  auftrag_abgeschlossen_am IS NULL
  AND Mandant = 10 AND Auftragsnummer IN(
  SELECT Auftragsnummer FROM `mr_auftragskoepfe_dispofilter`
  where Mandant = 10
  AND
  (
  GeaendertAm < '2013-02-00 00:00:00'
  OR (GeaendertAm IS NULL AND AngelegtAm < '2013-02-00 00:00:00')
  OR  Bearbeitungsstatus = 9
  )
  )
  )
 */

/**
 * Description of SystemController
 *
 * @author rybka
 */
class SystemController extends Zend_Controller_Action {

    static $text = '';
	protected $tlogFile = '';
	
    public function secondsToTime($d) {
        $h = floor($d / 3600);
        $m = ($d > 59) ? (floor($d / 60) % 60) . 'm ' : '';
        $s = ($d % 60) . 's';
        return ($h ? $h . 'h ' : '') . $m . $s;
    }
	
	public function tlog($line, $log) {
		$this->tlogFile = APPLICATION_PATH . '/log/wwsimport/dbimporter_' . date('YmdH') . '.log.txt';
		error_log( '[' . date('d-M-Y H:i:s') . ' UTC] ' . $line . ' ' . $log . PHP_EOL, 3, $this->tlogFile);
		return $log;
	}
	
	public function test1Action()
	{
		self::$text.= '#' . __LINE__ . ' ' . __METHOD__ . '<br>' . PHP_EOL;
		//die($this->text);
		$this->_forward('importneueauftraege');
	}
	
	public function test2Action()
	{
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' START !!!');
		self::$text.= '#' . __LINE__ . ' ' . __METHOD__ . '<br>' . PHP_EOL;
		die(self::$text);
	}
		
	public function importmissingauftragskoepfeAction() {
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' START !!!');
		$sql = 'SELECT t.Mandant, t.Auftragsnummer FROM ' . PHP_EOL
			  .' mr_touren_dispo_vorgaenge t  ' . PHP_EOL
			  .' LEFT JOIN `mr_auftragskoepfe_dispofilter` ak ON(  ' . PHP_EOL
			  .' t.Mandant = ak.Mandant  ' . PHP_EOL
			  .' AND t.Auftragsnummer = ak.Auftragsnummer  ' . PHP_EOL
			  .' ) ' . PHP_EOL
			  .' WHERE t.IsDefault = 0 AND ak.Auftragsnummer IS NULL  ' . PHP_EOL
			  .' GROUP BY t.Mandant, t.Auftragsnummer ' . PHP_EOL
			  .' ORDER BY `t`.`Mandant`  DESC';
		die( '<pre>' . $sql . '</pre>' );
	}
	

    //put your code here
    public function updategeaendertamAction() {
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' START !!!');
        echo $this->tlog('#' . __LINE__ . ' ' . __METHOD__) . PHP_EOL;
        $modelTA = MyProject_Model_Database::loadModel('tourenDispoAuftraege');
//        $modelTA = new Model_TourenDispoAuftraege();
        $modelTA->importWwsGeandertAm();

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' FINISHED !!!');
    }
	
	public function importneueauftraegeAction()
	{
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' START !!!');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' EXIT !!!');
		return;
        
        $lastImportTimeFile = APPLICATION_PATH . '/cache/lastTourenAuftraegeImport.log';
        
		file_put_contents($lastImportTimeFile, time());
		$modelDA = MyProject_Model_Database::loadModel('tourenDispoAuftraege');
		//$modelDA = new Model_TourenDispoAuftraege();
		$modelDA->importNeueAuftraege();
	}

    public function wwsimportAction() {
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' STARTED !!!');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $WwsImporter = new MyProject_Model_WwsFilterImport;
        echo $this->tlog('#' . __LINE__ . ' import Auftragskoepfe') . '<br/>' . PHP_EOL;
        $WwsImporter->import_auftragskoepfe();
        echo $this->tlog('#' . __LINE__ . ' import Bestellkoepfe') . '<br/>' . PHP_EOL;
        $WwsImporter->import_bestellkoepfe();
        echo $this->tlog('#' . __LINE__ . ' import Auftragspositionen') . '<br/>' . PHP_EOL;
        $WwsImporter->import_auftragspositionen();
        echo $this->tlog('#' . __LINE__ . ' import Bestellpositionen') . '<br/>' . PHP_EOL;
        $WwsImporter->import_bestellpositionen();
        ob_flush();
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' FINISHED AND EXIT !!!');
        die('Import abgeschlossen!');
    }
	
    function fittourdatesAction()
    {
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' STARTED !!!');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        /* @var $db Zend_Db_Adapter_Pdo_Mysql */
        $db = Zend_Registry::get('db');

        header('Content-Type: text/html');
        
        $sql = 'SELECT v.tour_id, v.Auftragsnummer, v.timeline_id, p.portlet_id, v.DatumVon, p.datum ' . PHP_EOL
              .' FROM `mr_touren_dispo_vorgaenge` v ' . PHP_EOL
              .' JOIN mr_touren_timelines t USING(timeline_id) ' . PHP_EOL
              .' JOIN mr_touren_portlets p ON t.portlet_id = p.portlet_id ' . PHP_EOL
              .' WHERE v.DatumVon != p.datum';
        $rows = $db->fetchAll($sql, null, Zend_Db::FETCH_OBJ);
        $stmt = $db->prepare('UPDATE mr_touren_dispo_vorgaenge Set DatumVon = :d, DatumBis = :d WHERE tour_id = :id');
        echo $this->tlog('Korrigiere ' . count($rows) . ' Touren mit abweichenden Datumseinträgen') . '<br>';
        echo '<table border=1 cellpadding=1 cellspacing=0><tr><td>tour-id<td>WWS-NR<td>timeline-id<td>porlet-id<td>Tour-Datum<td>Zeitschiene</tr>' . PHP_EOL;
        foreach($rows as $r) {
            echo '<tr><td>';
            echo implode('</td><td>', array(
                $r->tour_id, 
                $r->Auftragsnummer, 
                $r->timeline_id, 
                $r->portlet_id, 
                $r->DatumVon, 
                $r->datum));
            echo '</tr>' . PHP_EOL;
            $stmt->execute(array('d' => $r->datum, 'id' => $r->tour_id));            
        }
        echo '</table>';
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' FINISHED !!!');
    }

    public function wwscount() 
	{
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' STARTED !!!');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $tables = array(
            'wws_auftragskoepfe', 'wws_auftragspositionen',
            'wws_bestellkoepfe', 'wws_bestellpositionen',
            'wws_stg_auftragskoepfe', 'wws_stg_auftragspositionen',
            'wws_stg_bestellkoepfe', 'wws_stg_bestellpositionen',
        );
        /* @var $db Zend_Db_Adapter_Pdo_Mysql */
        $db = Zend_Registry::get('db');

        header('Content-Type: text/plain');
        foreach ($tables as $table) {
            echo $table . ' : ' . $db->fetchOne('SELECT COUNT(1) FROM ' . $table) . PHP_EOL;
        }
    }

    public function wwscleanlog($props, $msg, $reset = 0) 
	{
        $logdir = APPLICATION_PATH . '/log/wwsimport/';
        if (!$reset)
            file_put_contents($logdir . $props['log'], date('Ymd His ') . $msg . PHP_EOL, FILE_APPEND);
        else
            file_put_contents($logdir . $props['log'], date('Ymd His ') . $msg . PHP_EOL);
			
    }

    public function wwscleanAction() 
    {
		startPID();
		
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' STARTED !!!');
		$rq = $this->getRequest();
		$v = $rq->getParam('verbose', 0);
		$anr = $rq->getParam('anr', '');
		$mnd = $rq->getParam('mnd', '10');
        /* @var $db Zend_Db_Adapter_Pdo_Mysql */
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_Db::FETCH_NUM);
		
		$iMaxAllowedPacketSize = $db->fetchOne("SELECT @@max_allowed_packet");
		error_log('#' . __LINE__ . ' iMaxAllowedPacketsize: ' . print_r($iMaxAllowedPacketSize,1) );
		

        $wwsdb = Zend_Registry::get('sqlsrvdb');
        $wwsdb->setFetchMode(Sqlsrv::FETCH_ASSOC);

        $stgTDA = new Model_Db_TourenDispoAuftraege;
		
		$select = 'Mandant, Auftragsnummer';
        $sql = 'SELECT ' . $select . ' FROM ' . $stgTDA->info(Zend_Db_Table::NAME)
                . ' WHERE '
				. (!$anr 
				     ? ' auftrag_abgeschlossen_am IS NULL AND Mandant < 100'
					 : ' Auftragsnummer = ' . (int)$anr . ' AND Mandant =  ' . (int)$mnd
				  )
                . ' ORDER BY Mandant';
		$sqlCount = str_replace($select, 'count(1)', $sql);
		
        $lastM = 0;
        $query = '';
        $isFirstAnr = 0;
        $stmt = $db->query($sql);
		$count = $db->fetchOne($sqlCount);
        $a = 0;

        if ($v) {
			echo '<pre>' . PHP_EOL;
			echo '#' . __LINE__ . ' SQL GET Auftragsnummern: ' . PHP_EOL . $sql . PHP_EOL;
		}
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' ' . __FILE__);
		error_log('#' . __LINE__ . ' SQL: '. $sql);
		error_log('#' . __LINE__ . ' Rows:'. $count);
		
        while ($row = $stmt->fetch(Zend_Db::FETCH_NUM)) {
            ++$a;
            if ($row[0] != $lastM) {
                if ($lastM)
                    $query.= '))' . PHP_EOL;
                $query.= ($query ? ' OR ' : '') . '(t.Mandant = ' . $row[0] . ' AND (' . PHP_EOL;
                $isFirstAnr = true;
            }
            $query.= '   ' . ($isFirstAnr ? '' : 'OR ') . 't.Auftragsnummer = ' . $row[1] . PHP_EOL;
            if ($isFirstAnr)
                $isFirstAnr = false;
            $lastM = $row[0];
        }
        if ($query)
            $query.= '))' . PHP_EOL;

        $stgAK = MyProject_Model_Database::getStorageByClass('vorgaenge');
        $stgAP = MyProject_Model_Database::getStorageByClass('auftragspositionen');
        $stgBK = MyProject_Model_Database::getStorageByClass('bestellkoepfe');
        $stgBP = MyProject_Model_Database::getStorageByClass('bestellpositionen');
        $stgWB = MyProject_Model_Database::getStorageByClass('Warenbewegungen');
		error_log('#' . __LINE__ . ' ' . __METHOD__);
        $wwsTableKeys = array(
        'AuftragsKoepfe' => array(
            'keys' => array('Mandant', 'Auftragsnummer'),
            'stg' => 'mr_wws_ak_keys',
            'dst' => $stgAK->info(Zend_Db_Table::NAME),
            'dep' => array('mr_touren_dispo_auftraege', 'mr_auftragskoepfe_refs'),
            'log' => 'CleanAuftragskoepfe.log'),
        'AuftragsPositionen' => array(
            'keys' => array('Mandant', 'Auftragsnummer', 'Positionsnummer'),
            'stg' => 'mr_wws_ap_keys',
            'dst' => $stgAP->info(Zend_Db_Table::NAME),
            'dep' => array('mr_touren_dispo_auftragspositionen'), //'mr_touren_dispo_auftragspositionen_txt'),
            'log' => 'CleanAuftragspositionen.log'),
        'BestellKoepfe' => array(
            'keys' => array('Mandant', 'Bestellnummer'),
            'stg' => 'mr_wws_bk_keys',
            'dst' => $stgBK->info(Zend_Db_Table::NAME),
            'dep' => array('mr_bestellkoepfe'),
            'log' => 'CleanBestellkoepfe.log'),
        'BestellPositionen' => array(
            'keys' => array('Mandant', 'Bestellnummer', 'Positionsnummer'),
            'stg' => 'mr_wws_bp_keys',
            'dst' => $stgBP->info(Zend_Db_Table::NAME),
            'dep' => array('mr_bestellpositionen_dispofilter'),
            'log' => 'CleanBestellpositionen.log'),
        'Warenbewegungen' => array(
            'keys' => array('Mandant', 'LaufendeNummer'),
            'stg' => 'mr_wws_wb_keys',
            'dst' => $stgWB->info(Zend_Db_Table::NAME),
            'dep' => array(),
            'log' => 'CleanWarenbewegungen.log'),
        );
        foreach ($wwsTableKeys as $props) {
            $this->wwscleanlog($props, 'Start', 1);
        }
		error_log('#' . __LINE__ . ' ' . __METHOD__);

        $this->wwscleanlog($wwsTableKeys['AuftragsKoepfe'], 'Nicht abgeschlossene Vorgaenge: ' . $a);

        foreach ($wwsTableKeys as $wwsTbl => $props) {
			error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Truncate ' . $props['stg']);
            $db->query('TRUNCATE ' . $props['stg']);
            $_select = 'SELECT ' . implode(',', $props['keys']) . ' FROM ' . $wwsTbl . ' t WHERE ';
            if ($v) echo ' SQL Get ' . $wwsTbl . ': ' . $sql . PHP_EOL;
            $this->wwscleanlog($props, 'starting wwsQueries: ' . $wwsTbl);
            try {
				error_log('#' . __LINE__ . ' ' . __METHOD__ . ' ' . $_select . ' ... strlen: ' . strlen($query));
                $stmt = $wwsdb->query($_select . $query);
            } catch (Exception $e) {
				error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Exit ' . $e->getMessage());
                $this->wwscleanlog($props, ' ERROR: ' . $e->getMessage() . PHP_EOL . $sql);
                exit;
            }

            $blockInsert = 'INSERT INTO ' . $props['stg'] . '(' . implode(',', $props['keys']) . ')' . PHP_EOL . ' VALUES ' . PHP_EOL;
            $blockValues = '';
            $i = 0;
			error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Insert Bloecke: ' . $blockInsert . ' ...');
            while (($row = $stmt->fetch(Sqlsrv::FETCH_NUM)) && ++$i) {
                $blockValues.= ($blockValues ? ',' . PHP_EOL : '') . '(' . implode(',', $row) . ')';
                if ($i && $i % 3000 == 0) {
                    try {
                        $db->query($blockInsert . $blockValues);
                    } catch (Exception $e) {
						error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Exit ' . $e->getMessage());
                        $this->wwscleanlog($props, ' ERROR: ' . $e->getMessage() . PHP_EOL . $sql);
                        exit;
                    }
                    $this->wwscleanlog($props, 'Inserted ' . $i . ' Keys of ' . $wwsTbl);
                    $blockValues = '';
                }
            }
			error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Insert Last remaining Block');
            if ($blockValues) {
                $db->query($blockInsert . $blockValues);
				if ($v) echo '#' . __LINE__ . ' SQL Insert ' . $wwsTbl . ': ' . PHP_EOL . $blockInsert . $blockValues . PHP_EOL;
                $this->wwscleanlog($props, 'Inserted ' . $i . ' Keys of ' . $wwsTbl);
            }
        }
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Search For Del-Keys');

        foreach ($wwsTableKeys as $wwsTbl => $props) {
			error_log('#' . __LINE__ . ' ' . __METHOD__ . ' - in ' . $wwsTbl);
            $this->wwscleanlog($props, $wwsTbl . ' Search For Del-Keys');
            $sql = 'SELECT t.' . trim(implode(', t.', $props['keys'])) . ' ' . PHP_EOL
                    . 'FROM ' . $props['dst'] . ' t ' . PHP_EOL
                    . 'LEFT JOIN ' . $props['stg'] . ' s USING(' . implode(', ', $props['keys']) . ')' . PHP_EOL
                    . 'WHERE (' . $query . ') AND s.Mandant IS NULL';

            try {
                if ($v) echo '#' . __LINE__ . ' SQL Get Del Keys ' . $wwsTbl . ': ' . PHP_EOL . $sql . PHP_EOL;
				$stmt = $db->query($sql);
            } catch (Exception $e) {
				error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Exit ' . $e->getMessage());
                if ($v) echo '#' . __LINE__ . ' SQL ERROR ' . $wwsTbl . ': ' . PHP_EOL . $e->getMessage() . PHP_EOL;
				$this->wwscleanlog($props, ' ERROR: ' . $e->getMessage() . PHP_EOL . $sql);
                exit;
            }

            $d = 0;
			$bStartBlock = true;
            $where = '';
			$aWhere = array();
			$iWIdx = 0;
			$iMax = $iMaxAllowedPacketSize - 1000;
			
			// { } [ ] ||
            while ($row = $stmt->fetch()) {
                ++$d;
				
				if ($where) {
					$where.= ' OR ';
				}
				
				$where.= '(' . PHP_EOL;
                foreach ($props['keys'] as $i => $k) {
					if ($i) $where.= ' AND ';
					$where.= $k . '=' . $row[$i];
				}
				$where.= ')' . PHP_EOL;
				
				if (strlen($where) > $iMax) { 
					$aWhere[ $iWIdx ] = $where;
					$iWIdx++;
					$where = '';
				}
            }
			
            $this->wwscleanlog($props, 'found ' . $d . ' ' . $wwsTbl . ' did no more exist in WWS!');
            //echo $where . PHP_EOL;
            if (0 === $d) {
                continue;
			}
			
			error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Delete no more existing by Keys ');
            foreach (array_merge(array($props['dst']), $props['dep']) as $delTbl) {
				error_log('#' . __LINE__ . ' ' . __METHOD__ . ' - in ' . $delTbl);
                foreach($aWhere as $where) {
					try {
	//                  $sql = 'SELECT count(1) FROM ' . $delTbl . ' WHERE ' . $where;
	//                  $affectingRows = $db->fetchOne($sql);
						error_log('#' . __LINE__ . ' ' . __METHOD__ . ' DELETE-BLOCK');
						
						$sql = 'DELETE FROM ' . $delTbl . ' WHERE ' . $where;
						if ($v) echo '#' . __LINE__ . ' SQL Delete no more existing Keys from ' . $delTbl . ': ' . PHP_EOL . $sql . PHP_EOL;
						$stmt = $db->query($sql);
						$affectedRows = $stmt->rowCount();
						$this->wwscleanlog($props, 'Deleted Rows in ' . $delTbl . ' : ' . $affectedRows);
					} catch (Exception $e) {
						error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Exit ' . $e->getMessage());
						if ($v) echo '#' . __LINE__ . ' SQL ERROR : ' . PHP_EOL . $e->getMessage() . PHP_EOL;
						$this->wwscleanlog($props, ' ERROR: ' . $e->getMessage() . PHP_EOL . $sql);
						exit;
					}
				}
                
            }
        }
		error_log('#' . __LINE__ . ' ' . __METHOD__ . ' finished');
        die('finished successful!');
    }

    public function wwssynccheckAction()
    {
        $this->_helper->layout->disableLayout();

        /* @var $db Zend_Db_Adapter_Pdo_Mysql */
        $db = Zend_Registry::get('db');

        /** @var  $wwsdb MyProject_Db_Sqlsrv */
        $wwsdb = Zend_Registry::get('wwsdb');
        $wwsdb->setFetchMode(Sqlsrv::FETCH_ASSOC);
        $wwsdb->setScrollableCursor( SQLSRV_CURSOR_STATIC );

        $queryOpenTickets = <<<EOT
        SELECT CONCAT(Mandant, '-', Auftragsnummer, '-', BearbeitungsStatus) AS statusid FROM scoffice7.dbo.AuftragsKoepfe 
        WHERE BearbeitungsStatus BETWEEN 2 AND 5 
        UNION SELECT CONCAT(Mandant, '-', Auftragsnummer, '-', BearbeitungsStatus) AS statusid FROM scoffice7_Mig.dbo.AuftragsKoepfe 
        WHERE BearbeitungsStatus BETWEEN 2 AND 5
EOT;

        // works until here

        echo '#' . __LINE__ . "<br>\n";
        $rslt = $wwsdb->query($queryOpenTickets);
        echo '#' . __LINE__ . "<br>\n";
        // works until here
        $aWwsOpenIds = [];
        while($_row = $rslt->fetch(SQLSRV_FETCH_ASSOC)) {
            $aWwsOpenIds[] = $_row['statusid'];
        }
        echo '#' . __LINE__ . "<br>\n";
        flush();

        // works until here
        $iCountWwsOpen = count($aWwsOpenIds);

        $sql = 'SELECT COUNT(1) ';
        $sql.= ' FROM mr_auftragskoepfe_dispofilter ';
        $sql.= ' WHERE Bearbeitungsstatus BETWEEN 2 AND 5 ';
        $sql.= ' AND CONCAT(Mandant,"-", Auftragsnummer, "-", Bearbeitungsstatus) IN (';
        $sql.= '"' . implode("\",\n\"", $aWwsOpenIds) . '"';
        $sql.= ' )';
        $iCountMatches = $db->fetchOne( $sql );
        echo "#" . __LINE__ . "<br>\n";
        flush();

        $sql = 'SELECT Mandant, Auftragsnummer, Bearbeitungsstatus, "" AS WwsStat ';
        $sql.= ' FROM mr_auftragskoepfe_dispofilter ';
        $sql.= ' WHERE Bearbeitungsstatus BETWEEN 2 AND 5 ';
        $sql.= ' AND CONCAT(Mandant,"-", Auftragsnummer, "-", Bearbeitungsstatus) NOT IN (';
        $sql.= '"' . implode("\",\n\"", $aWwsOpenIds) . '"';
        $sql.= ' ) LIMIT 200';
        unset($aWwsOpenIds);
        $aAppMissmatches = $db->fetchAll( $sql );
        $iCountMissmatches = count($aAppMissmatches);
        echo "#" . __LINE__ . "<br>\n";

        $addWwsStat = function( $mid, $anr, $wstat ) use(&$aAppMissmatches, $db) {
            $found = false;
            foreach($aAppMissmatches as $_idx => $_row) {
                // echo "#" . __LINE__ . "<br>\n";
                // flush();
                if ($_row['Mandant'] == $mid
                    && $_row['Auftragsnummer'] == $anr
                    && $wstat != $_row['Bearbeitungsstatus']) {
                    echo "Found $mid, $anr, $wstat, Old-Stat: ". $_row['Bearbeitungsstatus'] . "<br>\n";
                    $aAppMissmatches[$_idx]['WwsStat'] = $wstat;
                    $db->query("UPDATE mr_auftragskoepfe_dispofilter SET Bearbeitungsstatus = $wstat WHERE Mandant = $mid AND Auftragsnummer = $anr");
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "NOT Found $mid, $anr, $wstat, Old-Stat: ". $_row['Bearbeitungsstatus'] . "<br>\n";
            }
        };

        echo '#' . __LINE__ . "<br>\n";
        $aCheckSCO7 = array_filter($aAppMissmatches,
            function($row) {
                return $row['Mandant'] != 10 || strlen($row['Auftragsnummer']) > 7;
            }
        );

        echo '#' . __LINE__ . "<br>\n";
        $aCheckSCO7Mig = array_filter($aAppMissmatches,
            function($row) {
                return $row['Mandant'] != 10 || strlen($row['Auftragsnummer']) < 8;
            }
        );

        $aMissmatchIds = array_chunk(array_map( function($row) { return "{$row['Mandant']}-{$row['Auftragsnummer']}";}, $aCheckSCO7), 100);
        $aMissmatchIdsMig = array_chunk(array_map( function($row) { return "{$row['Mandant']}-{$row['Auftragsnummer']}";}, $aCheckSCO7Mig), 100);

        $iCountWwsMismatches = 0;
        $iNumChunks = count($aMissmatchIds);
        for($i = 0; $i < $iNumChunks; $i++) {
            if (!count($aMissmatchIds[$i])) {
                continue;
            }
            $sql = str_replace(':IDS', implode("','", $aMissmatchIds[$i]), 'SELECT Mandant, Auftragsnummer, Bearbeitungsstatus 
FROM scoffice7.dbo.AuftragsKoepfe 
WHERE CONCAT("Mandant",\'-\',"Auftragsnummer") IN (\':IDS\')');
            echo '#' . __LINE__ . '; sql: ' . $sql . "<br>\n";

            // works until here
            $rslt = $wwsdb->query($sql);
            echo '#' . __LINE__ . "<br>\n";
            // works until here

            while ($_row = $rslt->fetch(SQLSRV_FETCH_ASSOC)) {
                // works until here
                $iCountWwsMismatches++;
                $addWwsStat($_row['Mandant'], $_row['Auftragsnummer'], $_row['Bearbeitungsstatus']);
            }
            // works until here
        }
        echo "#" . __LINE__ . "<br>\n";

        $iNumChunksMig = count($aMissmatchIdsMig);
        for($i = 0; $i < $iNumChunksMig; $i++) {
            if (!count($aMissmatchIdsMig[$i])) {
                continue;
            }
            $sql = str_replace(':IDS', implode("','", $aMissmatchIdsMig[$i]), 'SELECT Mandant, Auftragsnummer, Bearbeitungsstatus 
FROM scoffice7_Mig.dbo.AuftragsKoepfe 
WHERE CONCAT("Mandant",\'-\',"Auftragsnummer") IN (\':IDS\')');
            unset($aMissmatchIdsMig);
            echo '#' . __LINE__ . "<br>\n";
            // works until here

            $rslt = $wwsdb->query($sql);
            echo '#' . __LINE__ . '; rows: ' . $rslt->num_rows() . " for $sql <br>\n";
            // works until here
            while ($_row = $rslt->fetch(SQLSRV_FETCH_ASSOC)) {
                // die('#' . __LINE__);
                $iCountWwsMismatches++;
                $addWwsStat($_row['Mandant'], $_row['Auftragsnummer'], $_row['Bearbeitungsstatus']);
            }
            echo "#" . __LINE__ . "<br>\n";
        }


        die( '<pre>'
            . "Num Open in WWS: " . $iCountWwsOpen . "\n"
            . "Num App-Matches: " . $iCountMatches . "\n"
            . "Num Missmatches: " . $iCountMissmatches . "\n"
            . "WWS Missmatches: " . $iCountWwsMismatches . "\n"
            . "<b>Missmatching Records in App:</b>\n"
            . json_encode($aAppMissmatches, JSON_PRETTY_PRINT) . '</pre>'
        );
        die("Aborted in LINE " . __LINE__);

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
    public function wwssyncbylibAction()
    {

        header('X-Accel-Buffering: no');
        $this->_helper->layout->disableLayout();
        ob_end_clean();

        echo "Start Diff of open App and WWS-Items ...<br>\n";

        $sync = new app\library\MyProject\Wwssync\Bearbeitungsstatus();

        $aStat = $sync
            ->runDiff()
            ->saveChanges()
            ->getProcessStatus()
        // ->printDebugItemList()
        ;

        echo '<pre>aStat: ' . print_r($aStat, 1) . '</pre>' . "\n";

        exit;

    }

    public function wwsdirektimportAction() {
        startPID();

        error_log('#' . __LINE__ . ' ' . __METHOD__ . ' STARTED !!!');

        set_time_limit(14400);
        ignore_user_abort(1);

        header('Content-Type: text/plain');

        $wwssource = $this->getRequest()->getParam('wwssource', '');

        if (empty($wwssource)) {

            $this->wwsdirektimportByWWSDB('wwsdb2');

            $this->wwsdirektimportByWWSDB('wwsdb');

        } elseif (in_array($wwssource, ['wwsdb', 'wwsdb2'])) {
            $this->wwsdirektimportByWWSDB($wwssource);
        }

        die($this->tlog(__LINE__, 'finished successfully!'));

    }

    protected function wwsdirektimportByWWSDB(string $wwssource) {

        error_log('#' . __LINE__ . ' ' . __METHOD__ . '('. $wwssource . ') STARTED !!!');

        set_time_limit(14400);
        ignore_user_abort(1);

        $varStorage = new Model_Variables();

        $logdir = APPLICATION_PATH . '/log/wwsimport/';
        $timeIn = time();
        /* @var $db Zend_Db_Adapter_Pdo_Mysql */
        $db = Zend_Registry::get('db');

        $wwstable = $this->getRequest()->getParam('table', '');
        $reset = $this->getRequest()->getParam('reset', '');
        $dateOffset = $this->getRequest()->getParam('date', '');

        if (!empty($wwssource) && $wwssource === 'wwsdb2') {
            $wwsSelector = 'sqlsrvdb2';
            $wwsdb = Zend_Registry::get($wwsSelector);
            $wwsdb->setFetchMode(Sqlsrv::FETCH_ASSOC);
        } else {
            $wwsSelector = 'sqlsrvdb';
            /* @var $wwsdb MyProject_Db_Sqlsrv */
            $wwsdb = Zend_Registry::get($wwsSelector);
            $wwsdb->setFetchMode(Sqlsrv::FETCH_ASSOC);
        }

        if ($fillWssTables = 0) {
            $stgAK = MyProject_Model_Database::getStorageByClass('wwsVorgaenge');
            $stgAP = MyProject_Model_Database::getStorageByClass('wwsAuftragspositionen');
            $stgBK = MyProject_Model_Database::getStorageByClass('wwsBestellkoepfe');
            $stgBP = MyProject_Model_Database::getStorageByClass('wwsBestellpositionen');
            $stgWB = MyProject_Model_Database::getStorageByClass('wwsWarenbewegungen');
        } else {
            $stgAK = MyProject_Model_Database::getStorageByClass('vorgaenge');
            $stgAP = MyProject_Model_Database::getStorageByClass('auftragspositionen');
            $stgBK = MyProject_Model_Database::getStorageByClass('bestellkoepfe');
            $stgBP = MyProject_Model_Database::getStorageByClass('bestellpositionen');
            $stgWB = MyProject_Model_Database::getStorageByClass('warenbewegungen');
        }
        $wwsTablesToDispoModels = array(
            'AuftragsKoepfe' => $stgAK,
//            'AuftragsPositionen' => $stgAP,
//            'BestellKoepfe' => $stgBK,
//            'BestellPositionen' => $stgBP,
//            'Warenbewegungen' => $stgWB,
        );
        $wwsTablesReset = array(
            'AuftragsKoepfe' => 'DELETE FROM '. $stgAK->info(Zend_Db_Table::NAME) . ' WHERE Mandant > 110',
        );
        //if ($wwstable === 'Warenbewegungen) $wwsTablesToDispoModels['Warenbewegungen'] = $stgWB;

        $wwsImportFilter = array();

        if ($wwstable) {
            $checkWwsTables = explode(',', $wwstable);

            foreach ($wwsTablesToDispoModels as $k => $v) {
                //die('#'.__LINE__. ' ' . $k . ' in array('.print_r($checkWwsTables,1) . (int)in_array($k, $checkWwsTables ));
                if (in_array($k, $checkWwsTables))
                    $wwsImportFilter[$k] = $v;
            }
            unset($checkWwsTables);
            //die('#'.__LINE__ . print_r($wwsImportFilter,1) );
        } else {
            $wwsImportFilter = $wwsTablesToDispoModels;
        }

        if (!is_dir($logdir)) {
            mkdir($logdir);
        }

        $row = null;
        try {
            /* @var $stg Zend_Db_Table_Abstract */
            foreach ($wwsImportFilter as $table => $stg) {
                $primaries = $stg->info(Zend_Db_Table::PRIMARY);
                $join = '';

                $logfile = $logdir . $wwssource . '.' . $table . '.log';
                $cols = implode(',', $stg->info(Zend_Db_Table::COLS));
                if (strpos($cols, ',Preis,') !== false) {
                    // $cols = str_replace(',Preis,', '', $cols);
                }
                if (strpos($cols, ',mr_modified') !== false) {
                    $cols = str_replace(',mr_modified', '', $cols);
                }
                $cols = 't.' . str_replace(',', ',t.', $cols);

                $lastModVarName = $wwsSelector . '_' . $table . '_lastmod';
                $lastWWSCountQueryVarName = $wwsSelector . '_' . $table . '_wws_countquery';
                $lastWWSQueryVarName = $wwsSelector . '_' . $table . '_wws_queryrows';
                $lastWWSNumRowsVarName = $wwsSelector . '_' . $table . '_wws_num_rows';
                $lastWWSNewRowsVarName = $wwsSelector . '_' . $table . '_wws_new_rows';
                $lastWWSUpdRowsVarName = $wwsSelector . '_' . $table . '_wws_updated_rows';
                $lastWWSImportStatusVarName = $wwsSelector . '_' . $table . '_wws_import_status';
                $lastWWSImportStartVarName = $wwsSelector . '_' . $table . '_wws_import_start';
                $lastWWSImportDurationVarName = $wwsSelector . '_' . $table . '_wws_import_duration';
                $lastWWSImportStartTime = time();

//              die(print_r($cols,1));
                if ($reset) {
                    file_put_contents($logfile, date('YmdHis')
                        . ' ' . $this->secondsToTime(time() - $timeIn)
                        . ' start import with table reset' . PHP_EOL);
                    if (empty($wwsTablesReset[$table])) {
                        $db->query( $this->tlog(__LINE__, 'TRUNCATE '
                            . $stg->info(Zend_Db_Table::NAME)) );
                    } else {
                        $db->query( $this->tlog(__LINE__, $wwsTablesReset[$table]));
                    }
                    $this->tlog( __LINE__, 'Error: ' . print_r($db->error(),1) );
                    $where = '';
                } else {
                    file_put_contents($logfile, date('YmdHis')
                        . ' ' . $this->secondsToTime(time() - $timeIn) . 's start import update');
                    if (!$dateOffset) {
                        $maxDates = $db->fetchRow($this->tlog(__LINE__,'SELECT MAX(AngelegtAm) MaxAngelegtAm, MAX(GeaendertAm) MaxGeaendertAm FROM ' . $stg->info(Zend_Db_Table::NAME)));
                        $lastMod = ($maxDates['MaxGeaendertAm'] > $maxDates['MaxAngelegtAm']) ? $maxDates['MaxGeaendertAm'] : $maxDates['MaxAngelegtAm'];
                    } else {
                        $lastMod = $dateOffset;
                    }

                    if (!$dateOffset) {
                        $lastMod = $varStorage->get($lastModVarName);
                        if (!$lastMod && $wwsSelector === 'sqlsrvdb') {
                            $maxDates = $db->fetchRow($this->tlog(__LINE__, 'SELECT MAX(AngelegtAm) MaxAngelegtAm, MAX(GeaendertAm) MaxGeaendertAm FROM ' . $stg->info(Zend_Db_Table::NAME)));
                            $lastMod = ($maxDates['MaxGeaendertAm'] > $maxDates['MaxAngelegtAm']) ? $maxDates['MaxGeaendertAm'] : $maxDates['MaxAngelegtAm'];
                        }
                    } else {
                        $lastMod = $dateOffset;
                    }

                    //echo $lastMod . PHP_EOL;
                    if ($lastMod) {
                        $lastMod = preg_replace('#(\d{4})-(\d{2})-(\d{2})(.*)#', "$1/$3/$2$4", $lastMod);
                        //die($lastMod);
                    } else {
                        $lastMod = '2018/01/08 00:00:00';
                    }

                    $where = ' WHERE (' . "\n"
                        . ' (t.AngelegtAm IS NOT NULL AND t.AngelegtAm > DATEADD(SECOND, 0, \'' . $lastMod . '\')) ' . "\n"
                        . ' OR (t.GeaendertAm IS NOT NULL AND t.GeaendertAm > DATEADD(SECOND, 0, \'' . $lastMod . '\')) ' . "\n"
                        . ' OR Auftragsnummer >= 10000000 ' . "\n"
                        . ' OR Geschaeftsbereich LIKE \'Neuss Medientechnik\'' . "\n"
                        .') ' . "\n"
                        ;

                    if ('AuftragsKoepfe' === $table) {

                        //$where = ' WHERE ';
                        //$where.= ' Geschaeftsbereich LIKE \'Neuss Medientechnik\' ';

                        $cols = str_replace(',t.ZusatzVorgangsArtBezeichnung', ',zva.Bezeichnung ZusatzVorgangsArtBezeichnung', $cols);
                        $join = ' LEFT JOIN ZusatzVorgangsArten zva ON (t.Mandant = zva.Mandant AND t.Zusatzvorgangsartnr = zva.Zusatzvorgangsartnr)';

                        $where.= ' AND (t.Bearbeitungsstatus BETWEEN 2 AND 8';
                        $where.= ' AND t.Versandbedingung NOT LIKE \'Nicht ins Dispotool\') ';
                    }
                }

                $sqlCount = $this->tlog( __LINE__, 'SELECT COUNT(1) AS count FROM ' . $table . ' t ' . $where );
                try {
                    $total = $wwsdb->fetchOne($sqlCount);
                    // echo 'total: ' . $total  . PHP_EOL;
                } catch (Exception $e) {
                    echo $e->getMessage() . PHP_EOL;
                    echo $sqlCount . PHP_EOL;
                    file_put_contents($logfile, date('YmdHis') . ' '
                        . $this->secondsToTime(time() - $timeIn) . ' ERROR ' . PHP_EOL
                        . $e->getMessage() . PHP_EOL . ' sql ' . $sqlCount . PHP_EOL, FILE_APPEND);
                    $this->tlog( __LINE__, 'Error: ' . $e->getMessage());
                    error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Exit ' . $e->getMessage() . ' ' . $sqlCount);
                    exit;
                }

                file_put_contents($logfile, date('YmdHis')
                    . ' ' . $this->secondsToTime(time() - $timeIn)
                    . ' sqlCount ' . $sqlCount . ' => ' . $total . PHP_EOL, FILE_APPEND);

                $curr = 0;
                $saved = 0;
                $num_inserted = 0;
                $num_updated = 0;

                $varStorage->set($lastWWSImportStatusVarName, 'started')
                    ->set($lastWWSImportStartVarName, date('Y-m-d H:i:s', $lastWWSImportStartTime));

                $sqlSelectRows = $this->tlog(__LINE__, 'SELECT ' . $cols
                    . ' FROM ' . $table . ' t ' . $join . $where . ' ORDER BY ISNULL(t.GeaendertAm, t.AngelegtAm)');

                file_put_contents($logfile, date('YmdHis') . ' '
                    . $this->secondsToTime(time() - $timeIn) . ' sql ' . $sqlSelectRows . PHP_EOL, FILE_APPEND);

                try {
                    /* @var $stmt Sqlsrv_Stmt */
                    echo '#' . __LINE__ . ' ' . $wwssource . ' sql:' . $sqlSelectRows . '<br>' . PHP_EOL;
                    $stmtTotal = $wwsdb->query($sqlSelectRows);
                } catch (Exception $e) {
                    echo '#' . __LINE__ . ' exception: ' . $e->getMessage() . '<br>' . PHP_EOL;
                    $this->tlog( __LINE__, 'Error: ' . $e->getMessage());
                    error_log('#' . __LINE__ . ' ' . __METHOD__ . ' Exit ' . $e->getMessage() . ' ' . $sqlSelectRows);
                    exit;
                }

                $varStorage
                    ->set($lastWWSCountQueryVarName, $sqlCount)
                    ->set($lastWWSQueryVarName, $sqlSelectRows)
                    ->set($lastWWSNumRowsVarName, $total)
                    ->set($lastWWSImportStatusVarName, 'importing');;



                while ($curr < $total) {
                    ++$curr;
                    $rowSrc = $stmtTotal->fetch(Sqlsrv::FETCH_ASSOC);

                    if (!$rowSrc) {
                        echo '#'.__LINE__ . ' COULD NOT FETCH WWS-ROW ' . $curr . ' OF ' . $table . '<br>' . PHP_EOL;
                        continue;
                    }

                    // echo "[$curr / $total ] " . json_encode(array_intersect_key($rowSrc, array_flip($primaries)) ) . PHP_EOL;

                    $sql_set = ' ' . $stg->info(ZEND_DB_TABLE::NAME) . ' SET ' . PHP_EOL;
                    $sql_set_where = '';


                    $row = [];
                    foreach ($rowSrc as $k => $v) {
                        if ($v instanceof DateTime) {
                            $row[$k] = $v->format('Y-m-d H:i:s');
                        } else {
                            $row[$k] = $v;
                        }
                        $sql_set .= '`' . $k . '` = ' . (is_null($row[$k]) ? 'NULL' : $db->quote($row[$k])) . ',' . PHP_EOL;
                    }
                    $sql_set = rtrim($sql_set, ','.PHP_EOL);
                    $sql_set.= ' WHERE ';

                    $record = $stg->createRow();
                    $record->setFromArray($row);

                    try {
                        $where = array();
                        $_existsStmt = $stg->select('count(1)');
                        foreach($primaries as $_prim) {
                            $where[] = $db->quoteInto("$_prim = ?", $row[$_prim] );
                            $_existsStmt->where("$_prim = ?", $row[$_prim]);
                            $sql_set_where.= $_prim . ' = ' . $db->quote($row[$_prim]) . ' AND ';
                        }
                        $sql_set_where = rtrim($sql_set_where, 'AND ') . ';';
                        // echo $_existsStmt->assemble() . PHP_EOL;

                        $exists = $_existsStmt->query()->rowCount();

                        // echo $_existsStmt->assemble();
                        $this->tlog(__LINE__, 'update: '
                            . print_r(array('row'=>$row, 'where'=>$where),1));

                        $updateCountSuccess = -1;
                        $insertSuccess = -1;

                        if ($exists) {
                            $saveMode = 'Update';

                            $updateCountSuccess = $stg->update($row, $where);
                            $sql_set = 'UPDATE ' . $sql_set . ' WHERE ' . $sql_set_where;
                            $num_updated++;
                        } else {
                            $saveMode = 'Insert';
                            $sql_set = 'INSERT INTO ' . $sql_set;
                            $insertSuccess = $record->save();
                            $num_inserted++;
                        }
                        ++$saved;
                        $varStorage->set($lastModVarName, max( $row['AngelegtAm'], $row['GeaendertAm']));

                        if ($saved % 1000 == 0) {
                            file_put_contents($logfile, date('YmdHis') . ' '
                                . $this->secondsToTime(time() - $timeIn)
                                . ' running Es wurden ' . $saved
                                . ' von ' . $total . ' (' . $total . ') '
                                . $table . ' eingefuegt!' . PHP_EOL, FILE_APPEND);
                        }

                    } catch (Zend_Db_Statement_Exception $e) {

                        $details = '#'.__LINE__ . ' saveMode: ' . $saveMode . '; sql_set: ' . $sql_set . '; row-data: ' . print_r($row,1) . '<br>' . PHP_EOL;
                        $err = $this->tlog(__FILE__,
                            stripos($e->getMessage(), 'duplicate')
                                ? ' Duplicate Entry' . PHP_EOL . $details
                                : $e->getMessage() . PHP_EOL . $details);

                        file_put_contents($logfile, date('YmdHis')
                            . ' ' . $this->secondsToTime(time() - $timeIn)
                            . ' error (' . $saved . ' / ' . $total . ') '
                            . $err . PHP_EOL, FILE_APPEND);

                        echo $this->tlog('', '#' . __LINE__
                                . ' Zend_Db_Statement_Exception (code:'
                                . $e->getCode() . ') ') . PHP_EOL; // . $e->getMessage() . PHP_EOL;

                        echo $e->getMessage() . '<br>' . PHP_EOL;
                        echo 'saveMode: ' . $saveMode . '<br>' . PHP_EOL;

                        if ($exists) {
                            echo 'updateCountSuccess: '
                                . print_r($updateCountSuccess, 1) . PHP_EOL;
                        } else {
                            echo 'insertSuccess: ' . print_r($insertSuccess, 1)
                                . ' ' . gettype($insertSuccess) . PHP_EOL;
                        }

                        echo '<pre>' . $sql_set . '</pre>';
                        echo '<pre>' . $e->getTraceAsString() . '</pre>' . PHP_EOL;
                        throw $e;
                        exit;

                    } catch (Zend_Db_Exception $e) {
                        echo $this->tlog('', '#' . __LINE__
                                . ' Zend_Db_Exception (code:' . $e->getCode() . ') '
                                . $e->getMessage()) . PHP_EOL;
                        echo '<pre>' . $sql_set . '</pre>';
                        echo '<pre>' . $e->getTraceAsString() . '</pre>' . PHP_EOL;
                        throw $e;
                        exit;

                    } catch (Zend_Exception $e) {
                        echo $this->tlog('', '#' . __LINE__
                                . ' Zend_Exception ' . $e->getMessage()) . PHP_EOL;
                        throw $e;
                        exit;

                    } catch (Exception $e) {
                        echo $this->tlog('', '#' . __LINE__
                                . ' Exception ' . $e->getMessage()) . PHP_EOL;
                        echo '<pre>' . $sql_set . '</pre>';
                        echo '<pre>' . $e->getTraceAsString() . '</pre>' . PHP_EOL;
                        throw $e;
                        exit;

                    }
                }

                file_put_contents($logfile, date('YmdHis') . ' '
                    . $this->secondsToTime(time() - $timeIn) . ' finished Es wurden '
                    . $saved . ' von ' . $total . ' (' . $total . ')  ' . $table . ' eingefuegt!'
                    . PHP_EOL, FILE_APPEND);

                $lastWWSImportDurationTime = MyProject_Helper_Converter::secondsToTime(time() - $lastWWSImportStartTime);

                $varStorage
                    ->set($lastWWSNewRowsVarName, $num_inserted)
                    ->set($lastWWSUpdRowsVarName, $num_updated)
                    ->set($lastWWSImportStatusVarName, 'finished')
                    ->set($lastWWSImportDurationVarName, $lastWWSImportDurationTime);

                echo $this->tlog(__LINE__, 'Es wurden ' . $saved . ' von '
                        . $total . ' ' . $table . ' (' . $total . ') ' . ' eingefuegt!') . PHP_EOL;

                echo 'logs are written into ' . $logfile . PHP_EOL;
                flush();
            }
        } catch (Exception $e) {
            echo $this->tlog(__LINE__, $e->getMessage()) . PHP_EOL;
            echo $this->tlog(__LINE__, $e->getTraceAsString()) . PHP_EOL;
            echo $this->tlog(__LINE__, 'last-row: ' . print_r($row, 1)) . PHP_EOL;
            throw $e;
        }

        error_log('#' . __LINE__ . ' ' . __METHOD__ . ' FINISHED !!!');
        echo ' Unique-Counted-Field-Types: ' . print_r(Sqlsrv_Stmt::$uniqueFoundFieldTypes, 1) . PHP_EOL;
        $this->tlog(__LINE__, 'finished ' . $lastWWSImportDurationTime . ' successfully!');
    }
}
