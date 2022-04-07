<?php 

$wws_cache_file = dirname(__FILE__)."/cache/lastsync.cache.phs";
$mysql_conn_script = dirname(__FILE__)."/../../include/conn.php";
$mssql_conn_script = dirname(__FILE__)."/class/wwssync.sqlsrv.class.php";
require($mysql_conn_script);
require($mssql_conn_script);
$DbgMsg = "Start WWS-Status-Synchronisation: ".date("Y-m-d H:i:s")."<br>\n";
$ShowDbgMsg = (!empty($_GET["DBG"])) ? $_GET["DBG"] : false;
$sLogErr = '';

if (!$connid) {
	$sLogErr.= date("Y-m-d H:i:s")." Fehler bei Synchronisation mit WWS: Fehlender Mysql-DB-Connect!\n";
}

WWW_DB::check_mssql($errno);

if ($errno) {
	$sLogErr.= date("Y-m-d H:i:s")." ".$aErrno2Txt[$errno]." ".$enableNotice."\n";
}

if ($sLogErr) {
	file_put_contents("system_alerts.log", $sLogErr, FILE_APPEND);
	$DbgMsg.= "SYN-ABBRUCH:<br>\n".$sLogErr."<br>\n";
}

if (!$sLogErr) {
	$SQL = "SELECT vorgangsnr, mid, status FROM `".$_TABLE["projects"]."`\n";
	$SQL.= "WHERE status != \"Abgeschlossen\" AND status != \"Verloren\"";
	$r = mysql_query($SQL, $connid);
	if ($r) {
		$n = mysql_num_rows($r);
		for ($i = 0; $i < $n; $i++) {
			$e = mysql_fetch_array($r, MYSQL_ASSOC);
			$aProjectStatus[$e["mid"]][$e["vorgangsnr"]] = $e["status"];
			$aWwsIds[]= $e["vorgangsnr"];
		}
		mysql_free_result($r);
	}
	
	$DbgMsg.= "#".__LINE__." Updatesuche für ".count($aWwsIds)." Projekteinträge!<br>\n";
	
	if (!$errno) {
		$wws = new WWW_DB();
		$num_updates_soll = 0;
		$num_updates_done = 0;
		$GroupUpdateByIds = array();
		$aWwsStatien = $wws->get_projectsStatus($aWwsIds);

		file_put_contents($wws_cache_file, serialize($aWwsStatien));
		
		for ($i = 0; $i < count($aWwsStatien); $i++) {
	    	switch($aWwsStatien[$i]["Bearbeitungsstatus"]) {
				case "0": $wws_status = "Erfasst"; break;
				case "1": $wws_status = "Angebot"; break;
				case "9": $wws_status = ($aWwsStatien[$i]["AbschlussStatus"])?"Abgeschlossen":"Verloren"; break;
				default:  $wws_status = "Beauftragt";
			}
			$r_wwsid = $aWwsStatien[$i]["vorgangsnr"];
			$r_mid = $aWwsStatien[$i]["Mandant"];
			
			$DbgMsg.= "#".__LINE__." ".print_r($aWwsStatien[$i],true)."<br>\n";
			$DbgMsg.= "#".__LINE__." aProjectStatus[".$r_wwsid."]:"
                                . (isset($aProjectStatus[$r_mid][$r_wwsid])?$aProjectStatus[$r_mid][$r_wwsid]:'undefined')
                                . " != wws_status:".$wws_status."<br>\n";
                        
			if (isset($aProjectStatus[$r_mid][$r_wwsid]) && $aProjectStatus[$r_mid][$r_wwsid] != $wws_status) {
				$DbgMsg.= "<div style=\"color:#f00;\">#".__LINE__." UPDATE REQUIERED !</div>\n";
				$GroupUpdateByIds[$r_mid.":".$wws_status][] = $r_wwsid;
				$num_updates_soll++;
			} else {
				$DbgMsg.= "<div style=\"color:#0f0;\">#".__LINE__." NO UPDATE REQUIERED !</div>\n";
			}
		}
		$DbgMsg.= "#".__LINE__." <pre>".print_r($GroupUpdateByIds,true)."</pre>\n";
		
		foreach($GroupUpdateByIds as $k => $v) {
			list($r_mid, $r_status) = explode(":", $k);
      $SQL = "UPDATE `".$_TABLE["projects"]."`\n";
			$SQL.= "SET status = \"".mysql_real_escape_string($r_status)."\"\n";
			$SQL.= "WHERE vorgangsnr IN (".implode(",", $v).") AND mid = \"".mysql_real_escape_string($r_mid)."\"\n";
			$r = mysql_query($SQL, $connid);
			$n = mysql_affected_rows();
			$num_updates_done+= $n;
			$SQL.= "/* COUNT ENTRIES: ".count($v)."\nCOUNT UPDATES: $n */\n";
			$DbgMsg.= "#".__LINE__." <pre>".htmlentities($SQL)."</pre>\n";
		}
	}
}

$DbgMsg.= "#".__LINE__." num_updates_done: ".$num_updates_done."\n";
if ($ShowDbgMsg) echo $DbgMsg;
file_put_contents("last_wws_sync.log", $DbgMsg);
?>