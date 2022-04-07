<?php 

// Start: Init Testing
// Dummie-Class für lokales Testing ohne MSSQL-DB-Anbindung
// gibt einen existierenden Datensatz für WWSID: 191592 aus
$IsTest = false; // true; // 
class TEST_XYZ {
  var $connid = 1;
  function get_RowsById($wid, $mid, $_AllFields = 0) {
    $aTestRows = array();
    if ($wid == "191592") {
      $aTestRows[0] = array(
        "Mandant" => "10",
        "Bearbeitungsstatus" => "9",
        "UnterBearbeitungsstatus" => "0",
        "AbschlussStatus" => "1",
        "vorgangsnr" => "191592",
        "firmenname" => "Fortuna Düsseldorf 1895 e.V.",
        "firmenplz" => "40235",
        "firmenort" => "Düsseldorf",
        "firmenplzort" => "40235 Düsseldorf",
        "firmenstr" => "Flinger Broich 87",
		"verkaeufer" => "Becker"
      );
    }
    return $aTestRows;
  }
}
// Ende: Init Testing

require_once(dirname(__FILE__)."/class/wwssync.class.php");
require_once(dirname(__FILE__)."/../../include/conn.php");

// if ($IsTest) $errno = 0; else 
WWW_DB::check_mssql($errno);

$TplAjaxXmlResult = "<"."?xml version=\"1.0\" encoding=\"UTF-8\" ?".">\n";
$TplAjaxXmlResult.= "<result type=\"{ResultType}\">\n";
$TplAjaxXmlResult.= "	<Msg/>\n";
$TplAjaxXmlResult.= "	<Err/>\n";
$TplAjaxXmlResult.= "	<JScript/>\n";
$TplAjaxXmlResult.= "	<MoreFields/>\n";
$TplAjaxXmlResult.= "</result>\n";
$aXmlValues["ResultType"] = "error";
$aXmlValues["Msg"] = '';

$aFitTxt4Xml = array(
	"&" => "&#".ord("&").";",
	"\r" => "&#".ord("\r").";",
	"\n" => "&#".ord("\n").";",
	"\"" => "&#".ord("\"").";",
	"<" => "&#".ord("<").";",
	">" => "&#".ord(">").";"
);

function encodeXmlTxt($XmlText) {
	global $aFitTxt4Xml;
	$XmlText = strtr(decodeXmlTxt($XmlText), $aFitTxt4Xml);
	return $XmlText;
}
function decodeXmlTxt($XmlText) {
	global $aFitTxt4Xml;
	$XmlText = strtr($XmlText, array_flip($aFitTxt4Xml));
	return $XmlText;
}

function get_pentries_by_wwsid($wid, $mid, $piab = "") {
  global $connid;
  global $_TABLE;
  global $_TBLKEY;
  
  $n = 0;
  $aP = array();
  $SQL = "SELECT ".$_TBLKEY["projects"].", vorgangsnr, kunde \n";
  $SQL.= "FROM ".$_TABLE["projects"]." \n";
  $SQL.= "WHERE vorgangsnr = \"".mysql_real_escape_string($wid)."\"";
  if ($mid) $SQL.= " AND mid = \"".mysql_real_escape_string($mid)."\"";
  if ($piab) $SQL.= " AND Planung_IAB = \"".mysql_real_escape_string($piab)."\"";
  
  $r = @mysql_query($SQL, $connid);
  if ($r) {
    $n = mysql_num_rows($r);
    for ($i = 0; $i < $n; $i++) {
      $aP[] = mysql_fetch_assoc($r);
    }
    mysql_free_result($r);
  }
  return $aP;
}

$mid = (!empty($_GET["mid"])) ? $_GET["mid"] : "";
$wid = (!empty($_GET["wid"])) ? $_GET["wid"] : "";
$piab = (!empty($_GET["piab"])) ? $_GET["piab"] : "";
$wid_int = strval(intval($wid));
if ($wid_int == $wid && strlen($wid) <= 7 && strlen($wid) >= 5) { 
	if (!$errno) {
		if ($wid) {
			if ($mid) {
        
        $aPEntries = get_pentries_by_wwsid($wid, $mid, $piab);
        //print_r($aPEntries,0);
        
        $num_pentries = count($aPEntries);
        if ($num_pentries > 0) {
          if ($num_pentries == 1) {
            // index.php?s=projects&id=18371 $aPEntries[0]["id"]
            $directlnk = "index.php?s=projects&id=".urlencode($aPEntries[0]["pid"]);
            $lnk = "index.php?s=projects&searchTerm=".urlencode($wid)."&searchField=Vorgangsnr";
            $aXmlValues["Msg"].= "Es existiert bereits <a href=\"$lnk\" style=\"color:#00f;\">ein</a> Projekt-Eintrag mit der ID <span style=\"color:#000;\">$wid</span> und Planung_IAB <span style=\"color:#000;\">$piab</span> !";
          } else {
            // index.php?s=projects&searchTerm=$wid&searchField=Vorgangsnr
            $lnk = "index.php?s=projects&searchTerm=".urlencode($wid)."&searchField=Vorgangsnr";
            $aXmlValues["Msg"].= "Es existieren bereits <a href=\"$lnk\" style=\"color:#00f;\">".$num_pentries."</a> Projekt-Eintr&auml;ge mit der ID <span style=\"color:#000;\">$wid</span> und Planung_IAB <span style=\"color:#000;\">$piab</span> !";
          }
        }
        
        $wws = (!$IsTest) ? new WWW_DB() : new TEST_XYZ();
        
		if ($wws && $wws->connid) {
			$aRows = $wws->get_RowsById($wid, $mid, $_AllFields = 0);
			if (count($aRows)) {
				// echo "#".__LINE__." Angebotsstatus <pre>".print_r($aData,true)."</pre>\n";
				$aXmlValues["MoreFields"] = "";
	            for($i = 0; $i < count($aRows); $i++) {
	              $aXmlValues["MoreFields"] = "";
   					foreach($aRows[$i] as $k => $v) {
   						$aXmlValues["MoreFields"].= "\t<".$k.">".encodeXmlTxt($v)."</{$k}>\n";
   					}
	            }
  				$aXmlValues["ResultType"] = "success";
  			} else {
  				$aXmlValues["Err"] = "Es wurde kein WWS-Datensatz mit der Vorgangsnr gefunden!";
  			}
  		 } else {
  		    $aXmlValues["Err"] = "Es konnte keine DB-Verbindung zum WWS hergestellt werden!";
  		 }
      } else {
        $aXmlValues["Err"] = "Fehlende Mandanten-ID für WWS-Anfrage!";
      }
		} else {
			$aXmlValues["Err"] = "Fehlende WWS-ID für Anfrage";
		}
	} else {
		$aXmlValues["Err"] = $aErrno2Txt[$errno]." ".$enableNotice;
	}
} else {
	$aXmlValues["Err"] = "Ungültige oder fehlende WWS-ID (5-7 Numerische Zeichen): ".$wid."!";
}

$AjaxXmlResult = $TplAjaxXmlResult;
foreach($aXmlValues as $k => $v) {
	switch($k) {
		case "ResultType":
		$AjaxXmlResult = str_replace("{".$k."}", $v, $AjaxXmlResult);
		break;
		
		case "MoreFields":
		$AjaxXmlResult = str_replace("<".$k."/>", $v, $AjaxXmlResult);
		break;
		
		default:
		$AjaxXmlResult = str_replace("<".$k."/>", "<".$k."><![CDATA[".$v."]]></".$k.">", $AjaxXmlResult);
	}
}

header("Content-Type: text/xml; charset=utf-8");
echo utf8_encode($AjaxXmlResult);

?>